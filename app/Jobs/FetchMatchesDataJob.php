<?php

namespace App\Jobs;

use App\Services\SoccerApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchMatchesDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SoccerApiService $soccerApiService): void
    {
        try {
            Log::info('FetchMatchesDataJob: Starting to fetch matches data');
            
            $today = date('Y-m-d');
            
            // Fetch live matches
            $liveResponse = $soccerApiService->getLivescores(['_bypass_cache' => true]);
            
            // Fetch upcoming matches
            $upcomingResponse = $soccerApiService->getScheduleMatches($today, [
                'include' => 'events,stats,odds_prematch',
                '_bypass_cache' => true,
                '_sort_by_time' => true,
                '_filter_past_matches' => true
            ]);
            
            $liveMatches = [];
            $upcomingMatches = [];
            
            if ($liveResponse && isset($liveResponse['data']) && is_array($liveResponse['data'])) {
                foreach ($liveResponse['data'] as $apiMatch) {
                    $liveMatches[] = $soccerApiService->transformMatchToTableFormat($apiMatch);
                }
            }

            if ($upcomingResponse && isset($upcomingResponse['data']) && is_array($upcomingResponse['data'])) {
                foreach ($upcomingResponse['data'] as $apiMatch) {
                    $matchStatus = $apiMatch['status'] ?? null;
                    $statusName = $apiMatch['status_name'] ?? null;
                    
                    $isNotStarted = ($matchStatus === 0 || $matchStatus === '0' || $statusName === 'Notstarted');
                    $isLive = ($matchStatus === 1 || $statusName === 'Inplay');
                    $isFinished = ($matchStatus === 2 || $statusName === 'Finished');
                    
                    if ($isNotStarted && !$isLive && !$isFinished) {
                        $upcomingMatches[] = $soccerApiService->transformMatchToTableFormat($apiMatch);
                    }
                }
            }

            $allMatches = array_merge($liveMatches, $upcomingMatches);
            $bookmakers = $soccerApiService->extractBookmakers($allMatches);
            $matchIds = array_filter(array_column($allMatches, 'match_id'));
            
            // Fetch H2H data for first 50 matches
            if (!empty($matchIds)) {
                $matchIds = array_slice($matchIds, 0, 50);
                
                $h2hRequests = [];
                foreach ($matchIds as $matchId) {
                    $h2hConfig = $soccerApiService->getRequestConfig('h2h', [
                        't' => 'match',
                        'id' => $matchId,
                    ]);
                    
                    $h2hRequests["match_{$matchId}_h2h"] = $h2hConfig;
                }
                
                $h2hResponses = Http::pool(function ($pool) use ($h2hRequests) {
                    $poolRequests = [];
                    foreach ($h2hRequests as $key => $config) {
                        $poolRequests[$key] = $pool->as($key)->timeout(3)->retry(1, 30)->get($config['url'], $config['params']);
                    }
                    return $poolRequests;
                });
                
                $h2hMap = [];
                foreach ($matchIds as $matchId) {
                    $matchIdKey = (string) $matchId;
                    $h2hKey = "match_{$matchId}_h2h";
                    
                    $h2hData = null;
                    $h2hResponse = $h2hResponses[$h2hKey] ?? null;
                    if ($h2hResponse && $h2hResponse->successful()) {
                        $h2hJson = $h2hResponse->json();
                        if (isset($h2hJson['data'])) {
                            $h2hData = $h2hJson['data'];
                        } elseif (isset($h2hJson['home']) || isset($h2hJson['away']) || isset($h2hJson['h2h'])) {
                            $h2hData = $h2hJson;
                        }
                    }
                    
                    if (!$h2hData) {
                        $h2hData = $soccerApiService->getH2H($matchId);
                    }
                    
                    $h2hMap[$matchIdKey] = $h2hData;
                }
                
                // Add H2H to matches
                foreach ($liveMatches as &$match) {
                    $matchId = (string) ($match['match_id'] ?? null);
                    if ($matchId && isset($h2hMap[$matchId])) {
                        $match['h2h'] = $h2hMap[$matchId];
                    }
                }
                unset($match);
                
                foreach ($upcomingMatches as &$match) {
                    $matchId = (string) ($match['match_id'] ?? null);
                    if ($matchId && isset($h2hMap[$matchId])) {
                        $match['h2h'] = $h2hMap[$matchId];
                    }
                }
                unset($match);
            }
            
            // Store in cache with longer TTL (stale data)
            $cacheKey = 'matches:all:prefetched';
            $data = [
                'live' => $liveMatches,
                'upcoming' => $upcomingMatches,
                'bookmakers' => $bookmakers,
                'timestamp' => now()->timestamp,
            ];
            
            // Store with 5 minutes TTL (stale data)
            Cache::put($cacheKey, $data, 300);
            
            // Also store fresh data with shorter TTL (30 seconds)
            Cache::put($cacheKey . ':fresh', $data, 30);
            
            Log::info('FetchMatchesDataJob: Successfully cached matches data', [
                'live_count' => count($liveMatches),
                'upcoming_count' => count($upcomingMatches),
                'total_matches' => count($allMatches),
            ]);
            
        } catch (\Exception $e) {
            Log::error('FetchMatchesDataJob: Error fetching matches data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

