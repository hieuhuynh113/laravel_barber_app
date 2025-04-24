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
        $sort = $request->input('sort', 'newest');
        $search = $request->input('search');

        // Tạo query builder với eager loading để tối ưu hiệu suất
        $query = Product::active()->with('category');

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply category filter (multiple categories)
        if ($categoryId && is_array($categoryId)) {
            $query->whereIn('category_id', $categoryId);
        } elseif ($categoryId) {
            $query->where('category_id', $categoryId);
        }


        // Apply price filter (multiple price ranges)
        if ($price && is_array($price)) {
            $query->where(function($q) use ($price) {
                foreach ($price as $range) {
                    list($min, $max) = explode('-', $range);
                    if ($max == 0) {
                        $q->orWhere('price', '>=', $min);
                    } else {
                        $q->orWhereBetween('price', [$min, $max]);
                    }
                }
            });
        } elseif ($price) {
            switch ($price) {
                case '0-200000':
                    $query->where('price', '<', 200000);
                    break;
                case '200000-500000':
                    $query->whereBetween('price', [200000, 500000]);
                    break;
                case '500000-1000000':
                    $query->where('price', '>', 500000);
                    break;
            }
        }

        // Apply sorting - Sử dụng switch case để tối ưu
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                // Sắp xếp theo giá giảm dần cho sản phẩm phổ biến
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc');
        }

        // Thực hiện phân trang với số lượng phù hợp và giữ query string
        $products = $query->paginate(9)->withQueryString();

        // Lấy danh sách danh mục sản phẩm đang hoạt động
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

        return view('frontend.products.index', compact('products', 'categories', 'categoryId', 'price', 'sort', 'search'));
    }

    public function show($slug)
    {
        // Tải sản phẩm với eager loading category để tối ưu hiệu suất
        $product = Product::with('category')->where('slug', $slug)->active()->firstOrFail();

        // Lấy các sản phẩm liên quan cùng danh mục
        $relatedProducts = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->orderBy('created_at', 'desc') // Sắp xếp theo thời gian tạo mới nhất
            ->limit(3)
            ->get();

        return view('frontend.products.show', compact('product', 'relatedProducts'));
    }
}
