@extends('layouts.frontend')

@section('title', 'Hồ sơ của tôi')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ get_user_avatar($user, 'large') }}" alt="{{ $user->name }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <h5 class="card-title">{{ $user->name }}</h5>
                <p class="text-muted">Thành viên từ {{ $user->created_at->format('d/m/Y') }}</p>
                <a href="{{ route('profile.edit') }}" class="btn mt-2" style="background-color: #9E8A78; color: white;">Chỉnh sửa hồ sơ</a>
            </div>
        </div>

        <div class="list-group mb-4">
            <a href="{{ route('profile.index') }}" class="list-group-item list-group-item-action active">
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
            <div class="card-header text-white" style="background-color: #9E8A78;">
                <h5 class="card-title mb-0">Thông tin cá nhân</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold">Họ và tên:</h6>
                        <p>{{ $user->name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold">Email:</h6>
                        <p>{{ $user->email }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold">Số điện thoại:</h6>
                        <p>{{ $user->phone ?? 'Chưa cập nhật' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold">Địa chỉ:</h6>
                        <p>{{ $user->address ?? 'Chưa cập nhật' }}</p>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Đổi mật khẩu</h5>
                <form action="{{ route('profile.change-password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                        @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>

                    <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection