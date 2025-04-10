@extends('layouts.admin')

@section('title', 'Quản lý liên hệ')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý liên hệ</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách liên hệ</h6>
            <div class="d-flex">
                <div class="btn-group mr-3" role="group">
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm {{ $status === null ? 'btn-primary' : 'btn-outline-primary' }}">Tất cả</a>
                    <a href="{{ route('admin.contacts.index', ['status' => 0]) }}" class="btn btn-sm {{ $status === '0' ? 'btn-primary' : 'btn-outline-primary' }}">Chưa đọc</a>
                    <a href="{{ route('admin.contacts.index', ['status' => 1]) }}" class="btn btn-sm {{ $status === '1' ? 'btn-primary' : 'btn-outline-primary' }}">Đã đọc</a>
                </div>
                <form action="{{ route('admin.contacts.index') }}" method="GET" class="form-inline">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" name="search" placeholder="Tìm kiếm..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-primary" type="submit">
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
                <div class="mb-3">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Hành động <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu">
                            <button type="submit" name="action" value="mark_read" class="dropdown-item">Đánh dấu đã đọc</button>
                            <button type="submit" name="action" value="mark_unread" class="dropdown-item">Đánh dấu chưa đọc</button>
                            <div class="dropdown-divider"></div>
                            <button type="submit" name="action" value="delete" class="dropdown-item text-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa các liên hệ đã chọn?')">Xóa đã chọn</button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th width="5%">ID</th>
                                <th width="15%">Người gửi</th>
                                <th width="15%">Email</th>
                                <th width="15%">Tiêu đề</th>
                                <th width="10%">Trạng thái</th>
                                <th width="15%">Ngày gửi</th>
                                <th width="20%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contacts as $contact)
                                <tr class="{{ $contact->status ? '' : 'font-weight-bold' }}">
                                    <td>
                                        <input type="checkbox" name="contact_ids[]" value="{{ $contact->id }}" class="contact-checkbox">
                                    </td>
                                    <td>{{ $contact->id }}</td>
                                    <td>{{ $contact->name }}</td>
                                    <td>{{ $contact->email }}</td>
                                    <td>{{ $contact->subject }}</td>
                                    <td>
                                        @if($contact->status)
                                            <span class="badge badge-success">Đã đọc</span>
                                        @else
                                            <span class="badge badge-warning">Chưa đọc</span>
                                        @endif
                                    </td>
                                    <td>{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.contacts.show', $contact->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        
                                        @if($contact->status)
                                            <form action="{{ route('admin.contacts.markAsUnread', $contact->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.contacts.markAsRead', $contact->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-envelope-open"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa liên hệ này?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Không có liên hệ nào</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            
            <div class="d-flex justify-content-end">
                {{ $contacts->links() }}
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