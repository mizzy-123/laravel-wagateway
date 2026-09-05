<x-layouts.dashboard title="Riwayat Blast" subtitle="Pantau progress blast, lihat nomor gagal, dan kirim ulang">
    @if (session('success'))
        <div class="mb-6 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-slate-500">
            Setiap blast menyimpan Job ID dari WPPConnect untuk cek progress dan retry nomor yang gagal.
        </p>
        <a href="{{ route('dashboard.send') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-700">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0121.485 12 59.768 59.768 0 013.27 20.875L5.999 12zm0 0h7.5" />
            </svg>
            Buat Blast Baru
        </a>
    </div>

    <div class="rounded-2xl border border-border bg-surface-card shadow-card">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-border bg-slate-50/80 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Perangkat</th>
                        <th class="px-6 py-3">Pesan</th>
                        <th class="px-6 py-3">Progress</th>
                        <th class="px-6 py-3">Gagal</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($campaigns as $campaign)
                        <tr class="transition-colors hover:bg-slate-50/50">
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                                {{ $campaign->created_at?->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-900">{{ $campaign->device?->name ?? '-' }}</p>
                                <p class="font-mono text-xs text-slate-400">{{ $campaign->device?->session }}</p>
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                <p class="truncate text-slate-700" title="{{ $campaign->message }}">{{ $campaign->messagePreview() }}</p>
                                <p class="mt-0.5 font-mono text-[11px] text-slate-400" title="{{ $campaign->job_id }}">{{ Str::limit($campaign->job_id, 18) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="w-36">
                                    <div class="mb-1 flex justify-between text-xs text-slate-500">
                                        <span>{{ $campaign->sent }}/{{ $campaign->total }}</span>
                                        <span>{{ $campaign->progressPercent() }}%</span>
                                    </div>
                                    <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-brand-500" style="width: {{ $campaign->progressPercent() }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($campaign->failed > 0)
                                    <span class="inline-flex rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/20">
                                        {{ $campaign->failed }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <x-dashboard.badge :status="$campaign->status" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('dashboard.blasts.show', $campaign) }}" class="rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-700 hover:bg-brand-100">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                Belum ada riwayat blast. Kirim blast dari halaman Kirim Pesan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($campaigns->hasPages())
            <div class="border-t border-border px-6 py-4">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</x-layouts.dashboard>
