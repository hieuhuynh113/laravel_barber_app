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
        
        $query = Service::active()->with('category');
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        $services = $query->paginate(9);
        $categories = Category::service()->active()->get();
        
        return view('frontend.services.index', compact('services', 'categories', 'categoryId'));
    }
    
    public function show($slug)
    {
        $service = Service::where('slug', $slug)->active()->firstOrFail();
        $relatedServices = Service::where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->active()
            ->limit(3)
            ->get();
        
        // Load active reviews for this service with pagination
        $reviews = Review::where('service_id', $service->id)
            ->active()
            ->with('user', 'barber')
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        
        // Load the reviews count for the service
        $service->loadCount('reviews');
        
        return view('frontend.services.show', compact('service', 'relatedServices', 'reviews'));
    }

    public function storeReview(Request $request, Service $service)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
            'barber_id' => 'required|exists:barbers,id',
            'review_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $review = new Review();
        $review->user_id = Auth::id();
        $review->service_id = $service->id;
        $review->barber_id = $validated['barber_id'];
        $review->rating = $validated['rating'];
        $review->comment = $validated['comment'];
        
        // Handle images upload
        if ($request->hasFile('review_images')) {
            $images = [];
            foreach ($request->file('review_images') as $image) {
                $path = $image->store('reviews', 'public');
                $images[] = 'storage/' . $path;
            }
            $review->images = json_encode($images);
        }
        
        $review->save();
        
        return redirect()->back()->with('success', 'Đánh giá của bạn đã được gửi thành công!');
    }
}
