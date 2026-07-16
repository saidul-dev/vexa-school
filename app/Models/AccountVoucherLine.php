<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountVoucherLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_id', 'account_head_id', 'debit', 'credit'
    ];

    public function accountHead()
    {
        return $this->belongsTo(AccountHead::class, 'account_head_id');
    }

    public function voucher()
    {
        return $this->belongsTo(AccountVoucher::class, 'voucher_id');
    }
}
