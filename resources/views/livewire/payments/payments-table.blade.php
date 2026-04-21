<div>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-slate-900 tracking-tight">{{ __('messages.payments.title') }}</h2>
            <p class="text-sm text-slate-500 mt-0.5">{{ __('messages.payments.subtitle') }}</p>
        </div>
    </x-slot>

    @php $fmt = fn ($v) => number_format((float) $v, 2); @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white rounded-2xl ring-1 ring-slate-200 p-4">
                <div class="relative max-w-md">
                    <svg class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('messages.payments.search') }}"
                        class="pl-9 w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                </div>
            </div>

            <div class="bg-white rounded-2xl ring-1 ring-slate-200 overflow-hidden">
                @if ($payments->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="mx-auto h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                            <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <p class="text-sm text-slate-500">{{ __('messages.payments.empty') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs uppercase tracking-wider text-slate-500">
                                <th class="px-4 py-3">{{ __('messages.payments.col_date') }}</th>
                                <th class="px-4 py-3">{{ __('messages.payments.col_creditor') }}</th>
                                <th class="px-4 py-3">{{ __('messages.payments.col_cashflow') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('messages.payments.col_amount') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('messages.payments.col_before') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('messages.payments.col_after') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('messages.payments.col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($payments as $p)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-slate-700">{{ $p->paid_on?->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $p->debt?->creditor ?? '—' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $p->cashflow?->period?->format('M Y') ?? '—' }}</td>
                                    <td class="px-4 py-3 text-right text-indigo-600 font-semibold">{{ $fmt($p->amount) }}</td>
                                    <td class="px-4 py-3 text-right text-slate-600">{{ $fmt($p->balance_before) }}</td>
                                    <td class="px-4 py-3 text-right text-slate-900 font-medium">{{ $fmt($p->balance_after) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <button type="button"
                                            x-data
                                            @click="$dispatch('open-confirm', {
                                                title: @js(__('messages.payments.undo')),
                                                message: @js(__('messages.payments.confirm_undo')),
                                                confirm: @js(__('messages.payments.undo')),
                                                cancel: @js(__('messages.common.cancel')),
                                                variant: 'danger',
                                                action: () => $wire.delete({{ $p->id }}),
                                            })"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium text-rose-600 hover:bg-rose-50 transition">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                            {{ __('messages.payments.undo') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div class="px-4 py-3 border-t border-slate-200">{{ $payments->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
