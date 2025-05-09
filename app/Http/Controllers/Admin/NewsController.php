<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->input('category_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = News::with(['category', 'user']);

        // Lọc theo danh mục
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Lọc theo trạng thái
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        // Tìm kiếm theo tiêu đề hoặc nội dung
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $news = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::where('type', 'news')->get();

        return view('admin.news.index', compact('news', 'categories', 'categoryId', 'status', 'search'));
    }

    public function create()
    {
        $categories = Category::where('type', 'news')->active()->get();
        return view('admin.news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
            // Loại bỏ validation cho các trường meta không tồn tại
        ]);

        $news = new News([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'category_id' => $request->category_id,
            'content' => $request->content,
            'user_id' => Auth::id(),
            'status' => $request->status,
            // Loại bỏ các trường meta không tồn tại
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news', 'public');
            $news->image = $imagePath;
        }

        $news->save();

        return redirect()->route('admin.news.index')
            ->with('success', 'Bài viết đã được tạo thành công.');
    }

    public function show(News $news)
    {
        $news->load(['category', 'user']);
        return view('admin.news.show', compact('news'));
    }

    public function edit(News $news)
    {
        $categories = Category::where('type', 'news')->active()->get();
        return view('admin.news.edit', compact('news', 'categories'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
            // Loại bỏ validation cho các trường meta không tồn tại
        ]);

        $news->title = $request->title;

        // Chỉ cập nhật slug nếu tiêu đề thay đổi
        if ($news->isDirty('title')) {
            $news->slug = Str::slug($request->title);
        }

        $news->category_id = $request->category_id;
        $news->content = $request->content;
        $news->status = $request->status;
        // Loại bỏ cập nhật các trường meta không tồn tại

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }

            $imagePath = $request->file('image')->store('news', 'public');
            $news->image = $imagePath;
        }

        $news->save();

        return redirect()->route('admin.news.index')
            ->with('success', 'Bài viết đã được cập nhật thành công.');
    }

    public function destroy(News $news)
    {
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'Bài viết đã được xóa thành công.');
    }

    public function toggleFeatured(News $news)
    {
        $news->is_featured = !$news->is_featured;
        $news->save();

        return redirect()->back()
            ->with('success', 'Trạng thái nổi bật của bài viết đã được cập nhật.');
    }
}