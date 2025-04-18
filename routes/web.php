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

// Auth Routes
Auth::routes();

// Email Verification Routes
Route::post('/register/verify', [\App\Http\Controllers\EmailVerificationController::class, 'sendOTP'])->name('verification.send');
Route::get('/register/verify', [\App\Http\Controllers\EmailVerificationController::class, 'showVerificationForm'])->name('verification.form');
Route::post('/register/verify/confirm', [\App\Http\Controllers\EmailVerificationController::class, 'verifyOTP'])->name('verification.verify');
Route::post('/register/verify/resend', [\App\Http\Controllers\EmailVerificationController::class, 'resendOTP'])->name('verification.resend');

// Profile Routes
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/appointments', [ProfileController::class, 'appointments'])->name('appointments');
    Route::get('/reviews', [ProfileController::class, 'reviews'])->name('reviews');
    Route::delete('/reviews/{id}', [ProfileController::class, 'deleteReview'])->name('reviews.delete');
    Route::post('/reviews', [ProfileController::class, 'storeReview'])->name('reviews.store');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

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

    // Appointment Routes
    Route::resource('appointments', \App\Http\Controllers\Admin\AppointmentController::class);
    Route::post('appointments/{appointment}/update-status', [\App\Http\Controllers\Admin\AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');

    // Invoice Routes
    Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class);
    Route::get('invoices/{invoice}/print', [\App\Http\Controllers\Admin\InvoiceController::class, 'print'])->name('invoices.print');
    Route::get('invoices-statistics', [\App\Http\Controllers\Admin\InvoiceController::class, 'statistics'])->name('invoices.statistics');
    Route::patch('invoices/{invoice}/update-status', [\App\Http\Controllers\Admin\InvoiceController::class, 'updateStatus'])->name('invoices.update-status');
    Route::get('invoices/{invoice}/send-email', [\App\Http\Controllers\Admin\InvoiceController::class, 'sendEmail'])->name('invoices.send-email');

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

    // Setting Routes
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings/general', [\App\Http\Controllers\Admin\SettingController::class, 'updateGeneral'])->name('settings.updateGeneral');
    Route::post('settings/contact', [\App\Http\Controllers\Admin\SettingController::class, 'updateContact'])->name('settings.updateContact');
    Route::post('settings/social', [\App\Http\Controllers\Admin\SettingController::class, 'updateSocial'])->name('settings.updateSocial');
    Route::post('settings/seo', [\App\Http\Controllers\Admin\SettingController::class, 'updateSeo'])->name('settings.updateSeo');
});

// Barber Routes
Route::prefix('barber')->name('barber.')->middleware(['auth', \App\Http\Middleware\BarberMiddleware::class])->group(function () {
    Route::get('/', [\App\Http\Controllers\Barber\DashboardController::class, 'index'])->name('dashboard');
    // Thêm routes cho barber dashboard và quản lý lịch hẹn
});

// Review routes
Route::middleware(['auth'])->group(function () {
    Route::get('/my-reviews', [ReviewController::class, 'userReviews'])->name('user.reviews');
    Route::resource('reviews', ReviewController::class);
});
