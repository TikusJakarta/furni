<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use App\Models\Setting;
use App\Models\Page;
use App\Support\ThemeColors;
use App\Models\ActivityLog;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Models\User;
use App\Observers\AuditObserver;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;



class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Mengirim data settings dan page secara otomatis ke semua view (termasuk layout.app)
        View::composer('*', function ($view) {
            $routeName = Route::currentRouteName();
            
            $settings = Setting::pluck('value', 'key')->all();
            $page = Page::where('slug', $routeName)->first();

            $view->with([
                'settings' => $settings,
                'page' => $page,
                // Variabel CSS warna navbar/hero/footer dari halaman Pengaturan admin
                'themeCss' => ThemeColors::css($settings),
            ]);
        });

        foreach ([Product::class, Coupon::class, Order::class, User::class, Setting::class, OrderReturn::class] as $model) {
            $model::observe(AuditObserver::class);
        }
 
        Event::listen(Login::class, function ($event) {
            // Akun yang diblokir langsung dikeluarkan lagi oleh login admin: jangan dicatat sebagai login
            if (($event->user->status ?? null) === 'banned') {
                return;
            }
 
            ActivityLog::record('login', 'Login ke sistem', [], $event->user);
        });
 
        Event::listen(Logout::class, function ($event) {
            ActivityLog::record('logout', 'Keluar dari sistem', [], $event->user);
        });
 
        Event::listen(Failed::class, function ($event) {
            $user = $event->user ?? User::where('email', $event->credentials['email'] ?? '')->first();
 
            if ($user) {
                ActivityLog::record('login_failed', 'Percobaan login gagal (kata sandi salah)', [
                    'properties' => ['email' => $user->email],
                ], $user);
            }
        });

    }
}
