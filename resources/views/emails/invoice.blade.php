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
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 15px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .shop-info, .customer-info {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table th, table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            font-size: 18px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .thank-you {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-top: 30px;
            color: #28a745;
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
        
        <h1 style="text-align: center; color: #333;">HÓA ĐƠN #{{ $invoice->invoice_code }}</h1>
        
        <div class="invoice-info">
            <div class="shop-info">
                <h3>Thông tin cửa hàng</h3>
                <p>{{ $shopInfo['shop_name'] }}<br>
                Địa chỉ: {{ $shopInfo['shop_address'] }}<br>
                SĐT: {{ $shopInfo['shop_phone'] }}<br>
                Email: {{ $shopInfo['shop_email'] }}</p>
            </div>
            
            <div class="customer-info">
                <h3>Thông tin khách hàng</h3>
                <p>
                    @if($invoice->user)
                        Tên: {{ $invoice->user->name }}<br>
                        Email: {{ $invoice->user->email }}<br>
                        @if($invoice->user->phone)
                            SĐT: {{ $invoice->user->phone }}<br>
                        @endif
                    @else
                        Khách hàng: Khách vãng lai
                    @endif
                    <br>
                    Ngày lập: {{ $invoice->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Dịch vụ</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
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
                    <td>{{ $product->name }} (Sản phẩm)</td>
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
            <p>{{ $shopInfo['shop_name'] }} | {{ $shopInfo['shop_address'] }}</p>
            <p>Liên hệ: {{ $shopInfo['shop_phone'] }} | {{ $shopInfo['shop_email'] }}</p>
        </div>
    </div>
</body>
</html> 