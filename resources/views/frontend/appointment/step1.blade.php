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
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('appointment.post.step1') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                @forelse($services as $service)
                                    <div class="col-md-6 mb-3">
                                        <div class="card service-card h-100 {{ in_array($service->id, old('services', [])) ? 'border-primary' : '' }}">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input service-checkbox" type="checkbox" name="services[]" value="{{ $service->id }}" id="service-{{ $service->id }}" {{ in_array($service->id, old('services', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label d-block" for="service-{{ $service->id }}">
                                                        <h6 class="mb-1">{{ $service->name }}</h6>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="text-primary font-weight-bold">{{ number_format($service->price) }} VNĐ</span>
                                                            <span class="text-muted small">{{ $service->duration }} phút</span>
                                                        </div>
                                                        <p class="text-muted small mt-2 mb-0">{{ $service->description }}</p>
                                                    </label>
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
                            
                            <div class="mt-4 text-center">
                                <button type="submit" class="btn btn-primary btn-lg">Tiếp tục <i class="fas fa-arrow-right"></i></button>
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
        background-color: #0d6efd;
        color: white;
    }
    
    .step-text {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .step.active .step-text {
        color: #0d6efd;
        font-weight: bold;
    }
    
    .service-card {
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .service-card:hover {
        border-color: #0d6efd;
    }
    
    .service-card.selected {
        border-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.05);
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Xử lý chọn dịch vụ
        $('.service-card').on('click', function() {
            var checkbox = $(this).find('.service-checkbox');
            checkbox.prop('checked', !checkbox.prop('checked'));
            $(this).toggleClass('selected');
        });
        
        // Đảm bảo khi click vào checkbox không làm ảnh hưởng đến sự kiện click của card
        $('.service-checkbox').on('click', function(e) {
            e.stopPropagation();
            $(this).closest('.service-card').toggleClass('selected');
        });
        
        // Khởi tạo class selected cho các dịch vụ đã chọn
        $('.service-checkbox:checked').closest('.service-card').addClass('selected');
    });
</script>
@endsection 