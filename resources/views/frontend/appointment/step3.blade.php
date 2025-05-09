@extends('layouts.frontend')

@section('title', 'Đặt lịch - Bước 3: Chọn thời gian')

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
                                <div class="step completed">
                                    <div class="step-circle">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text">Chọn dịch vụ</div>
                                </div>
                                <div class="step completed">
                                    <div class="step-circle">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text">Chọn thợ cắt tóc</div>
                                </div>
                                <div class="step active">
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

                        <!-- Hiển thị tóm tắt thông tin đã chọn -->
                        <div class="selected-info mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary text-white py-3">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin đã chọn</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="service-summary">
                                                <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-cut me-2"></i>Dịch vụ</h6>
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
                                                                <th class="text-end text-primary">{{ number_format($totalPrice) }} VNĐ</th>
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
                                        <div class="col-md-6 mb-3">
                                            <div class="barber-summary">
                                                <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-user me-2"></i>Thợ cắt tóc</h6>
                                                @php $barber = session('appointment_barber'); @endphp
                                                <div class="d-flex align-items-center">
                                                    <div class="barber-avatar me-3">
                                                        <img src="{{ get_user_avatar($barber->user, 'small') }}" alt="{{ $barber->user->name }}" class="rounded-circle border shadow-sm" width="60" height="60">
                                                    </div>
                                                    <div class="barber-info">
                                                        <h6 class="mb-1">{{ $barber->user->name }}</h6>
                                                        <div class="d-flex align-items-center mb-1">
                                                            <span class="badge bg-warning text-dark me-2">
                                                                <i class="fas fa-star me-1"></i>{{ $barber->experience }} năm kinh nghiệm
                                                            </span>
                                                        </div>
                                                        <small class="text-muted">{{ $barber->specialty }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form chọn ngày và giờ -->
                        <h5 class="card-title mb-4">Bước 3: Chọn thời gian</h5>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-circle me-2 fs-4"></i>
                                    <div>
                                        @if($errors->has('time_slot'))
                                            <strong>{{ $errors->first('time_slot') }}</strong>
                                        @elseif($errors->has('date'))
                                            <strong>{{ $errors->first('date') }}</strong>
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

                        <form action="{{ route('appointment.post.step3') }}" method="POST" id="timeForm">
                            @csrf
                            <input type="hidden" name="date" id="selectedDate" value="{{ old('date', $currentDate) }}">

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label class="form-label">Chọn ngày</label>
                                    <div id="datepicker"></div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label class="form-label">Chọn giờ bắt đầu</label>
                                    <div class="time-slots-container" id="timeSlots">
                                        @if(count($timeSlots) > 0)
                                            @foreach($timeSlots as $slot)
                                                <div class="time-slot">
                                                    <input type="radio" name="time_slot" id="slot-{{ $slot['time'] }}" value="{{ $slot['formatted'] }}" class="time-slot-input" {{ old('time_slot') == $slot['formatted'] ? 'checked' : '' }}>
                                                    <label for="slot-{{ $slot['time'] }}" class="time-slot-label">
                                                        {{ $slot['formatted'] }}
                                                        @if($slot['available_spots'] > 0)
                                                            @php
                                                                $badgeClass = $slot['available_spots'] > ($slot['max_bookings'] / 2) ? 'bg-success' : 'bg-warning';
                                                            @endphp
                                                            <span class="badge {{ $badgeClass }} ms-2">Còn {{ $slot['available_spots'] }} chỗ</span>
                                                        @else
                                                            <span class="badge bg-danger ms-2">Đã đầy</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-info">
                                                Không có giờ trống cho ngày này. Vui lòng chọn ngày khác.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <a href="{{ route('appointment.step2') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary" id="continueBtn" {{ count($timeSlots) == 0 ? 'disabled' : '' }}>Tiếp tục <i class="fas fa-arrow-right"></i></button>
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Styling for selected info card */
    .selected-info .card-header {
        background-color: #9E8A78 !important;
    }

    .selected-info .text-primary {
        color: #9E8A78 !important;
    }

    .selected-info .icon-wrapper {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
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

    .barber-avatar img {
        border: 3px solid #fff;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    /* Progress steps styling */
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

    .step.completed .step-circle {
        background-color: #28a745;
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

    .step.completed .step-text {
        color: #28a745;
    }

    /* Time slots styling */
    .time-slots-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .time-slot {
        position: relative;
    }

    .time-slot-input {
        display: none;
    }

    .time-slot-label {
        display: block;
        padding: 10px 15px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
    }

    .time-slot-label:hover {
        background-color: #e9ecef;
    }

    .time-slot-input:checked + .time-slot-label {
        background-color: #9E8A78;
        border-color: #9E8A78;
        color: white;
    }

    .time-slot-input:checked + .time-slot-label .badge {
        background-color: #ffffff !important;
        color: #9E8A78;
    }

    /* Badge styling */
    .time-slot-label .badge {
        min-width: 80px;
        padding: 5px 8px;
        font-weight: 500;
        font-size: 0.75rem;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .time-slot-label .badge.bg-success {
        background-color: #28a745 !important;
    }

    .time-slot-label .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #212529;
    }

    .time-slot-label .badge.bg-danger {
        background-color: #dc3545 !important;
    }

    .time-slot-input:disabled + .time-slot-label {
        background-color: #e9ecef;
        color: #adb5bd;
        cursor: not-allowed;
        text-decoration: line-through;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(document).ready(function() {
        // Khởi tạo datepicker
        const fp = flatpickr("#datepicker", {
            inline: true,
            minDate: "today",
            dateFormat: "Y-m-d",
            defaultDate: "{{ old('date', $currentDate) }}",
            disable: [
                function(date) {
                    // Disable weekend nếu cần
                    // return (date.getDay() === 0 || date.getDay() === 6);
                    return false;
                }
            ],
            onChange: function(selectedDates, dateStr, instance) {
                $('#selectedDate').val(dateStr);
                loadTimeSlots(dateStr);
            }
        });

        // Load time slots cho ngày mặc định khi trang được tải
        loadTimeSlots("{{ old('date', $currentDate) }}");

        // Load time slots khi chọn ngày
        function loadTimeSlots(date) {
            $.ajax({
                url: "{{ route('appointment.check-availability') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    date: date,
                    barber_id: "{{ session('appointment_barber')->id }}"
                },
                beforeSend: function() {
                    $('#timeSlots').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div></div>');
                },
                success: function(response) {
                    console.log('Server response:', response);
                    let html = '';

                    // Lấy thời gian hiện tại của client
                    const clientNow = new Date();
                    const hours = clientNow.getHours().toString().padStart(2, '0');
                    const minutes = clientNow.getMinutes().toString().padStart(2, '0');
                    const clientTimeStr = `${hours}:${minutes}`;

                    // Kiểm tra xem có phải ngày hôm nay không
                    const today = new Date();
                    const selectedDate = new Date(date);
                    const isToday = today.getFullYear() === selectedDate.getFullYear() &&
                                    today.getMonth() === selectedDate.getMonth() &&
                                    today.getDate() === selectedDate.getDate();

                    console.log('Client time:', clientTimeStr);
                    console.log('Is today (client-side):', isToday);

                    // Hiển thị thông tin thời gian hiện tại
                    if (isToday) {
                        html += `<div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i> Thời gian hiện tại: <strong>${clientTimeStr}</strong>.
                            Chỉ hiển thị các khung giờ trong tương lai.
                        </div>`;
                    }

                    // Lọc các khung giờ đã qua nếu là ngày hôm nay
                    let filteredSlots = response.timeSlots;
                    if (isToday) {
                        filteredSlots = response.timeSlots.filter(function(slot) {
                            // Chuyển đổi thời gian của slot thành đối tượng Date
                            const [slotHours, slotMinutes] = slot.formatted.split(':').map(Number);

                            // So sánh với thời gian hiện tại + 60 phút
                            const slotTime = new Date();
                            slotTime.setHours(slotHours, slotMinutes, 0);

                            const minAllowedTime = new Date();
                            minAllowedTime.setMinutes(minAllowedTime.getMinutes() + 60);

                            console.log(`Slot time: ${slot.formatted}, Current time + 60m: ${minAllowedTime.getHours()}:${minAllowedTime.getMinutes()}`);

                            // Chỉ giữ lại các khung giờ trong tương lai
                            return slotTime > minAllowedTime;
                        });

                        console.log('Filtered slots:', filteredSlots.length);
                    }

                    if (filteredSlots.length > 0) {
                        html += '<div class="time-slots-container">';
                        filteredSlots.forEach(function(slot) {
                            html += `
                                <div class="time-slot">
                                    <input type="radio" name="time_slot" id="slot-${slot.time}" value="${slot.formatted}" class="time-slot-input">
                                    <label for="slot-${slot.time}" class="time-slot-label">
                                        ${slot.formatted}
                                        ${getBadgeHtml(slot.available_spots, slot.max_bookings)}
                                    </label>
                                </div>
                            `;
                        });
                        html += '</div>';
                        $('#continueBtn').prop('disabled', false);
                    } else {
                        if (isToday) {
                            html += '<div class="alert alert-warning">Không còn khung giờ trống cho hôm nay. Vui lòng chọn ngày khác.</div>';
                        } else {
                            html += '<div class="alert alert-info">Không có giờ trống cho ngày này. Vui lòng chọn ngày khác.</div>';
                        }
                        $('#continueBtn').prop('disabled', true);
                    }

                    $('#timeSlots').html(html);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading time slots:', error);
                    $('#timeSlots').html('<div class="alert alert-danger">Đã xảy ra lỗi khi tải dữ liệu. Vui lòng thử lại sau.</div>');
                    $('#continueBtn').prop('disabled', true);
                }
            });
        }

        // Hàm tạo HTML cho badge hiển thị số chỗ trống
        function getBadgeHtml(availableSpots, maxBookings) {
            if (availableSpots <= 0) {
                return '<span class="badge bg-danger ms-2">Đã đầy</span>';
            }

            const badgeClass = availableSpots > (maxBookings / 2) ? 'bg-success' : 'bg-warning';
            return `<span class="badge ${badgeClass} ms-2">Còn ${availableSpots} chỗ</span>`;
        }

        // Validate form trước khi submit
        $('#timeForm').on('submit', function(e) {
            if (!$('input[name="time_slot"]:checked').val()) {
                e.preventDefault();
                alert('Vui lòng chọn giờ bắt đầu');
            }
        });
    });
</script>
@endsection