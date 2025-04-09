<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Barber;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = Review::with(['user', 'service', 'barber'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barbers = Barber::active()->get();
        $services = Service::active()->get();
        
        return view('reviews.create', compact('barbers', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'barber_id' => 'required|exists:barbers,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
            'review_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $review = new Review();
        $review->user_id = Auth::id();
        $review->service_id = $validated['service_id'];
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
        
        return redirect()->route('reviews.show', $review->id)->with('success', 'Đánh giá của bạn đã được gửi thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        // Check if user owns this review
        if (Auth::id() !== $review->user_id && Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Bạn không có quyền chỉnh sửa đánh giá này.');
        }
        
        $barbers = Barber::active()->get();
        $services = Service::active()->get();
        
        return view('reviews.edit', compact('review', 'barbers', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        // Check if user owns this review
        if (Auth::id() !== $review->user_id && Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Bạn không có quyền chỉnh sửa đánh giá này.');
        }
        
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
            'barber_id' => 'required|exists:barbers,id',
            'review_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_images' => 'nullable|array'
        ]);
        
        $review->rating = $validated['rating'];
        $review->comment = $validated['comment'];
        $review->barber_id = $validated['barber_id'];
        
        // Handle existing images
        $currentImages = json_decode($review->images ?? '[]', true);
        $updatedImages = $currentImages;
        
        // Remove images if specified
        if (isset($validated['remove_images'])) {
            foreach ($validated['remove_images'] as $index) {
                if (isset($currentImages[$index])) {
                    $imagePath = str_replace('storage/', 'public/', $currentImages[$index]);
                    Storage::delete($imagePath);
                    unset($updatedImages[$index]);
                }
            }
            $updatedImages = array_values($updatedImages); // Reindex array
        }
        
        // Add new images
        if ($request->hasFile('review_images')) {
            foreach ($request->file('review_images') as $image) {
                $path = $image->store('reviews', 'public');
                $updatedImages[] = 'storage/' . $path;
            }
        }
        
        $review->images = !empty($updatedImages) ? json_encode($updatedImages) : null;
        $review->save();
        
        return redirect()->route('reviews.show', $review->id)->with('success', 'Đánh giá đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        // Check if user owns this review
        if (Auth::id() !== $review->user_id && Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa đánh giá này.');
        }
        
        // Delete review images
        if ($review->images) {
            $images = json_decode($review->images, true);
            foreach ($images as $image) {
                $imagePath = str_replace('storage/', 'public/', $image);
                Storage::delete($imagePath);
            }
        }
        
        $review->delete();
        
        return redirect()->route('reviews.index')->with('success', 'Đánh giá đã được xóa thành công!');
    }
    
    // Method for user's reviews in profile
    public function userReviews()
    {
        $reviews = Review::where('user_id', Auth::id())
            ->with(['service', 'barber'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);
            
        return view('profile.reviews', compact('reviews'));
    }
}
