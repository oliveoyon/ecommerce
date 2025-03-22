<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;
use App\Models\Size;

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
}
