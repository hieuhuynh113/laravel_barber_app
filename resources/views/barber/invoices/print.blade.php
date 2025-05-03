<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn #{{ $invoice->invoice_code }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
        }
        .invoice-header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }
        .invoice-header p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-info-item {
            flex: 1;
        }
        .invoice-info-item h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #2c3e50;
            font-size: 16px;
        }
        .invoice-info-item p {
            margin: 5px 0;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-table th, .invoice-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .invoice-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        .invoice-table .text-right {
            text-align: right;
        }
        .invoice-table .text-center {
            text-align: center;
        }
        .invoice-total {
            margin-top: 20px;
            text-align: right;
        }
        .invoice-total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 5px;
        }
        .invoice-total-row span:first-child {
            width: 150px;
            text-align: left;
        }
        .invoice-total-row span:last-child {
            width: 120px;
            text-align: right;
        }
        .invoice-total-row.grand-total {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }
        .invoice-footer {
            margin-top: 50px;
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .payment-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
        }
        .payment-status.paid {
            background-color: #d4edda;
            color: #155724;
        }
        .payment-status.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .section-title {
            margin-top: 30px;
            margin-bottom: 15px;
            color: #2c3e50;
            font-size: 18px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <h1>HÓA ĐƠN</h1>
            <p>Mã hóa đơn: #{{ $invoice->invoice_code }}</p>
            <p>Ngày tạo: {{ $invoice->created_at->format('d/m/Y H:i') }}</p>
            <p>
                Trạng thái thanh toán: 
                <span class="payment-status {{ $invoice->payment_status == 'paid' ? 'paid' : 'pending' }}">
                    {{ $invoice->payment_status == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                </span>
            </p>
        </div>

        <div class="invoice-info">
            <div class="invoice-info-item">
                <h3>Thông tin cửa hàng</h3>
                <p><strong>Barber Shop</strong></p>
                <p>Địa chỉ: 123 Đường ABC, Quận XYZ, TP. HCM</p>
                <p>Điện thoại: 0123 456 789</p>
                <p>Email: contact@barbershop.com</p>
            </div>
            <div class="invoice-info-item">
                <h3>Thông tin khách hàng</h3>
                <p><strong>{{ $invoice->user->name ?? $invoice->appointment->customer_name ?? 'Khách hàng' }}</strong></p>
                <p>Email: {{ $invoice->user->email ?? $invoice->appointment->email ?? 'N/A' }}</p>
                <p>Điện thoại: {{ $invoice->user->phone ?? $invoice->appointment->phone ?? 'N/A' }}</p>
                @if($invoice->appointment)
                    <p>Mã đặt lịch: {{ $invoice->appointment->booking_code }}</p>
                @endif
            </div>
        </div>

        <h3 class="section-title">Dịch vụ</h3>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th width="50%">Dịch vụ</th>
                    <th width="15%" class="text-center">Số lượng</th>
                    <th width="15%" class="text-right">Đơn giá</th>
                    <th width="20%" class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->services as $service)
                    <tr>
                        <td>{{ $service->name }}</td>
                        <td class="text-center">{{ $service->pivot->quantity }}</td>
                        <td class="text-right">{{ number_format($service->pivot->price) }} VNĐ</td>
                        <td class="text-right">{{ number_format($service->pivot->subtotal) }} VNĐ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($invoice->products->count() > 0)
            <h3 class="section-title">Sản phẩm</h3>
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th width="50%">Sản phẩm</th>
                        <th width="15%" class="text-center">Số lượng</th>
                        <th width="15%" class="text-right">Đơn giá</th>
                        <th width="20%" class="text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td class="text-center">{{ $product->pivot->quantity }}</td>
                            <td class="text-right">{{ number_format($product->pivot->price) }} VNĐ</td>
                            <td class="text-right">{{ number_format($product->pivot->subtotal) }} VNĐ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="invoice-total">
            <div class="invoice-total-row">
                <span>Tổng tiền dịch vụ:</span>
                <span>{{ number_format($invoice->subtotal) }} VNĐ</span>
            </div>
            @if($invoice->discount > 0)
                <div class="invoice-total-row">
                    <span>Giảm giá:</span>
                    <span>{{ number_format($invoice->discount) }} VNĐ</span>
                </div>
            @endif
            @if($invoice->tax > 0)
                <div class="invoice-total-row">
                    <span>Thuế ({{ number_format($invoice->tax_rate, 1) }}%):</span>
                    <span>{{ number_format($invoice->tax) }} VNĐ</span>
                </div>
            @endif
            <div class="invoice-total-row grand-total">
                <span>Tổng thanh toán:</span>
                <span>{{ number_format($invoice->total) }} VNĐ</span>
            </div>
        </div>

        @if($invoice->notes)
            <h3 class="section-title">Ghi chú</h3>
            <p>{{ $invoice->notes }}</p>
        @endif

        <div class="invoice-footer">
            <p>Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi!</p>
            <p>Hóa đơn này được tạo tự động và có giá trị pháp lý mà không cần chữ ký.</p>
        </div>
    </div>
</body>
</html>
