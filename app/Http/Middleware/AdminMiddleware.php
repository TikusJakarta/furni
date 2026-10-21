<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            $user = auth()->user();

            if (in_array($user->status, ['suspended', 'banned'])) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('admin.login')->with('error', 'Akses ditolak! Akun Administrator Anda sedang ditangguhkan atau diblokir.');
            }

            return $next($request);
        }

        return redirect()->route('home')->with('error', 'Akses ditolak! Anda bukan admin.');
    }
}