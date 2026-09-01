@props([
    'label',
    'value',
    'change' => null,
    'trend' => 'neutral',
    'icon' => 'message',
    'color' => 'brand',
])

@php
    $colorClasses = [
        'brand' => 'bg-brand-50 text-brand-600',
        'wa' => 'bg-wa-light text-wa-dark',
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'amber' => 'bg-amber-50 text-amber-600',
    ];

    $iconPaths = [
        'message' => 'M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z',
        'device' => 'M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3',
        'check' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'queue' => 'M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z',
    ];

    $trendColors = [
        'up' => 'text-emerald-600',
        'down' => 'text-amber-600',
        'neutral' => 'text-slate-500',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-border bg-surface-card p-5 shadow-card transition-shadow duration-200 hover:shadow-card-hover']) }}>
    <div class="flex items-start justify-between">
        <div class="flex size-11 items-center justify-center rounded-xl {{ $colorClasses[$color] ?? $colorClasses['brand'] }}">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPaths[$icon] ?? $iconPaths['message'] }}" />
            </svg>
        </div>
        @if ($change)
            <span class="text-xs font-medium {{ $trendColors[$trend] ?? $trendColors['neutral'] }}">
                {{ $change }}
            </span>
        @endif
    </div>
    <div class="mt-4">
        <p class="text-2xl font-semibold tracking-tight text-slate-900">{{ $value }}</p>
        <p class="mt-1 text-sm text-slate-500">{{ $label }}</p>
    </div>
</div>
