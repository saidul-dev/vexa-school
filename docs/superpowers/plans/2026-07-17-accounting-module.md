# Accounting Module (Chart of Accounts + Voucher Ledger) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a persistent Chart of Accounts and a double-entry voucher ledger underneath Ekattor8's Accounting menu, wire the existing Expense Manager and Student Fee Manager into it, add a new Income Manager, and produce Receipts & Payments Statement and Trial Balance reports.

**Architecture:** Three new tables (`account_heads`, `account_vouchers`, `account_voucher_lines`) form the ledger core. A single `App\Services\AccountingService` class is the only writer to the voucher tables (`recordVoucher()` / `voidVoucherFor()`). Existing `AdminController`/`AccountantController` methods for Expense and Student Fee Manager, plus new methods for Income Manager and reports, call into this service — following the codebase's existing convention of duplicating controller logic between the Admin and Accountant roles rather than introducing a shared base class.

**Tech Stack:** Laravel 9, PHP 7.3/8.0, Blade, PHPUnit 9.5 (scaffolded fresh — none exists yet), SQLite in-memory for tests.

## Global Constraints

- Laravel 9 / PHP `^7.3|^8.0` — do not use PHP 8.1+-only syntax (no enums, no readonly properties, no first-class callable syntax).
- Follow the codebase's existing style: no type hints on model/controller method parameters, `Model::create([...])`/`Model::where(...)->update([...])` array style, no DB-level foreign key constraints (matches every existing migration).
- Every new controller method must be added to **both** `AdminController.php` and `AccountantController.php`, and every route to **both** the `admin/` and `accountant/` route groups in `routes/web.php` — this mirrors 100% of the existing Accounting feature set.
- `school_id` scoping: every query must filter by `auth()->user()->school_id`, matching every existing method in this area.
- Money fields on new tables use `decimal(12,2)`. Do not change the type of pre-existing money columns (`expenses.amount` stays `string`, `student_fee_managers.total_amount` stays `integer`) — only add new columns to those tables.
- Do not use `Schema::table(...)->dropColumn(...)` in any migration's `up()` method — this repo has no confirmed `doctrine/dbal` install and the test DB is SQLite, where dropping columns is unreliable pre-3.35. Leave superseded columns/tables in place unused instead of dropping them.
- All `expenses` and `student_fee_managers` rows currently in the database are confirmed demo data — migrations in this plan truncate them, no backfill/migration script is written.

---

## Task 1: Scaffold PHPUnit and patch the missing `schools.running_session` column

Every controller method this plan touches calls `get_school_settings($school_id)->value('running_session')`, but no migration in this repo creates that column — it exists only in whatever SQL dump this CodeCanyon script ships for production installs. Without it, every feature test in this plan would fail on an unrelated, pre-existing gap. This task scaffolds testing and closes that one gap so later tasks aren't blocked by it.

**Files:**
- Create: `phpunit.xml`
- Create: `tests/TestCase.php`
- Create: `tests/CreatesApplication.php`
- Create: `tests/Feature/.gitkeep`
- Create: `tests/Unit/.gitkeep`
- Create: `database/migrations/2026_07_17_000001_add_running_session_to_schools_table.php`
- Test: `tests/Feature/ExampleBootTest.php`

**Interfaces:**
- Produces: `Tests\TestCase` (base class for all Feature tests in this plan), `Tests\CreatesApplication`.

- [ ] **Step 1: Verify the User model already has the fields tests will need**

Run: `grep -n "protected \$fillable" -A3 "app/Models/User.php"`
Expected: the fillable array includes `role_id`, `school_id`, `account_status` (already confirmed present — this step is a sanity check before writing tests against them).

- [ ] **Step 2: Create `phpunit.xml`**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">./app</directory>
        </include>
    </coverage>
    <php>
        <server name="APP_ENV" value="testing"/>
        <server name="BCRYPT_ROUNDS" value="4"/>
        <server name="CACHE_DRIVER" value="array"/>
        <server name="DB_CONNECTION" value="sqlite"/>
        <server name="DB_DATABASE" value=":memory:"/>
        <server name="MAIL_MAILER" value="array"/>
        <server name="QUEUE_CONNECTION" value="sync"/>
        <server name="SESSION_DRIVER" value="array"/>
    </php>
</phpunit>
```

- [ ] **Step 3: Create `tests/CreatesApplication.php`**

```php
<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
```

- [ ] **Step 4: Create `tests/TestCase.php` with shared school/session/user fixtures**

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createSchoolWithSession()
    {
        $sessionId = DB::table('sessions')->insertGetId([
            'session_title' => '2026-2027',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $schoolId = DB::table('schools')->insertGetId([
            'title' => 'Test School',
            'email' => 'school@example.com',
            'phone' => 1234567890,
            'address' => 'Test Address',
            'school_info' => 'Test school info',
            'status' => 1,
            'running_session' => $sessionId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$schoolId, $sessionId];
    }

    protected function actingAsSchoolAdmin($schoolId)
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin+'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'role_id' => 2,
            'school_id' => $schoolId,
            'account_status' => 'active',
        ]);

        $this->actingAs($user);

        return $user;
    }

    protected function actingAsSchoolAccountant($schoolId)
    {
        $user = User::create([
            'name' => 'Test Accountant',
            'email' => 'accountant+'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'role_id' => 4,
            'school_id' => $schoolId,
            'account_status' => 'active',
        ]);

        $this->actingAs($user);

        return $user;
    }
}
```

- [ ] **Step 5: Create the `running_session` migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRunningSessionToSchoolsTable extends Migration
{
    public function up()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->integer('running_session')->nullable();
        });
    }

    public function down()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('running_session');
        });
    }
}
```

- [ ] **Step 6: Write a boot smoke test**

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleBootTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_boots_and_migrates()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();

        $this->assertDatabaseHas('schools', ['id' => $schoolId, 'running_session' => $sessionId]);
    }
}
```

- [ ] **Step 7: Run the test**

Run: `php artisan test --filter=ExampleBootTest`
Expected: `OK (1 test, ...)`. If it fails with "could not find driver" for sqlite, run `php -m` and confirm `pdo_sqlite`/`sqlite3` are enabled in the PHP CLI build being used; if unavailable, switch `DB_CONNECTION`/`DB_DATABASE` in `phpunit.xml` to a dedicated MySQL test database instead and re-run before continuing.

- [ ] **Step 8: Commit**

```bash
git add phpunit.xml tests/ database/migrations/2026_07_17_000001_add_running_session_to_schools_table.php
git commit -m "test: scaffold PHPUnit and patch missing schools.running_session column"
```

---

## Task 2: `account_heads` table and model

**Files:**
- Create: `database/migrations/2026_07_17_000002_create_account_heads_table.php`
- Create: `app/Models/AccountHead.php`
- Create: `database/factories/AccountHeadFactory.php`
- Test: `tests/Feature/AccountHeadModelTest.php`

**Interfaces:**
- Produces: `App\Models\AccountHead` with fillable `school_id, name, type, opening_balance, opening_balance_type, is_system, status`. `type` values used elsewhere in this plan: `asset`, `income`, `expense`. `status` values: `active`, `inactive`.

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\AccountHead;

class AccountHeadModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_an_account_head()
    {
        [$schoolId] = $this->createSchoolWithSession();

        $head = AccountHead::create([
            'school_id' => $schoolId,
            'name' => 'Cash in Hand',
            'type' => 'asset',
            'opening_balance' => 500,
            'opening_balance_type' => 'debit',
            'is_system' => true,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('account_heads', [
            'id' => $head->id,
            'name' => 'Cash in Hand',
            'type' => 'asset',
            'is_system' => 1,
        ]);
        $this->assertTrue($head->is_system);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=AccountHeadModelTest`
Expected: FAIL — "could not find driver" is wrong; expect a table/class-not-found error such as "no such table: account_heads" or "Class App\Models\AccountHead not found".

- [ ] **Step 3: Create the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountHeadsTable extends Migration
{
    public function up()
    {
        Schema::create('account_heads', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id');
            $table->string('name');
            $table->string('type');
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->string('opening_balance_type')->default('debit');
            $table->boolean('is_system')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_heads');
    }
}
```

- [ ] **Step 4: Create the model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountHead extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'name', 'type', 'opening_balance', 'opening_balance_type', 'is_system', 'status'
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'opening_balance' => 'float',
    ];
}
```

- [ ] **Step 5: Create the factory**

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AccountHeadFactory extends Factory
{
    public function definition()
    {
        return [
            'school_id' => 1,
            'name' => $this->faker->words(2, true),
            'type' => 'expense',
            'opening_balance' => 0,
            'opening_balance_type' => 'debit',
            'is_system' => false,
            'status' => 'active',
        ];
    }
}
```

- [ ] **Step 6: Run test to verify it passes**

Run: `php artisan test --filter=AccountHeadModelTest`
Expected: `OK (1 test, ...)`

- [ ] **Step 7: Commit**

```bash
git add database/migrations/2026_07_17_000002_create_account_heads_table.php app/Models/AccountHead.php database/factories/AccountHeadFactory.php tests/Feature/AccountHeadModelTest.php
git commit -m "feat: add account_heads table and model"
```

---

## Task 3: `account_vouchers` and `account_voucher_lines` tables and models

**Files:**
- Create: `database/migrations/2026_07_17_000003_create_account_vouchers_table.php`
- Create: `database/migrations/2026_07_17_000004_create_account_voucher_lines_table.php`
- Create: `app/Models/AccountVoucher.php`
- Create: `app/Models/AccountVoucherLine.php`
- Create: `database/factories/AccountVoucherFactory.php`
- Test: `tests/Feature/AccountVoucherModelTest.php`

**Interfaces:**
- Consumes: `App\Models\AccountHead` (Task 2).
- Produces: `App\Models\AccountVoucher` (fillable: `school_id, session_id, voucher_no, voucher_type, voucher_date, particulars, source_type, source_id, amount, created_by`; has-many `lines()`), `App\Models\AccountVoucherLine` (fillable: `voucher_id, account_head_id, debit, credit`; belongs-to `accountHead()` and `voucher()`).

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\AccountHead;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherLine;

class AccountVoucherModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_voucher_has_many_lines_and_lines_belong_to_a_head()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();

        $cash = AccountHead::factory()->create(['school_id' => $schoolId, 'name' => 'Cash in Hand', 'type' => 'asset']);
        $expenseHead = AccountHead::factory()->create(['school_id' => $schoolId, 'name' => 'Travelling', 'type' => 'expense']);

        $voucher = AccountVoucher::create([
            'school_id' => $schoolId,
            'session_id' => $sessionId,
            'voucher_no' => 'PV-000001',
            'voucher_type' => 'payment',
            'voucher_date' => '2026-07-17',
            'particulars' => 'Test payment',
            'amount' => 100,
        ]);

        AccountVoucherLine::create(['voucher_id' => $voucher->id, 'account_head_id' => $expenseHead->id, 'debit' => 100, 'credit' => 0]);
        AccountVoucherLine::create(['voucher_id' => $voucher->id, 'account_head_id' => $cash->id, 'debit' => 0, 'credit' => 100]);

        $this->assertCount(2, $voucher->lines);
        $this->assertEquals('Travelling', $voucher->lines->first()->accountHead->name);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=AccountVoucherModelTest`
Expected: FAIL — table/class not found.

- [ ] **Step 3: Create the `account_vouchers` migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountVouchersTable extends Migration
{
    public function up()
    {
        Schema::create('account_vouchers', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id');
            $table->integer('session_id');
            $table->string('voucher_no');
            $table->string('voucher_type');
            $table->date('voucher_date');
            $table->string('particulars');
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_vouchers');
    }
}
```

- [ ] **Step 4: Create the `account_voucher_lines` migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountVoucherLinesTable extends Migration
{
    public function up()
    {
        Schema::create('account_voucher_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voucher_id');
            $table->unsignedBigInteger('account_head_id');
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('credit', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_voucher_lines');
    }
}
```

- [ ] **Step 5: Create `AccountVoucher` model**

```php
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
}
```

- [ ] **Step 6: Create `AccountVoucherLine` model**

```php
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
```

- [ ] **Step 7: Create the factory**

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AccountVoucherFactory extends Factory
{
    public function definition()
    {
        return [
            'school_id' => 1,
            'session_id' => 1,
            'voucher_no' => 'PV-'.str_pad($this->faker->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'voucher_type' => 'payment',
            'voucher_date' => now()->format('Y-m-d'),
            'particulars' => $this->faker->sentence(3),
            'amount' => 100,
        ];
    }
}
```

- [ ] **Step 8: Run test to verify it passes**

Run: `php artisan test --filter=AccountVoucherModelTest`
Expected: `OK (1 test, ...)`

- [ ] **Step 9: Commit**

```bash
git add database/migrations/2026_07_17_000003_create_account_vouchers_table.php database/migrations/2026_07_17_000004_create_account_voucher_lines_table.php app/Models/AccountVoucher.php app/Models/AccountVoucherLine.php database/factories/AccountVoucherFactory.php tests/Feature/AccountVoucherModelTest.php
git commit -m "feat: add account_vouchers and account_voucher_lines tables and models"
```

---

## Task 4: `AccountingService::recordVoucher()` with sequential voucher numbering

**Files:**
- Create: `app/Services/AccountingService.php`
- Test: `tests/Feature/AccountingServiceRecordVoucherTest.php`

**Interfaces:**
- Consumes: `App\Models\AccountVoucher`, `App\Models\AccountVoucherLine` (Task 3).
- Produces: `App\Services\AccountingService::recordVoucher($schoolId, $sessionId, $voucherType, $voucherDate, $particulars, $debitHeadId, $creditHeadId, $amount, $sourceType = null, $sourceId = null, $createdBy = null)` — returns the created `AccountVoucher`, with exactly 2 `AccountVoucherLine` rows (one debit, one credit, both equal to `$amount`). `$voucherType` is one of `receipt`, `payment`, `journal`; `$voucherDate` is a `Y-m-d` string. Voucher numbers are `RV-000001` for `receipt`, `PV-000001` for `payment`, `JV-000001` for `journal`, monotonically increasing per `$schoolId` + `$voucherType`.

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\AccountHead;
use App\Services\AccountingService;

class AccountingServiceRecordVoucherTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_records_a_two_line_voucher_and_generates_sequential_numbers()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();

        $expenseHead = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'expense', 'name' => 'Travelling']);
        $cashHead = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'asset', 'name' => 'Cash in Hand']);

        $service = new AccountingService();

        $voucher1 = $service->recordVoucher($schoolId, $sessionId, 'payment', '2026-07-17', 'Bus fare', $expenseHead->id, $cashHead->id, 50, 'expense', 1);
        $voucher2 = $service->recordVoucher($schoolId, $sessionId, 'payment', '2026-07-18', 'Bus fare 2', $expenseHead->id, $cashHead->id, 30, 'expense', 2);

        $this->assertEquals('PV-000001', $voucher1->voucher_no);
        $this->assertEquals('PV-000002', $voucher2->voucher_no);
        $this->assertCount(2, $voucher1->lines);

        $debitLine = $voucher1->lines->firstWhere('account_head_id', $expenseHead->id);
        $creditLine = $voucher1->lines->firstWhere('account_head_id', $cashHead->id);

        $this->assertEquals(50, $debitLine->debit);
        $this->assertEquals(0, $debitLine->credit);
        $this->assertEquals(0, $creditLine->debit);
        $this->assertEquals(50, $creditLine->credit);
    }

    public function test_voucher_numbering_is_independent_per_type()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();
        $head1 = AccountHead::factory()->create(['school_id' => $schoolId]);
        $head2 = AccountHead::factory()->create(['school_id' => $schoolId]);

        $service = new AccountingService();

        $payment = $service->recordVoucher($schoolId, $sessionId, 'payment', '2026-07-17', 'x', $head1->id, $head2->id, 10);
        $receipt = $service->recordVoucher($schoolId, $sessionId, 'receipt', '2026-07-17', 'y', $head1->id, $head2->id, 10);

        $this->assertEquals('PV-000001', $payment->voucher_no);
        $this->assertEquals('RV-000001', $receipt->voucher_no);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=AccountingServiceRecordVoucherTest`
Expected: FAIL — `App\Services\AccountingService` not found.

- [ ] **Step 3: Create `app/Services/AccountingService.php`**

```php
<?php

namespace App\Services;

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
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=AccountingServiceRecordVoucherTest`
Expected: `OK (2 tests, ...)`

- [ ] **Step 5: Commit**

```bash
git add app/Services/AccountingService.php tests/Feature/AccountingServiceRecordVoucherTest.php
git commit -m "feat: add AccountingService::recordVoucher with sequential voucher numbering"
```

---

## Task 5: `AccountingService::voidVoucherFor()`

**Files:**
- Modify: `app/Services/AccountingService.php`
- Test: `tests/Feature/AccountingServiceVoidVoucherTest.php`

**Interfaces:**
- Produces: `AccountingService::voidVoucherFor($sourceType, $sourceId)` — deletes every `AccountVoucher` (and its `AccountVoucherLine` rows) matching `source_type`/`source_id`. No-op if none exist.

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\AccountHead;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherLine;
use App\Services\AccountingService;

class AccountingServiceVoidVoucherTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_vouchers_and_lines_for_a_source()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();
        $head1 = AccountHead::factory()->create(['school_id' => $schoolId]);
        $head2 = AccountHead::factory()->create(['school_id' => $schoolId]);

        $service = new AccountingService();
        $voucher = $service->recordVoucher($schoolId, $sessionId, 'payment', '2026-07-17', 'x', $head1->id, $head2->id, 10, 'expense', 5);
        $voucherId = $voucher->id;

        $service->voidVoucherFor('expense', 5);

        $this->assertDatabaseMissing('account_vouchers', ['id' => $voucherId]);
        $this->assertDatabaseMissing('account_voucher_lines', ['voucher_id' => $voucherId]);
    }

    public function test_it_is_a_noop_when_nothing_matches()
    {
        $service = new AccountingService();

        $service->voidVoucherFor('expense', 999);

        $this->assertTrue(true);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=AccountingServiceVoidVoucherTest`
Expected: FAIL — "Call to undefined method AccountingService::voidVoucherFor()".

- [ ] **Step 3: Add the method to `app/Services/AccountingService.php`** (insert after `recordVoucher`, before `nextVoucherNo`)

```php
    public function voidVoucherFor($sourceType, $sourceId)
    {
        $vouchers = AccountVoucher::where('source_type', $sourceType)->where('source_id', $sourceId)->get();

        foreach ($vouchers as $voucher) {
            AccountVoucherLine::where('voucher_id', $voucher->id)->delete();
            $voucher->delete();
        }
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=AccountingServiceVoidVoucherTest`
Expected: `OK (2 tests, ...)`

- [ ] **Step 5: Commit**

```bash
git add app/Services/AccountingService.php tests/Feature/AccountingServiceVoidVoucherTest.php
git commit -m "feat: add AccountingService::voidVoucherFor"
```

---

## Task 6: Extend `expenses` table for the ledger and clear demo data

**Files:**
- Create: `database/migrations/2026_07_17_000005_extend_expenses_table_for_ledger.php`
- Modify: `app/Models/Expense.php`
- Test: `tests/Feature/ExpenseModelTest.php`

**Interfaces:**
- Produces: `Expense` fillable becomes `title, account_head_id, payment_account_head_id, date, amount, school_id, session_id` (drops `expense_category_id` from fillable; the column itself is left in the table, unused, per the Global Constraints no-dropColumn rule).

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Expense;
use App\Models\AccountHead;

class ExpenseModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_an_expense_with_the_new_ledger_fields()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();
        $expenseHead = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'expense']);
        $cashHead = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'asset']);

        $expense = Expense::create([
            'title' => 'Office rent',
            'account_head_id' => $expenseHead->id,
            'payment_account_head_id' => $cashHead->id,
            'date' => strtotime('2026-07-17'),
            'amount' => '500',
            'school_id' => $schoolId,
            'session_id' => $sessionId,
        ]);

        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'title' => 'Office rent']);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=ExpenseModelTest`
Expected: FAIL — "no such column: title" (or mass-assignment silently dropping it, causing the assertDatabaseHas to fail).

- [ ] **Step 3: Create the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ExtendExpensesTableForLedger extends Migration
{
    public function up()
    {
        DB::table('expenses')->delete();

        Schema::table('expenses', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->unsignedBigInteger('account_head_id')->nullable();
            $table->unsignedBigInteger('payment_account_head_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['title', 'account_head_id', 'payment_account_head_id']);
        });
    }
}
```

- [ ] **Step 4: Update `app/Models/Expense.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'account_head_id', 'payment_account_head_id', 'date', 'amount', 'school_id', 'session_id'
    ];
}
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --filter=ExpenseModelTest`
Expected: `OK (1 test, ...)`

- [ ] **Step 6: Commit**

```bash
git add database/migrations/2026_07_17_000005_extend_expenses_table_for_ledger.php app/Models/Expense.php tests/Feature/ExpenseModelTest.php
git commit -m "feat: extend expenses table with ledger fields and clear demo data"
```

---

## Task 7: Extend `student_fee_managers` table for the ledger and clear demo data

**Files:**
- Create: `database/migrations/2026_07_17_000006_extend_student_fee_managers_table_for_ledger.php`
- Modify: `app/Models/StudentFeeManager.php`
- Test: `tests/Feature/StudentFeeManagerModelTest.php`

**Interfaces:**
- Produces: `StudentFeeManager` fillable gains `account_head_id`.

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\StudentFeeManager;
use App\Models\AccountHead;

class StudentFeeManagerModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_fee_invoice_with_an_account_head()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();
        $incomeHead = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'income']);

        $invoice = StudentFeeManager::create([
            'title' => 'Tuition Fee - July',
            'total_amount' => 1000,
            'class_id' => 1,
            'student_id' => 1,
            'payment_method' => 'cash',
            'paid_amount' => 0,
            'status' => 'unpaid',
            'school_id' => $schoolId,
            'session_id' => $sessionId,
            'timestamp' => strtotime('2026-07-17'),
            'account_head_id' => $incomeHead->id,
        ]);

        $this->assertDatabaseHas('student_fee_managers', ['id' => $invoice->id, 'account_head_id' => $incomeHead->id]);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=StudentFeeManagerModelTest`
Expected: FAIL — `account_head_id` not persisted (no such column).

- [ ] **Step 3: Create the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ExtendStudentFeeManagersTableForLedger extends Migration
{
    public function up()
    {
        DB::table('student_fee_managers')->delete();

        Schema::table('student_fee_managers', function (Blueprint $table) {
            $table->unsignedBigInteger('account_head_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('student_fee_managers', function (Blueprint $table) {
            $table->dropColumn('account_head_id');
        });
    }
}
```

