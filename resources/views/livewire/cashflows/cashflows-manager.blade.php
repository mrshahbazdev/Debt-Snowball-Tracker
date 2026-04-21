<div>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-slate-900 tracking-tight">{{ __('messages.cashflows.title') }}</h2>
            <p class="text-sm text-slate-500 mt-0.5">{{ __('messages.cashflows.subtitle') }}</p>
        </div>
    </x-slot>

    @php $fmt = fn ($v) => number_format((float) $v, 2); @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-end">
                <button type="button" wire:click="openCreate"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 text-sm font-semibold shadow transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    {{ __('messages.cashflows.add') }}
                </button>
            </div>

            @if ($flash)
                <div class="rounded-xl bg-emerald-50 ring-1 ring-emerald-200 text-emerald-800 text-sm p-3 flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ $flash }}
                </div>
            @endif

            @if ($showForm)
                <div class="bg-white rounded-2xl ring-1 ring-slate-200 p-6">
                    <h3 class="font-semibold text-lg mb-4 text-slate-900">{{ $editingId ? __('messages.cashflows.edit') : __('messages.cashflows.add') }}</h3>
                    <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="period" :value="__('messages.cashflows.col_period')" />
                            <x-text-input id="period" type="date" wire:model="period" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('period')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="revenue" :value="__('messages.cashflows.col_revenue')" />
                            <x-text-input id="revenue" type="number" step="0.01" min="0" wire:model="revenue" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('revenue')" class="mt-2" />
                        </div>
                        <div class="md:col-span-3">
                            <x-input-label for="notes" :value="__('messages.cashflows.notes')" />
                            <textarea id="notes" wire:model="notes" rows="2" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"></textarea>
                        </div>
                        <div class="md:col-span-3 flex justify-end gap-2">
                            <button type="button" wire:click="cancel" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-medium transition">{{ __('messages.cashflows.cancel') }}</button>
                            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold shadow transition">{{ __('messages.cashflows.save') }}</button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="bg-white rounded-2xl ring-1 ring-slate-200 overflow-hidden">
                @if ($cashflows->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="mx-auto h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                            <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2"/></svg>
                        </div>
                        <p class="text-sm text-slate-500">{{ __('messages.cashflows.empty') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs uppercase tracking-wider text-slate-500">
                                <th class="px-4 py-3">{{ __('messages.cashflows.col_period') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('messages.cashflows.col_revenue') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('messages.cashflows.col_allocation') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('messages.cashflows.col_available') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('messages.cashflows.col_payments') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('messages.cashflows.col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($cashflows as $cf)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $cf->period->format('M Y') }}</td>
                                    <td class="px-4 py-3 text-right text-slate-700">{{ $fmt($cf->revenue) }}</td>
                                    <td class="px-4 py-3 text-right text-indigo-600 font-semibold">{{ $fmt($cf->debt_allocation) }}</td>
                                    <td class="px-4 py-3 text-right text-emerald-600 font-semibold">{{ $fmt($cf->available_cash) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">{{ $cf->payments_count }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex items-center gap-1">
                                            <button type="button"
                                                x-data
                                                @click="$dispatch('open-confirm', {
                                                    title: @js(__('messages.cashflows.apply')),
                                                    message: @js(__('messages.cashflows.confirm_apply')),
                                                    confirm: @js(__('messages.cashflows.apply')),
                                                    cancel: @js(__('messages.common.cancel')),
                                                    variant: 'primary',
                                                    action: () => $wire.applySnowball({{ $cf->id }}),
                                                })"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-sky-600 hover:bg-sky-700 text-white transition">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                {{ __('messages.cashflows.apply') }}
                                            </button>
                                            <button type="button" wire:click="openEdit({{ $cf->id }})" class="px-2 py-1 rounded-md text-xs font-medium text-slate-600 hover:bg-slate-100 transition">{{ __('messages.common.edit') }}</button>
                                            <button type="button"
                                                x-data
                                                @click="$dispatch('open-confirm', {
                                                    title: @js(__('messages.common.delete')),
                                                    message: @js(__('messages.cashflows.confirm_delete')),
                                                    confirm: @js(__('messages.common.delete')),
                                                    cancel: @js(__('messages.common.cancel')),
                                                    variant: 'danger',
                                                    action: () => $wire.delete({{ $cf->id }}),
                                                })"
                                                class="px-2 py-1 rounded-md text-xs font-medium text-rose-600 hover:bg-rose-50 transition">{{ __('messages.common.delete') }}</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div class="px-4 py-3 border-t border-slate-200">{{ $cashflows->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
