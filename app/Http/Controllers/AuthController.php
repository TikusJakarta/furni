<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLoginForm()
    {
        $settings = Setting::pluck('value', 'key')->all();
        
        // Mengarah ke resources/views/auth/login.blade.php
        return view('auth.login', compact('settings'));
    }

    // Memproses data login
    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        
        
        return redirect()->intended(url()->previous())->with('success', 'Berhasil login!');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
}

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/shop')->with('success', 'Berhasil logout!');
    }
}