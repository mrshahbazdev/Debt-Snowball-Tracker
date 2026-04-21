<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-100 min-h-screen">

    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-[30rem] w-[30rem] rounded-full bg-sky-500/20 blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 h-[28rem] w-[28rem] rounded-full bg-indigo-500/20 blur-3xl"></div>
    </div>

    <div class="min-h-screen grid lg:grid-cols-2">
        {{-- Left: brand panel --}}
        <div class="hidden lg:flex flex-col justify-between p-10 bg-gradient-to-br from-sky-900/40 via-slate-900 to-indigo-900/40 border-r border-white/5">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 text-white">
                <x-application-logo class="h-9 w-9 text-sky-400" />
                <span class="font-semibold tracking-tight text-lg">{{ config('app.name') }}</span>
            </a>
            <div class="max-w-md">
                <h2 class="text-3xl font-bold text-white leading-tight">{{ __('messages.landing.hero_title') }}</h2>
                <p class="mt-4 text-slate-300">{{ __('messages.landing.hero_subtitle') }}</p>

                <ul class="mt-8 space-y-3">
                    @foreach ([
                        __('messages.landing.feature_snowball_title'),
                        __('messages.landing.feature_cashflow_title'),
                        __('messages.landing.feature_ledger_title'),
                        __('messages.landing.feature_i18n_title'),
                    ] as $bullet)
                        <li class="flex items-center gap-3 text-sm text-slate-200">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-sky-500/20 ring-1 ring-sky-400/40">
                                <svg class="h-3.5 w-3.5 text-sky-300" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            {{ $bullet }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="text-xs text-slate-500">© {{ date('Y') }} {{ config('app.name') }}</div>
        </div>

        {{-- Right: form card --}}
        <div class="flex flex-col">
            <header class="flex items-center justify-between px-6 lg:px-10 py-5">
                <a href="{{ route('landing') }}" class="lg:hidden flex items-center gap-2 text-white">
                    <x-application-logo class="h-7 w-7 text-sky-400" />
                    <span class="font-semibold text-sm">{{ config('app.name') }}</span>
                </a>
                <div class="ml-auto">
                    <x-language-switcher variant="light" />
                </div>
            </header>

            <main class="flex-1 flex items-center justify-center px-6 pb-12">
                <div class="w-full max-w-md">
                    <div class="bg-white text-slate-900 rounded-2xl shadow-2xl shadow-black/40 ring-1 ring-white/10 px-7 py-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
