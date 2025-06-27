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
    public function index()
{
    $purchases = Purchase::with('supplier')->orderBy('purchase_date', 'desc')->paginate(15);
    return view('purchases.index', compact('purchases'));
}

public function create()
{
    $suppliers = Supplier::all();
    $products = Product::with('variants')->get();
    return view('purchases.create', compact('suppliers', 'products'));
}

public function store(Request $request)
{
    $request->validate([
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
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.color_id'   => 'nullable|exists:colors,id',
        'items.*.size_id'    => 'nullable|exists:sizes,id',

    ]);

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
        $purchase->purchaseItems()->create([
            'product_id' => $item['product_id'],
            'product_variant_id' => $item['product_variant_id'] ?? null,
            'batch_number' => $item['batch_number'] ?? null,
            'purchase_price' => $item['purchase_price'],
            'quantity' => $item['quantity'],
            'expiry_date' => $item['expiry_date'] ?? null,
            'total_price' => $item['purchase_price'] * $item['quantity'],
        ]);


        $variantId = null;
        if (isset($item['color_id']) || isset($item['size_id'])) {
            $variantId = ProductVariant::where('product_id', $item['product_id'])
                ->where('color_id', $item['color_id'] ?? null)
                ->where('size_id',  $item['size_id']  ?? null)
                ->value('id');   // returns null if no exact match
        }

        $purchase->purchaseItems()->create([
            'product_id'        => $item['product_id'],
            'product_variant_id'=> $variantId,
            'batch_number'      => $item['batch_number'] ?? null,
            'purchase_price'    => $item['purchase_price'],
            'quantity'          => $item['quantity'],
            'expiry_date'       => $item['expiry_date'] ?? null,
            'total_price'       => $item['purchase_price'] * $item['quantity'],
        ]);


    }

    return redirect()->route('purchases.index')->with('success', 'Purchase created successfully.');
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
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.purchase_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.color_id'   => 'nullable|exists:colors,id',
            'items.*.size_id'    => 'nullable|exists:sizes,id',

            // other validation as needed
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

        // Delete old items
        $purchase->purchaseItems()->delete();

        // Recreate purchase items
        foreach ($request->items as $item) {
            $purchase->purchaseItems()->create([
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'] ?? null,
                'batch_number' => $item['batch_number'] ?? null,
                'purchase_price' => $item['purchase_price'],
                'quantity' => $item['quantity'],
                'expiry_date' => $item['expiry_date'] ?? null,
                'total_price' => $item['purchase_price'] * $item['quantity'],
            ]);


            $variantId = null;
            if (isset($item['color_id']) || isset($item['size_id'])) {
                $variantId = ProductVariant::where('product_id', $item['product_id'])
                    ->where('color_id', $item['color_id'] ?? null)
                    ->where('size_id',  $item['size_id']  ?? null)
                    ->value('id');   // returns null if no exact match
            }

            $purchase->purchaseItems()->create([
                'product_id'        => $item['product_id'],
                'product_variant_id'=> $variantId,
                'batch_number'      => $item['batch_number'] ?? null,
                'purchase_price'    => $item['purchase_price'],
                'quantity'          => $item['quantity'],
                'expiry_date'       => $item['expiry_date'] ?? null,
                'total_price'       => $item['purchase_price'] * $item['quantity'],
            ]);


        }

        return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully.');
    }

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
        // Pull variant rows for this product
        $variants = ProductVariant::where('product_id', $product->id)->get();

        // Unique color & size IDs
        $colorIds = $variants->pluck('color_id')->filter()->unique();
        $sizeIds  = $variants->pluck('size_id')->filter()->unique();

        return response()->json([
            'colors' => Color::whereIn('id', $colorIds)->select('id', 'name')->get(),
            'sizes'  => Size::whereIn('id', $sizeIds)->select('id', 'name')->get(),
        ]);
    }
}
