<x-layouts.dashboard title="Template Pesan" subtitle="Kelola template notifikasi untuk pasien RS Roemani">
  <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <p class="text-sm text-slate-500">{{ count(array_filter($templates, fn ($t) => $t['status'] === 'active')) }} template aktif dari {{ count($templates) }} total</p>
    <button type="button" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-700">
      <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
      </svg>
      Buat Template
    </button>
  </div>

  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($templates as $template)
      <div class="group rounded-2xl border border-border bg-surface-card p-5 shadow-card transition-all duration-200 hover:border-brand-200 hover:shadow-card-hover">
        <div class="flex items-start justify-between">
          <div class="flex size-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
          </div>
          <x-dashboard.badge :status="$template['status']" />
        </div>

        <h3 class="mt-4 font-semibold text-slate-900">{{ $template['name'] }}</h3>
        <p class="mt-1 text-xs font-medium uppercase tracking-wider text-brand-600">{{ $template['category'] }}</p>

        <div class="mt-4 flex items-center justify-between border-t border-border pt-4">
          <div>
            <p class="text-xs text-slate-500">Digunakan</p>
            <p class="text-sm font-semibold text-slate-900">{{ number_format($template['usage']) }}x</p>
          </div>
          <div class="text-right">
            <p class="text-xs text-slate-500">Diperbarui</p>
            <p class="text-sm text-slate-600">{{ $template['updated_at'] }}</p>
          </div>
        </div>

        <div class="mt-4 flex gap-2 opacity-0 transition-opacity group-hover:opacity-100">
          <button type="button" class="flex-1 rounded-lg border border-border py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">Edit</button>
          <button type="button" class="flex-1 rounded-lg border border-border py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">Preview</button>
        </div>
      </div>
    @endforeach

    {{-- Create template card --}}
    <button type="button" class="flex min-h-[200px] flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-brand-200 bg-brand-50/30 p-6 text-brand-600 transition-all hover:border-brand-400 hover:bg-brand-50">
      <div class="flex size-12 items-center justify-center rounded-xl bg-brand-100">
        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
      </div>
      <p class="text-sm font-semibold">Buat Template Baru</p>
    </button>
  </div>
</x-layouts.dashboard>
