<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            // Chỉ tải routes/web.php vì chúng ta không có routes/api.php
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        // Override the default register controller with our custom one
        $this->overrideRegisterController();
    }

    /**
     * Override the default register controller with our custom one.
     */
    protected function overrideRegisterController(): void
    {
        $this->app->bind(
            \Illuminate\Foundation\Auth\RegistersUsers::class,
            \App\Http\Controllers\Auth\CustomRegisterController::class
        );

        // Override the register route to use our custom controller
        Route::get('register', [\App\Http\Controllers\Auth\CustomRegisterController::class, 'showRegistrationForm'])
            ->name('register');
        Route::post('register', [\App\Http\Controllers\Auth\CustomRegisterController::class, 'register']);
    }
}
