<?php

namespace App\Providers;

use App\Support\SiteSettings;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
         * The CMS settings are laid over config('clinique.*') here, so the
         * views that already read that config pick up whatever the Clinic
         * has filled in without any of them changing. A key left empty
         * keeps the file's default.
         */
        SiteSettings::applyToConfig();
    }
}
