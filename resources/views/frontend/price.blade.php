@extends('layouts.frontend')

@section('title', 'Thành viên & Giá cả')

@section('styles')

@endsection

@section('content')
@include('partials.page-header', [
    'title' => 'Thành viên & Giá cả',
    'description' => 'Trải nghiệm dịch vụ cao cấp với giá ưu đãi khi trở thành thành viên',
    'backgroundImage' => 'images/hero-bg-1.jpg'
])

<!-- Membership Plans Section -->
<section class="membership-section">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4">Gói thành viên</h2>
                <p class="lead">Trở thành thành viên để nhận nhiều ưu đãi đặc biệt và dịch vụ chăm sóc tóc cao cấp</p>
            </div>
        </div>

        <div class="row">
            <!-- Basic Plan -->
            <div class="col-lg-4 mb-4">
                <div class="membership-plan basic h-100">
                    <div class="membership-header">
                        <div class="membership-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="membership-title">Basic</h3>
                        <div class="membership-price">299.000 VNĐ</div>
                        <p class="membership-duration">1 tháng</p>
                    </div>
                    <div class="membership-body">
                        <ul class="membership-features">
                            <li class="membership-feature included">
                                <i class="fas fa-check-circle"></i>
                                <span>Giảm 10% cho tất cả dịch vụ</span>
                            </li>
                            <li class="membership-feature included">
                                <i class="fas fa-check-circle"></i>
                                <span>Ưu tiên đặt lịch</span>
                            </li>
                            <li class="membership-feature included">
                                <i class="fas fa-check-circle"></i>
                                <span>1 lần cắt tóc miễn phí</span>
                            </li>
                            <li class="membership-feature excluded">
                                <i class="fas fa-times-circle"></i>
                                <span>Dịch vụ chăm sóc da miễn phí</span>
                            </li>
                            <li class="membership-feature excluded">
                                <i class="fas fa-times-circle"></i>
                                <span>Sản phẩm chăm sóc tóc miễn phí</span>
                            </li>
                        </ul>
                        <div class="membership-cta">
                            <a href="{{ route('appointment.step1') }}" class="membership-btn">
                                Trải nghiệm ngay <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Premium Plan -->
            <div class="col-lg-4 mb-4">
                <div class="membership-plan premium popular h-100">
                    <div class="membership-header">
                        <div class="membership-icon">
                            <i class="fas fa-crown"></i>
                        </div>
                        <h3 class="membership-title">Premium</h3>
                        <div class="membership-price">599.000 VNĐ</div>
                        <p class="membership-duration">3 tháng</p>
                    </div>
                    <div class="membership-body">
                        <ul class="membership-features">
                            <li class="membership-feature included">
                                <i class="fas fa-check-circle"></i>
                                <span>Giảm 15% cho tất cả dịch vụ</span>
                            </li>
                            <li class="membership-feature included">
                                <i class="fas fa-check-circle"></i>
                                <span>Ưu tiên đặt lịch</span>
                            </li>
                            <li class="membership-feature included">
                                <i class="fas fa-check-circle"></i>
                                <span>3 lần cắt tóc miễn phí</span>
                            </li>
                            <li class="membership-feature included">
                                <i class="fas fa-check-circle"></i>
                                <span>1 lần chăm sóc da miễn phí</span>
                            </li>
                            <li class="membership-feature excluded">
                                <i class="fas fa-times-circle"></i>
                                <span>Sản phẩm chăm sóc tóc miễn phí</span>
                            </li>
                        </ul>
                        <div class="membership-cta">
                            <a href="{{ route('appointment.step1') }}" class="membership-btn">
                                Trải nghiệm ngay <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VIP Plan -->
            <div class="col-lg-4 mb-4">
                <div class="membership-plan vip h-100">
                    <div class="membership-header">
                        <div class="membership-icon">
                            <i class="fas fa-gem"></i>
                        </div>
                        <h3 class="membership-title">VIP</h3>
                        <div class="membership-price">999.000 VNĐ</div>
                        <p class="membership-duration">6 tháng</p>
                    </div>
                    <div class="membership-body">
                        <ul class="membership-features">
                            <li class="membership-feature included">
                                <i class="fas fa-check-circle"></i>
                                <span>Giảm 20% cho tất cả dịch vụ</span>
                            </li>
                            <li class="membership-feature included">
                                <i class="fas fa-check-circle"></i>
                                <span>Ưu tiên đặt lịch cao nhất</span>
                            </li>
                            <li class="membership-feature included">
                                <i class="fas fa-check-circle"></i>
                                <span>6 lần cắt tóc miễn phí</span>
                            </li>
                            <li class="membership-feature included">
                                <i class="fas fa-check-circle"></i>
                                <span>2 lần chăm sóc da miễn phí</span>
                            </li>
                            <li class="membership-feature included">
                                <i class="fas fa-check-circle"></i>
                                <span>Bộ sản phẩm chăm sóc tóc miễn phí</span>
                            </li>
                        </ul>
                        <div class="membership-cta">
                            <a href="{{ route('appointment.step1') }}" class="membership-btn">
                                Trải nghiệm ngay <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Table Section -->
