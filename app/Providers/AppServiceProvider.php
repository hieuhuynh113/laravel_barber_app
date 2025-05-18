<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Sử dụng Bootstrap cho pagination
        Paginator::useBootstrap();
        
        // Tự động phát hiện và áp dụng template pagination phù hợp với khu vực
        View::composer('*', function ($view) {
            $route = Request::route();
            if ($route) {
                $routeName = $route->getName();
                
                // Áp dụng template pagination dựa trên khu vực của route
                if (str_starts_with($routeName, 'admin.')) {
                    Paginator::defaultView('admin.partials.pagination');
                } elseif (str_starts_with($routeName, 'barber.')) {
                    Paginator::defaultView('barber.partials.pagination');
                } else {
                    Paginator::defaultView('frontend.partials.pagination');
                }
            }
        });

        // Chia sẻ dữ liệu cho tất cả view
        View::composer('*', function ($view) {
            $view->with('serviceCategories', Category::query()->service()->active()->get());
            $view->with('productCategories', Category::query()->product()->active()->get());
            $view->with('newsCategories', Category::query()->news()->active()->get());

            // Các cài đặt chung từ config/shop.php
            $view->with('shopName', config('shop.name'));
            $view->with('shopAddress', config('shop.address'));
            $view->with('shopPhone', config('shop.phone'));
            $view->with('shopEmail', config('shop.email'));
            $view->with('shopWorkingHours', config('shop.working_hours'));
        });
    }
}
