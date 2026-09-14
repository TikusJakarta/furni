<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Product;

class ShopController extends Controller
{
    public function index(Request $request)
{
    $settings = Setting::pluck('value', 'key')->all();
    $search = $request->input('search');

    $products = Product::when($search, function ($query, $search) {
        return $query->where('name', 'like', '%' . $search . '%')
                     ->orWhere('description', 'like', '%' . $search . '%');
    })->latest()->get();

    return view('shop', compact('settings', 'products', 'search'));
}

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $settings = Setting::pluck('value', 'key')->all();

        return view('product-detail', compact('product', 'settings'));
    }
}