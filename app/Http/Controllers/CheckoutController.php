<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $rawCart = session()->get('cart', []);

        if (empty($rawCart)) {
            return redirect()->route('shop')->with('swal_error', 'Keranjang belanjaan kamu masih kosong. Silakan pilih produk terlebih dahulu!');
        }

        $cartItems = [];
        $subtotal = 0;

        foreach ($rawCart as $id => $item) {
            // Ambil data produk asli dari database untuk memastikan berat & harga murni dari database
            $product = Product::find($id);

            $cartItems[$id] = [
                'name' => $product->name ?? ($item['name'] ?? 'Produk'),
                'price' => $product->price ?? ($item['price'] ?? 0),
                'quantity' => $item['quantity'] ?? 1,
                'weight' => $product->weight ?? ($item['weight'] ?? 0), // Berat murni dari database (gram)
                'image' => $product->image ?? ($item['image'] ?? ''),
            ];

            $subtotal += $cartItems[$id]['price'] * $cartItems[$id]['quantity'];
        }

        // Perhitungan Diskon Kupon
        $discount = 0;
        $coupon = session()->get('coupon');
        if ($coupon) {
            if ($coupon['type'] == 'percentage') {
                $discount = ($subtotal * $coupon['value']) / 100;
            } else {
                $discount = $coupon['value'];
            }
        }
        
        $discount = min($discount, $subtotal);
        $total = $subtotal - $discount;

        // Ambil alamat tersimpan milik user yang sedang login
        $userAddresses = auth()->check() ? auth()->user()->addresses()->latest()->get() : collect();

        return view('checkout', compact('settings', 'cartItems', 'subtotal', 'discount', 'total', 'coupon', 'userAddresses'));
    }

    // Simpan Alamat Baru dari Checkout
    public function storeAddress(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_zip' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        UserAddress::create([
            'user_id' => auth()->id(),
            'label' => $request->label,
            'recipient_name' => $request->recipient_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_zip,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['success' => true, 'message' => 'Alamat baru berhasil disimpan!']);
    }

    // Terapkan Kupon Promo
    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);

        $coupon = Coupon::where('code', $request->coupon_code)->first();

        if (!$coupon) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Kode promo / kupon tidak valid.'], 422);
            }
            return redirect()->back()->with('error', 'Kode promo / kupon tidak valid.');
        }

        if ($coupon->expires_at && now()->greaterThan($coupon->expires_at)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Kode promo / kupon sudah kedaluwarsa.'], 422);
            }
            return redirect()->back()->with('error', 'Kode promo / kupon sudah kedaluwarsa.');
        }

        $cartItems = session()->get('cart', []);
        $subtotal = 0;
        foreach ($cartItems as $id => $item) {
            $product = Product::find($id);
            $price = $product->price ?? ($item['price'] ?? 0);
            $subtotal += $price * $item['quantity'];
        }

        if ($subtotal < $coupon->min_spend) {
            $msg = 'Minimal belanja untuk kupon ini adalah Rp ' . number_format($coupon->min_spend, 0, ',', '.');
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        session()->put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kupon diskon berhasil digunakan!']);
        }
        return redirect()->back()->with('success', 'Kupon diskon berhasil digunakan!');
    }

    // Hapus Kupon Promo
    public function removeCoupon(Request $request)
    {
        session()->forget('coupon');
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kupon berhasil dihapus.']);
        }
        return redirect()->back()->with('success', 'Kupon berhasil dihapus.');
    }

    public function checkRates(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'city' => 'nullable|string', 
        ]);

        $rawCart = session()->get('cart', []);
        if (empty($rawCart)) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong'], 400);
        }

        $totalWeight = 0;
        $totalValue = 0;
        
        foreach ($rawCart as $id => $item) {
            $product = Product::find($id);
            // Ambil berat murni dari database produk
            $weight = $product->weight ?? ($item['weight'] ?? 0); 
            $price = $product->price ?? ($item['price'] ?? 0);
            $qty = $item['quantity'] ?? 1;

            $totalWeight += $weight * $qty;
            $totalValue += $price * $qty;
        }

        // --- FILTER KURIR PINTAR (BITESHIP + FALLBACK KOORDINAT) ---
        $city = strtolower($request->input('city', ''));
        $jabodetabekKeywords = ['jakarta', 'bogor', 'depok', 'tangerang', 'bekasi'];
        $isJabodetabek = false;

        foreach ($jabodetabekKeywords as $keyword) {
            if (str_contains($city, $keyword)) {
                $isJabodetabek = true;
                break;
            }
        }

        if (!$isJabodetabek && $request->latitude && $request->longitude) {
            $lat = (float) $request->latitude;
            $lng = (float) $request->longitude;
            
            if ($lat >= -6.65 && $lat <= -6.10 && $lng >= 106.50 && $lng <= 107.10) {
                $isJabodetabek = true;
            }
        }

        $couriers = $isJabodetabek 
            ? 'jne,sicepat,jnt,gojek,grab' 
            : 'jne,sicepat,jnt';

        $response = Http::withToken(env('BITESHIP_API_KEY'))
            ->post('https://api.biteship.com/v1/rates/couriers', [
                'origin_latitude' => -6.175392,
                'origin_longitude' => 106.827153,
                'destination_latitude' => (float) $request->latitude,
                'destination_longitude' => (float) $request->longitude,
                'couriers' => $couriers,
                'items' => [
                    [
                        'name' => 'Belanjaan Furni',
                        'description' => 'Produk Toko Furni',
                        'value' => (int) $totalValue,
                        'weight' => (int) max($totalWeight, 1), // Biteship minimal 1 gram
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

        if (isset($biteshipData['pricing'])) {
            foreach ($biteshipData['pricing'] as $rate) {
                $serviceName = $rate['courier_service_name'] ?? $rate['service_name'];
                $serviceLower = strtolower($serviceName);
                
                $type = (str_contains($serviceLower, 'instant') || str_contains($serviceLower, 'sameday')) ? 'instant' : 'reguler';

                $pricing[] = [
                    'courier_name' => strtoupper($rate['courier_code']),
                    'courier_service_name' => $serviceName,
                    'price' => (int) $rate['price'],
                    'shipment_duration' => $rate['duration'] ?? '1-3 hari',
                    'type' => $type
                ];
            }
        }

        return response()->json([
            'success' => true,
            'pricing' => $pricing,
            'is_jabodetabek' => $isJabodetabek
        ]);
    }

    public function process(Request $request)
    {
        if (auth()->check() && auth()->user()->status === 'suspended') {
            return redirect()->back()->with('error', 'Akun Anda sedang disuspend/ditangguhkan. Anda dapat menjelajahi website ini, tetapi tidak diizinkan untuk melakukan checkout.');
        }

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

        $rawCart = session()->get('cart', []);
        if (empty($rawCart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($rawCart as $id => $item) {
                $product = Product::find($id);
                $price = $product->price ?? ($item['price'] ?? 0);
                $subtotal += $price * $item['quantity'];
            }

            $discount = 0;
            $coupon = session()->get('coupon');
            if ($coupon) {
                $discount = ($coupon['type'] == 'percentage') ? ($subtotal * $coupon['value']) / 100 : $coupon['value'];
            }
            $subtotalAfterDiscount = max(0, $subtotal - $discount);

            $shippingCost = $request->shipping_cost;
            $totalPrice = $subtotalAfterDiscount + $shippingCost;

            foreach ($rawCart as $id => $item) {
                $product = Product::lockForUpdate()->find($id);

                if (!$product) {
                    throw new \Exception("Produk '{$item['name']}' sudah tidak tersedia.");
                }

                $reservedStock = \App\Models\LimitStock::where('product_id', $product->id)
                    ->where('expires_at', '>', now())
                    ->sum('quantity');

                $effectiveStock = $product->stock - $reservedStock;

                if ($effectiveStock < $item['quantity']) {
                    throw new \Exception("Stok produk '{$product->name}' baru saja habis atau tidak mencukupi.");
                }
            }

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

            foreach ($rawCart as $id => $item) {
                $product = Product::find($id);
                $price = $product->price ?? ($item['price'] ?? 0);

                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                ]);

                \App\Models\LimitStock::create([
                    'user_id' => auth()->id(),
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'order_id' => $order->id,
                    'expires_at' => now()->addMinutes(30),
                ]);
            }

            session()->forget(['cart', 'coupon']);
            DB::commit();

            return redirect()->route('shop')->with([
                'order_success_popup' => true,
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'total_price' => $totalPrice
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function userDashboard()
    {
        $userId = auth()->id();

        $orders = Order::with('orderItems.product')->where('user_id', $userId)->latest()->get();
        
        $pendingOrders = Order::with('orderItems.product')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->latest()->get();
        
        $processingOrders = Order::with('orderItems.product')
            ->where('user_id', $userId)
            ->whereIn('status', ['verifikasi', 'paid', 'packing', 'shipping', 'shipped'])
            ->latest()->get();
            
        $completedOrders = Order::with('orderItems.product')
            ->where('user_id', $userId)
            ->whereIn('status', ['completed', 'cancelled'])
            ->latest()->get();

        return view('user.dashboard', compact('orders', 'pendingOrders', 'processingOrders', 'completedOrders'));
    }
}