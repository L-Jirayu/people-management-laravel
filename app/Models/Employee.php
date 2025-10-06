<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'emp_code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'salary',
        'hired_date',
        'status',
        'attachments',
    ];

    protected $casts = [
        'hired_date' => 'date',
        'salary'     => 'decimal:2',
        'attachments' => 'array',
    ];
}
