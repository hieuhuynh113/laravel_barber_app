<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\News;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Lấy dịch vụ nổi bật và tính toán đánh giá trung bình
        $featuredServices = Service::active()->with('category')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->limit(6)
            ->get();

        // Lấy thợ cắt tóc và tính toán đánh giá trung bình
        $barbers = Barber::active()->with('user')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->limit(4)
            ->get();

        $latestNews = News::published()->with('category')->latest()->limit(3)->get();

        // Kiểm tra tham số auth để hiển thị modal tương ứng
        $showAuthModal = null;
        if ($request->has('auth')) {
            $authType = $request->input('auth');
            if ($authType === 'login') {
                $showAuthModal = 'login';
            } elseif ($authType === 'register') {
                $showAuthModal = 'register';
            }
        }

        return view('frontend.home', compact('featuredServices', 'barbers', 'latestNews', 'showAuthModal'));
    }

    public function about()
    {
        // Lấy thợ cắt tóc và tính toán đánh giá trung bình
        $barbers = Barber::active()->with('user')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->get();

        return view('frontend.about', compact('barbers'));
    }


}
