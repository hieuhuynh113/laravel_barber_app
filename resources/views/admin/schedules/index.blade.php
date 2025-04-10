@extends('layouts.admin')

@section('title', 'Quản lý lịch làm việc')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý lịch làm việc</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
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
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Ngày trong tuần</th>
                                <th>Giờ bắt đầu</th>
                                <th>Giờ kết thúc</th>
                                <th>Số KH tối đa</th>
                                <th>Ngày nghỉ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                <tr>
                                    <td>{{ $schedule->getDayNameAttribute() }}</td>
                                    <td>
                                        <input type="time" class="form-control time-input" 
                                               name="days[{{ $schedule->day_of_week }}][start_time]" 
                                               value="{{ optional($schedule->start_time)->format('H:i') }}"
                                               {{ $schedule->is_day_off ? 'disabled' : '' }}>
                                    </td>
                                    <td>
                                        <input type="time" class="form-control time-input" 
                                               name="days[{{ $schedule->day_of_week }}][end_time]" 
                                               value="{{ optional($schedule->end_time)->format('H:i') }}"
                                               {{ $schedule->is_day_off ? 'disabled' : '' }}>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" 
                                               name="days[{{ $schedule->day_of_week }}][max_appointments]" 
                                               value="{{ $schedule->max_appointments ?? 3 }}"
                                               min="1" max="20"
                                               {{ $schedule->is_day_off ? 'disabled' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input day-off-checkbox" 
                                                   id="day_off_{{ $schedule->day_of_week }}" 
                                                   name="days[{{ $schedule->day_of_week }}][is_day_off]" 
                                                   {{ $schedule->is_day_off ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="day_off_{{ $schedule->day_of_week }}"></label>
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
                    <a href="{{ route('admin.barbers.show', $barberId) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
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