<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    // Menampilkan halaman checkout
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $cartItems = session()->get('cart', []);
        
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $total = $subtotal;

        return view('checkout', compact('settings', 'cartItems', 'subtotal', 'total'));
    }

    // Memproses pesanan dan mengurangi stok di database
    public function process(Request $request)
    {
        $cartItems = session()->get('cart', []);

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        DB::beginTransaction();
        try {
            foreach ($cartItems as $id => $item) {
                // Ambil produk dan kunci barisnya untuk mencegah race condition
                $product = Product::lockForUpdate()->find($id);

                if (!$product) {
                    throw new \Exception("Produk '{$item['name']}' sudah tidak tersedia.");
                }

                // Validasi apakah stok mencukupi
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok untuk produk '{$product->name}' tidak mencukupi. Sisa stok: {$product->stock}");
                }

                // Kurangi stok produk
                $product->stock -= $item['quantity'];
                $product->save();
            }

            // Kosongkan keranjang setelah checkout berhasil
            session()->forget('cart');
            DB::commit();

            return redirect()->route('shop')->with('success', 'Checkout berhasil! Terima kasih atas pesanan Anda.');
        
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}