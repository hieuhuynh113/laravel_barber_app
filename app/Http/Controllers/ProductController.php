<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->input('category_id');
        
        $query = Product::active()->with('category');
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        $products = $query->paginate(9);
        $categories = Category::product()->active()->get();
        
        return view('frontend.products.index', compact('products', 'categories', 'categoryId'));
    }
    
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->active()->firstOrFail();
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->limit(3)
            ->get();
        
        return view('frontend.products.show', compact('product', 'relatedProducts'));
    }
}
