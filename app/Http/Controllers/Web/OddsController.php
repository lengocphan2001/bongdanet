<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SoccerApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class OddsController extends Controller
{
    protected SoccerApiService $soccerApiService;

    public function __construct(SoccerApiService $soccerApiService)
    {
        $this->soccerApiService = $soccerApiService;
    }

    /**
     * Display the odds page.
     */
    public function index(Request $request)
    {
        // Get today's date using Carbon with proper timezone
        $timezone = config('app.timezone', 'UTC');
        $today = Carbon::now($timezone)->format('Y-m-d');
        
        // Get date from request or default to today
        $date = $request->get('date', $today);
        
        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = $today;
        }

        // Cache transformed data
        $cacheKey = 'odds:transformed_matches:' . $date;
        
        $data = Cache::remember($cacheKey, 120, function () use ($date) {
            // Fetch schedule matches with odds from API
            $scheduleResponse = $this->soccerApiService->getScheduleMatchesWithOdds($date);
            
            $scheduleMatches = [];
            
            if ($scheduleResponse && isset($scheduleResponse['data']) && is_array($scheduleResponse['data'])) {
                // Transform API data to table format
                foreach ($scheduleResponse['data'] as $apiMatch) {
                    // Only include matches that are not started or live (exclude finished)
                    $matchStatus = $apiMatch['status'] ?? null;
                    $statusName = $apiMatch['status_name'] ?? null;
                    
                    // Check match status
                    $isNotStarted = ($matchStatus === 0 || $matchStatus === '0' || $statusName === 'Notstarted');
                    $isLive = ($matchStatus === 1 || $matchStatus === '1' || $statusName === 'Inplay');
                    $isFinished = ($matchStatus === 2 || $matchStatus === '2' || $statusName === 'Finished');
                    
                    // Only include matches that are not started or live (exclude finished)
                    if (($isNotStarted || $isLive) && !$isFinished) {
                        $transformedMatch = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
                        if ($transformedMatch) {
                            // Add league info
                            $transformedMatch['league_id'] = $apiMatch['league']['id'] ?? null;
                            $transformedMatch['league_name'] = $apiMatch['league']['name'] ?? 'N/A';
                            $transformedMatch['country_name'] = $apiMatch['league']['country_name'] ?? '';
                            $scheduleMatches[] = $transformedMatch;
                        }
                    }
                }
            }

            return [
                'scheduleMatches' => $scheduleMatches,
            ];
        });

        // Generate date options for tabs starting from today
        $dateOptions = [];
        $baseDate = Carbon::parse($today, $timezone);
        
        // Add "Kèo trực tuyến" (Live odds) option
        $dateOptions[] = [
            'value' => 'live',
            'label' => 'Kèo trực tuyến',
            'isLive' => true,
        ];
        
        // Add today and next 5 days
        for ($i = 0; $i < 6; $i++) {
            $dateOption = $baseDate->copy()->addDays($i)->format('Y-m-d');
            $dateLabel = $i == 0 ? 'Hôm nay' : ($i == 1 ? 'Ngày mai' : $baseDate->copy()->addDays($i)->format('d/m'));
            
            $dateOptions[] = [
                'value' => $dateOption,
                'label' => $dateLabel,
                'isLive' => false,
            ];
        }

        // Get live matches if "live" is selected
        $liveMatches = [];
        if ($date === 'live') {
            $liveResponse = $this->soccerApiService->getLivescores([
                'include' => 'odds_prematch'
            ]);
            
            if ($liveResponse && isset($liveResponse['data']) && is_array($liveResponse['data'])) {
                foreach ($liveResponse['data'] as $apiMatch) {
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
                            $liveMatches[] = $transformedMatch;
                        }
                    }
                }
            }
        }

        // Group matches by league
        $matchesToShow = ($date ?? '') === 'live' ? $liveMatches : ($data['scheduleMatches'] ?? []);
        
        // Additional filter to ensure no finished matches (double check)
        $filteredMatches = array_filter($matchesToShow, function($match) {
            // Check if match has status information
            // If time display is 'FT', it's finished
            $timeDisplay = $match['time'] ?? '';
            return $timeDisplay !== 'FT';
        });
        
        $groupedByLeague = [];
        foreach ($filteredMatches as $match) {
            $leagueId = $match['league_id'] ?? 'unknown';
            $leagueName = $match['league_name'] ?? 'N/A';
            $countryName = $match['country_name'] ?? '';
            
            $leagueKey = $leagueId . '|' . $leagueName;
            
            if (!isset($groupedByLeague[$leagueKey])) {
                $groupedByLeague[$leagueKey] = [
                    'league_id' => $leagueId,
                    'league_name' => $leagueName,
                    'country_name' => $countryName,
                    'matches' => [],
                ];
            }
            
            $groupedByLeague[$leagueKey]['matches'][] = $match;
        }

        return view('pages.odds', array_merge($data, [
            'date' => $date,
            'today' => $today,
            'dateOptions' => $dateOptions,
            'liveMatches' => $liveMatches,
            'groupedByLeague' => $groupedByLeague,
        ]));
    }

    /**
     * Display odds for a specific league
     */
    public function byLeague($leagueId, Request $request)
    {
        // Get league info
        $leagueInfo = $this->soccerApiService->getLeagueInfo($leagueId);
        
        if (!$leagueInfo) {
            abort(404, 'League not found');
        }

        $leagueName = $leagueInfo['name'] ?? null;
        if (!$leagueName) {
            $leaguesResponse = $this->soccerApiService->getLeaguesList();
            if ($leaguesResponse && isset($leaguesResponse['data']) && is_array($leaguesResponse['data'])) {
                foreach ($leaguesResponse['data'] as $l) {
                    if (($l['id'] ?? null) == $leagueId) {
                        $leagueName = $l['name'] ?? 'N/A';
                        break;
                    }
                }
            }
        }

        // Get current season ID
        $seasonId = $leagueInfo['id_current_season'] ?? null;
        if (!$seasonId) {
            abort(404, 'Season not found for this league');
        }

        // Get season info to get round_ids
        $seasonInfo = $this->soccerApiService->getSeasonInfo($seasonId);
        if (!$seasonInfo) {
            abort(404, 'Season info not found');
        }

        $roundIds = $seasonInfo['round_ids'] ?? [];
        if (empty($roundIds) || !is_array($roundIds)) {
            $roundIds = [];
        }

        // Get current round ID
        $currentRoundId = $seasonInfo['current_round_id'] ?? null;

        // Determine which rounds to fetch - next 2 rounds from current
        $roundsToFetch = [];
        if ($currentRoundId && in_array($currentRoundId, $roundIds)) {
            $currentIndex = array_search($currentRoundId, $roundIds);
            if ($currentIndex !== false) {
                $roundsToFetch = array_slice($roundIds, $currentIndex, 2);
            } else {
                $roundsToFetch = array_slice($roundIds, 0, 2);
            }
        } else {
            $roundsToFetch = array_slice($roundIds, 0, 2);
        }

        // Fetch fixtures with odds for each round
        $cacheKey = 'odds:league:' . $leagueId . ':rounds:' . md5(implode(',', $roundsToFetch));
        
        $allMatches = Cache::remember($cacheKey, 120, function () use ($roundsToFetch) {
            $matches = [];
            
            // Use Http::pool() for parallel API calls
            $responses = \Illuminate\Support\Facades\Http::pool(function ($pool) use ($roundsToFetch) {
                $requests = [];
                foreach ($roundsToFetch as $roundId) {
                    $config = $this->soccerApiService->getRequestConfig('fixtures', [
                        't' => 'season',
                        'round_id' => $roundId,
                        'include' => 'odds_prematch',
                    ]);
                    $requests["round_{$roundId}"] = $pool->as("round_{$roundId}")->timeout(10)->retry(1, 50)->get($config['url'], $config['params']);
                }
                return $requests;
            });
            
            // Process responses
            foreach ($roundsToFetch as $roundId) {
                $key = "round_{$roundId}";
                if (isset($responses[$key]) && $responses[$key]->successful()) {
                    $responseData = $responses[$key]->json();
                    $fixtures = $responseData['data'] ?? [];
                    
                    if (is_array($fixtures)) {
                        foreach ($fixtures as $apiMatch) {
                            $transformedMatch = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
                            if ($transformedMatch) {
                                $transformedMatch['round_id'] = $roundId;
                                $transformedMatch['round_name'] = $apiMatch['round_name'] ?? null;
                                $matches[] = $transformedMatch;
                            }
                        }
                    }
                }
            }
            
            return $matches;
        });

        // Sort by date/time ascending
        usort($allMatches, function($a, $b) {
            $dateA = $a['starting_datetime'] ?? '';
            $dateB = $b['starting_datetime'] ?? '';
            return strtotime($dateA) <=> strtotime($dateB);
        });

        // Get bookmakers for odds display
        $bookmakers = $this->soccerApiService->extractBookmakers($allMatches);

        return view('pages.odds-league', [
            'league' => [
                'id' => $leagueId,
                'name' => $leagueName,
                'country_name' => $leagueInfo['country_name'] ?? '',
            ],
            'leagueId' => $leagueId,
            'bookmakers' => $bookmakers,
            'matches' => $allMatches,
        ]);
    }
}

