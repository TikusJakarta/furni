<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Product;

class ShopController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $products = Product::all();

        return view('shop', compact('settings', 'products'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $settings = Setting::pluck('value', 'key')->all();

        return view('product-detail', compact('product', 'settings'));
    }
}