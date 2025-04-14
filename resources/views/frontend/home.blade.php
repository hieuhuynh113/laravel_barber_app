@extends('layouts.frontend')

@section('title', 'Trang chủ')

@section('hero')
<div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active" style="background-image: url('{{ asset('images/hero-bg-1.jpg') }}')">
            <div class="carousel-overlay"></div>
            <div class="container hero-content">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h1 class="hero-title">Không Chỉ Là Cắt Tóc, Đó Là Một Trải Nghiệm</h1>
                        <p class="hero-description mx-auto">Tiệm cắt tóc của chúng tôi là không gian được tạo ra dành riêng cho những người đàn ông đề cao chất lượng, thời gian và vẻ ngoài hoàn hảo.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('appointment.step1') }}" class="btn btn-primary btn-lg appointment-btn">Đặt lịch ngay</a>
                            <a href="{{ route('services.index') }}" class="btn btn-outline-light btn-lg">Xem dịch vụ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="carousel-item" style="background-image: url('{{ asset('images/hero-bg-2.jpg') }}')">
            <div class="carousel-overlay"></div>
            <div class="container hero-content">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h1 class="hero-title">Phong Cách Tóc Đẳng Cấp Dành Cho Quý Ông</h1>
                        <p class="hero-description mx-auto">Trải nghiệm dịch vụ cắt tóc chuyên nghiệp và chăm sóc tóc tuyệt vời tại Barber Shop của chúng tôi.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('appointment.step1') }}" class="btn btn-primary btn-lg">Đặt lịch ngay</a>
                            <a href="{{ route('services.index') }}" class="btn btn-outline-light btn-lg">Xem dịch vụ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="carousel-item" style="background-image: url('{{ asset('images/hero-bg-3.jpg') }}')">
            <div class="carousel-overlay"></div>
            <div class="container hero-content">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h1 class="hero-title">Nâng Tầm Phong Cách, Tôn Vinh Cá Tính</h1>
                        <p class="hero-description mx-auto">Hãy để chúng tôi giúp bạn tỏa sáng với kiểu tóc phù hợp nhất, được thực hiện bởi đội ngũ thợ cắt tóc hàng đầu.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('appointment.step1') }}" class="btn btn-primary btn-lg">Đặt lịch ngay</a>
                            <a href="{{ route('services.index') }}" class="btn btn-outline-light btn-lg">Xem dịch vụ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
@endsection

@section('content')
<!-- Giới thiệu -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="barber-collage-container position-relative">
                    <div class="collage-image collage-image-1">
                        <img src="{{ asset('images/about-1.jpg') }}" alt="Barber Shop" class="img-fluid rounded shadow">
                    </div>
                    <div class="collage-image collage-image-2">
                        <img src="{{ asset('images/about-2.jpg') }}" alt="Barber Shop" class="img-fluid rounded shadow">
                    </div>
                    <div class="collage-image collage-image-3">
                        <img src="{{ asset('images/about-3.jpg') }}" alt="Barber Shop" class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <h2 class="section-title">Về Barber Shop</h2>
                <p>Barber Shop là nơi cung cấp dịch vụ cắt tóc và chăm sóc tóc chuyên nghiệp dành cho nam giới. Với đội ngũ thợ cắt tóc giỏi và nhiều kinh nghiệm, chúng tôi cam kết mang đến cho quý khách hàng trải nghiệm tuyệt vời nhất.</p>
                <p>Chúng tôi tự hào về không gian sang trọng, dịch vụ chất lượng cao và sản phẩm chăm sóc tóc hàng đầu. Tại Barber Shop, chúng tôi không chỉ cắt tóc mà còn mang đến phong cách và sự tự tin cho quý ông.</p>
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-primary me-3" style="font-size: 24px;"></i>
                            <div>
                                <h5 class="mb-1">Chất lượng hàng đầu</h5>
                                <p class="mb-0 text-muted">Dịch vụ chuyên nghiệp</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-trophy text-primary me-3" style="font-size: 24px;"></i>
                            <div>
                                <h5 class="mb-1">Đội ngũ giỏi</h5>
                                <p class="mb-0 text-muted">Nhiều năm kinh nghiệm</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-star text-primary me-3" style="font-size: 24px;"></i>
                            <div>
                                <h5 class="mb-1">Không gian sang trọng</h5>
                                <p class="mb-0 text-muted">Thoải mái, hiện đại</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-cut text-primary me-3" style="font-size: 24px;"></i>
                            <div>
                                <h5 class="mb-1">Công cụ hiện đại</h5>
                                <p class="mb-0 text-muted">Sản phẩm chất lượng</p>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="{{ route('about') }}" class="btn btn-primary mt-3">Tìm hiểu thêm</a>
            </div>
        </div>
    </div>
