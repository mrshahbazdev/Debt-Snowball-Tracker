<?php

namespace App\Livewire\Dashboard;

use App\Services\SnowballService;
use App\Support\CurrentCompany;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class DashboardView extends Component
{
    public function render(SnowballService $snowball): View
    {
        $company = CurrentCompany::resolve(auth()->user());
        $kpis = $snowball->dashboardKpis($company);
        $ranked = $snowball->activeDebtsRanked($company);
        $cashflows = $company->cashflows()->orderBy('period')->get();
        $recentPayments = $company->payments()->with('debt')->orderByDesc('paid_on')->limit(5)->get();

        return view('livewire.dashboard.dashboard-view', [
            'kpis' => $kpis,
            'ranked' => $ranked,
            'cashflows' => $cashflows,
            'recentPayments' => $recentPayments,
            'currency' => $company->getOrCreateSetting()->currency,
        ]);
    }
}
