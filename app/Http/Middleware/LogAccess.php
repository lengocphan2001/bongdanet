<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AccessLog;
use Illuminate\Support\Facades\Log;

class LogAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000); // Convert to milliseconds
        
        // Skip logging for admin routes and API routes to avoid cluttering
        $path = $request->path();
        if (str_starts_with($path, 'admin') || str_starts_with($path, 'api')) {
            return $response;
        }
        
        // Skip logging for static assets
        if (preg_match('/\.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$/', $path)) {
            return $response;
        }
        
        // Skip logging for browser/system requests
        if (str_starts_with($path, '.well-known') || 
            str_starts_with($path, 'favicon.ico') ||
            str_starts_with($path, 'robots.txt') ||
            str_starts_with($path, 'sitemap') ||
            $path === 'up' || // Health check
            $path === 'health') {
            return $response;
        }
        
        try {
            $userAgent = $request->userAgent();
            $parsedUserAgent = $this->parseUserAgent($userAgent);
            
            AccessLog::create([
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $userAgent,
                'referer' => $request->header('referer'),
                'status_code' => $response->getStatusCode(),
                'response_time' => $responseTime,
                'device_type' => $parsedUserAgent['device_type'],
                'browser' => $parsedUserAgent['browser'],
                'os' => $parsedUserAgent['os'],
            ]);
        } catch (\Exception $e) {
            // Log error but don't break the request
            Log::error('Failed to log access', [
                'error' => $e->getMessage(),
                'url' => $request->fullUrl(),
            ]);
        }
        
        return $response;
    }
    
    /**
     * Parse user agent to extract device, browser, and OS info
     */
    private function parseUserAgent(?string $userAgent): array
    {
        if (!$userAgent) {
            return [
                'device_type' => 'unknown',
                'browser' => 'unknown',
                'os' => 'unknown',
            ];
        }
        
        $deviceType = 'desktop';
        $browser = 'unknown';
        $os = 'unknown';
        
        // Detect device type
        if (preg_match('/mobile|android|iphone|ipad|ipod|blackberry|iemobile|opera mini/i', $userAgent)) {
            if (preg_match('/tablet|ipad/i', $userAgent)) {
                $deviceType = 'tablet';
            } else {
                $deviceType = 'mobile';
            }
        }
        
        // Detect browser
        if (preg_match('/chrome/i', $userAgent) && !preg_match('/edg|opr/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/opr/i', $userAgent)) {
            $browser = 'Opera';
        } elseif (preg_match('/msie|trident/i', $userAgent)) {
            $browser = 'IE';
        }
        
        // Detect OS
        if (preg_match('/windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $os = 'iOS';
        }
        
        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os,
        ];
    }
}
