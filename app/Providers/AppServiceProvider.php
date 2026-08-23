<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use App\Models\Setting;
use App\Models\Page;

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
                'page' => $page
            ]);
        });
    }
}