<x-layouts.dashboard title="Detail Blast" subtitle="Job ID: {{ $blast->job_id }}">
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

    @if ($syncError)
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Gagal sync ke WPPConnect: {{ $syncError }}
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('dashboard.blasts') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-600 hover:text-brand-700">
            ← Kembali ke riwayat blast
        </a>
        <div class="flex flex-wrap gap-2">
            <form method="POST" action="{{ route('dashboard.blasts.refresh', $blast) }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-border bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Refresh Status
                </button>
            </form>
            @if ($blast->failed > 0 || count($failedItems) > 0)
                <form method="POST" action="{{ route('dashboard.blasts.retry-failed', $blast) }}" onsubmit="return confirm('Retry semua nomor yang gagal?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
                        Retry Semua Gagal
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-border bg-surface-card p-5 shadow-card">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Total</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($blast->total) }}</p>
        </div>
        <div class="rounded-2xl border border-border bg-surface-card p-5 shadow-card">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Terkirim</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-600">{{ number_format($blast->sent) }}</p>
        </div>
        <div class="rounded-2xl border border-border bg-surface-card p-5 shadow-card">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Antrian</p>
            <p class="mt-2 text-2xl font-semibold text-amber-600">{{ number_format($blast->queued) }}</p>
        </div>
        <div class="rounded-2xl border border-border bg-surface-card p-5 shadow-card">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Gagal</p>
            <p class="mt-2 text-2xl font-semibold text-red-600">{{ number_format(max($blast->failed, count($failedItems))) }}</p>
        </div>
    </div>

    <div class="mb-6 grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-border bg-surface-card p-6 shadow-card lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">Informasi Campaign</h3>
                <x-dashboard.badge :status="$blast->status" />
            </div>
            <dl class="space-y-3 text-sm">
                <div class="flex flex-col gap-1 sm:flex-row sm:justify-between">
                    <dt class="text-slate-500">Job ID</dt>
                    <dd class="font-mono text-xs text-slate-800 break-all">{{ $blast->job_id }}</dd>
                </div>
                <div class="flex flex-col gap-1 sm:flex-row sm:justify-between">
                    <dt class="text-slate-500">Perangkat</dt>
                    <dd class="font-medium text-slate-800">{{ $blast->device?->name ?? '-' }} ({{ $blast->device?->session }})</dd>
                </div>
                <div class="flex flex-col gap-1 sm:flex-row sm:justify-between">
                    <dt class="text-slate-500">Dibuat</dt>
                    <dd class="text-slate-800">{{ $blast->created_at?->format('d M Y H:i:s') }}</dd>
                </div>
                <div class="flex flex-col gap-1 sm:flex-row sm:justify-between">
                    <dt class="text-slate-500">Progress</dt>
                    <dd class="text-slate-800">{{ $blast->progressPercent() }}% ({{ $blast->sent + $blast->failed + $blast->cancelled }}/{{ $blast->total }})</dd>
                </div>
            </dl>
            <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-brand-500 transition-all" style="width: {{ $blast->progressPercent() }}%"></div>
            </div>
        </div>

        <div class="rounded-2xl border border-border bg-surface-card p-6 shadow-card">
            <h3 class="mb-3 text-base font-semibold text-slate-900">Isi Pesan</h3>
            <div class="max-h-48 overflow-y-auto whitespace-pre-wrap rounded-xl bg-slate-50 p-4 text-sm text-slate-700">{{ $blast->message }}</div>
        </div>
    </div>

    <div class="rounded-2xl border border-border bg-surface-card shadow-card">
        <div class="flex flex-col gap-3 border-b border-border px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-900">Pesan Gagal</h3>
                <p class="text-sm text-slate-500">Daftar nomor yang gagal dikirim dari WPPConnect</p>
            </div>
            @if (count($failedItems) > 0)
                <form id="retry-selected-form" method="POST" action="{{ route('dashboard.blasts.retry-failed', $blast) }}">
                    @csrf
                    <div id="retry-selected-indexes"></div>
                    <button
                        type="submit"
                        id="retry-selected-btn"
                        disabled
                        class="rounded-xl bg-wa px-4 py-2 text-sm font-medium text-white opacity-50 transition-opacity disabled:cursor-not-allowed"
                    >
                        Retry Terpilih (<span id="retry-selected-count">0</span>)
                    </button>
                </form>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-border bg-slate-50/80 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-3 w-10">
                            @if (count($failedItems) > 0)
                                <input type="checkbox" id="select-all-failed" class="size-4 rounded border-border text-brand-600 focus:ring-brand-500/20">
                            @endif
                        </th>
                        <th class="px-6 py-3">Index</th>
                        <th class="px-6 py-3">Nomor</th>
                        <th class="px-6 py-3">Error</th>
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($failedItems as $item)
                        @php
                            $index = (int) ($item['index'] ?? 0);
                            $phone = (string) ($item['phone'] ?? '-');
                            $error = (string) ($item['error'] ?? '-');
                            $updatedAt = $item['updatedAt'] ?? null;
                        @endphp
                        <tr class="transition-colors hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <input
                                    type="checkbox"
                                    class="failed-index-checkbox size-4 rounded border-border text-brand-600 focus:ring-brand-500/20"
                                    value="{{ $index }}"
                                >
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $index }}</td>
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $phone }}</td>
                            <td class="px-6 py-4 text-slate-600 max-w-md">
                                <p class="break-words">{{ $error }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                                {{ $updatedAt ? \Illuminate\Support\Carbon::parse($updatedAt)->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('dashboard.blasts.retry-failed', $blast) }}">
                                    @csrf
                                    <input type="hidden" name="indexes[]" value="{{ $index }}">
                                    <button type="submit" class="rounded-lg border border-border px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                                        Retry
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                Tidak ada pesan gagal saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (count($failedItems) > 0)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const checkboxes = [...document.querySelectorAll('.failed-index-checkbox')];
                const selectAll = document.getElementById('select-all-failed');
                const form = document.getElementById('retry-selected-form');
                const indexesContainer = document.getElementById('retry-selected-indexes');
                const countEl = document.getElementById('retry-selected-count');
                const btn = document.getElementById('retry-selected-btn');

                const syncSelected = () => {
                    const selected = checkboxes.filter((cb) => cb.checked).map((cb) => cb.value);
                    indexesContainer.innerHTML = selected
                        .map((index) => `<input type="hidden" name="indexes[]" value="${index}">`)
                        .join('');
                    countEl.textContent = String(selected.length);
                    btn.disabled = selected.length === 0;
                    btn.classList.toggle('opacity-50', selected.length === 0);
                    if (selectAll) {
                        selectAll.checked = selected.length > 0 && selected.length === checkboxes.length;
                        selectAll.indeterminate = selected.length > 0 && selected.length < checkboxes.length;
                    }
                };

                checkboxes.forEach((cb) => cb.addEventListener('change', syncSelected));
                selectAll?.addEventListener('change', () => {
                    checkboxes.forEach((cb) => { cb.checked = selectAll.checked; });
                    syncSelected();
                });

                form?.addEventListener('submit', (event) => {
                    const selected = checkboxes.filter((cb) => cb.checked);
                    if (selected.length === 0) {
                        event.preventDefault();
                        return;
                    }
                    if (!confirm(`Retry ${selected.length} nomor terpilih?`)) {
                        event.preventDefault();
                    }
                });

                syncSelected();
            });
        </script>
    @endif
</x-layouts.dashboard>
