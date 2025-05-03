/**
 * Tạo HTML cho modal hóa đơn
 *
 * @param {Object} invoice Dữ liệu hóa đơn
 * @returns {string} HTML cho modal hóa đơn
 */
function generateInvoiceModalHtml(invoice) {
    // Xác định trạng thái thanh toán
    const paymentStatusHtml = invoice.payment_status === 'paid'
        ? `<span class="badge bg-success p-2"><i class="fas fa-check-circle me-1"></i> Đã thanh toán</span>`
        : `<span class="badge bg-warning p-2"><i class="fas fa-clock me-1"></i> Chưa thanh toán</span>`;

    // Xác định phương thức thanh toán
    let paymentMethod = 'Không xác định';
    if (invoice.payment_method === 'cash') {
        paymentMethod = 'Tiền mặt';
    } else if (invoice.payment_method === 'bank_transfer') {
        paymentMethod = 'Chuyển khoản';
    }

    // Tạo HTML cho danh sách dịch vụ
    let servicesHtml = '';
    invoice.services.forEach(function(service) {
        servicesHtml += `
            <tr>
                <td>${service.name}</td>
                <td class="text-center">${service.quantity}</td>
                <td class="text-end">${service.price_formatted}</td>
                <td class="text-end">${service.subtotal_formatted}</td>
            </tr>
        `;
    });

    // Tạo HTML cho danh sách sản phẩm nếu có
    let productsHtml = '';
    let productsSection = '';
    if (invoice.products && invoice.products.length > 0) {
        invoice.products.forEach(function(product) {
            productsHtml += `
                <tr>
                    <td>${product.name}</td>
                    <td class="text-center">${product.quantity}</td>
                    <td class="text-end">${product.price_formatted}</td>
                    <td class="text-end">${product.subtotal_formatted}</td>
                </tr>
            `;
        });

        productsSection = `
            <h5 class="mb-3">Sản phẩm</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${productsHtml}
                    </tbody>
                </table>
            </div>
        `;
    }

    // Tạo HTML cho giảm giá nếu có
    let discountRow = '';
    if (invoice.discount > 0) {
        discountRow = `
            <div class="d-flex justify-content-between mb-2">
                <span>Giảm giá:</span>
                <span>${invoice.discount_formatted}</span>
            </div>
        `;
    }

    // Tạo HTML cho thuế nếu có
    let taxRow = '';
    if (invoice.tax > 0) {
        taxRow = `
            <div class="d-flex justify-content-between mb-2">
                <span>Thuế:</span>
                <span>${invoice.tax_formatted}</span>
            </div>
        `;
    }

    // Tạo HTML cho ghi chú nếu có
    let notesSection = '';
    if (invoice.notes) {
        notesSection = `
            <div class="mb-0">
                <h5 class="mb-2">Ghi chú</h5>
                <p class="mb-0">${invoice.notes}</p>
            </div>
        `;
    }

    // Tạo HTML cho thông tin lịch hẹn
    let bookingCode = 'N/A';
    let appointmentDate = 'N/A';
    if (invoice.appointment) {
        bookingCode = invoice.appointment.booking_code;
        appointmentDate = invoice.appointment.appointment_date + ' - ' + invoice.appointment.time_slot;
    }

    // Tạo HTML cho modal
    return `
        <div class="invoice-modal-content p-2">
            <div class="invoice-header mb-4 p-3 bg-light rounded">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="mb-1 text-primary">Hóa đơn #${invoice.invoice_code}</h4>
                        <p class="text-muted mb-0">Ngày tạo: ${invoice.created_at}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        ${paymentStatusHtml}
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Thông tin khách hàng</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><i class="fas fa-user me-2 text-primary"></i> <strong>Tên:</strong> ${invoice.customer_name}</p>
                            <p class="mb-2"><i class="fas fa-envelope me-2 text-primary"></i> <strong>Email:</strong> ${invoice.customer_email}</p>
                            <p class="mb-0"><i class="fas fa-phone me-2 text-primary"></i> <strong>Điện thoại:</strong> ${invoice.customer_phone}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Thông tin thanh toán</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><i class="fas fa-money-bill-wave me-2 text-primary"></i> <strong>Phương thức:</strong> ${paymentMethod}</p>
                            <p class="mb-2"><i class="fas fa-ticket-alt me-2 text-primary"></i> <strong>Mã lịch hẹn:</strong> ${bookingCode}</p>
                            <p class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i> <strong>Ngày hẹn:</strong> ${appointmentDate}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Dịch vụ</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Dịch vụ</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${servicesHtml}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            ${productsSection.replace('<h5 class="mb-3">Sản phẩm</h5>', '<div class="card mb-4"><div class="card-header bg-primary text-white"><h5 class="mb-0">Sản phẩm</h5></div><div class="card-body p-0">')}
            ${productsSection ? '</div></div>' : ''}

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tổng kết thanh toán</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tổng tiền dịch vụ:</span>
                        <span>${invoice.subtotal_formatted}</span>
                    </div>
                    ${discountRow}
                    ${taxRow}
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Tổng thanh toán:</span>
                        <span class="text-danger fs-5">${invoice.total_formatted}</span>
                    </div>
                </div>
            </div>

            ${notesSection ? `<div class="card mb-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Ghi chú</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">${invoice.notes}</p>
                </div>
            </div>` : ''}
        </div>
    `;
}
