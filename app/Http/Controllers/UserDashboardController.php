<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index()
    {
        // You can pass data to the view if needed
        return view('user.dashboard'); // Replace with your actual dashboard view
    }
}
