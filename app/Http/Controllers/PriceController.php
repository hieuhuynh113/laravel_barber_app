<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function index()
    {
        $categories = Category::service()->active()->with('services')->get();
        
        return view('frontend.price', compact('categories'));
    }
}
