@extends('layouts.app')

@section('title', 'Quản lý lịch hẹn')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/barber-dashboard.css') }}">
<style>
    .filter-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
        padding: 1.25rem;
    }
    
    .appointments-table th, .appointments-table td {
        vertical-align: middle;
    }
    
    .status-badge {
        padding: 0.5rem 0.75rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.8rem;
    }
    
    .status-pending {
        background-color: #f8f9fa;
        color: #f39c12;
        border: 1px solid #f39c12;
    }
    
    .status-confirmed {
        background-color: #e8f4fd;
        color: #3498db;
        border: 1px solid #3498db;
    }
    
    .status-completed {
        background-color: #e8f8f5;
        color: #2ecc71;
        border: 1px solid #2ecc71;
    }
    
    .status-canceled {
        background-color: #fdeeee;
        color: #e74c3c;
        border: 1px solid #e74c3c;
    }
    
    .action-btn {
        padding: 0.5rem 0.75rem;
        border-radius: 5px;
        margin-right: 0.25rem;
        transition: all 0.3s;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .service-badge {
        background-color: #f8f9fa;
        color: #2c3e50;
        border: 1px solid #ddd;
        padding: 0.25rem 0.5rem;
        border-radius: 50px;
        font-size: 0.75rem;
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
        display: inline-block;
    }
</style>
@endsection

@section('content')
<div class="container dashboard-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class="dashboard-title">Quản lý lịch hẹn</h1>
            <p class="dashboard-subtitle">Xem và quản lý tất cả các lịch hẹn của bạn</p>
            
            <!-- Bộ lọc -->
            <div class="filter-card">
                <form action="{{ route('barber.appointments.index') }}" method="GET" class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="date" class="form-label">Ngày hẹn</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Lọc
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Danh sách lịch hẹn -->
            <div class="card shadow">
                <div class="card-body">
                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table appointments-table">
                                <thead>
                                    <tr>
                                        <th>Mã đặt lịch</th>
                                        <th>Khách hàng</th>
                                        <th>Ngày</th>
                                        <th>Giờ</th>
                                        <th>Dịch vụ</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
                                        <tr>
                                            <td>{{ $appointment->booking_code }}</td>
                                            <td>{{ $appointment->customer_name }}</td>
                                            <td>{{ $appointment->appointment_date->format('d/m/Y') }}</td>
                                            <td>{{ $appointment->time_slot }}</td>
                                            <td>
                                                @foreach($appointment->services as $service)
                                                    <span class="service-badge">{{ $service->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($appointment->status == 'pending')
                                                    <span class="status-badge status-pending">Chờ xác nhận</span>
                                                @elseif($appointment->status == 'confirmed')
                                                    <span class="status-badge status-confirmed">Đã xác nhận</span>
                                                @elseif($appointment->status == 'completed')
                                                    <span class="status-badge status-completed">Hoàn thành</span>
                                                @elseif($appointment->status == 'canceled')
                                                    <span class="status-badge status-canceled">Đã hủy</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('barber.appointments.show', $appointment->id) }}" class="btn btn-info action-btn">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($appointment->status == 'confirmed')
                                                    <button type="button" class="btn btn-success action-btn" data-bs-toggle="modal" data-bs-target="#completeModal{{ $appointment->id }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    
                                                    <!-- Modal xác nhận hoàn thành -->
                                                    <div class="modal fade" id="completeModal{{ $appointment->id }}" tabindex="-1" aria-labelledby="completeModalLabel{{ $appointment->id }}" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="completeModalLabel{{ $appointment->id }}">Xác nhận hoàn thành lịch hẹn</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <form action="{{ route('barber.appointments.complete', $appointment->id) }}" method="POST">
                                                                    @csrf
                                                                    <div class="modal-body">
                                                                        <p>Bạn đang chuyển trạng thái lịch hẹn sang "Hoàn thành". Hệ thống sẽ tạo hóa đơn tự động.</p>
                                                                        <p>Vui lòng chọn trạng thái thanh toán:</p>
                                                                        
                                                                        <div class="form-check mb-3">
                                                                            <input class="form-check-input" type="radio" name="payment_status" id="payment-pending-{{ $appointment->id }}" value="pending" checked>
                                                                            <label class="form-check-label" for="payment-pending-{{ $appointment->id }}">
                                                                                Chưa thanh toán
                                                                            </label>
                                                                            <small class="text-muted d-block">Hóa đơn sẽ được tạo với trạng thái "Chưa thanh toán".</small>
                                                                        </div>
                                                                        
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="payment_status" id="payment-paid-{{ $appointment->id }}" value="paid">
                                                                            <label class="form-check-label" for="payment-paid-{{ $appointment->id }}">
                                                                                Đã thanh toán
                                                                            </label>
                                                                            <small class="text-muted d-block">Hóa đơn sẽ được tạo với trạng thái "Đã thanh toán".</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                                        <button type="submit" class="btn btn-success">Xác nhận</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Phân trang -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $appointments->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <h4>Không có lịch hẹn nào</h4>
                            <p class="text-muted">Không tìm thấy lịch hẹn nào phù hợp với bộ lọc của bạn.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
