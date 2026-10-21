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

    $products = Product::with('variants')->when($search, function ($query, $search) {
        return $query->where('name', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%');
    })->latest()->paginate(5)->withQueryString();

    return view('shop', compact('settings', 'products', 'search'));
}

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $settings = Setting::pluck('value', 'key')->all();

        return view('product-detail', compact('product', 'settings'));
    }

    public function suggestions(Request $request)
    {
        $keyword = $request->get('q');

        if (!$keyword) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $products = Product::where('name', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%")
            ->limit(5)
            ->get(['id', 'name', 'price', 'image']);

        $formatted = $products->map(function ($product) {
            return [
                'name' => $product->name,
                'price' => number_format($product->price, 0, ',', '.'),
                'image' => asset($product->image),
                'url' => route('shop', ['search' => $product->name])
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formatted
        ]);
    }
}