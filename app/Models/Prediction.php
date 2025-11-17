<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    protected $fillable = [
        'match_id',
        'match_api_id',
        'league_id',
        'home_team',
        'away_team',
        'league_name',
        'title',
        'thumbnail',
        'content',
        'analysis',
        'author_id',
        'status',
        'published_at',
        'match_time',
        'match_datetime',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'match_time' => 'datetime',
        'match_datetime' => 'datetime',
    ];

    /**
     * Get the author (user) of this prediction
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope to get only published predictions
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at');
    }

    /**
     * Scope to get predictions for upcoming matches
     */
    public function scopeUpcoming($query)
    {
        return $query->where('match_datetime', '>', now());
    }
}
