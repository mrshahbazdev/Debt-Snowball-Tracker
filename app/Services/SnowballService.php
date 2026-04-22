<?php

namespace App\Services;

use App\Models\Cashflow;
use App\Models\Company;
use App\Models\Debt;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SnowballService
{
    /**
     * Returns active debts ordered by smallest current balance first.
     *
     * @return Collection<int, Debt>
     */
    public function activeDebtsRanked(Company $company): Collection
    {
        return $company->debts()
            ->where('status', Debt::STATUS_ACTIVE)
            ->orderBy('current_balance', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public function currentTarget(Company $company): ?Debt
    {
        return $this->activeDebtsRanked($company)->first();
    }

    public function monthlyAllocation(Company $company): float
    {
        return $company->getOrCreateSetting()->monthlyAllocation();
    }

    public function estimatedMonthsToKillTarget(Company $company): ?int
    {
        $target = $this->currentTarget($company);
        if (!$target) {
            return null;
        }
        $alloc = $this->monthlyAllocation($company);
        if ($alloc <= 0) {
            return null;
        }
        return (int) ceil((float) $target->current_balance / $alloc);
    }

    public function estimatedMonthsToKillAll(Company $company): ?int
    {
        $alloc = $this->monthlyAllocation($company);
        if ($alloc <= 0) {
            return null;
        }
        $queue = $this->activeDebtsRanked($company)
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

    public function applyPaymentFromCashflow(Cashflow $cashflow, ?float $overrideAmount = null, ?Carbon $paidOn = null): ?Payment
    {
        $company = $cashflow->company;
        $target = $this->currentTarget($company);
        if (!$target) {
            return null;
        }

        $remainingAllocation = $cashflow->remainingAllocation();
        $requested = $overrideAmount !== null ? max(0.0, (float) $overrideAmount) : $remainingAllocation;
        $amount = min($requested, (float) $target->current_balance);
        if ($amount <= 0) {
            return null;
        }

        return DB::transaction(function () use ($cashflow, $company, $target, $amount, $paidOn) {
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
                'user_id'        => $company->user_id,
                'company_id'     => $company->id,
                'cashflow_id'    => $cashflow->id,
                'debt_id'        => $target->id,
                'paid_on'        => $paidOn ?: ($cashflow->period ?: now()->toDateString()),
                'amount'         => $amount,
                'balance_before' => $before,
                'balance_after'  => $after < 0 ? 0 : $after,
            ]);
        });
    }

    /**
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
     * @return array<string, mixed>
     */
    public function dashboardKpis(Company $company): array
    {
        $active = $company->debts()->where('status', Debt::STATUS_ACTIVE)->count();
        $paid = $company->debts()->where('status', Debt::STATUS_PAID)->count();
        $totalOutstanding = (float) $company->debts()->where('status', Debt::STATUS_ACTIVE)->sum('current_balance');
        $totalOriginal = (float) $company->debts()->sum('original_balance');
        $totalPaid = (float) $company->payments()->sum('amount');
        $latestCashflow = $company->cashflows()->orderByDesc('period')->first();
        $target = $this->currentTarget($company);

        return [
            'active_count'          => $active,
            'paid_count'            => $paid,
            'total_outstanding'     => $totalOutstanding,
            'total_original'        => $totalOriginal,
            'total_paid'            => $totalPaid,
            'latest_cashflow'       => $latestCashflow,
            'available_cash'        => $latestCashflow ? (float) $latestCashflow->available_cash : 0.0,
            'current_target'        => $target,
            'monthly_allocation'    => $this->monthlyAllocation($company),
            'months_to_kill_target' => $this->estimatedMonthsToKillTarget($company),
            'months_to_kill_all'    => $this->estimatedMonthsToKillAll($company),
            'new_debt_allowed'      => (bool) $company->getOrCreateSetting()->new_debt_allowed,
        ];
    }
}
