<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('messages.auth_ui.create_account') }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ __('messages.auth_ui.register_subtitle') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit"
                class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 text-sm font-semibold shadow transition">
            {{ __('messages.auth_ui.sign_up') }}
        </button>

        <p class="text-center text-sm text-slate-500">
            {{ __('messages.auth_ui.has_account') }}
            <a class="font-medium text-sky-600 hover:text-sky-700" href="{{ route('login') }}">{{ __('messages.auth_ui.sign_in') }}</a>
        </p>
    </form>
</x-guest-layout>
