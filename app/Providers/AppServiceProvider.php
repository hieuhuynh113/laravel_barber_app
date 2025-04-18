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

            // Các cài đặt chung
            $view->with('shopName', 'Barber Shop');
            $view->with('shopAddress', '123 Đường ABC, Quận XYZ, Thành phố HCM');
            $view->with('shopPhone', '0123456789');
            $view->with('shopEmail', 'hieu0559764554@gmail.com');
            $view->with('shopWorkingHours', '8:00 - 20:00');
        });
    }
}
