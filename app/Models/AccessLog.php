<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    protected $fillable = [
        'ip_address',
        'url',
        'method',
        'user_agent',
        'referer',
        'status_code',
        'response_time',
        'country',
        'city',
        'device_type',
        'browser',
        'os',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'response_time' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
