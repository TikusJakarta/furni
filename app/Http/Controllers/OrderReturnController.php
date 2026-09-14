<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderReturn;

class OrderReturnController extends Controller
{
    // Simpan pengajuan retur dari user
    public function store(Request $request, $orderId)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
        ]);

        $order = Order::where('id', $orderId)->where('user_id', auth()->id())->firstOrFail();

        if (!in_array($order->status, ['completed', 'shipped'])) {
            return redirect()->back()->with('error', 'Pesanan ini belum dapat diajukan retur.');
        }

        $imagePath = $request->file('proof_image')->store('return-proofs', 'public');

        OrderReturn::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'reason' => $request->reason,
            'proof_image' => $imagePath,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Pengajuan retur berhasil dikirim dan sedang menunggu konfirmasi admin.');
    }
}