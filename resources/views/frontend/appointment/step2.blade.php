@extends('layouts.frontend')

@section('title', 'Đặt lịch - Bước 2: Chọn thợ cắt tóc')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Đặt lịch hẹn</h4>
                    </div>
                    <div class="card-body">
                        <!-- Thanh tiến trình đặt lịch -->
                        <div class="progress-steps mb-5">
                            <div class="d-flex justify-content-between">
                                <div class="step completed">
                                    <div class="step-circle">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text">Chọn dịch vụ</div>
                                </div>
                                <div class="step active">
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

                        <!-- Hiển thị dịch vụ đã chọn -->
                        <div class="selected-services mb-4">
                            <h6>Dịch vụ đã chọn:</h6>
                            <div class="row">
                                @php $totalPrice = 0; $totalDuration = 0; @endphp
                                @foreach(session('appointment_services', []) as $service)
                                    <div class="col-md-6 mb-2">
                                        <div class="card bg-light">
                                            <div class="card-body py-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>{{ $service->name }}</span>
                                                    <span class="text-primary">{{ number_format($service->price) }} VNĐ</span>
                                                </div>
                                                <small class="text-muted">{{ $service->duration }} phút</small>
                                            </div>
                                        </div>
                                    </div>
                                    @php 
                                        $totalPrice += $service->price; 
                                        $totalDuration += $service->duration;
                                    @endphp
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span>Tổng cộng:</span>
                                <div class="text-end">
                                    <div>
                                        <strong class="text-primary">{{ number_format($totalPrice) }} VNĐ</strong>
                                    </div>
                                    <small class="text-muted">Thời gian dự kiến: {{ $totalDuration }} phút</small>
                                </div>
                            </div>
                        </div>

                        <!-- Form chọn thợ cắt tóc -->
                        <h5 class="card-title mb-4">Bước 2: Chọn thợ cắt tóc</h5>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('appointment.post.step2') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                @forelse($barbers as $barber)
                                    <div class="col-md-6 mb-3">
                                        <div class="card barber-card h-100 {{ old('barber_id') == $barber->id ? 'border-primary' : '' }}">
                                            <div class="card-body text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input barber-radio visually-hidden" type="radio" name="barber_id" value="{{ $barber->id }}" id="barber-{{ $barber->id }}" {{ old('barber_id') == $barber->id ? 'checked' : '' }}>
                                                    <label class="form-check-label d-block" for="barber-{{ $barber->id }}">
                                                        <div class="mb-3">
                                                            @if($barber->user->avatar)
                                                                <img src="{{ asset('storage/' . $barber->user->avatar) }}" alt="{{ $barber->user->name }}" class="rounded-circle barber-avatar" width="80" height="80">
                                                            @else
                                                                <img src="{{ asset('images/default-avatar.jpg') }}" alt="{{ $barber->user->name }}" class="rounded-circle barber-avatar" width="80" height="80">
                                                            @endif
                                                        </div>
                                                        <h5 class="mb-1">{{ $barber->user->name }}</h5>
                                                        <div class="text-muted">
                                                            <p class="mb-1"><i class="fas fa-star text-warning me-1"></i> Thợ cắt tóc {{ $barber->experience }} năm kinh nghiệm</p>
                                                            <p class="small mb-1">{{ $barber->specialty }}</p>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            Không có thợ cắt tóc nào hiện có. Vui lòng quay lại sau.
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="{{ route('appointment.step1') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary">Tiếp tục <i class="fas fa-arrow-right"></i></button>
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
    
    .step.completed .step-circle {
        background-color: #28a745;
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
    
    .step.completed .step-text {
        color: #28a745;
    }
    
    .barber-card {
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .barber-card:hover {
        border-color: #0d6efd;
    }
    
    .barber-card.selected {
        border-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .barber-avatar {
        object-fit: cover;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Xử lý chọn thợ cắt tóc
        $('.barber-card').on('click', function() {
            $('.barber-card').removeClass('selected');
            $(this).addClass('selected');
            $(this).find('.barber-radio').prop('checked', true);
        });
        
        // Khởi tạo class selected cho thợ cắt tóc đã chọn
        $('.barber-radio:checked').closest('.barber-card').addClass('selected');
    });
</script>
@endsection 