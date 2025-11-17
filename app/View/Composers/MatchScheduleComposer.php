<?php

namespace App\View\Composers;

use App\Services\SoccerApiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class MatchScheduleComposer
{
    protected SoccerApiService $soccerApiService;

    public function __construct(SoccerApiService $soccerApiService)
    {
        $this->soccerApiService = $soccerApiService;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Get today's date
        $today = date('Y-m-d');
        $now = now();
        
        // Cache key for today's upcoming matches (first 8)
        $cacheKey = 'match_schedule:upcoming_matches:8:' . $today;
        
        $matches = Cache::remember($cacheKey, 60, function () use ($today, $now) {
            // Fetch schedule matches from API for today and tomorrow
            $scheduleMatches = [];
            $datesToCheck = [$today, date('Y-m-d', strtotime('+1 day'))];
            
            foreach ($datesToCheck as $date) {
                $scheduleResponse = $this->soccerApiService->getScheduleMatches($date);
                
                if ($scheduleResponse && isset($scheduleResponse['data']) && is_array($scheduleResponse['data'])) {
                    foreach ($scheduleResponse['data'] as $apiMatch) {
                        // Only include matches that haven't started yet
                        $status = $apiMatch['status'] ?? null;
                        $statusName = $apiMatch['status_name'] ?? null;
                        
                        // Check if match is not started
                        $isNotStarted = ($status == 0 || $status === '0' || $statusName === 'Notstarted');
                        
                        // Check if match time is in the future
                        $matchTime = null;
                        if (isset($apiMatch['time']['datetime']) && $apiMatch['time']['datetime']) {
                            try {
                                $matchTime = \Carbon\Carbon::parse($apiMatch['time']['datetime']);
                            } catch (\Exception $e) {
                                // Skip if can't parse datetime
                                continue;
                            }
                        } elseif (isset($apiMatch['time']['date']) && isset($apiMatch['time']['time'])) {
                            try {
                                $matchTime = \Carbon\Carbon::parse($apiMatch['time']['date'] . ' ' . $apiMatch['time']['time']);
                            } catch (\Exception $e) {
                                // Skip if can't parse datetime
                                continue;
                            }
                        }
                        
                        // Only include if not started and time is in the future
                        if ($isNotStarted && $matchTime && $matchTime->isFuture()) {
                            $transformed = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
                            
                            $scheduleMatches[] = [
                                'home_team' => $transformed['home_team'] ?? '-',
                                'home_logo' => $transformed['home_team_info']['img'] ?? null,
                                'time' => $transformed['time'] ?? '-',
                                'away_team' => $transformed['away_team'] ?? '-',
                                'away_logo' => $transformed['away_team_info']['img'] ?? null,
                                'starting_datetime' => $matchTime->toDateTimeString(),
                            ];
                        }
                    }
                }
            }
            
            // Sort by starting_datetime (ascending - upcoming first)
            usort($scheduleMatches, function($a, $b) {
                $timeA = $a['starting_datetime'] ?? '';
                $timeB = $b['starting_datetime'] ?? '';
                return strtotime($timeA) <=> strtotime($timeB);
            });
            
            // Get first 8 matches
            return array_slice($scheduleMatches, 0, 8);
        });
        
        $view->with('matchScheduleMatches', $matches);
    }
}

