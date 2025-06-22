<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['brand', 'category', 'subcategory', 'unit'])->get();
        $brands = Brand::all();
        $categories = Category::all();
        $units = Unit::all();

        return view('products.index', compact('products', 'brands', 'categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'brand_id' => 'nullable',
            'unit_id' => 'nullable',
            'default_purchase_price' => 'nullable|numeric',
            'default_sale_price' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $validated['has_variants'] = $request->has('has_variants');
        $validated['has_expiry'] = $request->has('has_expiry');

        $product = Product::create($validated);

        // ✅ Image upload logic
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $filename = $image->store('products', 'public');

                $product->images()->create([
                    'image_path' => $filename,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }



    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'brand_id' => 'nullable',
            'unit_id' => 'nullable',
            'default_purchase_price' => 'nullable|numeric',
            'default_sale_price' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $validated['has_variants'] = $request->has('has_variants');
        $validated['has_expiry'] = $request->has('has_expiry');

        $product = Product::findOrFail($id);
        $product->update($validated);

        // ✅ Upload new images if provided
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $filename = $image->store('products', 'public');

                $product->images()->create([
                    'image_path' => $filename,
                    'is_primary' => false, // you can manage primary selection later
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
}