<section class="pricing-table-section">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4">Bảng giá dịch vụ</h2>
                <p class="lead">So sánh giá thông thường và giá thành viên để thấy được lợi ích khi đăng ký gói thành viên</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="table-responsive">
                    <table class="pricing-table">
                        <thead>
                            <tr>
                                <th width="40%">Dịch vụ</th>
                                <th width="20%">Thời gian</th>
                                <th width="20%">Giá thông thường</th>
                                <th width="20%">Giá thành viên</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td colspan="4" style="background-color: #f8f9fa; font-weight: 700;">{{ $category->name }}</td>
                                </tr>
                                @foreach($category->services as $service)
                                    <tr>
                                        <td>
                                            <div class="service-name">{{ $service->name }}</div>
                                        </td>
                                        <td>
                                            <div class="service-duration">
                                                <i class="far fa-clock"></i> {{ $service->duration }} phút
                                            </div>
                                        </td>
                                        <td>
                                            <div class="price-regular">{{ number_format($service->price) }} VNĐ</div>
                                        </td>
                                        <td>
                                            <div class="price-member">{{ number_format($service->price * 0.85) }} VNĐ</div>
                                            <div class="savings">Tiết kiệm 15%</div>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-info-circle fa-2x mb-3 text-muted"></i>
                                        <h5>Hiện tại chưa có dịch vụ nào</h5>
                                        <p class="mb-0">Vui lòng quay lại sau để xem các dịch vụ của chúng tôi.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Combo Deals Section -->
