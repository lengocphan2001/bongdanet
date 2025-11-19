<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SoccerApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    protected SoccerApiService $soccerApiService;

    public function __construct(SoccerApiService $soccerApiService)
    {
        $this->soccerApiService = $soccerApiService;
    }

    /**
     * Display the schedule page.
     */
    public function index(Request $request)
    {
        // Get today's date using Carbon with proper timezone (Asia/Ho_Chi_Minh for Vietnam)
        // If timezone is not set in config, use UTC
        $timezone = config('app.timezone', 'UTC');
        $today = Carbon::now($timezone)->format('Y-m-d');
        
        // Get date from request or default to today
        $date = $request->get('date', $today);
        
        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = $today;
        }

        // Cache transformed data
        $cacheKey = 'schedule:transformed_matches:' . $date;
        
        // Cache for 1 week (7 days) for schedule page
        $data = Cache::remember($cacheKey, 604800, function () use ($date) {
            // Fetch schedule matches from API (already cached in service)
            $scheduleResponse = $this->soccerApiService->getScheduleMatches($date);
            
            $scheduleMatches = [];
            
            if ($scheduleResponse && isset($scheduleResponse['data']) && is_array($scheduleResponse['data'])) {
                // Transform API data to table format
                foreach ($scheduleResponse['data'] as $apiMatch) {
                    // Only include matches with status = 0 (notstarted)
                    $matchStatus = $apiMatch['status'] ?? null;
                    $statusName = $apiMatch['status_name'] ?? null;
                    
                    // Check if match status is 0 (notstarted) - exclude live matches (status = 1) and finished matches (status = 2)
                    $isNotStarted = ($matchStatus === 0 || $matchStatus === '0' || $statusName === 'Notstarted');
                    $isLive = ($matchStatus === 1 || $statusName === 'Inplay');
                    $isFinished = ($matchStatus === 2 || $statusName === 'Finished');
                    
                    // Only include matches that are not started (exclude live and finished)
                    if ($isNotStarted && !$isLive && !$isFinished) {
                        $scheduleMatches[] = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
                    }
                }
            }

            return [
                'scheduleMatches' => $scheduleMatches,
            ];
        });

        // Generate date options for dropdown starting from today using Carbon
        $dateOptions = [];
        $baseDate = Carbon::parse($today, $timezone);
        
        for ($i = 0; $i < 8; $i++) {
            $dateOption = $baseDate->copy()->addDays($i)->format('Y-m-d');
            $dateLabel = $i == 0 ? 'Hôm nay' : ($i == 1 ? 'Ngày mai' : $baseDate->copy()->addDays($i)->format('d/m'));
            
            $dateOptions[] = [
                'value' => $dateOption,
                'label' => $dateLabel,
            ];
        }

        return view('pages.schedule', array_merge($data, [
            'date' => $date,
            'today' => $today,
            'dateOptions' => $dateOptions,
        ]));
    }

    /**
     * Display schedule for a specific league
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

        // Determine which rounds to fetch - 2 rounds forward from current_round_id
        $roundsToFetch = [];
        if ($isCup) {
            // For CUP leagues, fetch only recent rounds (last 5 rounds) for better performance
            $roundsToFetch = array_slice($roundIds, -5);
        } elseif ($selectedRound && in_array($selectedRound, $roundIds)) {
            // If a specific round is selected, fetch only that round
            $roundsToFetch = [$selectedRound];
        } else {
            // If no round selected, fetch the next 2 rounds from current_round_id going forward
            if ($currentRoundId && in_array($currentRoundId, $roundIds)) {
                // Find the index of current_round_id
                $currentIndex = array_search($currentRoundId, $roundIds);
                if ($currentIndex !== false) {
                    // Get 2 rounds starting from current_round_id going forward
                    // Include current_round_id and 1 round after it
                    $roundsToFetch = array_slice($roundIds, $currentIndex, 2);
                } else {
                    // Fallback: get next 2 rounds
                    $roundsToFetch = array_slice($roundIds, 0, 2);
                }
            } else {
                // Fallback: get first 2 rounds if current_round_id not found
                $roundsToFetch = array_slice($roundIds, 0, 2);
            }
        }

        // Fetch fixtures for each round using parallel requests for better performance
        $cacheKey = 'schedule:league:' . $leagueId . ':rounds:' . md5(implode(',', $roundsToFetch));
        
        // Cache for 1 week (7 days) for schedule league page
        $allMatches = Cache::remember($cacheKey, 604800, function () use ($roundsToFetch) {
            $matches = [];
            
            // Use Http::pool() for parallel API calls
            $responses = \Illuminate\Support\Facades\Http::pool(function ($pool) use ($roundsToFetch) {
                $requests = [];
                foreach ($roundsToFetch as $roundId) {
                    $config = $this->soccerApiService->getRequestConfig('fixtures', [
                        't' => 'season',
                        'round_id' => $roundId,
                        // No include needed - schedule page only needs basic match data
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
                            // Only include matches that are not started (status = 0)
                            $status = $apiMatch['status'] ?? null;
                            $statusName = $apiMatch['status_name'] ?? null;
                            $isNotStarted = ($status == 0 || $status === '0' || $statusName === 'Notstarted');
                            
                            if ($isNotStarted) {
                                $transformedMatch = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
                                if ($transformedMatch) {
                                    // Add round info
                                    $transformedMatch['round_id'] = $roundId;
                                    $transformedMatch['round_name'] = $apiMatch['round_name'] ?? null;
                                    $matches[] = $transformedMatch;
                                }
                            }
                        }
                    }
                }
            }
            
            return $matches;
        });

        // For CUP leagues, sort by date/time ascending (upcoming first) and limit to 20
        if ($isCup) {
            usort($allMatches, function($a, $b) {
                $dateA = $a['starting_datetime'] ?? '';
                $dateB = $b['starting_datetime'] ?? '';
                return strtotime($dateA) <=> strtotime($dateB);
            });
            $allMatches = array_slice($allMatches, 0, 20);
        } else {
            // For regular leagues, sort by date/time ascending
            usort($allMatches, function($a, $b) {
                $dateA = $a['starting_datetime'] ?? '';
                $dateB = $b['starting_datetime'] ?? '';
                return strtotime($dateA) <=> strtotime($dateB);
            });
        }

        // Group matches by date
        $matchesByDate = [];
        foreach ($allMatches as $match) {
            $date = $match['date'] ?? date('Y-m-d');
            if (!isset($matchesByDate[$date])) {
                $matchesByDate[$date] = [];
            }
            $matchesByDate[$date][] = $match;
        }

        // Sort dates ascending
        ksort($matchesByDate);

        // Get bookmakers for odds display (if needed)
        $bookmakers = $this->soccerApiService->extractBookmakers($allMatches);

        return view('pages.schedule-league', [
            'league' => [
                'id' => $leagueId,
                'name' => $leagueName,
                'country_name' => $leagueInfo['country_name'] ?? '',
            ],
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

