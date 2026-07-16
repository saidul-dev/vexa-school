# Accounting Module (Chart of Accounts + Voucher Ledger) — Design Spec

Date: 2026-07-17

## Background

Ekattor8's "Accounting" menu currently has four items: Student Fee Manager,
Offline Payment Request, Expense Manager, Expense Category. Investigation
found none of them implement real double-entry bookkeeping:

- `expenses` table = `{expense_category_id, date, amount, school_id, session_id}`.
  No title/particulars, no debit/credit, no voucher number, no payment
  account.
- `expense_categories` table = `{name, school_id, session_id}` — a flat
  label list.
- `student_fee_managers` table produces per-student invoices/payment
  status, not a consolidated ledger.
- No `ledger`, `account_head`, `chart_of_account`, `voucher`,
  `opening_balance`, or `closing_balance` concept exists anywhere in the
  codebase (verified by full-text search).

The user wants a standard accounting layer added: a Chart of Accounts,
a double-entry voucher engine feeding it, and the two most important
reports (Receipts & Payments Statement, Trial Balance), while extending
the existing Expense Manager and Student Fee Manager to plug into it
instead of building something disconnected.

All current `expenses`, `expense_categories`, and `student_fee_managers`
rows are confirmed demo/test data — no migration/backfill is needed; the
schema changes replace them outright.

## Goals

1. A persistent, school-level Chart of Accounts (Account Heads).
2. A double-entry voucher/ledger engine underneath every money
   transaction (expense, income, student fee receipt).
3. Extend Expense Manager and Student Fee Manager to auto-generate
   ledger vouchers instead of being flat, disconnected records.
4. A new generic Income/Receipt Voucher feature for non-fee income
   (donations, grants, etc).
5. Two reports: Receipts & Payments Statement, Trial Balance.

## Non-goals (explicitly out of scope for this spec)

- Full manual double-entry UI (user always picks one head; the offsetting
  side is implied by payment mode).
- Multiple configurable bank accounts beyond the two seeded defaults
  (Cash in Hand, Bank Account) — schema supports adding more later since
  Account Heads are admin-manageable, but no multi-bank UI work is done now.
- Migrating/backfilling existing expense or fee rows into the ledger.
- Ledger-by-account drill-down view, Income & Expenditure / P&L statement,
  Balance Sheet — future work.
- Formal reversal/adjustment entries on edit or delete (v1 deletes and
  recreates the linked voucher instead of preserving an audit trail).

## Data Model

### `account_heads` (Chart of Accounts)

| column | type | notes |
|---|---|---|
| id | bigint pk | |
| school_id | int | scope; **no session_id** — persists across academic sessions |
| name | string | e.g. "Cash in Hand", "Travelling and Conveyance" |
| type | enum | `asset`, `liability`, `income`, `expense`, `equity` |
| opening_balance | decimal(12,2) | default 0 — balance carried in from before the system was used |
| opening_balance_type | enum | `debit`, `credit` |
| is_system | boolean | true for seeded defaults (Cash in Hand, Bank Account, Student Fee Income) — blocks deletion, allows rename |
| status | enum | `active`, `inactive` |
| timestamps | | |

Seeded per school on creation (or via a one-time seeder for existing
schools): *Cash in Hand* (asset), *Bank Account* (asset), *Student Fee
Income* (income).

`expense_categories` is retired. The "Expense Category" screen becomes a
thin view over `account_heads` filtered to `type = 'expense'`, reusing the
same list/create/edit UI pattern the user already knows.

### `account_vouchers` (transaction header)

| column | type | notes |
|---|---|---|
| id | bigint pk | |
| school_id | int | |
| session_id | int | current academic session at time of entry — used for session-scoped filtering, not balance calculation |
| voucher_no | string | auto, sequential per school+type: `RV-000123` (receipt), `PV-000123` (payment) |
| voucher_type | enum | `receipt`, `payment`, `journal` (journal reserved for future manual entries) |
| voucher_date | date | |
| particulars | string | human description, e.g. expense title, "Fee payment - Rahim - Jan Tuition" |
| source_type | string, nullable | `expense`, `income`, `student_fee` |
| source_id | bigint, nullable | id of the originating row |
| amount | decimal(12,2) | total voucher amount (both lines equal this) |
| created_by | bigint, nullable | user id |
| timestamps | | |

### `account_voucher_lines`

| column | type | notes |
|---|---|---|
| id | bigint pk | |
| voucher_id | bigint fk | |
| account_head_id | bigint fk | |
| debit | decimal(12,2) | default 0 |
| credit | decimal(12,2) | default 0 |
| timestamps | | |

v1 always writes exactly two lines per voucher (one debit, one credit),
same amount, enforced in the service layer — not a DB constraint, to
leave room for true multi-line journal entries later.

