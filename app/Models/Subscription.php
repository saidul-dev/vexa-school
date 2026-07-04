<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'package_id', 'school_id', 'paid_amount', 'payment_method', 'transaction_keys', 'expire_date', 'studentLimit', 'date_added', 'active', 'status'
    ];
}
