@extends('layouts.admin')

@section('title', 'Thống kê doanh thu')

@section('styles')
<style>
    .stats-card {
        border-radius: 8px;
        transition: all 0.3s;
        height: 100%;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .stats-card .card-header {
        background-color: rgba(78, 115, 223, 0.05);
        border-bottom: 1px solid rgba(78, 115, 223, 0.125);
        padding: 1rem 1.25rem;
    }
    
    .stats-card .card-header h6 {
        color: #4e73df;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0;
    }
    
    .stats-card .card-body {
        padding: 1.5rem;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 1rem;
    }
    
    .stats-table {
        margin-top: 1.5rem;
        width: 100%;
        border-collapse: collapse;
    }
    
    .stats-table th {
        background-color: #f8f9fc;
        color: #4e73df;
        font-weight: 600;
        text-align: left;
        padding: 0.75rem 1rem;
        border: 1px solid #e3e6f0;
        font-size: 0.85rem;
        text-transform: uppercase;
    }
    
    .stats-table td {
        padding: 0.75rem 1rem;
        border: 1px solid #e3e6f0;
        vertical-align: middle;
    }
    
    .method-badge {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .badge-cash {
        background-color: #4e73df;
        color: white;
    }
    
    .badge-bank {
        background-color: #1cc88a;
        color: white;
    }
    
    .badge-card {
        background-color: #36b9cc;
        color: white;
    }
    
    .badge-paid {
        background-color: #1cc88a;
        color: white;
    }
    
    .badge-pending {
        background-color: #f6c23e;
        color: white;
    }
    
    .badge-canceled {
        background-color: #e74a3b;
        color: white;
    }
    
    .amount-cell {
        text-align: right;
        font-weight: 600;
        font-family: 'Nunito', sans-serif;
    }
    
    .count-cell {
        text-align: center;
        font-weight: 600;
    }
    
    .summary-card {
        border-left: 0.25rem solid #4e73df;
        border-radius: 0.35rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    
    .summary-card .card-body {
        padding: 1.25rem;
    }
    
    .summary-card .text-xs {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .summary-card .h5 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0;
    }
    
    /* Đảm bảo độ cao nhất quán cho biểu đồ */
    canvas {
        width: 100% !important;
        height: 100% !important;
    }
    
    .chart-area {
        position: relative;
        height: 300px;
        margin-bottom: 1rem;
    }
    
    .chart-pie {
        position: relative;
        height: 250px;
        margin-bottom: 1rem;
    }
    
    /* Đảm bảo bố cục đồng nhất */
    .table-responsive {
        overflow-x: auto;
    }
    
    /* Kiểu dáng tiêu đề trang */
    .page-header {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .page-header h1 {
        margin: 0;
        font-weight: 700;
        color: #5a5c69;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="page-header d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê doanh thu</h1>
        <div>
            <button onclick="exportToPDF()" class="btn btn-sm btn-danger">
                <i class="fas fa-file-pdf mr-1"></i> Xuất PDF
            </button>
            <button onclick="exportToExcel()" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel mr-1"></i> Xuất Excel
            </button>
            <button onclick="toggleDebug()" class="btn btn-sm btn-secondary">
                <i class="fas fa-bug"></i> Debug
            </button>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card summary-card border-left-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Doanh thu tháng này</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $currentMonth = \Carbon\Carbon::now()->month;
                                    $currentMonthRevenue = $monthlyRevenue->where('month', $currentMonth)->first();
                                    $monthTotal = $currentMonthRevenue ? $currentMonthRevenue->total : 0;
                                @endphp
                                {{ number_format($monthTotal, 0, ',', '.') }} VNĐ
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card summary-card border-left-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng doanh thu năm {{ \Carbon\Carbon::now()->year }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $yearTotal = $monthlyRevenue->sum('total');
                                @endphp
                                {{ number_format($yearTotal, 0, ',', '.') }} VNĐ
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card summary-card border-left-info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Số lượng hóa đơn đã thanh toán</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $paidInvoiceCount = $statusStats->where('status', 'paid')->first();
                                    $paidCount = $paidInvoiceCount ? $paidInvoiceCount->count : 0;
                                @endphp
                                {{ $paidCount }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card summary-card border-left-warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Số lượng hóa đơn chờ thanh toán</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $pendingInvoiceCount = $statusStats->where('status', 'pending')->first();
                                    $pendingCount = $pendingInvoiceCount ? $pendingInvoiceCount->count : 0;
                                @endphp
                                {{ $pendingCount }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ doanh thu theo tháng -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card stats-card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold">DOANH THU THEO THÁNG TRONG NĂM {{ \Carbon\Carbon::now()->year }}</h6>
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
        <!-- Thống kê theo phương thức thanh toán -->
        <div class="col-xl-6 col-lg-6">
            <div class="card stats-card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold">THỐNG KÊ THEO PHƯƠNG THỨC THANH TOÁN</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie mb-4">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                    <div class="table-responsive">
                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th width="40%">PHƯƠNG THỨC</th>
                                    <th width="20%">SỐ LƯỢNG</th>
                                    <th width="40%">TỔNG TIỀN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentMethodStats as $stat)
                                <tr>
                                    <td>
                                        @if($stat->payment_method == 'cash')
                                            <span class="method-badge badge-cash">Tiền mặt</span>
                                        @elseif($stat->payment_method == 'bank_transfer')
                                            <span class="method-badge badge-bank">Chuyển khoản</span>
                                        @elseif($stat->payment_method == 'card')
                                            <span class="method-badge badge-card">Thẻ</span>
                                        @else
                                            <span class="method-badge">{{ $stat->payment_method }}</span>
                                        @endif
                                    </td>
                                    <td class="count-cell">{{ $stat->count }}</td>
                                    <td class="amount-cell">{{ number_format($stat->total, 0, ',', '.') }} VNĐ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê theo trạng thái hóa đơn -->
        <div class="col-xl-6 col-lg-6">
            <div class="card stats-card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold">THỐNG KÊ THEO TRẠNG THÁI HÓA ĐƠN</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie mb-4">
                        <canvas id="paymentStatusChart"></canvas>
                    </div>
                    <div class="table-responsive">
                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th width="40%">TRẠNG THÁI</th>
                                    <th width="20%">SỐ LƯỢNG</th>
                                    <th width="40%">TỔNG TIỀN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statusStats as $stat)
                                <tr>
                                    <td>
                                        @if($stat->status == 'paid')
                                            <span class="method-badge badge-paid">Đã thanh toán</span>
                                        @elseif($stat->status == 'pending')
                                            <span class="method-badge badge-pending">Chờ thanh toán</span>
                                        @else
                                            <span class="method-badge">{{ $stat->status }}</span>
                                        @endif
                                    </td>
                                    <td class="count-cell">{{ $stat->count }}</td>
                                    <td class="amount-cell">{{ number_format($stat->total, 0, ',', '.') }} VNĐ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê theo dịch vụ/sản phẩm -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card stats-card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold">TOP DỊCH VỤ VÀ SẢN PHẨM BÁN CHẠY</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th width="40%">TÊN DỊCH VỤ/SẢN PHẨM</th>
                                    <th width="20%">LOẠI</th>
                                    <th width="15%">SỐ LƯỢNG</th>
                                    <th width="25%">DOANH THU</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($itemStats as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        @if($item->type == 'service')
                                            <span class="method-badge badge-info">Dịch vụ</span>
                                        @else
                                            <span class="method-badge badge-primary">Sản phẩm</span>
                                        @endif
                                    </td>
                                    <td class="count-cell">{{ $item->count }}</td>
                                    <td class="amount-cell">{{ number_format($item->total, 0, ',', '.') }} VNĐ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê theo thợ cắt tóc -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card stats-card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold">DOANH THU THEO THỢ CẮT TÓC</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th width="40%">THỢ CẮT TÓC</th>
                                    <th width="20%">SỐ HÓA ĐƠN</th>
                                    <th width="40%">DOANH THU</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($barberStats as $barber)
                                <tr>
                                    <td>{{ $barber->name }}</td>
                                    <td class="count-cell">{{ $barber->count }}</td>
                                    <td class="amount-cell">{{ number_format($barber->total, 0, ',', '.') }} VNĐ</td>
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
<!-- Debug info -->
<div class="debug-info" style="display: none;">
    <h3>Debug Phương thức thanh toán:</h3>
    <pre>{{ json_encode($paymentMethodStats, JSON_PRETTY_PRINT) }}</pre>
    
    <h3>Debug Trạng thái thanh toán:</h3>
    <pre>{{ json_encode($statusStats, JSON_PRETTY_PRINT) }}</pre>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
                backgroundColor: 'rgba(78, 115, 223, 0.7)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1,
                borderRadius: 4,
                maxBarThickness: 60
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN', { 
                                style: 'decimal',
                                maximumFractionDigits: 0
                            }).format(value) + ' VNĐ';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.8)',
                    titleColor: '#5a5c69',
                    bodyColor: '#5a5c69',
                    borderColor: 'rgba(78, 115, 223, 0.2)',
                    borderWidth: 1,
                    displayColors: false,
                    mode: 'index',
                    intersect: false,
                    padding: 10,
                    callbacks: {
                        label: function(context) {
                            let value = context.raw || 0;
                            return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN', { 
                                style: 'decimal',
                                maximumFractionDigits: 0
                            }).format(value) + ' VNĐ';
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
        item.payment_method === 'bank_transfer' ? 'Chuyển khoản' : 
        item.payment_method === 'card' ? 'Thẻ' : item.payment_method
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
                    'rgba(78, 115, 223, 0.8)',
                    'rgba(28, 200, 138, 0.8)',
                    'rgba(54, 185, 204, 0.8)',
                    'rgba(246, 194, 62, 0.8)'
                ],
                borderColor: [
                    'rgba(78, 115, 223, 1)',
                    'rgba(28, 200, 138, 1)',
                    'rgba(54, 185, 204, 1)',
                    'rgba(246, 194, 62, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.8)',
                    titleColor: '#5a5c69',
                    bodyColor: '#5a5c69',
                    borderColor: 'rgba(0, 0, 0, 0.1)',
                    borderWidth: 1,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let percentage = context.parsed || 0;
                            let sum = context.dataset.data.reduce((a, b) => a + b, 0);
                            percentage = Math.round((percentage / sum) * 100);
                            
                            return label + ': ' + new Intl.NumberFormat('vi-VN', { 
                                style: 'decimal',
                                maximumFractionDigits: 0
                            }).format(value) + ' VNĐ (' + percentage + '%)';
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
                    'rgba(28, 200, 138, 0.8)',
                    'rgba(246, 194, 62, 0.8)'
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
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.8)',
                    titleColor: '#5a5c69',
                    bodyColor: '#5a5c69',
                    borderColor: 'rgba(0, 0, 0, 0.1)',
                    borderWidth: 1,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let percentage = context.parsed || 0;
                            let sum = context.dataset.data.reduce((a, b) => a + b, 0);
                            percentage = Math.round((percentage / sum) * 100);
                            
                            return label + ': ' + new Intl.NumberFormat('vi-VN', { 
                                style: 'decimal',
                                maximumFractionDigits: 0
                            }).format(value) + ' VNĐ (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
    
    // Chức năng xuất báo cáo
    function exportToPDF() {
        alert('Đang xuất báo cáo PDF...');
        // Thêm chức năng xuất PDF ở đây
    }
    
    function exportToExcel() {
        alert('Đang xuất báo cáo Excel...');
        // Thêm chức năng xuất Excel ở đây
    }

    function toggleDebug() {
        const debugInfo = document.querySelector('.debug-info');
        if (debugInfo.style.display === 'none') {
            debugInfo.style.display = 'block';
        } else {
            debugInfo.style.display = 'none';
        }
    }
</script>
@endsection 