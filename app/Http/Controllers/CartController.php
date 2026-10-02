<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Product;
use App\Models\ProductVariant;

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

    public function add(Request $request, $id)
{
    $product = Product::findOrFail($id);
    
    $variantId = $request->input('product_variant_id');
    $quantity = (int) $request->input('quantity', 1);

    $price = $product->price;
    $variantName = '';

    if ($variantId) {
        $variant = ProductVariant::find($variantId);
        if ($variant) {
            $price = $variant->price ?? $product->price;
            $variantName = ' - ' . $variant->name;
        }
    }

    $cartKey = $variantId ? $id . '_' . $variantId : $id;

    $cart = session()->get('cart', []);

    if (isset($cart[$cartKey])) {
        $cart[$cartKey]['quantity'] += $quantity;
    } else {
        $cart[$cartKey] = [
            'product_id' => $product->id,
            'product_variant_id' => $variantId, 
            'name' => $product->name . $variantName,
            'price' => $price,
            'quantity' => $quantity,
            'weight' => $product->weight,
            'image' => $product->image,
        ];
    }

    session()->put('cart', $cart);

    return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
}

    public function update(Request $request)
    {
        $quantities = $request->input('quantity', []);
        $cart = session()->get('cart', []);

        foreach ($quantities as $cartKey => $quantity) {
            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] = max(1, (int)$quantity);
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

    public function remove($cartKey)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Item removed successfully.');
    }

    public function checkout()
    {
        $cartItems = session()->get('cart', []);
        
        if (empty($cartItems)) {
            return redirect()->route('shop')->with('swal_error', 'Keranjang belanjaan kamu masih kosong. Silakan pilih produk terlebih dahulu!');
        }

        $settings = Setting::pluck('value', 'key')->all();
        
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $total = $subtotal;

        return view('checkout', compact('settings', 'cartItems', 'subtotal', 'total'));
    }
}