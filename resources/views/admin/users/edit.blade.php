@extends('layouts.admin')

@section('title', 'Chỉnh sửa người dùng')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa người dùng</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin người dùng</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu <small class="text-muted">(Để trống nếu không muốn thay đổi)</small></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="role" class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Khách hàng</option>
                                <option value="barber" {{ old('role', $user->role) == 'barber' ? 'selected' : '' }}>Thợ cắt tóc</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $user->address) }}">
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="avatar" class="form-label d-block">Ảnh đại diện hiện tại</label>
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" width="100" class="img-thumbnail mb-2">
                    @else
                        <div class="text-muted">Không có ảnh</div>
                    @endif
                    <input type="file" class="form-control mt-2 @error('avatar') is-invalid @enderror" id="avatar" name="avatar">
                    <small class="text-muted">Tải lên ảnh mới nếu muốn thay đổi</small>
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Thông tin thợ cắt tóc (hiển thị/ẩn dựa vào vai trò) -->
                <div id="barber-info" class="border rounded p-3 mb-3" style="display: {{ $user->role === 'barber' ? 'block' : 'none' }};">
                    <h5 class="mb-3">Thông tin thợ cắt tóc</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="experience" class="form-label">Kinh nghiệm (năm)</label>
                                <input type="number" class="form-control @error('experience') is-invalid @enderror" id="experience" name="experience" value="{{ old('experience', $user->barber->experience ?? '') }}">
                                @error('experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="specialties" class="form-label">Chuyên môn</label>
                                <input type="text" class="form-control @error('specialties') is-invalid @enderror" id="specialties" name="specialties" value="{{ old('specialties', $user->barber->specialties ?? '') }}">
                                @error('specialties')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bio" class="form-label">Tiểu sử</label>
                        <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3">{{ old('bio', $user->barber->bio ?? '') }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                        <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật thông tin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Hiển thị/Ẩn thông tin thợ cắt tóc dựa vào vai trò
        $('#role').change(function() {
            if ($(this).val() === 'barber') {
                $('#barber-info').slideDown();
            } else {
                $('#barber-info').slideUp();
            }
        });
    });
</script>
@endsection 