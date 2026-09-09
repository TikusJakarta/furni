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
            $order = Order::with('items')->findOrFail($orderId);

            if ($order->status === 'paid') {
                return redirect()->route('user.dashboard')->with('info', 'Pesanan ini sudah dibayar sebelumnya.');
            }

            $order->update(['status' => 'paid']);

            foreach ($order->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if ($product) {
                    $product->stock -= $item->quantity;
                    $product->save();
                }
            }

            LimitStock::where('order_id', $order->id)->delete();

            DB::commit();

            return redirect()->route('user.dashboard')->with('payment_success', 'Pembayaran berhasil dikonfirmasi dan stok diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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

            LimitStock::where('order_id', $orderId)->delete();

            DB::commit();
            return response()->json(['message' => 'Pesanan dibatalkan dan stok dikembalikan ke sistem.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan halaman instruksi pembayaran (QR / Transfer) untuk user
     */
    public function showPayment($id)
    {
        $order = Order::with('orderItems.product')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        if ($order->status !== 'pending') {
            return redirect()->route('user.dashboard')->with('error', 'Pesanan ini sudah dibayar atau selesai.');
        }

        return view('order.pay', compact('order'));
    }

    /**
     * 3. Fungsi untuk memproses upload bukti transfer bank (Maks. 8MB)
     */
    public function uploadProof(Request $request, $orderId)
{
    $request->validate([
        'proof_of_payment' => 'required|image|mimes:jpeg,png,jpg|max:8192',
    ]);

    DB::beginTransaction();
    try {
        $order = Order::findOrFail($orderId);

        if ($request->hasFile('proof_of_payment')) {
            $file = $request->file('proof_of_payment');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/proofs'), $filename);
            
            $order->proof_of_payment = 'uploads/proofs/' . $filename;
        }

        // Karena transfer bank, ubah status ke 'verifikasi' untuk dicek admin
        $order->status = 'verifikasi';
        $order->save();

        DB::commit();

        return redirect()->route('user.dashboard')->with('payment_success', 'Bukti transfer berhasil diunggah! Menunggu verifikasi dari Admin.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
    }
}
}   