<?php

use App\Http\Controllers\Api\SoccerApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('soccer-api')->group(function () {
    Route::get('/test', [SoccerApiController::class, 'test'])->name('api.soccer.test');
    Route::get('/countries', [SoccerApiController::class, 'getCountries'])->name('api.soccer.countries');
    Route::get('/leagues', [SoccerApiController::class, 'getLeagues'])->name('api.soccer.leagues');
    Route::get('/matches', [SoccerApiController::class, 'getMatches'])->name('api.soccer.matches');
    Route::get('/teams', [SoccerApiController::class, 'getTeams'])->name('api.soccer.teams');
    Route::get('/standings', [SoccerApiController::class, 'getStandings'])->name('api.soccer.standings');
});

