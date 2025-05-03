@extends('layouts.app')

@section('title', 'Lịch làm việc')

@push('head-scripts')
<script>
    // Script được thêm vào phần head của trang
    document.addEventListener('DOMContentLoaded', function() {
        // Hàm để ẩn/hiện các trường thời gian
        function setupDayOffToggle() {
            var checkbox = document.getElementById('is_day_off');
            var timeFields = document.getElementById('time-fields');

            if (checkbox && timeFields) {
                // Thêm sự kiện change
                checkbox.addEventListener('change', function() {
                    timeFields.style.display = this.checked ? 'none' : 'block';
                });

                // Kiểm tra trạng thái ban đầu
                timeFields.style.display = checkbox.checked ? 'none' : 'block';
            }
        }

        // Thiết lập khi trang được tải
        setupDayOffToggle();

        // Thiết lập khi modal được hiển thị
        var requestModal = document.getElementById('requestModal');
        if (requestModal) {
            requestModal.addEventListener('shown.bs.modal', setupDayOffToggle);
        }
    });
</script>
@endpush

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/barber-dashboard.css') }}">
<style>
    .schedule-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }

    .schedule-card .card-header {
        background-color: #2c3e50;
        color: #fff;
        border-radius: 10px 10px 0 0;
        padding: 1rem 1.5rem;
    }

    .schedule-card .card-body {
        padding: 1.5rem;
    }

    .day-card {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        border-left: 4px solid #3498db;
        transition: all 0.3s;
    }

    .day-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .day-card.day-off {
        border-left-color: #e74c3c;
        background-color: #fdeeee;
    }

    .day-name {
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }

    .time-info {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .time-info i {
        color: #3498db;
        margin-right: 0.5rem;
    }

    .time-info.day-off i {
        color: #e74c3c;
    }

    .time-value {
        font-weight: 600;
    }

    .max-appointments {
        display: flex;
        align-items: center;
    }

    .max-appointments i {
        color: #2ecc71;
        margin-right: 0.5rem;
    }

    .edit-btn {
        padding: 0.5rem 0.75rem;
        border-radius: 5px;
        transition: all 0.3s;
        margin-left: auto;
    }

    .edit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .request-form {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-top: 1.5rem;
        border: 1px solid #dee2e6;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
    }

    .request-history {
        margin-top: 2rem;
    }

    .request-item {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #3498db;
    }

    .request-item.pending {
        border-left-color: #f39c12;
    }

    .request-item.approved {
        border-left-color: #2ecc71;
    }

    .request-item.rejected {
        border-left-color: #e74c3c;
    }

    .request-day {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }

    .request-time {
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .request-reason {
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #dee2e6;
    }

    .request-status {
        padding: 0.25rem 0.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .status-pending {
        background-color: #fff8e1;
        color: #f39c12;
        border: 1px solid #f39c12;
    }

    .status-approved {
        background-color: #e8f8f5;
        color: #2ecc71;
        border: 1px solid #2ecc71;
    }

    .status-rejected {
        background-color: #fdeeee;
        color: #e74c3c;
        border: 1px solid #e74c3c;
    }
</style>
@endsection

@section('content')
<div class="container dashboard-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="dashboard-title">Lịch làm việc</h1>
                    <p class="dashboard-subtitle">Xem lịch làm việc hiện tại và gửi yêu cầu thay đổi</p>
                </div>
                <a href="{{ route('barber.dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại Dashboard
                </a>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    <!-- Lịch làm việc hiện tại -->
                    <div class="schedule-card">
                        <div class="card-header">
                            <h5 class="mb-0">Lịch làm việc hiện tại</h5>
                        </div>
                        <div class="card-body">
                            @foreach($schedules as $schedule)
                                <div class="day-card {{ $schedule->is_day_off ? 'day-off' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="day-name">{{ $schedule->day_name }}</div>
                                            @if($schedule->is_day_off)
                                                <div class="time-info day-off">
                                                    <i class="fas fa-ban"></i>
                                                    <span class="time-value">Ngày nghỉ</span>
                                                </div>
                                            @else
                                                <div class="time-info">
                                                    <i class="far fa-clock"></i>
                                                    <span class="time-value">{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</span>
                                                </div>
                                                <div class="max-appointments">
                                                    <i class="fas fa-users"></i>
                                                    <span>Số lượng khách tối đa: {{ $schedule->max_appointments }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-outline-primary edit-btn" data-bs-toggle="modal" data-bs-target="#requestModal" data-day="{{ $schedule->day_of_week }}" data-day-name="{{ $schedule->day_name }}" data-start="{{ $schedule->start_time->format('H:i') }}" data-end="{{ $schedule->end_time->format('H:i') }}" data-off="{{ $schedule->is_day_off ? '1' : '0' }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Thông tin và hướng dẫn -->
                    <div class="schedule-card">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Lưu ý:</strong> Để thay đổi lịch làm việc, bạn cần gửi yêu cầu và chờ quản trị viên phê duyệt.
                            </div>
                            <p>Các yêu cầu thay đổi lịch làm việc sẽ được xem xét và phê duyệt trong vòng 24 giờ.</p>
                            <p>Nếu bạn cần thay đổi lịch làm việc gấp, vui lòng liên hệ trực tiếp với quản lý.</p>
                            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#requestModal">
                                <i class="fas fa-plus me-2"></i>Gửi yêu cầu thay đổi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal gửi yêu cầu thay đổi lịch làm việc -->
<div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestModalLabel">Gửi yêu cầu thay đổi lịch làm việc</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <script>
                // Script trực tiếp trong modal để đảm bảo nó được thực thi
                document.addEventListener('DOMContentLoaded', function() {
                    // Xử lý khi checkbox thay đổi
                    document.getElementById('is_day_off').addEventListener('change', function() {
                        document.getElementById('time-fields').style.display = this.checked ? 'none' : 'block';
                    });

                    // Kiểm tra trạng thái ban đầu
                    if (document.getElementById('is_day_off').checked) {
                        document.getElementById('time-fields').style.display = 'none';
                    }
                });
            </script>
            <form action="{{ route('barber.schedules.request-change') }}" method="POST" id="scheduleChangeForm" onsubmit="return validateAndSubmitForm(this);">
                @csrf
                <div class="modal-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <script>
                        // Script trực tiếp trong body của modal
                        function toggleTimeFieldsVisibility() {
                            var checkbox = document.getElementById('is_day_off');
                            var timeFields = document.getElementById('time-fields');
                            if (checkbox && timeFields) {
                                timeFields.style.display = checkbox.checked ? 'none' : 'block';
                                console.log('Toggle time fields:', checkbox.checked ? 'hidden' : 'visible');
                            }
                        }

                        // Thực hiện ngay khi script được tải
                        setTimeout(toggleTimeFieldsVisibility, 100);

                        // Thêm sự kiện cho checkbox
                        window.addEventListener('load', function() {
                            var checkbox = document.getElementById('is_day_off');
                            if (checkbox) {
                                checkbox.addEventListener('change', toggleTimeFieldsVisibility);
                            }
                        });
                    </script>
                    <div class="mb-3">
                        <label for="day_of_week" class="form-label">Ngày trong tuần</label>
                        <select name="day_of_week" id="day_of_week" class="form-select" required>
                            <option value="0">Chủ nhật</option>
                            <option value="1">Thứ hai</option>
                            <option value="2">Thứ ba</option>
                            <option value="3">Thứ tư</option>
                            <option value="4">Thứ năm</option>
                            <option value="5">Thứ sáu</option>
                            <option value="6">Thứ bảy</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_day_off" id="is_day_off"
                                onclick="document.getElementById('time-fields').style.display = this.checked ? 'none' : 'block';"
                                onchange="document.getElementById('time-fields').style.display = this.checked ? 'none' : 'block';"
                                data-controls="time-fields">
                            <label class="form-check-label" for="is_day_off">
                                Đánh dấu là ngày nghỉ
                            </label>
                        </div>
                    </div>

                    <div id="time-fields" class="time-fields-container" style="display: none;" data-controlled-by="is_day_off">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Giờ bắt đầu</label>
                                <input type="time" name="start_time" id="start_time" class="form-control" value="08:00">
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label">Giờ kết thúc</label>
                                <input type="time" name="end_time" id="end_time" class="form-control" value="17:00">
                            </div>
                        </div>
                    </div>

                    <!-- Trường ẩn để lưu giá trị khi ngày nghỉ -->
                    <input type="hidden" name="default_start_time" value="08:00">
                    <input type="hidden" name="default_end_time" value="17:00">

                    <div class="mb-3">
                        <label for="reason" class="form-label">Lý do thay đổi</label>
                        <textarea name="reason" id="reason" rows="3" class="form-control" required placeholder="Vui lòng nêu lý do bạn muốn thay đổi lịch làm việc"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" onclick="console.log('Nút submit được nhấn');">Gửi yêu cầu</button>
                </div>

                <!-- Hiển thị thông tin debug -->
                <div class="p-3 border-top">
                    <div class="form-text text-muted">
                        <small>Debug: Đảm bảo form được submit đúng cách</small>
                    </div>
                </div>

                <script>
                    // Script trực tiếp trong form
                    (function() {
                        // Hàm để ẩn/hiện các trường thời gian
                        function updateTimeFieldsVisibility() {
                            var checkbox = document.getElementById('is_day_off');
                            var timeFields = document.getElementById('time-fields');

                            if (checkbox && timeFields) {
                                if (checkbox.checked) {
                                    timeFields.style.display = 'none';
                                } else {
                                    timeFields.style.display = 'block';
                                }
                            }
                        }

                        // Thực hiện ngay lập tức
                        updateTimeFieldsVisibility();

                        // Thêm sự kiện cho checkbox
                        var checkbox = document.getElementById('is_day_off');
                        if (checkbox) {
                            checkbox.addEventListener('change', updateTimeFieldsVisibility);
                        }

                        // Thêm sự kiện submit cho form
                        var form = document.getElementById('scheduleChangeForm');
                        if (form) {
                            form.addEventListener('submit', function(e) {
                                // Đảm bảo các trường thời gian có giá trị khi submit
                                if (checkbox && checkbox.checked) {
                                    document.getElementById('start_time').value = '08:00';
                                    document.getElementById('end_time').value = '17:00';
                                }

                                // Kiểm tra xem form có hợp lệ không
                                if (!form.checkValidity()) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    console.error('Form không hợp lệ');
                                    return false;
                                }

                                console.log('Form đang được submit...');
                                return true;
                            });
                        }
                    })();
                </script>
            </form>
            <script>
                // Script cuối modal để đảm bảo nó được thực thi sau khi modal được tải hoàn toàn
                document.getElementById('requestModal').addEventListener('shown.bs.modal', function () {
                    // Kiểm tra trạng thái checkbox và ẩn/hiện các trường thời gian
                    var checkbox = document.getElementById('is_day_off');
                    var timeFields = document.getElementById('time-fields');
                    if (checkbox && timeFields) {
                        timeFields.style.display = checkbox.checked ? 'none' : 'block';
                        console.log('Modal shown, time fields visibility updated');
                    }
                });
            </script>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* CSS để ẩn hoàn toàn các trường thời gian khi chọn ngày nghỉ */
    #is_day_off:checked ~ .modal-body #time-fields {
        display: none !important;
    }

    /* Thêm CSS để đảm bảo các trường thời gian được ẩn khi checkbox được chọn */
    .hide-time-fields {
        display: none !important;
    }

    /* CSS trực tiếp để ẩn các trường thời gian khi checkbox được chọn */
    input[name="is_day_off"]:checked ~ #time-fields,
    input[name="is_day_off"]:checked ~ * #time-fields,
    input[name="is_day_off"]:checked ~ * * #time-fields,
    input[name="is_day_off"]:checked ~ * * * #time-fields {
        display: none !important;
    }
</style>
@endsection

@section('scripts')
<script>
    // Hàm xác thực và submit form
    function validateAndSubmitForm(form) {
        console.log('Đang xác thực form...');

        // Đảm bảo các trường thời gian có giá trị khi submit
        if (document.getElementById('is_day_off').checked) {
            document.getElementById('start_time').value = '08:00';
            document.getElementById('end_time').value = '17:00';
        }

        // Kiểm tra trường lý do
        var reason = document.getElementById('reason').value.trim();
        if (!reason) {
            alert('Vui lòng nhập lý do thay đổi lịch làm việc');
            document.getElementById('reason').focus();
            return false;
        }

        console.log('Form hợp lệ, đang submit...');
        return true;
    }

    $(document).ready(function() {
        // Hàm đơn giản để ẩn/hiện các trường thời gian
        function toggleTimeFields() {
            if ($('#is_day_off').is(':checked')) {
                // Ẩn các trường thời gian
                $('#time-fields').addClass('hide-time-fields');
                $('#time-fields').hide();
            } else {
                // Hiển thị các trường thời gian
                $('#time-fields').removeClass('hide-time-fields');
                $('#time-fields').show();
            }
        }

        // Gọi hàm khi checkbox thay đổi
        $('#is_day_off').on('change', toggleTimeFields);

        // Gọi hàm khi modal hiển thị
        $('#requestModal').on('shown.bs.modal', function() {
            toggleTimeFields();
            console.log('Modal shown, applying time fields visibility');
        });

        // Xử lý khi mở modal
        $('#requestModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var day = button.data('day');
            var start = button.data('start');
            var end = button.data('end');
            var isOff = button.data('off');

            if (day !== undefined) {
                $('#day_of_week').val(day);
                $('#start_time').val(start || '08:00');
                $('#end_time').val(end || '17:00');

                if (isOff == '1') {
                    $('#is_day_off').prop('checked', true);
                } else {
                    $('#is_day_off').prop('checked', false);
                }
            } else {
                // Reset form khi mở modal từ nút "Gửi yêu cầu thay đổi"
                $('#day_of_week').val('0');
                $('#start_time').val('08:00');
                $('#end_time').val('17:00');
                $('#is_day_off').prop('checked', false);
                $('#reason').val('');
            }

            // Áp dụng ngay sau khi thiết lập giá trị
            setTimeout(toggleTimeFields, 0);
        });

        // Xử lý khi submit form
        $('#scheduleChangeForm').submit(function(e) {
            // Đảm bảo các trường thời gian có giá trị khi submit
            if ($('#is_day_off').is(':checked')) {
                $('#start_time').val('08:00');
                $('#end_time').val('17:00');
            }

            // Kiểm tra xem form có hợp lệ không
            if (this.checkValidity() === false) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }

            // Hiển thị thông báo đang xử lý
            console.log('Form đang được submit...');
            return true;
        });

        // Thêm một đoạn code để đảm bảo các trường thời gian được ẩn khi checkbox được chọn
        // Thực hiện ngay khi trang tải
        toggleTimeFields();

        // Thêm một đoạn code để kiểm tra trạng thái checkbox sau khi modal hiển thị
        $('#requestModal').on('shown.bs.modal', function() {
            console.log('Modal shown, checkbox state:', $('#is_day_off').is(':checked'));
            toggleTimeFields();
        });

        // Thêm một đoạn code để đảm bảo các trường thời gian được ẩn khi checkbox được chọn
        // Thực hiện sau khi trang tải hoàn toàn
        $(window).on('load', function() {
            toggleTimeFields();

            // Thêm sự kiện click trực tiếp cho checkbox
            $('#is_day_off').on('click', function() {
                var isChecked = $(this).is(':checked');
                if (isChecked) {
                    $('#time-fields').hide();
                } else {
                    $('#time-fields').show();
                }
            });
        });
    });
</script>

<!-- Thêm script trực tiếp vào cuối trang -->
<script>
    // Script trực tiếp ở cuối trang
    document.addEventListener('DOMContentLoaded', function() {
        // Hàm để ẩn/hiện các trường thời gian
        function handleDayOffToggle() {
            var checkbox = document.getElementById('is_day_off');
            var timeFields = document.getElementById('time-fields');

            if (checkbox && timeFields) {
                timeFields.style.display = checkbox.checked ? 'none' : 'block';
            }
        }

        // Thêm sự kiện cho checkbox
        var checkbox = document.getElementById('is_day_off');
        if (checkbox) {
            checkbox.addEventListener('change', handleDayOffToggle);
            checkbox.addEventListener('click', handleDayOffToggle);
        }

        // Thêm sự kiện cho modal
        var modal = document.getElementById('requestModal');
        if (modal) {
            modal.addEventListener('shown.bs.modal', handleDayOffToggle);
        }

        // Thực hiện ngay khi script được tải
        setTimeout(handleDayOffToggle, 100);
    });
</script>
@endsection
