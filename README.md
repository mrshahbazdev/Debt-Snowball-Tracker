# Debt Snowball Tracker

A Laravel web app that ports the **Snowball +7** Excel debt-payoff tracker to a multi-user web application. Users register, add their debts, record monthly revenue, and the app applies the **snowball method** — always paying down the smallest active debt first — automatically rolling over to the next debt as each one is cleared.

## Stack

- **Laravel 10** (PHP 8.1+)
- **Breeze (Blade)** — authentication: register, login, password reset, profile
- **Livewire 3** — reactive UI for all CRUD screens
- **Tailwind CSS** + Alpine.js — styling and small interactions
- **Chart.js** — dashboard charts (debt balances, cashflow)
- **SQLite** (default) — easy local dev; swap `DB_CONNECTION=mysql` for shared hosting

## Features

- Multi-user auth; each user's data is private
- **Settings**: monthly revenue, debt allocation %, cash buffer, new-debt-allowed flag, currency
- **Debts**: full CRUD with status (ACTIVE / PAID), original vs current balance, minimum payment, progress %
- **Snowball rank (automatic)**: smallest active balance = current target
- **Cashflow**: monthly revenue entry → automatic allocation (revenue × %) and available cash
- **Apply Snowball**: one click on a cashflow month automatically distributes allocation to the current target, rolls over to the next debt when the target hits zero, and marks cleared debts as PAID
- **Payments ledger**: full history with balance before/after, undo support (refunds the debt balance)
- **Dashboard**: KPI cards, current target with progress bar, estimated months-to-kill, debt balances chart, monthly cashflow chart, recent payments

## Quick start

```bash
git clone https://github.com/mrshahbazdev/Debt-Snowball-Tracker.git
cd Debt-Snowball-Tracker

composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed   # seeds a demo user + Excel sample data

npm run build
php artisan serve
```

Open <http://127.0.0.1:8000> and sign up, or log in with the seeded demo user:

- **Email**: `demo@snowball.test`
- **Password**: `password`

## Routes

| URL            | Purpose                                           |
| -------------- | ------------------------------------------------- |
| `/register`    | Create account (Breeze)                           |
| `/login`       | Sign in (Breeze)                                  |
| `/dashboard`   | KPIs, charts, current snowball target             |
| `/debts`       | Debt CRUD, filter Active/Paid, mark paid/reopen   |
| `/cashflow`    | Monthly revenue entries, "Apply Snowball" button  |
| `/payments`    | Full payment history, undo a payment              |
| `/settings`    | Monthly revenue, allocation %, buffer, currency   |

## Snowball logic

Lives in `app/Services/SnowballService.php`:

- `activeDebtsRanked(User)` — active debts ordered smallest balance first
- `currentTarget(User)` — snowball target (rank 1)
- `applyPaymentFromCashflow(Cashflow)` — applies remaining allocation to the target, creates a `Payment`, decrements debt balance, auto-marks PAID on zero
- `distributeCashflow(Cashflow)` — repeatedly applies payments until the monthly allocation is exhausted or there is no active debt left
- `estimatedMonthsToKillTarget(User)` — rough months for the current target
- `estimatedMonthsToKillAll(User)` — simulates the snowball across all active debts

## Data model (maps to the original Excel sheets)

| Excel sheet      | Laravel table  | Notes                                                                   |
| ---------------- | -------------- | ----------------------------------------------------------------------- |
| `SETUP`          | `settings`     | One row per user                                                        |
| `DEBTS`          | `debts`        | Per-user debts; `Snowball Rank` & `Is Target?` are computed, not stored |
| `CASHFLOW`       | `cashflows`    | Monthly revenue + auto allocation / available cash                       |
| `DEBT_ACCOUNT`   | `payments`     | Payment ledger; `balance_before` / `balance_after` captured per payment |
| `SNOWBALL_VIEW`  | *(dashboard)*  | Computed live in `DashboardView` / `SnowballService`                    |
| `DASHBOARD`      | *(dashboard)*  | Computed live in `DashboardView` / `SnowballService`                    |
