<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\News;
use App\Models\Service;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredServices = Service::active()->with('category')->limit(6)->get();
        $barbers = Barber::active()->with('user')->limit(4)->get();
        $latestNews = News::published()->with('category')->latest()->limit(3)->get();
        
        return view('frontend.home', compact('featuredServices', 'barbers', 'latestNews'));
    }

    public function about()
    {
        $barbers = Barber::active()->with('user')->get();
        
        return view('frontend.about', compact('barbers'));
    }
}
