<?php

namespace App\Services;

use App\Models\AccountHead;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherLine;

class AccountingService
{
    public function recordVoucher($schoolId, $sessionId, $voucherType, $voucherDate, $particulars, $debitHeadId, $creditHeadId, $amount, $sourceType = null, $sourceId = null, $createdBy = null)
    {
        $voucher = AccountVoucher::create([
            'school_id' => $schoolId,
            'session_id' => $sessionId,
            'voucher_no' => $this->nextVoucherNo($schoolId, $voucherType),
            'voucher_type' => $voucherType,
            'voucher_date' => $voucherDate,
            'particulars' => $particulars,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'amount' => $amount,
            'created_by' => $createdBy,
        ]);

        AccountVoucherLine::create([
            'voucher_id' => $voucher->id,
            'account_head_id' => $debitHeadId,
            'debit' => $amount,
            'credit' => 0,
        ]);

        AccountVoucherLine::create([
            'voucher_id' => $voucher->id,
            'account_head_id' => $creditHeadId,
            'debit' => 0,
            'credit' => $amount,
        ]);

        return $voucher;
    }

    public function voidVoucherFor($sourceType, $sourceId)
    {
        $vouchers = AccountVoucher::where('source_type', $sourceType)->where('source_id', $sourceId)->get();

        foreach ($vouchers as $voucher) {
            AccountVoucherLine::where('voucher_id', $voucher->id)->delete();
            $voucher->delete();
        }
    }

    public function resolveAssetHead($schoolId, $paymentMethod)
    {
        $name = (strtolower((string) $paymentMethod) === 'cash') ? 'Cash in Hand' : 'Bank Account';

        return AccountHead::where('school_id', $schoolId)->where('type', 'asset')->where('name', $name)->first();
    }

    public function defaultStudentFeeIncomeHead($schoolId)
    {
        return AccountHead::where('school_id', $schoolId)->where('type', 'income')->where('name', 'Student Fee Income')->first();
    }

    protected function nextVoucherNo($schoolId, $voucherType)
    {
        $prefixes = ['receipt' => 'RV', 'payment' => 'PV', 'journal' => 'JV'];
        $prefix = $prefixes[$voucherType];

        $lastVoucher = AccountVoucher::where('school_id', $schoolId)
            ->where('voucher_type', $voucherType)
            ->orderByDesc('id')
            ->first();

        $next = 1;

        if ($lastVoucher) {
            $parts = explode('-', $lastVoucher->voucher_no);
            $next = ((int) end($parts)) + 1;
        }

        return $prefix.'-'.str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