- [ ] **Step 4: Update `app/Models/StudentFeeManager.php`**

```php
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
        'title', 'total_amount', 'class_id', 'parent_id','student_id', 'payment_method', 'paid_amount', 'status', 'school_id', 'session_id', 'timestamp', 'discounted_price', 'amount', 'account_head_id'
    ];
}
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --filter=StudentFeeManagerModelTest`
Expected: `OK (1 test, ...)`

- [ ] **Step 6: Commit**

```bash
git add database/migrations/2026_07_17_000006_extend_student_fee_managers_table_for_ledger.php app/Models/StudentFeeManager.php tests/Feature/StudentFeeManagerModelTest.php
git commit -m "feat: extend student_fee_managers table with account_head_id and clear demo data"
```

---

## Task 8: Seed default Account Heads per school

**Files:**
- Create: `database/seeders/AccountHeadSeeder.php`
- Test: `tests/Feature/AccountHeadSeederTest.php`

**Interfaces:**
- Produces: running `AccountHeadSeeder` creates exactly 3 `is_system=true` heads per school that doesn't already have any — `Cash in Hand` (asset), `Bank Account` (asset), `Student Fee Income` (income) — and is a no-op for schools that already have system heads.

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\AccountHead;
use Database\Seeders\AccountHeadSeeder;

class AccountHeadSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_three_default_heads_per_school()
    {
        [$schoolId] = $this->createSchoolWithSession();

        (new AccountHeadSeeder())->run();

        $this->assertEquals(3, AccountHead::where('school_id', $schoolId)->where('is_system', true)->count());
        $this->assertDatabaseHas('account_heads', ['school_id' => $schoolId, 'name' => 'Cash in Hand', 'type' => 'asset']);
        $this->assertDatabaseHas('account_heads', ['school_id' => $schoolId, 'name' => 'Bank Account', 'type' => 'asset']);
        $this->assertDatabaseHas('account_heads', ['school_id' => $schoolId, 'name' => 'Student Fee Income', 'type' => 'income']);
    }

    public function test_it_is_idempotent()
    {
        [$schoolId] = $this->createSchoolWithSession();

        (new AccountHeadSeeder())->run();
        (new AccountHeadSeeder())->run();

        $this->assertEquals(3, AccountHead::where('school_id', $schoolId)->count());
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=AccountHeadSeederTest`
Expected: FAIL — `Database\Seeders\AccountHeadSeeder` not found.

- [ ] **Step 3: Create `database/seeders/AccountHeadSeeder.php`**

```php
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
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=AccountHeadSeederTest`
Expected: `OK (2 tests, ...)`

- [ ] **Step 5: Run the seeder against the real database**

Run: `php artisan db:seed --class="Database\\Seeders\\AccountHeadSeeder"`
Expected: command exits 0; every existing school row now has 3 system account heads (verify with `php artisan tinker` → `App\Models\AccountHead::count()` if you want to double check, or query directly).

- [ ] **Step 6: Commit**

```bash
git add database/seeders/AccountHeadSeeder.php tests/Feature/AccountHeadSeederTest.php
git commit -m "feat: add AccountHeadSeeder for default per-school account heads"
```

---

## Task 9: Account Heads CRUD (Admin + Accountant)

**Files:**
- Modify: `app/Http/Controllers/AdminController.php` (add methods near the existing `expenseCategoryList`/`expenseCategoryCreate`/etc. block, i.e. after line ~3286)
- Modify: `app/Http/Controllers/AccountantController.php` (same, after line ~545)
- Modify: `routes/web.php` (admin group, after the "Expense category routes" block at line ~469; accountant group, after the equivalent block at line ~867)
- Modify: `resources/views/admin/navigation.blade.php` (Accounting submenu, lines 370–420)
- Modify: `resources/views/accountant/navigation.blade.php` (Accounting submenu, lines 97–137)
- Create: `resources/views/admin/account_heads/list.blade.php`
- Create: `resources/views/admin/account_heads/create.blade.php`
- Create: `resources/views/admin/account_heads/edit.blade.php`
- Create: `resources/views/accountant/account_heads/list.blade.php`
- Create: `resources/views/accountant/account_heads/create.blade.php`
- Create: `resources/views/accountant/account_heads/edit.blade.php`
- Test: `tests/Feature/AccountHeadCrudTest.php`

**Interfaces:**
- Consumes: `App\Models\AccountHead` (Task 2).
- Produces: routes `admin.account_heads.list`, `admin.account_heads.open_modal`, `admin.create.account_heads`, `admin.edit.account_heads`, `admin.account_heads.update`, `admin.account_heads.delete` (and the `accountant.*` equivalents).

- [ ] **Step 1: Write the failing HTTP test (admin side)**

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\AccountHead;

class AccountHeadCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_list_update_and_delete_an_account_head()
    {
        [$schoolId] = $this->createSchoolWithSession();
        $this->actingAsSchoolAdmin($schoolId);

        $this->post(route('admin.create.account_heads'), [
            'name' => 'Printing & Stationary',
            'type' => 'expense',
            'opening_balance' => 0,
            'opening_balance_type' => 'debit',
        ])->assertRedirect();

        $head = AccountHead::where('school_id', $schoolId)->where('name', 'Printing & Stationary')->first();
        $this->assertNotNull($head);

        $this->get(route('admin.account_heads.list'))->assertOk()->assertSee('Printing &amp; Stationary');

        $this->post(route('admin.account_heads.update', ['id' => $head->id]), [
            'name' => 'Printing and Stationary',
            'type' => 'expense',
            'opening_balance' => 10,
            'opening_balance_type' => 'debit',
        ])->assertRedirect();

        $this->assertDatabaseHas('account_heads', ['id' => $head->id, 'name' => 'Printing and Stationary', 'opening_balance' => 10]);

        $this->get(route('admin.account_heads.delete', ['id' => $head->id]))->assertRedirect();
        $this->assertDatabaseMissing('account_heads', ['id' => $head->id]);
    }

    public function test_a_system_account_head_cannot_be_deleted()
    {
        [$schoolId] = $this->createSchoolWithSession();
        $this->actingAsSchoolAdmin($schoolId);

        $head = AccountHead::factory()->create(['school_id' => $schoolId, 'is_system' => true]);

        $this->get(route('admin.account_heads.delete', ['id' => $head->id]))->assertRedirect();
        $this->assertDatabaseHas('account_heads', ['id' => $head->id]);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=AccountHeadCrudTest`
Expected: FAIL — route `admin.create.account_heads` not defined.

- [ ] **Step 3: Add methods to `app/Http/Controllers/AdminController.php`**

Insert after the existing `expenseCategoryDelete` method (around line 3286), using the same style as the neighboring expense-category methods:

```php
    public function accountHeadList()
    {
        $account_heads = AccountHead::where('school_id', auth()->user()->school_id)->paginate(10);
        return view('admin.account_heads.list', compact('account_heads'));
    }

    public function createAccountHead()
    {
        return view('admin.account_heads.create');
    }

    public function accountHeadCreate(Request $request)
    {
        $data = $request->all();

        AccountHead::create([
            'school_id' => auth()->user()->school_id,
            'name' => $data['name'],
            'type' => $data['type'],
            'opening_balance' => $data['opening_balance'] ?? 0,
            'opening_balance_type' => $data['opening_balance_type'] ?? 'debit',
            'is_system' => false,
            'status' => 'active',
        ]);

        return redirect()->back()->with('message', 'You have successfully created a new account head.');
    }

    public function editAccountHead($id)
    {
        $account_head = AccountHead::where('school_id', auth()->user()->school_id)->findOrFail($id);
        return view('admin.account_heads.edit', ['account_head' => $account_head]);
    }

    public function accountHeadUpdate(Request $request, $id)
    {
        $data = $request->all();
        $account_head = AccountHead::where('school_id', auth()->user()->school_id)->findOrFail($id);

        $account_head->update([
            'name' => $data['name'],
            'type' => $account_head->is_system ? $account_head->type : $data['type'],
            'opening_balance' => $data['opening_balance'] ?? 0,
            'opening_balance_type' => $data['opening_balance_type'] ?? 'debit',
        ]);

        return redirect()->back()->with('message', 'You have successfully updated account head.');
    }

    public function accountHeadDelete($id)
    {
        $account_head = AccountHead::where('school_id', auth()->user()->school_id)->findOrFail($id);

        if ($account_head->is_system) {
            return redirect()->back()->with('error', 'This is a default account head and cannot be deleted.');
        }

        $account_head->delete();

        return redirect()->back()->with('message', 'You have successfully deleted account head.');
    }
```

Add `use App\Models\AccountHead;` to the `use` block at the top of `AdminController.php` if not already present (it isn't — confirm with `grep -n "use App\\\\Models\\\\AccountHead" app/Http/Controllers/AdminController.php` returning nothing before adding).

- [ ] **Step 4: Add the identical methods to `app/Http/Controllers/AccountantController.php`**

Insert after the existing `expenseCategoryDelete` method (around line 545), same code as Step 3 but with view paths `accountant.account_heads.list`, `accountant.account_heads.create`, `accountant.account_heads.edit`. Also add `use App\Models\AccountHead;` to its `use` block.

- [ ] **Step 5: Add routes to `routes/web.php`** — admin group, after the "Expense category routes" block (after line 469, still inside the `AdminController` group closure)

```php
    //Account head routes
    Route::get('admin/account_heads/list', 'accountHeadList')->name('admin.account_heads.list')->middleware('admin_permission');
    Route::get('admin/account_heads/create', 'createAccountHead')->name('admin.account_heads.open_modal');
    Route::post('admin/account_heads/added', 'accountHeadCreate')->name('admin.create.account_heads');
    Route::get('admin/account_heads/{id}', 'editAccountHead')->name('admin.edit.account_heads');
    Route::post('admin/account_heads/{id}', 'accountHeadUpdate')->name('admin.account_heads.update');
    Route::get('admin/account_heads/delete/{id}', 'accountHeadDelete')->name('admin.account_heads.delete');
```

- [ ] **Step 6: Add the accountant routes** — accountant group, after the "Expenses category routes" block (after line 867)

```php
    //Account head routes
    Route::get('accountant/account_heads/list', 'accountHeadList')->name('accountant.account_heads.list');
    Route::get('accountant/account_heads/create', 'createAccountHead')->name('accountant.account_heads.open_modal');
    Route::post('accountant/account_heads/added', 'accountHeadCreate')->name('accountant.create.account_heads');
    Route::get('accountant/account_heads/{id}', 'editAccountHead')->name('accountant.edit.account_heads');
    Route::post('accountant/account_heads/{id}', 'accountHeadUpdate')->name('accountant.account_heads.update');
    Route::get('accountant/account_heads/delete/{id}', 'accountHeadDelete')->name('accountant.account_heads.delete');
```

- [ ] **Step 7: Create `resources/views/admin/account_heads/list.blade.php`**

```blade
@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Account Heads') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Accounting') }}</a></li>
                        <li><a href="#">{{ get_phrase('Account Heads') }}</a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.account_heads.open_modal') }}', '{{ get_phrase('Create Account Head') }}')"><i class="bi bi-plus"></i>{{ get_phrase('Add New Account Head') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            @if(count($account_heads) > 0)
            <div class="table-responsive tScrollFix pb-2">
                <table id="basic-datatable" class="table eTable">
                    <thead>
                      <tr>
                        <th>{{ get_phrase('Name') }}</th>
                        <th>{{ get_phrase('Type') }}</th>
                        <th>{{ get_phrase('Opening balance') }}</th>
                        <th class="text-end">{{ get_phrase('Option') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($account_heads as $account_head)
                        <tr>
                            <td>{{ $account_head->name }} @if($account_head->is_system) <span class="badge bg-secondary">{{ get_phrase('Default') }}</span> @endif</td>
                            <td>{{ ucfirst($account_head->type) }}</td>
                            <td>{{ school_currency($account_head->opening_balance) }} ({{ ucfirst($account_head->opening_balance_type) }})</td>
                            <td class="text-start">
                                <div class="adminTable-action">
                                    <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">
                                      {{ get_phrase('Actions') }}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                      <li>
                                        <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.edit.account_heads', ['id' => $account_head->id]) }}', '{{ get_phrase('Edit Account Head') }}')">{{ get_phrase('Edit') }}</a>
                                      </li>
                                      @if(!$account_head->is_system)
                                      <li>
                                        <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.account_heads.delete', ['id' => $account_head->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                                      </li>
                                      @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
            {{ $account_heads->links() }}
            @else
                <div class="empty_box center">
                    <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                    <br>
                    <span class="">{{ get_phrase('No data found') }}</span>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
```

- [ ] **Step 8: Create `resources/views/admin/account_heads/create.blade.php`**

```blade
<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.create.account_heads') }}">
    @csrf
    <div class="row">
        <div class="col-md-12 mb-3">
            <label class="eForm-label">{{ get_phrase('Name') }}</label>
            <input type="text" class="form-control eForm-control" name="name" required>
        </div>
        <div class="col-md-12 mb-3">
            <label class="eForm-label">{{ get_phrase('Type') }}</label>
            <select class="form-select eForm-select" name="type" required>
                <option value="asset">{{ get_phrase('Asset') }}</option>
                <option value="liability">{{ get_phrase('Liability') }}</option>
                <option value="income">{{ get_phrase('Income') }}</option>
                <option value="expense">{{ get_phrase('Expense') }}</option>
                <option value="equity">{{ get_phrase('Equity') }}</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="eForm-label">{{ get_phrase('Opening balance') }}</label>
            <input type="number" step="0.01" class="form-control eForm-control" name="opening_balance" value="0">
        </div>
        <div class="col-md-6 mb-3">
            <label class="eForm-label">{{ get_phrase('Opening balance type') }}</label>
            <select class="form-select eForm-select" name="opening_balance_type">
                <option value="debit">{{ get_phrase('Debit') }}</option>
                <option value="credit">{{ get_phrase('Credit') }}</option>
            </select>
        </div>
        <div class="col-md-12">
            <button type="submit" class="eBtn eBtn-secondary form-control">{{ get_phrase('Save') }}</button>
        </div>
    </div>
</form>
```

- [ ] **Step 9: Create `resources/views/admin/account_heads/edit.blade.php`**

```blade
<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.account_heads.update', ['id' => $account_head->id]) }}">
    @csrf
    <div class="row">
        <div class="col-md-12 mb-3">
            <label class="eForm-label">{{ get_phrase('Name') }}</label>
            <input type="text" class="form-control eForm-control" name="name" value="{{ $account_head->name }}" required>
        </div>
        <div class="col-md-12 mb-3">
            <label class="eForm-label">{{ get_phrase('Type') }}</label>
            <select class="form-select eForm-select" name="type" {{ $account_head->is_system ? 'disabled' : '' }} required>
                @foreach(['asset', 'liability', 'income', 'expense', 'equity'] as $type)
                    <option value="{{ $type }}" {{ $account_head->type == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
            @if($account_head->is_system)
                <input type="hidden" name="type" value="{{ $account_head->type }}">
            @endif
        </div>
        <div class="col-md-6 mb-3">
            <label class="eForm-label">{{ get_phrase('Opening balance') }}</label>
            <input type="number" step="0.01" class="form-control eForm-control" name="opening_balance" value="{{ $account_head->opening_balance }}">
        </div>
        <div class="col-md-6 mb-3">
            <label class="eForm-label">{{ get_phrase('Opening balance type') }}</label>
            <select class="form-select eForm-select" name="opening_balance_type">
                <option value="debit" {{ $account_head->opening_balance_type == 'debit' ? 'selected' : '' }}>{{ get_phrase('Debit') }}</option>
                <option value="credit" {{ $account_head->opening_balance_type == 'credit' ? 'selected' : '' }}>{{ get_phrase('Credit') }}</option>
            </select>
        </div>
        <div class="col-md-12">
            <button type="submit" class="eBtn eBtn-secondary form-control">{{ get_phrase('Update') }}</button>
        </div>
    </div>
</form>
```

- [ ] **Step 10: Copy the 3 views to the accountant namespace**

Create `resources/views/accountant/account_heads/list.blade.php`, `create.blade.php`, `edit.blade.php` identical to Steps 7–9, with `@extends('admin.navigation')` changed to `@extends('accountant.navigation')` and every `route('admin.*')` changed to `route('accountant.*')`.

- [ ] **Step 11: Add the sidebar entry to `resources/views/admin/navigation.blade.php`**

In the Accounting submenu block (lines 370–420), add a new `<li>` after the "Expense Category" entry (before the closing `@endif` at what was line ~415) and extend the outer permission/active-state checks:

```blade
              @if(empty($user->menu_permission) || in_array('admin.account_heads.list', $menu_permission))
              <li><a class="{{ (request()->is('admin/account_heads*')) ? 'active' : '' }}" href="{{ route('admin.account_heads.list') }}"><span>
                {{ get_phrase('Account Heads') }}
              </span></a></li>
              @endif
```

Also extend the outer `@if` condition (line ~370) to include `|| in_array('admin.account_heads.list', $menu_permission)`, and the `showMenu` class check (line ~371) to include `|| request()->is('admin/account_heads*')`.

- [ ] **Step 12: Add the sidebar entry to `resources/views/accountant/navigation.blade.php`**

In the Accounting submenu `<ul class="sub-menu">` block (lines ~117–135), add after the "Expense Category" `<li>`:

```blade
                    <li><a class="{{ (request()->is('accountant/account_heads*')) ? 'active' : '' }}" href="{{ route('accountant.account_heads.list') }}"><span>
                                {{ get_phrase('Account Heads') }}
                            </span></a></li>
```

Also extend the `showMenu` class check at the top of that `<li>` block to include `|| request()->is('accountant/account_heads*')`.

- [ ] **Step 13: Grant the new admin routes to whichever role/permission list gates menu items**

Run: `grep -rn "admin.expense.category_list" resources/views/admin/admin/menu_permission.blade.php`
If that file exists and lists route names for permission checkboxes, add `admin.account_heads.list` next to the existing `admin.expense.category_list` entry, mirroring its exact markup. If the file or pattern doesn't match this, note it and move on — this only affects the optional per-admin menu-permission restriction feature, not default access.

- [ ] **Step 14: Run test to verify it passes**

Run: `php artisan test --filter=AccountHeadCrudTest`
Expected: `OK (2 tests, ...)`

- [ ] **Step 15: Commit**

```bash
git add app/Http/Controllers/AdminController.php app/Http/Controllers/AccountantController.php routes/web.php resources/views/admin/navigation.blade.php resources/views/accountant/navigation.blade.php resources/views/admin/account_heads resources/views/accountant/account_heads tests/Feature/AccountHeadCrudTest.php
git commit -m "feat: add Account Heads CRUD for admin and accountant"
```

---

## Task 10: Wire Expense Manager into the ledger

**Files:**
- Modify: `app/Http/Controllers/AdminController.php` (`expenseCreate`, `expenseUpdate`, `expenseDelete`, `createExpense`, `editExpense`)
- Modify: `app/Http/Controllers/AccountantController.php` (same methods)
- Modify: `resources/views/admin/expenses/create.blade.php`, `edit.blade.php`
- Modify: `resources/views/accountant/expenses/create.blade.php`, `edit.blade.php`
- Modify: `resources/views/admin/expenses/list.blade.php`, `resources/views/accountant/expenses/list.blade.php`
- Test: `tests/Feature/ExpenseLedgerTest.php`

**Interfaces:**
- Consumes: `AccountingService::recordVoucher()`, `AccountingService::voidVoucherFor()` (Tasks 4–5).

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\AccountHead;
use App\Models\Expense;
use App\Models\AccountVoucher;

class ExpenseLedgerTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_an_expense_records_a_payment_voucher()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();
        $this->actingAsSchoolAdmin($schoolId);

        $expenseHead = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'expense', 'name' => 'Travelling']);
        $cashHead = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'asset', 'name' => 'Cash in Hand']);

        $this->post(route('admin.create.expenses'), [
            'title' => 'Bus fare',
            'account_head_id' => $expenseHead->id,
            'payment_account_head_id' => $cashHead->id,
            'date' => '07/17/2026',
            'amount' => '50',
        ])->assertRedirect();

        $expense = Expense::where('title', 'Bus fare')->first();
        $this->assertNotNull($expense);

        $voucher = AccountVoucher::where('source_type', 'expense')->where('source_id', $expense->id)->first();
        $this->assertNotNull($voucher);
        $this->assertEquals('payment', $voucher->voucher_type);
        $this->assertEquals(50, $voucher->amount);
        $this->assertCount(2, $voucher->lines);
    }

    public function test_deleting_an_expense_voids_its_voucher()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();
        $this->actingAsSchoolAdmin($schoolId);

        $expenseHead = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'expense']);
        $cashHead = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'asset']);

        $this->post(route('admin.create.expenses'), [
            'title' => 'Bus fare', 'account_head_id' => $expenseHead->id, 'payment_account_head_id' => $cashHead->id,
            'date' => '07/17/2026', 'amount' => '50',
        ]);
        $expense = Expense::first();

        $this->get(route('admin.expense.delete', ['id' => $expense->id]))->assertRedirect();

        $this->assertDatabaseMissing('account_vouchers', ['source_type' => 'expense', 'source_id' => $expense->id]);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=ExpenseLedgerTest`
Expected: FAIL — no voucher recorded (controller doesn't call `AccountingService` yet), or a validation error since the form fields being posted (`account_head_id`, `payment_account_head_id`) aren't the ones the current controller reads (`expense_category_id`).

- [ ] **Step 3: Update `expenseCreate` in `app/Http/Controllers/AdminController.php`**

```php
    public function expenseCreate(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $expense = Expense::create([
            'title' => $data['title'],
            'account_head_id' => $data['account_head_id'],
            'payment_account_head_id' => $data['payment_account_head_id'],
            'date' => strtotime($data['date']),
            'amount' => $data['amount'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        (new AccountingService())->recordVoucher(
            auth()->user()->school_id,
            $active_session,
            'payment',
            date('Y-m-d', $expense->date),
            $expense->title,
            $data['account_head_id'],
            $data['payment_account_head_id'],
            $data['amount'],
            'expense',
            $expense->id,
            auth()->user()->id
        );

        return redirect()->back()->with('message','You have successfully create a new expense.');
    }
```

- [ ] **Step 4: Update `expenseUpdate` in the same file**

```php
    public function expenseUpdate(Request $request, $id)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Expense::where('id', $id)->update([
            'title' => $data['title'],
            'account_head_id' => $data['account_head_id'],
            'payment_account_head_id' => $data['payment_account_head_id'],
            'date' => strtotime($data['date']),
            'amount' => $data['amount'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        $accountingService = new AccountingService();
        $accountingService->voidVoucherFor('expense', $id);
        $accountingService->recordVoucher(
            auth()->user()->school_id,
            $active_session,
            'payment',
            date('Y-m-d', strtotime($data['date'])),
            $data['title'],
            $data['account_head_id'],
            $data['payment_account_head_id'],
            $data['amount'],
            'expense',
            $id,
            auth()->user()->id
        );

        return redirect()->back()->with('message','You have successfully update expense.');
    }
```

- [ ] **Step 5: Update `expenseDelete` in the same file**

```php
    public function expenseDelete($id)
    {
        $expense = Expense::find($id);
        (new AccountingService())->voidVoucherFor('expense', $id);
        $expense->delete();
        return redirect()->back()->with('message','You have successfully delete expense.');
    }
```

- [ ] **Step 6: Update `createExpense` and `editExpense` to pass Account Heads instead of Expense Categories**

```php
    public function createExpense()
    {
        $expense_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'expense')->get();
        $asset_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'asset')->get();
        return view('admin.expenses.create', ['expense_heads' => $expense_heads, 'asset_heads' => $asset_heads]);
    }
```

```php
    public function editExpense($id)
    {
        $expense_details = Expense::find($id);
        $expense_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'expense')->get();
        $asset_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'asset')->get();
        return view('admin.expenses.edit', ['expense_heads' => $expense_heads, 'asset_heads' => $asset_heads, 'expense_details' => $expense_details]);
    }
```

- [ ] **Step 7: Add `use App\Models\AccountHead;` and `use App\Services\AccountingService;` to `AdminController.php`'s `use` block** (near the existing `use App\Models\Expense;` line)

- [ ] **Step 8: Repeat Steps 3–7 verbatim in `app/Http/Controllers/AccountantController.php`**, changing view paths from `admin.expenses.*` to `accountant.expenses.*`.

- [ ] **Step 9: Update `resources/views/admin/expenses/create.blade.php`** — replace the single `expense_category_id` field with `title`, `account_head_id`, `payment_account_head_id`:

```blade
<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.create.expenses') }}">
    @csrf
    <div class="row">
        <div class="col-md-12 mb-3">
            <label class="eForm-label">{{ get_phrase('Title') }}</label>
            <input type="text" class="form-control eForm-control" name="title" required>
        </div>
        <div class="col-md-12 mb-3">
            <label class="eForm-label">{{ get_phrase('Date') }}</label>
            <input type="text" class="form-control eForm-control inputDate" name="date" required>
        </div>
        <div class="col-md-12 mb-3">
            <label class="eForm-label">{{ get_phrase('Amount') }} ({{ school_currency('') }})</label>
            <input type="text" class="form-control eForm-control" name="amount" required>
        </div>
        <div class="col-md-12 mb-3">
            <label class="eForm-label">{{ get_phrase('Expense head') }}</label>
            <select class="form-select eForm-select eChoice-multiple-with-remove" name="account_head_id" required>
                @foreach ($expense_heads as $expense_head)
                    <option value="{{ $expense_head->id }}">{{ $expense_head->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 mb-3">
            <label class="eForm-label">{{ get_phrase('Paid from') }}</label>
            <select class="form-select eForm-select eChoice-multiple-with-remove" name="payment_account_head_id" required>
                @foreach ($asset_heads as $asset_head)
                    <option value="{{ $asset_head->id }}">{{ $asset_head->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12">
            <button type="submit" class="eBtn eBtn-secondary form-control">{{ get_phrase('Save') }}</button>
        </div>
    </div>
</form>
```

- [ ] **Step 10: Update `resources/views/admin/expenses/edit.blade.php`** the same way, pre-filling `value="{{ $expense_details->title }}"`, pre-selecting `account_head_id`/`payment_account_head_id` with `{{ $expense_details->account_head_id == $expense_head->id ? 'selected' : '' }}`, and posting to `route('admin.expenses.update', ['id' => $expense_details->id])`.

- [ ] **Step 11: Copy Steps 9–10 to `resources/views/accountant/expenses/create.blade.php` and `edit.blade.php`**, swapping the route names to `accountant.*`.

- [ ] **Step 12: Update `resources/views/admin/expenses/list.blade.php` and `resources/views/accountant/expenses/list.blade.php`** — replace the "Expense category" column with "Title" and "Expense head":

```blade
            <th>{{ get_phrase('Title') }}</th>
```
placed before the existing "Amount" column header, and in the row body replace:
```blade
                <td>
                    <?php $expense_categories = ExpenseCategory::find($expense['expense_category_id']); ?>
                    {{ $expense_categories['name'] }}
                </td>
```
with:
```blade
                <td>{{ $expense['title'] }}</td>
```
```blade
                <td>
                    <?php $account_head = \App\Models\AccountHead::find($expense['account_head_id']); ?>
                    {{ $account_head->name ?? '' }}
                </td>
```
(keeping the "Expense category"/`ExpenseCategory` header+cell replaced by "Expense head"/`AccountHead`, and adding the new "Title" column+cell before it). Remove the now-unused `use App\Models\ExpenseCategory;` line at the top of both list partials.

- [ ] **Step 13: Run test to verify it passes**

Run: `php artisan test --filter=ExpenseLedgerTest`
Expected: `OK (2 tests, ...)`

- [ ] **Step 14: Run the full suite to catch regressions**

Run: `php artisan test`
Expected: all tests pass.

- [ ] **Step 15: Commit**

```bash
git add app/Http/Controllers/AdminController.php app/Http/Controllers/AccountantController.php resources/views/admin/expenses resources/views/accountant/expenses tests/Feature/ExpenseLedgerTest.php
git commit -m "feat: wire Expense Manager into the double-entry ledger"
```

---

## Task 11: Income Manager (new feature)

**Files:**
- Create: `database/migrations/2026_07_17_000007_create_incomes_table.php`
- Create: `app/Models/Income.php`
- Modify: `app/Http/Controllers/AdminController.php` (new methods, mirroring Expense)
- Modify: `app/Http/Controllers/AccountantController.php` (same)
- Modify: `routes/web.php` (both groups)
- Modify: both `navigation.blade.php` files
- Create: `resources/views/admin/incomes/expense_manager.blade.php` → named `resources/views/admin/incomes/income_manager.blade.php`, `list.blade.php`, `create.blade.php`, `edit.blade.php`
- Create: `resources/views/accountant/incomes/income_manager.blade.php`, `list.blade.php`, `create.blade.php`, `edit.blade.php`
- Test: `tests/Feature/IncomeLedgerTest.php`

**Interfaces:**
- Produces: `App\Models\Income` (fillable: `title, account_head_id, payment_account_head_id, date, amount, school_id, session_id`); routes `admin.income.list`, `admin.income.open_modal`, `admin.create.income`, `admin.edit.income`, `admin.income.update`, `admin.income.delete` (and `accountant.*` equivalents).

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\AccountHead;
use App\Models\Income;
use App\Models\AccountVoucher;

class IncomeLedgerTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_an_income_records_a_receipt_voucher()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();
        $this->actingAsSchoolAdmin($schoolId);

        $incomeHead = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'income', 'name' => 'Donation']);
        $cashHead = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'asset', 'name' => 'Cash in Hand']);

        $this->post(route('admin.create.income'), [
            'title' => 'Annual donation',
            'account_head_id' => $incomeHead->id,
            'payment_account_head_id' => $cashHead->id,
            'date' => '07/17/2026',
            'amount' => '2000',
        ])->assertRedirect();

        $income = Income::where('title', 'Annual donation')->first();
        $this->assertNotNull($income);

        $voucher = AccountVoucher::where('source_type', 'income')->where('source_id', $income->id)->first();
        $this->assertNotNull($voucher);
        $this->assertEquals('receipt', $voucher->voucher_type);
        $this->assertEquals(2000, $voucher->amount);
    }

    public function test_deleting_an_income_voids_its_voucher()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();
        $this->actingAsSchoolAdmin($schoolId);

        $incomeHead = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'income']);
        $cashHead = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'asset']);

        $this->post(route('admin.create.income'), [
            'title' => 'Grant', 'account_head_id' => $incomeHead->id, 'payment_account_head_id' => $cashHead->id,
            'date' => '07/17/2026', 'amount' => '500',
        ]);
        $income = Income::first();

        $this->get(route('admin.income.delete', ['id' => $income->id]))->assertRedirect();

        $this->assertDatabaseMissing('account_vouchers', ['source_type' => 'income', 'source_id' => $income->id]);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=IncomeLedgerTest`
Expected: FAIL — route `admin.create.income` not defined.

- [ ] **Step 3: Create the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomesTable extends Migration
{
    public function up()
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('account_head_id');
            $table->unsignedBigInteger('payment_account_head_id');
            $table->integer('date');
            $table->decimal('amount', 12, 2);
            $table->integer('school_id');
            $table->integer('session_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('incomes');
    }
}
```

- [ ] **Step 4: Create `app/Models/Income.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'account_head_id', 'payment_account_head_id', 'date', 'amount', 'school_id', 'session_id'
    ];
}
```

- [ ] **Step 5: Add controller methods to `app/Http/Controllers/AdminController.php`** (insert after the Task 10 expense methods; add `use App\Models\Income;` to the `use` block)

```php
    public function incomeList(Request $request)
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if (count($request->all()) > 0) {
            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0].' 00:00:00');
            $date_to = strtotime($date[1].' 23:59:59');
        } else {
            $date_from = strtotime(date('d-m-Y', strtotime('first day of this month')).' 00:00:00');
            $date_to = strtotime(date('d-m-Y', strtotime('last day of this month')).' 23:59:59');
        }

        $incomes = Income::where('date', '>=', $date_from)
            ->where('date', '<=', $date_to)
            ->where('school_id', auth()->user()->school_id)
            ->where('session_id', $active_session)
            ->get();

        return view('admin.incomes.income_manager', ['incomes' => $incomes, 'date_from' => $date_from, 'date_to' => $date_to]);
    }

    public function createIncome()
    {
        $income_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'income')->get();
        $asset_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'asset')->get();
        return view('admin.incomes.create', ['income_heads' => $income_heads, 'asset_heads' => $asset_heads]);
    }

    public function incomeCreate(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $income = Income::create([
            'title' => $data['title'],
            'account_head_id' => $data['account_head_id'],
            'payment_account_head_id' => $data['payment_account_head_id'],
            'date' => strtotime($data['date']),
            'amount' => $data['amount'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        (new AccountingService())->recordVoucher(
            auth()->user()->school_id,
            $active_session,
            'receipt',
            date('Y-m-d', $income->date),
            $income->title,
            $data['payment_account_head_id'],
            $data['account_head_id'],
            $data['amount'],
            'income',
            $income->id,
            auth()->user()->id
        );

        return redirect()->back()->with('message', 'You have successfully create a new income.');
    }

    public function editIncome($id)
    {
        $income_details = Income::find($id);
        $income_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'income')->get();
        $asset_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'asset')->get();
        return view('admin.incomes.edit', ['income_heads' => $income_heads, 'asset_heads' => $asset_heads, 'income_details' => $income_details]);
    }

    public function incomeUpdate(Request $request, $id)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Income::where('id', $id)->update([
            'title' => $data['title'],
            'account_head_id' => $data['account_head_id'],
            'payment_account_head_id' => $data['payment_account_head_id'],
            'date' => strtotime($data['date']),
            'amount' => $data['amount'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        $accountingService = new AccountingService();
        $accountingService->voidVoucherFor('income', $id);
        $accountingService->recordVoucher(
            auth()->user()->school_id,
            $active_session,
            'receipt',
            date('Y-m-d', strtotime($data['date'])),
            $data['title'],
            $data['payment_account_head_id'],
            $data['account_head_id'],
            $data['amount'],
            'income',
            $id,
            auth()->user()->id
        );

        return redirect()->back()->with('message', 'You have successfully update income.');
    }

    public function incomeDelete($id)
    {
        $income = Income::find($id);
        (new AccountingService())->voidVoucherFor('income', $id);
        $income->delete();
        return redirect()->back()->with('message', 'You have successfully delete income.');
    }
```

Note the debit/credit order is reversed relative to Expense: an income receipt debits the asset (Cash/Bank) and credits the income head.

- [ ] **Step 6: Repeat Step 5 verbatim in `app/Http/Controllers/AccountantController.php`**, with view paths `accountant.incomes.*`.

- [ ] **Step 7: Add routes to `routes/web.php`** — admin group, after the Account Heads routes added in Task 9:

```php
    //Income routes
    Route::get('admin/income/list', 'incomeList')->name('admin.income.list')->middleware('admin_permission');
    Route::get('admin/income/create', 'createIncome')->name('admin.income.open_modal');
    Route::post('admin/income/added', 'incomeCreate')->name('admin.create.income');
    Route::get('admin/income/{id}', 'editIncome')->name('admin.edit.income');
    Route::post('admin/income/{id}', 'incomeUpdate')->name('admin.income.update');
    Route::get('admin/income/delete/{id}', 'incomeDelete')->name('admin.income.delete');
```

Accountant group, after the accountant Account Heads routes:

```php
    //Income routes
    Route::get('accountant/income/list', 'incomeList')->name('accountant.income.list');
    Route::get('accountant/income/create', 'createIncome')->name('accountant.income.open_modal');
    Route::post('accountant/income/added', 'incomeCreate')->name('accountant.create.income');
    Route::get('accountant/income/{id}', 'editIncome')->name('accountant.edit.income');
    Route::post('accountant/income/{id}', 'incomeUpdate')->name('accountant.income.update');
    Route::get('accountant/income/delete/{id}', 'incomeDelete')->name('accountant.income.delete');
```

- [ ] **Step 8: Create `resources/views/admin/incomes/income_manager.blade.php`**

Copy `resources/views/admin/expenses/expense_manager.blade.php` structure (parent list page with filter/export), replacing every "Expense"/`expense` reference with "Income"/`income`, the `@include` target with `admin.incomes.list`, the create-button route with `admin.income.open_modal`, the filter form's `action` with `route('admin.income.list')`, and dropping the `expense_category_id` select entirely (Income Manager's list has no category filter in v1 — just the date range):

```blade
@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Income') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Accounting') }}</a></li>
                        <li><a href="#">{{ get_phrase('Income') }}</a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.income.open_modal') }}', '{{ get_phrase('Create Income') }}')"><i class="bi bi-plus"></i>{{ get_phrase('Add New Income') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <form method="GET" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.income.list') }}">
                <div class="row justify-content-md-center">
                    <div class="col-xl-3 mb-3">
                        <input type="text" class="form-control eForm-control" name="eDateRange"
                            value="{{ date('m/d/Y', $date_from).' - '.date('m/d/Y', $date_to) }}" />
                    </div>
                    <div class="col-xl-2 mb-3">
                        <button type="submit" class="eBtn eBtn btn-secondary form-control">{{ get_phrase('Filter') }}</button>
                    </div>
                </div>
            </form>
            <div class="income_content">
                @include('admin.incomes.list')
            </div>
        </div>
    </div>
