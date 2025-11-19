<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use App\Models\BlockedIP;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccessLogController extends Controller
{
    /**
     * Display a listing of access logs
     */
    public function index(Request $request)
    {
        $query = AccessLog::latest();
        
        // Filter by IP address
        if ($request->has('ip') && $request->ip !== '') {
            $query->where('ip_address', 'like', "%{$request->ip}%");
        }
        
        // Filter by URL
        if ($request->has('url') && $request->url !== '') {
            $query->where('url', 'like', "%{$request->url}%");
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter by status code
        if ($request->has('status_code') && $request->status_code !== '') {
            $query->where('status_code', $request->status_code);
        }
        
        // Filter by device type
        if ($request->has('device_type') && $request->device_type !== '') {
            $query->where('device_type', $request->device_type);
        }
        
        // Filter by browser
        if ($request->has('browser') && $request->browser !== '') {
            $query->where('browser', $request->browser);
        }
        
        $logs = $query->paginate(50);
        
        // Get statistics
        $stats = [
            'total' => AccessLog::count(),
            'today' => AccessLog::whereDate('created_at', today())->count(),
            'this_week' => AccessLog::where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
            'this_month' => AccessLog::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'unique_ips' => AccessLog::distinct('ip_address')->count('ip_address'),
        ];
        
        // Get suspicious IPs (IPs with more than 100 requests in the last hour)
        $suspiciousIPs = AccessLog::select('ip_address')
            ->selectRaw('COUNT(*) as request_count')
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->groupBy('ip_address')
            ->having('request_count', '>', 100)
            ->orderBy('request_count', 'desc')
            ->get();
        
        // Get top IPs by request count (last 24 hours)
        $topIPs = AccessLog::select('ip_address')
            ->selectRaw('COUNT(*) as request_count')
            ->selectRaw('MIN(created_at) as first_request')
            ->selectRaw('MAX(created_at) as last_request')
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->groupBy('ip_address')
            ->orderBy('request_count', 'desc')
            ->limit(10)
            ->get();
        
        // Get blocked IPs
        $blockedIPs = BlockedIP::where(function($query) {
            $query->whereNull('blocked_until')
                  ->orWhere('blocked_until', '>', Carbon::now());
        })->latest()->get();
        
        return view('admin.access-logs.index', compact('logs', 'stats', 'suspiciousIPs', 'topIPs', 'blockedIPs'));
    }
    
    /**
     * Show details of a specific access log
     */
    public function show($id)
    {
        $log = AccessLog::findOrFail($id);
        
        return view('admin.access-logs.show', compact('log'));
    }
    
    /**
     * Delete old logs (older than specified days)
     */
    public function clean(Request $request)
    {
        $days = $request->input('days', 30);
        
        $deleted = AccessLog::where('created_at', '<', Carbon::now()->subDays($days))->delete();
        
        return redirect()->route('admin.access-logs.index')
            ->with('success', "Đã xóa {$deleted} bản ghi cũ hơn {$days} ngày.");
    }
    
    /**
     * Block an IP address
     */
    public function blockIP(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'nullable|string|max:255',
            'hours' => 'nullable|integer|min:1|max:8760', // Max 1 year
        ]);
        
        $ipAddress = $request->ip_address;
        $reason = $request->reason ?? 'Blocked by admin';
        $hours = $request->hours ?? null; // null = permanent
        
        $blockedUntil = $hours ? Carbon::now()->addHours($hours) : null;
        
        $blockedIP = BlockedIP::updateOrCreate(
            ['ip_address' => $ipAddress],
            [
                'reason' => $reason,
                'blocked_until' => $blockedUntil,
            ]
        );
        
        return redirect()->back()
            ->with('success', "Đã chặn IP {$ipAddress}" . ($hours ? " trong {$hours} giờ" : " vĩnh viễn") . ".");
    }
    
    /**
     * Unblock an IP address
     */
    public function unblockIP($ipAddress)
    {
        $blockedIP = BlockedIP::where('ip_address', $ipAddress)->first();
        
        if ($blockedIP) {
            $blockedIP->delete();
            return redirect()->back()
                ->with('success', "Đã bỏ chặn IP {$ipAddress}.");
        }
        
        return redirect()->back()
            ->with('error', "IP {$ipAddress} không có trong danh sách bị chặn.");
    }
}
