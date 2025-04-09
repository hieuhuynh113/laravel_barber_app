<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Setting;
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
            $view->with('shopName', Setting::getValue('shop_name', 'Barber Shop'));
            $view->with('shopAddress', Setting::getValue('shop_address', '123 Đường ABC, Quận XYZ, Thành phố HCM'));
            $view->with('shopPhone', Setting::getValue('shop_phone', '0123456789'));
            $view->with('shopEmail', Setting::getValue('shop_email', 'info@barbershop.com'));
            $view->with('shopWorkingHours', Setting::getValue('shop_working_hours', '8:00 - 20:00'));
        });
    }
}
