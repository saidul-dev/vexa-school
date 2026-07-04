<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethods extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $table = 'payment_methods';

    protected $fillable = [ 'id','name', 'payment_keys','image','status','mode','currency','currency_position','created_at','updated_at','school_id' ];

}
