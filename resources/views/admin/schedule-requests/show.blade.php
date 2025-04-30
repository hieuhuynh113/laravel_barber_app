@extends('layouts.admin')

@section('title', 'Chi tiết yêu cầu thay đổi lịch làm việc')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết yêu cầu thay đổi lịch làm việc</h1>
        <a href="{{ route('admin.schedule-requests.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Thông báo -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Chi tiết yêu cầu -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin yêu cầu</h6>
                    <div>
                        @if($request->status == 'pending')
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="fas fa-check"></i> Phê duyệt
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="fas fa-times"></i> Từ chối
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>ID yêu cầu:</strong> #{{ $request->id }}</p>
                            <p class="mb-1"><strong>Thợ cắt tóc:</strong> {{ $request->barber->user->name }}</p>
                            <p class="mb-1"><strong>Ngày trong tuần:</strong> {{ $request->day_name }}</p>
                            <p class="mb-1">
                                <strong>Trạng thái:</strong>
                                @if($request->status == 'pending')
                                    <span class="badge bg-warning text-dark">Đang chờ</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge bg-success">Đã phê duyệt</span>
                                @elseif($request->status == 'rejected')
                                    <span class="badge bg-danger">Đã từ chối</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Ngày tạo:</strong> {{ $request->created_at->format('d/m/Y H:i') }}</p>
                            <p class="mb-1"><strong>Cập nhật lần cuối:</strong> {{ $request->updated_at->format('d/m/Y H:i') }}</p>
                            <p class="mb-1">
                                <strong>Loại yêu cầu:</strong>
                                @if($request->is_day_off)
                                    <span class="badge bg-danger">Đăng ký ngày nghỉ</span>
                                @else
                                    <span class="badge bg-success">Thay đổi giờ làm việc</span>
                                @endif
                            </p>
                            @if(!$request->is_day_off)
                                <p class="mb-1"><strong>Thời gian làm việc:</strong> {{ $request->start_time }} - {{ $request->end_time }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="font-weight-bold">Lý do yêu cầu:</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $request->reason }}
                        </div>
                    </div>

                    @if($request->admin_notes)
                        <div class="mb-4">
                            <h6 class="font-weight-bold">Ghi chú của quản trị viên:</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $request->admin_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin thợ cắt tóc</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="{{ $request->barber->user->avatar ? asset('storage/' . $request->barber->user->avatar) : asset('img/default-avatar.png') }}" alt="{{ $request->barber->user->name }}" class="img-profile rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                    <p class="mb-1"><strong>Tên:</strong> {{ $request->barber->user->name }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $request->barber->user->email }}</p>
                    <p class="mb-1"><strong>Số điện thoại:</strong> {{ $request->barber->user->phone ?? 'Chưa cập nhật' }}</p>
                    <p class="mb-1"><strong>Ngày tham gia:</strong> {{ $request->barber->user->created_at->format('d/m/Y') }}</p>

                    <div class="mt-3">
                        <a href="{{ route('admin.barbers.show', $request->barber->id) }}" class="btn btn-primary btn-sm btn-block">
                            <i class="fas fa-user"></i> Xem hồ sơ thợ cắt tóc
                        </a>
                        <a href="{{ route('admin.schedules.index', ['barber_id' => $request->barber->id]) }}" class="btn btn-info btn-sm btn-block">
                            <i class="fas fa-calendar-alt"></i> Xem lịch làm việc
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Phê duyệt -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Phê duyệt yêu cầu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.schedule-requests.approve', $request->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn phê duyệt yêu cầu thay đổi lịch làm việc này?</p>
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Ghi chú (tùy chọn)</label>
                            <textarea name="admin_notes" id="admin_notes" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-success">Phê duyệt</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Từ chối -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Từ chối yêu cầu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.schedule-requests.reject', $request->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn từ chối yêu cầu thay đổi lịch làm việc này?</p>
                        <div class="mb-3">
                            <label for="admin_notes_reject" class="form-label">Lý do từ chối</label>
                            <textarea name="admin_notes" id="admin_notes_reject" rows="3" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">Từ chối</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