<section class="combo-deals-section">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4">Combo tiết kiệm</h2>
                <p class="lead">Tiết kiệm hơn với các gói combo dịch vụ được thiết kế đặc biệt</p>
            </div>
        </div>

        <div class="row">
            <!-- Combo 1 -->
            <div class="col-lg-4 mb-4">
                <div class="combo-deal h-100">
                    <div class="combo-header">
                        <h3>Combo Cơ bản</h3>
                    </div>
                    <div class="combo-body d-flex flex-column">
                        <ul class="combo-services flex-grow-1">
                            <li class="combo-service">
                                <div class="combo-service-icon">
                                    <i class="fas fa-cut"></i>
                                </div>
                                <div class="combo-service-details">
                                    <div class="combo-service-name">Cắt tóc nam</div>
                                    <div class="combo-service-duration">
                                        <i class="far fa-clock"></i> 30 phút
                                    </div>
                                </div>
                            </li>
                            <li class="combo-service">
                                <div class="combo-service-icon">
                                    <i class="fas fa-shower"></i>
                                </div>
                                <div class="combo-service-details">
                                    <div class="combo-service-name">Gội đầu</div>
                                    <div class="combo-service-duration">
                                        <i class="far fa-clock"></i> 15 phút
                                    </div>
                                </div>
                            </li>
                            <!-- Placeholder để giữ chiều cao đồng đều -->
                            <li class="combo-service invisible">
                                <div class="combo-service-icon">
                                    <i class="fas fa-spa"></i>
                                </div>
                                <div class="combo-service-details">
                                    <div class="combo-service-name">Placeholder</div>
                                    <div class="combo-service-duration">
                                        <i class="far fa-clock"></i> 15 phút
                                    </div>
                                </div>
                            </li>
                            <!-- Placeholder để giữ chiều cao đồng đều -->
                            <li class="combo-service invisible">
                                <div class="combo-service-icon">
                                    <i class="fas fa-razor"></i>
                                </div>
                                <div class="combo-service-details">
                                    <div class="combo-service-name">Placeholder</div>
                                    <div class="combo-service-duration">
                                        <i class="far fa-clock"></i> 15 phút
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div class="mt-auto">
                            <div class="combo-pricing">
                                <div class="combo-price-details">
                                    <div class="combo-price-regular">250.000 VNĐ</div>
                                    <div class="combo-price-discounted">200.000 VNĐ</div>
                                </div>
                                <div class="combo-savings">-20%</div>
                            </div>
                            <a href="{{ route('appointment.step1') }}" class="combo-btn">
                                Đặt combo <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Combo 2 -->
            <div class="col-lg-4 mb-4">
                <div class="combo-deal h-100">
                    <div class="combo-header">
                        <h3>Combo Chăm sóc</h3>
                    </div>
                    <div class="combo-body d-flex flex-column">
                        <ul class="combo-services flex-grow-1">
                            <li class="combo-service">
                                <div class="combo-service-icon">
                                    <i class="fas fa-cut"></i>
                                </div>
                                <div class="combo-service-details">
                                    <div class="combo-service-name">Cắt tóc nam</div>
                                    <div class="combo-service-duration">
                                        <i class="far fa-clock"></i> 30 phút
                                    </div>
                                </div>
                            </li>
                            <li class="combo-service">
                                <div class="combo-service-icon">
                                    <i class="fas fa-shower"></i>
                                </div>
                                <div class="combo-service-details">
                                    <div class="combo-service-name">Gội đầu</div>
                                    <div class="combo-service-duration">
                                        <i class="far fa-clock"></i> 15 phút
                                    </div>
                                </div>
                            </li>
                            <li class="combo-service">
                                <div class="combo-service-icon">
                                    <i class="fas fa-spa"></i>
                                </div>
                                <div class="combo-service-details">
                                    <div class="combo-service-name">Massage đầu</div>
                                    <div class="combo-service-duration">
                                        <i class="far fa-clock"></i> 15 phút
                                    </div>
                                </div>
                            </li>
                            <!-- Placeholder để giữ chiều cao đồng đều -->
                            <li class="combo-service invisible">
                                <div class="combo-service-icon">
                                    <i class="fas fa-razor"></i>
                                </div>
                                <div class="combo-service-details">
                                    <div class="combo-service-name">Placeholder</div>
                                    <div class="combo-service-duration">
                                        <i class="far fa-clock"></i> 15 phút
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div class="mt-auto">
                            <div class="combo-pricing">
                                <div class="combo-price-details">
                                    <div class="combo-price-regular">350.000 VNĐ</div>
                                    <div class="combo-price-discounted">280.000 VNĐ</div>
                                </div>
                                <div class="combo-savings">-20%</div>
                            </div>
                            <a href="{{ route('appointment.step1') }}" class="combo-btn">
                                Đặt combo <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Combo 3 -->
            <div class="col-lg-4 mb-4">
                <div class="combo-deal h-100">
                    <div class="combo-header">
                        <h3>Combo VIP</h3>
                    </div>
                    <div class="combo-body d-flex flex-column">
                        <ul class="combo-services flex-grow-1">
                            <li class="combo-service">
                                <div class="combo-service-icon">
                                    <i class="fas fa-cut"></i>
                                </div>
                                <div class="combo-service-details">
                                    <div class="combo-service-name">Cắt tóc nam</div>
                                    <div class="combo-service-duration">
                                        <i class="far fa-clock"></i> 30 phút
                                    </div>
                                </div>
                            </li>
                            <li class="combo-service">
                                <div class="combo-service-icon">
                                    <i class="fas fa-shower"></i>
                                </div>
                                <div class="combo-service-details">
                                    <div class="combo-service-name">Gội đầu</div>
                                    <div class="combo-service-duration">
                                        <i class="far fa-clock"></i> 15 phút
                                    </div>
                                </div>
                            </li>
                            <li class="combo-service">
                                <div class="combo-service-icon">
                                    <i class="fas fa-spa"></i>
                                </div>
                                <div class="combo-service-details">
                                    <div class="combo-service-name">Massage đầu</div>
                                    <div class="combo-service-duration">
                                        <i class="far fa-clock"></i> 15 phút
                                    </div>
                                </div>
                            </li>
                            <li class="combo-service">
                                <div class="combo-service-icon">
                                    <i class="fas fa-razor"></i>
                                </div>
                                <div class="combo-service-details">
                                    <div class="combo-service-name">Cạo râu</div>
                                    <div class="combo-service-duration">
                                        <i class="far fa-clock"></i> 15 phút
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div class="mt-auto">
                            <div class="combo-pricing">
                                <div class="combo-price-details">
                                    <div class="combo-price-regular">450.000 VNĐ</div>
                                    <div class="combo-price-discounted">350.000 VNĐ</div>
                                </div>
                                <div class="combo-savings">-22%</div>
                            </div>
                            <a href="{{ route('appointment.step1') }}" class="combo-btn">
                                Đặt combo <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4">Vì sao chọn Barber Shop?</h2>
                <p class="lead">Chúng tôi tự hào cung cấp dịch vụ cắt tóc chất lượng cao với giá cả hợp lý</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="feature-item text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-cut text-primary" style="font-size: 32px;"></i>
                    </div>
                    <h4>Thợ cắt tóc chuyên nghiệp</h4>
                    <p class="text-muted">Đội ngũ thợ cắt tóc có nhiều năm kinh nghiệm và được đào tạo bài bản.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-item text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-gem text-primary" style="font-size: 32px;"></i>
                    </div>
                    <h4>Sản phẩm cao cấp</h4>
                    <p class="text-muted">Sử dụng các sản phẩm chăm sóc tóc chất lượng cao và nhập khẩu chính hãng.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-item text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-smile text-primary" style="font-size: 32px;"></i>
                    </div>
                    <h4>Dịch vụ tận tâm</h4>
                    <p class="text-muted">Cam kết mang đến trải nghiệm thoải mái và hài lòng cho mọi khách hàng.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4">Câu hỏi thường gặp</h2>
                <p class="lead">Những thông tin hữu ích về giá cả và dịch vụ của chúng tôi</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Làm thế nào để đăng ký thành viên?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Để đăng ký thành viên, bạn có thể đến trực tiếp cửa hàng của chúng tôi hoặc đăng ký trực tuyến trên trang web. Sau khi hoàn tất thanh toán, bạn sẽ nhận được thẻ thành viên và có thể bắt đầu tận hưởng các ưu đãi ngay lập tức.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>Thành viên có những ưu đãi gì?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Thành viên của Barber Shop sẽ nhận được nhiều ưu đãi hấp dẫn như: giảm giá từ 10-20% cho tất cả dịch vụ, ưu tiên đặt lịch, dịch vụ miễn phí tùy theo gói thành viên, tích điểm đổi quà, và các ưu đãi đặc biệt vào dịp sinh nhật.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>Có thể thanh toán bằng những phương thức nào?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Chúng tôi chấp nhận nhiều phương thức thanh toán khác nhau bao gồm: tiền mặt, thẻ tín dụng/ghi nợ, và chuyển khoản ngân hàng. Bạn có thể chọn phương thức thanh toán thuận tiện nhất khi đặt lịch hoặc tại cửa hàng.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>Làm thế nào để đặt lịch dịch vụ?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Bạn có thể đặt lịch dịch vụ thông qua trang web của chúng tôi, gọi điện trực tiếp đến số hotline, hoặc đến trực tiếp cửa hàng. Chúng tôi khuyến khích đặt lịch trước để đảm bảo bạn được phục vụ vào thời gian mong muốn.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>Có thể hủy hoặc đổi lịch đã đặt không?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Có, bạn có thể hủy hoặc đổi lịch đã đặt ít nhất 2 giờ trước thời gian hẹn. Việc hủy lịch có thể được thực hiện thông qua trang web hoặc gọi điện trực tiếp cho chúng tôi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Special Offers Section -->
