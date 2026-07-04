<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'payment_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_type', 'user_id', 'course_id', 'amount', 'school_id', 'transaction_keys', 'document_image', 'paid_by', 'status', 'timestamp'
    ];
}
