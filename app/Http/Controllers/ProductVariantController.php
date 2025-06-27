<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function index()
    {
        // List all variants with related product, color, size
        $variants = ProductVariant::with('product', 'color', 'size')->paginate(20);
        return view('variants.index', compact('variants'));
    }

    public function create()
    {
        $products = Product::all();
        $colors = Color::all();
        $sizes = Size::all();
        return view('variants.create', compact('products', 'colors', 'sizes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id' => 'nullable|exists:colors,id',
            'size_id' => 'nullable|exists:sizes,id',
        ]);

        ProductVariant::create($request->only('product_id', 'color_id', 'size_id'));

        return redirect()->route('variants.index')->with('success', 'Variant added successfully');
    }

    public function show(ProductVariant $variant)
    {
        return view('variants.show', compact('variant'));
    }

    public function edit(ProductVariant $variant)
    {
        $products = Product::all();
        $colors = Color::all();
        $sizes = Size::all();
        return view('variants.edit', compact('variant', 'products', 'colors', 'sizes'));
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id' => 'nullable|exists:colors,id',
            'size_id' => 'nullable|exists:sizes,id',
        ]);

        $variant->update($request->only('product_id', 'color_id', 'size_id'));

        return redirect()->route('variants.index')->with('success', 'Variant updated successfully');
    }

    public function destroy(ProductVariant $variant)
    {
        $variant->delete();

        return redirect()->route('variants.index')->with('success', 'Variant deleted successfully');
    }
}
