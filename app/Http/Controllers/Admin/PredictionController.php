<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prediction;
use App\Services\SoccerApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PredictionController extends Controller
{
    protected SoccerApiService $soccerApiService;

    public function __construct(SoccerApiService $soccerApiService)
    {
        $this->soccerApiService = $soccerApiService;
    }

    /**
     * Display a listing of predictions
     */
    public function index(Request $request)
    {
        $query = Prediction::with('author')->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('home_team', 'like', "%{$search}%")
                  ->orWhere('away_team', 'like', "%{$search}%")
                  ->orWhere('league_name', 'like', "%{$search}%");
            });
        }

        $predictions = $query->paginate(20);

        return view('admin.predictions.index', compact('predictions'));
    }

    /**
     * Show the form for creating a new prediction
     */
    public function create(Request $request)
    {
        // Get upcoming matches from API (next 7 days)
        $upcomingMatches = [];
        try {
            $today = Carbon::now()->format('Y-m-d');
            $endDate = Carbon::now()->addDays(7)->format('Y-m-d');
            
            // Get matches for next 7 days (without include to optimize)
            $matches = [];
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::now()->addDays($i)->format('Y-m-d');
                // Get schedule matches without include parameter for minimal data
                $scheduleResponse = $this->soccerApiService->getScheduleMatches($date);
                
                if ($scheduleResponse && isset($scheduleResponse['data']) && is_array($scheduleResponse['data'])) {
                    foreach ($scheduleResponse['data'] as $match) {
                        // Only include not started matches
                        if (($match['status'] ?? null) == 0 || ($match['status_name'] ?? null) == 'Notstarted') {
                            $matches[] = $match;
                        }
                    }
                }
    }

            // Transform matches for dropdown
            foreach ($matches as $match) {
                $matchTime = null;
                if (isset($match['time']['datetime']) && $match['time']['datetime']) {
                    // API returns time in UTC+7 (based on utc=7 param), parse and keep as UTC+7
                    $matchTime = Carbon::parse($match['time']['datetime'])->setTimezone('Asia/Ho_Chi_Minh');
                } elseif (isset($match['time']['date']) && isset($match['time']['time'])) {
                    $matchTime = Carbon::parse($match['time']['date'] . ' ' . $match['time']['time'])->setTimezone('Asia/Ho_Chi_Minh');
                }
                
                if ($matchTime && $matchTime->isFuture()) {
                    $upcomingMatches[] = [
                        'id' => $match['id'] ?? null,
                        'home_team' => $match['teams']['home']['name'] ?? 'N/A',
                        'away_team' => $match['teams']['away']['name'] ?? 'N/A',
                        'league_id' => $match['league']['id'] ?? null,
                        'league_name' => $match['league']['name'] ?? 'N/A',
                        'country_name' => $match['league']['country_name'] ?? '',
                        'match_time' => $matchTime->format('Y-m-d H:i:s'), // Store in UTC+7 format
                        'match_time_display' => $matchTime->format('d/m/Y H:i'),
                    ];
                }
            }
            
            // Sort by match time
            usort($upcomingMatches, function($a, $b) {
                return strtotime($a['match_time']) - strtotime($b['match_time']);
            });
        } catch (\Exception $e) {
            \Log::error('Error fetching upcoming matches: ' . $e->getMessage());
        }

        return view('admin.predictions.create', compact('upcomingMatches'));
    }

    /**
     * Store a newly created prediction
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_id' => 'required|integer',
            'match_api_id' => 'nullable|string',
            'league_id' => 'nullable|integer',
            'home_team' => 'nullable|string|max:255',
            'away_team' => 'nullable|string|max:255',
            'league_name' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'content' => 'required|string',
            'analysis' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'match_time' => 'nullable|date',
            'match_datetime' => 'nullable|date',
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = time() . '_' . $thumbnail->getClientOriginalName();
            $thumbnailPath = $thumbnail->storeAs('predictions/thumbnails', $thumbnailName, 'public');
            $validated['thumbnail'] = $thumbnailPath;
        }

        $validated['author_id'] = Auth::id();
        
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        Prediction::create($validated);

        return redirect()->route('admin.predictions.index')
            ->with('success', 'Nhận định đã được tạo thành công.');
    }

    /**
     * Display the specified prediction
     */
    public function show(Prediction $prediction)
    {
        $prediction->load('author');
        return view('admin.predictions.show', compact('prediction'));
    }

    /**
     * Show the form for editing the specified prediction
     */
    public function edit(Prediction $prediction)
    {
        // Fetch upcoming matches for dropdown (same as create)
        $upcomingMatches = [];
        try {
            $today = Carbon::now()->format('Y-m-d');
            $endDate = Carbon::now()->addDays(7)->format('Y-m-d');
            
            // Get matches for next 7 days (without include to optimize)
            $matches = [];
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::now()->addDays($i)->format('Y-m-d');
                // Get schedule matches without include parameter for minimal data
                $scheduleResponse = $this->soccerApiService->getScheduleMatches($date);
                
                if ($scheduleResponse && isset($scheduleResponse['data']) && is_array($scheduleResponse['data'])) {
                    foreach ($scheduleResponse['data'] as $match) {
                        // Only include not started matches
                        if (($match['status'] ?? null) == 0 || ($match['status_name'] ?? null) == 'Notstarted') {
                            $matches[] = $match;
                        }
                    }
                }
    }

            // Transform matches for dropdown
            foreach ($matches as $match) {
                $matchTime = null;
                if (isset($match['time']['datetime']) && $match['time']['datetime']) {
                    // API returns time in UTC+7 (based on utc=7 param), parse and keep as UTC+7
                    $matchTime = Carbon::parse($match['time']['datetime'])->setTimezone('Asia/Ho_Chi_Minh');
                } elseif (isset($match['time']['date']) && isset($match['time']['time'])) {
                    $matchTime = Carbon::parse($match['time']['date'] . ' ' . $match['time']['time'])->setTimezone('Asia/Ho_Chi_Minh');
                }
                
                if ($matchTime && $matchTime->isFuture()) {
                    $upcomingMatches[] = [
                        'id' => $match['id'] ?? null,
                        'home_team' => $match['teams']['home']['name'] ?? 'N/A',
                        'away_team' => $match['teams']['away']['name'] ?? 'N/A',
                        'league_id' => $match['league']['id'] ?? null,
                        'league_name' => $match['league']['name'] ?? 'N/A',
                        'country_name' => $match['league']['country_name'] ?? '',
                        'match_time' => $matchTime->format('Y-m-d H:i:s'), // Store in UTC+7 format
                        'match_time_display' => $matchTime->format('d/m/Y H:i'),
                    ];
                }
            }
            
            // Sort by match time
            usort($upcomingMatches, function($a, $b) {
                return strtotime($a['match_time']) - strtotime($b['match_time']);
            });
        } catch (\Exception $e) {
            \Log::error('Error fetching upcoming matches: ' . $e->getMessage());
        }

        return view('admin.predictions.edit', compact('prediction', 'upcomingMatches'));
    }

    /**
     * Update the specified prediction
     */
    public function update(Request $request, Prediction $prediction)
    {
        $validated = $request->validate([
            'match_id' => 'required|integer',
            'match_api_id' => 'nullable|string',
            'league_id' => 'nullable|integer',
            'home_team' => 'nullable|string|max:255',
            'away_team' => 'nullable|string|max:255',
            'league_name' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'content' => 'required|string',
            'analysis' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'match_time' => 'nullable|date',
            'match_datetime' => 'nullable|date',
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($prediction->thumbnail && \Storage::disk('public')->exists($prediction->thumbnail)) {
                \Storage::disk('public')->delete($prediction->thumbnail);
            }
            
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = time() . '_' . $thumbnail->getClientOriginalName();
            $thumbnailPath = $thumbnail->storeAs('predictions/thumbnails', $thumbnailName, 'public');
            $validated['thumbnail'] = $thumbnailPath;
        }

        // If status changed to published and wasn't published before
        if ($validated['status'] === 'published' && !$prediction->published_at) {
            $validated['published_at'] = now();
        }

        // If status changed to draft, remove published_at
        if ($validated['status'] === 'draft') {
            $validated['published_at'] = null;
        }

        // Convert match_time from local timezone to UTC for storage
        if (isset($validated['match_time']) && $validated['match_time']) {
            $validated['match_time'] = Carbon::parse($validated['match_time'])->setTimezone('Asia/Ho_Chi_Minh')->utc();
        }

        $prediction->update($validated);

        return redirect()->route('admin.predictions.index')
            ->with('success', 'Nhận định đã được cập nhật thành công.');
    }

    /**
     * Remove the specified prediction
     */
    public function destroy(Prediction $prediction)
    {
        $prediction->delete();

        return redirect()->route('admin.predictions.index')
            ->with('success', 'Nhận định đã được xóa thành công.');
    }
}
