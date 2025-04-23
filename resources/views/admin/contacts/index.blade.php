@extends('layouts.admin')

@section('title', 'Quản lý liên hệ')

@section('styles')
<style>
    /* Bảng liên hệ */
    .contacts-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #e3e6f0;
    }

    .contacts-table th {
        background-color: #f8f9fc;
        font-weight: 600;
        text-align: left;
        padding: 12px 15px;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #4e73df;
        border-bottom: 1px solid #e3e6f0;
        border-right: 1px solid #e3e6f0;
    }

    .contacts-table th:last-child {
        border-right: none;
    }

    .contacts-table td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e3e6f0;
        border-right: 1px solid #e3e6f0;
        font-size: 0.9rem;
    }

    .contacts-table td:last-child {
        border-right: none;
    }

    .contacts-table tr:hover {
        background-color: #f8f9fc;
    }

    .contacts-table tr.unread {
        font-weight: bold;
        background-color: #f8f9fc;
    }

    /* Cột trong bảng liên hệ */
    .col-checkbox {
        width: 40px;
        text-align: center;
    }

    .col-id {
        width: 60px;
        text-align: center;
    }

    .col-name {
        width: 15%;
    }

    .col-email {
        width: 20%;
    }

    .col-subject {
        width: 25%;
    }

    .col-status {
        width: 100px;
        text-align: center;
    }

    .col-date {
        width: 120px;
        text-align: center;
    }

    .col-actions {
        width: 150px;
        text-align: center;
    }

    /* Badge trạng thái */
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-align: center;
        min-width: 80px;
    }

    /* Nút thao tác */
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 5px;
    }

    .action-buttons .btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    .action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }

    .action-buttons form {
        margin: 0;
    }

    /* Bộ lọc */
    .filter-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-buttons .btn-group .btn {
        border-radius: 4px;
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        transition: all 0.2s ease;
    }

    .filter-buttons .btn-group .btn:not(:last-child) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .filter-buttons .btn-group .btn:not(:first-child) {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .filter-buttons .btn-group .btn.active {
        font-weight: 600;
    }

    /* Checkbox */
    .custom-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý liên hệ</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách liên hệ</h6>
            <div class="filter-buttons">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.contacts.index') }}" class="btn {{ $status === null ? 'btn-primary' : 'btn-outline-primary' }}">Tất cả</a>
                    <a href="{{ route('admin.contacts.index', ['status' => 0]) }}" class="btn {{ $status === '0' ? 'btn-primary' : 'btn-outline-primary' }}">Chưa đọc</a>
                    <a href="{{ route('admin.contacts.index', ['status' => 1]) }}" class="btn {{ $status === '1' ? 'btn-primary' : 'btn-outline-primary' }}">Đã đọc</a>
                </div>
                <form action="{{ route('admin.contacts.index') }}" method="GET">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Tìm kiếm..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            <form id="bulk-action-form" action="{{ route('admin.contacts.bulkAction') }}" method="POST">
                @csrf
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Hành động <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu">
                            <button type="submit" name="action" value="mark_read" class="dropdown-item">Đánh dấu đã đọc</button>
                            <button type="submit" name="action" value="mark_unread" class="dropdown-item">Đánh dấu chưa đọc</button>
                            <div class="dropdown-divider"></div>
                            <button type="submit" name="action" value="delete" class="dropdown-item text-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa các liên hệ đã chọn?')">Xóa đã chọn</button>
                        </div>
                    </div>
                    <span class="text-muted">Tổng số: {{ $contacts->total() }} liên hệ</span>
                </div>

                <div class="table-responsive">
                    <table class="contacts-table">
                        <thead>
                            <tr>
                                <th class="col-checkbox">
                                    <input type="checkbox" id="select-all" class="custom-checkbox">
                                </th>
                                <th class="col-id">ID</th>
                                <th class="col-name">Người gửi</th>
                                <th class="col-email">Email</th>
                                <th class="col-subject">Tiêu đề</th>
                                <th class="col-status">Trạng thái</th>
                                <th class="col-date">Ngày gửi</th>
                                <th class="col-actions">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contacts as $contact)
                                <tr class="{{ $contact->status ? '' : 'unread' }}">
                                    <td class="col-checkbox">
                                        <input type="checkbox" name="contact_ids[]" value="{{ $contact->id }}" class="contact-checkbox custom-checkbox">
                                    </td>
                                    <td class="col-id">{{ $contact->id }}</td>
                                    <td class="col-name">{{ $contact->name }}</td>
                                    <td class="col-email">{{ $contact->email }}</td>
                                    <td class="col-subject">{{ $contact->subject }}</td>
                                    <td class="col-status">
                                        <span class="status-badge bg-{{ $contact->status ? 'success' : 'warning' }}">
                                            {{ $contact->status ? 'Đã đọc' : 'Chưa đọc' }}
                                        </span>
                                    </td>
                                    <td class="col-date">{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="col-actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.contacts.show', $contact->id) }}" class="btn btn-info btn-sm" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($contact->status)
                                                <form action="{{ route('admin.contacts.markAsUnread', $contact->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-warning btn-sm" title="Đánh dấu chưa đọc">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.contacts.markAsRead', $contact->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" title="Đánh dấu đã đọc">
                                                        <i class="fas fa-envelope-open"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa liên hệ này?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">Không có liên hệ nào</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <div class="d-flex justify-content-center mt-3">
                {{ $contacts->appends(request()->query())->links('admin.partials.pagination') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Xử lý checkbox chọn tất cả
        $('#select-all').click(function() {
            $('.contact-checkbox').prop('checked', $(this).prop('checked'));
        });

        $('.contact-checkbox').click(function() {
            if (!$(this).prop('checked')) {
                $('#select-all').prop('checked', false);
            } else {
                if ($('.contact-checkbox:checked').length === $('.contact-checkbox').length) {
                    $('#select-all').prop('checked', true);
                }
            }
        });

        // Kiểm tra xem có checkbox nào được chọn không khi thực hiện hành động hàng loạt
        $('#bulk-action-form').submit(function(e) {
            if ($('.contact-checkbox:checked').length === 0) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất một liên hệ.');
            }
        });
    });
</script>
@endsection