<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Payments') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow sm:rounded-lg p-4">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by creditor..."
                    class="w-full md:w-80 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left">
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Creditor</th>
                            <th class="px-4 py-2">Cashflow Month</th>
                            <th class="px-4 py-2 text-right">Amount</th>
                            <th class="px-4 py-2 text-right">Balance Before</th>
                            <th class="px-4 py-2 text-right">Balance After</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($payments as $p)
                            <tr>
                                <td class="px-4 py-2">{{ $p->paid_on?->format('Y-m-d') }}</td>
                                <td class="px-4 py-2 font-medium">{{ $p->debt?->creditor ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $p->cashflow?->period?->format('M Y') ?? '—' }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format((float) $p->amount, 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format((float) $p->balance_before, 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format((float) $p->balance_after, 2) }}</td>
                                <td class="px-4 py-2">
                                    <button wire:click="delete({{ $p->id }})"
                                        wire:confirm="Undo this payment? The debt balance will be restored."
                                        class="text-red-600 hover:underline text-xs">Undo</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">No payments recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3">{{ $payments->links() }}</div>
            </div>
        </div>
    </div>
</div>
