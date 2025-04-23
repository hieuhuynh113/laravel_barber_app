<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Service;
use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'service', 'barber.user']);

        // Lọc theo dịch vụ
        if ($request->has('service_id') && $request->service_id) {
            $query->where('service_id', $request->service_id);
        }

        // Lọc theo thợ cắt tóc
        if ($request->has('barber_id') && $request->barber_id) {
            $query->where('barber_id', $request->barber_id);
        }

        // Lọc theo số sao
        if ($request->has('rating') && $request->rating) {
            // Kiểm tra nếu rating là chuỗi có dấu phẩy (ví dụ: "1,2")
            if (strpos($request->rating, ',') !== false) {
                $ratings = explode(',', $request->rating);
                $query->whereIn('rating', $ratings);
            } else {
                $query->where('rating', $request->rating);
            }
        }

        // Lọc theo trạng thái
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sắp xếp
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $reviews = $query->paginate(10)->withQueryString();

        // Lấy danh sách dịch vụ và thợ cắt tóc cho bộ lọc
        $services = Service::active()->get();
        $barbers = Barber::with('user')->active()->get();

        return view('admin.reviews.index', compact('reviews', 'services', 'barbers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Admin không tạo đánh giá mới
        return redirect()->route('admin.reviews.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        $review->load(['user', 'service', 'barber.user']);
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        $review->load(['user', 'service', 'barber.user']);
        return view('admin.reviews.edit', compact('review'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'status' => 'required|boolean',
            'admin_response' => 'nullable|string|max:500',
        ]);

        $review->status = $validated['status'];
        $review->admin_response = $validated['admin_response'];
        $review->save();

        return redirect()->route('admin.reviews.show', $review->id)
            ->with('success', 'Đánh giá đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        // Xóa hình ảnh đánh giá nếu có
        if ($review->images) {
            $images = json_decode($review->images, true);
            foreach ($images as $image) {
                $imagePath = str_replace('storage/', 'public/', $image);
                Storage::delete($imagePath);
            }
        }

        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Đánh giá đã được xóa thành công.');
    }

    /**
     * Hiển thị thống kê đánh giá
     */
    public function statistics()
    {
        // Thống kê tổng quan
        $totalReviews = Review::count();
        $averageRating = Review::avg('rating');

        // Thống kê theo số sao
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingDistribution[$i] = [
                'count' => Review::where('rating', $i)->count(),
                'percentage' => $totalReviews > 0 ? round((Review::where('rating', $i)->count() / $totalReviews) * 100, 1) : 0
            ];
        }

        // Thống kê theo dịch vụ (top 5)
        $serviceStats = Service::withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->having('reviews_count', '>', 0)
            ->orderByDesc('reviews_count')
            ->take(5)
            ->get();

        // Thống kê theo thợ cắt tóc (top 5)
        $barberStats = Barber::with('user')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->having('reviews_count', '>', 0)
            ->orderByDesc('reviews_avg_rating')
            ->take(5)
            ->get();

        return view('admin.reviews.statistics', compact(
            'totalReviews',
            'averageRating',
            'ratingDistribution',
            'serviceStats',
            'barberStats'
        ));
    }

    /**
     * Thay đổi trạng thái đánh giá
     */
    public function toggleStatus(Review $review)
    {
        $review->status = !$review->status;
        $review->save();

        return redirect()->back()->with('success', 'Trạng thái đánh giá đã được cập nhật.');
    }
}
