<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Testimonial; 
use App\Models\Feature;
use App\Models\Product;
class ServicesController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $testimonials = Testimonial::all();
        $features = Feature::all();
        $products = Product::limit(3)->get();


        return view('services', compact('settings', 'testimonials', 'features', 'products'));
    }
}