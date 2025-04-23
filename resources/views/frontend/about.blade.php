@extends('layouts.frontend')

@section('title', 'Giới thiệu')

@section('content')
@include('partials.page-header', [
    'title' => 'Giới thiệu',
    'description' => 'Khám phá câu chuyện, sứ mệnh và đội ngũ chuyên nghiệp của Barber Shop chúng tôi',
    'backgroundImage' => 'images/about-banner.jpg'
])

<section class="py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="{{ asset('images/about-1.jpg') }}" alt="Về chúng tôi" class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6">
                <h2 class="section-title">Câu chuyện của chúng tôi</h2>
                <p>Barber Shop được thành lập vào năm 2010 với mục tiêu mang đến những trải nghiệm cắt tóc tuyệt vời nhất cho quý ông. Từ một cửa hàng nhỏ, chúng tôi đã phát triển thành một trong những địa chỉ được tin cậy hàng đầu trong lĩnh vực cắt tóc và chăm sóc tóc nam.</p>
                <p>Với đội ngũ thợ cắt tóc chuyên nghiệp và giàu kinh nghiệm, chúng tôi tự hào đã phục vụ hàng nghìn khách hàng và nhận được sự hài lòng từ họ. Sứ mệnh của chúng tôi là giúp mỗi người đàn ông trở nên tự tin hơn với vẻ ngoài của mình thông qua kiểu tóc phù hợp.</p>
                <p>Tại Barber Shop, chúng tôi không chỉ cắt tóc mà còn tạo ra phong cách, xây dựng cộng đồng và lan tỏa niềm đam mê với nghề.</p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="icon-box mb-3">
                            <i class="fas fa-bullseye text-primary" style="font-size: 48px;"></i>
                        </div>
                        <h3 class="h4 mb-3">Sứ mệnh</h3>
                        <p class="mb-0">Mang đến trải nghiệm cắt tóc và chăm sóc tóc tuyệt vời nhất cho mọi khách hàng, giúp họ tự tin với vẻ ngoài của mình.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="icon-box mb-3">
                            <i class="fas fa-eye text-primary" style="font-size: 48px;"></i>
                        </div>
                        <h3 class="h4 mb-3">Tầm nhìn</h3>
                        <p class="mb-0">Trở thành thương hiệu cắt tóc nam hàng đầu, nơi mọi khách hàng đều có trải nghiệm tuyệt vời và hài lòng.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="icon-box mb-3">
                            <i class="fas fa-heart text-primary" style="font-size: 48px;"></i>
                        </div>
                        <h3 class="h4 mb-3">Giá trị cốt lõi</h3>
                        <p class="mb-0">Chuyên nghiệp, Tôn trọng, Sáng tạo, Chất lượng và Không ngừng cải tiến là những giá trị chúng tôi luôn hướng tới.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <h2 class="section-title text-center mb-5">Tại sao chọn chúng tôi</h2>
            </div>
            <div class="col-md-6 mb-4">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        <div class="feature-icon text-center">
                            <i class="fas fa-cut text-primary" style="font-size: 36px;"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="h5 mb-2">Thợ cắt tóc chuyên nghiệp</h3>
                        <p class="mb-0">Đội ngũ thợ cắt tóc của chúng tôi được đào tạo bài bản và có nhiều năm kinh nghiệm trong ngành.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        <div class="feature-icon text-center">
                            <i class="fas fa-trophy text-primary" style="font-size: 36px;"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="h5 mb-2">Dịch vụ chất lượng cao</h3>
                        <p class="mb-0">Chúng tôi sử dụng các sản phẩm và công cụ chất lượng cao nhất để mang lại kết quả tốt nhất.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        <div class="feature-icon text-center">
                            <i class="fas fa-gem text-primary" style="font-size: 36px;"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="h5 mb-2">Không gian sang trọng</h3>
                        <p class="mb-0">Không gian cửa hàng được thiết kế hiện đại, sang trọng và thoải mái cho khách hàng.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        <div class="feature-icon text-center">
                            <i class="fas fa-clock text-primary" style="font-size: 36px;"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="h5 mb-2">Đặt lịch dễ dàng</h3>
                        <p class="mb-0">Hệ thống đặt lịch trực tuyến giúp bạn dễ dàng chọn thời gian và dịch vụ phù hợp.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Đội ngũ của chúng tôi</h2>
        <div class="row">
            @foreach($barbers as $barber)
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="barber-item text-center">
                    <img src="{{ asset('storage/' . $barber->user->avatar) }}" alt="{{ $barber->user->name }}" class="barber-image rounded-circle mb-3" style="width: 180px; height: 180px; object-fit: cover; border: 5px solid #efefef;">
                    <h4 class="barber-name">{{ $barber->user->name }}</h4>
                    <p class="barber-position text-muted">Thợ cắt tóc{{ $barber->experience > 0 ? ' - ' . $barber->experience . ' năm kinh nghiệm' : '' }}</p>

                    <!-- Đánh giá trung bình -->
                    <div class="barber-rating mb-2">
                        @php
                            $avgRating = $barber->reviews_avg_rating ?? 0;
                            $reviewsCount = $barber->reviews_count ?? 0;
                        @endphp
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($avgRating))
                                    <i class="fas fa-star text-warning"></i>
                                @elseif($i - 0.5 <= $avgRating)
                                    <i class="fas fa-star-half-alt text-warning"></i>
                                @else
                                    <i class="far fa-star text-warning"></i>
                                @endif
                            @endfor
                            <span class="ms-1 text-muted">({{ number_format($avgRating, 1) }})</span>
                        </div>
                        <small class="text-muted">{{ $reviewsCount }} đánh giá</small>
                    </div>

                    <p class="barber-description mb-3">{{ Str::limit($barber->description, 100) }}</p>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5 text-white text-center cta-appointment">
    <div class="container">
        <h2 class="h1 mb-4">Sẵn sàng trải nghiệm dịch vụ của chúng tôi?</h2>
        <p class="lead mb-4">Đặt lịch ngay hôm nay để có trải nghiệm cắt tóc tuyệt vời!</p>
        <a href="{{ route('appointment.step1') }}" class="btn btn-light btn-lg appointment-btn">Đặt lịch ngay</a>
    </div>
</section>
@endsection