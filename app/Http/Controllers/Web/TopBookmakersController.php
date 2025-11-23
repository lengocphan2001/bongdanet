<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bookmaker;
use Illuminate\Http\Request;

class TopBookmakersController extends Controller
{
    /**
     * Display the top bookmakers page
     */
    public function index(Request $request)
    {
        // Get all active bookmakers ordered by order field
        $bookmakers = Bookmaker::active()->ordered()->get();
        
        return view('pages.top-bookmakers', [
            'bookmakers' => $bookmakers,
        ]);
    }
}

