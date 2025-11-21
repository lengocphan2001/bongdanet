<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SoccerApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TopScorersController extends Controller
{
    protected SoccerApiService $soccerApiService;

    public function __construct(SoccerApiService $soccerApiService)
    {
        $this->soccerApiService = $soccerApiService;
    }

    /**
     * Display the top scorers page
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $selectedLeagueId = $request->get('league_id', null);
        
        // Get all leagues
        $leaguesResponse = $this->soccerApiService->getLeaguesList();
        $allLeagues = [];
        if ($leaguesResponse && isset($leaguesResponse['data']) && is_array($leaguesResponse['data'])) {
            $allLeagues = $leaguesResponse['data'];
            
            // Filter by search if provided
            if (!empty($search)) {
                $allLeagues = array_filter($allLeagues, function($league) use ($search) {
                    $name = strtolower($league['name'] ?? '');
                    $country = strtolower($league['country_name'] ?? '');
                    $searchLower = strtolower($search);
                    return strpos($name, $searchLower) !== false || strpos($country, $searchLower) !== false;
                });
            }
        }

        // Get selected league info and top scorers
        $selectedLeague = null;
        $topScorers = [];
        $leagueName = '';
        
        if ($selectedLeagueId) {
            // Find league in list
            foreach ($allLeagues as $league) {
                if (($league['id'] ?? null) == $selectedLeagueId) {
                    $selectedLeague = $league;
                    break;
                }
            }
            
            if ($selectedLeague) {
                $leagueName = $selectedLeague['name'] ?? 'N/A';
                $seasonId = $selectedLeague['current_season_id'] ?? null;
                
                if ($seasonId) {
                    $scorersResponse = $this->soccerApiService->getTopScorers($seasonId);
                    if ($scorersResponse && isset($scorersResponse['data']) && is_array($scorersResponse['data'])) {
                        $topScorers = $scorersResponse['data'];
                    }
                }
            }
        }

        return view('pages.top-scorers', [
            'allLeagues' => array_values($allLeagues),
            'selectedLeagueId' => $selectedLeagueId,
            'selectedLeague' => $selectedLeague,
            'topScorers' => $topScorers,
            'leagueName' => $leagueName,
            'search' => $search,
        ]);
    }

    /**
     * Get top scorers data via API (AJAX)
     */
    public function getTopScorersData(Request $request)
    {
        $leagueId = $request->get('league_id', null);
        
        if (!$leagueId) {
            return response()->json([
                'success' => false,
                'message' => 'League ID is required'
            ], 400);
        }
        
        // Get all leagues to find the selected one
        $leaguesResponse = $this->soccerApiService->getLeaguesList();
        $allLeagues = [];
        if ($leaguesResponse && isset($leaguesResponse['data']) && is_array($leaguesResponse['data'])) {
            $allLeagues = $leaguesResponse['data'];
        }
        
        // Find selected league
        $selectedLeague = null;
        foreach ($allLeagues as $league) {
            if (($league['id'] ?? null) == $leagueId) {
                $selectedLeague = $league;
                break;
            }
        }
        
        if (!$selectedLeague) {
            return response()->json([
                'success' => false,
                'message' => 'League not found'
            ], 404);
        }
        
        $topScorers = [];
        $seasonId = $selectedLeague['current_season_id'] ?? null;
        
        if ($seasonId) {
            $scorersResponse = $this->soccerApiService->getTopScorers($seasonId);
            if ($scorersResponse && isset($scorersResponse['data']) && is_array($scorersResponse['data'])) {
                $topScorers = $scorersResponse['data'];
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'league' => $selectedLeague,
                'topScorers' => $topScorers,
            ]
        ]);
    }
}

