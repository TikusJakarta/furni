<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\LimitStock;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * 1. Fungsi saat pembayaran berhasil (Settlement)
     */
    public function markAsPaid($orderId)
    {
        DB::beginTransaction();
        try {
            // Cari pesanan beserta item produknya (pastikan relasi 'items' sudah ada di Model Order)
            $order = Order::with('items')->findOrFail($orderId);
            
            if ($order->status === 'paid') {
                return response()->json(['message' => 'Pesanan sudah dibayar sebelumnya.']);
            }

            // Ubah status order jadi paid
            $order->update(['status' => 'paid']);

            // Kurangi stok fisik di tabel products secara permanen
            foreach ($order->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if ($product) {
                    $product->stock -= $item->quantity;
                    $product->save();
                }
            }

            // Hapus data limit_stocks karena stok fisik sudah dipotong permanen
            LimitStock::where('order_id', $order->id)->delete();

            DB::commit();
            return response()->json(['message' => 'Pembayaran berhasil dikonfirmasi dan stok diperbarui.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * 2. Fungsi saat pesanan dibatalkan atau kadaluarsa (Release Stok)
     */
    public function cancelOrder($orderId)
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($orderId);
            
            if ($order->status === 'cancelled') {
                return response()->json(['message' => 'Pesanan sudah dibatalkan sebelumnya.']);
            }

            $order->update(['status' => 'cancelled']);

            // Hapus limit_stocks agar stok kembali bebas/bisa dibeli orang lain
            LimitStock::where('order_id', $orderId)->delete();

            DB::commit();
            return response()->json(['message' => 'Pesanan dibatalkan dan stok dikembalikan ke sistem.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}