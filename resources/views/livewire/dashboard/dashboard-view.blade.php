<div>
    <x-slot name="header">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 tracking-tight">{{ __('messages.dashboard.title') }}</h2>
                <p class="text-sm text-slate-500 mt-0.5">{{ __('messages.dashboard.welcome', ['name' => auth()->user()->name]) }}</p>
            </div>
        </div>
    </x-slot>

    @php
        $fmt = fn ($v) => number_format((float) $v, 2);
        $kpiCards = [
            ['label' => __('messages.dashboard.kpi_active'), 'value' => $kpis['active_count'], 'color' => 'from-sky-500 to-cyan-400', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a4 4 0 00-8 0v2M5 9h14l-1 12H6L5 9z"/>'],
            ['label' => __('messages.dashboard.kpi_paid'), 'value' => $kpis['paid_count'], 'color' => 'from-emerald-500 to-teal-400', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>'],
            ['label' => __('messages.dashboard.kpi_outstanding'), 'value' => $currency . ' ' . $fmt($kpis['total_outstanding']), 'color' => 'from-amber-500 to-orange-400', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>'],
            ['label' => __('messages.dashboard.kpi_allocation'), 'value' => $currency . ' ' . $fmt($kpis['monthly_allocation']), 'color' => 'from-indigo-500 to-purple-400', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>'],
        ];
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- KPI cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($kpiCards as $c)
                    <div class="bg-white rounded-2xl ring-1 ring-slate-200 p-5 hover:shadow-sm transition">
                        <div class="flex items-center justify-between">
                            <div class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br {{ $c['color'] }} text-white">
                                <svg class="h-4.5 w-4.5 h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $c['icon'] !!}</svg>
                            </div>
                        </div>
                        <div class="mt-3 text-xs uppercase tracking-wider text-slate-500">{{ $c['label'] }}</div>
                        <div class="mt-1 text-2xl font-bold text-slate-900">{{ $c['value'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Target + Latest cashflow --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 bg-white rounded-2xl ring-1 ring-slate-200 p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-slate-900">{{ __('messages.dashboard.current_target') }}</h3>
                        @if (!$kpis['new_debt_allowed'])
                            <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-medium">{{ __('messages.settings.new_debt_allowed') }}: {{ __('messages.common.no') }}</span>
                        @else
                            <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-medium">{{ __('messages.settings.new_debt_allowed') }}: {{ __('messages.common.yes') }}</span>
                        @endif
                    </div>

                    @if ($kpis['current_target'])
                        @php $t = $kpis['current_target']; @endphp
                        <div class="mt-4">
                            <div class="flex items-baseline justify-between">
                                <div class="text-xl font-bold text-slate-900">{{ $t->creditor }}</div>
                                <div class="text-sm text-slate-500">{{ $t->progressPercent() }}% {{ __('messages.dashboard.progress') }}</div>
                            </div>
                            <div class="mt-1 text-sm text-slate-500">
                                {{ $currency }} {{ $fmt($t->current_balance) }} / {{ $fmt($t->original_balance) }}
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 rounded-full mt-3 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-sky-500 to-cyan-400 rounded-full" style="width: {{ $t->progressPercent() }}%"></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4 text-sm">
                                @if ($kpis['months_to_kill_target'])
                                    <div class="rounded-lg bg-slate-50 ring-1 ring-slate-100 p-3">
                                        <div class="text-xs text-slate-500">{{ __('messages.dashboard.months_to_target') }}</div>
                                        <div class="text-lg font-semibold text-slate-900">{{ $kpis['months_to_kill_target'] }}</div>
                                    </div>
                                @endif
                                @if ($kpis['months_to_kill_all'])
                                    <div class="rounded-lg bg-slate-50 ring-1 ring-slate-100 p-3">
                                        <div class="text-xs text-slate-500">{{ __('messages.dashboard.months_to_all') }}</div>
                                        <div class="text-lg font-semibold text-slate-900">{{ $kpis['months_to_kill_all'] }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="mt-4 rounded-lg bg-emerald-50 ring-1 ring-emerald-200 p-4 text-emerald-800 text-sm">
                            {{ __('messages.dashboard.no_target') }}
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-2xl ring-1 ring-slate-200 p-6">
                    <h3 class="font-semibold text-slate-900">{{ __('messages.dashboard.latest_cashflow') }}</h3>
                    @if ($kpis['latest_cashflow'])
                        @php $lc = $kpis['latest_cashflow']; @endphp
                        <dl class="mt-4 space-y-2 text-sm">
                            <div class="flex justify-between"><dt class="text-slate-500">{{ __('messages.cashflows.col_period') }}</dt><dd class="font-medium">{{ $lc->period->format('M Y') }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">{{ __('messages.cashflows.col_revenue') }}</dt><dd class="font-medium">{{ $currency }} {{ $fmt($lc->revenue) }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">{{ __('messages.cashflows.col_allocation') }}</dt><dd class="font-medium text-indigo-600">{{ $currency }} {{ $fmt($lc->debt_allocation) }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">{{ __('messages.cashflows.col_available') }}</dt><dd class="font-medium text-emerald-600">{{ $currency }} {{ $fmt($lc->available_cash) }}</dd></div>
                        </dl>
                    @else
                        <p class="mt-3 text-sm text-slate-500">—</p>
                    @endif
                </div>
            </div>

            {{-- Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl ring-1 ring-slate-200 p-6">
                    <h3 class="font-semibold text-slate-900 mb-4">{{ __('messages.dashboard.chart_balances') }}</h3>
                    <div class="h-64"><canvas id="debtsChart"></canvas></div>
                </div>
                <div class="bg-white rounded-2xl ring-1 ring-slate-200 p-6">
                    <h3 class="font-semibold text-slate-900 mb-4">{{ __('messages.dashboard.chart_cashflow') }}</h3>
                    <div class="h-64"><canvas id="cashflowChart"></canvas></div>
                </div>
            </div>

            {{-- Recent Payments --}}
            <div class="bg-white rounded-2xl ring-1 ring-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-900">{{ __('messages.dashboard.recent_payments') }}</h3>
                    <a href="{{ route('payments.index') }}" class="text-sm text-sky-600 hover:text-sky-700 font-medium">{{ __('messages.nav.payments') }} →</a>
                </div>
                @if ($recentPayments->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-slate-500">{{ __('messages.dashboard.no_payments') }}</div>
                @else
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs uppercase tracking-wider text-slate-500">
                                <th class="px-6 py-3">{{ __('messages.payments.col_date') }}</th>
                                <th class="px-6 py-3">{{ __('messages.payments.col_creditor') }}</th>
                                <th class="px-6 py-3 text-right">{{ __('messages.payments.col_amount') }}</th>
                                <th class="px-6 py-3 text-right">{{ __('messages.payments.col_after') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach ($recentPayments as $p)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-3 text-slate-700">{{ $p->paid_on?->format('Y-m-d') }}</td>
                                    <td class="px-6 py-3 font-medium">{{ $p->debt?->creditor ?? '—' }}</td>
                                    <td class="px-6 py-3 text-right text-indigo-600 font-semibold">{{ $currency }} {{ $fmt($p->amount) }}</td>
                                    <td class="px-6 py-3 text-right text-slate-600">{{ $currency }} {{ $fmt($p->balance_after) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
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

            const make = (id, cfg) => {
                const el = document.getElementById(id);
                if (!el) return;
                if (el._chart) el._chart.destroy();
                el._chart = new Chart(el, cfg);
            };

            make('debtsChart', {
                type: 'bar',
                data: {
                    labels: debtLabels,
                    datasets: [{
                        label: @json(__('messages.dashboard.chart_balances')),
                        data: debtBalances,
                        backgroundColor: 'rgba(14,165,233,0.8)',
                        borderRadius: 8,
                    }],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } },
            });

            make('cashflowChart', {
                type: 'line',
                data: {
                    labels: cfLabels,
                    datasets: [
                        { label: @json(__('messages.cashflows.col_revenue')), data: cfRevenue, borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.1)', tension: 0.3, fill: true },
                        { label: @json(__('messages.cashflows.col_allocation')), data: cfAlloc, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', tension: 0.3, fill: true },
                    ],
                },
                options: { responsive: true, maintainAspectRatio: false },
            });
        })();
    </script>
</div>
