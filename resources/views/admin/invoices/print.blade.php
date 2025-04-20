<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn #{{ $invoice->invoice_code }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            padding: 30px;
        }
        .invoice-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .invoice-header .logo {
            max-width: 200px;
            height: auto;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #4e73df;
            margin-bottom: 5px;
        }
        .invoice-subtitle {
            font-size: 14px;
            color: #6c757d;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        .col-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding-right: 15px;
            padding-left: 15px;
            box-sizing: border-box;
        }
        .invoice-details {
            margin-bottom: 30px;
        }
        .invoice-details h5 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #4e73df;
        }
        .invoice-details p {
            margin: 5px 0;
        }
        .invoice-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            table-layout: auto;
        }
        .invoice-items th, .invoice-items td {
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            text-align: center;
            vertical-align: middle;
        }
        .invoice-items th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #4e73df;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
        }
        /* Định dạng cột cụ thể */
        .invoice-items th:nth-child(1) { width: 5%; } /* STT */
        .invoice-items th:nth-child(2) { width: 40%; text-align: left; } /* Sản phẩm/Dịch vụ */
        .invoice-items th:nth-child(3) { width: 20%; } /* Đơn giá */
        .invoice-items th:nth-child(4) { width: 10%; } /* Số lượng */
        .invoice-items th:nth-child(5) { width: 25%; } /* Thành tiền */

        /* Căn trái cho cột tên sản phẩm/dịch vụ */
        .invoice-items td:nth-child(2) {
            text-align: left;
        }

        /* Căn phải cho cột giá và thành tiền */
        .invoice-items td:nth-child(3),
        .invoice-items td:nth-child(5) {
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 600;
        }
        .text-right {
            text-align: right;
        }
        .invoice-total {
            margin-top: 20px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
        .invoice-total table {
            width: 300px;
            margin-left: auto;
        }
        .invoice-total table td {
            padding: 5px 0;
        }
        .invoice-total table td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .invoice-total table tr:last-child td {
            font-size: 18px;
            color: #4e73df;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        .invoice-notes {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .invoice-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
        .btn-print {
            background-color: #4e73df;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 20px;
        }
        .btn-print:hover {
            background-color: #2e59d9;
        }
        .print-only {
            display: none;
        }
        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }
            .invoice-container {
                box-shadow: none;
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .print-only {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="row">
                <div class="col-6">
                    <div class="invoice-title">{{ $shopInfo['shop_name'] }}</div>
                    <div class="invoice-subtitle">{{ $shopInfo['shop_address'] }}</div>
                    <div class="invoice-subtitle">Điện thoại: {{ $shopInfo['shop_phone'] }}</div>
                    <div class="invoice-subtitle">Email: {{ $shopInfo['shop_email'] }}</div>
                </div>
                <div class="col-6 text-right">
                    <div class="invoice-title">HÓA ĐƠN</div>
                    <div class="invoice-subtitle">Mã hóa đơn: {{ $invoice->invoice_code }}</div>
                    <div class="invoice-subtitle">Ngày: {{ \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y') }}</div>
                    <div class="invoice-subtitle">
                        Trạng thái thanh toán:
                        @if($invoice->payment_status == 'paid')
                            <span style="color: #1cc88a; font-weight: bold;">Đã thanh toán</span>
                        @else
                            <span style="color: #f6c23e; font-weight: bold;">Chưa thanh toán</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-details">
            <div class="row">
                <div class="col-6">
                    <h5>Thông tin khách hàng</h5>
                    <p><strong>Tên:</strong> {{ $invoice->user->name ?? $invoice->appointment->user->name ?? 'Không xác định' }}</p>
                    <p><strong>Email:</strong> {{ $invoice->user->email ?? $invoice->appointment->user->email ?? 'Không xác định' }}</p>
                    <p><strong>Điện thoại:</strong> {{ $invoice->user->phone ?? $invoice->appointment->user->phone ?? 'Không xác định' }}</p>
                </div>
                <div class="col-6">
                    <h5>Thông tin dịch vụ</h5>
                    <p><strong>Thợ cắt tóc:</strong> {{ $invoice->barber->user->name ?? $invoice->appointment->barber->user->name ?? 'Không xác định' }}</p>
                    <p><strong>Phương thức thanh toán:</strong>
                        @if($invoice->payment_method == 'cash')
                            Tiền mặt
                        @elseif($invoice->payment_method == 'card')
                            Thẻ
                        @elseif($invoice->payment_method == 'bank_transfer')
                            Chuyển khoản
                        @else
                            {{ $invoice->payment_method }}
                        @endif
                    </p>
                    @if($invoice->appointment)
                    <p><strong>Ngày hẹn:</strong> {{ \Carbon\Carbon::parse($invoice->appointment->appointment_date)->format('d/m/Y') }}</p>
                    <p><strong>Giờ hẹn:</strong> {{ $invoice->appointment->appointment_time }}</p>
                    @endif
                </div>
            </div>
        </div>

        <table class="invoice-items">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Dịch vụ/Sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @php $index = 0; @endphp

                @if($invoice->services && $invoice->services->count() > 0)
                    @foreach($invoice->services as $service)
                        <tr>
                            <td>{{ ++$index }}</td>
                            <td>{{ $service->name }} <small>(Dịch vụ)</small></td>
                            <td>{{ number_format($service->pivot->price) }} VNĐ</td>
                            <td>{{ $service->pivot->quantity }}</td>
                            <td>{{ number_format($service->pivot->price * $service->pivot->quantity) }} VNĐ</td>
                        </tr>
                    @endforeach
                @endif

                @if($invoice->products && $invoice->products->count() > 0)
                    @foreach($invoice->products as $product)
                        <tr>
                            <td>{{ ++$index }}</td>
                            <td>{{ $product->name }} <small>(Sản phẩm)</small></td>
                            <td>{{ number_format($product->pivot->price) }} VNĐ</td>
                            <td>{{ $product->pivot->quantity }}</td>
                            <td>{{ number_format($product->pivot->price * $product->pivot->quantity) }} VNĐ</td>
                        </tr>
                    @endforeach
                @endif

                @if((!$invoice->services || $invoice->services->count() == 0) && (!$invoice->products || $invoice->products->count() == 0))
                    <tr>
                        <td colspan="5" class="text-center">Không có dịch vụ hoặc sản phẩm nào trong hóa đơn này</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="invoice-total">
            <table>
                <tr>
                    <td>Tạm tính:</td>
                    <td>{{ number_format($invoice->subtotal) }} VNĐ</td>
                </tr>
                @if($invoice->discount > 0)
                <tr>
                    <td>Giảm giá:</td>
                    <td>{{ number_format($invoice->discount) }} VNĐ</td>
                </tr>
                @endif
                @if($invoice->tax > 0)
                <tr>
                    <td>Thuế:</td>
                    <td>{{ number_format($invoice->tax) }} VNĐ</td>
                </tr>
                @endif
                <tr>
                    <td>Tổng cộng:</td>
                    <td>{{ number_format($invoice->total) }} VNĐ</td>
                </tr>
            </table>
        </div>

        @if($invoice->notes)
        <div class="invoice-notes">
            <h5>Ghi chú</h5>
            <p>{{ $invoice->notes }}</p>
        </div>
        @endif

        <div class="invoice-footer">
            <p>Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi!</p>
            <p>{{ $shopInfo['shop_name'] }} - {{ $shopInfo['shop_address'] }}</p>
        </div>

        <div class="no-print" style="text-align: center; margin-top: 30px;">
            <button class="btn-print" onclick="window.print()">In hóa đơn</button>
            <button class="btn-print" onclick="window.close()">Đóng</button>
        </div>
    </div>
</body>
</html>
