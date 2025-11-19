<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SoccerApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ResultsController extends Controller
{
    protected SoccerApiService $soccerApiService;

    public function __construct(SoccerApiService $soccerApiService)
    {
        $this->soccerApiService = $soccerApiService;
    }

    /**
     * Display the football results page.
     */
    public function index(Request $request)
    {
        // Get today's date using Carbon with proper timezone
        $timezone = config('app.timezone', 'UTC');
        $today = Carbon::now($timezone)->format('Y-m-d');
        
        // Get date from request or default to today
        $date = $request->get('date', $today);
        
        // Check if "live" results requested
        $isLive = $request->get('type') === 'live';
        
        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = $today;
        }

        // Cache transformed data with date key
        $cacheKey = $isLive ? 'results:live_matches' : 'results:transformed_matches:' . $date;
        
        // Cache for 1 week (7 days) for results page
        $data = Cache::remember($cacheKey, $isLive ? 30 : 604800, function () use ($date, $isLive) {
            if ($isLive) {
                // Fetch live matches
                $liveResponse = $this->soccerApiService->getLivescores();
                $finishedMatches = [];
                
                if ($liveResponse && isset($liveResponse['data']) && is_array($liveResponse['data'])) {
                    foreach ($liveResponse['data'] as $apiMatch) {
                        $finishedMatches[] = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
                    }
                }
            } else {
                // Fetch finished matches for specific date
                $finishedResponse = $this->soccerApiService->getFinishedMatches(['d' => $date]);
                
                $finishedMatches = [];
                
                if ($finishedResponse && isset($finishedResponse['data']) && is_array($finishedResponse['data'])) {
                    // Filter matches by date
                    foreach ($finishedResponse['data'] as $apiMatch) {
                        $matchDate = $apiMatch['time']['date'] ?? null;
                        if ($matchDate === $date) {
                            $finishedMatches[] = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
                        }
                    }
                }
            }

            // Extract unique bookmakers from matches
            $bookmakers = $this->soccerApiService->extractBookmakers($finishedMatches);
            
            return [
                'finishedMatches' => $finishedMatches,
                'bookmakers' => $bookmakers,
            ];
        });

        // Generate date options for tabs (past dates only for results page)
        $dateOptions = [];
        $baseDate = Carbon::parse($today, $timezone);
        
        // Add "Hôm qua" and "Hôm nay" first (right after "Kết quả trực truyền")
        $dateOptions[] = [
            'value' => $baseDate->copy()->addDays(-1)->format('Y-m-d'),
            'label' => 'Hôm qua',
        ];
        
        $dateOptions[] = [
            'value' => $baseDate->copy()->addDays(0)->format('Y-m-d'),
            'label' => 'Hôm nay',
        ];
        
        // Add 7 days before (past dates only, excluding yesterday)
        // Loop from -7 (7 days ago) to -2 (2 days ago)
        for ($i = -7; $i <= -2; $i++) {
            $dateOption = $baseDate->copy()->addDays($i)->format('Y-m-d');
            $dateLabel = $baseDate->copy()->addDays($i)->format('d/m');
            
            $dateOptions[] = [
                'value' => $dateOption,
                'label' => $dateLabel,
            ];
        }

        return view('pages.results', array_merge($data, [
            'date' => $date,
            'today' => $today,
            'isLive' => $isLive,
            'dateOptions' => $dateOptions,
        ]));
    }

    /**
     * Display results for a specific league
     */
    public function showLeague($leagueId, Request $request)
    {
        // Get league info using new API
        $leagueInfo = $this->soccerApiService->getLeagueInfo($leagueId);
        
        if (!$leagueInfo) {
            abort(404, 'League not found');
        }

        // Get league name from info or fallback to leagues list
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

        // Check if league is a CUP
        $isCup = ($leagueInfo['is_cup'] ?? '0') == '1' || ($leagueInfo['is_cup'] ?? 0) == 1;

        // Get current round ID from season info
        $currentRoundId = $seasonInfo['current_round_id'] ?? null;

        // Get selected round from request
        $selectedRound = $request->get('round', null);

        // Determine which rounds to fetch
        $roundsToFetch = [];
        if ($isCup) {
            // For CUP leagues, fetch only recent rounds (last 5 rounds) for better performance
            // We'll get the 20 most recent matches anyway, so we don't need many rounds
            $roundsToFetch = array_slice($roundIds, -5);
        } elseif ($selectedRound && in_array($selectedRound, $roundIds)) {
            // If a specific round is selected, fetch only that round
            $roundsToFetch = [$selectedRound];
        } else {
            // If no round selected, fetch the last 3 rounds from current_round_id going backwards
            if ($currentRoundId && in_array($currentRoundId, $roundIds)) {
                // Find the index of current_round_id
                $currentIndex = array_search($currentRoundId, $roundIds);
                if ($currentIndex !== false) {
                    // Get 3 rounds starting from current_round_id going backwards
                    // Include current_round_id and 2 rounds before it
                    $startIndex = max(0, $currentIndex - 2);
                    $roundsToFetch = array_slice($roundIds, $startIndex, 3);
                } else {
                    // Fallback: get last 3 rounds
                    $roundsToFetch = array_slice($roundIds, -3);
                }
            } else {
                // Fallback: get last 3 rounds if current_round_id not found
                $roundsToFetch = array_slice($roundIds, -3);
            }
        }

        // Fetch fixtures for each round using parallel requests for better performance
        // Use cache key based on rounds to fetch
        $cacheKey = 'results:league:' . $leagueId . ':rounds:' . md5(implode(',', $roundsToFetch));
        
        // Cache for 1 week (7 days) for results league page
        $allMatches = Cache::remember($cacheKey, 604800, function () use ($roundsToFetch) {
            $matches = [];
            
            // Use Http::pool() for parallel API calls
            $responses = \Illuminate\Support\Facades\Http::pool(function ($pool) use ($roundsToFetch) {
                $requests = [];
                foreach ($roundsToFetch as $roundId) {
                    $config = $this->soccerApiService->getRequestConfig('fixtures', [
                        't' => 'season',
                        'round_id' => $roundId,
                        // No include needed - results page only needs basic match data
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
                            // Only include finished matches
                            $status = $apiMatch['status'] ?? null;
                            $statusName = $apiMatch['status_name'] ?? null;
                            $isFinished = ($status == 3 || $status == 2 || $statusName === 'Finished');
                            
                            if ($isFinished) {
                                $transformedMatch = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
                                $transformedMatch['round_id'] = $roundId;
                                // Get round_name from transformed match (it's stored as 'round' in transformMatchToTableFormat)
                                // Or get directly from API response
                                $transformedMatch['round_name'] = $transformedMatch['round'] 
                                    ?? $apiMatch['round_name'] 
                                    ?? $apiMatch['round']['name'] 
                                    ?? null;
                                $matches[] = $transformedMatch;
                            }
                        }
                    }
                }
            }
            
            return $matches;
        });
        
        
        // For CUP, sort matches by date/time (newest first) and limit to 20 most recent
        if ($isCup) {
            // Sort all matches by starting_datetime descending (newest first)
            usort($allMatches, function($a, $b) {
                $dateA = $a['starting_datetime'] ?? $a['date'] ?? '';
                $dateB = $b['starting_datetime'] ?? $b['date'] ?? '';
                return strtotime($dateB) <=> strtotime($dateA);
            });
            
            // Limit to 20 most recent matches
            $allMatches = array_slice($allMatches, 0, 20);
        } else {
            // For regular leagues, limit to 20 matches (already limited by rounds)
            if (count($allMatches) > 20) {
                $allMatches = array_slice($allMatches, 0, 20);
            }
        }

        // Group matches by date for display
        $matchesByDate = [];
        foreach ($allMatches as $match) {
            // Get date from match
            $matchDate = $match['date'] ?? null;
            if (!$matchDate && isset($match['starting_datetime']) && $match['starting_datetime']) {
                try {
                    $matchDate = Carbon::parse($match['starting_datetime'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $matchDate = substr($match['starting_datetime'], 0, 10);
                }
            }
            
            if ($matchDate) {
                if (!isset($matchesByDate[$matchDate])) {
                    $matchesByDate[$matchDate] = [];
                }
                $matchesByDate[$matchDate][] = $match;
            }
        }
        
        // Sort dates: for CUP, show newest first (descending), for regular leagues also newest first
        if ($isCup) {
            // For CUP: sort dates descending (newest first) - show most recent matches
            krsort($matchesByDate);
        } else {
            // For regular leagues: also sort descending (newest first)
            krsort($matchesByDate);
        }

        // Extract unique bookmakers from matches
        $bookmakers = $this->soccerApiService->extractBookmakers($allMatches);

        // Prepare league data for view
        $league = [
            'id' => $leagueId,
            'name' => $leagueName,
            'country_name' => $leagueInfo['country']['name'] ?? null,
        ];

        return view('pages.results-league', [
            'league' => $league,
            'leagueId' => $leagueId,
            'isCup' => $isCup,
            'round' => $selectedRound,
            'roundIds' => $roundIds,
            'roundsToFetch' => $roundsToFetch,
            'bookmakers' => $bookmakers,
            'matchesByDate' => $matchesByDate,
        ]);
    }
}

