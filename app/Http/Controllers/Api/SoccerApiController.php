<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SoccerApiService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SoccerApiController extends ApiController
{
    protected SoccerApiService $soccerApiService;

    public function __construct(SoccerApiService $soccerApiService)
    {
        $this->soccerApiService = $soccerApiService;
    }

    /**
     * Test API connection
     */
    public function test()
    {
        $result = $this->soccerApiService->getCountries();
        
        if ($result) {
            return $this->success($result, 'API connection successful');
        }

        return $this->error('API connection failed', 500);
    }

    /**
     * Get countries
     */
    public function getCountries()
    {
        $result = $this->soccerApiService->getCountries();
        
        if ($result) {
            return $this->success($result);
        }

        return $this->error('Failed to fetch countries', 500);
    }

    /**
     * Get leagues by country
     */
    public function getLeagues(Request $request)
    {
        $request->validate([
            'country_id' => 'required|string',
        ]);

        $result = $this->soccerApiService->getLeagues($request->country_id);
        
        if ($result) {
            return $this->success($result);
        }

        return $this->error('Failed to fetch leagues', 500);
    }

    /**
     * Get matches
     */
    public function getMatches(Request $request)
    {
        $result = $this->soccerApiService->getMatches($request->all());
        
        if ($result) {
            return $this->success($result);
        }

        return $this->error('Failed to fetch matches', 500);
    }

    /**
     * Get teams
     */
    public function getTeams(Request $request)
    {
        $result = $this->soccerApiService->getTeams($request->all());
        
        if ($result) {
            return $this->success($result);
        }

        return $this->error('Failed to fetch teams', 500);
    }

    /**
     * Get standings
     */
    public function getStandings(Request $request)
    {
        $request->validate([
            'league_id' => 'required|string',
            'season_id' => 'required|string',
        ]);

        $result = $this->soccerApiService->getStandings(
            $request->league_id,
            $request->season_id
        );
        
        if ($result) {
            return $this->success($result);
        }

        return $this->error('Failed to fetch standings', 500);
    }

    /**
     * Get live scores formatted for table
     */
    public function getLivescoresTable()
    {
        $apiResponse = $this->soccerApiService->getLivescores();
        
        $matches = [];
        
        if ($apiResponse && isset($apiResponse['data']) && is_array($apiResponse['data'])) {
            // Transform API data to table format
            foreach ($apiResponse['data'] as $apiMatch) {
                $matches[] = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
            }
        }

        return $this->success($matches, 'Live scores fetched successfully');
    }

    /**
     * Get upcoming matches formatted for table
     */
    public function getUpcomingMatchesTable()
    {
        $apiResponse = $this->soccerApiService->getUpcomingMatches();
        
        $matches = [];
        
        if ($apiResponse && isset($apiResponse['data']) && is_array($apiResponse['data'])) {
            // Transform API data to table format
            foreach ($apiResponse['data'] as $apiMatch) {
                $matches[] = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
            }
        }

        return $this->success($matches, 'Upcoming matches fetched successfully');
    }

    /**
     * Get both live and upcoming matches
     */
    public function getAllMatchesTable()
    {
        // Always bypass cache - get fresh data from API every time
        // Get live matches - no cache
        $liveResponse = $this->soccerApiService->getLivescores(['_bypass_cache' => true]);
        
        // Get fixture notstart matches of today using fixtures API with odds_prematch
        // Always bypass cache - get fresh data from API every time
        $today = date('Y-m-d');
        $upcomingResponse = $this->soccerApiService->getScheduleMatches($today, ['include' => 'odds_prematch', '_bypass_cache' => true]);
        
        $liveMatches = [];
        $upcomingMatches = [];
        
        if ($liveResponse && isset($liveResponse['data']) && is_array($liveResponse['data'])) {
            foreach ($liveResponse['data'] as $apiMatch) {
                $liveMatches[] = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
            }
        }

        if ($upcomingResponse && isset($upcomingResponse['data']) && is_array($upcomingResponse['data'])) {
            $totalMatches = count($upcomingResponse['data']);
            $filteredByStatus = 0;
            
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
            \Log::info('Upcoming matches filter', [
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

        // Extract unique bookmakers
        $allMatches = array_merge($liveMatches, $upcomingMatches);
        $bookmakers = $this->soccerApiService->extractBookmakers($allMatches);

        return $this->success([
            'live' => $liveMatches,
            'upcoming' => $upcomingMatches,
            'bookmakers' => $bookmakers,
        ], 'Matches fetched successfully');
    }

    /**
     * Get match details (events and statistics) for modal display
     */
    public function getMatchDetails(Request $request)
    {
        $request->validate([
            'match_id' => 'required|string',
        ]);

        $matchDetails = $this->soccerApiService->getMatchDetails($request->match_id);

        if ($matchDetails) {
            return $this->success($matchDetails, 'Match details fetched successfully');
        }

        return $this->error('Failed to fetch match details', 500);
    }

    /**
     * Get schedule matches for a specific date (formatted for table)
     */
    public function getScheduleMatchesTable(Request $request)
    {
        // Get date from request or default to today
        $date = $request->get('date', date('Y-m-d'));
        
        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }

        $scheduleResponse = $this->soccerApiService->getScheduleMatches($date);
        
        $scheduleMatches = [];
        
        // Get current datetime for filtering
        $timezone = config('app.timezone', 'Asia/Ho_Chi_Minh');
        $now = Carbon::now($timezone);
        
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
                    $transformedMatch = $this->soccerApiService->transformMatchToTableFormat($apiMatch);
                    
                    // Filter: only include matches with starting_datetime >= current time
                    $startingDatetime = $transformedMatch['starting_datetime'] ?? null;
                    if ($startingDatetime) {
                        try {
                            $matchDateTime = Carbon::parse($startingDatetime, $timezone);
                            // Only add if match time is greater than or equal to current time
                            if ($matchDateTime->greaterThanOrEqualTo($now)) {
                                $scheduleMatches[] = $transformedMatch;
                }
                        } catch (\Exception $e) {
                            // If parsing fails, skip this match
                            continue;
                        }
                    } else {
                        // If no starting_datetime, skip this match
                        continue;
                    }
                }
            }
            
            // Sort matches by starting_datetime (earliest first)
            usort($scheduleMatches, function($a, $b) {
                $datetimeA = $a['starting_datetime'] ?? null;
                $datetimeB = $b['starting_datetime'] ?? null;
                
                if ($datetimeA === null && $datetimeB === null) return 0;
                if ($datetimeA === null) return 1; // Put null at the end
                if ($datetimeB === null) return -1; // Put null at the end
                
                return strtotime($datetimeA) <=> strtotime($datetimeB);
            });
        }

        return $this->success([
            'scheduleMatches' => $scheduleMatches,
            'date' => $date,
        ], 'Schedule matches fetched successfully');
    }

    /**
     * Get full match detail data for JavaScript rendering
     * Returns the same data structure as MatchDetailController@show
     */
    public function getMatchDetailData(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
        ]);

        $id = $request->id;
        
        // Use the same logic as MatchDetailController@show
        $matchDetailController = new \App\Http\Controllers\Web\MatchDetailController($this->soccerApiService);
        
        // Get all data using reflection to access protected method or create public method
        // For now, we'll duplicate the logic here or create a shared method
        
        // Get match data first (required for other operations)
        $matchData = \Illuminate\Support\Facades\Cache::remember("match_detail:{$id}", 300, function () use ($id) {
            return $this->soccerApiService->getFixtureInfo($id);
        });

        if (!$matchData) {
            return $this->error('Match not found', 404);
        }

        // Extract team and season info early
        $homeTeam = $matchData['teams']['home'] ?? [];
        $awayTeam = $matchData['teams']['away'] ?? [];
        $homeTeamId = $homeTeam['id'] ?? null;
        $awayTeamId = $awayTeam['id'] ?? null;
        $seasonId = $matchData['season_id'] ?? null;
        $venueId = $matchData['venue_id'] ?? null;

        // Get all data (using cached methods)
        $matchStats = \Illuminate\Support\Facades\Cache::remember("match_stats:{$id}", 300, function () use ($id) {
            return $this->soccerApiService->getMatchStats($id);
        });

        $matchLineups = \Illuminate\Support\Facades\Cache::remember("match_lineups:{$id}", 300, function () use ($id) {
            return $this->soccerApiService->getMatchLineups($id);
        });

        $matchEvents = \Illuminate\Support\Facades\Cache::remember("match_events:{$id}", 300, function () use ($id) {
            return $this->soccerApiService->getMatchEvents($id);
        });

        $venue = null;
        if ($venueId) {
            $venue = \Illuminate\Support\Facades\Cache::remember("venue_info:{$venueId}", 3600, function () use ($venueId) {
                return $this->soccerApiService->getVenueInfo($venueId);
            });
        }

        $matchOddsInfo = \Illuminate\Support\Facades\Cache::remember("match_odds_info:{$id}:2", 300, function () use ($id) {
            return $this->soccerApiService->getMatchOddsInfo($id, 2);
        });

        // Process match stats
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

        // Format date and time
        $time = $matchData['time'] ?? [];
        $matchDate = $time['date'] ?? date('Y-m-d');
        $matchTime = $time['time'] ?? '';
        $matchDatetime = $time['datetime'] ?? '';
        
        $displayDate = '';
        $displayTime = '';
        if ($matchDatetime) {
            try {
                $dateTime = new \DateTime($matchDatetime);
                $displayDate = $dateTime->format('d/m');
                $displayTime = $dateTime->format('H:i');
            } catch (\Exception $e) {
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

        return $this->success([
            'match' => $matchData,
            'homeTeam' => $homeTeam,
            'awayTeam' => $awayTeam,
            'league' => $matchData['league'] ?? [],
            'scores' => $matchData['scores'] ?? [],
            'matchDate' => $matchDate,
            'matchTime' => $matchTime,
            'displayDate' => $displayDate,
            'displayTime' => $displayTime,
            'matchId' => $id,
            'venue' => $venue,
            'homeMatchStats' => $homeMatchStats,
            'awayMatchStats' => $awayMatchStats,
            'matchLineups' => $matchLineups,
            'matchEvents' => $matchEvents,
        ], 'Match detail data fetched successfully');
    }
}

