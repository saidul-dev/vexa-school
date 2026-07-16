<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountHead extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'name', 'type', 'opening_balance', 'opening_balance_type', 'is_system', 'status'
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'opening_balance' => 'float',
    ];
}
