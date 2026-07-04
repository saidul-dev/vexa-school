<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'payments';

    protected $fillable = ['id', 'expense_type', 'expense_id', 'user_id', 'payment_method', 'amount', 'status','school_id', 'transaction_keys', 'created_at', 'updated_at'];

}
