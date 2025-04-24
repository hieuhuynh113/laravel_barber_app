<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Nếu đang truy cập vào admin area và là admin, chuyển hướng đến admin dashboard
                if (str_starts_with($request->path(), 'admin') && $user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }

                // Nếu đang truy cập vào barber area và là barber hoặc admin, chuyển hướng đến barber dashboard
                if (str_starts_with($request->path(), 'barber') && ($user->role === 'barber' || $user->role === 'admin')) {
                    return redirect()->route('barber.dashboard');
                }

                // Nếu không phải các trường hợp trên, chuyển hướng theo vai trò
                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                } else if ($user->role === 'barber') {
                    return redirect()->route('barber.dashboard');
                }

                return redirect()->route('profile.index');
            }
        }

        return $next($request);
    }
}