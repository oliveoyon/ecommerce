<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('web.home', compact('categories'));
    }
}
