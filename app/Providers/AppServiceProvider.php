<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
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
