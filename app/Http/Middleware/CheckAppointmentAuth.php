<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAppointmentAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            // Lưu URL hiện tại vào session để sau khi đăng nhập có thể quay lại
            session(['url.intended' => $request->url()]);
            
            // Trả về response JSON nếu là request AJAX
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vui lòng đăng nhập để đặt lịch',
                    'redirect' => route('login')
                ], 401);
            }
            
            // Chuyển hướng đến trang đăng nhập với thông báo
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập hoặc đăng ký để đặt lịch');
        }
        
        return $next($request);
    }
}