### `incomes` (new, mirrors `expenses`)

| column | type | notes |
|---|---|---|
| id | bigint pk | |
| title | string | |
| account_head_id | bigint fk | must be `type = income` |
| payment_account_head_id | bigint fk | must be `type = asset` (Cash/Bank) |
| date | int | matches `expenses.date` convention |
| amount | decimal(12,2) | |
| school_id, session_id | int | |
| timestamps | | |

### `expenses` (extended)

Add: `title` (string), `account_head_id` (fk, replaces
`expense_category_id`, must be `type = expense`), `payment_account_head_id`
(fk, must be `type = asset`). Drop `expense_category_id`.

### `student_fee_managers` (extended)

Add: `account_head_id` (fk, nullable, defaults to the seeded "Student Fee
Income" head, must be `type = income`).

## Voucher Generation Logic

A single `App\Services\AccountingService` class is the only place that
writes to `account_vouchers`/`account_voucher_lines`:

- `recordVoucher(schoolId, sessionId, type, date, particulars, debitHeadId, creditHeadId, amount, sourceType, sourceId, createdBy)`
  — creates one voucher + two lines. Generates the next sequential
  `voucher_no` for the school+type.
- `voidVoucherFor(sourceType, sourceId)` — deletes the voucher(s) (and
  cascades to lines) tied to a given source record. Called before
  recreating on edit, and on delete.

Call sites:

- **Expense create**: `recordVoucher(..., type: payment, debit: expense.account_head_id, credit: expense.payment_account_head_id, amount: expense.amount, source: ['expense', expense.id])`.
- **Expense update**: `voidVoucherFor('expense', id)` then re-record with new values.
- **Expense delete**: `voidVoucherFor('expense', id)`.
- **Income create/update/delete**: mirrors Expense, `type: receipt`,
  debit = payment head, credit = income head.
- **Student Fee payment** (fee created with `paid_amount` > 0, `paid_amount`
  increased on edit, or an offline payment request approved): compute
  `delta = new_paid_amount - old_paid_amount`; if `delta > 0`, record a
  receipt voucher for `delta` (debit = Cash/Bank resolved from
  `payment_method`, credit = fee's `account_head_id`). If a fee row or its
  paid amount is reduced/deleted, void the corresponding voucher(s)
  proportionally (simplification: void all vouchers for that
  `source_id` and, if any paid amount remains, re-record one voucher for
  the current total).

This logic is added to both `AdminController` and `AccountantController`
methods that currently touch expenses/fees, matching the codebase's
existing pattern of duplicating logic between those two controllers.

## New Feature: Income / Receipt Voucher

- New menu item "Income Manager" under Accounting, next to Expense
  Manager, for both Admin and Accountant.
- Same CRUD/list/filter/PDF-export UI shape as Expense Manager, backed by
  the new `incomes` table.
- New menu item "Account Heads" under Accounting (or Back Office) for
  managing the Chart of Accounts: list, create, edit, activate/deactivate,
  set opening balance. `is_system` heads can be renamed but not deleted.

## Reports

### Receipts & Payments Statement

- Filter: school (implicit from session), date range (From/To) — chosen
  over session-scoped filtering because opening/closing balance is a
  date-based accounting concept, matching the SK Associates reference
  report.
- **Transaction detail**: every `account_voucher_line` in range, joined to
  its voucher and head — columns: Date, Particulars, Account Head,
  Voucher Type, Ref No (voucher_no), Debit, Credit.
- **Summary by Account Head** (grouped by `type`): Opening Balance
  (= stored `account_heads.opening_balance` + net of all voucher lines
  dated before "From") / period Debit+Credit / Closing Balance.
- PDF export/print reusing the existing `html2pdf`/`window.print()`
  pattern already used by Expense Manager.

### Trial Balance

- Filter: as-of date.
- Lists every active Account Head with its running Dr/Cr balance
  (opening + all voucher lines up to that date).
- Footer totals both columns; they must be equal — a built-in sanity
  check that the ledger is internally consistent.

Both reports live under a new `AccountReportController` (or equivalent
methods on Admin/Accountant controllers, following existing convention),
accessible to Admin and Accountant.

## Roles & Routes

Access mirrors the existing Expense/Fee pattern: both `AdminController`
and `AccountantController` get the new methods, registered in both route
groups in `routes/web.php`. No new role/permission concept is introduced.

## Open Items Deferred to Future Work

- Ledger drill-down per Account Head.
- Income & Expenditure / Profit-Loss statement, Balance Sheet.
- Multiple named bank accounts beyond the two defaults.
- Formal reversal entries instead of delete-and-recreate on edit.
