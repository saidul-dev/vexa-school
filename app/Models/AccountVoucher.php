<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'session_id', 'voucher_no', 'voucher_type', 'voucher_date', 'particulars', 'source_type', 'source_id', 'amount', 'created_by'
    ];

    public function lines()
    {
        return $this->hasMany(AccountVoucherLine::class, 'voucher_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
