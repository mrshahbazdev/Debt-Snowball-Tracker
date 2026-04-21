<?php

namespace Database\Seeders;

use App\Models\Cashflow;
use App\Models\Debt;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoSnowballSeeder extends Seeder
{
    /**
     * Seeds a demo user matching the data in the original Snowball +7 Excel file.
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

        Setting::updateOrCreate(
            ['user_id' => $user->id],
            [
                'monthly_revenue' => 50000,
                'debt_allocation_percent' => 1.000, // 1%
                'minimum_cash_buffer' => 500,
                'new_debt_allowed' => false,
                'currency' => 'PKR',
            ]
        );

        $debts = [
            ['creditor' => 'Sparkasse', 'original_balance' => 10000, 'current_balance' => 9000, 'minimum_payment' => 500],
            ['creditor' => 'Sölter', 'original_balance' => 50000, 'current_balance' => 45000, 'minimum_payment' => 500],
            ['creditor' => 'Test Loan', 'original_balance' => 400, 'current_balance' => 200, 'minimum_payment' => 10],
        ];

        foreach ($debts as $d) {
            Debt::updateOrCreate(
                ['user_id' => $user->id, 'creditor' => $d['creditor']],
                array_merge($d, ['status' => Debt::STATUS_ACTIVE])
            );
        }

        $cashflows = [
            ['period' => '2026-01-01', 'revenue' => 10000],
            ['period' => '2026-02-01', 'revenue' => 12000],
        ];

        foreach ($cashflows as $c) {
            $period = Carbon::parse($c['period']);
            $revenue = $c['revenue'];
            $alloc = round($revenue * 0.01, 2);
            Cashflow::updateOrCreate(
                ['user_id' => $user->id, 'period' => $period],
                [
                    'revenue' => $revenue,
                    'debt_allocation' => $alloc,
                    'available_cash' => $revenue - $alloc,
                ]
            );
        }
    }
}
