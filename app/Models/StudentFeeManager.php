<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeeManager extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'total_amount', 'class_id', 'parent_id','student_id', 'payment_method', 'paid_amount', 'status', 'school_id', 'session_id', 'timestamp', 'discounted_price', 'amount'
    ];
}
