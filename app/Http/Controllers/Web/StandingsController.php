<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SoccerApiService;
use Illuminate\Http\Request;

class StandingsController extends Controller
{
    protected SoccerApiService $soccerApiService;

    public function __construct(SoccerApiService $soccerApiService)
    {
        $this->soccerApiService = $soccerApiService;
    }

    /**
     * Display the standings list page (all leagues)
     */
    public function index(Request $request)
    {
        $response = $this->soccerApiService->getLeaguesList();
        
        $allLeagues = [];
        if ($response && isset($response['data']) && is_array($response['data'])) {
            // Show all leagues including cup competitions
            $allLeagues = array_values($response['data']);
            
            // Log for debugging
            \Log::info('Standings index', [
                'total_from_api' => count($response['data']),
                'total_leagues' => count($allLeagues),
                'meta' => $response['meta'] ?? null,
            ]);
        }

        // Pagination
        $perPage = 30; // Number of leagues per page
        $currentPage = (int) $request->get('page', 1);
        $total = count($allLeagues);
        $totalPages = (int) ceil($total / $perPage);
        
        // Ensure current page is valid
        if ($currentPage < 1) {
            $currentPage = 1;
        } elseif ($currentPage > $totalPages && $totalPages > 0) {
            $currentPage = $totalPages;
        }
        
        // Get leagues for current page
        $offset = ($currentPage - 1) * $perPage;
        $leagues = array_slice($allLeagues, $offset, $perPage);

        return view('pages.standings.index', [
            'leagues' => $leagues,
            'allLeagues' => $allLeagues, // Pass all leagues for search functionality
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'total' => $total,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Display the standings detail page for a specific league
     */
    public function show($leagueId)
    {
        // Get league info from leagues list
        $leaguesResponse = $this->soccerApiService->getLeaguesList();
        $league = null;
        
        if ($leaguesResponse && isset($leaguesResponse['data']) && is_array($leaguesResponse['data'])) {
            foreach ($leaguesResponse['data'] as $l) {
                if (($l['id'] ?? null) == $leagueId) {
                    $league = $l;
                    break;
                }
            }
        }

        if (!$league) {
            abort(404, 'League not found');
        }

        // Get standings for the league (including cup competitions)
        // Use id_current_season instead of current_season_id
        $seasonId = $league['id_current_season'] ?? $league['current_season_id'] ?? null;
        if (!$seasonId) {
            abort(404, 'Season not found for this league');
        }

        $standingsResponse = $this->soccerApiService->getStandings($leagueId, $seasonId);
        
        $standings = [];
        $groupedStandings = [];
        $leagueInfo = null;
        $isCupFormat = false;
        
        // Handle both response structures (with or without 'data' wrapper)
        $responseData = $standingsResponse['data'] ?? $standingsResponse;
        
        if ($standingsResponse && $responseData) {
            $leagueInfo = [
                'league_id' => $responseData['league_id'] ?? null,
                'season_id' => $responseData['season_id'] ?? null,
                'has_groups' => $responseData['has_groups'] ?? 0,
                'number_standings' => $responseData['number_standings'] ?? 1,
            ];
            
            if (isset($responseData['standings']) && is_array($responseData['standings']) && !empty($responseData['standings'])) {
                $standings = $responseData['standings'];
                
                // Check if standings is array of arrays (CUP format) or flat array (league format)
                $isCupFormat = isset($standings[0]) && is_array($standings[0]) && isset($standings[0][0]) && is_array($standings[0][0]);
                
                if ($isCupFormat) {
                    // CUP format: standings is array of arrays, each inner array is a group
                    foreach ($standings as $groupIndex => $groupTeams) {
                        if (is_array($groupTeams) && !empty($groupTeams)) {
                            // Get group name from first team in group
                            $firstTeam = $groupTeams[0];
                            $groupName = $firstTeam['group_name'] ?? $firstTeam['group'] ?? ('Bảng ' . chr(65 + $groupIndex)); // A, B, C, etc.
                            
                            // Sort teams in group by position
                            usort($groupTeams, function($a, $b) {
                                $posA = $a['overall']['position'] ?? $a['position'] ?? 999;
                                $posB = $b['overall']['position'] ?? $b['position'] ?? 999;
                                return $posA <=> $posB;
                            });
                            
                            $groupedStandings[$groupName] = $groupTeams;
                        }
                    }
                } elseif (($leagueInfo['has_groups'] ?? 0) == 1) {
                    // League format with groups: standings is flat array, need to group by group field
                    foreach ($standings as $team) {
                        $group = $team['group'] ?? $team['group_name'] ?? 'default';
                        if (!isset($groupedStandings[$group])) {
                            $groupedStandings[$group] = [];
                        }
                        $groupedStandings[$group][] = $team;
                    }
                    
                    // Sort each group by position
                    foreach ($groupedStandings as $group => $teams) {
                        usort($groupedStandings[$group], function($a, $b) {
                            $posA = $a['overall']['position'] ?? $a['position'] ?? 999;
                            $posB = $b['overall']['position'] ?? $b['position'] ?? 999;
                            return $posA <=> $posB;
                        });
                    }
                } else {
                    // League format without groups: flat array
                    // Sort by position to ensure correct order
                    usort($standings, function($a, $b) {
                        $posA = $a['overall']['position'] ?? $a['position'] ?? 999;
                        $posB = $b['overall']['position'] ?? $b['position'] ?? 999;
                        return $posA <=> $posB;
                    });
                    $groupedStandings = ['default' => $standings];
                }
            }
        }

        return view('pages.standings.show', [
            'league' => $league,
            'leagueInfo' => $leagueInfo,
            'standings' => $standings,
            'groupedStandings' => $groupedStandings,
            'isCupFormat' => $isCupFormat,
        ]);
    }
}

