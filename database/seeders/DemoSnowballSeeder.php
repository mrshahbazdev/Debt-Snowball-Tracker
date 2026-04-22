<?php

namespace Database\Seeders;

use App\Models\Cashflow;
use App\Models\Company;
use App\Models\Debt;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoSnowballSeeder extends Seeder
{
    /**
     * Seeds a demo user with two companies so the multi-company
     * switcher has something meaningful out of the box.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'demo@snowball.test'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->seedCompany($user, 'Acme GmbH', 'Manufacturing', [
            ['creditor' => 'Sparkasse', 'original_balance' => 10000, 'current_balance' => 9000, 'minimum_payment' => 500],
            ['creditor' => 'Sölter',    'original_balance' => 50000, 'current_balance' => 45000, 'minimum_payment' => 500],
            ['creditor' => 'Test Loan', 'original_balance' => 400,   'current_balance' => 200,   'minimum_payment' => 10],
        ], [
            ['period' => '2026-01-01', 'revenue' => 50000],
            ['period' => '2026-02-01', 'revenue' => 52000],
        ]);

        $this->seedCompany($user, 'Beta Consulting', 'Services', [
            ['creditor' => 'Postbank',   'original_balance' => 5000,  'current_balance' => 3200, 'minimum_payment' => 200],
            ['creditor' => 'Commerzbank','original_balance' => 20000, 'current_balance' => 18500,'minimum_payment' => 600],
        ], [
            ['period' => '2026-01-01', 'revenue' => 18000],
            ['period' => '2026-02-01', 'revenue' => 20000],
        ]);
    }

    private function seedCompany(User $user, string $name, string $industry, array $debts, array $cashflows): void
    {
        $company = Company::updateOrCreate(
            ['user_id' => $user->id, 'name' => $name],
            ['industry' => $industry],
        );

        Setting::updateOrCreate(
            ['company_id' => $company->id],
            [
                'user_id'                 => $user->id,
                'monthly_revenue'         => $cashflows[0]['revenue'] ?? 0,
                'debt_allocation_percent' => 1.000,
                'minimum_cash_buffer'     => 500,
                'new_debt_allowed'        => false,
                'currency'                => 'EUR',
            ]
        );

        foreach ($debts as $d) {
            Debt::updateOrCreate(
                ['company_id' => $company->id, 'creditor' => $d['creditor']],
                array_merge($d, [
                    'user_id' => $user->id,
                    'status'  => Debt::STATUS_ACTIVE,
                ])
            );
        }

        foreach ($cashflows as $c) {
            $period  = Carbon::parse($c['period']);
            $revenue = $c['revenue'];
            $alloc   = round($revenue * 0.01, 2);
            Cashflow::updateOrCreate(
                ['company_id' => $company->id, 'period' => $period],
                [
                    'user_id'         => $user->id,
                    'revenue'         => $revenue,
                    'debt_allocation' => $alloc,
                    'available_cash'  => $revenue - $alloc,
                ]
            );
        }
    }
}
