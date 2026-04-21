<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 tracking-tight">{{ __('messages.debts.title') }}</h2>
                <p class="text-sm text-slate-500 mt-0.5">{{ __('messages.debts.subtitle') }}</p>
            </div>
            <button wire:click="openCreate"
                class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 text-sm font-semibold shadow transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                {{ __('messages.debts.add') }}
            </button>
        </div>
    </x-slot>

    @php
        $fmt = fn ($v) => number_format((float) $v, 2);
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if ($target)
                <div class="rounded-2xl bg-sky-50 ring-1 ring-sky-200 p-4 flex items-center gap-3 text-sm">
                    <div class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-sky-500 text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-sky-700 uppercase tracking-wider">{{ __('messages.debts.current_target_label') }}</div>
                        <div class="text-slate-900 font-semibold">{{ $target->creditor }} — {{ $fmt($target->current_balance) }}</div>
                    </div>
                </div>
            @else
                <div class="rounded-2xl bg-emerald-50 ring-1 ring-emerald-200 p-4 text-emerald-800 text-sm">
                    {{ __('messages.dashboard.no_target') }}
                </div>
            @endif

            @if ($showForm)
                <div class="bg-white rounded-2xl ring-1 ring-slate-200 p-6">
                    <h3 class="font-semibold text-lg mb-4 text-slate-900">{{ $editingId ? __('messages.debts.edit') : __('messages.debts.add') }}</h3>
                    <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-input-label for="creditor" :value="__('messages.debts.col_creditor')" />
                            <x-text-input id="creditor" wire:model="creditor" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('creditor')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="original_balance" :value="__('messages.debts.col_original')" />
                            <x-text-input id="original_balance" type="number" step="0.01" min="0" wire:model="original_balance" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('original_balance')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="current_balance" :value="__('messages.debts.col_current')" />
                            <x-text-input id="current_balance" type="number" step="0.01" min="0" wire:model="current_balance" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('current_balance')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="minimum_payment" :value="__('messages.debts.col_min')" />
                            <x-text-input id="minimum_payment" type="number" step="0.01" min="0" wire:model="minimum_payment" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('minimum_payment')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="status" :value="__('messages.debts.col_status')" />
                            <select id="status" wire:model="status" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                <option value="ACTIVE">{{ __('messages.debts.status_active') }}</option>
                                <option value="PAID">{{ __('messages.debts.status_paid') }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="notes" :value="__('messages.debts.notes')" />
                            <textarea id="notes" wire:model="notes" rows="2" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"></textarea>
                        </div>
                        <div class="md:col-span-2 flex justify-end gap-2">
                            <button type="button" wire:click="cancel" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-medium transition">{{ __('messages.debts.cancel') }}</button>
                            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold shadow transition">{{ __('messages.debts.save') }}</button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="bg-white rounded-2xl ring-1 ring-slate-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center gap-2">
                    <select wire:model.live="filter" class="rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500">
                        <option value="all">{{ __('messages.debts.filter_all') }}</option>
                        <option value="active">{{ __('messages.debts.filter_active') }}</option>
                        <option value="paid">{{ __('messages.debts.filter_paid') }}</option>
                    </select>
                </div>
                @if ($debts->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="mx-auto h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                            <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a4 4 0 00-8 0v2M5 9h14l-1 12H6L5 9z"/></svg>
                        </div>
                        <p class="text-sm text-slate-500">{{ __('messages.debts.empty') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs uppercase tracking-wider text-slate-500">
                                <th class="px-4 py-3">{{ __('messages.debts.col_creditor') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('messages.debts.col_original') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('messages.debts.col_current') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('messages.debts.col_min') }}</th>
                                <th class="px-4 py-3">{{ __('messages.debts.col_status') }}</th>
                                <th class="px-4 py-3">{{ __('messages.debts.col_progress') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('messages.debts.col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach ($debts as $debt)
                                @php $isTarget = $target && $target->id === $debt->id; @endphp
                                <tr class="hover:bg-slate-50 {{ $isTarget ? 'bg-sky-50/50' : '' }}">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            @if ($isTarget)
                                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-sky-500 text-white">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                </span>
                                            @endif
                                            <span class="font-semibold text-slate-900">{{ $debt->creditor }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right text-slate-600">{{ $fmt($debt->original_balance) }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-slate-900">{{ $fmt($debt->current_balance) }}</td>
                                    <td class="px-4 py-3 text-right text-slate-600">{{ $fmt($debt->minimum_payment) }}</td>
                                    <td class="px-4 py-3">
                                        @if ($debt->status === 'ACTIVE')
                                            <span class="inline-flex items-center rounded-full bg-sky-100 px-2 py-0.5 text-xs font-medium text-sky-700">{{ __('messages.debts.status_active') }}</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">{{ __('messages.debts.status_paid') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-20 h-2 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-sky-500 to-cyan-400" style="width: {{ $debt->progressPercent() }}%"></div>
                                            </div>
                                            <span class="text-xs text-slate-500">{{ $debt->progressPercent() }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex items-center gap-1">
                                            <button wire:click="openEdit({{ $debt->id }})" class="px-2 py-1 rounded-md text-xs font-medium text-slate-600 hover:bg-slate-100 transition">{{ __('messages.common.edit') }}</button>
                                            <button wire:click="togglePaid({{ $debt->id }})" class="px-2 py-1 rounded-md text-xs font-medium text-sky-600 hover:bg-sky-50 transition">
                                                {{ $debt->status === 'ACTIVE' ? __('messages.debts.mark_paid') : __('messages.debts.reopen') }}
                                            </button>
                                            <button wire:click="delete({{ $debt->id }})" wire:confirm="{{ __('messages.debts.confirm_delete') }}" class="px-2 py-1 rounded-md text-xs font-medium text-rose-600 hover:bg-rose-50 transition">{{ __('messages.debts.delete') }}</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div class="px-4 py-3 border-t border-slate-200">
                        {{ $debts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
