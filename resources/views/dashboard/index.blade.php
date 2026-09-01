<x-layouts.dashboard title="Dashboard" subtitle="Ringkasan aktivitas WhatsApp Gateway RS Roemani">
    {{-- Welcome banner --}}
    <div class="mb-6 overflow-hidden rounded-2xl gradient-brand p-6 text-white shadow-lg sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-brand-200">Selamat datang kembali 👋</p>
                <h2 class="mt-1 text-2xl font-bold sm:text-3xl">WhatsApp Gateway</h2>
                <p class="mt-2 max-w-lg text-sm text-brand-100">
                    Pantau pengiriman pesan notifikasi pasien, status perangkat, dan performa gateway secara real-time.
                </p>
            </div>
            <div class="flex shrink-0 items-center gap-3">
                <div class="rounded-xl bg-white/15 px-4 py-3 text-center backdrop-blur-sm">
                    <p class="text-2xl font-bold">3</p>
                    <p class="text-xs text-brand-200">Perangkat Aktif</p>
                </div>
                <div class="rounded-xl bg-wa/20 px-4 py-3 text-center backdrop-blur-sm">
                    <p class="text-2xl font-bold text-wa-light">98.7%</p>
                    <p class="text-xs text-brand-200">Delivery Rate</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats grid --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <x-dashboard.stat-card
                :label="$stat['label']"
                :value="$stat['value']"
                :change="$stat['change']"
                :trend="$stat['trend']"
                :icon="$stat['icon']"
                :color="$stat['color']"
            />
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Weekly chart --}}
        <div class="rounded-2xl border border-border bg-surface-card p-6 shadow-card lg:col-span-2">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Volume Pesan Mingguan</h3>
                    <p class="text-sm text-slate-500">Pesan terkirim vs gagal (7 hari terakhir)</p>
                </div>
                <span class="rounded-lg bg-brand-50 px-3 py-1 text-xs font-medium text-brand-700">Minggu ini</span>
            </div>

            @php
                $maxSent = max(array_column($weeklyMessages, 'sent'));
            @endphp

            <div class="flex items-end justify-between gap-2 sm:gap-4" style="height: 200px;">
                @foreach ($weeklyMessages as $day)
                    @php
                        $sentHeight = $maxSent > 0 ? ($day['sent'] / $maxSent) * 100 : 0;
                        $failedHeight = $maxSent > 0 ? ($day['failed'] / $maxSent) * 100 : 0;
                    @endphp
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <div class="flex w-full items-end justify-center gap-1" style="height: 160px;">
                            <div
                                class="w-3 rounded-t-md bg-brand-500 transition-all sm:w-5"
                                style="height: {{ $sentHeight }}%"
                                title="Terkirim: {{ $day['sent'] }}"
                            ></div>
                            <div
                                class="w-3 rounded-t-md bg-red-300 transition-all sm:w-5"
                                style="height: {{ max($failedHeight, 4) }}%"
                                title="Gagal: {{ $day['failed'] }}"
                            ></div>
                        </div>
                        <span class="text-xs font-medium text-slate-500">{{ $day['day'] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 flex items-center justify-center gap-6 text-xs text-slate-500">
                <span class="flex items-center gap-2">
                    <span class="size-3 rounded-sm bg-brand-500"></span> Terkirim
                </span>
                <span class="flex items-center gap-2">
                    <span class="size-3 rounded-sm bg-red-300"></span> Gagal
                </span>
            </div>
        </div>

        {{-- Device status --}}
        <div class="rounded-2xl border border-border bg-surface-card p-6 shadow-card">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">Status Perangkat</h3>
                <a href="{{ route('dashboard.devices') }}" class="text-xs font-medium text-brand-600 hover:text-brand-700">Lihat semua →</a>
            </div>

            <div class="space-y-3">
                @foreach ($devices as $device)
                    <div class="flex items-center gap-3 rounded-xl border border-border p-3 transition-colors hover:bg-slate-50">
                        <div class="flex size-10 items-center justify-center rounded-lg {{ $device->status === 'connected' ? 'bg-wa-light' : 'bg-red-50' }}">
                            <svg class="size-5 {{ $device->status === 'connected' ? 'text-wa-dark' : 'text-red-500' }}" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-900">{{ $device->name }}</p>
                            <p class="text-xs text-slate-500">{{ $device->messagesTodayCount() }} pesan hari ini</p>
                        </div>
                        <x-dashboard.badge :status="$device->status" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent messages table --}}
    <div class="mt-6 rounded-2xl border border-border bg-surface-card shadow-card">
        <div class="flex items-center justify-between border-b border-border px-6 py-4">
            <div>
                <h3 class="text-base font-semibold text-slate-900">Pesan Terbaru</h3>
                <p class="text-sm text-slate-500">Aktivitas pengiriman terakhir</p>
            </div>
            <a href="{{ route('dashboard.messages') }}" class="rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-700 hover:bg-brand-100">
                Lihat semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-border bg-slate-50/80 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-3">Penerima</th>
                        <th class="px-6 py-3">Template</th>
                        <th class="px-6 py-3">Perangkat</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($recentMessages as $message)
                        <tr class="transition-colors hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $message->displayName() }}</p>
                                    <p class="text-xs text-slate-500">{{ $message->displayPhone() }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $message->type }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $message->device?->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <x-dashboard.badge :status="$message->status ?? 'sent'" />
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $message->created_at->format('H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada pesan masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.dashboard>
