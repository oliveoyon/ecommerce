<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    // Display a listing of purchases
public function index(Request $request)
{
    $query = Purchase::with('supplier')->orderBy('purchase_date', 'desc');

    /* ---------- filters ---------- */
    if ($request->filled('supplier_id')) {
        $query->where('supplier_id', $request->supplier_id);
    }

    if ($request->filled('payment_status') && $request->payment_status !== 'All') {
        $query->where('payment_status', $request->payment_status);
    }

    if ($request->filled('start_date')) {
        $query->whereDate('purchase_date', '>=', $request->start_date);
    }
    if ($request->filled('end_date')) {
        $query->whereDate('purchase_date', '<=', $request->end_date);
    }
    /* ---------- /filters ---------- */

    $purchases  = $query->paginate(15)->appends($request->query());   // keep filters on links
    $suppliers  = Supplier::select('id','name')->get();              // for dropdown
    $statuses   = ['All','Paid','Partial','Due'];

    return view('purchases.index', compact('purchases','suppliers','statuses'));
}


public function create()
{
    $suppliers = Supplier::all();
    $products = Product::with('variants')->get();
    return view('purchases.create', compact('suppliers', 'products'));
}

public function store(Request $request)
{
    // dd($request->all());
    
    $validator = \Validator::make($request->all(), [
        'supplier_id' => 'required|exists:suppliers,id',
        'purchase_date' => 'required|date',
        'invoice_no' => 'nullable|unique:purchases,invoice_no',
        'payment_status' => 'required|in:Paid,Partial,Due',
        'total_amount' => 'required|numeric|min:0',
        'paid_amount' => 'required|numeric|min:0',
        'due_amount' => 'required|numeric|min:0',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.purchase_price' => 'required|numeric|min:0',
        'items.*.sale_price' => 'required|numeric|min:0', // mandatory sale price
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.color_id'   => 'nullable|exists:colors,id',
        'items.*.size_id'    => 'nullable|exists:sizes,id',
        'items.*.batch_number' => 'nullable|string',
        'items.*.expiry_date' => 'nullable|date',
    ]);

    if ($validator->fails()) {
        // Return errors for debugging
        return back()->withErrors($validator)->withInput();
    }

    // Create purchase
    $purchase = Purchase::create([
        'supplier_id' => $request->supplier_id,
        'invoice_no' => $request->invoice_no,
        'purchase_date' => $request->purchase_date,
        'payment_status' => $request->payment_status,
        'payment_method' => $request->payment_method,
        'total_amount' => $request->total_amount,
        'paid_amount' => $request->paid_amount,
        'due_amount' => $request->due_amount,
        'note' => $request->note,
    ]);

    foreach ($request->items as $item) {
        // Find variant id if exists
        $variantId = null;
        if (isset($item['color_id']) || isset($item['size_id'])) {
            $variantId = ProductVariant::where('product_id', $item['product_id'])
                ->where('color_id', $item['color_id'] ?? null)
                ->where('size_id', $item['size_id'] ?? null)
                ->value('id');
        }

        // Create purchase item
        $purchaseItem = $purchase->purchaseItems()->create([
            'product_id' => $item['product_id'],
            'product_variant_id' => $variantId,
            'batch_number' => $item['batch_number'] ?? null,
            'purchase_price' => $item['purchase_price'],
            'quantity' => $item['quantity'],
            'expiry_date' => $item['expiry_date'] ?? null,
            'total_price' => $item['purchase_price'] * $item['quantity'],
        ]);

        // Update product_batches table (stock)
        // Check if batch with same product, variant, batch_number exists
        $batchQuery = \DB::table('product_batches')
            ->where('product_id', $item['product_id'])
            ->where('product_variant_id', $variantId)
            ->where('batch_number', $item['batch_number'] ?? null);

        $existingBatch = $batchQuery->first();

        if ($existingBatch) {
            // Update quantity and prices (purchase_price can be updated as weighted avg or overwritten)
            $newQty = $existingBatch->quantity + $item['quantity'];
            $batchQuery->update([
                'quantity' => $newQty,
                'purchase_price' => $item['purchase_price'],  // or weighted average logic if preferred
                'sale_price' => $item['sale_price'],
                'expiry_date' => $item['expiry_date'] ?? $existingBatch->expiry_date,
                'purchase_date' => $request->purchase_date,
                'updated_at' => now(),
            ]);
        } else {
            // Insert new batch
            \DB::table('product_batches')->insert([
                'product_id' => $item['product_id'],
                'product_variant_id' => $variantId,
                'batch_number' => $item['batch_number'] ?? null,
                'quantity' => $item['quantity'],
                'purchase_price' => $item['purchase_price'],
                'sale_price' => $item['sale_price'],
                'expiry_date' => $item['expiry_date'] ?? null,
                'purchase_date' => $request->purchase_date,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    return redirect()->route('purchases.index')->with('success', 'Purchase created successfully.');
}


public function update(Request $request, Purchase $purchase)
{
    $request->validate([
        'supplier_id' => 'required|exists:suppliers,id',
        'purchase_date' => 'required|date',
        'invoice_no' => 'nullable|unique:purchases,invoice_no,' . $purchase->id,
        'payment_status' => 'required|in:Paid,Partial,Due',
        'total_amount' => 'required|numeric|min:0',
        'paid_amount' => 'required|numeric|min:0',
        'due_amount' => 'required|numeric|min:0',
        'items' => 'required|array|min:1',
        'items.*.product_variant_id' => 'required|exists:product_variants,id',
        'items.*.purchase_price' => 'required|numeric|min:0',
        'items.*.sale_price' => 'required|numeric|min:0', // mandatory sale price
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.batch_number' => 'nullable|string',
        'items.*.expiry_date' => 'nullable|date',
    ]);

    $purchase->update([
        'supplier_id' => $request->supplier_id,
        'invoice_no' => $request->invoice_no,
        'purchase_date' => $request->purchase_date,
        'payment_status' => $request->payment_status,
        'payment_method' => $request->payment_method,
        'total_amount' => $request->total_amount,
        'paid_amount' => $request->paid_amount,
        'due_amount' => $request->due_amount,
        'note' => $request->note,
    ]);

    // Delete old purchase items
    $purchase->purchaseItems()->delete();

    foreach ($request->items as $item) {
        // Get the variant and its product_id
        $variant = ProductVariant::find($item['product_variant_id']);
        if (!$variant) {
            continue; // skip if invalid variant
        }

        $purchaseItem = $purchase->purchaseItems()->create([
            'product_id' => $variant->product_id,
            'product_variant_id' => $variant->id,
            'batch_number' => $item['batch_number'] ?? null,
            'purchase_price' => $item['purchase_price'],
            'quantity' => $item['quantity'],
            'expiry_date' => $item['expiry_date'] ?? null,
            'total_price' => $item['purchase_price'] * $item['quantity'],
        ]);

        $batchQuery = \DB::table('product_batches')
            ->where('product_id', $variant->product_id)
            ->where('product_variant_id', $variant->id)
            ->where('batch_number', $item['batch_number'] ?? null);

        $existingBatch = $batchQuery->first();

        if ($existingBatch) {
            $newQty = $existingBatch->quantity + $item['quantity'];
            $batchQuery->update([
                'quantity' => $newQty,
                'purchase_price' => $item['purchase_price'],
                'sale_price' => $item['sale_price'],
                'expiry_date' => $item['expiry_date'] ?? $existingBatch->expiry_date,
                'purchase_date' => $request->purchase_date,
                'updated_at' => now(),
            ]);
        } else {
            \DB::table('product_batches')->insert([
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'batch_number' => $item['batch_number'] ?? null,
                'quantity' => $item['quantity'],
                'purchase_price' => $item['purchase_price'],
                'sale_price' => $item['sale_price'],
                'expiry_date' => $item['expiry_date'] ?? null,
                'purchase_date' => $request->purchase_date,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully.');
}




    // Show the specified purchase with items
    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'purchaseItems.product', 'purchaseItems.productVariant');
        return view('purchases.show', compact('purchase'));
    }

    // Show the form for editing the specified purchase
    public function edit(Purchase $purchase)
    {
        $purchase->load('purchaseItems');
        $suppliers = Supplier::all();
        $products = Product::all();

        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    // Update the specified purchase in storage
   

    // Remove the specified purchase from storage
    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully.');
    }

    // Optional: fetch products list for AJAX
    public function getProducts()
    {
        $products = Product::select('id', 'name')->get();
        return response()->json($products);
    }

public function getVariants(Product $product)
{
    $variants = ProductVariant::where('product_id', $product->id)
        ->with(['color', 'size'])
        ->get()
        ->map(function ($v) {
            return [
                'id' => $v->id,
                'color_name' => $v->color ? $v->color->name : null,
                'size_name' => $v->size ? $v->size->name : null,
            ];
        });

    return response()->json(['variants' => $variants]);
}


}
