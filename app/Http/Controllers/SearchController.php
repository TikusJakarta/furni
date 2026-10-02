<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function suggestions(Request $request)
    {
        $keyword =$request->get('q');

        if (!$keyword) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $products = Product::where('name', 'like', "\%{$keyword}%")
            ->limit(5) 
            ->get(['id', 'name', 'price', 'image', 'slug']);

        $formatted = $products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => number_format($product->price, 0, ',', '.'),
                'image' => asset('storage/' . $product->image), 
                'url' => route('products.show', $product->slug)
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formatted
        ]);
    }
}