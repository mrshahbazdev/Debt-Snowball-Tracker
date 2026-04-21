@props(['variant' => 'dark'])
@php
    $current = app()->getLocale();
    $baseColor = $variant === 'light'
        ? 'text-white/80 hover:text-white'
        : 'text-slate-500 hover:text-slate-900';
    $activeColor = $variant === 'light'
        ? 'bg-white/15 text-white'
        : 'bg-slate-900 text-white';
    $ring = $variant === 'light' ? 'ring-white/20' : 'ring-slate-200';
@endphp
<div class="inline-flex items-center rounded-full ring-1 {{ $ring }} p-0.5 text-xs font-medium">
    <a href="{{ route('locale.switch', 'en') }}"
       class="px-2.5 py-1 rounded-full transition {{ $current === 'en' ? $activeColor : $baseColor }}"
       aria-label="English">EN</a>
    <a href="{{ route('locale.switch', 'de') }}"
       class="px-2.5 py-1 rounded-full transition {{ $current === 'de' ? $activeColor : $baseColor }}"
       aria-label="Deutsch">DE</a>
</div>
