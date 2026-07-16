<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AccountHead;

class AccountHeadSeeder extends Seeder
{
    public function run()
    {
        $schoolIds = DB::table('schools')->pluck('id');

        foreach ($schoolIds as $schoolId) {
            $alreadySeeded = AccountHead::where('school_id', $schoolId)->where('is_system', true)->exists();

            if ($alreadySeeded) {
                continue;
            }

            AccountHead::create([
                'school_id' => $schoolId,
                'name' => 'Cash in Hand',
                'type' => 'asset',
                'opening_balance' => 0,
                'opening_balance_type' => 'debit',
                'is_system' => true,
                'status' => 'active',
            ]);

            AccountHead::create([
                'school_id' => $schoolId,
                'name' => 'Bank Account',
                'type' => 'asset',
                'opening_balance' => 0,
                'opening_balance_type' => 'debit',
                'is_system' => true,
                'status' => 'active',
            ]);

            AccountHead::create([
                'school_id' => $schoolId,
                'name' => 'Student Fee Income',
                'type' => 'income',
                'opening_balance' => 0,
                'opening_balance_type' => 'credit',
                'is_system' => true,
                'status' => 'active',
            ]);
        }
    }
}
