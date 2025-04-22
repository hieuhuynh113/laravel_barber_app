@extends('layouts.frontend')

@section('title', 'Chi tiết lịch hẹn #' . $appointment->id)

@section('content')
<div class="container py-5">
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
                <a href="{{ route('profile.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user me-2"></i> Hồ sơ của tôi
                </a>
                <a href="{{ route('profile.appointments') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-calendar me-2"></i> Lịch hẹn của tôi
                </a>
                <a href="{{ route('profile.reviews') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-star me-2"></i> Đánh giá của tôi
                </a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #9E8A78; color: white;">
                    <h5 class="mb-0">Chi tiết lịch hẹn #{{ $appointment->id }}</h5>
                    <a href="{{ route('profile.appointments') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-{{ $appointment->status == 'canceled' ? 'danger' : ($appointment->status == 'completed' ? 'success' : ($appointment->status == 'confirmed' ? 'info' : 'warning')) }} mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-{{ $appointment->status == 'canceled' ? 'times-circle' : ($appointment->status == 'completed' ? 'check-circle' : ($appointment->status == 'confirmed' ? 'info-circle' : 'clock')) }} fa-2x me-3"></i>
                            <div>
                                <h5 class="alert-heading mb-1">
                                    Trạng thái: 
                                    @if($appointment->status == 'pending')
                                        Chờ xác nhận
                                    @elseif($appointment->status == 'confirmed')
                                        Đã xác nhận
                                    @elseif($appointment->status == 'completed')
                                        Hoàn thành
                                    @elseif($appointment->status == 'canceled')
                                        Đã hủy
                                    @endif
                                </h5>
                                <p class="mb-0">
                                    @if($appointment->status == 'pending')
                                        Lịch hẹn của bạn đang chờ xác nhận từ nhân viên.
                                    @elseif($appointment->status == 'confirmed')
                                        Lịch hẹn của bạn đã được xác nhận. Vui lòng đến đúng giờ.
                                    @elseif($appointment->status == 'completed')
                                        Lịch hẹn của bạn đã hoàn thành. Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi.
                                    @elseif($appointment->status == 'canceled')
                                        Lịch hẹn này đã bị hủy.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h6 class="card-title border-bottom pb-2 mb-3">Thông tin cá nhân</h6>
                                    <p class="mb-2"><strong>Họ tên:</strong> {{ $appointment->customer_name }}</p>
                                    <p class="mb-2"><strong>Email:</strong> {{ $appointment->email }}</p>
                                    <p class="mb-2"><strong>Số điện thoại:</strong> {{ $appointment->phone }}</p>
                                    @if($appointment->notes)
                                        <p class="mb-0"><strong>Ghi chú:</strong> {{ $appointment->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h6 class="card-title border-bottom pb-2 mb-3">Thông tin cuộc hẹn</h6>
                                    <p class="mb-2">
                                        <strong>Ngày hẹn:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
                                    </p>
                                    <p class="mb-2"><strong>Giờ hẹn:</strong> {{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
                                    <p class="mb-2"><strong>Thợ cắt tóc:</strong> {{ $appointment->barber->user->name }}</p>
                                    <p class="mb-2">
                                        <strong>Mã đặt lịch:</strong> 
                                        <span class="badge bg-dark">{{ $appointment->booking_code }}</span>
                                    </p>
                                    <p class="mb-0">
                                        <strong>Ngày đặt:</strong> {{ $appointment->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="card-title border-bottom pb-2 mb-3">Dịch vụ đã chọn</h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>STT</th>
                                            <th>Tên dịch vụ</th>
                                            <th class="text-end">Giá</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @foreach($appointment->services as $index => $service)
                                            @php 
                                                $price = $service->pivot->price ?? $service->price;
                                                $total += $price;
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $service->name }}</td>
                                                <td class="text-end">{{ number_format($price) }} VNĐ</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <td colspan="2" class="text-end"><strong>Tổng cộng:</strong></td>
                                            <td class="text-end"><strong>{{ number_format($total) }} VNĐ</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="card-title border-bottom pb-2 mb-3">Thanh toán</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    @if($appointment->payment_status == 'paid')
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                    @else
                                        <i class="fas fa-clock text-warning fa-2x"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">
                                        @if($appointment->payment_status == 'paid')
                                            <span class="badge bg-success">Đã thanh toán</span>
                                        @else
                                            <span class="badge bg-warning">Chưa thanh toán</span>
                                        @endif
                                    </h6>
                                    <p class="mb-0 text-muted">
                                        Phương thức: 
                                        @if($appointment->payment_method == 'cash')
                                            <i class="fas fa-money-bill-wave text-success"></i> Tiền mặt
                                        @elseif($appointment->payment_method == 'bank_transfer')
                                            <i class="fas fa-university text-primary"></i> Chuyển khoản
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            @if($appointment->payment_method == 'bank_transfer' && $appointment->payment_status != 'paid')
                                <div class="alert alert-info">
                                    <p class="mb-0">Vui lòng chuyển khoản đến tài khoản sau và tải lên biên lai:</p>
                                    <ul class="mb-2">
                                        <li>Ngân hàng: VCB</li>
                                        <li>Số tài khoản: 1234567890</li>
                                        <li>Chủ tài khoản: BARBER SHOP</li>
                                        <li>Nội dung: {{ $appointment->booking_code }}</li>
                                    </ul>
                                    <a href="{{ route('appointment.payment.confirmation', $appointment->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-upload me-1"></i> Tải lên biên lai
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('profile.appointments') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
                        
                        @if(in_array($appointment->status, ['pending', 'confirmed']))
                            <button class="btn btn-danger" onclick="confirmCancel({{ $appointment->id }})">
                                <i class="fas fa-times-circle me-1"></i> Hủy lịch hẹn
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal xác nhận hủy lịch hẹn -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Xác nhận hủy lịch hẹn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn hủy lịch hẹn này không?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Lưu ý: Nếu bạn hủy lịch hẹn trong vòng 24 giờ trước thời gian hẹn, bạn có thể sẽ bị tính phí hủy.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <form id="cancelForm" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmCancel(id) {
        document.getElementById('cancelForm').action = "{{ url('appointment/cancel') }}/" + id;
        var cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
        cancelModal.show();
    }
</script>
@endsection 