<section class="py-5 bg-light special-offers-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="mb-4">Ưu đãi đặc biệt</h2>
                <p class="lead mb-4">Đăng ký thành viên để nhận nhiều ưu đãi hấp dẫn</p>

                <div class="special-offer-list">
                    <div class="special-offer-item">
                        <div class="special-offer-icon">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="special-offer-text">Giảm 10% cho lần đầu sử dụng dịch vụ</div>
                    </div>

                    <div class="special-offer-item">
                        <div class="special-offer-icon">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div class="special-offer-text">Tích điểm đổi quà với mỗi dịch vụ sử dụng</div>
                    </div>

                    <div class="special-offer-item">
                        <div class="special-offer-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div class="special-offer-text">Các chương trình khuyến mãi hàng tháng</div>
                    </div>

                    <div class="special-offer-item">
                        <div class="special-offer-icon">
                            <i class="fas fa-birthday-cake"></i>
                        </div>
                        <div class="special-offer-text">Ưu đãi đặc biệt vào dịp sinh nhật</div>
                    </div>
                </div>

                <a href="{{ route('appointment.step1') }}" class="btn btn-primary mt-3 appointment-btn price-item-btn">
                    Đặt lịch ngay <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="col-lg-6">
                <div class="ratio ratio-16x9 rounded overflow-hidden shadow">
                    <img src="{{ asset('images/barber-special.jpg') }}" alt="Ưu đãi đặc biệt" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 text-white text-center cta-appointment">
    <div class="container">
        <h2 class="h1 mb-4">Sẵn sàng trải nghiệm dịch vụ của chúng tôi?</h2>
        <p class="lead mb-4">Đặt lịch ngay hôm nay và trải nghiệm dịch vụ cắt tóc chuyên nghiệp tại Barber Shop.</p>
        <a href="{{ route('appointment.step1') }}" class="btn btn-light btn-lg appointment-btn">Đặt lịch ngay</a>
    </div>
