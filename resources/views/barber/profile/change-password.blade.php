@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/barber-dashboard.css') }}">
<style>
    .password-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }

    .password-card .card-header {
        background-color: #2c3e50;
        color: #fff;
        border-radius: 10px 10px 0 0;
        padding: 1rem 1.5rem;
    }

    .password-card .card-body {
        padding: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
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

    .password-tips {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 1.25rem;
        margin-top: 1.5rem;
        border-left: 4px solid #3498db;
    }

    .password-tips h6 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .password-tips ul {
        padding-left: 1.5rem;
        margin-bottom: 0;
    }

    .password-tips li {
        margin-bottom: 0.5rem;
        color: #34495e;
    }

    .password-tips li:last-child {
        margin-bottom: 0;
    }
</style>
@endsection

@section('content')
<div class="container dashboard-container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="dashboard-title">Đổi mật khẩu</h1>
                <a href="{{ route('barber.profile.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>

            <div class="password-card">
                <div class="card-header">
                    <h5 class="mb-0">Đổi mật khẩu</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('barber.profile.change-password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control password-validate @error('password') is-invalid @enderror" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control password-confirm" required data-password-field="password">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="password-tips">
                            <h6><i class="fas fa-lightbulb me-2"></i>Mẹo tạo mật khẩu an toàn</h6>
                            <ul>
                                <li>Sử dụng ít nhất 8 ký tự</li>
                                <li>Kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt</li>
                                <li>Không sử dụng thông tin cá nhân dễ đoán như ngày sinh, tên</li>
                                <li>Không sử dụng lại mật khẩu đã dùng trước đây</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('barber.profile.index') }}" class="btn btn-secondary action-btn me-2">Hủy</a>
                            <button type="submit" class="btn btn-primary action-btn">Đổi mật khẩu</button>
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
        // Hiển thị/ẩn mật khẩu
        $('.toggle-password').click(function() {
            var target = $(this).data('target');
            var input = $('#' + target);
            var icon = $(this).find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    });
</script>
@endsection
