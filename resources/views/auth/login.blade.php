<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('messages.auth_ui.welcome_back') }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ __('messages.auth_ui.login_subtitle') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-sky-600 hover:text-sky-700" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <button type="submit"
                class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 text-sm font-semibold shadow transition">
            {{ __('messages.auth_ui.sign_in') }}
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </button>

        <p class="text-center text-sm text-slate-500">
            {{ __('messages.auth_ui.no_account') }}
            <a class="font-medium text-sky-600 hover:text-sky-700" href="{{ route('register') }}">{{ __('messages.auth_ui.sign_up') }}</a>
        </p>
    </form>
</x-guest-layout>
