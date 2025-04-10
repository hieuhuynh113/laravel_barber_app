@extends('layouts.frontend')

@section('title', 'Đặt lịch - Bước 5: Thanh toán')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Đặt lịch hẹn</h4>
                    </div>
                    <div class="card-body">
                        <!-- Thanh tiến trình đặt lịch -->
                        <div class="progress-steps mb-5">
                            <div class="d-flex justify-content-between">
                                <div class="step completed">
                                    <div class="step-circle">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text">Chọn dịch vụ</div>
                                </div>
                                <div class="step completed">
                                    <div class="step-circle">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text">Chọn thợ cắt tóc</div>
                                </div>
                                <div class="step completed">
                                    <div class="step-circle">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text">Chọn thời gian</div>
                                </div>
                                <div class="step completed">
                                    <div class="step-circle">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text">Thông tin cá nhân</div>
                                </div>
                                <div class="step active">
                                    <div class="step-circle">5</div>
                                    <div class="step-text">Thanh toán</div>
                                </div>
                                <div class="step">
                                    <div class="step-circle">6</div>
                                    <div class="step-text">Xác nhận</div>
                                </div>
                            </div>
                        </div>

                        <!-- Hiển thị tóm tắt thông tin đã chọn -->
                        <div class="selected-info mb-4">
                            <h6>Thông tin đặt lịch:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Thông tin cá nhân</h6>
                                            <p class="mb-1"><strong>Họ tên:</strong> {{ session('customer_name') }}</p>
                                            <p class="mb-1"><strong>Email:</strong> {{ session('customer_email') }}</p>
                                            <p class="mb-1"><strong>Số điện thoại:</strong> {{ session('customer_phone') }}</p>
                                            @if(session('customer_address'))
                                                <p class="mb-1"><strong>Địa chỉ:</strong> {{ session('customer_address') }}</p>
                                            @endif
                                            @if(session('customer_notes'))
                                                <p class="mb-0"><strong>Ghi chú:</strong> {{ session('customer_notes') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Chi tiết cuộc hẹn</h6>
                                            <p class="mb-1">
                                                <strong>Ngày:</strong> {{ \Carbon\Carbon::parse(session('appointment_date'))->format('d/m/Y') }}
                                            </p>
                                            <p class="mb-1"><strong>Giờ:</strong> {{ session('appointment_time') }}</p>
                                            <p class="mb-1">
                                                <strong>Thợ cắt tóc:</strong> {{ session('appointment_barber')->user->name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Dịch vụ đã chọn</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Dịch vụ</th>
                                                    <th>Thời gian</th>
                                                    <th class="text-end">Giá</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $totalPrice = 0; $totalDuration = 0; @endphp
                                                @foreach(session('appointment_services', []) as $service)
                                                    <tr>
                                                        <td>{{ $service->name }}</td>
                                                        <td>{{ $service->duration }} phút</td>
                                                        <td class="text-end">{{ number_format($service->price) }} VNĐ</td>
                                                    </tr>
                                                    @php 
                                                        $totalPrice += $service->price; 
                                                        $totalDuration += $service->duration;
                                                    @endphp
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Tổng</th>
                                                    <th>{{ $totalDuration }} phút</th>
                                                    <th class="text-end">{{ number_format($totalPrice) }} VNĐ</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form phương thức thanh toán -->
                        <h5 class="card-title mb-4">Bước 5: Chọn phương thức thanh toán</h5>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('appointment.post.step5') }}" method="POST">
                            @csrf
                            
                            <div class="payment-methods mb-4">
                                <div class="form-check mb-3 payment-option">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="cash" checked>
                                    <label class="form-check-label d-flex align-items-center" for="payment_cash">
                                        <span class="payment-icon me-3 bg-light rounded p-2">
                                            <i class="fas fa-money-bill-wave text-success fs-4"></i>
                                        </span>
                                        <div>
                                            <div class="fw-bold">Thanh toán tại cửa hàng</div>
                                            <div class="text-muted small">Thanh toán bằng tiền mặt sau khi sử dụng dịch vụ</div>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3 payment-option">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_momo" value="momo">
                                    <label class="form-check-label d-flex align-items-center" for="payment_momo">
                                        <span class="payment-icon me-3 rounded p-2" style="background-color: #f5f5f5;">
                                            <img src="{{ asset('images/momo-logo.png') }}" alt="MoMo" width="40" height="40">
                                        </span>
                                        <div>
                                            <div class="fw-bold">Thanh toán qua MoMo</div>
                                            <div class="text-muted small">Thanh toán trực tuyến qua ví điện tử MoMo</div>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3 payment-option">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_vnpay" value="vnpay">
                                    <label class="form-check-label d-flex align-items-center" for="payment_vnpay">
                                        <span class="payment-icon me-3 rounded p-2" style="background-color: #f5f5f5;">
                                            <img src="{{ asset('images/vnpay-logo.png') }}" alt="VNPay" width="40" height="40">
                                        </span>
                                        <div>
                                            <div class="fw-bold">Thanh toán qua VNPay</div>
                                            <div class="text-muted small">Thanh toán trực tuyến qua cổng thanh toán VNPay</div>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="form-check payment-option">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_bank" value="bank">
                                    <label class="form-check-label d-flex align-items-center" for="payment_bank">
                                        <span class="payment-icon me-3 bg-light rounded p-2">
                                            <i class="fas fa-university text-primary fs-4"></i>
                                        </span>
                                        <div>
                                            <div class="fw-bold">Chuyển khoản ngân hàng</div>
                                            <div class="text-muted small">Chuyển khoản đến tài khoản ngân hàng của chúng tôi</div>
                                        </div>
                                    </label>
                                    
                                    <div class="bank-details mt-3 ms-4 p-3 border rounded" id="bank_details" style="display: none;">
                                        <p class="mb-2">Vui lòng chuyển khoản đến tài khoản sau:</p>
                                        <ul class="list-unstyled mb-0">
                                            <li><strong>Ngân hàng:</strong> Vietcombank</li>
                                            <li><strong>Số tài khoản:</strong> 1234567890</li>
                                            <li><strong>Chủ tài khoản:</strong> CÔNG TY TNHH BARBER SHOP</li>
                                            <li><strong>Nội dung:</strong> Thanh toan dich vu {{ session('customer_name') }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="agree_terms" id="agree_terms" value="1" required>
                                <label class="form-check-label" for="agree_terms">
                                    Tôi đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Điều khoản và Điều kiện</a> của Barber Shop
                                </label>
                            </div>
                            
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="{{ route('appointment.step4') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary">Hoàn tất đặt lịch <i class="fas fa-arrow-right"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Điều khoản -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Điều khoản và Điều kiện</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Đặt lịch và Hủy lịch</h6>
                <p>- Khách hàng cần đặt lịch trước ít nhất 24 giờ.</p>
                <p>- Trong trường hợp khách hàng cần hủy lịch, vui lòng thông báo trước ít nhất 2 giờ.</p>
                <p>- Nếu khách hàng không đến theo lịch hẹn mà không thông báo trước, chúng tôi có quyền từ chối phục vụ trong lần đặt lịch tiếp theo.</p>
                
                <h6>2. Thanh toán</h6>
                <p>- Khách hàng có thể thanh toán bằng tiền mặt tại cửa hàng sau khi sử dụng dịch vụ.</p>
                <p>- Đối với thanh toán trực tuyến, giao dịch sẽ được xử lý ngay lập tức.</p>
                <p>- Trong trường hợp hủy lịch sau khi đã thanh toán trực tuyến, khách hàng sẽ được hoàn tiền trong vòng 7 ngày làm việc.</p>
                
                <h6>3. Giá cả và Dịch vụ</h6>
                <p>- Giá dịch vụ đã bao gồm thuế VAT.</p>
                <p>- Chúng tôi có quyền thay đổi giá dịch vụ mà không cần thông báo trước.</p>
                <p>- Thời gian dịch vụ có thể thay đổi tùy thuộc vào yêu cầu cụ thể của khách hàng.</p>
                
                <h6>4. Quyền riêng tư</h6>
                <p>- Thông tin cá nhân của khách hàng sẽ được bảo mật và chỉ sử dụng cho mục đích đặt lịch và liên hệ.</p>
                <p>- Chúng tôi không chia sẻ thông tin khách hàng cho bên thứ ba nếu không có sự đồng ý.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đồng ý</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .progress-steps {
        position: relative;
    }
    
    .progress-steps:before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #e9ecef;
        z-index: 0;
    }
    
    .step {
        text-align: center;
        z-index: 1;
        flex: 1;
        position: relative;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-weight: bold;
    }
    
    .step.active .step-circle {
        background-color: #0d6efd;
        color: white;
    }
    
    .step.completed .step-circle {
        background-color: #28a745;
        color: white;
    }
    
    .step-text {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .step.active .step-text {
        color: #0d6efd;
        font-weight: bold;
    }
    
    .step.completed .step-text {
        color: #28a745;
    }
    
    .payment-option {
        padding: 15px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        margin-bottom: 15px !important;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .payment-option:hover {
        background-color: #f8f9fa;
    }
    
    .payment-option input:checked ~ label {
        font-weight: bold;
    }
    
    .form-check-input:checked ~ .form-check-label .payment-icon {
        border: 2px solid #0d6efd;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hiển thị chi tiết ngân hàng khi chọn phương thức chuyển khoản
        const bankRadio = document.getElementById('payment_bank');
        const bankDetails = document.getElementById('bank_details');
        
        bankRadio.addEventListener('change', function() {
            if (this.checked) {
                bankDetails.style.display = 'block';
            } else {
                bankDetails.style.display = 'none';
            }
        });
        
        // Ẩn chi tiết ngân hàng khi chọn phương thức khác
        const otherPaymentMethods = document.querySelectorAll('input[name="payment_method"]:not(#payment_bank)');
        otherPaymentMethods.forEach(function(method) {
            method.addEventListener('change', function() {
                bankDetails.style.display = 'none';
            });
        });
        
        // Làm cho toàn bộ khối payment-option có thể click để chọn radio
        const paymentOptions = document.querySelectorAll('.payment-option');
        paymentOptions.forEach(function(option) {
            option.addEventListener('click', function(e) {
                // Không trigger khi click vào bank details
                if (e.target.closest('#bank_details')) return;
                
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                
                // Trigger change event
                const event = new Event('change');
                radio.dispatchEvent(event);
            });
        });
    });
</script>
@endsection 