@extends('layouts.frontend')

@section('title', 'Bảng giá dịch vụ')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/price-list.css') }}">
@endsection

@section('content')
@include('partials.page-header', [
    'title' => 'Bảng giá dịch vụ',
    'description' => 'Khám phá các dịch vụ chất lượng cao của chúng tôi với mức giá hợp lý',
    'backgroundImage' => 'images/hero-bg-1.jpg'
])

<section class="py-5 bg-light price-list-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                @forelse($categories as $category)
                    <div class="price-category mb-5">
                        <div class="price-category-header">
                            <h2 class="mb-0">{{ $category->name }}</h2>
                        </div>
                        <div class="price-category-body">
                            @foreach($category->services as $service)
                                <div class="price-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h4 class="price-item-title">{{ $service->name }}</h4>
                                            <p class="price-item-desc mb-0">{{ Str::limit($service->description, 80) }}</p>
                                        </div>
                                        <div class="col-md-3 text-md-center">
                                            <div class="price-item-duration">
                                                <i class="far fa-clock me-2"></i>{{ $service->duration }} phút
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-md-end">
                                            <div class="price-item-price">{{ number_format($service->price) }} VNĐ</div>
                                        </div>
                                    </div>
                                </div>


                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info text-center p-5">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <h4>Hiện tại chưa có dịch vụ nào</h4>
                        <p>Vui lòng quay lại sau để xem các dịch vụ của chúng tôi.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4">Vì sao chọn Barber Shop?</h2>
                <p class="mb-5">Chúng tôi tự hào cung cấp dịch vụ cắt tóc chất lượng cao với giá cả hợp lý</p>

                <div class="row mt-5">
                    <div class="col-md-4 mb-4">
                        <div class="feature-item text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-cut text-primary" style="font-size: 48px;"></i>
                            </div>
                            <h4>Thợ cắt tóc chuyên nghiệp</h4>
                            <p class="text-muted">Đội ngũ thợ cắt tóc có nhiều năm kinh nghiệm và được đào tạo bài bản.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-item text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-gem text-primary" style="font-size: 48px;"></i>
                            </div>
                            <h4>Sản phẩm cao cấp</h4>
                            <p class="text-muted">Sử dụng các sản phẩm chăm sóc tóc chất lượng cao và nhập khẩu chính hãng.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-item text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-smile text-primary" style="font-size: 48px;"></i>
                            </div>
                            <h4>Dịch vụ tận tâm</h4>
                            <p class="text-muted">Cam kết mang đến trải nghiệm thoải mái và hài lòng cho mọi khách hàng.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2>Ưu đãi đặc biệt</h2>
                <p class="lead mb-4">Đăng ký thành viên để nhận nhiều ưu đãi hấp dẫn</p>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-primary me-2"></i> Giảm 10% cho lần đầu sử dụng dịch vụ
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-primary me-2"></i> Tích điểm đổi quà với mỗi dịch vụ sử dụng
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-primary me-2"></i> Các chương trình khuyến mãi hàng tháng
                    </li>
                    <li>
                        <i class="fas fa-check-circle text-primary me-2"></i> Ưu đãi đặc biệt vào dịp sinh nhật
                    </li>
                </ul>
                <a href="{{ route('appointment.step1') }}" class="btn btn-primary mt-4 appointment-btn">Đặt lịch ngay</a>
            </div>
            <div class="col-lg-6">
                <div class="ratio ratio-16x9 rounded overflow-hidden shadow">
                    <img src="{{ asset('images/barber-special.jpg') }}" alt="Ưu đãi đặc biệt" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-primary text-white text-center cta-appointment">
    <div class="container">
        <h2 class="h1 mb-4">Sẵn sàng trải nghiệm dịch vụ của chúng tôi?</h2>
        <p class="lead mb-4">Đặt lịch ngay hôm nay và trải nghiệm dịch vụ cắt tóc chuyên nghiệp tại Barber Shop.</p>
        <a href="{{ route('appointment.step1') }}" class="btn btn-light btn-lg appointment-btn">Đặt lịch ngay</a>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Sử dụng requestAnimationFrame để tối ưu hiệu suất animation
    $(document).ready(function() {
        // Tối ưu animation cho price items
        const priceItems = document.querySelectorAll('.price-item');

        // Sử dụng CSS animation với delay ngắn hơn
        priceItems.forEach((item, index) => {
            item.style.animationDelay = (index * 0.03) + 's';
        });

        // Animation cho price items
        // Không cần xử lý sự kiện click vì đã bỏ chức năng xem chi tiết
    });
</script>
@endsection