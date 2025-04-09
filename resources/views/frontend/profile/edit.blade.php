@extends('layouts.frontend')

@section('title', 'Chỉnh sửa hồ sơ')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ $user->avatar ? asset($user->avatar) : asset('images/avatar-placeholder.jpg') }}" alt="{{ $user->name }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <h5 class="card-title">{{ $user->name }}</h5>
                <p class="text-muted">Thành viên từ {{ $user->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
        
        <div class="list-group mb-4">
            <a href="{{ route('profile.index') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-user me-2"></i> Hồ sơ của tôi
            </a>
            <a href="{{ route('profile.appointments') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-calendar me-2"></i> Lịch hẹn của tôi
            </a>
            <a href="{{ route('profile.reviews') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-star me-2"></i> Đánh giá của tôi
            </a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Chỉnh sửa hồ sơ</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Họ và tên</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="{{ $user->email }}" disabled>
                        <small class="text-muted">Email không thể thay đổi.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Ảnh đại diện</label>
                        <input class="form-control @error('avatar') is-invalid @enderror" type="file" id="avatar" name="avatar">
                        <small class="text-muted">Chọn ảnh có kích thước tối đa 2MB (định dạng: jpg, jpeg, png, gif).</small>
                        @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary me-2">Lưu thay đổi</button>
                        <a href="{{ route('profile.index') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 