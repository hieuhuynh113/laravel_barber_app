<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->input('category_id');
        $level = $request->input('level');
        $sort = $request->input('sort');

        $query = Service::active()->with('category')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        // Apply category filter
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Apply level filter (assuming you have a level column)
        if ($level) {
            $query->where('level', $level);
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

        $services = $query->paginate(9);
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

        return view('frontend.services.index', compact('services', 'categories', 'categoryId', 'level', 'sort'));
    }

    public function show($slug)
    {
        $service = Service::where('slug', $slug)
            ->active()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->firstOrFail();

        $relatedServices = Service::where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->active()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->limit(3)
            ->get();

        // Load active reviews for this service with pagination
        $reviews = Review::where('service_id', $service->id)
            ->active()
            ->with('user', 'barber')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // Tính toán phân bố đánh giá theo số sao
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = Review::where('service_id', $service->id)
                ->where('rating', $i)
                ->count();
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
