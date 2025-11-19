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
        
        // Rate limiting: Check if IP has made too many requests
        // Pattern detection: If requests come every 3 seconds, that's 20 requests per minute
        // Allow max 30 requests per minute for normal users
        $cacheKey = 'rate_limit:' . $ipAddress;
        $requests = Cache::get($cacheKey, 0);
        
        if ($requests >= 30) {
            // Auto-block IP if exceeds rate limit
            $this->autoBlockIP($ipAddress, 'Exceeded rate limit: 30 requests per minute');
            
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => 'Too many requests. Please try again later.',
            ], 429);
        }
        
        // Increment request count
        Cache::put($cacheKey, $requests + 1, 60); // Cache for 1 minute
        
        // Check for suspicious pattern: more than 10 requests in 30 seconds (pattern: every 3 seconds)
        $recentCacheKey = 'rate_limit_recent:' . $ipAddress;
        $recentRequests = Cache::get($recentCacheKey, 0);
        
        if ($recentRequests >= 10) {
            // Auto-block IP for suspicious activity (pattern: every 3 seconds)
            $this->autoBlockIP($ipAddress, 'Suspicious activity: more than 10 requests in 30 seconds (pattern: every 3 seconds)');
            
            return response()->json([
                'error' => 'Suspicious activity detected',
                'message' => 'Your IP has been temporarily blocked due to suspicious activity.',
            ], 403);
        }
        
        // Increment recent requests counter (reset every 30 seconds)
        Cache::put($recentCacheKey, $recentRequests + 1, 30);
        
        return $next($request);
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
