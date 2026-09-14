<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Order;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sistem Auto-Cancel Pesanan Belum Bayar (24 Jam)
Schedule::call(function () {
    $expiredOrders = Order::where('status', 'pending')
        ->where('created_at', '<=', now()->subHours(24))
        ->with('orderItems.product')
        ->get();

    foreach ($expiredOrders as $order) {
        $order->update(['status' => 'cancelled']);

        foreach ($order->orderItems as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }
    }
})->hourly();