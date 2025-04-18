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
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
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
                    <div class="d-flex align-items-center">
                        <a href="{{ route('appointment.step1') }}" class="btn btn-primary me-3 appointment-btn">Đặt lịch hẹn</a>

                        @guest
                            <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Đăng nhập</a>
                            <a href="{{ route('register') }}" class="btn btn-light">Đăng ký</a>
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
                        <h6>Đặt lịch ngay</h6>
                        <a href="{{ route('appointment.step1') }}" class="btn btn-primary mt-2 appointment-btn">Đặt lịch hẹn</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5 class="text-uppercase mb-4">Liên hệ</h5>
                    <ul class="list-unstyled contact-info">
                        <li class="mb-3"><i class="fas fa-map-marker-alt me-2"></i> Quốc lộ 1A, Diên Toàn, Diên Khánh, Khánh Hòa</li>
                        <li class="mb-3"><i class="fas fa-phone-alt me-2"></i> <a href="tel:0559764554" class="text-white text-decoration-none">0559764554</a></li>
                        <li class="mb-3"><i class="fas fa-envelope me-2"></i> <a href="mailto:hieu0559764554@gmail.com" class="text-white text-decoration-none">hieu0559764554@gmail.com</a></li>
                    </ul>
                </div>
            </div>
            <div class="text-center pt-4 mt-4 border-top">
                <p class="mb-0">&copy; {{ date('Y') }} Barber Shop. Bản quyền thuộc về chúng tôi.</p>
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

    @yield('scripts')
</body>
</html>