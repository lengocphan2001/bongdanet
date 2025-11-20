<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SoccerApiService
{
    protected string $baseUrl;
    protected string $username;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = config('soccer_api.base_url');
        $this->username = config('soccer_api.username');
        $this->token = config('soccer_api.token');
    }

    /**
     * Make API request to Soccer API
     *
     * @param string $endpoint
     * @param array $params
     * @return array|null
     */
    public function makeRequest(string $endpoint, array $params = []): ?array
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        
        // Add default authentication parameters
        $params['user'] = $this->username;
        $params['token'] = $this->token;
        // Add UTC timezone parameter
        $params['utc'] = 7;

        try {
            $response = Http::timeout(3)
                ->retry(1, 30)
                ->get($url, $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Soccer API request failed', [
                'url' => $url,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Soccer API exception', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get base URL and default params for API requests
     * Used for building requests in Http::pool()
     *
     * @param string $endpoint
     * @param array $params
     * @return array ['url' => string, 'params' => array]
     */
    public function getRequestConfig(string $endpoint, array $params = []): array
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        
        // Add default authentication parameters
        $params['user'] = $this->username;
        $params['token'] = $this->token;
        // Add UTC timezone parameter
        $params['utc'] = 7;

        return [
            'url' => $url,
            'params' => $params,
        ];
    }

    /**
     * Get countries list
     *
     * @return array|null
     */
    public function getCountries(): ?array
    {
        return $this->makeRequest('countries');
    }

    /**
     * Get leagues by country
     *
     * @param string $countryId
     * @return array|null
     */
    public function getLeagues(string $countryId): ?array
    {
        return $this->makeRequest('leagues', ['country_id' => $countryId]);
    }

    /**
     * Get matches
     *
     * @param array $params
     * @return array|null
     */
    public function getMatches(array $params = []): ?array
    {
        return $this->makeRequest('matches', $params);
    }

    /**
     * Get teams
     *
     * @param array $params
     * @return array|null
     */
    public function getTeams(array $params = []): ?array
    {
        return $this->makeRequest('teams', $params);
    }

    /**
     * Get leagues list
     * Fetches all pages if API supports pagination
     *
     * @return array|null
     */
    public function getLeaguesList(): ?array
    {
        // Use a new cache key to force refresh all pages
        $cacheKey = 'soccer_api:leagues_list:all_pages';
        
        // Cache for 1 week (7 days) for standings page
        return Cache::remember($cacheKey, 604800, function () {
            $allLeagues = [];
            $page = 1;
            $hasMorePages = true;
            $firstResponse = null;
            
            while ($hasMorePages) {
                $response = $this->makeRequest('leagues', ['t' => 'list', 'page' => $page]);
                
                // Store first response for fallback
                if ($page === 1) {
                    $firstResponse = $response;
                }
                
                if (!$response) {
                    \Log::warning("No response for page {$page}");
                    break;
                }
                
                // Extract leagues from response
                $leagues = $response['data'] ?? [];
                if (is_array($leagues) && !empty($leagues)) {
                    $allLeagues = array_merge($allLeagues, $leagues);
                    \Log::info("Fetched page {$page}", [
                        'leagues_count' => count($leagues),
                        'total_so_far' => count($allLeagues),
                    ]);
                } else {
                    \Log::warning("No leagues in page {$page} response");
                }
                
                // Check if there are more pages
                $meta = $response['meta'] ?? [];
                $currentPage = (int)($meta['page'] ?? $page);
                $totalPages = (int)($meta['pages'] ?? 1);
                $totalFromMeta = (int)($meta['total'] ?? 0);
                
                \Log::info("Page {$page} meta", [
                    'current_page' => $currentPage,
                    'total_pages' => $totalPages,
                    'total_from_meta' => $totalFromMeta,
                ]);
                
                // If current page is less than total pages, continue
                if ($currentPage < $totalPages) {
                    $page++;
                } else {
                    $hasMorePages = false;
                    \Log::info("Reached last page. Total pages: {$totalPages}, Current page: {$currentPage}");
                }
                
                // Safety check: limit to 100 pages to avoid infinite loops
                if ($page > 100) {
                    \Log::warning("Reached safety limit of 100 pages");
                    break;
                }
            }
            
            // Return response structure with all leagues
            if (!empty($allLeagues)) {
                \Log::info('Leagues list fetched', [
                    'total_leagues' => count($allLeagues),
                    'pages_fetched' => $page,
                ]);
                
                return [
                    'data' => $allLeagues,
                    'meta' => [
                        'count' => count($allLeagues),
                        'pages' => $page,
                        'page' => 1,
                        'total' => count($allLeagues),
                    ],
                ];
            }
            
            // Fallback to original response if no pagination or no leagues found
            \Log::warning('No leagues found or pagination failed', [
                'first_response_meta' => $firstResponse['meta'] ?? null,
            ]);
            return $firstResponse;
        });
    }

    /**
     * Get league info
     *
     * @param int|string $leagueId
     * @return array|null
     */
    public function getLeagueInfo($leagueId): ?array
    {
        $cacheKey = 'soccer_api:league_info:' . $leagueId;
        
        // Cache for 1 hour (league info doesn't change frequently)
        return Cache::remember($cacheKey, 3600, function () use ($leagueId) {
            $response = $this->makeRequest('leagues', [
                't' => 'info',
                'id' => $leagueId,
            ]);
            
            if ($response && isset($response['data'])) {
                return $response['data'];
            }
            
            return null;
        });
    }

    /**
     * Get season info
     *
     * @param int|string $seasonId
     * @return array|null
     */
    public function getSeasonInfo($seasonId): ?array
    {
        $cacheKey = 'soccer_api:season_info:' . $seasonId;
        
        // Cache for 1 hour (season info doesn't change frequently)
        return Cache::remember($cacheKey, 3600, function () use ($seasonId) {
            $response = $this->makeRequest('seasons', [
                't' => 'info',
                'id' => $seasonId,
            ]);
            
            if ($response && isset($response['data'])) {
                return $response['data'];
            }
            
            return null;
        });
    }

    /**
     * Get fixtures by round_id
     * Optimized for results page - minimal includes
     *
     * @param int|string $roundId
     * @return array|null
     */
    public function getFixturesByRound($roundId): ?array
    {
        $cacheKey = 'soccer_api:fixtures_round:' . $roundId;
        
        // Cache for 5 minutes (fixtures can change)
        return Cache::remember($cacheKey, 300, function () use ($roundId) {
            $response = $this->makeRequest('fixtures', [
                't' => 'season',
                'round_id' => $roundId,
                // No include needed - results page only needs basic match data
            ]);
            
            if ($response && isset($response['data']) && is_array($response['data'])) {
                return $response['data'];
            }
            
            return [];
        });
    }

    public function getStandings(string $leagueId, string $seasonId): ?array
    {
        $cacheKey = 'soccer_api:standings:' . $leagueId . ':' . $seasonId;
        
        // Cache for 1 week (7 days) for standings page
        return Cache::remember($cacheKey, 604800, function () use ($leagueId, $seasonId) {
            return $this->makeRequest('leagues', [
                't' => 'standings',
                'season_id' => $seasonId,
            ]);
        });
    }

    /**
     * Get live scores
     * Includes all necessary data for modal display (events, stats, odds, etc.)
     *
     * @param array $params
     * @return array|null
     */
    public function getLivescores(array $params = []): ?array
    {
        $defaultParams = [
            't' => 'live',
            'include' => 'events,stats,odds,odds_prematch,odds_inplay',
        ];

        $params = array_merge($defaultParams, $params);
        
        // Check if cache should be bypassed
        $bypassCache = isset($params['_bypass_cache']) && $params['_bypass_cache'];
        unset($params['_bypass_cache']); // Remove from params before making request
        
        // If include is explicitly set to empty string, remove it from params
        if (isset($params['include']) && $params['include'] === '') {
            unset($params['include']);
        }
        
        // Create cache key based on params
        $cacheKey = 'soccer_api:livescores:' . md5(json_encode($params));
        
        // If bypass cache, fetch fresh data directly without cache
        if ($bypassCache) {
            // Also clear cache to ensure fresh data next time
            Cache::forget($cacheKey);
            return $this->makeRequest('livescores', $params);
        }
        
        // Cache for 3 seconds (live data changes frequently, need fresh data for corners and events)
        // Reduced cache time to ensure corner and event data updates properly
        return Cache::remember($cacheKey, 3, function () use ($params) {
            return $this->makeRequest('livescores', $params);
        });
    }

    /**
     * Get upcoming matches (not started)
     * Uses livescores API with t=notstarted parameter
     * Optimized for home page - minimal includes
     *
     * @param array $params
     * @return array|null
     */
    public function getUpcomingMatches(array $params = []): ?array
    {
        $defaultParams = [
            't' => 'notstarted',
            'include' => 'odds', // Only odds needed for home page
        ];

        $params = array_merge($defaultParams, $params);
        
        // Check if cache should be bypassed
        $bypassCache = isset($params['_bypass_cache']) && $params['_bypass_cache'];
        unset($params['_bypass_cache']); // Remove from params before making request
        
        // Create cache key based on params
        $cacheKey = 'soccer_api:upcoming:' . md5(json_encode($params));
        
        // If bypass cache, fetch fresh data directly without cache
        if ($bypassCache) {
            // Clear cache to ensure fresh data
            Cache::forget($cacheKey);
            return $this->makeRequest('livescores', $params);
        }
        
        // Cache for 60 seconds (upcoming matches don't change that frequently)
        return Cache::remember($cacheKey, 60, function () use ($params) {
            return $this->makeRequest('livescores', $params);
        });
    }

    /**
     * Get finished matches (ended)
     * Optimized for faster loading - minimal includes
     *
     * @param array $params
     * @return array|null
     */
    public function getFinishedMatches(array $params = []): ?array
    {
        $defaultParams = [
            't' => 'ended',
            // No include needed - results page only needs basic match data
        ];

        $params = array_merge($defaultParams, $params);
        
        // Create cache key based on params
        $cacheKey = 'soccer_api:finished:' . md5(json_encode($params));
        
        // Cache for 1 week (7 days) for results page
        return Cache::remember($cacheKey, 604800, function () use ($params) {
            return $this->makeRequest('livescores', $params);
        });
    }

    /**
     * Get schedule matches (fixtures)
     * Optimized for schedule page - minimal includes
     *
     * @param string $date Date in format Y-m-d (e.g., 2025-11-04)
     * @param array $params
     * @return array|null
     */
    public function getScheduleMatches(string $date, array $params = []): ?array
    {
        $defaultParams = [
            't' => 'schedule',
            'd' => $date,
            // No include needed - schedule page only needs basic match data
        ];

        $params = array_merge($defaultParams, $params);
        
        // Check if cache should be bypassed
        $bypassCache = isset($params['_bypass_cache']) && $params['_bypass_cache'];
        unset($params['_bypass_cache']); // Remove from params before making request
        
        // Check if should sort by time
        $sortByTime = isset($params['_sort_by_time']) && $params['_sort_by_time'];
        unset($params['_sort_by_time']); // Remove from params before making request
        
        // Check if should filter past matches
        $filterPastMatches = isset($params['_filter_past_matches']) && $params['_filter_past_matches'];
        unset($params['_filter_past_matches']); // Remove from params before making request
        
        // Create cache key based on params
        $cacheKey = 'soccer_api:schedule:' . md5(json_encode($params));
        
        // If bypass cache, fetch fresh data directly without cache
        if ($bypassCache) {
            // Clear cache to ensure fresh data
            Cache::forget($cacheKey);
            $response = $this->makeRequest('fixtures', $params);
            // Filter past matches if requested
            if ($filterPastMatches && $response && isset($response['data']) && is_array($response['data'])) {
                $this->filterPastMatches($response['data']);
            }
            // Sort by time if requested
            if ($sortByTime && $response && isset($response['data']) && is_array($response['data'])) {
                $this->sortMatchesByTime($response['data']);
            }
            return $response;
        }
        
        // Cache for 1 week (7 days) for schedule page
        $response = Cache::remember($cacheKey, 604800, function () use ($params) {
            return $this->makeRequest('fixtures', $params);
        });
        
        // Filter past matches if requested (after cache retrieval)
        if ($filterPastMatches && $response && isset($response['data']) && is_array($response['data'])) {
            $this->filterPastMatches($response['data']);
        }
        
        // Sort by time if requested (after cache retrieval)
        if ($sortByTime && $response && isset($response['data']) && is_array($response['data'])) {
            $this->sortMatchesByTime($response['data']);
        }
        
        return $response;
    }
    
    /**
     * Filter out past matches (matches that have already started)
     * 
     * @param array $matches Array of match data from API
     * @return void
     */
    protected function filterPastMatches(array &$matches): void
    {
        $timezone = config('app.timezone', 'Asia/Ho_Chi_Minh');
        $now = Carbon::now($timezone);
        
        $matches = array_filter($matches, function($match) use ($now, $timezone) {
            // Get match datetime from API response
            $matchDatetime = $match['time']['datetime'] ?? $match['time']['date'] ?? null;
            
            if (!$matchDatetime) {
                // If no datetime, exclude the match
                return false;
            }
            
            try {
                $matchDateTime = Carbon::parse($matchDatetime, $timezone);
                // Only include matches with datetime >= current time
                return $matchDateTime->greaterThanOrEqualTo($now);
            } catch (\Exception $e) {
                // If parsing fails, exclude the match
                return false;
            }
        });
        
        // Re-index array after filtering
        $matches = array_values($matches);
    }
    
    /**
     * Sort matches array by starting datetime (earliest first)
     * 
     * @param array $matches Array of match data from API
     * @return void
     */
    protected function sortMatchesByTime(array &$matches): void
    {
        usort($matches, function($a, $b) {
            $datetimeA = $a['time']['datetime'] ?? $a['time']['date'] ?? $a['starting_datetime'] ?? null;
            $datetimeB = $b['time']['datetime'] ?? $b['time']['date'] ?? $b['starting_datetime'] ?? null;
            
            if ($datetimeA === null && $datetimeB === null) return 0;
            if ($datetimeA === null) return 1; // Put null at the end
            if ($datetimeB === null) return -1; // Put null at the end
            
            $timestampA = strtotime($datetimeA);
            $timestampB = strtotime($datetimeB);
            
            return $timestampA <=> $timestampB; // Ascending order (earliest first)
        });
    }

    /**
     * Get schedule matches with odds_prematch for odds page
     *
     * @param string $date Date in format Y-m-d (e.g., 2025-11-04)
     * @param array $params
     * @return array|null
     */
    public function getScheduleMatchesWithOdds(string $date, array $params = []): ?array
    {
        $defaultParams = [
            't' => 'schedule',
            'd' => $date,
            'include' => 'odds_prematch', // Include odds_prematch for odds page
        ];

        $params = array_merge($defaultParams, $params);
        
        // Create cache key based on params
        $cacheKey = 'soccer_api:schedule_odds:' . md5(json_encode($params));
        
        // Cache for 1 week (7 days) for odds page
        return Cache::remember($cacheKey, 604800, function () use ($params) {
            return $this->makeRequest('fixtures', $params);
        });
    }

    /**
     * Get match statistics using stats API
     *
     * @param int|string $matchId
     * @return array|null
     */
    public function getMatchStats($matchId): ?array
    {
        $cacheKey = 'soccer_api:match_stats:' . $matchId;
        
        // Cache for 5 minutes (stats don't change frequently)
        return Cache::remember($cacheKey, 300, function () use ($matchId) {
            $params = [
                't' => 'match',
                'id' => $matchId,
            ];
            
            $response = $this->makeRequest('stats', $params);
            
            if ($response && isset($response['data']) && is_array($response['data'])) {
                return $response['data'];
            }
            
            return null;
        });
    }

    /**
     * Get match lineups using fixtures API
     *
     * @param int|string $matchId
     * @return array|null
     */
    public function getMatchLineups($matchId): ?array
    {
        $cacheKey = 'soccer_api:match_lineups:' . $matchId;
        
        // Cache for 5 minutes (lineups don't change frequently)
        return Cache::remember($cacheKey, 300, function () use ($matchId) {
            $params = [
                't' => 'match_lineups',
                'id' => $matchId,
                // No include needed - stats are fetched separately via getMatchStats()
            ];
            
            $response = $this->makeRequest('fixtures', $params);
            
            if ($response && isset($response['data'])) {
                return $response['data'];
            }
            
            return null;
        });
    }

    /**
     * Get match events (for accurate corner data)
     *
     * @param int|string $matchId
     * @return array|null
     */
    public function getMatchEvents($matchId): ?array
    {
        $cacheKey = 'soccer_api:match_events:' . $matchId;
        
        // Cache for 5 minutes (events don't change frequently)
        return Cache::remember($cacheKey, 300, function () use ($matchId) {
            $params = [
                't' => 'match_events',
                'id' => $matchId,
            ];
            
            $response = $this->makeRequest('fixtures', $params);
            
            if ($response && isset($response['data']) && is_array($response['data'])) {
                return $response['data'];
            }
            
            return null;
        });
    }

    /**
     * Get fixture info using fixtures API with t=info
     *
     * @param int|string $matchId
     * @return array|null
     */
    public function getFixtureInfo($matchId, $includeOdds = false): ?array
    {
        $cacheKey = 'soccer_api:fixture_info:' . $matchId . ($includeOdds ? '_with_odds' : '');
        
        // Cache for 5 minutes (match info doesn't change frequently)
        return Cache::remember($cacheKey, 300, function () use ($matchId, $includeOdds) {
            $params = [
                't' => 'info',
                'id' => $matchId,
            ];
            
            // Include odds_prematch if requested
            if ($includeOdds) {
                $params['include'] = 'odds_prematch';
            }
            
            $response = $this->makeRequest('fixtures', $params);
            
            if ($response && isset($response['data'])) {
                return $response['data'];
            }
            
            return null;
        });
    }

    /**
     * Get venue info using venues API with t=info
     *
     * @param int|string $venueId
     * @return array|null
     */
    public function getVenueInfo($venueId): ?array
    {
        if (!$venueId) {
            return null;
        }

        $params = [
            't' => 'info',
            'id' => $venueId,
        ];
        
        $response = $this->makeRequest('venues', $params);
        
        if ($response && isset($response['data'])) {
            return $response['data'];
        }
        
        return null;
    }

    /**
     * Get match odds using fixtures API with t=match_odds
     *
     * @param int|string $matchId
     * @return array|null
     */
    public function getMatchOdds($matchId): ?array
    {
        $params = [
            't' => 'match_odds',
            'id' => $matchId,
        ];
        
        $response = $this->makeRequest('fixtures', $params);
        
        if ($response && isset($response['data']) && is_array($response['data'])) {
            return $response['data'];
        }
        
        return null;
    }

    /**
     * Get match odds info for a specific bookmaker using fixtures API with t=match_odds_info
     *
     * @param int|string $matchId
     * @param int|string $bookmakerId Bookmaker ID (default: 2 for Bet365)
     * @return array|null
     */
    public function getMatchOddsInfo($matchId, $bookmakerId = 2): ?array
    {
        $params = [
            't' => 'match_odds_info',
            'id' => $matchId,
            'bookmaker_id' => $bookmakerId,
        ];
        
        $response = $this->makeRequest('fixtures', $params);
        
        if ($response && isset($response['data'])) {
            return $response['data'];
        }
        
        return null;
    }

    /**
     * Get team statistics using stats API with t=team
     *
     * @param int|string $teamId
     * @param int|string $seasonId
     * @return array|null
     */
    public function getTeamStats($teamId, $seasonId): ?array
    {
        if (!$teamId || !$seasonId) {
            return null;
        }

        $params = [
            't' => 'team',
            'id' => $teamId,
            'season_id' => $seasonId,
        ];
        
        $response = $this->makeRequest('stats', $params);
        
        if ($response && isset($response['data'])) {
            return $response['data'];
        }
        
        return null;
    }

    /**
     * Get team fixtures/matches for a season to calculate detailed stats
     *
     * @param int|string $teamId
     * @param int|string $seasonId
     * @param int $limit Limit number of matches to fetch
     * @return array|null
     */
    public function getTeamMatches($teamId, $seasonId, $limit = 10): ?array
    {
        if (!$teamId || !$seasonId) {
            return null;
        }

        $params = [
            't' => 'team',
            'id' => $teamId,
            'season_id' => $seasonId,
            'include' => 'events,lineups,stats,odds',
        ];
        
        $response = $this->makeRequest('fixtures', $params);
        
        if ($response && isset($response['data']) && is_array($response['data'])) {
            // Sort by date descending (most recent first) and limit
            usort($response['data'], function($a, $b) {
                $dateA = $a['time']['datetime'] ?? $a['time']['date'] ?? '';
                $dateB = $b['time']['datetime'] ?? $b['time']['date'] ?? '';
                return strtotime($dateB) <=> strtotime($dateA);
            });
            
            return array_slice($response['data'], 0, $limit);
        }
        
        return null;
    }

    /**
     * Get detailed match events and statistics for modal display
     * Uses the same API approach as corner data
     *
     * @param int|string $matchId
     * @return array|null
     */
    public function getMatchDetails($matchId): ?array
    {
        try {
            // Get match details with stats using fixtures API (similar to livescores)
            $params = [
                't' => 'match',
                'id' => $matchId,
                'include' => 'events,stats,odds,odds_prematch,odds_inplay',
            ];
            $matchResponse = $this->makeRequest('fixtures', $params);
            
            // Fallback: try to get from livescores if fixtures fails
            if (!$matchResponse || !isset($matchResponse['data'])) {
                // Try to find match in livescores
                $liveResponse = $this->getLivescores();
                $upcomingResponse = $this->getUpcomingMatches();
                
                $match = null;
                if ($liveResponse && isset($liveResponse['data']) && is_array($liveResponse['data'])) {
                    foreach ($liveResponse['data'] as $m) {
                        if (($m['id'] ?? null) == $matchId) {
                            $match = $m;
                            break;
                        }
                    }
                }
                
                if (!$match && $upcomingResponse && isset($upcomingResponse['data']) && is_array($upcomingResponse['data'])) {
                    foreach ($upcomingResponse['data'] as $m) {
                        if (($m['id'] ?? null) == $matchId) {
                            $match = $m;
                            break;
                        }
                    }
                }
                
                if (!$match) {
                    return null;
                }
            } else {
                $match = $matchResponse['data'] ?? null;
                if (!$match) {
                    return null;
                }
            }
            
            $homeTeamId = $match['teams']['home']['id'] ?? null;
            $awayTeamId = $match['teams']['away']['id'] ?? null;
            
            // Extract all events (goals, cards, etc.) from events array in main API response
            $matchEvents = [];
            
            // Use events from main API (no need for separate match_events API call)
            $apiEvents = $match['events'] ?? [];
            foreach ($apiEvents as $event) {
                $eventType = $event['type'] ?? null;
                $eventTeamId = $event['team_id'] ?? null;
                
                // Only include relevant events: goals, yellow cards, red cards
                if (in_array($eventType, ['goal', 'yellowcard', 'redcard', 'yellowredcard'])) {
                    $matchEvents[] = [
                        'type' => $eventType,
                        'team_id' => $eventTeamId,
                        'minute' => $event['minute'] ?? null,
                        'extra_minute' => $event['extra_minute'] ?? null,
                        'player_name' => $event['player_name'] ?? null,
                        'related_player_name' => $event['related_player_name'] ?? null,
                        'period' => $event['period'] ?? '',
                    ];
                }
            }
            
            // Sort events by minute
            usort($matchEvents, function($a, $b) {
                $minuteA = (int)($a['minute'] ?? 0);
                $minuteB = (int)($b['minute'] ?? 0);
                if ($minuteA === $minuteB) {
                    $extraA = (int)($a['extra_minute'] ?? 0);
                    $extraB = (int)($b['extra_minute'] ?? 0);
                    return $extraA <=> $extraB;
                }
                return $minuteA <=> $minuteB;
            });
            
            // Extract statistics from stats array
            $stats = $match['stats'] ?? [];
            $homeStats = [];
            $awayStats = [];
            
            foreach ($stats as $stat) {
                $teamId = $stat['team_id'] ?? null;
                if ($teamId == $homeTeamId) {
                    $homeStats = $stat;
                } elseif ($teamId == $awayTeamId) {
                    $awayStats = $stat;
                }
            }
            
            return [
                'events' => $matchEvents,
                'home_stats' => $homeStats,
                'away_stats' => $awayStats,
                'home_team' => [
                    'id' => $homeTeamId,
                    'name' => $match['teams']['home']['name'] ?? '',
                    'img' => $match['teams']['home']['img'] ?? null,
                ],
                'away_team' => [
                    'id' => $awayTeamId,
                    'name' => $match['teams']['away']['name'] ?? '',
                    'img' => $match['teams']['away']['img'] ?? null,
                ],
                'home_position' => $match['standings']['home_position'] ?? null,
                'away_position' => $match['standings']['away_position'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch match details: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Format score for display
     *
     * @param array $scores
     * @param bool $isNotStarted
     * @return string
     */
    protected function formatScore(array $scores, bool $isNotStarted): string
    {
        // For not started matches, return empty or "-"
        if ($isNotStarted) {
            return '';
        }
        
        // Priority 1: Use ft_score (full time score) if available
        if (isset($scores['ft_score']) && $scores['ft_score'] !== '' && $scores['ft_score'] !== null) {
            return $scores['ft_score'];
        }
        
        // Priority 2: Use home_score and away_score
        $homeScore = $scores['home_score'] ?? '';
        $awayScore = $scores['away_score'] ?? '';
        
        // If both scores are empty, return empty string
        if ($homeScore === '' && $awayScore === '') {
            return '';
        }
        
        // If one score is empty, treat as 0
        $homeScore = $homeScore === '' ? '0' : $homeScore;
        $awayScore = $awayScore === '' ? '0' : $awayScore;
        
        return $homeScore . '-' . $awayScore;
    }

    /**
     * Transform API match data to table format (lightweight version for finished matches)
     *
     * @param array $apiMatch
     * @return array
     */
    public function transformMatchToTableFormat(array $apiMatch): array
    {
        // Only status 1 (Inplay) is considered live
        $isLive = ($apiMatch['status'] == 1);
        
        // Check if match is not started
        $isNotStarted = ($apiMatch['status'] == 0 || $apiMatch['status_name'] == 'Notstarted');
        
        // Get starting datetime for sorting
        $startingDatetime = null;
        $matchDate = null;
        if (isset($apiMatch['time']['datetime']) && $apiMatch['time']['datetime'] !== '') {
            $startingDatetime = $apiMatch['time']['datetime'];
            // Extract date from datetime
            try {
                $matchDate = date('Y-m-d', strtotime($startingDatetime));
            } catch (\Exception $e) {
                $matchDate = substr($startingDatetime, 0, 10);
            }
        } elseif (isset($apiMatch['time']['time']) && $apiMatch['time']['time'] !== '') {
            // If only time is available, combine with date
            $matchDate = $apiMatch['time']['date'] ?? date('Y-m-d');
            $startingDatetime = $matchDate . ' ' . $apiMatch['time']['time'];
        } elseif (isset($apiMatch['time']['date']) && $apiMatch['time']['date'] !== '') {
            $matchDate = $apiMatch['time']['date'];
        }
        
        // Determine time display
        $timeDisplay = '';
        if ($isLive && isset($apiMatch['time']['minute']) && $apiMatch['time']['minute'] !== '') {
            $timeDisplay = $apiMatch['time']['minute'] . "'";
        } elseif ($apiMatch['status'] == 11 || $apiMatch['status_name'] == 'Halftime') {
            $timeDisplay = 'HT';
        } elseif ($apiMatch['status'] == 2 || $apiMatch['status_name'] == 'Finished') {
            $timeDisplay = 'FT';
        } elseif (isset($apiMatch['time']['time']) && $apiMatch['time']['time'] !== '') {
            // Format time for schedule matches (e.g., "15:00:00" -> "15:00")
            $timeDisplay = date('H:i', strtotime($apiMatch['time']['time']));
        } elseif (isset($apiMatch['time']['datetime']) && $apiMatch['time']['datetime'] !== '') {
            // Fallback to datetime if time is not available
            $timeDisplay = date('H:i', strtotime($apiMatch['time']['datetime']));
        } else {
            $timeDisplay = '-';
        }

        // Determine status display
        $statusDisplay = $timeDisplay;
        if ($apiMatch['status_name'] == 'Halftime') {
            $statusDisplay = 'HT';
        } elseif ($apiMatch['status_name'] == 'Finished') {
            $statusDisplay = 'FT';
        } elseif ($isLive && isset($apiMatch['time']['minute'])) {
            $statusDisplay = $apiMatch['time']['minute'] . "'";
        }

        // Build stats array based on available data
        $stats = [];
        if (isset($apiMatch['events']) && !empty($apiMatch['events'])) {
            $stats[] = 'flag';
        }
        if (isset($apiMatch['stats']) && !empty($apiMatch['stats'])) {
            $stats[] = 'ball';
        }
        if (isset($apiMatch['coverage']['has_lineups']) && $apiMatch['coverage']['has_lineups']) {
            $stats[] = 'jersey';
        }

        // Extract odds from odds_prematch
        $oddsData = $this->extractOddsData($apiMatch['odds_prematch'] ?? []);
        
        // Default odds display (1X2 from first available bookmaker)
        $odds1X2 = '- / - / -';
        if (!empty($oddsData['1X2'])) {
            $firstBookmaker = array_key_first($oddsData['1X2']);
            if ($firstBookmaker && isset($oddsData['1X2'][$firstBookmaker]['home'])) {
                $odds1X2 = sprintf(
                    '%s / %s / %s',
                    $oddsData['1X2'][$firstBookmaker]['home'] ?? '-',
                    $oddsData['1X2'][$firstBookmaker]['draw'] ?? '-',
                    $oddsData['1X2'][$firstBookmaker]['away'] ?? '-'
                );
            }
        }
        
        // Default Over/Under odds (from first available bookmaker)
        $oddsOverUnder = '- / - / -';
        $oddsOverUnderHandicap = null;
        if (!empty($oddsData['Over/Under'])) {
            $firstBookmaker = array_key_first($oddsData['Over/Under']);
            if ($firstBookmaker && isset($oddsData['Over/Under'][$firstBookmaker]['over'])) {
                $handicap = $oddsData['Over/Under'][$firstBookmaker]['handicap'] ?? null;
                $oddsOverUnderHandicap = $handicap;
                $oddsOverUnder = sprintf(
                    '%s / %s / %s',
                    $oddsData['Over/Under'][$firstBookmaker]['over'] ?? '-',
                    $handicap !== null ? $handicap : '-',
                    $oddsData['Over/Under'][$firstBookmaker]['under'] ?? '-'
                );
            }
        }
        
        // Default Asian Handicap odds (from first available bookmaker)
        $oddsAsianHandicap = '- / - / -';
        $oddsAsianHandicapValue = null;
        if (!empty($oddsData['Asian Handicap'])) {
            $firstBookmaker = array_key_first($oddsData['Asian Handicap']);
            if ($firstBookmaker && isset($oddsData['Asian Handicap'][$firstBookmaker]['home'])) {
                $handicap = $oddsData['Asian Handicap'][$firstBookmaker]['handicap'] ?? null;
                $oddsAsianHandicapValue = $handicap;
                $oddsAsianHandicap = sprintf(
                    '%s / %s / %s',
                    $oddsData['Asian Handicap'][$firstBookmaker]['home'] ?? '-',
                    $handicap !== null ? $handicap : '-',
                    $oddsData['Asian Handicap'][$firstBookmaker]['away'] ?? '-'
                );
            }
        }

        // Extract corner data from events array in main API response (no need for match_events API)
        $homeTeamId = $apiMatch['teams']['home']['id'] ?? null;
        $awayTeamId = $apiMatch['teams']['away']['id'] ?? null;
        $matchId = $apiMatch['id'] ?? null;
        
        $homeTotalCorners = 0;
        $awayTotalCorners = 0;
        $homeHtCorners = 0;
        $awayHtCorners = 0;
        $cornerEvents = [];
        
        // Use events from main API (livescores) - no need for separate match_events API call
        if (isset($apiMatch['events']) && is_array($apiMatch['events'])) {
            foreach ($apiMatch['events'] as $event) {
                if (isset($event['type']) && $event['type'] === 'corner') {
                    $eventTeamId = $event['team_id'] ?? null;
                    $minute = $event['minute'] ?? null;
                    $period = $event['period'] ?? '';
                    
                    if ($minute !== null) {
                        $cornerEvents[] = [
                            'team_id' => $eventTeamId,
                            'minute' => $minute,
                            'period' => $period,
                        ];
                        
                        // Count total corners by team
                        if ($eventTeamId == $homeTeamId) {
                            $homeTotalCorners++;
                        } elseif ($eventTeamId == $awayTeamId) {
                            $awayTotalCorners++;
                        }
                        
                        // Count first half corners
                        if (stripos($period, '1st half') !== false || stripos($period, 'first half') !== false) {
                            if ($eventTeamId == $homeTeamId) {
                                $homeHtCorners++;
                            } elseif ($eventTeamId == $awayTeamId) {
                                $awayHtCorners++;
                            }
                        }
                    }
                }
            }
        }
        
        // Final fallback to stats if events are still not available
        if ($homeTotalCorners == 0 && $awayTotalCorners == 0 && isset($apiMatch['stats']) && is_array($apiMatch['stats'])) {
            foreach ($apiMatch['stats'] as $stat) {
                $teamId = $stat['team_id'] ?? null;
                $corners = (int)($stat['corners'] ?? 0);
                
                if ($teamId == $homeTeamId) {
                    $homeTotalCorners = $corners;
                } elseif ($teamId == $awayTeamId) {
                    $awayTotalCorners = $corners;
                }
            }
        }
        
        // Sort corner events by minute
        usort($cornerEvents, function($a, $b) {
            return (int)$a['minute'] <=> (int)$b['minute'];
        });

        // Extract cards and standings from stats and standings
        $homeYellowCards = 0;
        $homeRedCards = 0;
        $awayYellowCards = 0;
        $awayRedCards = 0;
        $homePosition = $apiMatch['standings']['home_position'] ?? null;
        $awayPosition = $apiMatch['standings']['away_position'] ?? null;
        
        if (isset($apiMatch['stats']) && is_array($apiMatch['stats'])) {
            foreach ($apiMatch['stats'] as $stat) {
                $teamId = $stat['team_id'] ?? null;
                $yellowCards = (int)($stat['yellowcards'] ?? 0);
                $redCards = (int)($stat['redcards'] ?? 0);
                
                if ($teamId == $homeTeamId) {
                    $homeYellowCards = $yellowCards;
                    $homeRedCards = $redCards;
                } elseif ($teamId == $awayTeamId) {
                    $awayYellowCards = $yellowCards;
                    $awayRedCards = $redCards;
                }
            }
        }
        
        // Extract match events (goals, cards) for modal - use events from main API (livescores)
        $matchEvents = [];
        $matchEventsData = [];
        
        // Use events from main API (livescores) - no need for separate match_events API call
        if (isset($apiMatch['events']) && is_array($apiMatch['events'])) {
            $matchEventsData = $apiMatch['events'];
        }
        
        // Extract relevant events (goals, yellow cards, red cards)
        foreach ($matchEventsData as $event) {
            $eventType = $event['type'] ?? null;
            $eventTeamId = $event['team_id'] ?? null;
            
            if (in_array($eventType, ['goal', 'yellowcard', 'redcard', 'yellowredcard'])) {
                $matchEvents[] = [
                    'type' => $eventType,
                    'team_id' => $eventTeamId,
                    'minute' => $event['minute'] ?? null,
                    'extra_minute' => $event['extra_minute'] ?? null,
                    'player_name' => $event['player_name'] ?? null,
                    'related_player_name' => $event['related_player_name'] ?? null,
                    'period' => $event['period'] ?? '',
                ];
            }
        }
        
        // Sort match events by minute
        usort($matchEvents, function($a, $b) {
            $minuteA = (int)($a['minute'] ?? 0);
            $minuteB = (int)($b['minute'] ?? 0);
            if ($minuteA === $minuteB) {
                $extraA = (int)($a['extra_minute'] ?? 0);
                $extraB = (int)($b['extra_minute'] ?? 0);
                return $extraA <=> $extraB;
            }
            return $minuteA <=> $minuteB;
        });
        
        // Extract statistics for modal
        $homeStats = [];
        $awayStats = [];
        
        if (isset($apiMatch['stats']) && is_array($apiMatch['stats'])) {
            foreach ($apiMatch['stats'] as $stat) {
                $teamId = $stat['team_id'] ?? null;
                if ($teamId == $homeTeamId) {
                    $homeStats = $stat;
                } elseif ($teamId == $awayTeamId) {
                    $awayStats = $stat;
                }
            }
        }

        return [
            'league' => $apiMatch['league']['name'] ?? '-',
            'league_id' => $apiMatch['league']['id'] ?? null,
            'country_name' => $apiMatch['league']['country_name'] ?? null,
            'status' => $statusDisplay,
            'time' => $timeDisplay,
            'date' => $matchDate, // Match date in Y-m-d format
            'starting_datetime' => $startingDatetime, // For sorting upcoming matches
            'round_id' => $apiMatch['round']['id'] ?? $apiMatch['round_id'] ?? null,
            'round' => $apiMatch['round']['name'] ?? $apiMatch['round_name'] ?? null,
            'home_team' => $apiMatch['teams']['home']['name'] ?? '-',
            'home_team_info' => [
                'id' => $apiMatch['teams']['home']['id'] ?? null,
                'img' => $apiMatch['teams']['home']['img'] ?? null,
            ],
            'score' => $this->formatScore($apiMatch['scores'] ?? [], $isNotStarted),
            'away_team' => $apiMatch['teams']['away']['name'] ?? '-',
            'away_team_info' => [
                'id' => $apiMatch['teams']['away']['id'] ?? null,
                'img' => $apiMatch['teams']['away']['img'] ?? null,
            ],
            'half_time' => $apiMatch['scores']['ht_score'] ?? null,
            'full_time' => $apiMatch['scores']['ft_score'] ?? null,
            // Live match data for real-time updates
            'minute' => $apiMatch['time']['minute'] ?? null,
            'extra_minute' => $apiMatch['time']['extra_minute'] ?? null,
            'status_period' => $apiMatch['status_period'] ?? null,
            'status_name' => $apiMatch['status_name'] ?? null,
            'scores' => $apiMatch['scores'] ?? [], // Full scores object for live updates
            'stats' => $stats,
            'odds_1x2' => $odds1X2,
            'odds_over_under' => $oddsOverUnder,
            'odds_over_under_handicap' => $oddsOverUnderHandicap,
            'odds_asian_handicap' => $oddsAsianHandicap,
            'odds_asian_handicap_value' => $oddsAsianHandicapValue,
            'odds_data' => $oddsData, // Full odds data for filtering
            'is_favorite' => false,
            'is_live' => $isLive,
            'match_id' => $apiMatch['id'] ?? null,
            // Corner data
            'home_total_corners' => $homeTotalCorners,
            'away_total_corners' => $awayTotalCorners,
            'home_ht_corners' => $homeHtCorners,
            'away_ht_corners' => $awayHtCorners,
            'corner_events' => $cornerEvents,
            'home_team_id' => $homeTeamId,
            'away_team_id' => $awayTeamId,
            // Cards and standings
            'home_yellow_cards' => $homeYellowCards,
            'home_red_cards' => $homeRedCards,
            'away_yellow_cards' => $awayYellowCards,
            'away_red_cards' => $awayRedCards,
            'home_position' => $homePosition,
            'away_position' => $awayPosition,
            // Match details for modal (events and stats)
            'match_events' => $matchEvents,
            'home_stats' => $homeStats,
            'away_stats' => $awayStats,
        ];
    }

    /**
     * Extract and organize odds data from odds_prematch
     *
     * @param array $oddsPrematch
     * @return array
     */
    protected function extractOddsData(array $oddsPrematch): array
    {
        $organizedOdds = [
            '1X2' => [],      // id: 1
            'Asian Handicap' => [], // id: 3
            'Over/Under' => [], // id: 2
        ];

        foreach ($oddsPrematch as $oddsType) {
            $typeId = $oddsType['id'] ?? null;
            $typeName = $oddsType['name'] ?? '';
            $bookmakers = $oddsType['bookmakers'] ?? [];

            foreach ($bookmakers as $bookmaker) {
                $bookmakerId = $bookmaker['id'] ?? null;
                $bookmakerName = $bookmaker['name'] ?? '';
                $oddsInfo = $bookmaker['odds']['data'] ?? null;

                if (!$bookmakerName || !$oddsInfo) {
                    continue;
                }

                // Organize by type
                if ($typeId == 1) { // 1X2, Full Time Result
                    if (is_array($oddsInfo) && isset($oddsInfo['home'])) {
                        $organizedOdds['1X2'][$bookmakerName] = [
                            'bookmaker_id' => $bookmakerId,
                            'home' => $oddsInfo['home'] ?? null,
                            'draw' => $oddsInfo['draw'] ?? null,
                            'away' => $oddsInfo['away'] ?? null,
                        ];
                    }
                } elseif ($typeId == 3) { // Asian Handicap
                    if (is_array($oddsInfo) && isset($oddsInfo['home'])) {
                        $organizedOdds['Asian Handicap'][$bookmakerName] = [
                            'bookmaker_id' => $bookmakerId,
                            'home' => $oddsInfo['home'] ?? null,
                            'away' => $oddsInfo['away'] ?? null,
                            'handicap' => $oddsInfo['handicap'] ?? null,
                        ];
                    }
                } elseif ($typeId == 2) { // Over/Under
                    if (is_array($oddsInfo)) {
                        // Handle array of over/under options
                        $firstOption = is_array($oddsInfo) && isset($oddsInfo[0]) ? $oddsInfo[0] : $oddsInfo;
                        if (isset($firstOption['over'])) {
                            $organizedOdds['Over/Under'][$bookmakerName] = [
                                'bookmaker_id' => $bookmakerId,
                                'over' => $firstOption['over'] ?? null,
                                'under' => $firstOption['under'] ?? null,
                                'handicap' => $firstOption['handicap'] ?? null,
                            ];
                        }
                    }
                }
            }
        }

        return $organizedOdds;
    }

    /**
     * Extract unique bookmakers from matches
     *
     * @param array $matches
     * @return array
     */
    public function extractBookmakers(array $matches): array
    {
        $bookmakers = [];

        foreach ($matches as $match) {
            if (isset($match['odds_data'])) {
                foreach ($match['odds_data'] as $oddsType => $bookmakerOdds) {
                    foreach (array_keys($bookmakerOdds) as $bookmakerName) {
                        if (!in_array($bookmakerName, $bookmakers)) {
                            $bookmakers[] = $bookmakerName;
                        }
                    }
                }
            }
        }

        // Sort alphabetically
        sort($bookmakers);
        
        return $bookmakers;
    }

    /**
     * Get H2H (Head to Head) data for a match
     *
     * @param int|string $matchId
     * @return array|null
     */
    public function getH2H($matchId): ?array
    {
        $cacheKey = 'soccer_api:h2h:' . $matchId;
        
        // Cache for 1 hour (H2H data doesn't change frequently)
        return Cache::remember($cacheKey, 3600, function () use ($matchId) {
            $params = [
                't' => 'match',
                'id' => $matchId,
            ];
            
            $response = $this->makeRequest('h2h', $params);
            
            // Response can be either { data: {...} } or direct object
            if ($response) {
                if (isset($response['data'])) {
                    return $response['data'];
                }
                // If response is already the data object
                if (isset($response['home']) || isset($response['away']) || isset($response['h2h'])) {
                    return $response;
                }
            }
            
            return null;
        });
    }
}

