@extends('layouts.frontend')

@section('title', 'Đặt lịch - Bước 5: Thanh toán')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header text-white" style="background-color: #9E8A78;">
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
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary text-white py-3">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin đặt lịch</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="customer-summary">
                                                <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-user-circle me-2"></i>Thông tin cá nhân</h6>
                                                <div class="customer-details">
                                                    <div class="d-flex mb-2">
                                                        <div class="icon-col text-primary" style="width: 24px;">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                        <div class="ms-3">
                                                            <div class="text-muted small">Họ tên</div>
                                                            <div class="fw-medium">{{ session('appointment_customer_name') }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex mb-2">
                                                        <div class="icon-col text-primary" style="width: 24px;">
                                                            <i class="fas fa-envelope"></i>
                                                        </div>
                                                        <div class="ms-3">
                                                            <div class="text-muted small">Email</div>
                                                            <div class="fw-medium">{{ session('appointment_customer_email') }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex mb-2">
                                                        <div class="icon-col text-primary" style="width: 24px;">
                                                            <i class="fas fa-phone-alt"></i>
                                                        </div>
                                                        <div class="ms-3">
                                                            <div class="text-muted small">Số điện thoại</div>
                                                            <div class="fw-medium">{{ session('appointment_customer_phone') }}</div>
                                                        </div>
                                                    </div>
                                                    @if(session('appointment_notes'))
                                                    <div class="d-flex">
                                                        <div class="icon-col text-primary" style="width: 24px;">
                                                            <i class="fas fa-sticky-note"></i>
                                                        </div>
                                                        <div class="ms-3">
                                                            <div class="text-muted small">Ghi chú</div>
                                                            <div class="fw-medium">{{ session('appointment_notes') }}</div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="appointment-summary">
                                                <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-calendar-check me-2"></i>Chi tiết cuộc hẹn</h6>
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="icon-wrapper bg-light rounded-circle p-2 me-3">
                                                        <i class="fas fa-calendar-day text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-muted small">Ngày</div>
                                                        <div class="fw-medium">{{ \Carbon\Carbon::parse(session('appointment_date'))->format('d/m/Y') }}</div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="icon-wrapper bg-light rounded-circle p-2 me-3">
                                                        <i class="fas fa-clock text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-muted small">Giờ</div>
                                                        <div class="fw-medium">{{ session('appointment_start_time') }} - {{ session('appointment_end_time') }}</div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-wrapper bg-light rounded-circle p-2 me-3">
                                                        <i class="fas fa-user-tie text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-muted small">Thợ cắt tóc</div>
                                                        <div class="fw-medium">{{ session('appointment_barber')->user->name }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="service-summary">
                                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-cut me-2"></i>Dịch vụ đã chọn</h6>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Dịch vụ</th>
                                                        <th class="text-center">Thời gian</th>
                                                        <th class="text-end">Giá</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $totalPrice = 0; $totalDuration = 0; @endphp
                                                    @foreach(session('appointment_services', []) as $service)
                                                        <tr>
                                                            <td>{{ $service->name }}</td>
                                                            <td class="text-center">{{ $service->duration }} phút</td>
                                                            <td class="text-end">{{ number_format($service->price) }} VNĐ</td>
                                                        </tr>
                                                        @php
                                                            $totalPrice += $service->price;
                                                            $totalDuration += $service->duration;
                                                        @endphp
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <th>Tổng cộng</th>
                                                        <th class="text-center">{{ $totalDuration }} phút</th>
                                                        <th class="text-end text-primary">{{ number_format($totalPrice) }} VNĐ</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form phương thức thanh toán -->
                        <h5 class="card-title mb-4">Bước 5: Chọn phương thức thanh toán</h5>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-circle me-2 fs-4"></i>
                                    <div>
                                        @if($errors->has('payment_method'))
                                            <strong>{{ $errors->first('payment_method') }}</strong>
                                        @elseif($errors->has('agree_terms'))
                                            <strong>{{ $errors->first('agree_terms') }}</strong>
                                        @else
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
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
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_bank" value="bank_transfer">
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
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i> Vui lòng chuyển khoản trong vòng 24 giờ sau khi đặt lịch. Lịch hẹn sẽ được xác nhận sau khi chúng tôi nhận được thanh toán của bạn.
                                        </div>

                                        <h6 class="mt-3 mb-3">Thông tin chuyển khoản:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <th style="width: 35%">Ngân hàng:</th>
                                                        <td>VCB - Vietcombank</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Số tài khoản:</th>
                                                        <td><span class="fw-bold">0559764554</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Chủ tài khoản:</th>
                                                        <td>HUYNH TRUNG HIEU</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Số tiền:</th>
                                                        <td class="fw-bold text-danger">{{ number_format($totalPrice) }} VNĐ</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Nội dung chuyển khoản:</th>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <span class="fw-bold" id="transferContent">{{ session('appointment_customer_name') }}_{{ date('dmY') }}</span>
                                                                <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('transferContent')">
                                                                    <i class="fas fa-copy"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-3">
                                            <h6>Sau khi chuyển khoản:</h6>
                                            <ol>
                                                <li>Chụp màn hình hoặc lưu biên lai chuyển khoản</li>
                                                <li>Gửi biên lai qua email <a href="mailto:hieu.ht.63cntt@ntu.edu.vn">hieu.ht.63cntt@ntu.edu.vn</a> hoặc số Zalo <strong>0559764554</strong></li>
                                                <li>Bạn sẽ nhận được email xác nhận sau khi chúng tôi xác nhận thanh toán</li>
                                            </ol>
                                        </div>
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
    /* Styling for selected info card */
    .selected-info .card-header {
        background-color: #9E8A78 !important;
    }

    .selected-info .text-primary {
        color: #9E8A78 !important;
    }

    .selected-info .icon-wrapper {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .selected-info .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(158, 138, 120, 0.05);
    }

    .selected-info .table-light {
        background-color: rgba(158, 138, 120, 0.1);
    }

    .customer-details .icon-col {
        color: #9E8A78;
    }

    /* Progress steps styling */
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

        // Kiểm tra xem nếu payment_bank đã được chọn, hiển thị chi tiết ngân hàng
        if (bankRadio.checked) {
            bankDetails.style.display = 'block';
        }
    });

    // Hàm sao chép nội dung vào clipboard
    function copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        const text = element.textContent;

        navigator.clipboard.writeText(text).then(function() {
            // Hiển thị thông báo đã sao chép
            const button = element.nextElementSibling;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.remove('btn-outline-primary');
            button.classList.add('btn-success');

            setTimeout(function() {
                button.innerHTML = originalHTML;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-primary');
            }, 2000);
        }).catch(function(err) {
            console.error('Không thể sao chép nội dung: ', err);
        });
    }
</script>
@endsection