<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Team;
use App\Models\Testimonial;
use App\Models\Feature; // Pastikan model Feature di-import

class AboutController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $teams = Team::all();
        $testimonials = Testimonial::all();
        
        // Ambil data features untuk dikirim ke view about.blade.php
        $features = Feature::all(); 

        return view('about', compact('settings', 'teams', 'testimonials', 'features'));
    }
}