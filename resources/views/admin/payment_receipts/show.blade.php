@extends('layouts.admin')

@section('title', 'Chi tiết biên lai thanh toán')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết biên lai thanh toán</h1>
        <a href="{{ route('admin.payment-receipts.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin lịch hẹn</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 35%">Mã đặt lịch:</th>
                                <td>{{ $receipt->appointment->booking_code }}</td>
                            </tr>
                            <tr>
                                <th>Khách hàng:</th>
                                <td>{{ $receipt->appointment->customer_name }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $receipt->appointment->email }}</td>
                            </tr>
                            <tr>
                                <th>Số điện thoại:</th>
                                <td>{{ $receipt->appointment->phone }}</td>
                            </tr>
                            <tr>
                                <th>Ngày hẹn:</th>
                                <td>{{ \Carbon\Carbon::parse($receipt->appointment->appointment_date)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Giờ hẹn:</th>
                                <td>{{ $receipt->appointment->start_time }} - {{ $receipt->appointment->end_time }}</td>
                            </tr>
                            <tr>
                                <th>Thợ cắt tóc:</th>
                                <td>{{ $receipt->appointment->barber->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái lịch hẹn:</th>
                                <td>
                                    @if($receipt->appointment->status == 'pending')
                                    <span class="badge badge-warning">Chờ xác nhận</span>
                                    @elseif($receipt->appointment->status == 'confirmed')
                                    <span class="badge badge-primary">Đã xác nhận</span>
                                    @elseif($receipt->appointment->status == 'completed')
                                    <span class="badge badge-success">Đã hoàn thành</span>
                                    @elseif($receipt->appointment->status == 'canceled')
                                    <span class="badge badge-danger">Đã hủy</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Trạng thái thanh toán:</th>
                                <td>
                                    @if($receipt->appointment->payment_status == 'pending')
                                    <span class="badge badge-warning">Chưa thanh toán</span>
                                    @elseif($receipt->appointment->payment_status == 'paid')
                                    <span class="badge badge-success">Đã thanh toán</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Phương thức thanh toán:</th>
                                <td>
                                    @if($receipt->appointment->payment_method == 'cash')
                                    <span>Tiền mặt</span>
                                    @elseif($receipt->appointment->payment_method == 'bank_transfer')
                                    <span>Chuyển khoản ngân hàng</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dịch vụ đã chọn</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Dịch vụ</th>
                                    <th>Thời gian</th>
                                    <th class="text-right">Giá</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalPrice = 0; @endphp
                                @foreach($receipt->appointment->services as $service)
                                <tr>
                                    <td>{{ $service->name }}</td>
                                    <td>{{ $service->duration }} phút</td>
                                    <td class="text-right">{{ number_format($service->pivot->price) }} VNĐ</td>
                                </tr>
                                @php $totalPrice += $service->pivot->price; @endphp
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Tổng cộng</th>
                                    <th class="text-right">{{ number_format($totalPrice) }} VNĐ</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin biên lai</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 35%">ID:</th>
                                <td>{{ $receipt->id }}</td>
                            </tr>
                            <tr>
                                <th>Ngày tải lên:</th>
                                <td>{{ $receipt->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái:</th>
                                <td>
                                    @if($receipt->status == 'pending')
                                    <span class="badge badge-warning">Chờ xác nhận</span>
                                    @elseif($receipt->status == 'approved')
                                    <span class="badge badge-success">Đã xác nhận</span>
                                    @elseif($receipt->status == 'rejected')
                                    <span class="badge badge-danger">Đã từ chối</span>
                                    @endif
                                </td>
                            </tr>
                            @if($receipt->notes)
                            <tr>
                                <th>Ghi chú của khách hàng:</th>
                                <td>{{ $receipt->notes }}</td>
                            </tr>
                            @endif
                            @if($receipt->admin_notes)
                            <tr>
                                <th>Ghi chú của admin:</th>
                                <td>{{ $receipt->admin_notes }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>

                    <div class="mt-4">
                        <h6 class="font-weight-bold">Hình ảnh biên lai:</h6>
                        <div class="text-center mt-3">
                            <img src="{{ asset('storage/' . $receipt->file_path) }}" alt="Biên lai thanh toán" class="img-fluid border" style="max-height: 500px;">
                        </div>
                    </div>

                    @if($receipt->status == 'pending')
                    <div class="mt-4">
                        <h6 class="font-weight-bold">Cập nhật trạng thái:</h6>
                        <form action="{{ route('admin.payment-receipts.update-status', $receipt->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="status">Trạng thái:</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="approved">Xác nhận</option>
                                    <option value="rejected">Từ chối</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="admin_notes">Ghi chú:</label>
                                <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" placeholder="Nhập ghi chú (bắt buộc nếu từ chối)"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thao tác</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.appointments.show', $receipt->appointment->id) }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-calendar-alt"></i> Xem chi tiết lịch hẹn
                    </a>
                    
                    @if($receipt->status == 'pending')
                    <form action="{{ route('admin.payment-receipts.update-status', $receipt->id) }}" method="POST" class="mb-2">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Bạn có chắc chắn muốn xác nhận biên lai này?')">
                            <i class="fas fa-check"></i> Xác nhận biên lai
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.payment-receipts.update-status', $receipt->id) }}" method="POST" class="mb-2">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <input type="hidden" name="admin_notes" value="Biên lai không hợp lệ. Vui lòng tải lên biên lai khác.">
                        <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Bạn có chắc chắn muốn từ chối biên lai này?')">
                            <i class="fas fa-times"></i> Từ chối biên lai
                        </button>
                    </form>
                    @endif
                    
                    <form action="{{ route('admin.payment-receipts.destroy', $receipt->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Bạn có chắc chắn muốn xóa biên lai này?')">
                            <i class="fas fa-trash"></i> Xóa biên lai
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
