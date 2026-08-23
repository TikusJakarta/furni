<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Feature;
use App\Models\Testimonial;
use App\Models\BlogPost; // Sesuaikan dengan nama model blog kamu

class HomeController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $products = Product::limit(3)->get(); 
        $features = Feature::limit(4)->get(); 
        $popularProducts = Product::limit(3)->get(); 
        $testimonials = Testimonial::all(); 
        
        // Ambil data blog (biasanya dibatasi 3 post untuk halaman home)
        $blogs = BlogPost::limit(3)->get(); 

        // Masukkan 'blogs' ke dalam compact
        return view('home', compact('settings', 'products', 'features', 'popularProducts', 'testimonials', 'blogs')); 
    }
}