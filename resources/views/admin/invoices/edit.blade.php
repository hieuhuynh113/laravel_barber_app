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

    <form action="{{ route('admin.invoices.update', $invoice->id) }}" method="POST" id="invoiceForm" class="invoice-form">
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
                                    @foreach($invoice->items as $index => $item)
                                    <tr class="invoice-item">
                                        <td>
                                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                            <select class="form-select item-type" name="items[{{ $index }}][type]">
                                                <option value="service" {{ $item->type == 'service' ? 'selected' : '' }}>Dịch vụ</option>
                                                <option value="product" {{ $item->type == 'product' ? 'selected' : '' }}>Sản phẩm</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select item-select" name="items[{{ $index }}][item_id]" data-type="service" {{ $item->type == 'service' ? '' : 'style=display:none;' }}>
                                                <option value="">-- Chọn dịch vụ --</option>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" data-price="{{ $service->price }}" {{ $item->type == 'service' && $item->item_id == $service->id ? 'selected' : '' }}>
                                                        {{ $service->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <select class="form-select item-select" name="items[{{ $index }}][item_id]" data-type="product" {{ $item->type == 'product' ? '' : 'style=display:none;' }}>
                                                <option value="">-- Chọn sản phẩm --</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ $item->type == 'product' && $item->item_id == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control item-price" name="items[{{ $index }}][price]" value="{{ $item->price }}" min="0">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control item-quantity" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="1">
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
                                <option value="transfer" {{ $invoice->payment_method == 'transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="payment_status">Tình trạng thanh toán</label>
                            <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status">
                                <option value="0" {{ !$invoice->payment_status ? 'selected' : '' }}>Chưa thanh toán</option>
                                <option value="1" {{ $invoice->payment_status ? 'selected' : '' }}>Đã thanh toán</option>
                            </select>
                            @error('payment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="status">Trạng thái hóa đơn</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="pending" {{ $invoice->status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="confirmed" {{ $invoice->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
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

            row.find('.item-select').hide();
            row.find(`.item-select[data-type="${type}"]`).show();

            updateRowTotal(row);
            calculateTotals();
        });

        // Xử lý khi chọn sản phẩm/dịch vụ
        $(document).on('change', '.item-select', function() {
            if (!$(this).is(':visible')) return;

            const row = $(this).closest('tr');
            const selectedOption = $(this).find('option:selected');
            const price = selectedOption.data('price') || 0;

            row.find('.item-price').val(price);
            updateRowTotal(row);
            calculateTotals();
        });

        // Xử lý khi thay đổi giá hoặc số lượng
        $(document).on('input', '.item-price, .item-quantity', function() {
            const row = $(this).closest('tr');
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
        let itemCount = {{ count($invoice->items) }};
        $('#addItem').click(function() {
            itemCount++;
            const newRow = `
                <tr class="invoice-item">
                    <td>
                        <select class="form-select item-type" name="items[${itemCount}][type]">
                            <option value="service">Dịch vụ</option>
                            <option value="product">Sản phẩm</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select item-select" name="items[${itemCount}][item_id]" data-type="service">
                            <option value="">-- Chọn dịch vụ --</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" data-price="{{ $service->price }}">
                                    {{ $service->name }}
                                </option>
                            @endforeach
                        </select>
                        <select class="form-select item-select" name="items[${itemCount}][item_id]" data-type="product" style="display: none;">
                            <option value="">-- Chọn sản phẩm --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control item-price" name="items[${itemCount}][price]" value="0" min="0">
                    </td>
                    <td>
                        <input type="number" class="form-control item-quantity" name="items[${itemCount}][quantity]" value="1" min="1">
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
            $('#invoice-items').append(newRow);
        });

        // Xóa sản phẩm/dịch vụ
        $(document).on('click', '.remove-item', function() {
            if ($('.invoice-item').length > 1) {
                $(this).closest('tr').remove();
                calculateTotals();
            } else {
                alert('Hóa đơn cần ít nhất một sản phẩm hoặc dịch vụ');
            }
        });

        // Khởi tạo
        calculateTotals();
    });
</script>
@endsection