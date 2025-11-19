<?php

namespace App\View\Composers;

use App\Models\Prediction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Carbon\Carbon;

class FootballPredictionsTodayComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Cache key for today's predictions
        $cacheKey = 'football_predictions_today:' . date('Y-m-d');
        
        $predictions = Cache::remember($cacheKey, 300, function () {
            // Get published predictions for today and upcoming matches
            return Prediction::published()
                ->where(function($query) {
                    // Match time is today or in the future
                    $query->where('match_time', '>=', Carbon::today())
                          ->orWhereNull('match_time');
                })
                ->orderBy('match_time', 'asc')
                ->limit(10)
                ->get()
                ->map(function($prediction) {
                    $matchTime = $prediction->match_time 
                        ? Carbon::parse($prediction->match_time)->setTimezone('Asia/Ho_Chi_Minh')
                        : null;
                    
                    $teams = ($prediction->home_team ?? 'N/A') . ' vs ' . ($prediction->away_team ?? 'N/A');
                    $timeDisplay = $matchTime 
                        ? $matchTime->format('H:i') . ' ngày ' . $matchTime->format('d/m')
                        : 'N/A';
                    
                    return [
                        'type' => 'Nhận định, nhận định',
                        'teams' => $teams,
                        'time' => $timeDisplay,
                        'comment' => Str::limit($prediction->title, 30),
                        'url' => route('prediction.detail', $prediction->id),
                    ];
                })
                ->toArray();
        });
        
        $view->with('predictionsToday', $predictions);
    }
}

