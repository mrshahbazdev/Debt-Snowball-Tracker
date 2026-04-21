<?php

namespace App\Services;

use App\Models\Cashflow;
use App\Models\Debt;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SnowballService
{
    /**
     * Returns active debts ordered by smallest current balance first (snowball rank 1 = smallest).
     *
     * @return Collection<int, Debt>
     */
    public function activeDebtsRanked(User $user): Collection
    {
        return $user->debts()
            ->where('status', Debt::STATUS_ACTIVE)
            ->orderBy('current_balance', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * The current target debt (smallest active balance). Null if none active.
     */
    public function currentTarget(User $user): ?Debt
    {
        return $this->activeDebtsRanked($user)->first();
    }

    /**
     * Monthly debt allocation = monthly_revenue * allocation_percent (based on user settings).
     */
    public function monthlyAllocation(User $user): float
    {
        $setting = $user->getOrCreateSetting();
        return $setting->monthlyAllocation();
    }

    /**
     * Rough estimate: how many months until current target is paid off at the monthly allocation rate.
     */
    public function estimatedMonthsToKillTarget(User $user): ?int
    {
        $target = $this->currentTarget($user);
        if (!$target) {
            return null;
        }
        $alloc = $this->monthlyAllocation($user);
        if ($alloc <= 0) {
            return null;
        }
        return (int) ceil((float) $target->current_balance / $alloc);
    }

    /**
     * Estimate months to eliminate ALL active debts assuming:
     *   - Every month the full monthly allocation goes to the current smallest active debt.
     *   - When one is paid off, allocation rolls to the next smallest.
     * Returns null if allocation <= 0 or no active debts.
     */
    public function estimatedMonthsToKillAll(User $user): ?int
    {
        $alloc = $this->monthlyAllocation($user);
        if ($alloc <= 0) {
            return null;
        }
        $queue = $this->activeDebtsRanked($user)
            ->map(fn (Debt $d) => (float) $d->current_balance)
            ->filter(fn ($b) => $b > 0)
            ->values();

        if ($queue->isEmpty()) {
            return 0;
        }

        $months = 0;
        $budget = 0.0;
        $safety = 0;
        while ($queue->isNotEmpty() && $safety < 100000) {
            $safety++;
            $budget += $alloc;
            $months++;
            while ($queue->isNotEmpty() && $budget >= $queue->first()) {
                $budget -= $queue->shift();
            }
        }
        return $months;
    }

    /**
     * Applies a snowball payment from a given cashflow to the current target debt.
     * Creates a Payment row and decrements the debt's current_balance.
     * Marks debt as PAID if balance reaches 0.
     *
     * $overrideAmount lets the caller supply a custom amount; otherwise the cashflow's
     * remaining allocation is used (capped at target's current balance).
     *
     * Returns the Payment, or null if no target / nothing to pay.
     */
    public function applyPaymentFromCashflow(Cashflow $cashflow, ?float $overrideAmount = null, ?Carbon $paidOn = null): ?Payment
    {
        /** @var User $user */
        $user = $cashflow->user;
        $target = $this->currentTarget($user);
        if (!$target) {
            return null;
        }

        $remainingAllocation = $cashflow->remainingAllocation();
        $requested = $overrideAmount !== null ? max(0.0, (float) $overrideAmount) : $remainingAllocation;
        $amount = min($requested, (float) $target->current_balance);
        if ($amount <= 0) {
            return null;
        }

        return DB::transaction(function () use ($cashflow, $user, $target, $amount, $paidOn) {
            $before = (float) $target->current_balance;
            $after = round($before - $amount, 2);

            $target->current_balance = $after;
            if ($after <= 0) {
                $target->current_balance = 0;
                $target->status = Debt::STATUS_PAID;
                $target->paid_at = now();
            }
            $target->save();

            return Payment::create([
                'user_id' => $user->id,
                'cashflow_id' => $cashflow->id,
                'debt_id' => $target->id,
                'paid_on' => $paidOn ?: ($cashflow->period ?: now()->toDateString()),
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after < 0 ? 0 : $after,
            ]);
        });
    }

    /**
     * Apply the full monthly allocation from a cashflow, rolling over to next debts as they get paid off.
     * Creates multiple Payment rows if necessary.
     *
     * @return Payment[]
     */
    public function distributeCashflow(Cashflow $cashflow): array
    {
        $payments = [];
        $safety = 0;
        while ($cashflow->fresh()->remainingAllocation() > 0 && $safety < 100) {
            $safety++;
            $payment = $this->applyPaymentFromCashflow($cashflow->fresh());
            if (!$payment) {
                break;
            }
            $payments[] = $payment;
        }
        return $payments;
    }

    /**
     * Dashboard KPIs for a user.
     *
     * @return array<string, mixed>
     */
    public function dashboardKpis(User $user): array
    {
        $active = $user->debts()->where('status', Debt::STATUS_ACTIVE)->count();
        $paid = $user->debts()->where('status', Debt::STATUS_PAID)->count();
        $totalOutstanding = (float) $user->debts()->where('status', Debt::STATUS_ACTIVE)->sum('current_balance');
        $totalOriginal = (float) $user->debts()->sum('original_balance');
        $totalPaid = (float) $user->payments()->sum('amount');
        $latestCashflow = $user->cashflows()->orderByDesc('period')->first();
        $target = $this->currentTarget($user);

        return [
            'active_count' => $active,
            'paid_count' => $paid,
            'total_outstanding' => $totalOutstanding,
            'total_original' => $totalOriginal,
            'total_paid' => $totalPaid,
            'latest_cashflow' => $latestCashflow,
            'available_cash' => $latestCashflow ? (float) $latestCashflow->available_cash : 0.0,
            'current_target' => $target,
            'monthly_allocation' => $this->monthlyAllocation($user),
            'months_to_kill_target' => $this->estimatedMonthsToKillTarget($user),
            'months_to_kill_all' => $this->estimatedMonthsToKillAll($user),
            'new_debt_allowed' => (bool) $user->getOrCreateSetting()->new_debt_allowed,
        ];
    }
}