</section>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Tối ưu animation cho price items
        const priceItems = document.querySelectorAll('.price-item');
        priceItems.forEach((item, index) => {
            item.style.animationDelay = (index * 0.05) + 's';
        });

        // Xử lý bộ lọc danh mục
        const filterButtons = document.querySelectorAll('.price-filter-btn');
        const categoryGroups = document.querySelectorAll('.category-group');

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Xóa class active từ tất cả các nút
                filterButtons.forEach(btn => btn.classList.remove('active'));

                // Thêm class active cho nút được click
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');

                // Hiển thị/ẩn các nhóm danh mục dựa trên bộ lọc
                if (filter === 'all') {
                    categoryGroups.forEach(group => {
                        group.style.display = 'block';

                        // Thêm animation cho các mục khi hiển thị lại
                        const items = group.querySelectorAll('.price-item');
                        items.forEach((item, index) => {
                            item.style.opacity = '0';
                            item.style.animationDelay = (index * 0.05) + 's';
                            setTimeout(() => {
                                item.style.opacity = '1';
                            }, 10);
                        });
                    });
                } else {
                    categoryGroups.forEach(group => {
                        if (group.getAttribute('data-category') === filter) {
                            group.style.display = 'block';

                            // Thêm animation cho các mục khi hiển thị lại
                            const items = group.querySelectorAll('.price-item');
                            items.forEach((item, index) => {
                                item.style.opacity = '0';
                                item.style.animationDelay = (index * 0.05) + 's';
                                setTimeout(() => {
                                    item.style.opacity = '1';
                                }, 10);
                            });
                        } else {
                            group.style.display = 'none';
                        }
                    });
                }

                // Không cuộn trang khi lọc dịch vụ
                // Đã loại bỏ đoạn code cuộn trang
            });
        });

        // Xử lý FAQ
        const faqQuestions = document.querySelectorAll('.faq-question');

        faqQuestions.forEach(question => {
            question.addEventListener('click', function() {
                const answer = this.nextElementSibling;
                const isActive = this.classList.contains('active');

                // Đóng tất cả các câu trả lời
                document.querySelectorAll('.faq-answer').forEach(item => {
                    item.classList.remove('active');
                });

                document.querySelectorAll('.faq-question').forEach(item => {
                    item.classList.remove('active');
                });

                // Mở câu trả lời hiện tại nếu chưa mở
                if (!isActive) {
                    answer.classList.add('active');
                    this.classList.add('active');
                }
            });
        });
    });
</script>
@endsection