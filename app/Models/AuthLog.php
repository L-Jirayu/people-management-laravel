<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthLog extends Model
{
    protected $table = 'auth_logs';
    public $timestamps = true;

    protected $fillable = [
        'event',
        'username',
        'success',
        'ip',
        'user_agent',
        'message',
        'occurred_at',
    ];

    protected $casts = [
        'success'     => 'boolean',
        'occurred_at' => 'datetime',
    ];
}
