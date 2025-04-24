<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarberLoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/barber';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Không sử dụng middleware
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Kiểm tra nếu người dùng đã đăng nhập
        if (auth()->check()) {
            $user = auth()->user();

            // Nếu là barber hoặc admin, chuyển hướng đến trang barber dashboard
            if ($user->role === 'barber' || $user->role === 'admin') {
                return redirect('/barber');
            }

            // Nếu không phải barber hoặc admin, chuyển hướng đến trang chủ với thông báo lỗi
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập vào trang thợ cắt tóc');
        }

        return view('auth.barber-login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            // Lưu thông tin người dùng trước khi kiểm tra vai trò
            $user = Auth::user();
            $userRole = $user ? $user->role : null;

            // Kiểm tra xem người dùng có phải là thợ cắt tóc không
            if ($userRole !== 'barber') {
                Auth::logout();

                $errorMessage = 'Bạn không có quyền truy cập vào trang thợ cắt tóc.';

                // Thêm hướng dẫn dựa trên vai trò
                if ($userRole === 'customer') {
                    $errorMessage .= ' Đây là trang đăng nhập dành cho thợ cắt tóc. Vui lòng truy cập trang chủ để đăng nhập với tài khoản khách hàng.';
                } else if ($userRole === 'admin') {
                    $errorMessage .= ' Đây là trang đăng nhập dành cho thợ cắt tóc. Vui lòng truy cập trang đăng nhập dành cho quản trị viên.';
                }

                return redirect()->back()->withErrors(['email' => $errorMessage]);
            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('web');
    }
}
