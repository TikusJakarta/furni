<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\OrderReturnController;

// RUTE PUBLIK
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/services', [ServicesController::class, 'index'])->name('services');
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/contact', function () {
    return view('contact');
})->name('contact');
Route::get('/product/{id}', [ShopController::class, 'show'])->name('product.show');

// Rute Autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// THROTTLE
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.process');



Route::post('/product/{id}/review', [ReviewController::class, 'store'])->name('product.review')->middleware('auth');

// RUTE KHUSUS MEMBER (Harus Login Dulu)
Route::middleware(['auth'])->group(function () {

    Route::get('/my-orders', [CheckoutController::class, 'userDashboard'])->name('user.dashboard');
    Route::post('/order/{id}/return', [OrderReturnController::class, 'store'])->name('order.return');

    // Rute Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::get('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    // Rute Checkout & Thankyou
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/check-shipping-rates', [CheckoutController::class, 'checkRates']);
    Route::post('/checkout/save-address', [App\Http\Controllers\CheckoutController::class, 'storeAddress'])->name('checkout.save_address');
    Route::post('/checkout/check-rates', [CheckoutController::class, 'checkRates']);
    Route::get('/thankyou', function () {
        return view('thankyou');
    })->name('thankyou');

    // Rute Kupon / Promo
    Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.applyCoupon');
    Route::get('/checkout/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.removeCoupon');

    // Rute Manajemen Order (Settlement & Cancel/Release Stok)
    Route::get('/order/{id}/pay', [OrderController::class, 'showPayment'])->name('order.pay');
    Route::post('/order/{id}/mark-paid', [OrderController::class, 'markAsPaid'])->name('order.markPaid');
    Route::post('/order/{id}/upload-proof', [OrderController::class, 'uploadProof'])->name('order.uploadProof');
    Route::post('/order/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('order.cancel');

    // Logout 
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    // RUTE KHUSUS ADMIN
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // CRUD Produk
        Route::get('/products', [AdminController::class, 'productsIndex'])->name('products.index');
        Route::get('/products/create', [AdminController::class, 'productsCreate'])->name('products.create');
        Route::post('/products', [AdminController::class, 'productsStore'])->name('products.store');
        Route::get('/products/{id}/edit', [AdminController::class, 'productsEdit'])->name('products.edit');
        Route::put('/products/{id}', [AdminController::class, 'productsUpdate'])->name('products.update');
        Route::delete('/products/{id}', [AdminController::class, 'productsDestroy'])->name('products.destroy');

        // Manajemen Pesanan Admin
        Route::get('/orders', [AdminController::class, 'ordersIndex'])->name('orders.index');
        Route::post('/orders/{id}/status', [AdminController::class, 'ordersUpdateStatus'])->name('orders.updateStatus');

        // Manajemen Kupon Promo Admin
        Route::get('/coupons', [AdminController::class, 'couponsIndex'])->name('coupons.index');
        Route::get('/coupons/create', [AdminController::class, 'couponsCreate'])->name('coupons.create');
        Route::post('/coupons', [AdminController::class, 'couponsStore'])->name('coupons.store');
        Route::delete('/coupons/{id}', [AdminController::class, 'couponsDestroy'])->name('coupons.destroy');

        // Manajemen Pengguna / User (Suspend, Banned, Active)
        Route::get('/users', [AdminController::class, 'usersIndex'])->name('users.index');
        Route::post('/users/{id}/status', [AdminController::class, 'usersUpdateStatus'])->name('users.updateStatus');

        // Manajemen Retur Admin (Diperbaiki di sini)
        Route::get('/returns', [AdminController::class, 'returnsIndex'])->name('returns.index');
        Route::post('/returns/{id}/update', [AdminController::class, 'returnsUpdateStatus'])->name('returns.update');

        // CMS Pengaturan Web
        Route::get('/settings', [AdminController::class, 'settingsIndex'])->name('settings.index');
        Route::post('/settings', [AdminController::class, 'settingsUpdate'])->name('settings.update');
    });
});