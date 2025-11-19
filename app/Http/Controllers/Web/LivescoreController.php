<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SoccerApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LivescoreController extends Controller
{
    protected SoccerApiService $soccerApiService;

    public function __construct(SoccerApiService $soccerApiService)
    {
        $this->soccerApiService = $soccerApiService;
    }

    /**
     * Display the livescore page.
     */
    public function index()
    {
        // Cache for 30 seconds (live data changes frequently but we can reduce API calls)
        $cacheKey = 'livescore:grouped_matches';
        
        $data = Cache::remember($cacheKey, 30, function () {
            // Fetch live scores from API - only basic data needed, no events/stats/odds
            $liveResponse = $this->soccerApiService->getLivescores([
                'include' => '' // No includes - only basic match data
            ]);
            
            $allMatches = [];
            
            if ($liveResponse && isset($liveResponse['data']) && is_array($liveResponse['data'])) {
                foreach ($liveResponse['data'] as $apiMatch) {
                    // Only include live matches (status = 1 or Inplay)
                    $status = $apiMatch['status'] ?? null;
                    $statusName = $apiMatch['status_name'] ?? null;
                    $isLive = ($status == 1 || $status === '1' || $statusName === 'Inplay');
                    
                    if ($isLive) {
                        $transformedMatch = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
                        if ($transformedMatch) {
                            // Add league info
                            $transformedMatch['league_id'] = $apiMatch['league']['id'] ?? null;
                            $transformedMatch['league_name'] = $apiMatch['league']['name'] ?? 'N/A';
                            $transformedMatch['country_name'] = $apiMatch['league']['country_name'] ?? '';
                            
                            // Add match details for display
                            $transformedMatch['minute'] = $apiMatch['time']['minute'] ?? null;
                            $transformedMatch['extra_minute'] = $apiMatch['time']['extra_minute'] ?? null;
                            $transformedMatch['status_period'] = $apiMatch['status_period'] ?? null;
                            $transformedMatch['status_name'] = $apiMatch['status_name'] ?? null;
                            $transformedMatch['status'] = $apiMatch['status'] ?? null;
                            $transformedMatch['scores'] = $apiMatch['scores'] ?? [];
                            $transformedMatch['is_live'] = true;
                            
                            $allMatches[] = $transformedMatch;
                        }
                    }
                }
            }
            
            // Group matches by league
            $groupedByLeague = [];
            foreach ($allMatches as $match) {
                $leagueId = $match['league_id'] ?? null;
                $leagueName = $match['league_name'] ?? 'N/A';
                $countryName = $match['country_name'] ?? '';
                
                // Create unique key for league (use 'unknown' only for grouping, but keep league_id as null)
                $leagueKey = ($leagueId ?? 'unknown') . '_' . $leagueName;
                
                if (!isset($groupedByLeague[$leagueKey])) {
                    $groupedByLeague[$leagueKey] = [
                        'league_id' => $leagueId, // Keep as null if not available, not 'unknown'
                        'league_name' => $leagueName,
                        'country_name' => $countryName,
                        'matches' => [],
                    ];
                }
                
                $groupedByLeague[$leagueKey]['matches'][] = $match;
            }
            
            // Sort leagues by number of matches (descending)
            uasort($groupedByLeague, function($a, $b) {
                return count($b['matches']) <=> count($a['matches']);
            });
            
            return [
                'groupedMatches' => $groupedByLeague,
            ];
        });

        return view('pages.livescore', $data);
    }

    /**
     * Get livescore data as JSON for auto-refresh
     */
    public function getLivescoreData()
    {
        // Cache for 30 seconds to reduce API calls
        $cacheKey = 'livescore:api:grouped_matches';
        
        $data = Cache::remember($cacheKey, 30, function () {
            // Fetch live scores from API - only basic data needed, no events/stats/odds
            $liveResponse = $this->soccerApiService->getLivescores([
                'include' => '' // No includes - only basic match data
            ]);
        
        $allMatches = [];
        
        if ($liveResponse && isset($liveResponse['data']) && is_array($liveResponse['data'])) {
            foreach ($liveResponse['data'] as $apiMatch) {
                // Only include live matches (status = 1 or Inplay)
                $status = $apiMatch['status'] ?? null;
                $statusName = $apiMatch['status_name'] ?? null;
                $isLive = ($status == 1 || $status === '1' || $statusName === 'Inplay');
                
                if ($isLive) {
                    $transformedMatch = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
                    if ($transformedMatch) {
                        // Add league info
                        $transformedMatch['league_id'] = $apiMatch['league']['id'] ?? null;
                        $transformedMatch['league_name'] = $apiMatch['league']['name'] ?? 'N/A';
                        $transformedMatch['country_name'] = $apiMatch['league']['country_name'] ?? '';
                        
                        // Add match details for display
                        $transformedMatch['minute'] = $apiMatch['time']['minute'] ?? null;
                        $transformedMatch['extra_minute'] = $apiMatch['time']['extra_minute'] ?? null;
                        $transformedMatch['status_period'] = $apiMatch['status_period'] ?? null;
                        $transformedMatch['status_name'] = $apiMatch['status_name'] ?? null;
                        $transformedMatch['status'] = $apiMatch['status'] ?? null;
                        $transformedMatch['scores'] = $apiMatch['scores'] ?? [];
                        $transformedMatch['is_live'] = true;
                        
                        $allMatches[] = $transformedMatch;
                    }
                }
            }
        }
        
        // Group matches by league
        $groupedByLeague = [];
        foreach ($allMatches as $match) {
            $leagueId = $match['league_id'] ?? null;
            $leagueName = $match['league_name'] ?? 'N/A';
            $countryName = $match['country_name'] ?? '';
            
            // Create unique key for league (use 'unknown' only for grouping, but keep league_id as null)
            $leagueKey = ($leagueId ?? 'unknown') . '_' . $leagueName;
            
            if (!isset($groupedByLeague[$leagueKey])) {
                $groupedByLeague[$leagueKey] = [
                    'league_id' => $leagueId, // Keep as null if not available, not 'unknown'
                    'league_name' => $leagueName,
                    'country_name' => $countryName,
                    'matches' => [],
                ];
            }
            
            $groupedByLeague[$leagueKey]['matches'][] = $match;
        }
        
            // Sort leagues by number of matches (descending)
            uasort($groupedByLeague, function($a, $b) {
                return count($b['matches']) <=> count($a['matches']);
            });
            
            return [
                'groupedMatches' => $groupedByLeague,
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}

