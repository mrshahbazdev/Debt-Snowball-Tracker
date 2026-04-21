<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Cashflow') }}</h2>
            <button wire:click="openCreate"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none">
                + Add Month
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($flash)
                <div class="p-3 rounded bg-emerald-50 text-emerald-700 text-sm">{{ $flash }}</div>
            @endif

            @if ($showForm)
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg mb-4">{{ $editingId ? 'Edit Cashflow' : 'Add Monthly Cashflow' }}</h3>
                    <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="period" value="Month" />
                            <x-text-input id="period" type="date" wire:model="period" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('period')" class="mt-2" />
                            <p class="text-xs text-gray-500 mt-1">Will be normalized to first day of the month.</p>
                        </div>
                        <div>
                            <x-input-label for="revenue" value="Revenue" />
                            <x-text-input id="revenue" type="number" step="0.01" min="0"
                                wire:model="revenue" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('revenue')" class="mt-2" />
                        </div>
                        <div class="md:col-span-3">
                            <x-input-label for="notes" value="Notes (optional)" />
                            <textarea id="notes" wire:model="notes" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <div class="md:col-span-3 flex justify-end gap-2">
                            <button type="button" wire:click="cancel"
                                class="px-4 py-2 bg-gray-200 rounded text-gray-800 text-sm">Cancel</button>
                            <x-primary-button>{{ $editingId ? 'Update' : 'Create' }}</x-primary-button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left">
                            <th class="px-4 py-2">Month</th>
                            <th class="px-4 py-2 text-right">Revenue</th>
                            <th class="px-4 py-2 text-right">Debt Allocation</th>
                            <th class="px-4 py-2 text-right">Available Cash</th>
                            <th class="px-4 py-2 text-right">Payments</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($cashflows as $cf)
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ $cf->period->format('M Y') }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format((float) $cf->revenue, 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format((float) $cf->debt_allocation, 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format((float) $cf->available_cash, 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ $cf->payments_count }}</td>
                                <td class="px-4 py-2 whitespace-nowrap space-x-1">
                                    <button wire:click="applySnowball({{ $cf->id }})"
                                        wire:confirm="Apply this month's allocation to the current snowball target?"
                                        class="text-indigo-600 hover:underline text-xs">Apply Snowball</button>
                                    <button wire:click="openEdit({{ $cf->id }})"
                                        class="text-gray-600 hover:underline text-xs">Edit</button>
                                    <button wire:click="delete({{ $cf->id }})"
                                        wire:confirm="Delete this cashflow entry?"
                                        class="text-red-600 hover:underline text-xs">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">No cashflow yet. Add a month to start.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3">{{ $cashflows->links() }}</div>
            </div>
        </div>
    </div>
</div>
