<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hóa đơn #{{ $invoice->invoice_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 15px;
        }
        .logo {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .invoice-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin: 15px 0;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info-content {
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        table th, table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
        }
        .thank-you {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            color: #28a745;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ $shopInfo['shop_name'] }}</div>
            <div>{{ $shopInfo['shop_address'] }}</div>
            <div>SĐT: {{ $shopInfo['shop_phone'] }} | Email: {{ $shopInfo['shop_email'] }}</div>
        </div>

        <div class="invoice-title">HÓA ĐƠN #{{ $invoice->invoice_code }}</div>

        <div class="info-section">
            <div class="info-title">Thông tin cửa hàng</div>
            <div class="info-content">
                {{ $shopInfo['shop_name'] }}<br>
                Địa chỉ: {{ $shopInfo['shop_address'] }}<br>
                SĐT: {{ $shopInfo['shop_phone'] }}<br>
                Email: {{ $shopInfo['shop_email'] }}
            </div>
        </div>

        <div class="info-section">
            <div class="info-title">Thông tin khách hàng</div>
            <div class="info-content">
                @if($invoice->user)
                    Tên: {{ $invoice->user->name }}<br>
                    Email: {{ $invoice->user->email }}<br>
                    @if($invoice->user->phone)
                        SĐT: {{ $invoice->user->phone }}<br>
                    @endif
                @else
                    Khách hàng: Khách vãng lai<br>
                @endif
                Ngày lập: {{ $invoice->created_at->format('d/m/Y H:i') }}
            </div>
        </div>

        <table cellspacing="0" cellpadding="0" border="1">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Dịch vụ</th>
                    <th>Giá</th>
                    <th>SL</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->services as $index => $service)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $service->name }}</td>
                    <td>{{ number_format($service->pivot->price) }}đ</td>
                    <td>{{ $service->pivot->quantity }}</td>
                    <td>{{ number_format($service->pivot->subtotal) }}đ</td>
                </tr>
                @endforeach

                @foreach($invoice->products as $index => $product)
                <tr>
                    <td>{{ $index + count($invoice->services) + 1 }}</td>
                    <td>{{ $product->name }} (SP)</td>
                    <td>{{ number_format($product->pivot->price) }}đ</td>
                    <td>{{ $product->pivot->quantity }}</td>
                    <td>{{ number_format($product->pivot->subtotal) }}đ</td>
                </tr>
                @endforeach

                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Tổng cộng:</td>
                    <td>{{ number_format($invoice->total_amount) }}đ</td>
                </tr>
            </tbody>
        </table>

        <div class="thank-you">
            Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi!
        </div>

        <div class="footer">
            {{ $shopInfo['shop_name'] }} | {{ $shopInfo['shop_address'] }}<br>
            Liên hệ: {{ $shopInfo['shop_phone'] }} | {{ $shopInfo['shop_email'] }}
        </div>
    </div>
</body>
</html>