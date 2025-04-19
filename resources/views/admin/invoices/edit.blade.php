@extends('layouts.admin')

@section('title', 'Chỉnh sửa hóa đơn')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/invoice-form.css') }}">
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa hóa đơn #{{ $invoice->invoice_code }}</h1>
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <form action="{{ route('admin.invoices.update', $invoice->id) }}" method="POST" id="invoiceForm" class="invoice-form" onsubmit="return validateForm();">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Thông tin khách hàng</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_type">Loại khách hàng</label>
                                    <select class="form-select @error('customer_type') is-invalid @enderror" id="customer_type" name="customer_type">
                                        <option value="guest" {{ $invoice->user_id ? '' : 'selected' }}>Khách vãng lai</option>
                                        <option value="registered" {{ $invoice->user_id ? 'selected' : '' }}>Khách đã đăng ký</option>
                                    </select>
                                    @error('customer_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 registered-customer-section" style="{{ $invoice->user_id ? '' : 'display: none;' }}">
                                <div class="form-group">
                                    <label for="user_id">Chọn khách hàng</label>
                                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                                        <option value="">-- Chọn khách hàng --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $invoice->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="guest-customer-section" style="{{ $invoice->user_id ? 'display: none;' : '' }}">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_name">Tên khách hàng</label>
                                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name', $invoice->customer_name) }}">
                                        @error('customer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_phone">Số điện thoại</label>
                                        <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $invoice->customer_phone) }}">
                                        @error('customer_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_email">Email</label>
                                        <input type="email" class="form-control @error('customer_email') is-invalid @enderror" id="customer_email" name="customer_email" value="{{ old('customer_email', $invoice->customer_email) }}">
                                        @error('customer_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_address">Địa chỉ</label>
                                        <input type="text" class="form-control @error('customer_address') is-invalid @enderror" id="customer_address" name="customer_address" value="{{ old('customer_address', $invoice->customer_address) }}">
                                        @error('customer_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Thông tin sản phẩm/dịch vụ</h6>
                        <button type="button" class="btn btn-primary btn-sm" id="addItem">
                            <i class="fas fa-plus"></i> Thêm sản phẩm/dịch vụ
                        </button>
                    </div>
                    <!-- Trường ẩn đánh dấu rằng phần sản phẩm đã được gửi -->
                    <input type="hidden" name="product_section_submitted" value="1">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="items-table">
                                <thead>
                                    <tr>
                                        <th>Loại</th>
                                        <th>Sản phẩm/Dịch vụ</th>
                                        <th>Đơn giá (VNĐ)</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền (VNĐ)</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="invoice-items">
                                    @php $serviceIndex = 0; $productIndex = 0; @endphp
                                    @foreach($invoice->items as $index => $item)
                                    <tr class="invoice-item">
                                        <td>
                                            <input type="hidden" class="item-type-input" value="{{ $item->type }}">
                                            <select class="form-select item-type">
                                                <option value="service" {{ $item->type == 'service' ? 'selected' : '' }}>Dịch vụ</option>
                                                <option value="product" {{ $item->type == 'product' ? 'selected' : '' }}>Sản phẩm</option>
                                            </select>
                                        </td>
                                        <td>
                                            @if($item->type == 'service')
                                                <select class="form-select item-select" name="service_ids[{{ $serviceIndex }}]" data-type="service">
                                                    <option value="">-- Chọn dịch vụ --</option>
                                                    @foreach($services as $service)
                                                        <option value="{{ $service->id }}" data-price="{{ $service->price }}" {{ $item->item_id == $service->id ? 'selected' : '' }}>
                                                            {{ $service->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="service_prices[{{ $serviceIndex }}]" value="{{ $item->price }}" class="service-price">
                                                <input type="hidden" name="service_quantities[{{ $serviceIndex }}]" value="{{ $item->quantity }}" class="service-quantity">
                                                @php $serviceIndex++; @endphp
                                            @endif

                                            @if($item->type == 'product')
                                                <select class="form-select item-select" name="product_ids[{{ $productIndex }}]" data-type="product">
                                                    <option value="">-- Chọn sản phẩm --</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ $item->item_id == $product->id ? 'selected' : '' }}>
                                                            {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="product_prices[{{ $productIndex }}]" value="{{ $item->price }}" class="product-price">
                                                <input type="hidden" name="product_quantities[{{ $productIndex }}]" value="{{ $item->quantity }}" class="product-quantity">
                                                @php $productIndex++; @endphp
                                            @endif

                                            <select class="form-select item-select" data-type="service" style="display:none;">
                                                <option value="">-- Chọn dịch vụ --</option>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" data-price="{{ $service->price }}">
                                                        {{ $service->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <select class="form-select item-select" data-type="product" style="display:none;">
                                                <option value="">-- Chọn sản phẩm --</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control item-price" value="{{ $item->price }}" min="0">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control item-quantity" value="{{ $item->quantity }}" min="1">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control item-total" value="{{ $item->price * $item->quantity }}" readonly>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm remove-item">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Thông tin thanh toán</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="invoice_code">Mã hóa đơn</label>
                            <input type="text" class="form-control" id="invoice_code" value="{{ $invoice->invoice_code }}" readonly>
                        </div>

                        <div class="form-group mb-3">
                            <label for="subtotal">Tạm tính</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="subtotal" name="subtotal" value="{{ $invoice->subtotal }}" readonly>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="discount_amount">Giảm giá</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', $invoice->discount_amount) }}" min="0">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tax_rate">Thuế (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $invoice->tax_rate) }}" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tax_amount">Tiền thuế</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="tax_amount" name="tax_amount" value="{{ $invoice->tax_amount }}" readonly>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="total_amount">Tổng tiền</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="total_amount" name="total_amount" value="{{ $invoice->total_amount }}" readonly>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="payment_method">Phương thức thanh toán</label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method">
                                <option value="cash" {{ $invoice->payment_method == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                                <option value="card" {{ $invoice->payment_method == 'card' ? 'selected' : '' }}>Thẻ</option>
                                <option value="bank_transfer" {{ $invoice->payment_method == 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="payment_status">Tình trạng thanh toán</label>
                            <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status">
                                <option value="pending" {{ $invoice->payment_status == 'pending' ? 'selected' : '' }}>Chưa thanh toán</option>
                                <option value="paid" {{ $invoice->payment_status == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                            </select>
                            @error('payment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="status">Trạng thái hóa đơn</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="pending" {{ $invoice->status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="completed" {{ $invoice->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="canceled" {{ $invoice->status == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes">Ghi chú</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $invoice->notes) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block w-100">
                            <i class="fas fa-save"></i> Cập nhật hóa đơn
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Xử lý loại khách hàng
        $('#customer_type').change(function() {
            if ($(this).val() === 'registered') {
                $('.registered-customer-section').show();
                $('.guest-customer-section').hide();
            } else {
                $('.registered-customer-section').hide();
                $('.guest-customer-section').show();
            }
        });

        // Xử lý khi thay đổi loại item (sản phẩm/dịch vụ)
        $(document).on('change', '.item-type', function() {
            const row = $(this).closest('tr');
            const type = $(this).val();
            const oldType = row.find('.item-type-input').val();

            // Cập nhật giá trị loại mới
            row.find('.item-type-input').val(type);

            // Ẩn tất cả các select và hiển thị select phù hợp
            row.find('.item-select').hide();

            // Lấy giá trị hiện tại
            const price = row.find('.item-price').val();
            const quantity = row.find('.item-quantity').val();

            if (type === 'service') {
                // Nếu chuyển từ product sang service
                if (oldType === 'product') {
                    // Xóa các trường product cũ
                    row.find('input[name^="product_"]').remove();

                    // Tạo mới các trường service
                    row.find('td:eq(1)').append(`
                        <input type="hidden" name="service_prices[${serviceIndex}]" value="${price}" class="service-price">
                        <input type="hidden" name="service_quantities[${serviceIndex}]" value="${quantity}" class="service-quantity">
                    `);

                    // Cập nhật name cho select
                    row.find('.item-select[data-type="service"]').attr('name', `service_ids[${serviceIndex}]`);
                    serviceIndex++;
                } else {
                    // Cập nhật giá trị cho các trường service hiện tại
                    row.find('.service-price').val(price);
                    row.find('.service-quantity').val(quantity);
                }

                row.find('.item-select[data-type="service"]').show();
            } else {
                // Nếu chuyển từ service sang product
                if (oldType === 'service') {
                    // Xóa các trường service cũ
                    row.find('input[name^="service_"]').remove();

                    // Tạo mới các trường product
                    row.find('td:eq(1)').append(`
                        <input type="hidden" name="product_prices[${productIndex}]" value="${price}" class="product-price">
                        <input type="hidden" name="product_quantities[${productIndex}]" value="${quantity}" class="product-quantity">
                    `);

                    // Cập nhật name cho select
                    row.find('.item-select[data-type="product"]').attr('name', `product_ids[${productIndex}]`);
                    productIndex++;
                } else {
                    // Cập nhật giá trị cho các trường product hiện tại
                    row.find('.product-price').val(price);
                    row.find('.product-quantity').val(quantity);
                }

                row.find('.item-select[data-type="product"]').show();
            }

            updateRowTotal(row);
            calculateTotals();
        });

        // Xử lý khi chọn sản phẩm/dịch vụ
        $(document).on('change', '.item-select', function() {
            if (!$(this).is(':visible')) return;

            const row = $(this).closest('tr');
            const selectedOption = $(this).find('option:selected');
            const price = selectedOption.data('price') || 0;
            const type = row.find('.item-type-input').val();
            const quantity = row.find('.item-quantity').val() || 1;

            row.find('.item-price').val(price);

            // Cập nhật giá trị trong các trường ẩn
            if (type === 'service') {
                row.find('.service-price').val(price);
                row.find('.service-quantity').val(quantity);
            } else if (type === 'product') {
                row.find('.product-price').val(price);
                row.find('.product-quantity').val(quantity);
            }

            updateRowTotal(row);
            calculateTotals();
        });

        // Xử lý khi thay đổi giá hoặc số lượng
        $(document).on('input', '.item-price, .item-quantity', function() {
            const row = $(this).closest('tr');
            const type = row.find('.item-type-input').val();
            const price = row.find('.item-price').val();
            const quantity = row.find('.item-quantity').val();

            // Cập nhật giá trị trong các trường ẩn
            if (type === 'service') {
                row.find('.service-price').val(price);
                row.find('.service-quantity').val(quantity);
            } else if (type === 'product') {
                row.find('.product-price').val(price);
                row.find('.product-quantity').val(quantity);
            }

            updateRowTotal(row);
            calculateTotals();
        });

        // Xử lý khi nhập giảm giá hoặc thuế
        $('#discount_amount, #tax_rate').on('input', function() {
            calculateTotals();
        });

        // Cập nhật thành tiền của một dòng
        function updateRowTotal(row) {
            const price = parseFloat(row.find('.item-price').val()) || 0;
            const quantity = parseInt(row.find('.item-quantity').val()) || 0;
            const total = price * quantity;
            row.find('.item-total').val(total);
        }

        // Tính toán tổng tiền
        function calculateTotals() {
            let subtotal = 0;
            $('.item-total').each(function() {
                subtotal += parseFloat($(this).val()) || 0;
            });

            const discountAmount = parseFloat($('#discount_amount').val()) || 0;
            const taxRate = parseFloat($('#tax_rate').val()) || 0;

            const taxableAmount = Math.max(0, subtotal - discountAmount);
            const taxAmount = taxableAmount * (taxRate / 100);
            const totalAmount = taxableAmount + taxAmount;

            $('#subtotal').val(subtotal);
            $('#tax_amount').val(taxAmount);
            $('#total_amount').val(totalAmount);
        }

        // Thêm sản phẩm/dịch vụ mới
        let serviceIndex = {{ $serviceIndex }};
        let productIndex = {{ $productIndex }};

        $('#addItem').click(function() {
            const newRow = `
                <tr class="invoice-item">
                    <td>
                        <input type="hidden" class="item-type-input" value="service">
                        <select class="form-select item-type">
                            <option value="service" selected>Dịch vụ</option>
                            <option value="product">Sản phẩm</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select item-select" name="service_ids[${serviceIndex}]" data-type="service">
                            <option value="">-- Chọn dịch vụ --</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" data-price="{{ $service->price }}">
                                    {{ $service->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="service_prices[${serviceIndex}]" value="0" class="service-price">
                        <input type="hidden" name="service_quantities[${serviceIndex}]" value="1" class="service-quantity">

                        <select class="form-select item-select" data-type="product" style="display: none;">
                            <option value="">-- Chọn sản phẩm --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control item-price" value="0" min="0">
                    </td>
                    <td>
                        <input type="number" class="form-control item-quantity" value="1" min="1">
                    </td>
                    <td>
                        <input type="number" class="form-control item-total" value="0" readonly>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-item">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;
            const $newRow = $(newRow);
            $('#invoice-items').append($newRow);

            // Cập nhật giá trị ban đầu cho các trường ẩn
            $newRow.find('.service-price').val(0);
            $newRow.find('.service-quantity').val(1);

            // Cập nhật tổng tiền
            updateRowTotal($newRow);
            calculateTotals();

            serviceIndex++;
        });

        // Xóa sản phẩm/dịch vụ
        $(document).on('click', '.remove-item', function() {
            if ($('.invoice-item').length > 1) {
                // Xóa hàng và các trường ẩn liên quan
                const row = $(this).closest('tr');
                const type = row.find('.item-type-input').val();

                // Xóa hàng
                row.remove();

                // Cập nhật tổng tiền
                calculateTotals();

                // Ghi log để debug
                console.log('Item removed, type:', type);
                console.log('Remaining items:', $('.invoice-item').length);
            } else {
                alert('Hóa đơn cần ít nhất một sản phẩm hoặc dịch vụ');
            }
        });

        // Khởi tạo
        calculateTotals();

        // Hàm kiểm tra form trước khi submit
        window.validateForm = function() {
            // Đếm số lượng dịch vụ và sản phẩm
            let serviceCount = 0;
            let productCount = 0;

            // Đảm bảo rằng các trường ẩn được cập nhật đúng
            $('.invoice-item').each(function(index) {
                const row = $(this);
                const type = row.find('.item-type-input').val();
                const price = row.find('.item-price').val();
                const quantity = row.find('.item-quantity').val();

                // Cập nhật index cho các trường để tránh trùng lập
                if (type === 'service') {
                    // Xóa các trường product nếu có
                    row.find('input[name^="product_"]').remove();

                    // Lấy giá trị của service select
                    const serviceSelect = row.find('.item-select[data-type="service"]');
                    const serviceId = serviceSelect.val();

                    // Chỉ xử lý nếu có dịch vụ được chọn
                    if (serviceId && serviceId !== '') {
                        // Cập nhật giá trị cho các trường service
                        serviceSelect.attr('name', `service_ids[${serviceCount}]`);

                        // Tạo hoặc cập nhật các trường ẩn
                        let servicePrice = row.find('.service-price');
                        let serviceQuantity = row.find('.service-quantity');

                        if (servicePrice.length === 0) {
                            row.find('td:eq(1)').append(`<input type="hidden" name="service_prices[${serviceCount}]" value="${price}" class="service-price">`);
                        } else {
                            servicePrice.attr('name', `service_prices[${serviceCount}]`).val(price);
                        }

                        if (serviceQuantity.length === 0) {
                            row.find('td:eq(1)').append(`<input type="hidden" name="service_quantities[${serviceCount}]" value="${quantity}" class="service-quantity">`);
                        } else {
                            serviceQuantity.attr('name', `service_quantities[${serviceCount}]`).val(quantity);
                        }

                        // Tăng số lượng dịch vụ
                        serviceCount++;
                    }
                } else if (type === 'product') {
                    // Xóa các trường service nếu có
                    row.find('input[name^="service_"]').remove();

                    // Lấy giá trị của product select
                    const productSelect = row.find('.item-select[data-type="product"]');
                    const productId = productSelect.val();

                    // Chỉ xử lý nếu có sản phẩm được chọn
                    if (productId && productId !== '') {
                        // Cập nhật giá trị cho các trường product
                        productSelect.attr('name', `product_ids[${productCount}]`);

                        // Tạo hoặc cập nhật các trường ẩn
                        let productPrice = row.find('.product-price');
                        let productQuantity = row.find('.product-quantity');

                        if (productPrice.length === 0) {
                            row.find('td:eq(1)').append(`<input type="hidden" name="product_prices[${productCount}]" value="${price}" class="product-price">`);
                        } else {
                            productPrice.attr('name', `product_prices[${productCount}]`).val(price);
                        }

                        if (productQuantity.length === 0) {
                            row.find('td:eq(1)').append(`<input type="hidden" name="product_quantities[${productCount}]" value="${quantity}" class="product-quantity">`);
                        } else {
                            productQuantity.attr('name', `product_quantities[${productCount}]`).val(quantity);
                        }

                        // Tăng số lượng sản phẩm
                        productCount++;
                    }
                }
            });

            // Cập nhật lại tổng tiền
            calculateTotals();

            // Hiển thị thông tin debug
            console.log('Form data:', $('#invoiceForm').serialize());
            console.log('Service count:', serviceCount);
            console.log('Product count:', productCount);

            return true;
        };
    });
</script>
@endsection