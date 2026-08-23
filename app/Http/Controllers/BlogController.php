<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\BlogPost;
use App\Models\Testimonial;

class BlogController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $posts = BlogPost::all();
        $testimonials = Testimonial::all();

        return view('blog', compact('settings', 'posts', 'testimonials'));
    }
}