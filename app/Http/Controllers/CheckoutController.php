<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Product;
use App\Models\ProductVariant;
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

        foreach ($rawCart as $cartKey => $item) {
            $productId = $item['product_id'] ?? explode('_', $cartKey)[0];
            $product = Product::find($productId);

            // Ambil harga dan nama dari session cart (yang sudah membawa varian) atau fallback ke database
            $cartItems[$cartKey] = [
                'name' => $item['name'] ?? ($product->name ?? 'Produk'),
                'price' => $item['price'] ?? ($product->price ?? 0),
                'quantity' => $item['quantity'] ?? 1,
                'weight' => $product->weight ?? ($item['weight'] ?? 0),
                'image' => $product->image ?? ($item['image'] ?? ''),
            ];

            $subtotal += $cartItems[$cartKey]['price'] * $cartItems[$cartKey]['quantity'];
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

        $userAddresses = auth()->check() ? auth()->user()->addresses()->latest()->get() : collect();

        return view('checkout', compact('settings', 'cartItems', 'subtotal', 'discount', 'total', 'coupon', 'userAddresses'));
    }

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
        foreach ($cartItems as $cartKey => $item) {
            $productId = $item['product_id'] ?? explode('_', $cartKey)[0];
            $product = Product::find($productId);
            $price = $item['price'] ?? ($product->price ?? 0);
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
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'city' => 'nullable|string',
        ]);

        $rawCart = session()->get('cart', []);
        if (empty($rawCart)) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong'], 400);
        }

        $totalWeight = 0;
        $itemsPayload = [];

        foreach ($rawCart as $cartKey => $item) {
            $productId = $item['product_id'] ?? explode('_', $cartKey)[0];
            $product = Product::find($productId);
            $weight = $product->weight ?? ($item['weight'] ?? 1000);
            $price = $item['price'] ?? ($product->price ?? 0);
            $qty = $item['quantity'] ?? 1;

            $totalWeight += $weight * $qty;

            $itemsPayload[] = [
                'name' => $item['name'] ?? ($product->name ?? 'Produk'),
                'description' => 'Produk Furniture',
                'value' => (int) $price,
                'weight' => (int) $weight,
                'quantity' => (int) $qty,
            ];
        }

        $lat = $request->latitude ? (float) $request->latitude : -6.175392;
        $lng = $request->longitude ? (float) $request->longitude : 106.827153;

        $response = Http::withToken(env('BITESHIP_API_KEY'))
            ->post('https://api.biteship.com/v1/rates/couriers', [
                'origin_latitude' => -6.175392,
                'origin_longitude' => 106.827153,
                'destination_latitude' => $lat,
                'destination_longitude' => $lng,
                'couriers' => 'jne,sicepat,jnt,gojek,grab',
                'items' => $itemsPayload
            ]);

        if (!$response->successful()) {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil tarif dari Biteship.'], 400);
        }

        $biteshipData = $response->json();
        $pricing = [];

        $totalWeightKg = max(1, ceil($totalWeight / 1000));

        if (isset($biteshipData['pricing'])) {
            foreach ($biteshipData['pricing'] as $rate) {
                $serviceName = $rate['courier_service_name'] ?? $rate['service_name'];
                $serviceLower = strtolower($serviceName);
                $isInstant = str_contains($serviceLower, 'instant') || str_contains($serviceLower, 'sameday');
                $basePrice = (int) $rate['price'];

                $finalPrice = $isInstant ? $basePrice : ($basePrice * $totalWeightKg);

                $insuranceCost = 0;
                if (isset($rate['insurance'])) {
                    $insuranceCost = is_array($rate['insurance']) ? ($rate['insurance']['fee'] ?? 0) : (int) $rate['insurance'];
                } elseif (isset($rate['insurance_fee'])) {
                    $insuranceCost = (int) $rate['insurance_fee'];
                }

                $pricing[] = [
                    'courier_name' => strtoupper($rate['courier_code']),
                    'courier_service_name' => $serviceName,
                    'price' => $finalPrice,
                    'insurance_cost' => $insuranceCost,
                    'shipment_duration' => $rate['duration'] ?? '1-3 hari',
                    'type' => $rate['type'] ?? ($isInstant ? 'instant' : 'reguler'),
                ];
            }
        }

        return response()->json(['success' => true, 'pricing' => $pricing]);
    }

    public function process(Request $request)
    {
        if (auth()->check() && auth()->user()->status === 'suspended') {
            return redirect()->back()->with('error', 'Akun Anda sedang disuspend.');
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
            'use_protection' => 'nullable|boolean',
        ]);

        $rawCart = session()->get('cart', []);
        if (empty($rawCart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;

            // 1. Validasi Stok Utama & Varian Terlebih Dahulu
            foreach ($rawCart as $cartKey => $item) {
                $productId = $item['product_id'] ?? explode('_', $cartKey)[0];
                $variantId = $item['product_variant_id'] ?? null;
                $product = Product::lockForUpdate()->find($productId);

                if (!$product) {
                    throw new \Exception("Produk '{$item['name']}' sudah tidak tersedia.");
                }

                if ($variantId) {
                    $variant = ProductVariant::lockForUpdate()->find($variantId);
                    if (!$variant) {
                        throw new \Exception("Varian produk untuk '{$item['name']}' sudah tidak tersedia.");
                    }

                    if ($variant->stock < $item['quantity']) {
                        throw new \Exception("Stok varian '{$variant->name}' untuk produk '{$product->name}' tidak mencukupi.");
                    }
                } else {
                    $reservedStock = \App\Models\LimitStock::where('product_id', $product->id)
                        ->where('expires_at', '>', now())
                        ->sum('quantity');

                    $effectiveStock = $product->stock - $reservedStock;

                    if ($effectiveStock < $item['quantity']) {
                        throw new \Exception("Stok produk '{$product->name}' baru saja habis atau tidak mencukupi.");
                    }
                }

                $subtotal += $item['price'] * $item['quantity'];
            }

            $discount = 0;
            $coupon = session()->get('coupon');
            if ($coupon) {
                $discount = ($coupon['type'] == 'percentage') ? ($subtotal * $coupon['value']) / 100 : $coupon['value'];
            }
            $subtotalAfterDiscount = max(0, $subtotal - $discount);

            $shippingCost = (float) $request->shipping_cost;
            $useProtection = $request->has('use_protection');
            $protectionFee = $useProtection ? ($subtotal * 0.005) : 0;
            $totalPrice = $subtotalAfterDiscount + $shippingCost;

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
                'is_protected' => $useProtection,
                'protection_fee' => $protectionFee,
            ]);

            // 2. Simpan Order Items dan Catat / Batasi Stok
            foreach ($rawCart as $cartKey => $item) {
                $productId = $item['product_id'] ?? explode('_', $cartKey)[0];
                $variantId = $item['product_variant_id'] ?? null;

                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'product_variant_id' => $variantId, // Pastikan kolom ini ada di tabel order_items atau sesuaikan nama kolomnya
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                \App\Models\LimitStock::create([
                    'user_id' => auth()->id(),
                    'product_id' => $productId,
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

        $orders = Order::with(['orderItems.product', 'orderItems.productVariant'])->where('user_id', $userId)->latest()->get();

        $pendingOrders = Order::with(['orderItems.product', 'orderItems.productVariant'])
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->latest()->get();

        $processingOrders = Order::with(['orderItems.product', 'orderItems.productVariant'])
            ->where('user_id', $userId)
            ->whereIn('status', ['verifikasi', 'paid', 'packing', 'shipping', 'shipped'])
            ->latest()->get();

        $completedOrders = Order::with(['orderItems.product', 'orderItems.productVariant'])
            ->where('user_id', $userId)
            ->whereIn('status', ['completed', 'cancelled'])
            ->latest()->get();

        return view('user.dashboard', compact('orders', 'pendingOrders', 'processingOrders', 'completedOrders'));
    }

    public function getCities()
    {
        $cities = DB::table('cities')
            ->select('city', 'postal_code')
            ->get();

        return response()->json($cities);
    }
}