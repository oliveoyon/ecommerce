<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{

    
    public function index()
    {
        // You can pass data to the view if needed
        return view('dashboard.admin.dashboard'); // Replace with your actual dashboard view
    }
}
