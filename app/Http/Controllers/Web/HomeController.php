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
        // Fetch live scores from API
        $liveResponse = $this->soccerApiService->getLivescores();
        // Get fixture notstart matches of today using fixtures API with odds_prematch
        $today = date('Y-m-d');
        $upcomingResponse = $this->soccerApiService->getScheduleMatches($today, ['include' => 'odds_prematch']);
        
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
            
            // Sort upcoming matches by starting datetime
            usort($upcomingMatches, function($a, $b) {
                $datetimeA = $a['starting_datetime'] ?? null;
                $datetimeB = $b['starting_datetime'] ?? null;
                
                if ($datetimeA === null && $datetimeB === null) return 0;
                if ($datetimeA === null) return 1; // Put null at the end
                if ($datetimeB === null) return -1; // Put null at the end
                
                $timestampA = strtotime($datetimeA);
                $timestampB = strtotime($datetimeB);
                
                return $timestampA <=> $timestampB; // Ascending order (earliest first)
            });
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

