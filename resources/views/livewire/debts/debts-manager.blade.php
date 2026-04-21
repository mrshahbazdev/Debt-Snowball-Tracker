<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Debts') }}</h2>
            <button wire:click="openCreate"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none">
                + Add Debt
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($target)
                <div class="bg-indigo-50 border border-indigo-200 text-indigo-800 rounded-lg p-4 text-sm">
                    <span class="font-semibold">Current Snowball Target:</span>
                    {{ $target->creditor }} — balance {{ number_format((float) $target->current_balance, 2) }}
                </div>
            @else
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg p-4 text-sm">
                    No active debts. You're debt free!
                </div>
            @endif

            @if ($showForm)
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg mb-4">{{ $editingId ? 'Edit Debt' : 'Add Debt' }}</h3>
                    <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-input-label for="creditor" value="Creditor" />
                            <x-text-input id="creditor" wire:model="creditor" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('creditor')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="original_balance" value="Original Balance" />
                            <x-text-input id="original_balance" type="number" step="0.01" min="0"
                                wire:model="original_balance" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('original_balance')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="current_balance" value="Current Balance" />
                            <x-text-input id="current_balance" type="number" step="0.01" min="0"
                                wire:model="current_balance" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('current_balance')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="minimum_payment" value="Minimum Payment" />
                            <x-text-input id="minimum_payment" type="number" step="0.01" min="0"
                                wire:model="minimum_payment" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('minimum_payment')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="status" value="Status" />
                            <select id="status" wire:model="status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="ACTIVE">ACTIVE</option>
                                <option value="PAID">PAID</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="notes" value="Notes" />
                            <textarea id="notes" wire:model="notes" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <div class="md:col-span-2 flex justify-end gap-2">
                            <button type="button" wire:click="cancel"
                                class="px-4 py-2 bg-gray-200 rounded text-gray-800 text-sm">Cancel</button>
                            <x-primary-button>{{ $editingId ? 'Update' : 'Create' }}</x-primary-button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="px-4 py-3 border-b flex items-center gap-2">
                    <select wire:model.live="filter"
                        class="rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all">All</option>
                        <option value="active">Active only</option>
                        <option value="paid">Paid only</option>
                    </select>
                    <span class="text-sm text-gray-500">Showing {{ $debts->total() }} record(s)</span>
                </div>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left">
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">Creditor</th>
                            <th class="px-4 py-2 text-right">Original</th>
                            <th class="px-4 py-2 text-right">Current</th>
                            <th class="px-4 py-2 text-right">Min Pay</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2 text-right">Progress</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($debts as $debt)
                            <tr class="{{ $target && $debt->id === $target->id ? 'bg-indigo-50' : '' }}">
                                <td class="px-4 py-2 text-gray-500">{{ $debt->id }}</td>
                                <td class="px-4 py-2 font-medium">
                                    {{ $debt->creditor }}
                                    @if ($target && $debt->id === $target->id)
                                        <span class="ml-2 text-xs bg-indigo-600 text-white rounded px-2 py-0.5">TARGET</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right">{{ number_format((float) $debt->original_balance, 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format((float) $debt->current_balance, 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format((float) $debt->minimum_payment, 2) }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-0.5 text-xs rounded
                                        {{ $debt->status === 'ACTIVE' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $debt->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right">{{ $debt->progressPercent() }}%</td>
                                <td class="px-4 py-2 whitespace-nowrap space-x-1">
                                    <button wire:click="openEdit({{ $debt->id }})"
                                        class="text-indigo-600 hover:underline text-xs">Edit</button>
                                    <button wire:click="togglePaid({{ $debt->id }})"
                                        class="text-amber-600 hover:underline text-xs">
                                        {{ $debt->status === 'ACTIVE' ? 'Mark Paid' : 'Reopen' }}
                                    </button>
                                    <button wire:click="delete({{ $debt->id }})"
                                        wire:confirm="Delete this debt? All its payments will be removed."
                                        class="text-red-600 hover:underline text-xs">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">No debts yet. Click "Add Debt" above.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3">{{ $debts->links() }}</div>
            </div>
        </div>
    </div>
</div>
