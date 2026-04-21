<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php
                $fmt = fn ($v) => number_format((float) $v, 2);
            @endphp

            {{-- KPI cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Active Debts</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $kpis['active_count'] }}</div>
                </div>
                <div class="bg-white shadow rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Debts Eliminated</div>
                    <div class="mt-1 text-2xl font-semibold text-emerald-700">{{ $kpis['paid_count'] }}</div>
                </div>
                <div class="bg-white shadow rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Total Outstanding</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $currency }} {{ $fmt($kpis['total_outstanding']) }}</div>
                </div>
                <div class="bg-white shadow rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Monthly Allocation</div>
                    <div class="mt-1 text-2xl font-semibold text-indigo-700">{{ $currency }} {{ $fmt($kpis['monthly_allocation']) }}</div>
                </div>
            </div>

            {{-- Target + Meta --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 bg-white shadow rounded-lg p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800">Current Snowball Target</h3>
                        @if (!$kpis['new_debt_allowed'])
                            <span class="text-xs px-2 py-0.5 rounded bg-amber-100 text-amber-800">New debt not allowed</span>
                        @else
                            <span class="text-xs px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">New debt allowed</span>
                        @endif
                    </div>
                    @if ($kpis['current_target'])
                        @php $t = $kpis['current_target']; @endphp
                        <div class="mt-3">
                            <div class="text-lg font-semibold text-indigo-700">{{ $t->creditor }}</div>
                            <div class="text-sm text-gray-500">
                                Remaining: {{ $currency }} {{ $fmt($t->current_balance) }} / {{ $fmt($t->original_balance) }}
                                ({{ $t->progressPercent() }}% paid)
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded mt-2 overflow-hidden">
                                <div class="h-full bg-indigo-600" style="width: {{ $t->progressPercent() }}%"></div>
                            </div>
                            @if ($kpis['months_to_kill_target'])
                                <div class="text-sm text-gray-600 mt-3">
                                    Estimated months to kill this target: <span class="font-semibold">{{ $kpis['months_to_kill_target'] }}</span>
                                </div>
                            @endif
                            @if ($kpis['months_to_kill_all'])
                                <div class="text-sm text-gray-600">
                                    Estimated months to kill ALL debts: <span class="font-semibold">{{ $kpis['months_to_kill_all'] }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="mt-3 text-emerald-700">No active debts — you're debt free!</div>
                    @endif
                </div>

                <div class="bg-white shadow rounded-lg p-5">
                    <h3 class="font-semibold text-gray-800">Latest Cashflow</h3>
                    @if ($kpis['latest_cashflow'])
                        @php $lc = $kpis['latest_cashflow']; @endphp
                        <div class="mt-3 text-sm space-y-1">
                            <div><span class="text-gray-500">Month:</span> {{ $lc->period->format('M Y') }}</div>
                            <div><span class="text-gray-500">Revenue:</span> {{ $currency }} {{ $fmt($lc->revenue) }}</div>
                            <div><span class="text-gray-500">Debt Allocation:</span> {{ $currency }} {{ $fmt($lc->debt_allocation) }}</div>
                            <div><span class="text-gray-500">Available Cash:</span> {{ $currency }} {{ $fmt($lc->available_cash) }}</div>
                        </div>
                    @else
                        <div class="mt-3 text-gray-500 text-sm">No cashflow records yet.</div>
                    @endif
                </div>
            </div>

            {{-- Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white shadow rounded-lg p-5">
                    <h3 class="font-semibold text-gray-800 mb-2">Active Debts by Balance</h3>
                    <canvas id="chart-debts" height="220"></canvas>
                </div>
                <div class="bg-white shadow rounded-lg p-5">
                    <h3 class="font-semibold text-gray-800 mb-2">Monthly Cashflow</h3>
                    <canvas id="chart-cashflow" height="220"></canvas>
                </div>
            </div>

            {{-- Recent payments --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b font-semibold text-gray-800">Recent Payments</div>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left">
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Creditor</th>
                            <th class="px-4 py-2 text-right">Amount</th>
                            <th class="px-4 py-2 text-right">Balance After</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($recentPayments as $p)
                            <tr>
                                <td class="px-4 py-2">{{ $p->paid_on?->format('Y-m-d') }}</td>
                                <td class="px-4 py-2">{{ $p->debt?->creditor ?? '—' }}</td>
                                <td class="px-4 py-2 text-right">{{ $fmt($p->amount) }}</td>
                                <td class="px-4 py-2 text-right">{{ $fmt($p->balance_after) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">No payments yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const debtLabels = @json($ranked->pluck('creditor'));
            const debtBalances = @json($ranked->map(fn ($d) => (float) $d->current_balance));
            const cfLabels = @json($cashflows->map(fn ($c) => $c->period->format('M Y')));
            const cfRevenue = @json($cashflows->map(fn ($c) => (float) $c->revenue));
            const cfAlloc = @json($cashflows->map(fn ($c) => (float) $c->debt_allocation));

            function init() {
                const el1 = document.getElementById('chart-debts');
                if (el1 && debtLabels.length) {
                    new Chart(el1, {
                        type: 'bar',
                        data: {
                            labels: debtLabels,
                            datasets: [{ label: 'Current Balance', data: debtBalances, backgroundColor: '#6366f1' }]
                        },
                        options: { responsive: true, plugins: { legend: { display: false } } }
                    });
                }
                const el2 = document.getElementById('chart-cashflow');
                if (el2 && cfLabels.length) {
                    new Chart(el2, {
                        type: 'line',
                        data: {
                            labels: cfLabels,
                            datasets: [
                                { label: 'Revenue', data: cfRevenue, borderColor: '#0ea5e9', backgroundColor: 'rgba(14,165,233,0.2)', tension: 0.3 },
                                { label: 'Debt Allocation', data: cfAlloc, borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.2)', tension: 0.3 }
                            ]
                        },
                        options: { responsive: true }
                    });
                }
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
</div>