</section>

<!-- Dịch vụ -->
<section class="py-5 section-services full-width-bg">
    <div class="container">
        <h2 class="section-title text-center mb-5">Dịch vụ của chúng tôi</h2>

        <div class="row">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="service-card text-center p-4 bg-white rounded shadow-sm h-100">
                    <div class="service-icon mb-3">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="service-title mb-3">Các Kiểu Tóc</h3>
                    <p class="service-description">Thợ cắt tóc chuyên nghiệp với nhiều năm kinh nghiệm tạo kiểu.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="service-card text-center p-4 bg-white rounded shadow-sm h-100">
                    <div class="service-icon mb-3">
                        <i class="fas fa-scissors"></i>
                    </div>
                    <h3 class="service-title mb-3">Tỉa Râu</h3>
                    <p class="service-description">Thợ cắt tóc chuyên nghiệp với nhiều năm kinh nghiệm tạo kiểu.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="service-card text-center p-4 bg-white rounded shadow-sm h-100">
                    <div class="service-icon mb-3">
                        <i class="fas fa-brush"></i>
                    </div>
                    <h3 class="service-title mb-3">Cạo Râu Nhẵn</h3>
                    <p class="service-description">Thợ cắt tóc chuyên nghiệp với nhiều năm kinh nghiệm tạo kiểu.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="service-card text-center p-4 bg-white rounded shadow-sm h-100">
                    <div class="service-icon mb-3">
                        <i class="fas fa-spa"></i>
                    </div>
                    <h3 class="service-title mb-3">Đắp Mặt Nạ</h3>
                    <p class="service-description">Thợ cắt tóc chuyên nghiệp với nhiều năm kinh nghiệm tạo kiểu.</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('services.index') }}" class="btn btn-primary">Xem tất cả dịch vụ</a>
        </div>
    </div>
</section>

<!-- Thợ cắt tóc -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title text-center">Đội ngũ thợ cắt tóc</h2>
        <p class="section-description text-center">Gặp gỡ đội ngũ thợ cắt tóc chuyên nghiệp của chúng tôi.</p>

        <div class="row">
            @foreach($barbers as $barber)
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="barber-item">
                    <img src="{{ asset('storage/' . $barber->user->avatar) }}" alt="{{ $barber->user->name }}" class="barber-image">
                    <h4 class="barber-name">{{ $barber->user->name }}</h4>
                    <p class="barber-position">Thợ cắt tóc {{ $barber->experience > 0 ? '- ' . $barber->experience . ' năm kinh nghiệm' : '' }}</p>
                    <p class="barber-description mb-3">{{ Str::limit($barber->description, 100) }}</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Tin tức -->
<section class="py-5 section-news full-width-section">
    <div class="container">
        <h2 class="section-title text-center">Tin tức & Mẹo</h2>
        <p class="section-description text-center">Cập nhật thông tin mới nhất về xu hướng tóc và mẹo chăm sóc tóc.</p>

        <div class="row">
            @foreach($latestNews as $item)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" alt="{{ $item->title }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $item->title }}</h5>
                        <p class="card-text">{{ Str::limit($item->content, 120) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                            <a href="{{ route('news.show', $item->slug) }}" class="btn btn-outline-primary">Đọc thêm</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('news.index') }}" class="btn btn-primary">Xem tất cả tin tức</a>
        </div>
    </div>
</section>

<!-- Đặt lịch -->
<section class="appointment-banner py-5 full-width-bg">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h2 class="h1 mb-3">Đặt lịch hẹn ngay hôm nay</h2>
                <p class="lead mb-4">Trải nghiệm dịch vụ cắt tóc chuyên nghiệp tại Barber Shop. Dễ dàng đặt lịch hẹn trực tuyến.</p>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="fas fa-check-circle me-2"></i> Thợ cắt tóc chuyên nghiệp</li>
                    <li class="mb-2"><i class="fas fa-check-circle me-2"></i> Không gian thoải mái, sang trọng</li>
                    <li><i class="fas fa-check-circle me-2"></i> Dịch vụ đa dạng, chất lượng cao</li>
                </ul>
            </div>
            <div class="col-lg-4 text-center">
                <a href="{{ route('appointment.step1') }}" class="btn btn-light btn-lg px-5 py-3 appointment-btn">Đặt lịch ngay</a>
            </div>
        </div>
    </div>
</section>
@endsection