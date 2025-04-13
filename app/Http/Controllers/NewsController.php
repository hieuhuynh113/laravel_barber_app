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
        $timeFilter = $request->input('time');
        $sort = $request->input('sort');

        $query = News::published()->with(['category', 'user']);

        // Apply category filter
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Apply time filter
        if ($timeFilter) {
            switch ($timeFilter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
            }
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
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $news = $query->paginate(6);
        $categories = Category::where('type', 'news')->where('status', 1)->get();

        // Handle AJAX request
        if ($request->ajax() || $request->input('format') === 'json') {
            $newsHtml = view('frontend.news._news_list', compact('news'))->render();
            $paginationHtml = view('frontend.partials.pagination', ['paginator' => $news])->render();

            return response()->json([
                'html' => $newsHtml,
                'pagination' => $paginationHtml,
                'count' => [
                    'visible' => $news->count(),
                    'total' => $news->total()
                ]
            ]);
        }

        return view('frontend.news.index', compact('news', 'categories', 'categoryId', 'timeFilter', 'sort'));
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
