<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Règles de conservation
|--------------------------------------------------------------------------
|
| Nightly, outside the Clinic's opening hours. The rules themselves are
| in the command; what matters here is that they run without anyone
| remembering to run them, which is the whole point of a retention
| schedule.
|
| On the server this needs the scheduler itself to be running:
|   * * * * * cd /var/www/clinique && php artisan schedule:run >> /dev/null 2>&1
|
*/
Schedule::command('clinique:conservation')
    ->dailyAt('03:30')
    ->withoutOverlapping();
