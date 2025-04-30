@extends('layouts.app')

@section('title', 'Chỉnh sửa thông tin cá nhân')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/barber-dashboard.css') }}">
<style>
    .profile-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    
    .profile-card .card-header {
        background-color: #2c3e50;
        color: #fff;
        border-radius: 10px 10px 0 0;
        padding: 1rem 1.5rem;
    }
    
    .profile-card .card-body {
        padding: 1.5rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .avatar-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        margin-bottom: 1rem;
    }
    
    .action-btn {
        padding: 0.75rem 1.5rem;
        border-radius: 5px;
        margin-right: 0.5rem;
        transition: all 0.3s;
        font-weight: 600;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

@section('content')
<div class="container dashboard-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="dashboard-title">Chỉnh sửa thông tin cá nhân</h1>
                <a href="{{ route('barber.profile.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
            
            <div class="profile-card">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin cá nhân</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('barber.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="mb-3">
                                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}" alt="{{ $user->name }}" class="avatar-preview" id="avatar-preview">
                                    <div class="mt-2">
                                        <label for="avatar" class="btn btn-outline-primary">
                                            <i class="fas fa-upload me-2"></i>Chọn ảnh
                                        </label>
                                        <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*">
                                    </div>
                                    @error('avatar')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Họ và tên</label>
                                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Số điện thoại</label>
                                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" required>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="experience" class="form-label">Kinh nghiệm (năm)</label>
                                            <input type="number" name="experience" id="experience" class="form-control @error('experience') is-invalid @enderror" value="{{ old('experience', $user->barber->experience) }}" min="0">
                                            @error('experience')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Địa chỉ</label>
                                    <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $user->address) }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="specialties" class="form-label">Chuyên môn</label>
                                    <input type="text" name="specialties" id="specialties" class="form-control @error('specialties') is-invalid @enderror" value="{{ old('specialties', $user->barber->specialty) }}">
                                    @error('specialties')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Mô tả</label>
                                    <textarea name="bio" id="bio" rows="4" class="form-control @error('bio') is-invalid @enderror">{{ old('bio', $user->barber->description) }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-3">
                            <a href="{{ route('barber.profile.index') }}" class="btn btn-secondary action-btn me-2">Hủy</a>
                            <button type="submit" class="btn btn-primary action-btn">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Xem trước ảnh đại diện
        $('#avatar').change(function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#avatar-preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endsection
