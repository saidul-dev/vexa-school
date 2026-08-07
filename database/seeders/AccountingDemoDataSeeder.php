<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AccountHead;
use App\Models\Expense;
use App\Models\Income;
use App\Services\AccountingService;

class AccountingDemoDataSeeder extends Seeder
{
    protected $expenseHeadNames = [
        'Travelling and Conveyance',
        'Office Entertainment',
        'Printing & Stationary',
        'Utility Bill',
        'Salary & Wages',
    ];

    protected $incomeHeadNames = [
        'Donation Income',
        'Other Income',
    ];

    protected $expenseTitles = [
        'Travelling and Conveyance' => [
            'Office to City Center - Bus fare',
            'Client visit - CNG fare',
            'Bank visit - Rickshaw fare',
            'Airport pickup - Car rental',
        ],
        'Office Entertainment' => [
            'Snacks for staff meeting',
            'Tea and coffee for office',
            'Lunch for visiting guests',
        ],
        'Printing & Stationary' => [
            'Photocopy and printing charges',
            'Office stationery purchase',
            'Notice board printing',
        ],
        'Utility Bill' => [
            'Electricity bill payment',
            'Internet bill payment',
            'Water bill payment',
        ],
        'Salary & Wages' => [
            'Support staff wages',
            'Part-time staff salary',
        ],
    ];

    protected $incomeTitles = [
        'Donation Income' => [
            'Donation from alumni',
            'Donation from local business',
        ],
        'Other Income' => [
            'Hall rent income',
            'Sale of old furniture',
            'Miscellaneous income',
        ],
    ];

    /**
     * Seeds Account Heads, then a batch of Expense and Income transactions
     * (each properly posted through AccountingService, exactly like the
     * real Expense/Income Manager screens do) for every school that has a
     * running session. Safe to run more than once: the account heads it
     * creates are deduplicated, but each run adds a fresh batch of
     * transactions on top of whatever already exists.
     */
    public function run()
    {
        (new AccountHeadSeeder())->run();

        $schoolIds = DB::table('schools')->pluck('id');

        foreach ($schoolIds as $schoolId) {
            DB::transaction(function () use ($schoolId) {
                $this->seedForSchool($schoolId);
            });
        }
    }

    protected function seedForSchool($schoolId)
    {
        $sessionId = DB::table('schools')->where('id', $schoolId)->value('running_session');

        if (empty($sessionId)) {
            return;
        }

        $createdBy = DB::table('users')->where('school_id', $schoolId)->where('role_id', 2)->value('id');

        $cashHead = AccountHead::where('school_id', $schoolId)->where('type', 'asset')->where('name', 'Cash in Hand')->first();
        $bankHead = AccountHead::where('school_id', $schoolId)->where('type', 'asset')->where('name', 'Bank Account')->first();

        if (!$cashHead || !$bankHead) {
            return;
        }

        $paymentHeads = [$cashHead, $bankHead];

        $expenseHeads = collect($this->expenseHeadNames)->map(function ($name) use ($schoolId) {
            return AccountHead::firstOrCreate(
                ['school_id' => $schoolId, 'name' => $name, 'type' => 'expense'],
                ['opening_balance' => 0, 'opening_balance_type' => 'debit', 'is_system' => false, 'status' => 'active']
            );
        });

        $incomeHeads = collect($this->incomeHeadNames)->map(function ($name) use ($schoolId) {
            return AccountHead::firstOrCreate(
                ['school_id' => $schoolId, 'name' => $name, 'type' => 'income'],
                ['opening_balance' => 0, 'opening_balance_type' => 'credit', 'is_system' => false, 'status' => 'active']
            );
        });

        $accountingService = new AccountingService();

        foreach (range(1, 20) as $i) {
            $head = $expenseHeads->random();
            $titles = $this->expenseTitles[$head->name] ?? [$head->name];
            $title = $titles[array_rand($titles)];
            $paymentHead = $paymentHeads[array_rand($paymentHeads)];
            $amount = rand(50, 2000);
            $date = now()->subDays(rand(0, 90));

            $expense = Expense::create([
                'title' => $title,
                'account_head_id' => $head->id,
                'payment_account_head_id' => $paymentHead->id,
                'date' => $date->timestamp,
                'amount' => $amount,
                'school_id' => $schoolId,
                'session_id' => $sessionId,
            ]);

            $accountingService->recordVoucher(
                $schoolId,
                $sessionId,
                'payment',
                $date->format('Y-m-d'),
                $title,
                $head->id,
                $paymentHead->id,
                $amount,
                'expense',
                $expense->id,
                $createdBy
            );
        }

        foreach (range(1, 8) as $i) {
            $head = $incomeHeads->random();
            $titles = $this->incomeTitles[$head->name] ?? [$head->name];
            $title = $titles[array_rand($titles)];
            $paymentHead = $paymentHeads[array_rand($paymentHeads)];
            $amount = rand(500, 10000);
            $date = now()->subDays(rand(0, 90));

            $income = Income::create([
                'title' => $title,
                'account_head_id' => $head->id,
                'payment_account_head_id' => $paymentHead->id,
                'date' => $date->timestamp,
                'amount' => $amount,
                'school_id' => $schoolId,
                'session_id' => $sessionId,
            ]);

            $accountingService->recordVoucher(
                $schoolId,
                $sessionId,
                'receipt',
                $date->format('Y-m-d'),
                $title,
                $paymentHead->id,
                $head->id,
                $amount,
                'income',
                $income->id,
                $createdBy
            );
        }
    }
}
