<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'account_head_id', 'payment_account_head_id', 'date', 'amount', 'school_id', 'session_id'
    ];
}
