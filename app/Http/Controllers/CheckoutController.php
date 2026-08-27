<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $cartItems = session()->get('cart', []);

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $total = $subtotal; // Total awal sebelum ongkir dipilih

        return view('checkout', compact('settings', 'cartItems', 'subtotal', 'total'));
    }

    public function checkRates(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $cartItems = session()->get('cart', []);
        if (empty($cartItems)) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong'], 400);
        }

        // Hitung total berat (gram) dan total harga barang dari keranjang
        $totalWeight = 0;
        $totalValue = 0;
        foreach ($cartItems as $item) {
            $weight = $item['weight'] ?? 1000;
            $totalWeight += $weight * $item['quantity'];
            $totalValue += $item['price'] * $item['quantity'];
        }

        // Panggil API Biteship untuk cek tarif kurir Reguler & Instant
        $response = Http::withToken(env('BITESHIP_API_KEY'))
            ->post('https://api.biteship.com/v1/rates/couriers', [
                'origin_latitude' => -6.175392,
                'origin_longitude' => 106.827153,
                'destination_latitude' => (float) $request->latitude,
                'destination_longitude' => (float) $request->longitude,
                'couriers' => 'jne,sicepat,jnt,gojek,grab',
                'items' => [
                    [
                        'name' => 'Belanjaan Furni',
                        'description' => 'Produk Toko Furni',
                        'value' => (int) $totalValue,
                        'weight' => (int) $totalWeight,
                        'quantity' => 1,
                    ]
                ]
            ]);

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil tarif dari Biteship. Periksa kembali API Key Anda.'
            ], 400);
        }

        $biteshipData = $response->json();
        $pricing = [];

        // Mapping hasil respon Biteship agar sesuai dengan dropdown di frontend kamu
        if (isset($biteshipData['pricing'])) {
            foreach ($biteshipData['pricing'] as $rate) {
                $pricing[] = [
                    'courier_name' => strtoupper($rate['courier_code']),
                    'courier_service_name' => $rate['courier_service_name'] ?? $rate['service_name'],
                    'price' => (int) $rate['price'],
                    'shipment_duration' => $rate['duration'] ?? '1-3 hari'
                ];
            }
        }

        return response()->json([
            'success' => true,
            'pricing' => $pricing
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'country' => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string',
            'state_country' => 'required|string|max:255',
            'postal_zip' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:bank,qr',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'shipping_cost' => 'required|numeric|min:0',
            'shipping_courier' => 'required|string',
        ]);

        $cartItems = session()->get('cart', []);
        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            $shippingCost = $request->shipping_cost;
            $totalPrice = $subtotal + $shippingCost;

            // 1. Validasi Atomic & Lock Row Produk
            foreach ($cartItems as $id => $item) {
                $product = Product::lockForUpdate()->find($id);

                if (!$product) {
                    throw new \Exception("Produk '{$item['name']}' sudah tidak tersedia.");
                }

                // Hitung ulang stok efektif secara akurat di dalam database lock
                $reservedStock = \App\Models\LimitStock::where('product_id', $product->id)
                    ->where('expires_at', '>', now())
                    ->sum('quantity');

                $effectiveStock = $product->stock - $reservedStock;

                if ($effectiveStock < $item['quantity']) {
                    throw new \Exception("Stok produk '{$product->name}' baru saja habis atau tidak mencukupi.");
                }
            }


            // 2. Buat Order
            $order = Order::create([
                'user_id' => auth()->id(),
                'country' => $request->country,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'company_name' => $request->company_name,
                'address' => $request->address,
                'apartment' => $request->apartment,
                'state_country' => $request->state_country,
                'postal_zip' => $request->postal_zip,
                'email' => $request->email,
                'phone' => $request->phone,
                'payment_method' => $request->payment_method,
                'order_notes' => $request->order_notes,
                'shipping_cost' => $shippingCost,
                'shipping_courier' => $request->shipping_courier,
                'total_price' => $totalPrice,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'status' => 'pending',
            ]);

            // SIMPAN DETAIL ITEM KE ORDER_ITEMS
            foreach ($cartItems as $id => $item) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Masukkan ke limit_stocks (Hold Stok)
                \App\Models\LimitStock::create([
                    'user_id' => auth()->id(),
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'order_id' => $order->id,
                    'expires_at' => now()->addMinutes(30),
                ]);
            }

            session()->forget('cart');
            DB::commit();

            return redirect()->route('shop')->with('success', 'Checkout berhasil! Silakan selesaikan pembayaran dalam waktu 30 menit.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}