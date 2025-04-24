<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - Barber Shop</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'Barber Shop - Nơi cung cấp dịch vụ cắt tóc và chăm sóc tóc chuyên nghiệp')">
    <meta name="keywords" content="@yield('meta_keywords', 'cắt tóc, barber, tóc nam, barber shop')">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filter-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/service-button.css') }}">

    <!-- Page Specific CSS -->
    @yield('styles')
</head>
<body class="{{ Auth::check() ? 'user-logged-in' : 'user-guest' }}">
    <!-- Header -->
    <header class="site-header">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Barber Shop Logo" height="40">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <div class="navbar-nav-container">
                        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Trang chủ</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Giới thiệu</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">Dịch vụ</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Sản phẩm</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('price.index') ? 'active' : '' }}" href="{{ route('price.index') }}">Bảng giá</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}" href="{{ route('news.index') }}">Tin tức</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}" href="{{ route('contact.index') }}">Liên hệ</a>
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex align-items-center navbar-actions">
                        <a href="{{ route('appointment.step1') }}" class="btn btn-primary me-3 appointment-btn">Đặt lịch ngay</a>

                        @guest
                            <button type="button" class="btn btn-outline-light" id="loginButton">Đăng nhập</button>
                        @else
                            <div class="dropdown">
                                <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ Auth::user()->name }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    @if(Auth::user()->role === 'admin')
                                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Quản trị</a></li>
                                    @elseif(Auth::user()->role === 'barber')
                                        <li><a class="dropdown-item" href="{{ route('barber.dashboard') }}">Bảng điều khiển</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ url('/profile') }}">Hồ sơ của tôi</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/profile/appointments') }}">Lịch hẹn của tôi</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Đăng xuất
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        @yield('hero')

        <div class="container py-5">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5 class="text-uppercase mb-4">Barber Shop</h5>
                    <p>Chúng tôi cung cấp dịch vụ cắt tóc và chăm sóc tóc chất lượng cao cho quý khách hàng, với đội ngũ thợ cắt tóc giỏi và nhiều kinh nghiệm.</p>
                    <div class="social-icons mt-4">
                        <a href="https://www.facebook.com/profile.php?id=100078969199950" class="text-white text-decoration-none me-3 facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/hiuthubar_/" class="text-white text-decoration-none me-3 instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.youtube.com/@hieuhuynh3551" class="text-white text-decoration-none me-3 youtube"><i class="fab fa-youtube"></i></a>
                        <a href="https://www.tiktok.com/@.dinhcuong" class="text-white text-decoration-none tiktok"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5 class="text-uppercase mb-4">Giờ làm việc</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">Thứ 2 - Thứ 6: 8:00 - 20:00</li>
                        <li class="mb-2">Thứ 7: 8:00 - 21:00</li>
                        <li>Chủ nhật: 9:00 - 18:00</li>
                    </ul>
                    <div class="mt-4">
                        <h6>Đặt lịch cắt tóc</h6>
                        <a href="{{ route('appointment.step1') }}" class="btn btn-primary mt-2 appointment-btn">Đặt lịch ngay</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5 class="text-uppercase mb-4">Liên hệ</h5>
                    <ul class="list-unstyled contact-info">
                        <li class="mb-3"><i class="fas fa-map-marker-alt me-2"></i> Quốc lộ 1A, Diên Toàn, Diên Khánh, Khánh Hòa</li>
                        <li class="mb-3"><i class="fas fa-phone-alt me-2"></i> <a href="tel:0559764554" class="text-white text-decoration-none">0559764554</a></li>
                        <li class="mb-3"><i class="fas fa-envelope me-2"></i> <a href="mailto:hieu.ht.63cntt@ntu.edu.vn" class="text-white text-decoration-none">hieu.ht.63cntt@ntu.edu.vn</a></li>
                    </ul>
                </div>
            </div>
            <div class="text-center pt-4 mt-4 border-top">
                <p class="mb-0">&copy; {{ date('Y') }} Barber Shop. Bản quyền thuộc về chúng tôi.</p>
                <div class="mt-2">
                    <a href="{{ route('admin.login') }}" class="text-white text-decoration-none me-3"><small>Đăng nhập quản trị</small></a>
                    <a href="{{ route('barber.login') }}" class="text-white text-decoration-none"><small>Đăng nhập thợ cắt tóc</small></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/frontend.js') }}"></script>
    <script src="{{ asset('js/appointment-auth-check.js') }}"></script>
    <script src="{{ asset('js/filter.js') }}"></script>
    <script src="{{ asset('js/login-modal.js') }}"></script>
    <script src="{{ asset('js/smooth-scroll.js') }}"></script>

    @yield('scripts')

    <!-- Login/Register Modal -->
    <div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="authModalLabel">Đăng nhập</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Alert Messages -->
                    <div id="authAlert" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
                        <span id="authAlertMessage"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <!-- Login Form -->
                    <div id="loginForm">
                        <form id="ajaxLoginForm" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="login_email" class="form-label">Email</label>
                                <div class="position-relative">
                                    <input id="login_email" type="email" class="form-control" name="email" required autocomplete="email" autofocus>
                                    <div id="emailFeedback" class="invalid-feedback">
                                        Vui lòng nhập email hợp lệ.
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="login_password" class="form-label">Mật khẩu</label>
                                <div class="position-relative">
                                    <input id="login_password" type="password" class="form-control" name="password" required autocomplete="current-password">
                                    <div id="passwordFeedback" class="invalid-feedback">
                                        Vui lòng nhập mật khẩu.
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="login_remember">
                                <label class="form-check-label" for="login_remember">
                                    Ghi nhớ đăng nhập
                                </label>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="loginButton">
                                    <span class="spinner-border spinner-border-sm d-none" id="loginSpinner" role="status" aria-hidden="true"></span>
                                    Đăng nhập
                                </button>
                            </div>
                        </form>

                        <div class="mt-3 text-center">
                            @if (Route::has('password.request'))
                                <a class="text-decoration-none" href="{{ route('password.request') }}">
                                    Quên mật khẩu?
                                </a>
                            @endif
                            <p class="mt-3">Chưa có tài khoản? <a href="javascript:void(0)" id="showRegisterForm" class="text-decoration-none">Đăng ký ngay</a></p>
                        </div>
                    </div>

                    <!-- Register Form -->
                    <div id="registerForm" style="display: none;">
                        <form id="ajaxRegisterForm" method="POST" action="{{ route('verification.send') }}" class="needs-validation" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label for="register_name" class="form-label">Họ tên</label>
                                <input id="register_name" type="text" class="form-control" name="name" required autocomplete="name" autofocus>
                                <div class="invalid-feedback">
                                    Vui lòng nhập họ tên.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="register_email" class="form-label">Email</label>
                                <input id="register_email" type="email" class="form-control" name="email" required autocomplete="email">
                                <div class="invalid-feedback">
                                    Vui lòng nhập email hợp lệ.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="register_password" class="form-label">Mật khẩu</label>
                                <input id="register_password" type="password" class="form-control" name="password" required autocomplete="new-password">
                                <div class="invalid-feedback">
                                    Mật khẩu phải có ít nhất 8 ký tự.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password-confirm" class="form-label">Xác nhận mật khẩu</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                <div class="invalid-feedback">
                                    Xác nhận mật khẩu không khớp.
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="registerButton">
                                    <span class="spinner-border spinner-border-sm d-none" id="registerSpinner" role="status" aria-hidden="true"></span>
                                    Đăng ký
                                </button>
                            </div>
                        </form>

                        <div class="mt-3 text-center">
                            <p>Đã có tài khoản? <a href="javascript:void(0)" id="showLoginForm" class="text-decoration-none">Đăng nhập ngay</a></p>
                        </div>
                    </div>

                    <!-- OTP Verification Form -->
                    <div id="otpVerificationForm" style="display: none;">
                        <div class="text-center mb-4">
                            <i class="fas fa-envelope-open-text fa-3x text-primary mb-3"></i>
                            <h4>Xác thực email</h4>
                            <p>Chúng tôi đã gửi mã OTP đến email của bạn. Vui lòng nhập mã để hoàn tất đăng ký.</p>

                            <!-- Đồng hồ đếm ngược -->
                            <div class="otp-timer-container mt-3">
                                <div class="otp-timer-label">Mã OTP sẽ hết hạn sau:</div>
                                <div id="otpExpiryTimer" class="otp-timer">05:00</div>
                            </div>
                        </div>

                        <form id="ajaxOtpForm" method="POST" action="{{ route('verification.verify') }}">
                            @csrf
                            <input type="hidden" name="email" id="otp_email">

                            <div class="mb-4">
                                <label for="otp" class="form-label">Mã xác thực (OTP)</label>
                                <div class="position-relative">
                                    <input id="otp" type="text" class="form-control form-control-lg text-center" name="otp" required autocomplete="off" autofocus maxlength="6">
                                    <div id="otpFeedback" class="invalid-feedback">
                                        Vui lòng nhập mã OTP hợp lệ.
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary" id="verifyButton">
                                    <span class="spinner-border spinner-border-sm d-none" id="verifySpinner" role="status" aria-hidden="true"></span>
                                    Xác thực
                                </button>
                            </div>
                        </form>

                        <!-- Form hết hạn OTP (hiển thị khi hết thời gian) -->
                        <div id="otpExpiredForm" class="text-center d-none">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Mã OTP đã hết hạn
                            </div>
                            <p>Bạn có thể chọn gửi lại mã mới hoặc quay lại form đăng ký.</p>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary" id="resendExpiredOtp">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi lại mã
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="backToRegister">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại đăng ký
                                </button>
                            </div>
                        </div>

                        <div class="text-center mt-3" id="resendOtpContainer">
                            <p>Chưa nhận được mã?
                                <a href="javascript:void(0)" id="resendOtp" class="text-decoration-none">
                                    Gửi lại mã
                                    <span id="otpCountdown" class="d-none">(60s)</span>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>