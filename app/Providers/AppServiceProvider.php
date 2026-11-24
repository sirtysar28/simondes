<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Bagikan branding (nama, logo, favicon) ke seluruh view
        View::composer('*', function ($view) {
            try {
                $view->with('branding', [
                    'name' => Setting::get('app_name', 'SiMONDES'),
                    'short' => Setting::get('app_short', 'SiMONDES'),
                    'logo' => Setting::get('logo_path'),
                    'favicon' => Setting::get('favicon_path'),
                ]);
            } catch (\Throwable) {
                $view->with('branding', [
                    'name' => 'SiMONDES',
                    'short' => 'SiMONDES',
                    'logo' => null,
                    'favicon' => null,
                ]);
            }
        });
    }
}
