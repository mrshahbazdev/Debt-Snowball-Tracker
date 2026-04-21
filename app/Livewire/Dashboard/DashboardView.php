<?php

namespace App\Livewire\Dashboard;

use App\Services\SnowballService;
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
        $user = auth()->user();
        $kpis = $snowball->dashboardKpis($user);
        $ranked = $snowball->activeDebtsRanked($user);
        $cashflows = $user->cashflows()->orderBy('period')->get();
        $recentPayments = $user->payments()->with('debt')->orderByDesc('paid_on')->limit(5)->get();

        return view('livewire.dashboard.dashboard-view', [
            'kpis' => $kpis,
            'ranked' => $ranked,
            'cashflows' => $cashflows,
            'recentPayments' => $recentPayments,
            'currency' => $user->getOrCreateSetting()->currency,
        ]);
    }
}
