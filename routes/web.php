<?php

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\PredictionsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\Web\OddsController::class, 'index'])->name('home');
Route::get('/trang-chu', [HomeController::class, 'index'])->name('home.matches');
Route::get('/nhan-dinh-bong-da', [PredictionsController::class, 'index'])->name('predictions');
Route::get('/nhan-dinh-bong-da/league/{leagueSlug}', [PredictionsController::class, 'byLeague'])->name('predictions.league');
Route::get('/nhan-dinh-bong-da/{id}', [PredictionsController::class, 'show'])->name('prediction.detail');
Route::get('/ket-qua-bong-da', [\App\Http\Controllers\Web\ResultsController::class, 'index'])->name('results');
Route::get('/ket-qua-bong-da/{leagueId}', [\App\Http\Controllers\Web\ResultsController::class, 'showLeague'])->name('results.league');
Route::get('/ket-qua/{id}', [\App\Http\Controllers\Web\MatchDetailController::class, 'show'])->name('match.detail');
Route::get('/livescore', [\App\Http\Controllers\Web\LivescoreController::class, 'index'])->name('livescore');
Route::get('/api/livescore-data', [\App\Http\Controllers\Web\LivescoreController::class, 'getLivescoreData'])->name('api.livescore.data');
Route::get('/lich-thi-dau', [\App\Http\Controllers\Web\ScheduleController::class, 'index'])->name('schedule');
Route::get('/lich-thi-dau/{leagueId}', [\App\Http\Controllers\Web\ScheduleController::class, 'showLeague'])->name('schedule.league');
Route::get('/bang-xep-hang-bong-da', [\App\Http\Controllers\Web\StandingsController::class, 'index'])->name('standings.index');
Route::get('/bang-xep-hang-bong-da/{leagueId}', [\App\Http\Controllers\Web\StandingsController::class, 'show'])->name('standings.show');
Route::get('/keo-bong-da', [\App\Http\Controllers\Web\OddsController::class, 'index'])->name('odds');
Route::get('/keo-bong-da/{leagueId}', [\App\Http\Controllers\Web\OddsController::class, 'byLeague'])->name('odds.league');
Route::get('/top-ghi-ban', [\App\Http\Controllers\Web\TopScorersController::class, 'index'])->name('top-scorers');
Route::get('/api/top-scorers-data', [\App\Http\Controllers\Web\TopScorersController::class, 'getTopScorersData'])->name('api.top-scorers.data');

// Test API route
Route::get('/api/test-soccer', [\App\Http\Controllers\Api\SoccerApiController::class, 'test'])->name('api.test.soccer');
// Get live scores for table (auto-refresh)
Route::get('/api/livescores-table', [\App\Http\Controllers\Api\SoccerApiController::class, 'getLivescoresTable'])->name('api.livescores.table');
// Get upcoming matches for table
Route::get('/api/upcoming-matches-table', [\App\Http\Controllers\Api\SoccerApiController::class, 'getUpcomingMatchesTable'])->name('api.upcoming.matches.table');
// Get both live and upcoming matches
Route::get('/api/all-matches-table', [\App\Http\Controllers\Api\SoccerApiController::class, 'getAllMatchesTable'])->name('api.all.matches.table');
// Get match details (events and statistics) for modal
Route::get('/api/match-details', [\App\Http\Controllers\Api\SoccerApiController::class, 'getMatchDetails'])->name('api.match.details');
// Get full match detail data for JavaScript rendering
Route::get('/api/match-detail-data', [\App\Http\Controllers\Api\SoccerApiController::class, 'getMatchDetailData'])->name('api.match.detail.data');
Route::get('/api/match-detail/{id}', [\App\Http\Controllers\Api\SoccerApiController::class, 'getMatchDetail'])->name('api.match.detail');
Route::get('/api/match-events/{id}', [\App\Http\Controllers\Api\SoccerApiController::class, 'getMatchEvents'])->name('api.match.events');
Route::get('/api/match-h2h/{id}', [\App\Http\Controllers\Api\SoccerApiController::class, 'getH2H'])->name('api.match.h2h');
Route::get('/api/match-all/{id}', [\App\Http\Controllers\Api\SoccerApiController::class, 'getMatchAllData'])->name('api.match.all');
// Get schedule matches for table (auto-refresh)
Route::get('/api/schedule-matches-table', [\App\Http\Controllers\Api\SoccerApiController::class, 'getScheduleMatchesTable'])->name('api.schedule.matches.table');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Auth routes (no middleware)
    Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
    
    // Protected admin routes
    Route::middleware('admin')->group(function () {
        Route::resource('predictions', \App\Http\Controllers\Admin\PredictionController::class);
        Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class);
        Route::post('/banners/{banner}/toggle-status', [\App\Http\Controllers\Admin\BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
        Route::resource('bookmakers', \App\Http\Controllers\Admin\BookmakerController::class);
        Route::post('/bookmakers/{bookmaker}/toggle-status', [\App\Http\Controllers\Admin\BookmakerController::class, 'toggleStatus'])->name('bookmakers.toggle-status');
        Route::post('/upload-image', [\App\Http\Controllers\Admin\ImageUploadController::class, 'upload'])->name('upload.image');
        
        // Access Logs
        Route::get('/access-logs', [\App\Http\Controllers\Admin\AccessLogController::class, 'index'])->name('access-logs.index');
        Route::get('/access-logs/{id}', [\App\Http\Controllers\Admin\AccessLogController::class, 'show'])->name('access-logs.show');
        Route::post('/access-logs/clean', [\App\Http\Controllers\Admin\AccessLogController::class, 'clean'])->name('access-logs.clean');
        Route::post('/access-logs/block-ip', [\App\Http\Controllers\Admin\AccessLogController::class, 'blockIP'])->name('access-logs.block-ip');
        Route::delete('/access-logs/unblock-ip/{ipAddress}', [\App\Http\Controllers\Admin\AccessLogController::class, 'unblockIP'])->name('access-logs.unblock-ip');
    });
});
