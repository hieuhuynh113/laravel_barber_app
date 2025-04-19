@extends('layouts.admin')

@section('title', 'Quản lý khung giờ')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý khung giờ</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Chọn thợ cắt tóc và ngày</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.time-slots.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="barber_id">Thợ cắt tóc:</label>
                            <select id="barber_id" name="barber_id" class="form-control" required>
                                <option value="">-- Chọn thợ cắt tóc --</option>
                                @foreach($barbers as $barber)
                                    <option value="{{ $barber->id }}" {{ $barberId == $barber->id ? 'selected' : '' }}>
                                        {{ $barber->user->name }} - {{ $barber->experience }} năm kinh nghiệm
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="date">Ngày:</label>
                            <input type="date" id="date" name="date" class="form-control" value="{{ $date }}" required>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> Xem khung giờ
                        </button>
                    </div>
                </div>
            </form>

            @if($barberId)
                <div class="card mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Tạo khung giờ mới</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.time-slots.generate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="barber_id" value="{{ $barberId }}">
                            <input type="hidden" name="date" value="{{ $date }}">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Tạo khung giờ dựa trên lịch làm việc của thợ cắt tóc cho ngày {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}.
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus-circle"></i> Tạo khung giờ
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Cập nhật hàng loạt</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.time-slots.bulk-update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="barber_id" value="{{ $barberId }}">
                            <input type="hidden" name="date" value="{{ $date }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="max_bookings">Số khách hàng tối đa cho tất cả khung giờ:</label>
                                        <input type="number" id="max_bookings" name="max_bookings" class="form-control" min="1" max="20" value="2" required>
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-sync"></i> Cập nhật tất cả khung giờ
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Danh sách khung giờ</h6>
                    </div>
                    <div class="card-body">
                        @if($timeSlots->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Khung giờ</th>
                                            <th>Số khách đã đặt</th>
                                            <th>Số khách tối đa</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($timeSlots as $slot)
                                            <tr>
                                                <td>{{ $slot->time_slot }}</td>
                                                <td>{{ $slot->booked_count }}</td>
                                                <td>
                                                    <form action="{{ route('admin.time-slots.update', $slot->id) }}" method="POST" class="d-flex">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="number" name="max_bookings" class="form-control form-control-sm" value="{{ $slot->max_bookings }}" min="1" max="20" style="width: 70px;">
                                                        <button type="submit" class="btn btn-sm btn-primary ml-2">
                                                            <i class="fas fa-save"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                                <td>
                                                    @if($slot->isAvailable())
                                                        <span class="badge bg-success text-white">Còn {{ $slot->availableSpots() }} chỗ</span>
                                                    @else
                                                        <span class="badge bg-danger text-white">Đã đầy</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#slotInfoModal{{ $slot->id }}">
                                                            <i class="fas fa-info-circle"></i> Chi tiết
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal hiển thị thông tin chi tiết -->
                                            <div class="modal fade" id="slotInfoModal{{ $slot->id }}" tabindex="-1" role="dialog" aria-labelledby="slotInfoModalLabel{{ $slot->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="slotInfoModalLabel{{ $slot->id }}">Chi tiết khung giờ {{ $slot->time_slot }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>Thợ cắt tóc:</strong> {{ $slot->barber->user->name }}</p>
                                                            <p><strong>Ngày:</strong> {{ \Carbon\Carbon::parse($slot->date)->format('d/m/Y') }}</p>
                                                            <p><strong>Giờ:</strong> {{ $slot->time_slot }}</p>
                                                            <p><strong>Số khách đã đặt:</strong> {{ $slot->booked_count }}</p>
                                                            <p><strong>Số khách tối đa:</strong> {{ $slot->max_bookings }}</p>
                                                            <p><strong>Trạng thái:</strong> 
                                                                @if($slot->isAvailable())
                                                                    <span class="badge bg-success text-white">Còn {{ $slot->availableSpots() }} chỗ</span>
                                                                @else
                                                                    <span class="badge bg-danger text-white">Đã đầy</span>
                                                                @endif
                                                            </p>
                                                            <p><strong>Ngày tạo:</strong> {{ $slot->created_at->format('d/m/Y H:i:s') }}</p>
                                                            <p><strong>Cập nhật lần cuối:</strong> {{ $slot->updated_at->format('d/m/Y H:i:s') }}</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Không có khung giờ nào cho ngày này. Hãy nhấn nút "Tạo khung giờ" để tạo các khung giờ dựa trên lịch làm việc của thợ cắt tóc.
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Vui lòng chọn thợ cắt tóc và ngày để xem và quản lý các khung giờ.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Xử lý khi thay đổi thợ cắt tóc
        $('#barber_id').change(function() {
            if ($(this).val()) {
                $('#date').prop('disabled', false);
            } else {
                $('#date').prop('disabled', true);
            }
        });

        // Khởi tạo trạng thái ban đầu
        if (!$('#barber_id').val()) {
            $('#date').prop('disabled', true);
        }
    });
</script>
@endsection
