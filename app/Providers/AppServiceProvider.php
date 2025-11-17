<?php

namespace App\Providers;

use App\View\Composers\MatchScheduleComposer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS in production (uncomment when SSL is configured)
        // if (config('app.env') === 'production' && config('app.url') && str_starts_with(config('app.url'), 'https://')) {
        //     URL::forceScheme('https');
        // }

        // Share match schedule data with match-schedule component
        View::composer('components.match-schedule', MatchScheduleComposer::class);
        
        // Share predictions today data with football-predictions-today component
        View::composer('components.football-predictions-today', \App\View\Composers\FootballPredictionsTodayComposer::class);
    }
}
