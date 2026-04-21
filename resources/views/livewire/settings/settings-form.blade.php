<div>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-slate-900 tracking-tight">{{ __('messages.settings.title') }}</h2>
            <p class="text-sm text-slate-500 mt-0.5">{{ __('messages.settings.subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl ring-1 ring-slate-200 p-8">
                @if ($savedMessage)
                    <div class="mb-5 rounded-xl bg-emerald-50 ring-1 ring-emerald-200 text-emerald-800 text-sm p-3 flex items-center gap-2" wire:key="flash-{{ now()->timestamp }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ __('messages.settings.saved') }}
                    </div>
                @endif

                <form wire:submit="save" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <x-input-label for="monthly_revenue" :value="__('messages.settings.monthly_revenue')" />
                            <x-text-input id="monthly_revenue" type="number" step="0.01" min="0" wire:model="monthly_revenue" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('monthly_revenue')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="debt_allocation_percent" :value="__('messages.settings.allocation_percent')" />
                            <x-text-input id="debt_allocation_percent" type="number" step="0.001" min="0" max="100" wire:model="debt_allocation_percent" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('debt_allocation_percent')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="minimum_cash_buffer" :value="__('messages.settings.cash_buffer')" />
                            <x-text-input id="minimum_cash_buffer" type="number" step="0.01" min="0" wire:model="minimum_cash_buffer" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('minimum_cash_buffer')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="currency" :value="__('messages.settings.currency')" />
                            <x-text-input id="currency" type="text" maxlength="8" wire:model="currency" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                        </div>
                    </div>

                    <label class="flex items-center gap-3 rounded-xl bg-slate-50 ring-1 ring-slate-200 p-3 cursor-pointer hover:bg-slate-100 transition">
                        <input id="new_debt_allowed" type="checkbox" wire:model="new_debt_allowed" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                        <span class="text-sm font-medium text-slate-800">{{ __('messages.settings.new_debt_allowed') }}</span>
                    </label>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 text-sm font-semibold shadow transition">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ __('messages.settings.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
