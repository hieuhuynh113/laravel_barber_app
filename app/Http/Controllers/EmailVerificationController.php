<?php

namespace App\Http\Controllers;

use App\Models\EmailVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmailVerificationController extends Controller
{
    /**
     * Hiển thị form xác thực OTP
     */
    public function showVerificationForm(Request $request)
    {
        $email = $request->email;

        if (!$email) {
            return redirect()->route('register')->with('error', 'Email không hợp lệ');
        }

        // Lấy thông tin về OTP để kiểm tra thời gian hết hạn
        $verification = EmailVerification::where('email', $email)->first();
        $expiryTime = null;

        if ($verification && $verification->expires_at) {
            $expiryTime = $verification->expires_at->diffInSeconds(now());
            if ($expiryTime <= 0) {
                // OTP đã hết hạn
                return view('auth.verify-otp', compact('email'))->with('error', 'Mã OTP đã hết hạn. Vui lòng gửi lại mã mới.');
            }
        }

        return view('auth.verify-otp', compact('email', 'expiryTime'));
    }

    /**
     * Gửi mã OTP đến email
     */
    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
        ], [
            'password.regex' => 'Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.'
        ]);

        if ($validator->fails()) {
            // Kiểm tra xem có lỗi email unique không
            $errors = $validator->errors();
            $errorMessage = 'Thông tin không hợp lệ';

            if ($errors->has('email') && str_contains($errors->first('email'), 'đã được sử dụng')) {
                $errorMessage = 'Email này đã được sử dụng. Vui lòng chọn email khác hoặc đăng nhập nếu đây là tài khoản của bạn.';
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        // Tạo mã OTP ngẫu nhiên 6 số
        $otp = sprintf('%06d', mt_rand(1, 999999));

        // Lưu thông tin đăng ký và OTP vào bảng email_verifications
        EmailVerification::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(10), // OTP hết hạn sau 10 phút
            ]
        );

        // Gửi email chứa mã OTP
        $emailSent = $this->sendOTPEmail($request->email, $otp);

        // Lấy thời gian hết hạn để hiển thị cho người dùng
        $expiryTime = 600; // 10 phút = 600 giây

        if (!$emailSent && config('app.env') !== 'local') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể gửi email xác thực. Vui lòng thử lại sau.'
                ], 500);
            }

            return back()->with('error', 'Không thể gửi email xác thực. Vui lòng thử lại sau.');
        }

        // Chuẩn bị thông báo
        $message = 'Chúng tôi đã gửi mã OTP đến email của bạn. Vui lòng kiểm tra và nhập mã để hoàn tất đăng ký.';

        // Chỉ hiển thị hướng dẫn kiểm tra log trong môi trường phát triển và khi sử dụng log driver
        if (config('app.env') === 'local' && config('mail.mailer') === 'log') {
            $message .= ' (Kiểm tra mã OTP trong log của ứng dụng)';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'expiryTime' => $expiryTime
            ]);
        }

        return redirect()->route('verification.form', ['email' => $request->email])
            ->with('success', $message)
            ->with('expiryTime', $expiryTime);
    }

    /**
     * Xác thực OTP và hoàn tất đăng ký
     */
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thông tin không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()->withErrors($validator);
        }

        $verification = EmailVerification::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$verification) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã OTP không hợp lệ hoặc đã hết hạn'
                ], 422);
            }

            return back()->with('error', 'Mã OTP không hợp lệ hoặc đã hết hạn');
        }

        // Tạo tài khoản người dùng mới
        $user = User::create([
            'name' => $verification->name,
            'email' => $verification->email,
            'password' => $verification->password,
            'role' => 'customer',
            'email_verified_at' => Carbon::now(),
        ]);

        // Xóa bản ghi xác thực
        $verification->delete();

        // Đăng nhập người dùng
        auth()->login($user);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đăng ký tài khoản thành công!',
                'user' => $user,
                'redirect_url' => route('profile.index') // Người dùng mới luôn là khách hàng, chuyển hướng đến trang profile
            ]);
        }

        return redirect()->route('home')
            ->with('success', 'Đăng ký tài khoản thành công!');
    }

    /**
     * Gửi lại mã OTP
     */
    public function resendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()->withErrors($validator);
        }

        $verification = EmailVerification::where('email', $request->email)->first();

        if (!$verification) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin đăng ký'
                ], 404);
            }

            return back()->with('error', 'Không tìm thấy thông tin đăng ký');
        }

        // Tạo mã OTP mới
        $otp = sprintf('%06d', mt_rand(1, 999999));

        // Cập nhật mã OTP và thời gian hết hạn
        $verification->update([
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Gửi email chứa mã OTP
        $emailSent = $this->sendOTPEmail($request->email, $otp);

        // Lấy thời gian hết hạn để hiển thị cho người dùng
        $expiryTime = 600; // 10 phút = 600 giây

        if (!$emailSent && config('app.env') !== 'local') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể gửi email xác thực. Vui lòng thử lại sau.'
                ], 500);
            }

            return back()->with('error', 'Không thể gửi email xác thực. Vui lòng thử lại sau.');
        }

        // Chuẩn bị thông báo
        $message = 'Chúng tôi đã gửi lại mã OTP đến email của bạn.';

        // Chỉ hiển thị hướng dẫn kiểm tra log trong môi trường phát triển và khi sử dụng log driver
        if (config('app.env') === 'local' && config('mail.mailer') === 'log') {
            $message .= ' (Kiểm tra mã OTP trong log của ứng dụng)';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'expiryTime' => $expiryTime
            ]);
        }

        return back()->with('success', $message)->with('expiryTime', $expiryTime);
    }

    /**
     * Gửi email chứa mã OTP
     */
    private function sendOTPEmail($email, $otp)
    {
        try {
            $data = [
                'otp' => $otp,
                'email' => $email
            ];

            Mail::send('emails.otp', $data, function($message) use ($email) {
                $message->to($email)
                    ->subject('[Barber Shop] Mã xác thực đăng ký tài khoản của bạn')
                    ->priority(1); // Đặt ưu tiên cao nhất
            });

            // Luôn ghi log mã OTP để dễ dàng kiểm tra
            \Log::info("OTP for {$email}: {$otp}");

            return true;
        } catch (\Exception $e) {
            // Ghi log lỗi chi tiết
            \Log::error("Failed to send OTP email: " . $e->getMessage());
            \Log::error("Error details: " . $e->getTraceAsString());

            // Trong môi trường phát triển, vẫn tiếp tục quá trình đăng ký
            if (config('app.env') === 'local') {
                \Log::info("OTP for {$email}: {$otp}");
                return true;
            }

            return false;
        }
    }
}
