<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

// Service Routes
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

// Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Price List Route
Route::get('/price-list', [PriceController::class, 'index'])->name('price.index');

// News Routes
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

// Contact Routes
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Appointment Routes
Route::prefix('appointment')->name('appointment.')->middleware(\App\Http\Middleware\CheckAppointmentAuth::class)->group(function () {
    Route::get('/step-1', [AppointmentController::class, 'step1'])->name('step1');
    Route::post('/step-1', [AppointmentController::class, 'postStep1'])->name('post.step1');

    Route::get('/step-2', [AppointmentController::class, 'step2'])->name('step2');
    Route::post('/step-2', [AppointmentController::class, 'postStep2'])->name('post.step2');

    Route::get('/step-3', [AppointmentController::class, 'step3'])->name('step3');
    Route::post('/step-3', [AppointmentController::class, 'postStep3'])->name('post.step3');
    Route::post('/check-availability', [AppointmentController::class, 'checkAvailability'])->name('check-availability');

    Route::get('/step-4', [AppointmentController::class, 'step4'])->name('step4');
    Route::post('/step-4', [AppointmentController::class, 'postStep4'])->name('post.step4');

    Route::get('/step-5', [AppointmentController::class, 'step5'])->name('step5');
    Route::post('/step-5', [AppointmentController::class, 'postStep5'])->name('post.step5');

    Route::get('/step-6', [AppointmentController::class, 'step6'])->name('step6');
    Route::get('/complete', [AppointmentController::class, 'complete'])->name('complete');
    Route::post('/complete', [AppointmentController::class, 'complete'])->name('post.complete');

    // Thanh toán chuyển khoản
    Route::get('/payment-confirmation/{id}', [AppointmentController::class, 'paymentConfirmation'])->name('payment.confirmation');
    Route::post('/upload-receipt/{id}', [AppointmentController::class, 'uploadReceipt'])->name('upload-receipt');
});

// Auth Routes - Chỉ giữ lại các route cần thiết
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::post('/password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
Route::get('/password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');

// Email Verification Routes
Route::post('/register/verify', [\App\Http\Controllers\EmailVerificationController::class, 'sendOTP'])->name('verification.send');
Route::get('/register/verify', [\App\Http\Controllers\EmailVerificationController::class, 'showVerificationForm'])->name('verification.form');
Route::post('/register/verify/confirm', [\App\Http\Controllers\EmailVerificationController::class, 'verifyOTP'])->name('verification.verify');
Route::post('/register/verify/resend', [\App\Http\Controllers\EmailVerificationController::class, 'resendOTP'])->name('verification.resend');

// Redirect login/register to home with modal
Route::get('/login', function() {
    return redirect()->route('home', ['auth' => 'login']);
})->name('login');

Route::get('/register', function() {
    return redirect()->route('home', ['auth' => 'register']);
})->name('register');

// Admin Login Routes
Route::get('/admin/login', [\App\Http\Controllers\Auth\AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [\App\Http\Controllers\Auth\AdminLoginController::class, 'login'])->name('admin.login.submit');

// Barber Login Routes
Route::get('/barber/login', [\App\Http\Controllers\Auth\BarberLoginController::class, 'showLoginForm'])->name('barber.login');
Route::post('/barber/login', [\App\Http\Controllers\Auth\BarberLoginController::class, 'login'])->name('barber.login.submit');

// Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::get('/profile/appointments', [ProfileController::class, 'appointments'])->name('profile.appointments');
    Route::get('/profile/appointment/{id}', [ProfileController::class, 'appointmentDetail'])->name('profile.appointment.detail');
    Route::get('/profile/reviews', [ProfileController::class, 'reviews'])->name('profile.reviews');
    Route::post('/profile/reviews', [ProfileController::class, 'storeReview'])->name('profile.reviews.store');
    Route::post('/appointment/cancel/{id}', [ProfileController::class, 'cancelAppointment'])->name('appointment.cancel');
});

// Admin Routes
// Route riêng cho /admin để xử lý chuyển hướng đúng
Route::get('/admin', function() {
    if (!auth()->check()) {
        return redirect('/admin/login')->with('error', 'Vui lòng đăng nhập để tiếp tục');
    }

    if (auth()->user()->role !== 'admin') {
        return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập vào trang này');
    }

    return app()->make(\App\Http\Controllers\Admin\DashboardController::class)->index();
});

