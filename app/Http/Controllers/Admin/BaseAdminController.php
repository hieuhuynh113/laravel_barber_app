<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BaseAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Check if the user is an admin
            if (!Auth::user() || Auth::user()->role !== 'admin') {
                return redirect('/')->with('error', 'Bạn không có quyền truy cập vào trang này');
            }
            
            return $next($request);
        });
    }
} 