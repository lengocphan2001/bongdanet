<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BlockedIP;
use App\Models\AccessLog;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class RateLimitIP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ipAddress = $request->ip();
        
        // Skip for admin routes
        if (str_starts_with($request->path(), 'admin')) {
            return $next($request);
        }
        
        // Check if IP is blocked
        $blockedIP = BlockedIP::where('ip_address', $ipAddress)->first();
        if ($blockedIP && $blockedIP->isBlocked()) {
            return response()->json([
                'error' => 'Access denied',
                'message' => 'Your IP address has been blocked.',
            ], 403);
        }
        
        // Rate limit check: >=20 requests per minute
        $rateLimitCacheKey = 'rate_limit:' . $ipAddress;
        $requestTimestamps = Cache::get($rateLimitCacheKey, []);
        
        $currentTime = now()->timestamp;
        
        // Add current request timestamp
        $requestTimestamps[] = $currentTime;
        
        // Keep only timestamps from last 1 minute (60 seconds)
        $requestTimestamps = array_filter($requestTimestamps, function($timestamp) use ($currentTime) {
            return ($currentTime - $timestamp) <= 60; // Last 1 minute
        });
        
        // Count requests in the last minute
        $requestCount = count($requestTimestamps);
        
        // Block if >=20 requests per minute
        if ($requestCount >= 20) {
            $this->autoBlockIP($ipAddress, "Rate limit exceeded: {$requestCount} requests in 1 minute (limit: 20)");
            
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => 'Your IP has been blocked due to excessive requests (>=20 requests per minute).',
            ], 403);
        }
        
        // Save updated timestamps (keep max 30 to avoid memory issues)
        if (count($requestTimestamps) > 30) {
            $requestTimestamps = array_slice($requestTimestamps, -30);
        }
        Cache::put($rateLimitCacheKey, array_values($requestTimestamps), 60); // Cache for 1 minute
        
        // Detect abnormal request patterns (e.g., every 3 seconds, 5 seconds)
        // Only block if there's a suspicious pattern, not normal requests
        $patternCacheKey = 'request_pattern:' . $ipAddress;
        $patternTimestamps = Cache::get($patternCacheKey, []);
        
        // Add current request timestamp for pattern detection
        $patternTimestamps[] = $currentTime;
        
        // Keep only timestamps from last 2 minutes for pattern detection
        $patternTimestamps = array_filter($patternTimestamps, function($timestamp) use ($currentTime) {
            return ($currentTime - $timestamp) <= 120; // Last 2 minutes
        });
        
        // Sort timestamps
        sort($patternTimestamps);
        
        // Check for abnormal patterns (requests every 3s, 5s, etc.)
        $isAbnormalPattern = $this->detectAbnormalPattern($patternTimestamps);
        
        if ($isAbnormalPattern) {
            // Auto-block IP for abnormal pattern
            $pattern = $isAbnormalPattern['pattern'];
            $interval = $isAbnormalPattern['interval'];
            $this->autoBlockIP($ipAddress, "Abnormal request pattern detected: requests every {$interval} seconds ({$pattern} requests in pattern)");
            
            return response()->json([
                'error' => 'Suspicious activity detected',
                'message' => 'Your IP has been temporarily blocked due to abnormal request pattern.',
            ], 403);
        }
        
        // Save updated pattern timestamps (keep max 50 to avoid memory issues)
        if (count($patternTimestamps) > 50) {
            $patternTimestamps = array_slice($patternTimestamps, -50);
        }
        Cache::put($patternCacheKey, array_values($patternTimestamps), 120); // Cache for 2 minutes
        
        return $next($request);
    }
    
    /**
     * Detect abnormal request patterns (e.g., requests every 3s, 5s)
     * Returns false if pattern is normal, or array with pattern info if abnormal
     */
    private function detectAbnormalPattern(array $timestamps): array|false
    {
        if (count($timestamps) < 5) {
            return false; // Not enough data to detect pattern
        }
        
        // Check for patterns: requests every 3s, 5s, 10s, etc.
        $suspiciousIntervals = [3, 5, 10, 15, 30]; // Common bot intervals
        
        foreach ($suspiciousIntervals as $interval) {
            $matches = 0;
            $totalIntervals = 0;
            
            // Check if there's a consistent pattern with this interval
            for ($i = 1; $i < count($timestamps); $i++) {
                $actualInterval = $timestamps[$i] - $timestamps[$i - 1];
                $totalIntervals++;
                
                // Allow small variance (±1 second) for network latency
                if (abs($actualInterval - $interval) <= 1) {
                    $matches++;
                }
            }
            
            // If more than 60% of intervals match the suspicious pattern, it's abnormal
            if ($totalIntervals > 0 && ($matches / $totalIntervals) >= 0.6 && $matches >= 3) {
                return [
                    'pattern' => $matches,
                    'interval' => $interval,
                    'total' => count($timestamps)
                ];
            }
        }
        
        return false; // Normal pattern, no blocking
    }
    
    /**
     * Auto-block an IP address
     */
    private function autoBlockIP(string $ipAddress, string $reason): void
    {
        // Check if already blocked
        $existing = BlockedIP::where('ip_address', $ipAddress)->first();
        
        if (!$existing) {
            // Block for 1 hour
            BlockedIP::create([
                'ip_address' => $ipAddress,
                'reason' => $reason,
                'blocked_until' => Carbon::now()->addHour(),
            ]);
        } elseif (!$existing->isBlocked()) {
            // Extend block time
            $existing->update([
                'reason' => $reason,
                'blocked_until' => Carbon::now()->addHour(),
            ]);
        }
    }
}
