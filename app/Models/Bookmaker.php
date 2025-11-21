<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmaker extends Model
{
    protected $fillable = [
        'name',
        'image',
        'link',
        'target',
        'is_active',
        'order',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope to get only active bookmakers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('created_at', 'desc');
    }
}
