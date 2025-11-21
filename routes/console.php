<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule background jobs for cache warming
// Note: runInBackground() is not needed for jobs, they run in background by default
Schedule::job(\App\Jobs\FetchMatchesDataJob::class)
    ->everyTwentySeconds()
    ->withoutOverlapping()
    ->name('fetch-matches-data');
