<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->input('category_id');
        
        $query = News::published()->with(['category', 'user']);
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        $news = $query->latest()->paginate(6);
        $categories = Category::news()->active()->get();
        
        return view('frontend.news.index', compact('news', 'categories', 'categoryId'));
    }
    
    public function show($slug)
    {
        $news = News::where('slug', $slug)->published()->firstOrFail();
        $news->increaseViewCount();
        
        $relatedNews = News::where('category_id', $news->category_id)
            ->where('id', '!=', $news->id)
            ->published()
            ->latest()
            ->limit(3)
            ->get();
        
        return view('frontend.news.show', compact('news', 'relatedNews'));
    }
}
