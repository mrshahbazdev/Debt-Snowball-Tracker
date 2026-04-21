<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — {{ __('messages.app.tagline') }}</title>
    <meta name="description" content="{{ __('messages.landing.hero_subtitle') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-100 overflow-x-hidden">

    {{-- Background glow --}}
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-[32rem] w-[32rem] rounded-full bg-sky-500/20 blur-3xl"></div>
        <div class="absolute top-1/3 -right-40 h-[28rem] w-[28rem] rounded-full bg-indigo-500/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/4 h-[24rem] w-[24rem] rounded-full bg-cyan-400/10 blur-3xl"></div>
    </div>

    {{-- Header --}}
    <header class="relative z-10">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-5 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 text-white">
                <x-application-logo class="h-9 w-9 text-sky-400" />
                <span class="font-semibold tracking-tight text-lg">{{ config('app.name') }}</span>
            </a>

            <nav class="flex items-center gap-3">
                <x-language-switcher variant="light" />
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-white text-slate-900 px-4 py-1.5 text-sm font-semibold hover:bg-slate-100 transition">
                        {{ __('messages.nav.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline text-sm text-white/80 hover:text-white px-3 py-1.5">
                        {{ __('messages.nav.login') }}
                    </a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-1.5 rounded-full bg-white text-slate-900 px-4 py-1.5 text-sm font-semibold hover:bg-slate-100 transition">
                        {{ __('messages.nav.register') }}
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Hero --}}
    <section class="relative z-10">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 pt-14 pb-20 lg:pt-24 lg:pb-28 text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-white/5 ring-1 ring-white/10 px-3 py-1 text-xs font-medium text-sky-300">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                {{ __('messages.landing.hero_eyebrow') }}
            </span>
            <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                {{ __('messages.landing.hero_title') }}
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-lg text-slate-300">
                {{ __('messages.landing.hero_subtitle') }}
            </p>
            <div class="mt-9 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 rounded-full bg-sky-500 hover:bg-sky-400 text-slate-950 px-6 py-3 text-sm font-semibold shadow-lg shadow-sky-500/20 transition">
                    {{ __('messages.landing.hero_cta_primary') }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 rounded-full bg-white/5 ring-1 ring-white/15 hover:bg-white/10 text-white px-6 py-3 text-sm font-semibold transition">
                    {{ __('messages.landing.hero_cta_secondary') }}
                </a>
            </div>

            {{-- Hero mini-preview --}}
            <div class="mt-16 mx-auto max-w-4xl rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur p-5 shadow-2xl shadow-sky-500/10">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach ([
                        ['label' => __('messages.dashboard.kpi_active'), 'value' => '3', 'color' => 'text-sky-300'],
                        ['label' => __('messages.dashboard.kpi_paid'), 'value' => '0', 'color' => 'text-emerald-300'],
                        ['label' => __('messages.dashboard.kpi_outstanding'), 'value' => 'PKR 54,200', 'color' => 'text-amber-300'],
                        ['label' => __('messages.dashboard.kpi_allocation'), 'value' => 'PKR 500', 'color' => 'text-indigo-300'],
                    ] as $k)
                    <div class="rounded-xl bg-slate-900/60 ring-1 ring-white/10 p-4 text-left">
                        <div class="text-[11px] uppercase tracking-wider text-slate-400">{{ $k['label'] }}</div>
                        <div class="mt-1.5 text-xl font-bold {{ $k['color'] }}">{{ $k['value'] }}</div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 rounded-xl bg-slate-900/60 ring-1 ring-white/10 p-4 text-left">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-semibold text-white">{{ __('messages.dashboard.current_target') }}</div>
                        <div class="text-xs text-slate-400">Test Loan · PKR 200 / 400</div>
                    </div>
                    <div class="mt-3 h-2 rounded-full bg-slate-800 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-sky-400 to-cyan-300" style="width: 50%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="relative z-10 bg-slate-950/40 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-20">
            <div class="max-w-2xl">
                <h2 class="text-3xl sm:text-4xl font-bold text-white tracking-tight">{{ __('messages.landing.features_title') }}</h2>
                <p class="mt-3 text-slate-300">{{ __('messages.landing.features_subtitle') }}</p>
            </div>

            @php
                $features = [
                    ['title' => __('messages.landing.feature_snowball_title'), 'body' => __('messages.landing.feature_snowball_body'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>', 'color' => 'from-sky-500 to-cyan-400'],
                    ['title' => __('messages.landing.feature_cashflow_title'), 'body' => __('messages.landing.feature_cashflow_body'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>', 'color' => 'from-emerald-500 to-teal-400'],
                    ['title' => __('messages.landing.feature_ledger_title'), 'body' => __('messages.landing.feature_ledger_body'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>', 'color' => 'from-amber-500 to-orange-400'],
                    ['title' => __('messages.landing.feature_dashboard_title'), 'body' => __('messages.landing.feature_dashboard_body'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6m6 6V5m-9 14h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>', 'color' => 'from-indigo-500 to-purple-400'],
                    ['title' => __('messages.landing.feature_multiuser_title'), 'body' => __('messages.landing.feature_multiuser_body'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 11c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.33 0-10 1.67-10 5v3h20v-3c0-3.33-6.67-5-10-5z"/>', 'color' => 'from-pink-500 to-rose-400'],
                    ['title' => __('messages.landing.feature_i18n_title'), 'body' => __('messages.landing.feature_i18n_body'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>', 'color' => 'from-sky-400 to-blue-500'],
                ];
            @endphp

            <div class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($features as $f)
                    <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-6 hover:bg-white/[0.07] transition">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br {{ $f['color'] }} text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $f['icon'] !!}</svg>
                        </div>
                        <h3 class="mt-4 text-base font-semibold text-white">{{ $f['title'] }}</h3>
                        <p class="mt-2 text-sm text-slate-300 leading-relaxed">{{ $f['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="relative z-10 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-20">
            <div class="text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-white tracking-tight">{{ __('messages.landing.how_title') }}</h2>
            </div>
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ([
                    ['t' => __('messages.landing.how_step1_title'), 'b' => __('messages.landing.how_step1_body')],
                    ['t' => __('messages.landing.how_step2_title'), 'b' => __('messages.landing.how_step2_body')],
                    ['t' => __('messages.landing.how_step3_title'), 'b' => __('messages.landing.how_step3_body')],
                ] as $s)
                    <div class="rounded-2xl bg-gradient-to-br from-white/10 to-white/[0.02] ring-1 ring-white/10 p-6">
                        <h3 class="text-lg font-semibold text-white">{{ $s['t'] }}</h3>
                        <p class="mt-2 text-sm text-slate-300 leading-relaxed">{{ $s['b'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="relative z-10 border-t border-white/5">
        <div class="max-w-4xl mx-auto px-6 lg:px-8 py-20 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-white tracking-tight">{{ __('messages.landing.cta_title') }}</h2>
            <p class="mt-3 text-slate-300">{{ __('messages.landing.cta_subtitle') }}</p>
            <div class="mt-8">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 rounded-full bg-sky-500 hover:bg-sky-400 text-slate-950 px-7 py-3 text-sm font-semibold shadow-lg shadow-sky-500/20 transition">
                    {{ __('messages.landing.hero_cta_primary') }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="relative z-10 border-t border-white/5 bg-slate-950/70">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-10 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2.5 text-white/80">
                <x-application-logo class="h-7 w-7 text-sky-400" />
                <span class="text-sm">{{ __('messages.landing.footer_tagline') }}</span>
            </div>
            <div class="text-xs text-slate-500">© {{ date('Y') }} {{ config('app.name') }}. {{ __('messages.landing.footer_builtwith') }}</div>
        </div>
    </footer>

</body>
</html>
