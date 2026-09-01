@props([
    'status',
])

@php
    $styles = [
        'connected' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'disconnected' => 'bg-red-50 text-red-700 ring-red-600/20',
        'delivered' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'read' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'sent' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'failed' => 'bg-red-50 text-red-700 ring-red-600/20',
        'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'connecting' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'received' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
    ];

    $labels = [
        'connected' => 'Terhubung',
        'disconnected' => 'Terputus',
        'delivered' => 'Terkirim',
        'read' => 'Dibaca',
        'sent' => 'Terkirim',
        'failed' => 'Gagal',
        'active' => 'Aktif',
        'draft' => 'Draft',
        'connecting' => 'Menghubungkan',
        'received' => 'Diterima',
    ];
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset ' . ($styles[$status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/20'),
]) }}>
    <span class="size-1.5 rounded-full {{ in_array($status, ['connected', 'delivered', 'read', 'active']) ? 'bg-current' : ($status === 'failed' || $status === 'disconnected' ? 'bg-current' : 'bg-current') }}"></span>
    {{ $labels[$status] ?? $status }}
</span>