</div>
@endsection
```

- [ ] **Step 9: Create `resources/views/admin/incomes/list.blade.php`**

```blade
@if(count($incomes) > 0)
<div class="table-responsive tScrollFix pb-2" id="income_report">
    <table id="basic-datatable" class="table eTable">
        <thead>
          <tr>
            <th>{{ get_phrase('Date') }}</th>
            <th>{{ get_phrase('Title') }}</th>
            <th>{{ get_phrase('Amount') }}</th>
            <th>{{ get_phrase('Income head') }}</th>
            <th class="text-end">{{ get_phrase('Option') }}</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($incomes as $income): ?>
            <tr>
                <td>{{ date('D, d-M-Y', $income['date']) }}</td>
                <td>{{ $income['title'] }}</td>
                <td>{{ school_currency($income['amount']) }}</td>
                <td>
                    <?php $account_head = \App\Models\AccountHead::find($income['account_head_id']); ?>
                    {{ $account_head->name ?? '' }}
                </td>
                <td class="text-start">
                    <div class="adminTable-action">
                        <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">
                          {{ get_phrase('Actions') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                          <li>
                            <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.edit.income', ['id' => $income['id']]) }}', '{{ get_phrase('Edit Income') }}')">{{ get_phrase('Edit') }}</a>
                          </li>
                          <li>
                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.income.delete', ['id' => $income['id']]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                          </li>
                        </ul>
                    </div>
                </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
    </table>
</div>
@else
    <div class="empty_box center">
        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
        <br>
        <span class="">{{ get_phrase('No data found') }}</span>
    </div>
@endif
```

- [ ] **Step 10: Create `resources/views/admin/incomes/create.blade.php` and `edit.blade.php`**, structurally identical to `resources/views/admin/expenses/create.blade.php`/`edit.blade.php` from Task 10 Steps 9–10, but with `income_heads` in place of `expense_heads`, label "Income head" in place of "Expense head", label "Received into" in place of "Paid from", and posting to `admin.create.income` / `admin.income.update`.

- [ ] **Step 11: Copy Steps 8–10 into `resources/views/accountant/incomes/`**, swapping `@extends('admin.navigation')` for `@extends('accountant.navigation')` and every `admin.*` route to `accountant.*`.

- [ ] **Step 12: Add the sidebar entry to both `navigation.blade.php` files**, same pattern as Task 9 Steps 11–12, using route `admin.income.list` / `accountant.income.list` and label "Income Manager", placed after the "Account Heads" entry.

- [ ] **Step 13: Run test to verify it passes**

Run: `php artisan test --filter=IncomeLedgerTest`
Expected: `OK (2 tests, ...)`

- [ ] **Step 14: Run the full suite**

Run: `php artisan test`
Expected: all tests pass.

- [ ] **Step 15: Commit**

```bash
git add database/migrations/2026_07_17_000007_create_incomes_table.php app/Models/Income.php app/Http/Controllers/AdminController.php app/Http/Controllers/AccountantController.php routes/web.php resources/views/admin/navigation.blade.php resources/views/accountant/navigation.blade.php resources/views/admin/incomes resources/views/accountant/incomes tests/Feature/IncomeLedgerTest.php
git commit -m "feat: add Income Manager wired into the ledger"
```

---

## Task 12: Wire Student Fee Manager into the ledger

**Files:**
- Modify: `app/Http/Controllers/AdminController.php` (`feeManagerCreate`, `feeManagerUpdate`, `update_offline_payment`)
- Modify: `app/Http/Controllers/AccountantController.php` (same three methods)
- Test: `tests/Feature/StudentFeeLedgerTest.php`

**Interfaces:**
- Consumes: `AccountingService::recordVoucher()`, `AccountingService::voidVoucherFor()`; the seeded "Cash in Hand" / "Bank Account" / "Student Fee Income" heads (Task 8).
- Produces: `AccountingService::resolveAssetHead($schoolId, $paymentMethod)` and `AccountingService::defaultStudentFeeIncomeHead($schoolId)` helper methods, plus a shared rule applied identically in all three call sites: **a voucher (or set of vouchers) exists for a `student_fee` source if and only if the invoice's `status` is `'paid'`, sized to its current `paid_amount`.**

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\AccountHead;
use App\Models\StudentFeeManager;
use App\Models\AccountVoucher;

class StudentFeeLedgerTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_paid_invoice_records_a_receipt_voucher()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();
        $admin = $this->actingAsSchoolAdmin($schoolId);
        AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'asset', 'name' => 'Cash in Hand', 'is_system' => true]);
        AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'asset', 'name' => 'Bank Account', 'is_system' => true]);
        AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'income', 'name' => 'Student Fee Income', 'is_system' => true]);

        $studentId = \App\Models\User::create([
            'name' => 'Student One', 'email' => 'student1+'.uniqid().'@example.com', 'password' => bcrypt('x'),
            'role_id' => 5, 'school_id' => $schoolId, 'account_status' => 'active',
        ])->id;

        $this->post(route('admin.create.fee_manager', ['value' => 'single']), [
            'title' => 'Tuition Fee', 'amount' => 1000, 'discounted_price' => 0,
            'class_id' => 1, 'student_id' => $studentId, 'payment_method' => 'cash',
            'paid_amount' => 1000, 'status' => 'paid',
        ])->assertRedirect();

        $invoice = StudentFeeManager::where('title', 'Tuition Fee')->first();
        $this->assertNotNull($invoice);

        $voucher = AccountVoucher::where('source_type', 'student_fee')->where('source_id', $invoice->id)->first();
        $this->assertNotNull($voucher);
        $this->assertEquals('receipt', $voucher->voucher_type);
        $this->assertEquals(1000, $voucher->amount);
    }

    public function test_declining_an_offline_payment_leaves_no_voucher()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();
        $this->actingAsSchoolAdmin($schoolId);
        AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'asset', 'name' => 'Cash in Hand', 'is_system' => true]);
        AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'asset', 'name' => 'Bank Account', 'is_system' => true]);
        AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'income', 'name' => 'Student Fee Income', 'is_system' => true]);

        $invoice = StudentFeeManager::create([
            'title' => 'Tuition Fee', 'total_amount' => 1000, 'class_id' => 1, 'student_id' => 1,
            'payment_method' => 'offline', 'paid_amount' => 0, 'status' => 'pending',
            'school_id' => $schoolId, 'session_id' => $sessionId, 'timestamp' => strtotime('2026-07-17'),
        ]);

        $this->get(route('admin.update_offline_payment', ['id' => $invoice->id, 'status' => 'decline']))->assertRedirect();

        $this->assertDatabaseMissing('account_vouchers', ['source_type' => 'student_fee', 'source_id' => $invoice->id]);
    }

    public function test_approving_an_offline_payment_records_a_voucher()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();
        $this->actingAsSchoolAdmin($schoolId);
        AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'asset', 'name' => 'Cash in Hand', 'is_system' => true]);
        AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'asset', 'name' => 'Bank Account', 'is_system' => true]);
        AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'income', 'name' => 'Student Fee Income', 'is_system' => true]);

        $invoice = StudentFeeManager::create([
            'title' => 'Tuition Fee', 'total_amount' => 1500, 'class_id' => 1, 'student_id' => 1,
            'payment_method' => 'offline', 'paid_amount' => 0, 'status' => 'pending',
            'school_id' => $schoolId, 'session_id' => $sessionId, 'timestamp' => strtotime('2026-07-17'),
        ]);

        $this->get(route('admin.update_offline_payment', ['id' => $invoice->id, 'status' => 'approve']))->assertRedirect();

        $voucher = AccountVoucher::where('source_type', 'student_fee')->where('source_id', $invoice->id)->first();
        $this->assertNotNull($voucher);
        $this->assertEquals(1500, $voucher->amount);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=StudentFeeLedgerTest`
Expected: FAIL — no vouchers recorded yet.

- [ ] **Step 3: Add `resolveAssetHead` and `defaultStudentFeeIncomeHead` to `app/Services/AccountingService.php`** (insert after `voidVoucherFor`, before `nextVoucherNo`; add `use App\Models\AccountHead;` to the top of the file)

```php
    public function resolveAssetHead($schoolId, $paymentMethod)
    {
        $name = (strtolower((string) $paymentMethod) === 'cash') ? 'Cash in Hand' : 'Bank Account';

        return AccountHead::where('school_id', $schoolId)->where('type', 'asset')->where('name', $name)->first();
    }

    public function defaultStudentFeeIncomeHead($schoolId)
    {
        return AccountHead::where('school_id', $schoolId)->where('type', 'income')->where('name', 'Student Fee Income')->first();
    }
```

- [ ] **Step 4: Add a shared private helper to `AdminController.php`** that both `feeManagerCreate`/`feeManagerUpdate`/`update_offline_payment` call — insert directly above `feeManagerCreate` (around line 2945):

```php
    private function syncStudentFeeVoucher($invoice, $previousPaidAmount)
    {
        $accountingService = new AccountingService();

        if ($invoice->status != 'paid') {
            $accountingService->voidVoucherFor('student_fee', $invoice->id);
            return;
        }

        $delta = $invoice->paid_amount - $previousPaidAmount;

        if ($delta == 0) {
            return;
        }

        if ($delta < 0) {
            $accountingService->voidVoucherFor('student_fee', $invoice->id);
            if ($invoice->paid_amount <= 0) {
                return;
            }
            $delta = $invoice->paid_amount;
        }

        $assetHead = $accountingService->resolveAssetHead(auth()->user()->school_id, $invoice->payment_method);
        $incomeHead = $invoice->account_head_id ? $invoice->account_head_id : optional($accountingService->defaultStudentFeeIncomeHead(auth()->user()->school_id))->id;

        if (!$assetHead || !$incomeHead) {
            return;
        }

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $accountingService->recordVoucher(
            auth()->user()->school_id,
            $active_session,
            'receipt',
            date('Y-m-d'),
            $invoice->title,
            $assetHead->id,
            $incomeHead,
            $delta,
            'student_fee',
            $invoice->id,
            auth()->user()->id
        );
    }
```

- [ ] **Step 5: Call it from `feeManagerCreate`'s `single` branch**, right after `StudentFeeManager::create($data);` (and before the `return redirect()...` line):

```php
            $invoice = StudentFeeManager::create($data);
            $this->syncStudentFeeVoucher($invoice, 0);
```

(replace the bare `StudentFeeManager::create($data);` call with the two lines above; the `mass` branch inside the same method's `foreach ($enrolments as $enrolment)` loop gets the same treatment — replace `StudentFeeManager::create($data);` there with:)

```php
                $invoice = StudentFeeManager::create($data);
                $this->syncStudentFeeVoucher($invoice, 0);
```

- [ ] **Step 6: Call it from `feeManagerUpdate`**, right after the `StudentFeeManager::where('id', $id)->update([...]);` call:

```php
        StudentFeeManager::where('id', $id)->update([
            'title' => $data['title'],
            'total_amount' => $total_amount,
            'amount' => $data['amount'],
            'discounted_price' => $data['discounted_price'],
            'class_id' => $data['class_id'],
            'student_id' => $data['student_id'],
            'paid_amount' => $data['paid_amount'],
            'payment_method' => $data['payment_method'],
            'timestamp' => $timestamp,
            'status' => $data['status'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        $this->syncStudentFeeVoucher(StudentFeeManager::find($id), $previous_invoice_data['paid_amount']);

        return redirect()->back()->with('message','You have successfully update invoice.');
```

- [ ] **Step 7: Call it from `update_offline_payment`**, replacing both branches' bodies to capture the previous `paid_amount` first and call the helper after each update:

```php
    public function update_offline_payment($id,$status)
    {
        $studentFeeManager = StudentFeeManager::find($id);
        $previous_paid_amount = $studentFeeManager->paid_amount;
        $amount = $studentFeeManager->total_amount;

        $students_id = User::find($studentFeeManager->student_id);
        $student_email = $students_id->email;
        $parents_id = User::find($studentFeeManager->parent_id);

        if (!empty($parents_id)) {
          $parents_email = $parents_id->email;
        }

        if($status=='approve')
        {
            StudentFeeManager::where('id', $id)->update([
                'status' => 'paid',
                'updated_at'=>date("Y-m-d H:i:s"),
                'paid_amount' =>$amount,
                'payment_method' => 'offline']);

            $this->syncStudentFeeVoucher(StudentFeeManager::find($id), $previous_paid_amount);

            if(!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))){
                if (!empty($parents_id)) {
                    Mail::to($student_email)->send(new StudentsEmail($studentFeeManager));
                    Mail::to($parents_email)->send(new StudentsEmail($studentFeeManager));
                }else{
                    Mail::to($student_email)->send(new StudentsEmail($studentFeeManager));
                }
            }

                return redirect()->back()->with('message','Payment Approved');
        }
        elseif($status=='decline')
        {
            StudentFeeManager::where('id',$id)->update([
                'status' => 'unpaid',
                'updated_at'=>date("Y-m-d H:i:s"),
                'paid_amount' =>$amount,
                'payment_method' => 'offline']);

            $this->syncStudentFeeVoucher(StudentFeeManager::find($id), $previous_paid_amount);

                return redirect()->back()->with('message','Payment Decline');


        }


    }
```

- [ ] **Step 8: Add `use App\Services\AccountingService;` to `AdminController.php`'s `use` block** if Task 10 didn't already add it.

- [ ] **Step 9: Repeat Steps 4–8 in `app/Http/Controllers/AccountantController.php`**, adapted to that file's slightly different method bodies (`feeManagerCreate`'s `single` branch there does not compute `total_amount` from `amount - discounted_price`; leave that difference exactly as-is and only add the `syncStudentFeeVoucher` calls — `AccountantController`'s `update_offline_payment` also lacks the email-sending block Admin's has; leave that as-is too and only add the voucher sync call after each of its two `StudentFeeManager::where(...)->update(...)` calls, capturing `$previous_paid_amount` from `StudentFeeManager::find($id)->paid_amount` before either branch runs).

- [ ] **Step 10: Run test to verify it passes**

Run: `php artisan test --filter=StudentFeeLedgerTest`
Expected: `OK (3 tests, ...)`

- [ ] **Step 11: Run the full suite**

Run: `php artisan test`
Expected: all tests pass.

- [ ] **Step 12: Commit**

```bash
git add app/Services/AccountingService.php app/Http/Controllers/AdminController.php app/Http/Controllers/AccountantController.php tests/Feature/StudentFeeLedgerTest.php
git commit -m "feat: wire Student Fee Manager into the ledger via status-gated vouchers"
```

---

## Task 13: Receipts & Payments Statement report

**Files:**
- Modify: `app/Http/Controllers/AdminController.php` (new `receiptsPaymentsStatement` method)
- Modify: `app/Http/Controllers/AccountantController.php` (same)
- Modify: `routes/web.php` (both groups)
- Modify: both `navigation.blade.php` files
- Create: `resources/views/admin/reports/receipts_payments.blade.php`
- Create: `resources/views/accountant/reports/receipts_payments.blade.php`
- Test: `tests/Feature/ReceiptsPaymentsStatementTest.php`

**Interfaces:**
- Consumes: `App\Models\AccountVoucherLine` joined to `AccountVoucher`/`AccountHead` (Tasks 2–3).
- Produces: route `admin.reports.receipts_payments` (and `accountant.*`) rendering, for a `From`/`To` date range: the raw transaction list, and a per-head summary with Opening/Period/Closing balances where **Opening Balance(head, From) = head.opening_balance + Σ(debit − credit) for all lines dated before From**, and **Closing = Opening + Σ(debit − credit) for lines within [From, To]**.

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\AccountHead;
use App\Services\AccountingService;

class ReceiptsPaymentsStatementTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_computes_opening_and_closing_balance_for_a_head()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();
        $this->actingAsSchoolAdmin($schoolId);

        $cash = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'asset', 'name' => 'Cash in Hand', 'opening_balance' => 100, 'opening_balance_type' => 'debit']);
        $expenseHead = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'expense', 'name' => 'Travelling']);

        $service = new AccountingService();
        // before the report window: cash goes down by 20
        $service->recordVoucher($schoolId, $sessionId, 'payment', '2026-06-01', 'old', $expenseHead->id, $cash->id, 20);
        // inside the report window: cash goes down by 30
        $service->recordVoucher($schoolId, $sessionId, 'payment', '2026-07-05', 'new', $expenseHead->id, $cash->id, 30);

        $response = $this->get(route('admin.reports.receipts_payments', ['from' => '2026-07-01', 'to' => '2026-07-31']));

        $response->assertOk();
        // opening = 100 - 20 = 80, closing = 80 - 30 = 50
        $response->assertSee('80.00', false);
        $response->assertSee('50.00', false);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=ReceiptsPaymentsStatementTest`
Expected: FAIL — route `admin.reports.receipts_payments` not defined.

- [ ] **Step 3: Add `receiptsPaymentsStatement` to `app/Http/Controllers/AdminController.php`** (insert after the Task 12 changes; add `use App\Models\AccountVoucher;`, `use App\Models\AccountVoucherLine;` to the `use` block if not already present)

```php
    public function receiptsPaymentsStatement(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $from = $request->input('from', date('Y-m-01'));
        $to = $request->input('to', date('Y-m-t'));

        $lines = AccountVoucherLine::whereHas('voucher', function ($query) use ($schoolId, $from, $to) {
                $query->where('school_id', $schoolId)->whereBetween('voucher_date', [$from, $to]);
            })
            ->with(['voucher', 'accountHead'])
            ->get()
            ->sortBy(function ($line) { return $line->voucher->voucher_date; });

        $heads = AccountHead::where('school_id', $schoolId)->get();

        $summary = $heads->map(function ($head) use ($from, $to) {
            $priorMovement = AccountVoucherLine::where('account_head_id', $head->id)
                ->whereHas('voucher', function ($query) use ($from) {
                    $query->where('voucher_date', '<', $from);
                })
                ->selectRaw('COALESCE(SUM(debit),0) - COALESCE(SUM(credit),0) as net')
                ->value('net');

            $openingSigned = $head->opening_balance_type == 'debit' ? $head->opening_balance : -$head->opening_balance;
            $opening = $openingSigned + (float) $priorMovement;

            $periodDebit = AccountVoucherLine::where('account_head_id', $head->id)
                ->whereHas('voucher', function ($query) use ($from, $to) {
                    $query->whereBetween('voucher_date', [$from, $to]);
                })->sum('debit');

            $periodCredit = AccountVoucherLine::where('account_head_id', $head->id)
                ->whereHas('voucher', function ($query) use ($from, $to) {
                    $query->whereBetween('voucher_date', [$from, $to]);
                })->sum('credit');

            $closing = $opening + $periodDebit - $periodCredit;

            return [
                'head' => $head,
                'opening' => $opening,
                'debit' => $periodDebit,
                'credit' => $periodCredit,
                'closing' => $closing,
            ];
        });

        return view('admin.reports.receipts_payments', ['lines' => $lines, 'summary' => $summary, 'from' => $from, 'to' => $to]);
    }
