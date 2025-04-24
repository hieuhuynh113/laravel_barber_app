@extends('layouts.frontend')

@section('title', 'Xác nhận thanh toán')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Xác nhận thanh toán</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h4>Lịch hẹn của bạn đã được đặt thành công!</h4>
                            <p class="text-muted">Mã đặt lịch: <strong>{{ $appointment->booking_code }}</strong></p>
                        </div>

                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-info-circle fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading">Vui lòng hoàn tất thanh toán</h5>
                                    <p>Để xác nhận lịch hẹn, vui lòng chuyển khoản trong vòng 24 giờ. Lịch hẹn sẽ được xác nhận sau khi chúng tôi nhận được thanh toán của bạn.</p>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Thông tin lịch hẹn</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Khách hàng:</strong> {{ $appointment->customer_name }}</p>
                                        <p><strong>Email:</strong> {{ $appointment->email }}</p>
                                        <p><strong>Số điện thoại:</strong> {{ $appointment->phone }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Ngày hẹn:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</p>
                                        <p><strong>Giờ hẹn:</strong> {{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
                                        <p><strong>Thợ cắt tóc:</strong> {{ $appointment->barber->user->name }}</p>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <h6>Dịch vụ đã chọn:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Dịch vụ</th>
                                                    <th class="text-end">Giá</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $totalPrice = 0; @endphp
                                                @foreach($appointment->services as $service)
                                                    <tr>
                                                        <td>{{ $service->name }}</td>
                                                        <td class="text-end">{{ number_format($service->pivot->price) }} VNĐ</td>
                                                    </tr>
                                                    @php $totalPrice += $service->pivot->price; @endphp
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Tổng cộng</th>
                                                    <th class="text-end">{{ number_format($totalPrice) }} VNĐ</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Thông tin chuyển khoản</h5>
                            </div>
                            <div class="card-body">
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
                                                        <span class="fw-bold" id="transferContent">{{ $appointment->booking_code }}</span>
                                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('transferContent')">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Gửi biên lai chuyển khoản</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('appointment.upload-receipt', $appointment->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="receipt" class="form-label">Tải lên biên lai chuyển khoản</label>
                                        <input class="form-control" type="file" id="receipt" name="receipt" accept="image/*">
                                        <div class="form-text">Chấp nhận các định dạng ảnh: JPG, PNG, JPEG</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Ghi chú (nếu có)</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Gửi biên lai</button>
                                </form>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6>Các bước tiếp theo:</h6>
                            <ol>
                                <li>Chuyển khoản theo thông tin bên trên</li>
                                <li>Tải lên biên lai chuyển khoản hoặc gửi qua email <a href="mailto:hieu.ht.63cntt@ntu.edu.vn">hieu.ht.63cntt@ntu.edu.vn</a></li>
                                <li>Chúng tôi sẽ xác nhận thanh toán và gửi email xác nhận cho bạn</li>
                                <li>Đến tiệm theo lịch hẹn đã đặt</li>
                            </ol>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">Về trang chủ</a>
                            <a href="{{ route('profile.appointments') }}" class="btn btn-primary">Xem lịch hẹn của tôi</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
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
