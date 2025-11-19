<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedIP extends Model
{
    protected $fillable = [
        'ip_address',
        'reason',
        'blocked_until',
    ];

    protected $casts = [
        'blocked_until' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Check if IP is currently blocked
     */
    public function isBlocked(): bool
    {
        // If blocked_until is null, it's permanently blocked
        if ($this->blocked_until === null) {
            return true;
        }
        
        // If blocked_until is in the future, it's still blocked
        return $this->blocked_until->isFuture();
    }
}
