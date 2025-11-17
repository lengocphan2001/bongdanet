<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Prediction;
use App\Services\SoccerApiService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PredictionsController extends Controller
{
    protected SoccerApiService $soccerApiService;

    public function __construct(SoccerApiService $soccerApiService)
    {
        $this->soccerApiService = $soccerApiService;
    }
    public function index(Request $request)
    {
        // Get all published predictions for upcoming matches
        $query = Prediction::published()
            ->upcoming()
            ->with('author')
            ->orderBy('match_time', 'asc');

        // Filter by league if provided
        if ($request->has('league_id') && $request->league_id) {
            $query->where('league_id', $request->league_id);
        }

        // Get all predictions
        $allPredictions = $query->get();

        // Group predictions by league
        $predictionsByLeague = $allPredictions->groupBy('league_id');

        // Get recent predictions (all published, not just upcoming)
        $recentPredictions = Prediction::published()
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->limit(10)
            ->get();

        return view('pages.predictions', compact('allPredictions', 'predictionsByLeague', 'recentPredictions'));
    }

    public function show($id)
    {
        $prediction = Prediction::published()
            ->with('author')
            ->findOrFail($id);

        // Get match info from API if match_id exists
        $matchInfo = null;
        if ($prediction->match_id || $prediction->match_api_id) {
            $matchId = $prediction->match_id ?? $prediction->match_api_id;
            try {
                $matchInfo = $this->soccerApiService->getFixtureInfo($matchId);
            } catch (\Exception $e) {
                \Log::error('Error fetching match info from API: ' . $e->getMessage());
            }
        }

        // Get related predictions (same league, excluding current)
        $relatedPredictions = Prediction::published()
            ->where('league_id', $prediction->league_id)
            ->where('id', '!=', $prediction->id)
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        return view('pages.prediction-detail', compact('prediction', 'relatedPredictions', 'matchInfo'));
    }

    public function byLeague($leagueSlug)
    {
        // Map league slug to league name
        $leagueMap = [
            'ngoai-hang-anh' => 'Ngoại Hạng Anh',
            'cup-c1' => 'Cúp C1',
            'vdqg-duc' => 'VĐQG Đức',
            'la-liga' => 'La Liga',
            'vdqg-y' => 'VĐQG Ý',
            'vdqg-phap' => 'VĐQG Pháp',
            'v-league' => 'V League',
            'vdqg-uc' => 'VĐQG Úc',
            'cup-c2' => 'Cúp C2',
            'cup-c3' => 'Cúp C3',
            'c2-chau-a' => 'C2 Châu Á',
            'cup-c1-chau-a' => 'Cúp C1 Châu Á',
        ];

        $leagueName = $leagueMap[$leagueSlug] ?? null;
        
        if (!$leagueName) {
            abort(404, 'Giải đấu không tồn tại');
        }

        // Get all published predictions for this league
        $allPredictions = Prediction::published()
            ->where('league_name', $leagueName)
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->get();

        // Get featured prediction (most recent)
        $featuredPrediction = $allPredictions->first();

        // Get other predictions (excluding featured)
        $otherPredictions = $allPredictions->skip(1)->take(10);

        // Get recent predictions for sidebar
        $recentPredictions = Prediction::published()
            ->where('league_name', $leagueName)
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        // Get all league names for navigation tabs
        $allLeagues = Prediction::published()
            ->distinct()
            ->pluck('league_name')
            ->filter()
            ->values();

        return view('pages.predictions-league', compact(
            'leagueName',
            'leagueSlug',
            'featuredPrediction',
            'otherPredictions',
            'recentPredictions',
            'allLeagues',
            'leagueMap'
        ));
    }
}

