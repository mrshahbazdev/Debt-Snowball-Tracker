<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ __('messages.companies.title') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('messages.companies.subtitle') }}</p>
        </div>
        <button wire:click="openCreate"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-sky-500 to-indigo-500 text-white text-sm font-semibold shadow hover:shadow-md transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('messages.companies.add') }}
        </button>
    </div>

    @if ($flash)
        <div class="rounded-lg bg-emerald-50 ring-1 ring-emerald-100 text-emerald-700 px-4 py-2 text-sm">{{ $flash }}</div>
    @endif

    @if ($showForm)
        <div class="rounded-xl bg-white ring-1 ring-slate-200 p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">
                {{ $editingId ? __('messages.companies.edit') : __('messages.companies.add') }}
            </h2>
            <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.companies.name') }}</label>
                    <input type="text" wire:model="name" class="w-full rounded-lg border-slate-300 focus:border-sky-500 focus:ring-sky-500 text-sm">
                    @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.companies.industry') }}</label>
                    <input type="text" wire:model="industry" class="w-full rounded-lg border-slate-300 focus:border-sky-500 focus:ring-sky-500 text-sm">
                    @error('industry') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.companies.notes') }}</label>
                    <textarea wire:model="notes" rows="2" class="w-full rounded-lg border-slate-300 focus:border-sky-500 focus:ring-sky-500 text-sm"></textarea>
                </div>
                <div class="md:col-span-2 flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold">{{ __('messages.common.save') }}</button>
                    <button type="button" wire:click="cancel" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm">{{ __('messages.common.cancel') }}</button>
                </div>
            </form>
        </div>
    @endif

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-left">{{ __('messages.companies.name') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('messages.companies.industry') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('messages.nav.debts') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('messages.nav.cashflow') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('messages.nav.payments') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('messages.common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($companies as $c)
                    <tr class="{{ $c->id === $currentId ? 'bg-sky-50/50' : '' }}">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900 flex items-center gap-2">
                                {{ $c->name }}
                                @if ($c->id === $currentId)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-100 text-sky-700">{{ __('messages.companies.current') }}</span>
                                @endif
                            </div>
                            @if ($c->notes)<div class="text-xs text-slate-500 mt-0.5">{{ $c->notes }}</div>@endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $c->industry ?: '—' }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ $c->debts_count }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ $c->cashflows_count }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ $c->payments_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-1">
                                @if ($c->id !== $currentId)
                                    <button wire:click="switch({{ $c->id }})" class="px-2 py-1 rounded-md text-xs font-medium text-sky-700 hover:bg-sky-50 transition">{{ __('messages.companies.switch') }}</button>
                                @endif
                                <button wire:click="openEdit({{ $c->id }})" class="px-2 py-1 rounded-md text-xs font-medium text-slate-600 hover:bg-slate-100 transition">{{ __('messages.common.edit') }}</button>
                                <button
                                    x-data
                                    @click="$dispatch('open-confirm', {
                                        title: @js(__('messages.companies.delete_title')),
                                        message: @js(__('messages.companies.delete_message', ['name' => $c->name])),
                                        confirm: @js(__('messages.common.delete')),
                                        cancel: @js(__('messages.common.cancel')),
                                        action: () => $wire.delete({{ $c->id }}),
                                    })"
                                    class="px-2 py-1 rounded-md text-xs font-medium text-rose-600 hover:bg-rose-50 transition">
                                    {{ __('messages.common.delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">{{ __('messages.companies.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
