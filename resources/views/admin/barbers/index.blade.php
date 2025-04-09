@extends('layouts.admin')

@section('title', 'Quản lý thợ cắt tóc')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý thợ cắt tóc</h1>
        <a href="{{ route('admin.barbers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm thợ cắt tóc mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách thợ cắt tóc</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ảnh</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Kinh nghiệm</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barbers as $barber)
                            <tr>
                                <td>{{ $barber->id }}</td>
                                <td>
                                    @if($barber->avatar)
                                        <img src="{{ asset('storage/' . $barber->avatar) }}" alt="{{ $barber->name }}" width="50" height="50" class="rounded-circle">
                                    @else
                                        <img src="{{ asset('images/default-avatar.jpg') }}" alt="{{ $barber->name }}" width="50" height="50" class="rounded-circle">
                                    @endif
                                </td>
                                <td>{{ $barber->name }}</td>
                                <td>{{ $barber->email }}</td>
                                <td>{{ $barber->phone }}</td>
                                <td>{{ $barber->barber->experience ?? 0 }} năm</td>
                                <td>
                                    @if($barber->status)
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-danger">Không hoạt động</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.barbers.show', $barber->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.barbers.edit', $barber->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.barbers.destroy', $barber->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa thợ cắt tóc này?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $barbers->links() }}
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