@extends('layouts.admin')

@section('title', 'Quản lý lịch làm việc')

@section('styles')
<style>
    /* Styles for schedule table */
    .schedule-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #e3e6f0;
        table-layout: fixed;
    }

    .schedule-table th, .schedule-table td {
        border: 1px solid #e3e6f0;
        padding: 12px 15px;
        text-align: center;
        vertical-align: middle;
    }

    .schedule-table th {
        background-color: #f8f9fc;
        border-bottom-width: 1px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        color: #4e73df;
    }

    .schedule-table .col-day {
        width: 20%;
        text-align: left;
    }

    .schedule-table .col-time {
        width: 20%;
    }

    .schedule-table .col-max {
        width: 15%;
    }

    .schedule-table .col-off {
        width: 15%;
    }

    .time-input:disabled, .form-control:disabled {
        background-color: #f8f9fc;
        opacity: 0.7;
    }

    .custom-control-input:checked ~ .custom-control-label::before {
        background-color: #e74a3b;
        border-color: #e74a3b;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý lịch làm việc</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lịch làm việc của thợ cắt tóc</h6>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="barber_id" class="font-weight-bold">Chọn thợ cắt tóc:</label>
                <select id="barber_select" class="form-control" onchange="window.location.href = '{{ route('admin.schedules.index') }}?barber_id=' + this.value">
                    @foreach($barbers as $barber)
                        <option value="{{ $barber->id }}" {{ $barberId == $barber->id ? 'selected' : '' }}>
                            {{ $barber->user->name }} - {{ $barber->experience }} năm kinh nghiệm
                        </option>
                    @endforeach
                </select>
            </div>

            <form action="{{ route('admin.schedules.batch-update') }}" method="POST">
                @csrf
                <input type="hidden" name="barber_id" value="{{ $barberId }}">

                <div class="table-responsive">
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th class="col-day">Ngày trong tuần</th>
                                <th class="col-time">Giờ bắt đầu</th>
                                <th class="col-time">Giờ kết thúc</th>
                                <th class="col-max">Số KH tối đa</th>
                                <th class="col-off">Ngày nghỉ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                <tr class="{{ $schedule->is_day_off ? 'day-off' : '' }}">
                                    <td class="col-day">{{ $schedule->getDayNameAttribute() }}</td>
                                    <td class="col-time">
                                        <input type="time" class="form-control time-input"
                                               name="days[{{ $schedule->day_of_week }}][start_time]"
                                               value="{{ optional($schedule->start_time)->format('H:i') }}"
                                               {{ $schedule->is_day_off ? 'disabled' : '' }}>
                                    </td>
                                    <td class="col-time">
                                        <input type="time" class="form-control time-input"
                                               name="days[{{ $schedule->day_of_week }}][end_time]"
                                               value="{{ optional($schedule->end_time)->format('H:i') }}"
                                               {{ $schedule->is_day_off ? 'disabled' : '' }}>
                                    </td>
                                    <td class="col-max">
                                        <input type="number" class="form-control"
                                               name="days[{{ $schedule->day_of_week }}][max_appointments]"
                                               value="{{ $schedule->max_appointments ?? 3 }}"
                                               min="1" max="20"
                                               {{ $schedule->is_day_off ? 'disabled' : '' }}>
                                    </td>
                                    <td class="col-off">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input day-off-checkbox"
                                                   id="day_off_{{ $schedule->day_of_week }}"
                                                   name="days[{{ $schedule->day_of_week }}][is_day_off]"
                                                   {{ $schedule->is_day_off ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="day_off_{{ $schedule->day_of_week }}">Ngày nghỉ</label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu thay đổi
                    </button>
                    <a href="{{ route('admin.barbers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại danh sách thợ cắt tóc
                    </a>
                </div>
            </form>

            <div class="alert alert-info mt-4">
                <h5><i class="fas fa-info-circle"></i> Hướng dẫn:</h5>
                <ul>
                    <li>Đặt <strong>Số KH tối đa</strong> để giới hạn số lượng khách hàng có thể đặt lịch trong một ngày.</li>
                    <li>Đánh dấu <strong>Ngày nghỉ</strong> để đánh dấu ngày không làm việc.</li>
                    <li>Khung giờ làm việc được chia thành các slot 30 phút. Ví dụ: 8:00 - 17:00 sẽ tạo ra các slot 8:00-8:30, 8:30-9:00,... 16:30-17:00.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Xử lý khi thay đổi ngày nghỉ
        $('.day-off-checkbox').on('change', function() {
            var tr = $(this).closest('tr');
            var inputs = tr.find('input.form-control');

            if ($(this).is(':checked')) {
                inputs.attr('disabled', 'disabled');
            } else {
                inputs.removeAttr('disabled');
            }
        });
    });
</script>
@endsection