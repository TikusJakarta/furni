<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        $settings = Setting::pluck('value', 'key')->all();
        
        return view('auth.login', compact('settings'));
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Autentikasi login
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = auth()->user();

        // Hanya blokir jika statusnya 'banned'
        if ($user->status == 'banned') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akun Anda telah diblokir (banned).',
            ])->withInput();
        }

        // Jika status 'suspended', mereka tetap dibolehkan login dan masuk,
        // tapi nanti otomatis dicegat saat mau checkout (sudah diatur di CheckoutController).

        if ($user->role == 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended(route('home'));
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->withInput();
}

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/shop')->with('success', 'Berhasil logout!');
    }
}