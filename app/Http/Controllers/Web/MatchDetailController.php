<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SoccerApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MatchDetailController extends Controller
{
    protected SoccerApiService $soccerApiService;

    public function __construct(SoccerApiService $soccerApiService)
    {
        $this->soccerApiService = $soccerApiService;
    }

    /**
     * Display match detail page (ket-qua)
     */
    public function show($id)
    {
        // Get match data first (required for other operations)
        $matchData = Cache::remember("match_detail:{$id}", 300, function () use ($id) {
            return $this->soccerApiService->getFixtureInfo($id);
        });

        if (!$matchData) {
            abort(404, 'Match not found');
        }

        // Extract team and season info early
        $homeTeam = $matchData['teams']['home'] ?? [];
        $awayTeam = $matchData['teams']['away'] ?? [];
        $homeTeamId = $homeTeam['id'] ?? null;
        $awayTeamId = $awayTeam['id'] ?? null;
        $venueId = $matchData['venue_id'] ?? null;

        // Get cached data first (if available) to avoid unnecessary API calls
        $matchStats = Cache::get("match_stats:{$id}");
        $matchLineups = Cache::get("match_lineups:{$id}");
        $matchEvents = Cache::get("match_events:{$id}");
        $venue = $venueId ? Cache::get("venue_info:{$venueId}") : null;
        $matchOddsInfo = Cache::get("match_odds_info:{$id}:2");

        // Only make API calls for data that's not cached
        $needsApiCall = !$matchStats || !$matchLineups || !$matchEvents || ($venueId && !$venue) || !$matchOddsInfo;

        if ($needsApiCall) {
            // Use Laravel's Http::pool() for parallel API calls to improve performance
            $responses = \Illuminate\Support\Facades\Http::pool(function ($pool) use ($id, $venueId) {
                $requests = [];
                
                // Only request data that's not cached
                if (!Cache::has("match_stats:{$id}")) {
                    $config = $this->soccerApiService->getRequestConfig('stats', ['t' => 'match', 'id' => $id]);
                    $requests['matchStats'] = $pool->as('matchStats')->timeout(15)->retry(2, 50)->get($config['url'], $config['params']);
                }
                
                if (!Cache::has("match_lineups:{$id}")) {
                    $config = $this->soccerApiService->getRequestConfig('fixtures', ['t' => 'match_lineups', 'id' => $id]);
                    $requests['matchLineups'] = $pool->as('matchLineups')->timeout(15)->retry(2, 50)->get($config['url'], $config['params']);
                }
                
                if (!Cache::has("match_events:{$id}")) {
                    $config = $this->soccerApiService->getRequestConfig('fixtures', ['t' => 'match_events', 'id' => $id]);
                    $requests['matchEvents'] = $pool->as('matchEvents')->timeout(15)->retry(2, 50)->get($config['url'], $config['params']);
                }
                
                if (!Cache::has("match_odds_info:{$id}:2")) {
                    $config = $this->soccerApiService->getRequestConfig('fixtures', ['t' => 'match_odds_info', 'id' => $id, 'bookmaker_id' => 2]);
                    $requests['matchOddsInfo'] = $pool->as('matchOddsInfo')->timeout(15)->retry(2, 50)->get($config['url'], $config['params']);
                }
                
                if ($venueId && !Cache::has("venue_info:{$venueId}")) {
                    $config = $this->soccerApiService->getRequestConfig('venues', ['t' => 'info', 'id' => $venueId]);
                    $requests['venue'] = $pool->as('venue')->timeout(15)->retry(2, 50)->get($config['url'], $config['params']);
                }
                
                return $requests;
            });

            // Process and cache responses
            if (isset($responses['matchStats'])) {
                $response = $responses['matchStats'];
                if ($response && $response->successful()) {
                    $data = $response->json();
                    $matchStats = $data['data'] ?? null;
                    if ($matchStats !== null) {
                        Cache::put("match_stats:{$id}", $matchStats, 300);
                    }
                }
            }

            if (isset($responses['matchLineups'])) {
                $response = $responses['matchLineups'];
                if ($response && $response->successful()) {
                    $data = $response->json();
                    $matchLineups = $data['data'] ?? null;
                    if ($matchLineups !== null) {
                        Cache::put("match_lineups:{$id}", $matchLineups, 300);
                    }
                }
            }

            if (isset($responses['matchEvents'])) {
                $response = $responses['matchEvents'];
                if ($response && $response->successful()) {
                    $data = $response->json();
                    $matchEvents = $data['data'] ?? null;
                    if ($matchEvents !== null) {
                        Cache::put("match_events:{$id}", $matchEvents, 300);
                    }
                }
            }

            if (isset($responses['venue'])) {
                $response = $responses['venue'];
                if ($response && $response->successful()) {
                    $data = $response->json();
                    $venue = $data['data'] ?? null;
                    if ($venue !== null && $venueId) {
                        Cache::put("venue_info:{$venueId}", $venue, 3600);
                    }
                }
            }

            if (isset($responses['matchOddsInfo'])) {
                $response = $responses['matchOddsInfo'];
                if ($response && $response->successful()) {
                    $data = $response->json();
                    $matchOddsInfo = $data['data'] ?? null;
                    if ($matchOddsInfo !== null) {
                        Cache::put("match_odds_info:{$id}:2", $matchOddsInfo, 300);
                    }
                }
            }
        }

        // If stats are available from stats API, merge them into matchData
        if ($matchStats && is_array($matchStats)) {
            $matchData['stats'] = $matchStats;
        }

        // Process odds info data
        $oddsData = $this->processOddsInfoData($matchOddsInfo);

        // Fallback: Get match odds if odds info is not available
        $matchOdds = null;
        if (!$matchOddsInfo || empty($oddsData['full_match']['1x2']['home'])) {
            $matchOdds = Cache::remember("match_odds:{$id}", 300, function () use ($id) {
                return $this->soccerApiService->getMatchOdds($id);
            });
            $oddsData = $this->processOddsData($matchOdds);
        }

        // Team stats and matches are no longer needed (removed from view)
        // Removed to improve performance

        $league = $matchData['league'] ?? [];
        $time = $matchData['time'] ?? [];
        $scores = $matchData['scores'] ?? [];
        
        // Format date and time
        $matchDate = $time['date'] ?? date('Y-m-d');
        $matchTime = $time['time'] ?? '';
        $matchDatetime = $time['datetime'] ?? '';
        
        // Format datetime for display (e.g., "14h45 ngày 06/11")
        $displayDate = '';
        $displayTime = '';
        if ($matchDatetime) {
            try {
                $dateTime = new \DateTime($matchDatetime);
                $displayDate = $dateTime->format('d/m');
                $displayTime = $dateTime->format('H:i');
            } catch (\Exception $e) {
                // Fallback to time if datetime parsing fails
                if ($matchTime) {
                    $displayTime = date('H:i', strtotime($matchTime));
                }
                if ($matchDate) {
                    $displayDate = date('d/m', strtotime($matchDate));
                }
            }
        } elseif ($matchTime) {
            $displayTime = date('H:i', strtotime($matchTime));
            if ($matchDate) {
                $displayDate = date('d/m', strtotime($matchDate));
            }
        }

        // Process match stats to separate home and away
        $homeMatchStats = null;
        $awayMatchStats = null;
        if ($matchStats && is_array($matchStats)) {
            foreach ($matchStats as $stat) {
                $teamId = $stat['team_id'] ?? null;
                if ($teamId == $homeTeamId) {
                    $homeMatchStats = $stat;
                } elseif ($teamId == $awayTeamId) {
                    $awayMatchStats = $stat;
                }
            }
        }

        return view('pages.match-detail', [
            'match' => $matchData,
            'homeTeam' => $homeTeam,
            'awayTeam' => $awayTeam,
            'league' => $league,
            'scores' => $scores,
            'matchDate' => $matchDate,
            'matchTime' => $matchTime,
            'displayDate' => $displayDate,
            'displayTime' => $displayTime,
            'matchId' => $id,
            'venue' => $venue,
            'matchOdds' => $matchOdds,
            'oddsData' => $oddsData,
            'homeMatchStats' => $homeMatchStats,
            'awayMatchStats' => $awayMatchStats,
            'matchLineups' => $matchLineups,
            'matchEvents' => $matchEvents,
        ]);
    }

    /**
     * Process match odds data for display
     */
    private function processOddsData(?array $matchOdds): array
    {
        if (!$matchOdds || !is_array($matchOdds)) {
            return [
                'full_match' => [
                    'handicap' => ['home' => null, 'away' => null, 'handicap' => null],
                    'over_under' => ['over' => null, 'under' => null, 'handicap' => null],
                    '1x2' => ['home' => null, 'draw' => null, 'away' => null],
                ],
                'first_half' => [
                    'handicap' => ['home' => null, 'away' => null, 'handicap' => null],
                    'over_under' => ['over' => null, 'under' => null, 'handicap' => null],
                    '1x2' => ['home' => null, 'draw' => null, 'away' => null],
                ],
            ];
        }

        $oddsData = [
            'full_match' => [
                'handicap' => ['home' => null, 'away' => null, 'handicap' => null],
                'over_under' => ['over' => null, 'under' => null, 'handicap' => null],
                '1x2' => ['home' => null, 'draw' => null, 'away' => null],
            ],
            'first_half' => [
                'handicap' => ['home' => null, 'away' => null, 'handicap' => null],
                'over_under' => ['over' => null, 'under' => null, 'handicap' => null],
                '1x2' => ['home' => null, 'draw' => null, 'away' => null],
            ],
        ];

        // Find Bet365 first, then fallback to first available bookmaker
        $preferredBookmaker = 'Bet365';
        
        foreach ($matchOdds as $oddsType) {
            $typeId = $oddsType['id'] ?? null;
            $typeName = $oddsType['name'] ?? '';
            $bookmakers = $oddsType['bookmakers'] ?? [];

            // Find preferred bookmaker or first available
            $selectedBookmaker = null;
            foreach ($bookmakers as $bookmaker) {
                if (($bookmaker['name'] ?? '') === $preferredBookmaker) {
                    $selectedBookmaker = $bookmaker;
                    break;
                }
            }
            if (!$selectedBookmaker && !empty($bookmakers)) {
                $selectedBookmaker = $bookmakers[0];
            }

            if (!$selectedBookmaker) {
                continue;
            }

            $oddsInfo = $selectedBookmaker['odds']['data'] ?? null;
            if (!$oddsInfo) {
                continue;
            }

            // Process based on odds type
            if ($typeId == 1 || $typeName == '1X2, Full Time Result') {
                // 1X2 odds
                if (isset($oddsInfo['home']) && isset($oddsInfo['draw']) && isset($oddsInfo['away'])) {
                    $oddsData['full_match']['1x2'] = [
                        'home' => $oddsInfo['home'],
                        'draw' => $oddsInfo['draw'],
                        'away' => $oddsInfo['away'],
                    ];
                }
            } elseif ($typeId == 3 || $typeName == 'Asian Handicap') {
                // Asian Handicap
                if (isset($oddsInfo['home']) && isset($oddsInfo['away']) && isset($oddsInfo['handicap'])) {
                    $oddsData['full_match']['handicap'] = [
                        'home' => $oddsInfo['home'],
                        'away' => $oddsInfo['away'],
                        'handicap' => $oddsInfo['handicap'],
                    ];
                }
            } elseif ($typeId == 2 || $typeName == 'Over/Under, Goal Line') {
                // Over/Under
                // Data can be array or object
                if (is_array($oddsInfo) && isset($oddsInfo[0])) {
                    $firstOdds = $oddsInfo[0];
                    if (isset($firstOdds['over']) && isset($firstOdds['under']) && isset($firstOdds['handicap'])) {
                        $oddsData['full_match']['over_under'] = [
                            'over' => $firstOdds['over'],
                            'under' => $firstOdds['under'],
                            'handicap' => $firstOdds['handicap'],
                        ];
                    }
                } elseif (isset($oddsInfo['over']) && isset($oddsInfo['under']) && isset($oddsInfo['handicap'])) {
                    $oddsData['full_match']['over_under'] = [
                        'over' => $oddsInfo['over'],
                        'under' => $oddsInfo['under'],
                        'handicap' => $oddsInfo['handicap'],
                    ];
                }
            }
        }

        return $oddsData;
    }

    /**
     * Process match odds info data (from match_odds_info API)
     */
    private function processOddsInfoData(?array $matchOddsInfo): array
    {
        if (!$matchOddsInfo || !isset($matchOddsInfo['markets']) || !is_array($matchOddsInfo['markets'])) {
            return [
                'full_match' => [
                    'handicap' => ['home' => null, 'away' => null, 'handicap' => null],
                    'over_under' => ['over' => null, 'under' => null, 'handicap' => null],
                    '1x2' => ['home' => null, 'draw' => null, 'away' => null],
                ],
                'first_half' => [
                    'handicap' => ['home' => null, 'away' => null, 'handicap' => null],
                    'over_under' => ['over' => null, 'under' => null, 'handicap' => null],
                    '1x2' => ['home' => null, 'draw' => null, 'away' => null],
                ], 
            ];
        }

        $oddsData = [
            'full_match' => [
                'handicap' => ['home' => null, 'away' => null, 'handicap' => null],
                'over_under' => ['over' => null, 'under' => null, 'handicap' => null],
                '1x2' => ['home' => null, 'draw' => null, 'away' => null],
            ],
            'first_half' => [
                'handicap' => ['home' => null, 'away' => null, 'handicap' => null],
                'over_under' => ['over' => null, 'under' => null, 'handicap' => null],
                '1x2' => ['home' => null, 'draw' => null, 'away' => null],
            ],
        ];

        foreach ($matchOddsInfo['markets'] as $market) {
            $marketId = $market['id'] ?? null;
            $marketName = $market['name'] ?? '';
            $oddsArray = $market['odds'] ?? [];

            if (empty($oddsArray)) {
                continue;
            }

            // Get the latest odds (first item in array, as it's sorted by date descending)
            $latestOdds = $oddsArray[0];

            // Check if market is for first half
            $isFirstHalf = (stripos($marketName, '1st Half') !== false || 
                           stripos($marketName, 'First Half') !== false ||
                           stripos($marketName, 'Half Time') !== false);

            // Process based on market type
            if ($marketId == 1 || $marketName == '1X2, Full Time Result') {
                // 1X2 odds for full match
                if (isset($latestOdds['home']) && isset($latestOdds['draw']) && isset($latestOdds['away'])) {
                    $oddsData['full_match']['1x2'] = [
                        'home' => $latestOdds['home'],
                        'draw' => $latestOdds['draw'],
                        'away' => $latestOdds['away'],
                    ];
                }
            } elseif ($marketId == 3 || $marketName == 'Asian Handicap') {
                // Asian Handicap for full match
                if (isset($latestOdds['home']) && isset($latestOdds['away']) && isset($latestOdds['handicap'])) {
                    $oddsData['full_match']['handicap'] = [
                        'home' => $latestOdds['home'],
                        'away' => $latestOdds['away'],
                        'handicap' => $latestOdds['handicap'],
                    ];
                }
            } elseif (($marketId == 2 || stripos($marketName, 'Over/Under') !== false || stripos($marketName, 'Goal Line') !== false)) {
                // Over/Under market - check if it's for first half or full match
                if ($isFirstHalf) {
                    // Over/Under for first half
                    if (isset($latestOdds['over']) && isset($latestOdds['under']) && isset($latestOdds['handicap'])) {
                        $oddsData['first_half']['over_under'] = [
                            'over' => $latestOdds['over'],
                            'under' => $latestOdds['under'],
                            'handicap' => $latestOdds['handicap'],
                        ];
                    }
                } else {
                    // Over/Under for full match
                    if (isset($latestOdds['over']) && isset($latestOdds['under']) && isset($latestOdds['handicap'])) {
                        $oddsData['full_match']['over_under'] = [
                            'over' => $latestOdds['over'],
                            'under' => $latestOdds['under'],
                            'handicap' => $latestOdds['handicap'],
                        ];
                    }
                }
            } elseif ($marketId == 4 || $marketName == '1st Half Asian Handicap') {
                // Asian Handicap for first half
                if (isset($latestOdds['home']) && isset($latestOdds['away']) && isset($latestOdds['handicap'])) {
                    $oddsData['first_half']['handicap'] = [
                        'home' => $latestOdds['home'],
                        'away' => $latestOdds['away'],
                        'handicap' => $latestOdds['handicap'],
                    ];
                }
            } elseif ($marketId == 8 || $marketName == 'Half Time Result') {
                // 1X2 odds for first half
                if (isset($latestOdds['home']) && isset($latestOdds['draw']) && isset($latestOdds['away'])) {
                    $oddsData['first_half']['1x2'] = [
                        'home' => $latestOdds['home'],
                        'draw' => $latestOdds['draw'],
                        'away' => $latestOdds['away'],
                    ];
                }
            }
        }

        return $oddsData;
    }

    /**
     * Calculate detailed team statistics from matches
     */
    private function calculateTeamDetailedStats(?array $matches, $teamId): array
    {
        if (!$matches || !is_array($matches) || empty($matches)) {
            return [
                'last_3_matches' => [
                    'home' => ['corners' => null, 'possession' => null, 'shots_on_target' => null, 'fouls' => null],
                    'away' => ['corners' => null, 'possession' => null, 'shots_on_target' => null, 'fouls' => null],
                ],
                'last_10_matches' => [
                    'home' => ['corners' => null, 'possession' => null, 'shots_on_target' => null, 'fouls' => null],
                    'away' => ['corners' => null, 'possession' => null, 'shots_on_target' => null, 'fouls' => null],
                ],
            ];
        }

        $last3Matches = array_slice($matches, 0, 3);
        $last10Matches = array_slice($matches, 0, 10);

        $calculateStats = function($matchList, $teamId) {
            $homeStats = ['corners' => [], 'possession' => [], 'shots_on_target' => [], 'fouls' => []];
            $awayStats = ['corners' => [], 'possession' => [], 'shots_on_target' => [], 'fouls' => []];

            foreach ($matchList as $match) {
                $homeTeamId = $match['teams']['home']['id'] ?? null;
                $awayTeamId = $match['teams']['away']['id'] ?? null;
                $isHome = ($homeTeamId == $teamId);
                $isAway = ($awayTeamId == $teamId);

                if (!$isHome && !$isAway) {
                    continue;
                }

                $stats = $match['stats'] ?? [];

                // Corners
                if (isset($stats['corners'])) {
                    if ($isHome) {
                        $homeStats['corners'][] = $stats['corners']['home'] ?? 0;
                    } else {
                        $awayStats['corners'][] = $stats['corners']['away'] ?? 0;
                    }
                }

                // Possession
                if (isset($stats['possession'])) {
                    if ($isHome) {
                        $homeStats['possession'][] = $stats['possession']['home'] ?? 0;
                    } else {
                        $awayStats['possession'][] = $stats['possession']['away'] ?? 0;
                    }
                }

                // Shots on target
                if (isset($stats['shots_on_target'])) {
                    if ($isHome) {
                        $homeStats['shots_on_target'][] = $stats['shots_on_target']['home'] ?? 0;
                    } else {
                        $awayStats['shots_on_target'][] = $stats['shots_on_target']['away'] ?? 0;
                    }
                }

                // Fouls
                if (isset($stats['fouls'])) {
                    if ($isHome) {
                        $homeStats['fouls'][] = $stats['fouls']['home'] ?? 0;
                    } else {
                        $awayStats['fouls'][] = $stats['fouls']['away'] ?? 0;
                    }
                }
            }

            return [
                'home' => [
                    'corners' => !empty($homeStats['corners']) ? array_sum($homeStats['corners']) / count($homeStats['corners']) : null,
                    'possession' => !empty($homeStats['possession']) ? array_sum($homeStats['possession']) / count($homeStats['possession']) : null,
                    'shots_on_target' => !empty($homeStats['shots_on_target']) ? array_sum($homeStats['shots_on_target']) / count($homeStats['shots_on_target']) : null,
                    'fouls' => !empty($homeStats['fouls']) ? array_sum($homeStats['fouls']) / count($homeStats['fouls']) : null,
                ],
                'away' => [
                    'corners' => !empty($awayStats['corners']) ? array_sum($awayStats['corners']) / count($awayStats['corners']) : null,
                    'possession' => !empty($awayStats['possession']) ? array_sum($awayStats['possession']) / count($awayStats['possession']) : null,
                    'shots_on_target' => !empty($awayStats['shots_on_target']) ? array_sum($awayStats['shots_on_target']) / count($awayStats['shots_on_target']) : null,
                    'fouls' => !empty($awayStats['fouls']) ? array_sum($awayStats['fouls']) / count($awayStats['fouls']) : null,
                ],
            ];
        };

        return [
            'last_3_matches' => $calculateStats($last3Matches, $teamId),
            'last_10_matches' => $calculateStats($last10Matches, $teamId),
        ];
    }
}

