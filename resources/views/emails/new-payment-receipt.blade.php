<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Biên lai thanh toán mới</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #0d6efd;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0d6efd;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Biên lai thanh toán mới</h1>
        </div>
        
        <div class="content">
            <p>Xin chào Admin,</p>
            
            <p>Một khách hàng vừa tải lên biên lai thanh toán cho lịch hẹn của họ. Dưới đây là thông tin chi tiết:</p>
            
            <h3>Thông tin lịch hẹn:</h3>
            <table>
                <tr>
                    <th>Mã đặt lịch:</th>
                    <td>{{ $appointment->booking_code }}</td>
                </tr>
                <tr>
                    <th>Khách hàng:</th>
                    <td>{{ $appointment->customer_name }}</td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td>{{ $appointment->email }}</td>
                </tr>
                <tr>
                    <th>Số điện thoại:</th>
                    <td>{{ $appointment->phone }}</td>
                </tr>
                <tr>
                    <th>Ngày hẹn:</th>
                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Giờ hẹn:</th>
                    <td>{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
                </tr>
                <tr>
                    <th>Thợ cắt tóc:</th>
                    <td>{{ $appointment->barber->user->name }}</td>
                </tr>
                <tr>
                    <th>Tổng tiền:</th>
                    <td>{{ number_format($appointment->services->sum('pivot.price')) }} VNĐ</td>
                </tr>
            </table>
            
            <h3>Thông tin biên lai:</h3>
            <p>Khách hàng đã tải lên biên lai thanh toán. Vui lòng kiểm tra tệp đính kèm.</p>
            
            @if($receipt->notes)
            <p><strong>Ghi chú từ khách hàng:</strong> {{ $receipt->notes }}</p>
            @endif
            
            <p>Vui lòng xác nhận thanh toán này trong hệ thống quản trị.</p>
            
            <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="btn">Xem chi tiết lịch hẹn</a>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Barber Shop. Tất cả các quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>
