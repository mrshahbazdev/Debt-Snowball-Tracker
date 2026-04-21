<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('messages.auth_ui.forgot_password_title') }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ __('messages.auth_ui.forgot_password_subtitle') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit"
                class="w-full inline-flex justify-center rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 text-sm font-semibold shadow transition">
            {{ __('Email Password Reset Link') }}
        </button>

        <p class="text-center text-sm text-slate-500">
            <a class="font-medium text-sky-600 hover:text-sky-700" href="{{ route('login') }}">← {{ __('messages.auth_ui.sign_in') }}</a>
        </p>
    </form>
</x-guest-layout>
