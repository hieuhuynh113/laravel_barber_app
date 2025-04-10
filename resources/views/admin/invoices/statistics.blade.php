@extends('layouts.admin')

@section('title', 'Thống kê doanh thu')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Thống kê doanh thu</h1>

    <div class="row">
        <!-- Monthly Revenue Card -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Doanh thu theo tháng trong năm {{ \Carbon\Carbon::now()->year }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Payment Method Stats Card -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê theo phương thức thanh toán</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                    <div class="mt-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Phương thức</th>
                                    <th>Số lượng</th>
                                    <th>Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentMethodStats as $stat)
                                <tr>
                                    <td>
                                        @if($stat->payment_method == 'cash')
                                            <span class="badge bg-primary">Tiền mặt</span>
                                        @elseif($stat->payment_method == 'bank_transfer')
                                            <span class="badge bg-success">Chuyển khoản</span>
                                        @endif
                                    </td>
                                    <td>{{ $stat->count }}</td>
                                    <td>{{ number_format($stat->total, 0, ',', '.') }} VNĐ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Status Stats Card -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê theo trạng thái hóa đơn</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="paymentStatusChart"></canvas>
                    </div>
                    <div class="mt-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Trạng thái</th>
                                    <th>Số lượng</th>
                                    <th>Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statusStats as $stat)
                                <tr>
                                    <td>
                                        @if($stat->status == 'paid')
                                            <span class="badge bg-success">Đã thanh toán</span>
                                        @elseif($stat->status == 'pending')
                                            <span class="badge bg-warning">Chờ thanh toán</span>
                                        @endif
                                    </td>
                                    <td>{{ $stat->count }}</td>
                                    <td>{{ number_format($stat->total, 0, ',', '.') }} VNĐ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chuẩn bị dữ liệu biểu đồ doanh thu theo tháng
    const monthlyData = @json($monthlyRevenue);
    const months = [
        'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
        'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
    ];
    
    const revenueData = Array(12).fill(0);
    monthlyData.forEach(item => {
        revenueData[item.month - 1] = item.total;
    });
    
    // Biểu đồ doanh thu theo tháng
    const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    new Chart(monthlyRevenueCtx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: revenueData,
                backgroundColor: 'rgba(78, 115, 223, 0.5)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + ' VNĐ';
                        }
                    }
                }
            }
        }
    });
    
    // Chuẩn bị dữ liệu biểu đồ theo phương thức thanh toán
    const paymentMethodData = @json($paymentMethodStats);
    const methodLabels = paymentMethodData.map(item => 
        item.payment_method === 'cash' ? 'Tiền mặt' : 
        item.payment_method === 'bank_transfer' ? 'Chuyển khoản' : item.payment_method
    );
    const methodValues = paymentMethodData.map(item => item.total);
    
    // Biểu đồ theo phương thức thanh toán
    const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
    new Chart(paymentMethodCtx, {
        type: 'pie',
        data: {
            labels: methodLabels,
            datasets: [{
                data: methodValues,
                backgroundColor: [
                    'rgba(78, 115, 223, 0.7)',
                    'rgba(28, 200, 138, 0.7)',
                    'rgba(246, 194, 62, 0.7)',
                    'rgba(231, 74, 59, 0.7)'
                ],
                borderColor: [
                    'rgba(78, 115, 223, 1)',
                    'rgba(28, 200, 138, 1)',
                    'rgba(246, 194, 62, 1)',
                    'rgba(231, 74, 59, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            return label + ': ' + value.toLocaleString('vi-VN') + ' VNĐ';
                        }
                    }
                }
            }
        }
    });
    
    // Chuẩn bị dữ liệu biểu đồ theo trạng thái
    const statusData = @json($statusStats);
    const statusLabels = statusData.map(item => 
        item.status === 'paid' ? 'Đã thanh toán' : 
        item.status === 'pending' ? 'Chờ thanh toán' : item.status
    );
    const statusValues = statusData.map(item => item.total);
    
    // Biểu đồ theo trạng thái
    const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
    new Chart(paymentStatusCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: [
                    'rgba(28, 200, 138, 0.7)',
                    'rgba(246, 194, 62, 0.7)'
                ],
                borderColor: [
                    'rgba(28, 200, 138, 1)',
                    'rgba(246, 194, 62, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            return label + ': ' + value.toLocaleString('vi-VN') + ' VNĐ';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection 