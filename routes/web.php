<?php

use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceBookingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\SuperAdmin\SalesReportController;
use App\Http\Controllers\SuperAdmin\SuperAdminAdminController;
use App\Http\Controllers\SuperAdmin\SuperAdminBookingController;
use App\Http\Controllers\SuperAdmin\SuperAdminCategoryController;
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\SuperAdminOrderController;
use App\Http\Controllers\SuperAdmin\SuperAdminPaymentController;
use App\Http\Controllers\SuperAdmin\SuperAdminProductController;
use App\Http\Controllers\SuperAdmin\SuperAdminReviewController;
use App\Http\Controllers\SuperAdmin\SuperAdminServiceController;
use App\Http\Controllers\SuperAdmin\SuperAdminUserController;
use App\Http\Controllers\SuperAdmin\SystemSettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/produk', [ProductController::class, 'index'])->name('produk.index');
Route::get('/produk/{produk}', [ProductController::class, 'show'])->name('produk.show');
Route::get('/kategori/{kategori}', [CategoryController::class, 'show'])->name('kategori.show');

Route::get('/jasa', [ServiceController::class, 'index'])->name('jasa.index');
Route::get('/jasa/{jasa}', [ServiceController::class, 'show'])->name('jasa.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| USER (pelanggan)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Keranjang
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Pesanan & pembayaran
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/payment/{order}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{order}', [PaymentController::class, 'store'])->name('payment.store');

    // Jasa pemasangan
    Route::get('/services', [ServiceBookingController::class, 'index'])->name('services.index');
    Route::get('/services/history', [ServiceBookingController::class, 'history'])->name('services.history');
    Route::get('/services/booking', [ServiceBookingController::class, 'create'])->name('services.booking');
    Route::post('/services/booking', [ServiceBookingController::class, 'store'])->name('services.booking.store');
    Route::get('/services/slots', [ServiceBookingController::class, 'slots'])->name('services.slots');

    // Ulasan & profil
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
    Route::patch('/payments/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');
    Route::patch('/payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');

    Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
    Route::post('/services', [AdminServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{service}', [AdminServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [AdminServiceController::class, 'destroy'])->name('services.destroy');

    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::patch('/bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');

    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/reply', [AdminReviewController::class, 'reply'])->name('reviews.reply');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', SuperAdminProductController::class)->except(['show']);
    Route::resource('categories', SuperAdminCategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('/orders', [SuperAdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [SuperAdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [SuperAdminOrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('/payments', [SuperAdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [SuperAdminPaymentController::class, 'show'])->name('payments.show');
    Route::patch('/payments/{payment}/verify', [SuperAdminPaymentController::class, 'verify'])->name('payments.verify');
    Route::patch('/payments/{payment}/reject', [SuperAdminPaymentController::class, 'reject'])->name('payments.reject');

    Route::get('/services', [SuperAdminServiceController::class, 'index'])->name('services.index');
    Route::post('/services', [SuperAdminServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{service}', [SuperAdminServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [SuperAdminServiceController::class, 'destroy'])->name('services.destroy');

    Route::get('/bookings', [SuperAdminBookingController::class, 'index'])->name('bookings.index');
    Route::patch('/bookings/{booking}', [SuperAdminBookingController::class, 'update'])->name('bookings.update');

    Route::get('/reviews', [SuperAdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/reply', [SuperAdminReviewController::class, 'reply'])->name('reviews.reply');
    Route::delete('/reviews/{review}', [SuperAdminReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/users', [SuperAdminUserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}', [SuperAdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [SuperAdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/admins', [SuperAdminAdminController::class, 'index'])->name('admins.index');
    Route::post('/admins', [SuperAdminAdminController::class, 'store'])->name('admins.store');
    Route::put('/admins/{admin}', [SuperAdminAdminController::class, 'update'])->name('admins.update');
    Route::delete('/admins/{admin}', [SuperAdminAdminController::class, 'destroy'])->name('admins.destroy');

    Route::get('/reports', [SalesReportController::class, 'index'])->name('reports.index');
    Route::get('/settings', [SystemSettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SystemSettingController::class, 'update'])->name('settings.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
