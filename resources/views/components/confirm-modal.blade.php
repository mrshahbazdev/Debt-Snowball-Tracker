{{--
    Global confirm modal. Listens for `open-confirm` on window.
    Dispatch:
        $dispatch('open-confirm', {
            message: 'Delete this item?',
            title: 'Delete debt',
            confirm: 'Delete',
            cancel: 'Cancel',
            variant: 'danger', // danger | primary
            action: () => $wire.delete(123),
        })
--}}
<div
    x-data="{
        open: false,
        title: '',
        message: '',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        variant: 'danger',
        action: null,
        show(detail = {}) {
            this.title = detail.title || '';
            this.message = detail.message || 'Are you sure?';
            this.confirmText = detail.confirm || 'Confirm';
            this.cancelText = detail.cancel || 'Cancel';
            this.variant = detail.variant || 'danger';
            this.action = detail.action || null;
            this.open = true;
            this.$nextTick(() => this.$refs.confirmBtn && this.$refs.confirmBtn.focus());
        },
        accept() {
            const a = this.action;
            this.open = false;
            if (typeof a === 'function') a();
        },
        cancel() {
            this.open = false;
            this.action = null;
        },
    }"
    @open-confirm.window="show($event.detail || {})"
    @keydown.escape.window="open && cancel()"
>
    <template x-teleport="body">
        <div
            x-show="open"
            x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            style="display: none;"
        >
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="cancel()"></div>
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white rounded-2xl ring-1 ring-slate-200 shadow-2xl max-w-md w-full p-6"
                role="dialog"
                aria-modal="true"
            >
                <div class="flex items-start gap-4">
                    <div
                        :class="variant === 'danger' ? 'bg-rose-100 text-rose-600' : 'bg-sky-100 text-sky-600'"
                        class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center"
                    >
                        <svg x-show="variant === 'danger'" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        <svg x-show="variant !== 'danger'" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 x-show="title" class="text-base font-semibold text-slate-900" x-text="title"></h3>
                        <p class="text-sm text-slate-600 mt-1" x-text="message"></p>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-2">
                    <button
                        type="button"
                        @click="cancel()"
                        class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-medium transition"
                        x-text="cancelText"
                    ></button>
                    <button
                        type="button"
                        x-ref="confirmBtn"
                        @click="accept()"
                        :class="variant === 'danger'
                            ? 'bg-rose-600 hover:bg-rose-700 text-white'
                            : 'bg-sky-600 hover:bg-sky-700 text-white'"
                        class="px-4 py-2 rounded-lg text-sm font-semibold shadow transition"
                        x-text="confirmText"
                    ></button>
                </div>
            </div>
        </div>
    </template>
</div>
