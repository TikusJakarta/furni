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
}