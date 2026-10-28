<?php

namespace App\Providers;

use App\Models\ContactMessage;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\View;
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

        /*
         * The topbar's alert badge. Composed here rather than returned by
         * each of the twenty admin controllers, since the topbar is the
         * only thing that reads it — and not queried at all for an
         * éditeur, who cannot open the screen it points to.
         */
        View::composer('admin.partials.topbar', function ($view) {
            $view->with(
                'unhandledMessages',
                auth()->user()?->isAdmin() ? ContactMessage::unhandled()->count() : 0
            );
        });
    }
}
