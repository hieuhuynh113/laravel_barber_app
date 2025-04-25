@extends('layouts.frontend')

@section('title', 'Đặt lịch - Bước 1: Chọn dịch vụ')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header text-white" style="background-color: #9E8A78;">
                        <h4 class="mb-0">Đặt lịch hẹn</h4>
                    </div>
                    <div class="card-body">
                        <!-- Thanh tiến trình đặt lịch -->
                        <div class="progress-steps mb-5">
                            <div class="d-flex justify-content-between">
                                <div class="step active">
                                    <div class="step-circle">1</div>
                                    <div class="step-text">Chọn dịch vụ</div>
                                </div>
                                <div class="step">
                                    <div class="step-circle">2</div>
                                    <div class="step-text">Chọn thợ cắt tóc</div>
                                </div>
                                <div class="step">
                                    <div class="step-circle">3</div>
                                    <div class="step-text">Chọn thời gian</div>
                                </div>
                                <div class="step">
                                    <div class="step-circle">4</div>
                                    <div class="step-text">Thông tin cá nhân</div>
                                </div>
                                <div class="step">
                                    <div class="step-circle">5</div>
                                    <div class="step-text">Thanh toán</div>
                                </div>
                                <div class="step">
                                    <div class="step-circle">6</div>
                                    <div class="step-text">Xác nhận</div>
                                </div>
                            </div>
                        </div>

                        <!-- Form chọn dịch vụ -->
                        <h5 class="card-title mb-4">Bước 1: Chọn dịch vụ</h5>



                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-circle me-2 fs-4"></i>
                                    <div>
                                        @if($errors->has('services'))
                                            <strong>{{ $errors->first('services') }}</strong>
                                        @else
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('appointment.post.step1') }}" method="POST">
                            @csrf

                            <!-- Phân loại dịch vụ theo danh mục -->
                            @php
                                $servicesByCategory = $services->groupBy('category.name');
                            @endphp

                            <div class="service-categories mb-4">
                                <div class="d-flex flex-wrap justify-content-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary m-1 category-filter active" data-category="all">
                                        <i class="fas fa-th-large me-1"></i> Tất cả
                                    </button>
                                    @foreach($servicesByCategory as $categoryName => $categoryServices)
                                        <button type="button" class="btn btn-sm btn-outline-primary m-1 category-filter" data-category="{{ Str::slug($categoryName) }}">
                                            <i class="{{ getCategoryIcon($categoryName) }} me-1"></i> {{ $categoryName }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="row">
                                @forelse($services as $service)
                                    <div class="col-md-6 mb-4 service-item" data-category="{{ Str::slug($service->category->name) }}">
                                        <div class="card service-card h-100 {{ in_array($service->id, old('services', [])) ? 'selected' : '' }}">
                                            <div class="service-selected-status"><i class="fas fa-check-circle me-1"></i> Đã chọn</div>
                                            @if($service->image)
                                                <div class="service-image-container">
                                                    <img src="{{ asset('storage/' . $service->image) }}" class="card-img-top service-image" alt="{{ $service->name }}">
                                                    <div class="service-duration">
                                                        <i class="far fa-clock"></i> {{ $service->duration }} phút
                                                    </div>
                                                </div>
                                            @else
                                                <div class="service-no-image d-flex align-items-center justify-content-center">
                                                    <i class="{{ getCategoryIcon($service->category->name) }} service-icon"></i>
                                                </div>
                                            @endif
                                            <div class="card-body d-flex flex-column">
                                                <input class="service-checkbox" type="checkbox" name="services[]" value="{{ $service->id }}" id="service-{{ $service->id }}" {{ in_array($service->id, old('services', [])) || (isset($selectedServiceId) && $selectedServiceId == $service->id) ? 'checked' : '' }} hidden>
                                                <h5 class="card-title mb-2">{{ $service->name }}</h5>
                                                <div class="service-category mb-2">
                                                    <span class="badge bg-light text-dark">
                                                        <i class="{{ getCategoryIcon($service->category->name) }} me-1"></i> {{ $service->category->name }}
                                                    </span>
                                                </div>
                                                <p class="card-text flex-grow-1 service-description">{{ Str::limit($service->description, 100) }}</p>
                                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                                    <span class="service-price">
                                                        {{ number_format($service->price) }} VNĐ
                                                    </span>
                                                    <button type="button" class="btn btn-sm btn-link p-0 service-details" data-bs-toggle="modal" data-bs-target="#serviceModal-{{ $service->id }}">
                                                        Chi tiết <i class="fas fa-chevron-right ms-1"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal chi tiết dịch vụ -->
                                        <div class="modal fade" id="serviceModal-{{ $service->id }}" tabindex="-1" aria-labelledby="serviceModalLabel-{{ $service->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color: #9E8A78; color: white;">
                                                        <h5 class="modal-title" id="serviceModalLabel-{{ $service->id }}">
                                                            <i class="{{ getCategoryIcon($service->category->name) }} me-2"></i>{{ $service->name }}
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="service-modal-image mb-3">
                                                            @if($service->image)
                                                                <img src="{{ asset('storage/' . $service->image) }}" class="img-fluid rounded w-100" alt="{{ $service->name }}" style="max-height: 250px; object-fit: cover;">
                                                            @else
                                                                <div class="service-no-image-modal d-flex align-items-center justify-content-center rounded">
                                                                    <i class="{{ getCategoryIcon($service->category->name) }} service-icon-modal"></i>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="service-modal-info p-3 bg-light rounded mb-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                                <span class="badge bg-primary">
                                                                    <i class="{{ getCategoryIcon($service->category->name) }} me-1"></i> {{ $service->category->name }}
                                                                </span>
                                                                <span class="badge bg-secondary"><i class="far fa-clock me-1"></i> {{ $service->duration }} phút</span>
                                                            </div>
                                                            <h4 class="text-primary mb-2 service-modal-price">
                                                                {{ number_format($service->price) }} VNĐ
                                                            </h4>
                                                        </div>

                                                        <div class="service-modal-description">
                                                            <h6 class="text-dark mb-2">Mô tả dịch vụ:</h6>
                                                            <p>{{ $service->description }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                            <i class="fas fa-times me-1"></i> Đóng
                                                        </button>
                                                        <button type="button" class="btn btn-primary select-service-btn" data-service-id="{{ $service->id }}">
                                                            <i class="fas fa-check me-1"></i> Chọn dịch vụ này
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            Không có dịch vụ nào. Vui lòng quay lại sau.
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            <div class="mt-5 text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5 py-3">
                                    Tiếp tục <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    .progress-steps {
        position: relative;
    }

    .progress-steps:before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #e9ecef;
        z-index: 0;
    }

    .step {
        text-align: center;
        z-index: 1;
        flex: 1;
        position: relative;
    }

    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-weight: bold;
    }

    .step.active .step-circle {
        background-color: #9E8A78;
        color: white;
    }

    .step-text {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .step.active .step-text {
        color: #9E8A78;
        font-weight: bold;
    }

    /* Cải thiện giao diện card dịch vụ */
    .service-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid transparent;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    }

    .service-card.selected {
        border-color: #9E8A78;
        background-color: rgba(158, 138, 120, 0.05);
        box-shadow: 0 0 0 1px #9E8A78;
    }

    /* Badge đã được loại bỏ */

    .service-selected-status {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        background-color: #9E8A78;
        color: white;
        padding: 8px 15px;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        z-index: 90;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-100%);
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
    }

    .service-card.selected .service-selected-status {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    @keyframes scale-in {
        0% {
            transform: scale(0);
            opacity: 0;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    @keyframes fade-in {
        0% {
            opacity: 0;
        }
        50% {
            opacity: 0.5;
        }
        100% {
            opacity: 1;
        }
    }

    .service-image-container {
        position: relative;
        height: 160px;
        overflow: hidden;
    }

    .service-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .service-card:hover .service-image {
        transform: scale(1.05);
    }

    .service-duration {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background-color: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
    }

    .service-no-image {
        height: 160px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        position: relative;
        overflow: hidden;
    }

    .service-no-image:before {
        content: '';
        position: absolute;
        top: -30px;
        left: -30px;
        right: -30px;
        bottom: -30px;
        background: radial-gradient(circle at center, rgba(158, 138, 120, 0.1) 0%, rgba(158, 138, 120, 0) 70%);
        z-index: 0;
    }

    .service-icon {
        font-size: 3rem;
        color: #9E8A78;
        position: relative;
        z-index: 1;
        transition: all 0.3s ease;
    }

    .service-card:hover .service-icon {
        transform: scale(1.1);
        color: #7d6c5d;
    }

    .service-title {
        font-weight: 600;
        color: #343a40;
    }

    .service-category {
        margin-bottom: 10px;
    }

    .service-description {
        font-size: 0.9rem;
        color: #6c757d;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .service-price {
        font-weight: 700;
        font-size: 1.1rem;
        color: #9E8A78;
    }

    .service-details {
        color: #9E8A78;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .service-select-text {
        font-size: 0.85rem;
        color: #6c757d;
    }

    /* Cải thiện checkbox */
    .service-selection-container {
        position: relative;
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .custom-control {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        min-height: 1.5rem;
        padding-left: 0;
    }

    .custom-control-input {
        position: absolute;
        left: 0;
        z-index: -1;
        width: 1.5rem;
        height: 1.5rem;
        opacity: 0;
    }

    .custom-control-label {
        position: relative;
        margin-bottom: 0;
        vertical-align: top;
        cursor: pointer;
        padding-left: 2rem;
    }

    .custom-control-label::before {
        position: absolute;
        top: 0.125rem;
        left: 0;
        display: block;
        width: 1.5rem;
        height: 1.5rem;
        content: "";
        background-color: #fff;
        border: 2px solid #9E8A78;
        border-radius: 0.25rem;
        transition: all 0.15s ease-in-out;
    }

    .custom-control-input:checked ~ .custom-control-label::before {
        color: #fff;
        border-color: #9E8A78;
        background-color: #9E8A78;
    }

    .custom-control-input:focus ~ .custom-control-label::before {
        box-shadow: 0 0 0 0.2rem rgba(158, 138, 120, 0.25);
    }

    .custom-control-label::after {
        position: absolute;
        top: 0.125rem;
        left: 0;
        display: block;
        width: 1.5rem;
        height: 1.5rem;
        content: "";
        background: no-repeat 50% / 50% 50%;
    }

    .custom-control-input:checked ~ .custom-control-label::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23fff' d='M6.564.75l-3.59 3.612-1.538-1.55L0 4.26 2.974 7.25 8 2.193z'/%3e%3c/svg%3e");
    }

    /* Cải thiện bộ lọc danh mục */
    .service-categories {
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .category-filter {
        border-color: #9E8A78;
        color: #9E8A78;
        margin: 0 5px;
        border-radius: 20px;
        font-size: 0.85rem;
        padding: 5px 15px;
    }

    .category-filter:hover, .category-filter.active {
        background-color: #9E8A78;
        color: white;
    }

    /* CSS cho modal dịch vụ */
    .service-no-image-modal {
        height: 200px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        position: relative;
        overflow: hidden;
        border-radius: 8px;
    }

    .service-icon-modal {
        font-size: 4rem;
        color: #9E8A78;
    }

    .service-modal-price {
        color: #9E8A78;
        font-weight: 700;
    }

    .service-modal-info {
        border-left: 4px solid #9E8A78;
    }

    .service-modal-description h6 {
        position: relative;
        padding-left: 15px;
    }

    .service-modal-description h6:before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 8px;
        height: 8px;
        background-color: #9E8A78;
        border-radius: 50%;
    }

    /* Tối ưu hiệu suất modal */
    .modal {
        will-change: transform;
    }

    .modal-content {
        will-change: transform;
        backface-visibility: hidden;
        transform: translateZ(0);
    }

    .modal-backdrop {
        will-change: opacity;
    }

    /* Hiệu ứng nhấp nháy cho dịch vụ được chọn từ URL */
    @keyframes pulse-highlight {
        0% {
            box-shadow: 0 0 0 0 rgba(158, 138, 120, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(158, 138, 120, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(158, 138, 120, 0);
        }
    }

    .pulse-animation {
        animation: pulse-highlight 1s ease-in-out 3;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Tải trước các modal để tránh lag
        $('.modal').each(function() {
            $(this).appendTo('body');
        });

        // Xử lý chọn dịch vụ khi click vào card
        $('.service-card').on('click', function(e) {
            // Không xử lý khi click vào nút chi tiết hoặc checkbox
            if ($(e.target).hasClass('service-details') ||
                $(e.target).hasClass('fa-chevron-right') ||
                $(e.target).hasClass('service-checkbox') ||
                $(e.target).hasClass('form-check-label') ||
                $(e.target).hasClass('service-select-text') ||
                $(e.target).hasClass('custom-control-label')) {
                return;
            }

            var checkbox = $(this).find('.service-checkbox');
            var isChecked = !checkbox.prop('checked');
            checkbox.prop('checked', isChecked);
            $(this).toggleClass('selected', isChecked);

            // Cập nhật trạng thái đã chọn
            if (isChecked) {
                $(this).find('.service-selected-status').css({
                    'opacity': '1',
                    'visibility': 'visible',
                    'transform': 'translateY(0)'
                });
            } else {
                $(this).find('.service-selected-status').css({
                    'opacity': '0',
                    'visibility': 'hidden',
                    'transform': 'translateY(-100%)'
                });
            }
        });

        // Đảm bảo khi click vào checkbox không làm ảnh hưởng đến sự kiện click của card
        $('.service-checkbox, .custom-control-label').on('click', function(e) {
            e.stopPropagation();
            var card = $(this).closest('.service-card');
            var checkbox = card.find('.service-checkbox');

            // Toggle checkbox state
            if ($(this).hasClass('custom-control-label')) {
                setTimeout(function() {
                    card.toggleClass('selected', checkbox.prop('checked'));
                    // Cập nhật trạng thái đã chọn
                    if (checkbox.prop('checked')) {
                        card.find('.service-selected-status').css({
                            'opacity': '1',
                            'visibility': 'visible',
                            'transform': 'translateY(0)'
                        });
                    } else {
                        card.find('.service-selected-status').css({
                            'opacity': '0',
                            'visibility': 'hidden',
                            'transform': 'translateY(-100%)'
                        });
                    }
                }, 10);
            } else {
                card.toggleClass('selected', $(this).prop('checked'));
                // Cập nhật trạng thái đã chọn
                if ($(this).prop('checked')) {
                    card.find('.service-selected-status').css({
                        'opacity': '1',
                        'visibility': 'visible',
                        'transform': 'translateY(0)'
                    });
                } else {
                    card.find('.service-selected-status').css({
                        'opacity': '0',
                        'visibility': 'hidden',
                        'transform': 'translateY(-100%)'
                    });
                }
            }
        });

        // Tối ưu hiệu suất khi mở modal
        $('.service-details').on('click', function(e) {
            e.preventDefault();
            var modalId = $(this).data('bs-target');
            $(modalId).modal({
                backdrop: true,
                keyboard: true,
                focus: true
            });
        });

        // Xử lý khi click vào nút "Chọn dịch vụ này" trong modal
        $('.select-service-btn').on('click', function() {
            var serviceId = $(this).data('service-id');
            var checkbox = $('#service-' + serviceId);
            var card = checkbox.closest('.service-card');

            checkbox.prop('checked', true);
            card.addClass('selected');

            // Hiển thị trạng thái đã chọn
            card.find('.service-selected-status').css({
                'opacity': '1',
                'visibility': 'visible',
                'transform': 'translateY(0)'
            });

            // Đóng modal
            $('#serviceModal-' + serviceId).modal('hide');
        });

        // Khởi tạo class selected cho các dịch vụ đã chọn
        $('.service-checkbox:checked').each(function() {
            var card = $(this).closest('.service-card');
            card.addClass('selected');

            // Hiển thị trạng thái đã chọn
            card.find('.service-selected-status').css({
                'opacity': '1',
                'visibility': 'visible',
                'transform': 'translateY(0)'
            });

            // Nếu có service_id trong URL, cuộn đến dịch vụ đó
            @if(isset($selectedServiceId))
            if ($(this).val() == {{ $selectedServiceId }}) {
                // Cuộn đến dịch vụ đã chọn sau khi trang tải xong
                setTimeout(function() {
                    $('html, body').animate({
                        scrollTop: card.offset().top - 100
                    }, 500);

                    // Thêm hiệu ứng nhấp nháy để thu hút sự chú ý
                    card.addClass('pulse-animation');
                    setTimeout(function() {
                        card.removeClass('pulse-animation');
                    }, 1500);
                }, 500);

                // Nếu dịch vụ thuộc danh mục khác, chuyển sang danh mục đó
                var category = card.closest('.service-item').data('category');
                if (category) {
                    $('.category-filter[data-category="' + category + '"]').click();
                }
            }
            @endif
        });

        // Xử lý bộ lọc danh mục
        $('.category-filter').on('click', function() {
            var category = $(this).data('category');

            // Đánh dấu nút được chọn
            $('.category-filter').removeClass('active');
            $(this).addClass('active');

            // Hiển thị/ẩn dịch vụ theo danh mục
            if (category === 'all') {
                $('.service-item').show();
            } else {
                $('.service-item').hide();
                $('.service-item[data-category="' + category + '"]').show();
            }
        });
    });
</script>
@endsection