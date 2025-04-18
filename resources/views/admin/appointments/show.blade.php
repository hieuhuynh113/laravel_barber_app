@extends('layouts.admin')

@section('title', 'Chi tiết lịch hẹn #' . $appointment->id)

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết lịch hẹn #{{ $appointment->id }}</h1>
        <div>
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <a href="{{ route('admin.appointments.edit', $appointment->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin lịch hẹn</h6>
                    <div>
                        @if($appointment->status == 'pending')
                            <span class="badge bg-warning">Chờ xác nhận</span>
                        @elseif($appointment->status == 'confirmed')
                            <span class="badge bg-primary">Đã xác nhận</span>
                        @elseif($appointment->status == 'completed')
                            <span class="badge bg-success">Hoàn thành</span>
                        @elseif($appointment->status == 'canceled')
                            <span class="badge bg-danger">Đã hủy</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Thông tin khách hàng</h5>
                            @if($appointment->user)
                                <p><strong>Tên:</strong> {{ $appointment->user->name }}</p>
                                <p><strong>Email:</strong> {{ $appointment->user->email }}</p>
                                <p><strong>Điện thoại:</strong> {{ $appointment->user->phone ?? 'Chưa cập nhật' }}</p>
                                <p><strong>Địa chỉ:</strong> {{ $appointment->user->address ?? 'Chưa cập nhật' }}</p>
                            @else
                                <p><strong>Tên:</strong> {{ $appointment->customer_name }}</p>
                                <p><strong>Email:</strong> {{ $appointment->email }}</p>
                                <p><strong>Điện thoại:</strong> {{ $appointment->phone ?? 'Chưa cập nhật' }}</p>
                                <p><strong>Địa chỉ:</strong> Chưa cập nhật</p>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Thông tin thợ cắt tóc</h5>
                            @if($appointment->barber && $appointment->barber->user)
                                <p><strong>Tên:</strong> {{ $appointment->barber->user->name }}</p>
                                <p><strong>Email:</strong> {{ $appointment->barber->user->email }}</p>
                                <p><strong>Điện thoại:</strong> {{ $appointment->barber->user->phone ?? 'Chưa cập nhật' }}</p>
                                <p><strong>Địa chỉ:</strong> {{ $appointment->barber->user->address ?? 'Chưa cập nhật' }}</p>
                                <p><strong>Chuyên môn:</strong> {{ $appointment->barber->specialties ?? 'Chưa cập nhật' }}</p>
                            @else
                                <p><strong>Tên:</strong> Không xác định</p>
                                <p><strong>Email:</strong> Không xác định</p>
                                <p><strong>Điện thoại:</strong> Không xác định</p>
                                <p><strong>Địa chỉ:</strong> Không xác định</p>
                                <p><strong>Chuyên môn:</strong> Không xác định</p>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Thời gian</h5>
                            <p><strong>Ngày hẹn:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</p>
                            <p><strong>Giờ hẹn:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</p>
                            <p><strong>Thời gian tạo:</strong> {{ $appointment->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Cập nhật lần cuối:</strong> {{ $appointment->updated_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Ghi chú</h5>
                            <p>{{ $appointment->note ?? 'Chưa có ghi chú' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h5 class="font-weight-bold">Dịch vụ đã chọn</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tên dịch vụ</th>
                                            <th>Thời gian</th>
                                            <th class="text-end">Giá</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @foreach($appointment->services as $service)
                                            @php $total += $service->price; @endphp
                                            <tr>
                                                <td>{{ $service->name }}</td>
                                                <td>{{ $service->duration }} phút</td>
                                                <td class="text-end">{{ number_format($service->price) }} VNĐ</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-end">Tổng cộng:</th>
                                            <th class="text-end">{{ number_format($total) }} VNĐ</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <!-- Thao tác -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thao tác</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="font-weight-bold">Cập nhật trạng thái</h5>
                        <div class="list-group">
                            <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="pending">
                                <button type="submit" class="list-group-item list-group-item-action {{ $appointment->status == 'pending' ? 'active' : '' }}">
                                    <i class="fas fa-clock me-2"></i> Chờ xác nhận
                                </button>
                            </form>

                            <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="list-group-item list-group-item-action {{ $appointment->status == 'confirmed' ? 'active' : '' }}">
                                    <i class="fas fa-check me-2"></i> Xác nhận
                                </button>
                            </form>

                            <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="list-group-item list-group-item-action {{ $appointment->status == 'completed' ? 'active' : '' }}">
                                    <i class="fas fa-check-double me-2"></i> Hoàn thành
                                </button>
                            </form>

                            <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="canceled">
                                <button type="submit" class="list-group-item list-group-item-action {{ $appointment->status == 'canceled' ? 'active' : '' }}">
                                    <i class="fas fa-times me-2"></i> Hủy
                                </button>
                            </form>
                        </div>
                    </div>

                    <div>
                        <form action="{{ route('admin.appointments.destroy', $appointment->id) }}" method="POST"
                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa lịch hẹn này không?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash me-2"></i> Xóa lịch hẹn
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Thông tin thanh toán -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin thanh toán</h6>
                    <div>
                        @if($appointment->payment_status == 'paid')
                            <span class="badge bg-success">Đã thanh toán</span>
                        @else
                            <span class="badge bg-warning">Chưa thanh toán</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p>
                            <strong>Phương thức thanh toán:</strong>
                            @if($appointment->payment_method == 'cash')
                                Tiền mặt
                            @elseif($appointment->payment_method == 'bank_transfer')
                                Chuyển khoản ngân hàng
                            @else
                                {{ $appointment->payment_method }}
                            @endif
                        </p>
                        <p>
                            <strong>Trạng thái thanh toán:</strong>
                            @if($appointment->payment_status == 'paid')
                                <span class="badge bg-success">Đã thanh toán</span>
                            @else
                                <span class="badge bg-warning">Chưa thanh toán</span>
                            @endif
                        </p>
                        <p><strong>Tổng tiền:</strong> {{ number_format($appointment->services->sum('pivot.price')) }} VNĐ</p>
                    </div>

                    @if($appointment->payment_method == 'bank_transfer')
                        <!-- Biên lai chuyển khoản -->
                        @if($appointment->paymentReceipt)
                            <div class="alert alert-info">
                                <h6 class="alert-heading">Biên lai chuyển khoản</h6>
                                <p class="mb-0"><strong>Trạng thái:</strong>
                                    @if($appointment->paymentReceipt->status == 'pending')
                                        <span class="badge bg-warning">Chờ xác nhận</span>
                                    @elseif($appointment->paymentReceipt->status == 'approved')
                                        <span class="badge bg-success">Đã xác nhận</span>
                                    @elseif($appointment->paymentReceipt->status == 'rejected')
                                        <span class="badge bg-danger">Đã từ chối</span>
                                    @endif
                                </p>
                                <p class="mb-0"><strong>Ngày tải lên:</strong> {{ $appointment->paymentReceipt->created_at->format('d/m/Y H:i') }}</p>
                                <div class="mt-2">
                                    <a href="{{ route('admin.payment-receipts.show', $appointment->paymentReceipt->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Xem biên lai
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <p class="mb-0">Khách hàng chưa tải lên biên lai chuyển khoản.</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Thông tin hóa đơn -->
            @if($appointment->status == 'completed')
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Thông tin hóa đơn</h6>
                        @if($appointment->invoice)
                            <span class="badge bg-success">Hóa đơn đã tạo</span>
                        @else
                            <span class="badge bg-warning">Chưa có hóa đơn</span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($appointment->invoice)
                            <div class="mb-3">
                                <p><strong>Mã hóa đơn:</strong> {{ $appointment->invoice->invoice_code }}</p>
                                <p><strong>Ngày tạo:</strong> {{ $appointment->invoice->created_at->format('d/m/Y H:i') }}</p>
                                <p><strong>Tổng tiền:</strong> {{ number_format($appointment->invoice->total) }} VNĐ</p>
                                <p>
                                    <strong>Phương thức thanh toán:</strong>
                                    @if($appointment->invoice->payment_method == 'cash')
                                        Tiền mặt
                                    @elseif($appointment->invoice->payment_method == 'bank_transfer')
                                        Chuyển khoản
                                    @elseif($appointment->invoice->payment_method == 'card')
                                        Thẻ
                                    @else
                                        {{ $appointment->invoice->payment_method }}
                                    @endif
                                </p>
                                <p>
                                    <strong>Trạng thái thanh toán:</strong>
                                    @if($appointment->invoice->payment_status == 'paid')
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    @else
                                        <span class="badge bg-warning">Chưa thanh toán</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <a href="{{ route('admin.invoices.show', $appointment->invoice->id) }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-eye me-2"></i> Xem chi tiết hóa đơn
                                </a>
                                <a href="{{ route('admin.invoices.edit', $appointment->invoice->id) }}" class="btn btn-info btn-block mt-2">
                                    <i class="fas fa-edit me-2"></i> Chỉnh sửa hóa đơn
                                </a>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                Chưa có hóa đơn cho lịch hẹn này.
                            </div>
                            <a href="{{ route('admin.invoices.create', ['appointment_id' => $appointment->id]) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-plus me-2"></i> Tạo hóa đơn
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection