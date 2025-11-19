<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
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
        
        return view('admin.access-logs.index', compact('logs', 'stats'));
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
}
