<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

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
        $total = $subtotal;

        return view('checkout', compact('settings', 'cartItems', 'subtotal', 'total'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'country'       => 'required|string',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'address'       => 'required|string',
            'state_country' => 'required|string|max:255',
            'postal_zip'    => 'required|string|max:20',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:20',
        ]);

        $cartItems = session()->get('cart', []);

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        DB::beginTransaction();
        try {
            foreach ($cartItems as $id => $item) {
                $product = Product::lockForUpdate()->find($id);

                if (!$product) {
                    throw new \Exception("Produk '{$item['name']}' sudah tidak tersedia.");
                }

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok untuk produk '{$product->name}' tidak mencukupi. Sisa stok: {$product->stock}");
                }

                $product->stock -= $item['quantity'];
                $product->save();
            }

            session()->forget('cart');
            DB::commit();

            return redirect()->route('shop')->with('success', 'Checkout berhasil! Terima kasih atas pesanan Anda.');
        
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}