<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->input('category_id');
        $level = $request->input('level');
        $price = $request->input('price');
        $sort = $request->input('sort');
        $categoryType = $request->input('category_type');
        $search = $request->input('search');

        $query = Service::active()->with('category')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

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



        // Apply price range filter (multiple price ranges)
        if ($price && is_array($price)) {
            $query->where(function($q) use ($price) {
                foreach ($price as $priceRange) {
                    $range = explode('-', $priceRange);
                    if (count($range) == 2) {
                        $minPrice = (int)$range[0];
                        $maxPrice = (int)$range[1];
                        $q->orWhereBetween('price', [$minPrice, $maxPrice]);
                    }
                }
            });
        } elseif ($price) {
            $priceRange = explode('-', $price);
            if (count($priceRange) == 2) {
                $minPrice = (int)$priceRange[0];
                $maxPrice = (int)$priceRange[1];
                $query->whereBetween('price', [$minPrice, $maxPrice]);
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
                // Sắp xếp theo giá giảm dần thay vì views (không tồn tại)
                $query->orderBy('price', 'desc');
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

        // Thực hiện phân trang với số lượng phù hợp và giữ query string
        $services = $query->paginate(6)->withQueryString(); // Reduced to 6 for better display in list view
        $categories = Category::service()->active()->get();

        // Handle AJAX request
        if ($request->ajax() || $request->input('format') === 'json') {
            $servicesHtml = view('frontend.services._service_list', compact('services'))->render();
            $paginationHtml = view('frontend.partials.pagination', ['paginator' => $services])->render();

            return response()->json([
                'html' => $servicesHtml,
                'pagination' => $paginationHtml,
                'count' => [
                    'visible' => $services->count(),
                    'total' => $services->total()
                ]
            ]);
        }

        return view('frontend.services.index', compact('services', 'categories', 'categoryId', 'level', 'price', 'sort'));
    }

    public function show($slug)
    {
        // Tải dịch vụ với eager loading category để tối ưu hiệu suất
        $service = Service::with('category')
            ->where('slug', $slug)
            ->active()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->firstOrFail();

        // Lấy các dịch vụ liên quan cùng danh mục
        $relatedServices = Service::with('category')
            ->where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->active()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy('created_at', 'desc') // Sắp xếp theo thời gian tạo mới nhất
            ->limit(3)
            ->get();

        // Load active reviews for this service with pagination
        $reviews = Review::where('service_id', $service->id)
            ->active()
            ->with('user', 'barber')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // Tính toán phân bố đánh giá theo số sao - Tối ưu bằng cách sử dụng một truy vấn duy nhất
        $ratingDistribution = [];

        // Lấy số lượng đánh giá cho mỗi mức sao trong một truy vấn
        $ratingCounts = Review::where('service_id', $service->id)
            ->selectRaw('rating, count(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        // Tạo phân bố đánh giá
        for ($i = 1; $i <= 5; $i++) {
            $count = $ratingCounts[$i] ?? 0;
            $ratingDistribution[$i] = [
                'count' => $count,
                'percentage' => $service->reviews_count > 0 ? round(($count / $service->reviews_count) * 100, 1) : 0
            ];
        }

        return view('frontend.services.show', compact('service', 'relatedServices', 'reviews', 'ratingDistribution'));
    }

    // Phương thức storeReview đã được chuyển sang ProfileController
    // Đánh giá dịch vụ chỉ được thực hiện từ trang lịch hẹn đã hoàn thành
}
