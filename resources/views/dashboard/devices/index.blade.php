<x-layouts.dashboard title="Perangkat WA" subtitle="Kelola koneksi WhatsApp per unit layanan">
    @php
        $connectedCount = $devices->where('status', 'connected')->count();
    @endphp

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex size-12 items-center justify-center rounded-2xl bg-wa-light">
                <svg class="size-6 text-wa-dark" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-slate-500">{{ $connectedCount }} dari {{ $devices->count() }} perangkat terhubung</p>
                <p class="text-xs text-slate-400">Webhook: {{ config('whatsapp.webhook_url') }}</p>
            </div>
        </div>
        <button
            type="button"
            id="open-add-device"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-700"
        >
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Perangkat
        </button>
    </div>

    <div class="grid gap-5 sm:grid-cols-2" id="devices-grid">
        @forelse ($devices as $device)
            <div
                class="group rounded-2xl border border-border bg-surface-card p-6 shadow-card transition-all duration-200 hover:border-brand-200 hover:shadow-card-hover"
                data-device-card
                data-device-id="{{ $device->id }}"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="flex min-w-0 flex-1 items-center gap-4">
                        <div class="relative flex size-14 shrink-0 items-center justify-center rounded-2xl {{ $device->status === 'connected' ? 'bg-gradient-to-br from-wa to-wa-dark' : ($device->status === 'connecting' ? 'bg-amber-400' : 'bg-slate-200') }}">
                            <svg class="size-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                            </svg>
                            @if ($device->status === 'connected')
                                <span class="absolute -bottom-0.5 -right-0.5 size-4 rounded-full border-2 border-white bg-wa"></span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h3 class="truncate font-semibold text-slate-900">{{ $device->name }}</h3>
                            <p class="truncate text-sm text-slate-500">{{ $device->phone ?? 'Belum terhubung' }}</p>
                        </div>
                    </div>
                    <x-dashboard.badge class="shrink-0" :status="$device->status" data-device-status />
                </div>

                <div class="mt-4 space-y-1.5">
                    <div class="flex items-center gap-1.5 rounded-lg bg-slate-50 px-2 py-1.5">
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-medium uppercase tracking-wide text-slate-400">Session</p>
                            <p class="truncate font-mono text-xs text-slate-700" title="{{ $device->session }}">{{ $device->session }}</p>
                        </div>
                        <button
                            type="button"
                            class="shrink-0 rounded-md p-1.5 text-slate-400 transition-colors hover:bg-white hover:text-brand-600"
                            data-copy="{{ $device->session }}"
                            data-copy-label="Session"
                            title="Salin session"
                            aria-label="Salin session"
                        >
                            <svg class="size-3.5" data-copy-icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9.75a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                            </svg>
                            <svg class="hidden size-3.5 text-brand-600" data-copy-check fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center gap-1.5 rounded-lg bg-slate-50 px-2 py-1.5">
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-medium uppercase tracking-wide text-slate-400">Token</p>
                            @if ($device->token)
                                <p class="truncate font-mono text-xs text-slate-700" title="{{ $device->token }}">{{ $device->token }}</p>
                            @else
                                <p class="text-xs italic text-slate-400">Belum ada</p>
                            @endif
                        </div>
                        @if ($device->token)
                            <button
                                type="button"
                                class="shrink-0 rounded-md p-1.5 text-slate-400 transition-colors hover:bg-white hover:text-brand-600"
                                data-copy="{{ $device->token }}"
                                data-copy-label="Token"
                                title="Salin token"
                                aria-label="Salin token"
                            >
                                <svg class="size-3.5" data-copy-icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9.75a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                                </svg>
                                <svg class="hidden size-3.5 text-brand-600" data-copy-check fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Pesan Hari Ini</p>
                        <p class="mt-0.5 text-lg font-semibold text-slate-900" data-device-messages>{{ number_format($device->messagesTodayCount()) }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Terakhir Aktif</p>
                        <p class="mt-0.5 text-sm font-medium text-slate-900" data-device-last-seen>{{ $device->lastSeenLabel() }}</p>
                    </div>
                </div>

                <div class="mt-5 flex gap-2">
                    @if ($device->status === 'connected')
                        <button
                            type="button"
                            class="flex-1 rounded-xl border border-red-200 bg-red-50 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-100"
                            data-action="disconnect"
                            data-device-id="{{ $device->id }}"
                        >
                            Putuskan
                        </button>
                        <form method="POST" action="{{ route('dashboard.devices.destroy', $device) }}" class="flex-1" onsubmit="return confirm('Hapus perangkat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-xl border border-border bg-white py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50">
                                Hapus
                            </button>
                        </form>
                    @else
                        <button
                            type="button"
                            class="flex-1 rounded-xl bg-brand-600 py-2 text-sm font-medium text-white transition-colors hover:bg-brand-700"
                            data-action="connect"
                            data-device-id="{{ $device->id }}"
                            data-device-name="{{ $device->name }}"
                        >
                            Hubungkan via QR
                        </button>
                        <form method="POST" action="{{ route('dashboard.devices.destroy', $device) }}" class="flex-1" onsubmit="return confirm('Hapus perangkat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-xl border border-border bg-white py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50">
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-brand-200 bg-brand-50/40 p-10 text-center">
                <p class="font-medium text-brand-700">Belum ada perangkat WhatsApp</p>
                <p class="mt-1 text-sm text-brand-600">Tambahkan perangkat pertama untuk mulai menghubungkan WhatsApp.</p>
            </div>
        @endforelse

        <button
            type="button"
            id="open-add-device-card"
            class="flex min-h-[220px] flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-brand-200 bg-brand-50/50 p-6 text-brand-600 transition-all hover:border-brand-400 hover:bg-brand-50"
        >
            <div class="flex size-14 items-center justify-center rounded-2xl bg-brand-100">
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </div>
            <div class="text-center">
                <p class="font-semibold">Tambah Perangkat Baru</p>
                <p class="mt-1 text-sm text-brand-500">Scan QR code untuk menghubungkan WhatsApp</p>
            </div>
        </button>
    </div>

    {{-- Modal: Tambah Perangkat --}}
    <div id="add-device-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Tambah Perangkat</h3>
                <button type="button" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100" data-close-modal="add-device-modal">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('dashboard.devices.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Perangkat</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        placeholder="Front Office RS"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="session" class="mb-1.5 block text-sm font-medium text-slate-700">Session ID</label>
                    <input
                        id="session"
                        name="session"
                        type="text"
                        value="{{ old('session') }}"
                        required
                        placeholder="FRONT_OFFICE_RS"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm uppercase focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                    >
                    <p class="mt-1 text-xs text-slate-500">Huruf, angka, dan underscore saja. Contoh: NERDWHATS_AMERICA</p>
                    @error('session')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full rounded-xl bg-brand-600 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                    Simpan Perangkat
                </button>
            </form>
        </div>
    </div>

    {{-- Modal: QR Connect --}}
    <div id="qr-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900" id="qr-modal-title">Hubungkan WhatsApp</h3>
                    <p class="text-sm text-slate-500" id="qr-modal-subtitle">Scan QR code dengan aplikasi WhatsApp</p>
                </div>
                <button type="button" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100" data-close-modal="qr-modal">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex flex-col items-center">
                <div id="qr-loading" class="flex size-64 items-center justify-center rounded-2xl border border-border bg-slate-50">
                    <div class="text-center">
                        <div class="mx-auto mb-3 size-8 animate-spin rounded-full border-4 border-brand-200 border-t-brand-600"></div>
                        <p class="text-sm text-slate-500">Memuat QR code...</p>
                    </div>
                </div>
                <img id="qr-image" src="" alt="QR Code WhatsApp" class="hidden size-64 rounded-2xl border border-border bg-white object-contain p-2">
                <p id="qr-status" class="mt-4 text-sm font-medium text-slate-600">Menunggu QR code...</p>
                <p id="qr-error" class="mt-2 hidden text-sm text-red-600"></p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('add-device-modal');
                modal?.classList.remove('hidden');
                modal?.classList.add('flex');
            });
        </script>
    @endif
</x-layouts.dashboard>
