@extends('layouts.admin')

@section('title', 'Quản lý yêu cầu thay đổi lịch làm việc')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/schedule-requests.css') }}">
@endsection

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

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="sort_by" class="form-label">Sắp xếp theo</label>
                        <select name="sort_by" id="sort_by" class="form-select">
                            <option value="created_at" {{ request('sort_by') === 'created_at' || !request('sort_by') ? 'selected' : '' }}>Ngày tạo</option>
                            <option value="barber_name" {{ request('sort_by') === 'barber_name' ? 'selected' : '' }}>Tên thợ cắt tóc</option>
                            <option value="day_of_week" {{ request('sort_by') === 'day_of_week' ? 'selected' : '' }}>Ngày trong tuần</option>
                            <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Trạng thái</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="sort_order" class="form-label">Thứ tự</label>
                        <select name="sort_order" id="sort_order" class="form-select">
                            <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Tăng dần</option>
                            <option value="desc" {{ request('sort_order') === 'desc' || !request('sort_order') ? 'selected' : '' }}>Giảm dần</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="per_page" class="form-label">Hiển thị</label>
                        <select name="per_page" id="per_page" class="form-select">
                            <option value="10" {{ request('per_page') == 10 || !request('per_page') ? 'selected' : '' }}>10 mục</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 mục</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 mục</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 mục</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <div class="d-grid gap-2 w-100">
                            <a href="{{ route('admin.schedule-requests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-sync-alt"></i> Đặt lại bộ lọc
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách yêu cầu -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách yêu cầu thay đổi lịch làm việc</h6>
            <div>
                <button type="button" class="btn btn-danger" id="bulkDeleteBtn" disabled>
                    <i class="fas fa-trash"></i> Xóa đã chọn
                </button>
            </div>
        </div>
        <div class="card-body">
            <form id="bulkActionForm" action="{{ route('admin.schedule-requests.bulk-delete') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th><span class="column-title">ID</span></th>
                                <th><span class="column-title">Thợ cắt<br>tóc</span></th>
                                <th><span class="column-title">Ngày trong<br>tuần</span></th>
                                <th><span class="column-title">Thời gian</span></th>
                                <th><span class="column-title">Ngày nghỉ</span></th>
                                <th><span class="column-title">Lý do</span></th>
                                <th><span class="column-title">Trạng thái</span></th>
                                <th><span class="column-title">Ngày tạo</span></th>
                                <th class="text-center bg-light action-header"><span class="column-title">Thao tác</span></th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($requests as $request)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input request-checkbox" type="checkbox" name="ids[]" value="{{ $request->id }}">
                                    </div>
                                </td>
                                <td>{{ $request->id }}</td>
                                <td>{{ $request->barber->user->name }}</td>
                                <td>{{ $request->day_name }}</td>
                                <td>
                                    @if($request->is_day_off)
                                        <span class="text-muted">-</span>
                                    @else
                                        <span class="time-display">{{ substr($request->start_time, 0, 5) }} - {{ substr($request->end_time, 0, 5) }}</span>
                                    @endif
                                </td>
                                <td class="text-center day-off-column">
                                    @if($request->is_day_off)
                                        <span class="badge bg-danger text-white">Ngày nghỉ</span>
                                    @else
                                        <span class="badge bg-success text-white">Ngày làm việc</span>
                                    @endif
                                </td>
                                <td class="reason-tooltip">
                                    <span class="reason-text">{{ $request->reason }}</span>
                                    <span class="tooltip-text">{{ $request->reason }}</span>
                                </td>
                                <td class="text-center status-column">
                                    @if($request->status == 'pending')
                                        <span class="badge bg-warning text-dark">Đang chờ</span>
                                    @elseif($request->status == 'approved')
                                        <span class="badge bg-success text-white">Đã phê duyệt</span>
                                    @elseif($request->status == 'rejected')
                                        <span class="badge bg-danger text-white">Đã từ chối</span>
                                    @endif
                                </td>
                                <td class="date-column">{{ $request->created_at->format('d/m/Y') }}</td>
                                <td class="bg-light action-column">
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.schedule-requests.show', $request->id) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($request->status == 'pending')
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal{{ $request->id }}" title="Phê duyệt">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}" title="Từ chối">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $request->id }}" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <!-- Nút hiển thị trên mobile -->
                                    <div class="d-block d-md-none mt-2">
                                        <div class="btn-group btn-group-sm w-100" role="group">
                                            <a href="{{ route('admin.schedule-requests.show', $request->id) }}" class="btn btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($request->status == 'pending')
                                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $request->id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-danger delete-btn" data-id="{{ $request->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
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
                                <td colspan="10" class="text-center">Không có yêu cầu thay đổi lịch làm việc nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <div class="d-flex justify-content-end">
                {{ $requests->links() }}
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa yêu cầu thay đổi lịch làm việc này?</p>
                <p class="text-danger"><strong>Lưu ý:</strong> Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" action="" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xóa hàng loạt -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkDeleteModalLabel">Xác nhận xóa hàng loạt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa <span id="selectedCount">0</span> yêu cầu thay đổi lịch làm việc đã chọn?</p>
                <p class="text-danger"><strong>Lưu ý:</strong> Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmBulkDelete">Xóa</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal hiển thị lý do đầy đủ -->
<div class="modal fade" id="reasonModal" tabindex="-1" aria-labelledby="reasonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reasonModalLabel">Chi tiết lý do</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="p-3 bg-light rounded">
                    <p id="fullReason" class="mb-0" style="white-space: pre-wrap;"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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

        // Tự động submit form khi thay đổi các tùy chọn sắp xếp
        const sortByElement = document.getElementById('sort_by');
        const sortOrderElement = document.getElementById('sort_order');
        const perPageElement = document.getElementById('per_page');

        if (sortByElement) {
            sortByElement.addEventListener('change', function() {
                this.form.submit();
            });
        }

        if (sortOrderElement) {
            sortOrderElement.addEventListener('change', function() {
                this.form.submit();
            });
        }

        if (perPageElement) {
            perPageElement.addEventListener('change', function() {
                this.form.submit();
            });
        }

        // Xử lý xóa một yêu cầu
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteForm = document.getElementById('deleteForm');
        const deleteModalElement = document.getElementById('deleteModal');

        if (deleteButtons.length > 0 && deleteForm && deleteModalElement) {
            const deleteModal = new bootstrap.Modal(deleteModalElement);

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    if (id) {
                        deleteForm.action = "{{ route('admin.schedule-requests.destroy', ':id') }}".replace(':id', id);
                        deleteModal.show();
                    }
                });
            });
        }

        // Xử lý chọn tất cả
        const selectAllCheckbox = document.getElementById('selectAll');
        const requestCheckboxes = document.querySelectorAll('.request-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkDeleteModalElement = document.getElementById('bulkDeleteModal');
        const selectedCountSpan = document.getElementById('selectedCount');
        const bulkActionForm = document.getElementById('bulkActionForm');
        const confirmBulkDeleteBtn = document.getElementById('confirmBulkDelete');

        // Kiểm tra xem các phần tử có tồn tại không
        if (selectAllCheckbox && requestCheckboxes.length > 0 && bulkDeleteBtn &&
            bulkDeleteModalElement && selectedCountSpan && bulkActionForm && confirmBulkDeleteBtn) {

            const bulkDeleteModal = new bootstrap.Modal(bulkDeleteModalElement);

            // Hàm cập nhật trạng thái nút xóa hàng loạt
            function updateBulkDeleteButton() {
                const checkedCount = document.querySelectorAll('.request-checkbox:checked').length;
                bulkDeleteBtn.disabled = checkedCount === 0;
                selectedCountSpan.textContent = checkedCount;
            }

            // Xử lý sự kiện chọn tất cả
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                requestCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateBulkDeleteButton();
            });

            // Xử lý sự kiện chọn từng checkbox
            requestCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateBulkDeleteButton();

                    // Cập nhật trạng thái của checkbox "Chọn tất cả"
                    const allChecked = Array.from(requestCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(requestCheckboxes).some(cb => cb.checked);

                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                });
            });

            // Xử lý sự kiện khi nhấn nút xóa hàng loạt
            bulkDeleteBtn.addEventListener('click', function() {
                if (!this.disabled) {
                    bulkDeleteModal.show();
                }
            });

            // Xử lý sự kiện khi xác nhận xóa hàng loạt
            confirmBulkDeleteBtn.addEventListener('click', function() {
                if (document.querySelectorAll('.request-checkbox:checked').length > 0) {
                    bulkActionForm.submit();
                } else {
                    bulkDeleteModal.hide();
                }
            });

            // Khởi tạo trạng thái ban đầu
            updateBulkDeleteButton();
        }

        // Xử lý hiển thị lý do đầy đủ khi click
        const reasonTexts = document.querySelectorAll('.reason-text');
        if (reasonTexts.length > 0) {
            reasonTexts.forEach(text => {
                text.addEventListener('click', function() {
                    // Tạo modal hiển thị lý do đầy đủ
                    const reason = this.textContent;
                    const modal = new bootstrap.Modal(document.getElementById('reasonModal'));
                    document.getElementById('fullReason').textContent = reason;
                    modal.show();
                });
            });
        }
    });
</script>
@endsection
