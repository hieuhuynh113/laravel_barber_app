<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('category')->latest()->paginate(10);

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $categories = Category::where('type', 'service')->where('status', 1)->get();

        return view('admin.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        // Xử lý upload hình ảnh
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
            $data['image'] = $imagePath;
        }

        Service::create($data);

        return redirect()->route('admin.services.index')
            ->with('success', 'Dịch vụ đã được tạo thành công.');
    }

    public function edit(Service $service)
    {
        $categories = Category::where('type', 'service')->where('status', 1)->get();

        // Lấy đánh giá của dịch vụ
        $reviews = $service->reviews()->with(['user', 'barber.user'])->latest()->paginate(5, ['*'], 'reviews_page');

        // Tính toán thống kê đánh giá
        $reviewsCount = $service->reviews()->count();
        $averageRating = $service->reviews()->avg('rating') ?? 0;

        // Phân bố đánh giá theo số sao
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $service->reviews()->where('rating', $i)->count();
            $ratingDistribution[$i] = [
                'count' => $count,
                'percentage' => $reviewsCount > 0 ? round(($count / $reviewsCount) * 100, 1) : 0
            ];
        }

        return view('admin.services.edit', compact('service', 'categories', 'reviews', 'reviewsCount', 'averageRating', 'ratingDistribution'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        // Xử lý upload hình ảnh
        if ($request->hasFile('image')) {
            // Xóa hình ảnh cũ
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }

            $imagePath = $request->file('image')->store('services', 'public');
            $data['image'] = $imagePath;
        }

        $service->update($data);

        return redirect()->route('admin.services.index')
            ->with('success', 'Dịch vụ đã được cập nhật thành công.');
    }

    public function destroy(Service $service)
    {
        // Xóa hình ảnh
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Dịch vụ đã được xóa thành công.');
    }
}