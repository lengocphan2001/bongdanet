<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SoccerApiService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected SoccerApiService $soccerApiService;

    public function __construct(SoccerApiService $soccerApiService)
    {
        $this->soccerApiService = $soccerApiService;
    }

    /**
     * Display the home page.
     */
    public function index()
    {
        // No cache - fetch fresh data directly from API
        // Fetch live scores from API - bypass cache for fresh data
        $liveResponse = $this->soccerApiService->getLivescores(['_bypass_cache' => true]);
        // Get fixture notstart matches of today using fixtures API with odds_prematch, sorted by time, filtered past matches
        // Bypass cache for fresh data
        $today = date('Y-m-d');
        $upcomingResponse = $this->soccerApiService->getScheduleMatches($today, [
            'include' => 'odds_prematch',
            '_bypass_cache' => true,
            '_sort_by_time' => true,
            '_filter_past_matches' => true
        ]);
        
        $liveMatches = [];
        $upcomingMatches = [];
        
        if ($liveResponse && isset($liveResponse['data']) && is_array($liveResponse['data'])) {
            // Transform API data to table format
            foreach ($liveResponse['data'] as $apiMatch) {
                $liveMatches[] = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
            }
        }

        if ($upcomingResponse && isset($upcomingResponse['data']) && is_array($upcomingResponse['data'])) {
            $totalMatches = count($upcomingResponse['data']);
            $filteredByStatus = 0;
            
            // Filter to include only matches with status = 0 (notstarted) of today
            // Data is already sorted by time from API
            foreach ($upcomingResponse['data'] as $apiMatch) {
                // Only include matches with status = 0 (notstarted)
                $matchStatus = $apiMatch['status'] ?? null;
                $statusName = $apiMatch['status_name'] ?? null;
                
                // Check if match status is 0 (notstarted)
                $isNotStarted = ($matchStatus === 0 || $matchStatus === '0' || $statusName === 'Notstarted');
                $isLive = ($matchStatus === 1 || $statusName === 'Inplay');
                $isFinished = ($matchStatus === 2 || $statusName === 'Finished');
                
                // Only include matches that are not started (exclude live and finished)
                // Only include today's not started matches
                if ($isNotStarted && !$isLive && !$isFinished) {
                    $filteredByStatus++;
                    $upcomingMatches[] = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
                }
            }
            
            // Log for debugging
            \Log::info('HomeController upcoming matches filter', [
                'total_from_api' => $totalMatches,
                'filtered_by_status' => $filteredByStatus,
                'final_count' => count($upcomingMatches),
                'date' => $today,
            ]);
        }

        // Extract unique bookmakers from all matches
        $allMatches = array_merge($liveMatches, $upcomingMatches);
        $bookmakers = $this->soccerApiService->extractBookmakers($allMatches);
        
        $data = [
            'liveMatches' => $liveMatches,
            'upcomingMatches' => $upcomingMatches,
            'bookmakers' => $bookmakers,
        ];

        return view('pages.home', $data);
    }
}

