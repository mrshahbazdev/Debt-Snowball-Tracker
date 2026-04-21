@props(['class' => 'h-8 w-8'])
<svg {{ $attributes->merge(['class' => $class]) }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="16" cy="16" r="13" fill="currentColor" fill-opacity="0.08" />
    <circle cx="11" cy="19" r="4" />
    <circle cx="18" cy="14" r="6" />
    <path d="M16 8 L16 10 M16 8 L14.5 9 M16 8 L17.5 9" />
    <path d="M24 14 L22 14 M24 14 L23 12.5 M24 14 L23 15.5" />
</svg>
