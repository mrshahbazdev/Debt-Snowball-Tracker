<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Settings') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                @if ($savedMessage)
                    <div class="mb-4 p-3 rounded bg-emerald-50 text-emerald-700 text-sm" wire:key="flash-{{ now()->timestamp }}">
                        {{ $savedMessage }}
                    </div>
                @endif

                <form wire:submit="save" class="space-y-5">
                    <div>
                        <x-input-label for="monthly_revenue" value="Monthly Revenue" />
                        <x-text-input id="monthly_revenue" type="number" step="0.01" min="0"
                            wire:model="monthly_revenue" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('monthly_revenue')" class="mt-2" />
                        <p class="text-xs text-gray-500 mt-1">Assumed monthly income used to calculate debt allocation.</p>
                    </div>

                    <div>
                        <x-input-label for="debt_allocation_percent" value="Debt Allocation %" />
                        <x-text-input id="debt_allocation_percent" type="number" step="0.001" min="0" max="100"
                            wire:model="debt_allocation_percent" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('debt_allocation_percent')" class="mt-2" />
                        <p class="text-xs text-gray-500 mt-1">Percent of revenue applied to debts each month (e.g. 1 = 1%).</p>
                    </div>

                    <div>
                        <x-input-label for="minimum_cash_buffer" value="Minimum Cash Buffer" />
                        <x-text-input id="minimum_cash_buffer" type="number" step="0.01" min="0"
                            wire:model="minimum_cash_buffer" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('minimum_cash_buffer')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="currency" value="Currency Code" />
                        <x-text-input id="currency" type="text" maxlength="8"
                            wire:model="currency" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="new_debt_allowed" type="checkbox" wire:model="new_debt_allowed"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="new_debt_allowed" class="text-sm text-gray-700">New debt allowed?</label>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
