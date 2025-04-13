<?php

namespace App\Http\Controllers;

use App\Models\EmailVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

        return view('auth.verify-otp', compact('email'));
    }

    /**
     * Gửi mã OTP đến email
     */
    public function sendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

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

        if (!$emailSent && config('app.env') !== 'local') {
            return back()->with('error', 'Không thể gửi email xác thực. Vui lòng thử lại sau.');
        }

        // Chuẩn bị thông báo
        $message = 'Chúng tôi đã gửi mã OTP đến email của bạn. Vui lòng kiểm tra và nhập mã để hoàn tất đăng ký.';

        // Chỉ hiển thị hướng dẫn kiểm tra log trong môi trường phát triển và khi sử dụng log driver
        if (config('app.env') === 'local' && config('mail.mailer') === 'log') {
            $message .= ' (Kiểm tra mã OTP trong log của ứng dụng)';
        }

        return redirect()->route('verification.form', ['email' => $request->email])
            ->with('success', $message);
    }

    /**
     * Xác thực OTP và hoàn tất đăng ký
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $verification = EmailVerification::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$verification) {
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

        return redirect()->route('home')
            ->with('success', 'Đăng ký tài khoản thành công!');
    }

    /**
     * Gửi lại mã OTP
     */
    public function resendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $verification = EmailVerification::where('email', $request->email)->first();

        if (!$verification) {
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

        if (!$emailSent && config('app.env') !== 'local') {
            return back()->with('error', 'Không thể gửi email xác thực. Vui lòng thử lại sau.');
        }

        // Chuẩn bị thông báo
        $message = 'Chúng tôi đã gửi lại mã OTP đến email của bạn.';

        // Chỉ hiển thị hướng dẫn kiểm tra log trong môi trường phát triển và khi sử dụng log driver
        if (config('app.env') === 'local' && config('mail.mailer') === 'log') {
            $message .= ' (Kiểm tra mã OTP trong log của ứng dụng)';
        }

        return back()->with('success', $message);
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
