@extends('layouts.frontend')

@section('title', 'Dịch vụ')

@section('content')
@include('partials.page-header', [
    'title' => 'Dịch vụ của chúng tôi',
    'description' => 'Khám phá các dịch vụ chất lượng cao và chuyên nghiệp tại Barber Shop',
    'backgroundImage' => 'images/hero-bg-2.jpg'
])

<section class="py-5 bg-light">
    <div class="container">
        <!-- New Filter UI based on reference image -->
        <div class="filter-header mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div class="filter-toggle d-flex align-items-center">
                    <i class="fas fa-filter me-2"></i>
                    <span>Filters:</span>
                </div>
                <div class="filter-count">
                    Hiển thị {{ $services->count() }} / {{ $services->total() }} dịch vụ
                </div>
            </div>
        </div>

        <div class="filter-options mb-4">
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="filter-group">
                        <label for="categoryFilter">Danh mục:</label>
                        <select class="form-select" id="categoryFilter">
                            <option value="" {{ !$categoryId ? 'selected' : '' }}>Tất cả danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="filter-group">
                        <label for="levelFilter">Mức độ:</label>
                        <select class="form-select" id="levelFilter">
                            <option value="">Tất cả mức độ</option>
                            <option value="basic">Cơ bản</option>
                            <option value="intermediate">Trung bình</option>
                            <option value="advanced">Nâng cao</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs mb-4">
            <div class="filter-tab {{ !$sort ? 'active' : '' }}" data-sort="">Tất cả dịch vụ</div>
            <div class="filter-tab {{ $sort == 'popular' ? 'active' : '' }}" data-sort="popular">Phổ biến nhất</div>
            <div class="filter-tab {{ $sort == 'newest' ? 'active' : '' }}" data-sort="newest">Mới nhất</div>
            <div class="filter-tab {{ $sort == 'recommended' ? 'active' : '' }}" data-sort="recommended">Đề xuất</div>
        </div>

        <div class="row" id="services-container">
            @if($services->count() > 0)
                @foreach($services as $service)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 service-card">
                        <div class="card-img-container position-relative">
                            <img src="{{ asset('storage/' . $service->image) }}" class="card-img-top" alt="{{ $service->name }}">
                            <div class="service-duration position-absolute">
                                <span>{{ $service->duration }} phút</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="service-category mb-2">
                                <span class="badge bg-light text-dark">{{ $service->category->name }}</span>
                            </div>
                            <h5 class="card-title">{{ $service->name }}</h5>
                            <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                        </div>
                        <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center">
                            <span class="price text-primary fw-bold">{{ number_format($service->price) }} VNĐ</span>
                            <a href="{{ route('services.show', $service->slug) }}" class="btn btn-sm btn-outline-primary">Chi tiết <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="alert alert-info text-center p-5">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <h4>Không tìm thấy dịch vụ nào</h4>
                        <p>Hiện tại không có dịch vụ nào trong danh mục này. Vui lòng chọn danh mục khác.</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-4 pagination-container">
            {{ $services->appends(request()->query())->links() }}
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2>Tại sao chọn dịch vụ của chúng tôi?</h2>
                <p>Barber Shop tự hào cung cấp các dịch vụ cắt tóc và chăm sóc tóc chất lượng cao. Chúng tôi cam kết mang đến trải nghiệm tuyệt vời nhất cho quý khách hàng.</p>

                <div class="mt-4">
                    <div class="d-flex mb-3">
                        <div class="feature-icon me-3">
                            <i class="fas fa-user-tie text-primary" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <h5>Thợ cắt tóc chuyên nghiệp</h5>
                            <p class="text-muted">Đội ngũ thợ cắt tóc của chúng tôi đều được đào tạo bài bản và có nhiều năm kinh nghiệm.</p>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <div class="feature-icon me-3">
                            <i class="fas fa-cut text-primary" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <h5>Dụng cụ hiện đại</h5>
                            <p class="text-muted">Sử dụng các dụng cụ và sản phẩm chất lượng cao để đảm bảo kết quả tốt nhất.</p>
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="feature-icon me-3">
                            <i class="fas fa-gem text-primary" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <h5>Không gian sang trọng</h5>
                            <p class="text-muted">Môi trường thoải mái, sang trọng giúp quý khách có trải nghiệm thư giãn nhất.</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('appointment.step1') }}" class="btn btn-primary mt-3 appointment-btn">Đặt lịch ngay</a>
            </div>

            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="{{ asset('images/services-main.jpg') }}" alt="Dịch vụ cắt tóc" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4">Câu hỏi thường gặp</h2>
                <p class="mb-5">Một số câu hỏi khách hàng thường hỏi về dịch vụ của chúng tôi</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Tôi có cần đặt lịch trước không?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Chúng tôi khuyến khích quý khách đặt lịch trước để đảm bảo được phục vụ đúng giờ và không phải chờ đợi. Tuy nhiên, chúng tôi vẫn phục vụ khách hàng đến trực tiếp nếu có chỗ trống.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Các phương thức thanh toán được chấp nhận?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Chúng tôi chấp nhận thanh toán bằng tiền mặt, thẻ tín dụng/ghi nợ và các ví điện tử phổ biến như MoMo, ZaloPay, VNPay.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Tôi có thể hủy hoặc đổi lịch hẹn không?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Quý khách có thể hủy hoặc đổi lịch hẹn trước ít nhất 2 giờ. Việc hủy lịch sau thời gian này có thể phát sinh phí.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                Có cần tư vấn trước khi sử dụng dịch vụ không?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Chúng tôi khuyên quý khách nên tham khảo ý kiến của thợ cắt tóc trước khi quyết định kiểu tóc. Các thợ cắt tóc của chúng tôi sẽ tư vấn kiểu tóc phù hợp nhất với khuôn mặt và phong cách của quý khách.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <p class="mb-3">Còn câu hỏi khác? Liên hệ với chúng tôi</p>
            <a href="{{ route('contact.index') }}" class="btn btn-primary">Liên hệ ngay</a>
        </div>
    </div>
</section>

<section class="py-5 text-white text-center cta-appointment">
    <div class="container">
        <h2 class="h1 mb-4">Sẵn sàng trải nghiệm dịch vụ?</h2>
        <p class="lead mb-4">Đặt lịch ngay hôm nay và trải nghiệm dịch vụ cắt tóc chuyên nghiệp tại Barber Shop.</p>
        <a href="{{ route('appointment.step1') }}" class="btn btn-light btn-lg appointment-btn">Đặt lịch ngay</a>
    </div>
</section>
@endsection