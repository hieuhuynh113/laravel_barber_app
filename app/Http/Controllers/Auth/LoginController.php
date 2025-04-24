<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        attemptLogin as protected baseAttemptLogin;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Kiểm tra xem có URL dự định truy cập trong session không
        $intendedUrl = session('url.intended');

        // Xác định URL chuyển hướng dựa trên vai trò nếu không có URL dự định truy cập
        $redirectUrl = $intendedUrl ?: $this->getRedirectUrlByRole($user);

        // Xóa URL dự định truy cập khỏi session
        if ($intendedUrl) {
            session()->forget('url.intended');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'user' => $user,
                'redirect_url' => $redirectUrl,
                'has_intended_url' => !empty($intendedUrl)
            ]);
        }

        return redirect()->to($redirectUrl);
    }

    /**
     * Lấy URL chuyển hướng dựa trên vai trò của người dùng
     *
     * @param  \App\Models\User  $user
     * @return string
     */
    protected function getRedirectUrlByRole($user)
    {
        if ($user->role === 'admin') {
            return route('admin.dashboard');
        } else if ($user->role === 'barber') {
            return route('barber.dashboard');
        }

        return route('profile.index');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => trans('auth.failed')
            ], 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => trans('auth.failed'),
            ]);
    }

    /**
     * Ghi đè phương thức attemptLogin để kiểm tra vai trò
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        // Lấy thông tin đăng nhập
        $credentials = $this->credentials($request);

        // Kiểm tra xem người dùng có tồn tại không
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        // Nếu người dùng tồn tại và không phải là khách hàng, trả về lỗi
        if ($user && $user->role !== 'customer') {
            if ($request->ajax() || $request->wantsJson()) {
                abort(403, 'Vui lòng đăng nhập tại trang dành cho ' .
                    ($user->role === 'admin' ? 'quản trị viên' : 'thợ cắt tóc'));
            }

            // Thêm lỗi vào session và chuyển hướng
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    $this->username() => 'Tài khoản này không phải là tài khoản khách hàng. Vui lòng đăng nhập tại trang dành cho ' .
                        ($user->role === 'admin' ? 'quản trị viên' : 'thợ cắt tóc'),
                ])->send();
        }

        // Nếu là khách hàng, tiếp tục đăng nhập bình thường
        return $this->baseAttemptLogin($request);
    }
}
