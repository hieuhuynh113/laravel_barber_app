@extends('layouts.admin')

@section('title', 'Quản lý yêu cầu thay đổi lịch làm việc')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý yêu cầu thay đổi lịch làm việc</h1>
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

    <!-- Bộ lọc -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.schedule-requests.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="barber_id" class="form-label">Thợ cắt tóc</label>
                        <select name="barber_id" id="barber_id" class="form-select">
                            <option value="">-- Tất cả thợ cắt tóc --</option>
                            @foreach($barbers as $barber)
                                <option value="{{ $barber->id }}" {{ request('barber_id') == $barber->id ? 'selected' : '' }}>
                                    {{ $barber->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Đang chờ</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã phê duyệt</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Tìm kiếm theo tên thợ cắt tóc hoặc lý do..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <div class="d-grid gap-2 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Lọc
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách yêu cầu -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách yêu cầu thay đổi lịch làm việc</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Thợ cắt tóc</th>
                            <th>Ngày trong tuần</th>
                            <th>Thời gian</th>
                            <th>Ngày nghỉ</th>
                            <th>Lý do</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            <tr>
                                <td>{{ $request->id }}</td>
                                <td>{{ $request->barber->user->name }}</td>
                                <td>{{ $request->day_name }}</td>
                                <td>
                                    @if($request->is_day_off)
                                        <span class="text-muted">-</span>
                                    @else
                                        {{ $request->start_time }} - {{ $request->end_time }}
                                    @endif
                                </td>
                                <td>
                                    @if($request->is_day_off)
                                        <span class="badge bg-danger">Ngày nghỉ</span>
                                    @else
                                        <span class="badge bg-success">Ngày làm việc</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($request->reason, 50) }}</td>
                                <td>
                                    @if($request->status == 'pending')
                                        <span class="badge bg-warning text-dark">Đang chờ</span>
                                    @elseif($request->status == 'approved')
                                        <span class="badge bg-success">Đã phê duyệt</span>
                                    @elseif($request->status == 'rejected')
                                        <span class="badge bg-danger">Đã từ chối</span>
                                    @endif
                                </td>
                                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.schedule-requests.show', $request->id) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($request->status == 'pending')
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal{{ $request->id }}" title="Phê duyệt yêu cầu">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}" title="Từ chối yêu cầu">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            <!-- Modal Phê duyệt -->
                            <div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1" aria-labelledby="approveModalLabel{{ $request->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="approveModalLabel{{ $request->id }}">Phê duyệt yêu cầu</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.schedule-requests.approve', $request->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <p>Bạn có chắc chắn muốn phê duyệt yêu cầu thay đổi lịch làm việc này?</p>
                                                <div class="mb-3">
                                                    <label for="admin_notes_approve_{{ $request->id }}" class="form-label">Ghi chú (tùy chọn)</label>
                                                    <textarea name="admin_notes" id="admin_notes_approve_{{ $request->id }}" rows="3" class="form-control"></textarea>
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
                            <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $request->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="rejectModalLabel{{ $request->id }}">Từ chối yêu cầu</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.schedule-requests.reject', $request->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <p>Bạn có chắc chắn muốn từ chối yêu cầu thay đổi lịch làm việc này?</p>
                                                <div class="mb-3">
                                                    <label for="admin_notes_reject_{{ $request->id }}" class="form-label">Lý do từ chối</label>
                                                    <textarea name="admin_notes" id="admin_notes_reject_{{ $request->id }}" rows="3" class="form-control" required></textarea>
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
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Không có yêu cầu thay đổi lịch làm việc nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <div class="d-flex justify-content-end">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
