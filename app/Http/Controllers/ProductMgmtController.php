<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;
use App\Models\Size;
use App\Models\Supplier;

class ProductMgmtController extends Controller
{
    public function colors()
    {
        $colors = Color::all();
        return view('dashboard.admin.color', compact('colors'));
    }
    // Function to Add a New Color
    public function colorAdd(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:colors,name',
        ]);

        // Create a new color
        $color = new Color();
        $color->name = $request->name;
        $color->save();

        return response()->json([
            'success' => true,
            'message' => 'Color added successfully!',
            'color' => $color,
        ]);
    }

    // Function to Update an Existing Color
    public function colorUpdate(Request $request, $colorId)
    {
        $request->validate([
            'name' => 'required|unique:colors,name,' . $colorId,
        ]);

        $color = Color::findOrFail($colorId);
        $color->name = $request->name;
        $color->save();

        return response()->json([
            'success' => true,
            'message' => 'Color updated successfully!',
            'color' => $color,
        ]);
    }

    // Function to Delete a Color
    public function colorDelete($colorId)
    {
        $color = Color::findOrFail($colorId);
        $color->delete();

        return response()->json([
            'success' => true,
            'message' => 'Color deleted successfully!',
        ]);
    }

    public function sizes()
    {
        $sizes = Size::all();
        return view('dashboard.admin.size', compact('sizes'));
    }
    // Function to Add a New Size
    public function sizeAdd(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sizes,name',
        ]);

        // Create a new size
        $size = new Size();
        $size->name = $request->name;
        $size->save();

        return response()->json([
            'success' => true,
            'message' => 'Size added successfully!',
            'size' => $size,
        ]);
    }

    // Function to Update an Existing Size
    public function sizeUpdate(Request $request, $sizeId)
    {
        $request->validate([
            'name' => 'required|unique:sizes,name,' . $sizeId,
        ]);

        $size = Size::findOrFail($sizeId);
        $size->name = $request->name;
        $size->save();

        return response()->json([
            'success' => true,
            'message' => 'Size updated successfully!',
            'size' => $size,
        ]);
    }

    // Function to Delete a Size
    public function sizeDelete($sizeId)
    {
        $size = Size::findOrFail($sizeId);
        $size->delete();

        return response()->json([
            'success' => true,
            'message' => 'Size deleted successfully!',
        ]);
    }

    public function suppliers()
    {
        $suppliers = Supplier::all();
        return view('dashboard.admin.supplier', compact('suppliers'));
    }
    // Function to Add a New Supplier
    public function supplierAdd(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name',
        ]);

        // Create a new supplier
        $supplier = new Supplier();
        $supplier->name = $request->name;
        $supplier->contact_person = $request->contact_person;
        $supplier->phone = $request->phone;
        $supplier->email = $request->email;
        $supplier->address = $request->address;
        $supplier->save();

        return response()->json([
            'success' => true,
            'message' => 'Supplier added successfully!',
            'supplier' => $supplier,
        ]);
    }

    // Function to Update an Existing Supplier
    public function supplierUpdate(Request $request, $supplierId)
    {
        $request->validate([
            'name' => 'required|unique:suppliers,name,' . $supplierId,
        ]);

        $supplier = Supplier::findOrFail($supplierId);
        $supplier->name = $request->name;
        $supplier->contact_person = $request->contact_person;
        $supplier->phone = $request->phone;
        $supplier->email = $request->email;
        $supplier->address = $request->address;
        $supplier->save();

        return response()->json([
            'success' => true,
            'message' => 'Supplier updated successfully!',
            'supplier' => $supplier,
        ]);
    }

    // Function to Delete a Supplier
    public function supplierDelete($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Supplier deleted successfully!',
        ]);
    }
}
