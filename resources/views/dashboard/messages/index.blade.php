<x-layouts.dashboard title="Pesan" subtitle="Riwayat dan status pengiriman pesan WhatsApp">
    <div class="rounded-2xl border border-border bg-surface-card shadow-card">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-border bg-slate-50/80 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Kontak</th>
                        <th class="px-6 py-3">Pesan</th>
                        <th class="px-6 py-3">Perangkat</th>
                        <th class="px-6 py-3">Arah</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($messages as $message)
                        <tr class="transition-colors hover:bg-slate-50/50">
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">#{{ $message->id }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-9 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-700">
                                        {{ strtoupper(substr($message->displayName(), 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $message->displayName() }}</p>
                                        <p class="text-xs text-slate-500">{{ $message->displayPhone() }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="max-w-xs truncate px-6 py-4 text-slate-600">{{ $message->body ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $message->device?->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                    {{ $message->direction === 'inbound' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <x-dashboard.badge :status="$message->status ?? 'sent'" />
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $message->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-500">
                                Belum ada pesan. Pesan masuk dari webhook akan muncul di sini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-border px-6 py-4">
            <p class="text-sm text-slate-500">Menampilkan {{ $messages->count() }} pesan terbaru</p>
        </div>
    </div>
</x-layouts.dashboard>
