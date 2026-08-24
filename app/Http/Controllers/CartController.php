<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Product;

class CartController extends Controller
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

        return view('cart', compact('settings', 'cartItems', 'subtotal', 'total'));
    }

    public function add($id)
{
    $product = Product::findOrFail($id);
    $cart = session()->get('cart', []);

    $currentQty = isset($cart[$id]) ? $cart[$id]['quantity'] + 1 : 1;

    // Cek apakah kuantitas melebihi stok
    if ($currentQty > $product->stock) {
        return redirect()->back()->with('error', 'Stok produk tidak mencukupi.');
    }

    if (isset($cart[$id])) {
        $cart[$id]['quantity'] = $currentQty;
    } else {
        $cart[$id] = [
            "name" => $product->name,
            "quantity" => 1,
            "price" => $product->price,
            "image" => $product->image
        ];
    }

    session()->put('cart', $cart);
    return redirect()->back()->with('success', 'Product added to cart successfully!');
}

    // --- PERBARUI METHOD UPDATE INI ---
    public function update(Request $request)
    {
        $quantities = $request->input('quantity', []);
        $cart = session()->get('cart', []);

        foreach ($quantities as $id => $quantity) {
            if (isset($cart[$id])) {
                $cart[$id]['quantity'] = max(1, (int)$quantity);
            }
        }

        session()->put('cart', $cart);

        if ($request->ajax() || $request->wantsJson()) {
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            return response()->json([
                'status' => 'success',
                'subtotal' => $subtotal,
                'total' => $subtotal
            ]);
        }

        return redirect()->back()->with('success', 'Cart updated successfully.');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Item removed successfully.');
    }

    public function checkout()
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
}