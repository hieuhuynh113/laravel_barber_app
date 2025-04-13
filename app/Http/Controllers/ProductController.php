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
        $price = $request->input('price');
        $sort = $request->input('sort');

        $query = Product::active()->with('category');

        // Apply category filter
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Apply price filter
        if ($price) {
            switch ($price) {
                case 'low':
                    $query->where('price', '<', 200000);
                    break;
                case 'medium':
                    $query->whereBetween('price', [200000, 500000]);
                    break;
                case 'high':
                    $query->where('price', '>', 500000);
                    break;
            }
        }

        // Apply sorting
        if ($sort) {
            switch ($sort) {
                case 'popular':
                    $query->orderBy('views', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'recommended':
                    $query->where('is_featured', 1)->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $products = $query->paginate(9);
        $categories = Category::product()->active()->get();

        // Handle AJAX request
        if ($request->ajax() || $request->input('format') === 'json') {
            $productsHtml = view('frontend.products._product_list', compact('products'))->render();
            $paginationHtml = view('frontend.partials.pagination', ['paginator' => $products])->render();

            return response()->json([
                'html' => $productsHtml,
                'pagination' => $paginationHtml,
                'count' => [
                    'visible' => $products->count(),
                    'total' => $products->total()
                ]
            ]);
        }

        return view('frontend.products.index', compact('products', 'categories', 'categoryId', 'price', 'sort'));
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
