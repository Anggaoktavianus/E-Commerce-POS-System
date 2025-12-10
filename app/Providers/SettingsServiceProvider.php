<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\View\View;
use Illuminate\Support\Facades\View as ViewFacade;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share settings with all views
        ViewFacade::composer('*', function (View $view) {
            $settings = Setting::pluck('value', 'key')->toArray();
            $view->with('siteSettings', $settings);
        });
    }
}
