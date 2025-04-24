@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('styles')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý người dùng</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Thêm người dùng mới
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách người dùng</h6>
            <div>
                <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex">
                    <select name="role" class="form-select me-2" onchange="this.form.submit()">
                        <option value="">Tất cả vai trò</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                        <option value="barber" {{ request('role') == 'barber' ? 'selected' : '' }}>Thợ cắt tóc</option>
                        <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Khách hàng</option>
                    </select>
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary ms-2">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0" id="usersTable">
                    <thead>
                        <tr>
                            <th class="id-cell">ID</th>
                            <th class="avatar-cell">Ảnh</th>
                            <th class="name-cell">Tên</th>
                            <th class="email-cell">Email</th>
                            <th class="role-cell">Vai trò</th>
                            <th class="status-cell">Trạng thái</th>
                            <th class="date-cell">Ngày tạo</th>
                            <th class="actions-cell">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="id-cell">{{ $user->id }}</td>
                            <td class="avatar-cell text-center">
                                <img src="{{ get_user_avatar($user, 'small') }}" alt="{{ $user->name }}" class="img-profile rounded-circle" width="40">
                            </td>
                            <td class="name-cell">{{ $user->name }}</td>
                            <td class="email-cell" title="{{ $user->email }}">{{ $user->email }}</td>
                            <td class="role-cell text-center">
                                @php
                                    $roleClass = '';
                                    $roleName = '';

                                    if ($user->role === 'admin') {
                                        $roleClass = 'bg-danger';
                                        $roleName = 'Quản trị viên';
                                    } elseif ($user->role === 'barber') {
                                        $roleClass = 'bg-info';
                                        $roleName = 'Thợ cắt tóc';
                                    } elseif ($user->role === 'customer') {
                                        $roleClass = 'bg-secondary';
                                        $roleName = 'Khách hàng';
                                    }
                                @endphp
                                <span class="badge {{ $roleClass }}">{{ $roleName }}</span>
                            </td>
                            <td class="status-cell text-center">
                                <span class="badge bg-{{ $user->status ? 'success' : 'danger' }}">
                                    {{ $user->status ? 'Hoạt động' : 'Bị khóa' }}
                                </span>
                            </td>
                            <td class="date-cell text-center">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="actions-cell text-center">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Không có người dùng nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $users->appends(request()->query())->links('admin.partials.pagination') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            "paging": false,
            "ordering": true,
            "info": false,
            "searching": true,
            "autoWidth": false,
            "responsive": true,
            "columnDefs": [
                { "width": "50px", "targets": 0, "className": "id-cell" },
                { "width": "100px", "targets": 1, "className": "avatar-cell text-center" },
                { "width": "150px", "targets": 2, "className": "name-cell" },
                { "width": "200px", "targets": 3, "className": "email-cell" },
                { "width": "120px", "targets": 4, "className": "role-cell text-center" },
                { "width": "100px", "targets": 5, "className": "status-cell text-center" },
                { "width": "100px", "targets": 6, "className": "date-cell text-center" },
                { "width": "120px", "targets": 7, "className": "actions-cell text-center" }
            ]
        });
    });
</script>
@endsection
