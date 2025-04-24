<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Kiểm tra nếu đang truy cập vào admin area
        if (str_starts_with($request->path(), 'admin')) {
            return '/admin/login';
        }

        // Kiểm tra nếu đang truy cập vào barber area
        if (str_starts_with($request->path(), 'barber')) {
            return '/barber/login';
        }

        return route('login');
    }
}