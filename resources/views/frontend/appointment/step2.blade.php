@extends('layouts.frontend')

@section('title', 'Đặt lịch - Bước 2: Chọn thợ cắt tóc')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header text-white" style="background-color: #9E8A78;">
                        <h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Đặt lịch hẹn</h4>
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
                        <div class="selected-info mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary text-white py-3" style="background-color: #9E8A78 !important;">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin đã chọn</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="service-summary">
                                        <h6 class="text-primary border-bottom pb-2 mb-3" style="color: #9E8A78 !important;"><i class="fas fa-cut me-2"></i>Dịch vụ</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless mb-0">
                                                <tbody>
                                                    @php $totalPrice = 0; $totalDuration = 0; @endphp
                                                    @foreach(session('appointment_services', []) as $service)
                                                        <tr>
                                                            <td>{{ $service->name }}</td>
                                                            <td class="text-end fw-medium">{{ number_format($service->price) }} VNĐ</td>
                                                        </tr>
                                                        @php
                                                            $totalPrice += $service->price;
                                                            $totalDuration += $service->duration;
                                                        @endphp
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Tổng cộng</th>
                                                        <th class="text-end text-primary" style="color: #9E8A78 !important;">{{ number_format($totalPrice) }} VNĐ</th>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="text-muted pt-0">
                                                            <small><i class="far fa-clock me-1"></i>Thời gian dự kiến: {{ $totalDuration }} phút</small>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form chọn thợ cắt tóc -->
                        <h5 class="card-title mb-4"><span class="badge me-2" style="background-color: #9E8A78;">Bước 2</span> Chọn thợ cắt tóc</h5>
                        <p class="text-muted mb-4">Hãy chọn thợ cắt tóc phù hợp với nhu cầu của bạn. Mỗi thợ cắt tóc của chúng tôi đều có chuyên môn và kỹ năng riêng.</p>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-circle me-2 fs-4"></i>
                                    <div>
                                        @if($errors->has('barber_id'))
                                            <strong>{{ $errors->first('barber_id') }}</strong>
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

                        <form action="{{ route('appointment.post.step2') }}" method="POST">
                            @csrf

                            <div class="row">
                                @forelse($barbers as $barber)
                                    <div class="col-md-6 mb-3">
                                        <div class="card barber-card h-100 {{ old('barber_id') == $barber->id ? 'selected' : '' }}">
                                            <div class="barber-selected-status"><i class="fas fa-check-circle me-1"></i> Đã chọn</div>
                                            <div class="card-body text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input barber-radio visually-hidden" type="radio" name="barber_id" value="{{ $barber->id }}" id="barber-{{ $barber->id }}" {{ old('barber_id') == $barber->id ? 'checked' : '' }}>
                                                    <label class="form-check-label d-block" for="barber-{{ $barber->id }}">
                                                        <div class="barber-avatar-container mb-3">
                                                            @if($barber->user->avatar)
                                                                <img src="{{ asset('storage/' . $barber->user->avatar) }}" alt="{{ $barber->user->name }}" class="barber-avatar">
                                                            @else
                                                                <img src="{{ asset('images/default-avatar.jpg') }}" alt="{{ $barber->user->name }}" class="barber-avatar">
                                                            @endif
                                                        </div>
                                                        <h5 class="mb-2">{{ $barber->user->name }}</h5>
                                                        <div class="barber-rating mb-2">
                                                            <span class="badge bg-warning text-dark"><i class="fas fa-star me-1"></i>{{ $barber->experience }} năm kinh nghiệm</span>
                                                        </div>
                                                        <div class="barber-specialty">
                                                            <p class="mb-2">{{ $barber->specialty }}</p>
                                                        </div>
                                                        <div class="barber-skills mt-2">
                                                            <div class="d-flex flex-wrap justify-content-center gap-1">
                                                                @foreach(explode(',', $barber->skills ?? 'Cắt tóc nam,Uốn tóc,Nhuộm tóc') as $skill)
                                                                    <span class="badge bg-light text-dark">{{ trim($skill) }}</span>
                                                                @endforeach
                                                            </div>
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

                            <div class="text-center mt-3">
                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Bạn có thể thay đổi lựa chọn thợ cắt tóc sau khi đặt lịch nếu cần.</small>
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
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
    }

    .step.active .step-circle {
        background-color: #9E8A78;
        color: white;
        transform: scale(1.1);
        box-shadow: 0 4px 8px rgba(158, 138, 120, 0.3);
    }

    .step.completed .step-circle {
        background-color: #28a745;
        color: white;
    }

    /* Styling for selected info card */
    .selected-info .card-header {
        background-color: #9E8A78 !important;
    }

    .selected-info .text-primary {
        color: #9E8A78 !important;
    }

    .selected-info .table-borderless tr:not(:last-child) td {
        padding-bottom: 0.5rem;
    }

    .selected-info .table-borderless tfoot {
        border-top: 1px dashed #dee2e6;
    }

    .selected-info .table-borderless tfoot th {
        padding-top: 0.75rem;
    }

    .step-text {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .step.active .step-text {
        color: #9E8A78;
        font-weight: bold;
    }

    .step.completed .step-text {
        color: #28a745;
    }

    .barber-card {
        cursor: pointer;
        transition: all 0.3s;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .barber-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }

    .barber-card.selected {
        border: 2px solid #9E8A78;
        background-color: rgba(158, 138, 120, 0.05);
    }

    .barber-selected-status {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #9E8A78;
        color: white;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.3s;
        z-index: 2;
    }

    .barber-card.selected .barber-selected-status {
        opacity: 1;
        transform: translateY(0);
    }

    .barber-avatar-container {
        width: 100px;
        height: 100px;
        margin: 0 auto;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid #f8f9fa;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .barber-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .barber-rating {
        margin-top: 10px;
    }

    .barber-specialty {
        color: #6c757d;
        font-style: italic;
    }

    .barber-skills {
        margin-top: 10px;
    }

    /* Hiệu ứng animation */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .pulse-animation {
        animation: pulse 0.5s;
    }

    .hover-effect {
        background-color: #f8f9fa;
        transform: translateY(-3px);
    }

    /* Nút tiếp tục và quay lại */
    .btn-primary {
        background-color: #9E8A78;
        border-color: #9E8A78;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background-color: #8a7868;
        border-color: #8a7868;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-outline-secondary {
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-outline-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

            // Hiệu ứng khi chọn
            $(this).addClass('pulse-animation');
            setTimeout(function() {
                $('.barber-card').removeClass('pulse-animation');
            }, 500);
        });

        // Khởi tạo class selected cho thợ cắt tóc đã chọn
        $('.barber-radio:checked').closest('.barber-card').addClass('selected');

        // Hiệu ứng hover
        $('.barber-card').hover(
            function() {
                if (!$(this).hasClass('selected')) {
                    $(this).addClass('hover-effect');
                }
            },
            function() {
                $(this).removeClass('hover-effect');
            }
        );
    });
</script>
@endsection