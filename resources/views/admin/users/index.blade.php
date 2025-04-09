@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

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

    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $role === 'customer' ? 'active' : '' }}" href="{{ route('admin.users.index', ['role' => 'customer']) }}">
                <i class="fas fa-users mr-1"></i> Khách hàng
                <span class="badge badge-pill badge-info">{{ \App\Models\User::where('role', 'customer')->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $role === 'barber' ? 'active' : '' }}" href="{{ route('admin.users.index', ['role' => 'barber']) }}">
                <i class="fas fa-cut mr-1"></i> Thợ cắt tóc
                <span class="badge badge-pill badge-info">{{ \App\Models\User::where('role', 'barber')->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $role === 'admin' ? 'active' : '' }}" href="{{ route('admin.users.index', ['role' => 'admin']) }}">
                <i class="fas fa-user-shield mr-1"></i> Quản trị viên
                <span class="badge badge-pill badge-info">{{ \App\Models\User::where('role', 'admin')->count() }}</span>
            </a>
        </li>
    </ul>

    <!-- Search form -->
    <form action="{{ route('admin.users.index') }}" method="GET" class="mb-4">
        <input type="hidden" name="role" value="{{ $role }}">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên, email hoặc số điện thoại" value="{{ $search ?? '' }}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </div>
        </div>
    </form>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách người dùng</h6>
        </div>
        <div class="card-body">
            <!-- Users table -->
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 60px">ID</th>
                            <th style="width: 80px">Ảnh</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Trạng thái</th>
                            <th style="width: 180px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" 
                                             alt="{{ $user->name }}" 
                                             class="rounded-circle" 
                                             width="50" height="50">
                                    @else
                                        <img src="{{ asset('images/default-avatar.jpg') }}" 
                                             alt="{{ $user->name }}" 
                                             class="rounded-circle" 
                                             width="50" height="50">
                                    @endif
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->status ? 'success' : 'danger' }}">
                                        {{ $user->status ? 'Hoạt động' : 'Tạm khóa' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
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
                                <td colspan="7" class="text-center">Không có người dùng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $users->appends(['role' => $role, 'search' => $search ?? ''])->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            paging: false,
            searching: true,
            ordering: true,
            info: false,
        });
    });
</script>
@endsection 