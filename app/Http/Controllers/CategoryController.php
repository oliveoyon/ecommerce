<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Unit;
use App\Models\Brand;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class CategoryController extends Controller
{
    public function categories()
    {
        $categories = Category::all();
        return view('dashboard.admin.category', compact('categories'));
    }
    
    // Function to Add a New Category
    public function categoryAdd(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'category_img' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);
        $categorySlug = Str::slug($request->name);
        $category = new Category();
        $category->name = $request->name;
        $category->category_slug = $categorySlug;
        if ($request->hasFile('category_img')) {
            $imageExtension = $request->file('category_img')->getClientOriginalExtension();
            $imageName = $categorySlug . '.' . $imageExtension;
            $imagePath = $request->file('category_img')->storeAs('categories', $imageName, 'public');
            $category->category_img = $imagePath;
        }

        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Category added successfully!',
            'category' => $category,
        ]);
    }


    public function categoryUpdate(Request $request, $categoryId)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $categoryId,
            'category_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|boolean',
        ]);

        $category = Category::findOrFail($categoryId);
        $originalName = $category->name;
        $category->name = $request->name;
        $categorySlug = Str::slug($request->name);
        $category->category_slug = $categorySlug; // Update the category_slug field
        if ($request->hasFile('category_img')) {
            if ($category->category_img) {
                Storage::disk('public')->delete('categories/' . $category->category_img);
            }
            $imageExtension = $request->file('category_img')->getClientOriginalExtension();
            $imageName = $categorySlug . '.' . $imageExtension;
            $imagePath = $request->file('category_img')->storeAs('categories', $imageName, 'public');
            $category->category_img = $imagePath; // Update the category image path
        }
        if ($request->has('status')) {
            $category->status = $request->status;
        }

        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully!',
            'category' => $category,
        ]);
    }



    // Function to Delete a Category
    public function categoryDelete($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        if ($category->category_img) {
            $filePath = 'categories/' . basename($category->category_img);
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }
        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully!',
        ]);
    }

    public function subcategories()
    {
        $subcategories = SubCategory::all();
        $categories = Category::all(); // Fetch all categories
        return view('dashboard.admin.subcategory', compact('subcategories', 'categories'));
    }

    // Function to Add a New Subcategory
    public function subcategoryAdd(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sub_categories,name',
            'sub_category_slug' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Create a new subcategory
        $subcategory = new SubCategory();
        $subcategory->name = $request->name;
        $subcategory->category_id = $request->category_id;
        $subcategory->sub_category_slug = Str::slug($request->name);
        $subcategory->status = $request->status;
        $subcategory->save();

        return response()->json([
            'success' => true,
            'message' => 'Subcategory added successfully!',
            'subcategory' => $subcategory,
        ]);
    }

    // Function to Update an Existing Subcategory
    public function subcategoryUpdate(Request $request, $subcategoryId)
    {
        $request->validate([
            'name' => 'required|unique:sub_categories,name,' . $subcategoryId,
        ]);

        $subcategory = SubCategory::findOrFail($subcategoryId);
        $subcategory->name = $request->name;
        $subcategory->category_id = $request->category_id;
        $subcategory->sub_category_slug = Str::slug($request->name);
        $subcategory->status = $request->status;
        $subcategory->save();

        return response()->json([
            'success' => true,
            'message' => 'Subcategory updated successfully!',
            'subcategory' => $subcategory,
        ]);
    }

    // Function to Delete a Subcategory
    public function subcategoryDelete($subcategoryId)
    {
        $subcategory = SubCategory::findOrFail($subcategoryId);
        $subcategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subcategory deleted successfully!',
        ]);
    }

    public function units()
    {
        $units = Unit::all();
        return view('dashboard.admin.unit', compact('units'));
    }
    // Function to Add a New Unit
    public function unitAdd(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units,name',
        ]);

        // Create a new unit
        $unit = new Unit();
        $unit->name = $request->name;
        $unit->save();

        return response()->json([
            'success' => true,
            'message' => 'Unit added successfully!',
            'unit' => $unit,
        ]);
    }

    // Function to Update an Existing Unit
    public function unitUpdate(Request $request, $unitId)
    {
        $request->validate([
            'name' => 'required|unique:units,name,' . $unitId,
        ]);

        $unit = Unit::findOrFail($unitId);
        $unit->name = $request->name;
        $unit->save();

        return response()->json([
            'success' => true,
            'message' => 'Unit updated successfully!',
            'unit' => $unit,
        ]);
    }

    // Function to Delete a Unit
    public function unitDelete($unitId)
    {
        $unit = Unit::findOrFail($unitId);
        $unit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unit deleted successfully!',
        ]);
    }

    public function brands()
    {
        $brands = Brand::all();
        return view('dashboard.admin.brand', compact('brands'));
    }
    
    // Function to Add a New Brand
    public function brandAdd(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'brand_img' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);
        $brandSlug = Str::slug($request->name);
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->status = $request->status;
        $brand->brand_slug = $brandSlug;
        if ($request->hasFile('brand_img')) {
            $imageExtension = $request->file('brand_img')->getClientOriginalExtension();
            $imageName = $brandSlug . '.' . $imageExtension;
            $imagePath = $request->file('brand_img')->storeAs('brands', $imageName, 'public');
            $brand->brand_img = $imagePath;
        }

        $brand->save();

        return response()->json([
            'success' => true,
            'message' => 'Brand added successfully!',
            'brand' => $brand,
        ]);
    }


    public function brandUpdate(Request $request, $brandId)
    {
        $request->validate([
            'name' => 'required|unique:brands,name,' . $brandId,
            'brand_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|boolean',
        ]);

        $brand = Brand::findOrFail($brandId);
        $originalName = $brand->name;
        $brand->name = $request->name;
        $brand->status = $request->status;
        $brandSlug = Str::slug($request->name);
        $brand->brand_slug = $brandSlug; // Update the brand_slug field
        if ($request->hasFile('brand_img')) {
            if ($brand->brand_img) {
                Storage::disk('public')->delete('brands/' . $brand->brand_img);
            }
            $imageExtension = $request->file('brand_img')->getClientOriginalExtension();
            $imageName = $brandSlug . '.' . $imageExtension;
            $imagePath = $request->file('brand_img')->storeAs('brands', $imageName, 'public');
            $brand->brand_img = $imagePath; // Update the brand image path
        }
        if ($request->has('status')) {
            $brand->status = $request->status;
        }

        $brand->save();

        return response()->json([
            'success' => true,
            'message' => 'Brand updated successfully!',
            'brand' => $brand,
        ]);
    }



    // Function to Delete a Brand
    public function brandDelete($brandId)
    {
        $brand = Brand::findOrFail($brandId);
        if ($brand->brand_img) {
            $filePath = 'brands/' . basename($brand->brand_img);
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }
        $brand->delete();
        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully!',
        ]);
    }
}
