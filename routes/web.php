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
use App\Http\Controllers\OrderController; // 1. Tambahkan import Controller ini

// RUTE PUBLIK
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/services', [ServicesController::class, 'index'])->name('services');
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/contact', function () { return view('contact'); })->name('contact');
Route::get('/product/{id}', [ShopController::class, 'show'])->name('product.show');

// Rute Autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');


// RUTE KHUSUS MEMBER (Harus Login Dulu)
Route::middleware(['auth'])->group(function () {
    
    // Rute Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::get('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add'); 
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    // Rute Checkout & Thankyou
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/check-shipping-rates', [CheckoutController::class, 'checkRates']);
    Route::get('/thankyou', function () { return view('thankyou'); })->name('thankyou');

    // 2. Rute Manajemen Order (Settlement & Cancel/Release Stok)
    Route::post('/order/{id}/pay', [OrderController::class, 'markAsPaid'])->name('order.pay');
    Route::post('/order/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('order.cancel');

    // Logout 
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
});