```

- [ ] **Step 4: Repeat Step 3 in `app/Http/Controllers/AccountantController.php`**, view path `accountant.reports.receipts_payments`.

- [ ] **Step 5: Add routes to `routes/web.php`** — admin group, after the Task 11 income routes:

```php
    //Report routes
    Route::get('admin/reports/receipts_payments', 'receiptsPaymentsStatement')->name('admin.reports.receipts_payments')->middleware('admin_permission');
```

Accountant group:

```php
    //Report routes
    Route::get('accountant/reports/receipts_payments', 'receiptsPaymentsStatement')->name('accountant.reports.receipts_payments');
```

- [ ] **Step 6: Create `resources/views/admin/reports/receipts_payments.blade.php`**

```blade
@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <h4>{{ get_phrase('Receipts & Payments Statement') }}</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <form method="GET" class="d-block" action="{{ route('admin.reports.receipts_payments') }}">
                <div class="row justify-content-md-center">
                    <div class="col-md-3 mb-3">
                        <label class="eForm-label">{{ get_phrase('From') }}</label>
                        <input type="date" class="form-control eForm-control" name="from" value="{{ $from }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="eForm-label">{{ get_phrase('To') }}</label>
                        <input type="date" class="form-control eForm-control" name="to" value="{{ $to }}">
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="eBtn eBtn-secondary form-control">{{ get_phrase('Filter') }}</button>
                    </div>
                </div>
            </form>

            <h5>{{ get_phrase('Transactions') }}</h5>
            <div class="table-responsive tScrollFix pb-2">
                <table class="table eTable">
                    <thead>
                        <tr>
                            <th>{{ get_phrase('Date') }}</th>
                            <th>{{ get_phrase('Particulars') }}</th>
                            <th>{{ get_phrase('Account Head') }}</th>
                            <th>{{ get_phrase('Voucher No') }}</th>
                            <th>{{ get_phrase('Debit') }}</th>
                            <th>{{ get_phrase('Credit') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lines as $line)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($line->voucher->voucher_date)->format('d-M-Y') }}</td>
                            <td>{{ $line->voucher->particulars }}</td>
                            <td>{{ $line->accountHead->name }}</td>
                            <td>{{ $line->voucher->voucher_no }}</td>
                            <td>{{ $line->debit > 0 ? number_format($line->debit, 2) : '' }}</td>
                            <td>{{ $line->credit > 0 ? number_format($line->credit, 2) : '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h5>{{ get_phrase('Summary by Account Head') }}</h5>
            <div class="table-responsive tScrollFix pb-2">
                <table class="table eTable">
                    <thead>
                        <tr>
                            <th>{{ get_phrase('Account Head') }}</th>
                            <th>{{ get_phrase('Type') }}</th>
                            <th>{{ get_phrase('Opening balance') }}</th>
                            <th>{{ get_phrase('Debit') }}</th>
                            <th>{{ get_phrase('Credit') }}</th>
                            <th>{{ get_phrase('Closing balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($summary as $row)
                        <tr>
                            <td>{{ $row['head']->name }}</td>
                            <td>{{ ucfirst($row['head']->type) }}</td>
                            <td>{{ number_format($row['opening'], 2) }}</td>
                            <td>{{ number_format($row['debit'], 2) }}</td>
                            <td>{{ number_format($row['credit'], 2) }}</td>
                            <td>{{ number_format($row['closing'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
```

- [ ] **Step 7: Copy Step 6 to `resources/views/accountant/reports/receipts_payments.blade.php`**, swapping `@extends('admin.navigation')` for `@extends('accountant.navigation')` and the form `action` to `route('accountant.reports.receipts_payments')`.

- [ ] **Step 8: Add the sidebar entry to both `navigation.blade.php` files**, same pattern as Task 9 Steps 11–12, label "Receipts & Payments Statement", route `admin.reports.receipts_payments` / `accountant.reports.receipts_payments`, placed after the "Income Manager" entry.

- [ ] **Step 9: Run test to verify it passes**

Run: `php artisan test --filter=ReceiptsPaymentsStatementTest`
Expected: `OK (1 test, ...)`

- [ ] **Step 10: Commit**

```bash
git add app/Http/Controllers/AdminController.php app/Http/Controllers/AccountantController.php routes/web.php resources/views/admin/navigation.blade.php resources/views/accountant/navigation.blade.php resources/views/admin/reports resources/views/accountant/reports tests/Feature/ReceiptsPaymentsStatementTest.php
git commit -m "feat: add Receipts & Payments Statement report"
```

---

## Task 14: Trial Balance report

**Files:**
- Modify: `app/Http/Controllers/AdminController.php` (new `trialBalance` method)
- Modify: `app/Http/Controllers/AccountantController.php` (same)
- Modify: `routes/web.php` (both groups)
- Modify: both `navigation.blade.php` files
- Create: `resources/views/admin/reports/trial_balance.blade.php`
- Create: `resources/views/accountant/reports/trial_balance.blade.php`
- Test: `tests/Feature/TrialBalanceTest.php`

**Interfaces:**
- Consumes: same data as Task 13.
- Produces: route `admin.reports.trial_balance` (and `accountant.*`) — for each active head, Dr/Cr balance as of a given date, with a footer confirming total debit == total credit.

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\AccountHead;
use App\Services\AccountingService;

class TrialBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_total_debit_equals_total_credit()
    {
        [$schoolId, $sessionId] = $this->createSchoolWithSession();
        $this->actingAsSchoolAdmin($schoolId);

        $cash = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'asset', 'name' => 'Cash in Hand']);
        $expenseHead = AccountHead::factory()->create(['school_id' => $schoolId, 'type' => 'expense', 'name' => 'Travelling']);

        (new AccountingService())->recordVoucher($schoolId, $sessionId, 'payment', '2026-07-10', 'fare', $expenseHead->id, $cash->id, 75);

        $response = $this->get(route('admin.reports.trial_balance', ['as_of' => '2026-07-31']));

        $response->assertOk();
        $response->assertSee('75.00', false);
        $response->assertViewHas('totals', function ($totals) {
            return abs($totals['debit'] - $totals['credit']) < 0.001;
        });
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=TrialBalanceTest`
Expected: FAIL — route `admin.reports.trial_balance` not defined.

- [ ] **Step 3: Add `trialBalance` to `app/Http/Controllers/AdminController.php`**

```php
    public function trialBalance(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $asOf = $request->input('as_of', date('Y-m-d'));

        $heads = AccountHead::where('school_id', $schoolId)->where('status', 'active')->get();

        $rows = $heads->map(function ($head) use ($asOf) {
            $movement = AccountVoucherLine::where('account_head_id', $head->id)
                ->whereHas('voucher', function ($query) use ($asOf) {
                    $query->where('voucher_date', '<=', $asOf);
                })
                ->selectRaw('COALESCE(SUM(debit),0) as debit, COALESCE(SUM(credit),0) as credit')
                ->first();

            $openingSigned = $head->opening_balance_type == 'debit' ? $head->opening_balance : -$head->opening_balance;
            $net = $openingSigned + (float) $movement->debit - (float) $movement->credit;

            return [
                'head' => $head,
                'debit' => $net > 0 ? $net : 0,
                'credit' => $net < 0 ? abs($net) : 0,
            ];
        });

        $totals = [
            'debit' => $rows->sum('debit'),
            'credit' => $rows->sum('credit'),
        ];

        return view('admin.reports.trial_balance', ['rows' => $rows, 'totals' => $totals, 'as_of' => $asOf]);
    }
```

- [ ] **Step 4: Repeat Step 3 in `app/Http/Controllers/AccountantController.php`**, view path `accountant.reports.trial_balance`.

- [ ] **Step 5: Add routes to `routes/web.php`** — admin group, after the receipts & payments route:

```php
    Route::get('admin/reports/trial_balance', 'trialBalance')->name('admin.reports.trial_balance')->middleware('admin_permission');
```

Accountant group:

```php
    Route::get('accountant/reports/trial_balance', 'trialBalance')->name('accountant.reports.trial_balance');
```

- [ ] **Step 6: Create `resources/views/admin/reports/trial_balance.blade.php`**

```blade
@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <h4>{{ get_phrase('Trial Balance') }}</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <form method="GET" class="d-block" action="{{ route('admin.reports.trial_balance') }}">
                <div class="row justify-content-md-center">
                    <div class="col-md-3 mb-3">
                        <label class="eForm-label">{{ get_phrase('As of') }}</label>
                        <input type="date" class="form-control eForm-control" name="as_of" value="{{ $as_of }}">
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="eBtn eBtn-secondary form-control">{{ get_phrase('Filter') }}</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive tScrollFix pb-2">
                <table class="table eTable">
                    <thead>
                        <tr>
                            <th>{{ get_phrase('Account Head') }}</th>
                            <th>{{ get_phrase('Type') }}</th>
                            <th>{{ get_phrase('Debit') }}</th>
                            <th>{{ get_phrase('Credit') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                        <tr>
                            <td>{{ $row['head']->name }}</td>
                            <td>{{ ucfirst($row['head']->type) }}</td>
                            <td>{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}</td>
                            <td>{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">{{ get_phrase('Total') }}</th>
                            <th>{{ number_format($totals['debit'], 2) }}</th>
                            <th>{{ number_format($totals['credit'], 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if(abs($totals['debit'] - $totals['credit']) > 0.01)
                <div class="alert alert-danger">{{ get_phrase('Warning: the ledger does not balance.') }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
```

- [ ] **Step 7: Copy Step 6 to `resources/views/accountant/reports/trial_balance.blade.php`**, swapping the navigation extend and form `action` to `accountant.reports.trial_balance`.

- [ ] **Step 8: Add the sidebar entry to both `navigation.blade.php` files**, label "Trial Balance", route `admin.reports.trial_balance` / `accountant.reports.trial_balance`, placed after the "Receipts & Payments Statement" entry.

- [ ] **Step 9: Run test to verify it passes**

Run: `php artisan test --filter=TrialBalanceTest`
Expected: `OK (1 test, ...)`

- [ ] **Step 10: Run the entire suite one last time**

Run: `php artisan test`
Expected: all tests across every task in this plan pass.

- [ ] **Step 11: Commit**

```bash
git add app/Http/Controllers/AdminController.php app/Http/Controllers/AccountantController.php routes/web.php resources/views/admin/navigation.blade.php resources/views/accountant/navigation.blade.php resources/views/admin/reports resources/views/accountant/reports tests/Feature/TrialBalanceTest.php
git commit -m "feat: add Trial Balance report"
```
