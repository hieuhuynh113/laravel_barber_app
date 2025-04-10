@extends('layouts.frontend')

@section('title', 'Trang chủ')

@section('hero')
<section class="hero-section" style="background-image: url('{{ asset('images/hero-bg.jpg') }}');">
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-7">
                <h1 class="hero-title">Phong cách tóc đẳng cấp dành cho quý ông</h1>
                <p class="hero-description">Trải nghiệm dịch vụ cắt tóc chuyên nghiệp và chăm sóc tóc tuyệt vời tại Barber Shop của chúng tôi.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('appointment.step1') }}" class="btn btn-primary btn-lg">Đặt lịch ngay</a>
                    <a href="{{ route('services.index') }}" class="btn btn-outline-light btn-lg">Xem dịch vụ</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('content')
<!-- Giới thiệu -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="{{ asset('images/about.jpg') }}" alt="Về chúng tôi" class="img-fluid rounded shadow">
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
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center">Dịch vụ của chúng tôi</h2>
        <p class="section-description text-center">Chúng tôi cung cấp đa dạng các dịch vụ chăm sóc tóc và cắt tóc chuyên nghiệp.</p>
        
        <div class="row">
            @foreach($featuredServices as $service)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('storage/' . $service->image) }}" class="card-img-top" alt="{{ $service->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $service->name }}</h5>
                        <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary fw-bold">{{ number_format($service->price) }} VNĐ</span>
                            <a href="{{ route('services.show', $service->slug) }}" class="btn btn-outline-primary">Chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
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

<!-- Đánh giá -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center">Khách hàng nói gì</h2>
        <p class="section-description text-center">Những đánh giá từ khách hàng đã trải nghiệm dịch vụ của chúng tôi.</p>
        
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-item">
                    <div class="testimonial-text">
                        <i class="fas fa-quote-left text-primary mb-3" style="font-size: 24px;"></i>
                        <p>Dịch vụ cắt tóc tại đây thực sự tuyệt vời. Tôi rất hài lòng với kiểu tóc mới và sẽ quay lại lần sau.</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="{{ asset('images/client1.jpg') }}" alt="Nguyễn Văn A" width="50" height="50" class="rounded-circle">
                        <div class="testimonial-author-info">
                            <h5>Nguyễn Văn A</h5>
                            <p>Khách hàng thường xuyên</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-item">
                    <div class="testimonial-text">
                        <i class="fas fa-quote-left text-primary mb-3" style="font-size: 24px;"></i>
                        <p>Thợ cắt tóc ở đây rất chuyên nghiệp và thân thiện. Không gian cũng rất thoải mái và sang trọng.</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="{{ asset('images/client2.jpg') }}" alt="Trần Văn B" width="50" height="50" class="rounded-circle">
                        <div class="testimonial-author-info">
                            <h5>Trần Văn B</h5>
                            <p>Khách hàng mới</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="testimonial-item">
                    <div class="testimonial-text">
                        <i class="fas fa-quote-left text-primary mb-3" style="font-size: 24px;"></i>
                        <p>Tôi đã thử nhiều nơi nhưng Barber Shop là nơi tôi cảm thấy hài lòng nhất. Sẽ giới thiệu cho bạn bè.</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="{{ asset('images/client3.jpg') }}" alt="Lê Văn C" width="50" height="50" class="rounded-circle">
                        <div class="testimonial-author-info">
                            <h5>Lê Văn C</h5>
                            <p>Khách hàng VIP</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tin tức -->
<section class="py-5">
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
<section class="py-5 bg-primary text-white">
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
                <a href="{{ route('appointment.step1') }}" class="btn btn-light btn-lg px-5 py-3">Đặt lịch ngay</a>
            </div>
        </div>
    </div>
</section>
@endsection 