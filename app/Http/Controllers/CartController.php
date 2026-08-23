<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

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

    public function update(Request $request)
    {
        $quantities = $request->input('quantity', []);
        $cart = session()->get('cart', []);

        foreach ($quantities as $id => $quantity) {
            if (isset($cart[$id])) {
                if ($quantity > 0) {
                    $cart[$id]['quantity'] = $quantity;
                } else {
                    unset($cart[$id]);
                }
            }
        }

        session()->put('cart', $cart);

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