@extends('layouts.frontend')

@section('title', 'Xác thực OTP')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Xác thực tài khoản</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <i class="fas fa-envelope-open-text fa-3x text-primary mb-3"></i>
                        <h5>Xác thực email của bạn</h5>
                        <p>Chúng tôi đã gửi mã xác thực (OTP) đến email <strong>{{ $email }}</strong>.<br>Vui lòng kiểm tra hộp thư đến và nhập mã xác thực để hoàn tất đăng ký.</p>
                        <div class="alert alert-warning">
                            <i class="fas fa-clock"></i> Mã OTP sẽ hết hạn sau <span id="otpExpiryTimer" class="fw-bold">10:00</span> phút
                        </div>
                    </div>

                    <form method="POST" action="{{ route('verification.verify') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="mb-4">
                            <label for="otp" class="form-label">Mã xác thực (OTP)</label>
                            <input id="otp" type="text" class="form-control form-control-lg text-center @error('otp') is-invalid @enderror" name="otp" required autocomplete="off" autofocus maxlength="6">
                            @error('otp')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Xác thực
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 text-center">
                        <p>Không nhận được mã?</p>
                        <form id="resendForm" method="POST" action="{{ route('verification.resend') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <button id="resendButton" type="submit" class="btn btn-primary">
                                Gửi lại mã
                            </button>
                        </form>
                        <div id="countdown" class="mt-2 d-none">
                            <span class="text-muted">Gửi lại mã sau <span id="timer">60</span> giây</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tự động focus vào ô nhập OTP
        document.getElementById('otp').focus();

        // Xử lý đếm ngược thời gian cho nút gửi lại mã
        const resendButton = document.getElementById('resendButton');
        const countdownElement = document.getElementById('countdown');
        const timerElement = document.getElementById('timer');
        const resendForm = document.getElementById('resendForm');
        const otpExpiryTimer = document.getElementById('otpExpiryTimer');

        // Kiểm tra nếu có thời gian đếm ngược trong localStorage
        const resendTime = localStorage.getItem('resendTime');
        if (resendTime && new Date().getTime() < parseInt(resendTime)) {
            // Còn thời gian đếm ngược, hiển thị đếm ngược
            startCountdown(Math.ceil((parseInt(resendTime) - new Date().getTime()) / 1000));
        }

        // Bắt đầu đếm ngược thời gian hết hạn OTP
        @if(isset($expiryTime) && $expiryTime > 0)
            // Sử dụng thời gian hết hạn từ server
            startOtpExpiryCountdown({{ $expiryTime }});
        @else
            // Sử dụng thời gian mặc định 10 phút
            startOtpExpiryCountdown();
        @endif

        // Hàm bắt đầu đếm ngược thời gian hết hạn OTP
        function startOtpExpiryCountdown(initialSeconds = null) {
            let secondsRemaining;

            // Nếu có thời gian ban đầu từ server, sử dụng nó
            if (initialSeconds !== null) {
                secondsRemaining = initialSeconds;
                // Lưu thời gian hết hạn vào localStorage
                const otpExpiryTime = new Date().getTime() + (secondsRemaining * 1000);
                localStorage.setItem('otpExpiryTime', otpExpiryTime.toString());
            } else {
                // Lấy thời gian hết hạn OTP từ localStorage hoặc tạo mới
                let otpExpiryTime = localStorage.getItem('otpExpiryTime');

                // Nếu không có hoặc đã hết hạn, tạo mới (10 phút từ bây giờ)
                if (!otpExpiryTime || new Date().getTime() > parseInt(otpExpiryTime)) {
                    otpExpiryTime = new Date().getTime() + (10 * 60 * 1000); // 10 phút
                    localStorage.setItem('otpExpiryTime', otpExpiryTime.toString());
                }

                // Tính số giây còn lại
                secondsRemaining = Math.max(0, Math.ceil((parseInt(otpExpiryTime) - new Date().getTime()) / 1000));
            }
            updateOtpExpiryTimer(secondsRemaining);

            // Cập nhật đếm ngược mỗi giây
            const otpExpiryInterval = setInterval(function() {
                secondsRemaining--;

                if (secondsRemaining <= 0) {
                    clearInterval(otpExpiryInterval);
                    // Hiển thị thông báo OTP đã hết hạn
                    otpExpiryTimer.parentElement.classList.remove('alert-warning');
                    otpExpiryTimer.parentElement.classList.add('alert-danger');
                    otpExpiryTimer.parentElement.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Mã OTP đã hết hạn. Vui lòng gửi lại mã mới.';

                    // Vô hiệu hóa form nhập OTP
                    document.getElementById('otp').disabled = true;
                    document.querySelector('button[type="submit"]').disabled = true;

                    // Xóa thời gian hết hạn khỏi localStorage
                    localStorage.removeItem('otpExpiryTime');
                } else {
                    updateOtpExpiryTimer(secondsRemaining);
                }
            }, 1000);
        }

        // Hàm cập nhật hiển thị thời gian hết hạn OTP
        function updateOtpExpiryTimer(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            otpExpiryTimer.textContent = `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;

            // Đổi màu khi gần hết thời gian
            if (seconds < 60) { // Dưới 1 phút
                otpExpiryTimer.classList.add('text-danger');
            }
        }

        // Xử lý sự kiện submit form gửi lại mã
        resendForm.addEventListener('submit', function(e) {
            // Không ngăn chặn form submit để gửi yêu cầu gửi lại mã
            // Nhưng bắt đầu đếm ngược ngay lập tức
            startCountdown(60);

            // Lưu thời gian kết thúc đếm ngược vào localStorage
            const endTime = new Date().getTime() + (60 * 1000);
            localStorage.setItem('resendTime', endTime.toString());

            // Reset thời gian hết hạn OTP (10 phút mới)
            const newOtpExpiryTime = new Date().getTime() + (10 * 60 * 1000);
            localStorage.setItem('otpExpiryTime', newOtpExpiryTime.toString());

            // Kích hoạt lại form nhập OTP nếu đã bị vô hiệu hóa
            document.getElementById('otp').disabled = false;
            document.querySelector('button[type="submit"]').disabled = false;

            // Cập nhật giao diện thông báo
            if (otpExpiryTimer.parentElement.classList.contains('alert-danger')) {
                otpExpiryTimer.parentElement.classList.remove('alert-danger');
                otpExpiryTimer.parentElement.classList.add('alert-warning');
                otpExpiryTimer.parentElement.innerHTML = '<i class="fas fa-clock"></i> Mã OTP sẽ hết hạn sau <span id="otpExpiryTimer" class="fw-bold">10:00</span> phút';
                // Cập nhật lại tham chiếu đến otpExpiryTimer vì đã thay đổi DOM
                otpExpiryTimer = document.getElementById('otpExpiryTimer');
            }
        });

        // Hàm bắt đầu đếm ngược
        function startCountdown(seconds) {
            // Vô hiệu hóa nút gửi lại mã
            resendButton.disabled = true;
            resendButton.classList.add('btn-secondary');
            resendButton.classList.remove('btn-primary');

            // Hiển thị đếm ngược
            countdownElement.classList.remove('d-none');
            timerElement.textContent = seconds;

            // Bắt đầu đếm ngược
            const countdownInterval = setInterval(function() {
                seconds--;
                timerElement.textContent = seconds;

                if (seconds <= 0) {
                    // Dừng đếm ngược
                    clearInterval(countdownInterval);

                    // Kích hoạt lại nút gửi lại mã
                    resendButton.disabled = false;
                    resendButton.classList.remove('btn-secondary');
                    resendButton.classList.add('btn-primary');

                    // Ẩn đếm ngược
                    countdownElement.classList.add('d-none');

                    // Xóa thời gian đếm ngược khỏi localStorage
                    localStorage.removeItem('resendTime');
                }
            }, 1000);
        }
    });
</script>
@endsection
