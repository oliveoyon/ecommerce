<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Size;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['brand', 'category', 'subcategory', 'unit'])->get();
        $brands = Brand::all();
        $categories = Category::all();
        $units = Unit::all();
        $colors = Color::all();    // Add this
        $sizes = Size::all();      // And this

        return view('products.index', compact('products', 'brands', 'categories', 'units', 'colors', 'sizes'));
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

        if ($request->has('has_variants') && $request->filled('variants')) {
            foreach ($request->variants as $variant) {
                $product->variants()->create($variant);
            }
        }

        return response()->json(['success' => true]);
    }



    public function edit($id)
    {
        $product = Product::with(['images'])->findOrFail($id);
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

    public function deleteImage($id)
    {
        $image = ProductImage::findOrFail($id);
        
        // Delete the file from storage
        if (Storage::exists('public/' . $image->image_path)) {
            Storage::delete('public/' . $image->image_path);
        }

        // Delete from DB
        $image->delete();

        return response()->json(['success' => true]);
    }
}