// Các route admin khác
Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->withoutMiddleware(['auth']);

    // Admin Profile Routes
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/change-password', [\App\Http\Controllers\Admin\ProfileController::class, 'changePassword'])->name('profile.change-password');

    // Category Routes
    Route::resource('categories', CategoryController::class);

    // Service Routes
    Route::resource('services', AdminServiceController::class);

    // Product Routes
    Route::resource('products', AdminProductController::class);

    // Barber Routes
    Route::resource('barbers', \App\Http\Controllers\Admin\BarberController::class);

    // Barber Schedule Routes
    Route::resource('schedules', \App\Http\Controllers\Admin\BarberScheduleController::class);
    Route::post('schedules/batch-update', [\App\Http\Controllers\Admin\BarberScheduleController::class, 'updateBatch'])->name('schedules.batch-update');

    // Schedule Change Request Routes
    Route::resource('schedule-requests', \App\Http\Controllers\Admin\ScheduleChangeRequestController::class)->only(['index', 'show', 'destroy']);
    Route::post('schedule-requests/{id}/approve', [\App\Http\Controllers\Admin\ScheduleChangeRequestController::class, 'approve'])->name('schedule-requests.approve');
    Route::post('schedule-requests/{id}/reject', [\App\Http\Controllers\Admin\ScheduleChangeRequestController::class, 'reject'])->name('schedule-requests.reject');
    Route::post('schedule-requests/bulk-delete', [\App\Http\Controllers\Admin\ScheduleChangeRequestController::class, 'bulkDelete'])->name('schedule-requests.bulk-delete');

    // Time Slot Routes
    Route::get('time-slots', [\App\Http\Controllers\Admin\TimeSlotController::class, 'index'])->name('time-slots.index');
    Route::put('time-slots/{id}', [\App\Http\Controllers\Admin\TimeSlotController::class, 'update'])->name('time-slots.update');
    Route::post('time-slots/bulk-update', [\App\Http\Controllers\Admin\TimeSlotController::class, 'bulkUpdate'])->name('time-slots.bulk-update');
    Route::post('time-slots/generate', [\App\Http\Controllers\Admin\TimeSlotController::class, 'generate'])->name('time-slots.generate');

    // Appointment Routes
    Route::resource('appointments', \App\Http\Controllers\Admin\AppointmentController::class);
    Route::post('appointments/{appointment}/update-status', [\App\Http\Controllers\Admin\AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');

    // Invoice Routes
    Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class);
    Route::get('invoices/{invoice}/print', [\App\Http\Controllers\Admin\InvoiceController::class, 'print'])->name('invoices.print');
    Route::get('invoices-statistics', [\App\Http\Controllers\Admin\InvoiceController::class, 'statistics'])->name('invoices.statistics');
    Route::patch('invoices/{invoice}/update-status', [\App\Http\Controllers\Admin\InvoiceController::class, 'updateStatus'])->name('invoices.update-status');
    Route::get('invoices/{invoice}/send-email', [\App\Http\Controllers\Admin\InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
    Route::post('invoices/{invoice}/cancel', [\App\Http\Controllers\Admin\InvoiceController::class, 'cancelInvoice'])->name('invoices.cancel');

    // Payment Receipt Routes
    Route::resource('payment-receipts', \App\Http\Controllers\Admin\PaymentReceiptController::class)->only(['index', 'show', 'destroy']);
    Route::post('payment-receipts/{id}/update-status', [\App\Http\Controllers\Admin\PaymentReceiptController::class, 'updateStatus'])->name('payment-receipts.update-status');

    // User Routes
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

    // News Routes
    Route::resource('news', \App\Http\Controllers\Admin\NewsController::class);
    Route::post('news/{news}/toggle-featured', [\App\Http\Controllers\Admin\NewsController::class, 'toggleFeatured'])->name('news.toggleFeatured');

    // Review Routes
    Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class);
    Route::get('reviews-statistics', [\App\Http\Controllers\Admin\ReviewController::class, 'statistics'])->name('reviews.statistics');
    Route::post('reviews/{review}/toggle-status', [\App\Http\Controllers\Admin\ReviewController::class, 'toggleStatus'])->name('reviews.toggleStatus');

    // Notification Routes
    Route::get('notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/mark-as-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('notifications/mark-all-as-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // Image Upload for CKEditor
    Route::post('upload/image', [\App\Http\Controllers\Admin\UploadController::class, 'uploadImage'])->name('upload.image');

    // Contact Routes
    Route::resource('contacts', \App\Http\Controllers\Admin\ContactController::class)->except(['create', 'store', 'edit', 'update']);
    Route::post('contacts/{contact}/reply', [\App\Http\Controllers\Admin\ContactController::class, 'reply'])->name('contacts.reply');
    Route::post('contacts/{contact}/mark-as-read', [\App\Http\Controllers\Admin\ContactController::class, 'markAsRead'])->name('contacts.markAsRead');
    Route::post('contacts/{contact}/mark-as-unread', [\App\Http\Controllers\Admin\ContactController::class, 'markAsUnread'])->name('contacts.markAsUnread');
    Route::post('contacts/bulk-action', [\App\Http\Controllers\Admin\ContactController::class, 'bulkAction'])->name('contacts.bulkAction');

    // Setting Routes đã bị xóa
});

// Barber Routes
// Route riêng cho /barber để xử lý chuyển hướng đúng
Route::get('/barber', function() {
    if (!auth()->check()) {
        return redirect('/barber/login')->with('error', 'Vui lòng đăng nhập để tiếp tục');
    }

    if (auth()->user()->role !== 'barber' && auth()->user()->role !== 'admin') {
        return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập vào trang này');
    }

    return app()->make(\App\Http\Controllers\Barber\DashboardController::class)->index();
});

// Thêm route POST cho /barber để xử lý trường hợp form submit sai URL
Route::post('/barber', function() {
    return redirect('/barber')->with('error', 'Có lỗi xảy ra khi xử lý yêu cầu. Vui lòng thử lại.');
});

// Các route barber khác
Route::prefix('barber')->name('barber.')->middleware(['auth', \App\Http\Middleware\BarberMiddleware::class])->group(function () {
    Route::get('/', [\App\Http\Controllers\Barber\DashboardController::class, 'index'])->name('dashboard')->withoutMiddleware(['auth']);

    // Quản lý lịch hẹn
    Route::get('/appointments', [\App\Http\Controllers\Barber\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [\App\Http\Controllers\Barber\AppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/complete', [\App\Http\Controllers\Barber\AppointmentController::class, 'markAsCompleted'])->name('appointments.complete');
    Route::post('/appointments/{appointment}/confirm', [\App\Http\Controllers\Barber\AppointmentController::class, 'confirmAppointment'])->name('appointments.confirm');

    // Quản lý lịch làm việc
    Route::get('/schedules', [\App\Http\Controllers\Barber\ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules/request-change', [\App\Http\Controllers\Barber\ScheduleController::class, 'requestChange'])->name('schedules.request-change');

    // Quản lý thông tin cá nhân
    Route::get('/profile', [\App\Http\Controllers\Barber\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [\App\Http\Controllers\Barber\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [\App\Http\Controllers\Barber\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [\App\Http\Controllers\Barber\ProfileController::class, 'changePasswordForm'])->name('profile.change-password-form');
    Route::put('/profile/change-password', [\App\Http\Controllers\Barber\ProfileController::class, 'changePassword'])->name('profile.change-password');

    // Quản lý thông báo
    Route::get('/notifications', [\App\Http\Controllers\Barber\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [\App\Http\Controllers\Barber\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [\App\Http\Controllers\Barber\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');

    // Quản lý hóa đơn
    Route::get('/invoices/{invoice}', [\App\Http\Controllers\Barber\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/print', [\App\Http\Controllers\Barber\InvoiceController::class, 'print'])->name('invoices.print');
    Route::get('/invoices/{invoice}/data', [\App\Http\Controllers\Barber\InvoiceController::class, 'getInvoiceData'])->name('invoices.data');
});

// Review routes
Route::middleware(['auth'])->group(function () {
    Route::get('/my-reviews', [ReviewController::class, 'userReviews'])->name('user.reviews');
    Route::resource('reviews', ReviewController::class);
});
