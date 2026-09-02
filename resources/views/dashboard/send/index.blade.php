<x-layouts.dashboard title="Kirim Pesan" subtitle="Kirim pesan WhatsApp ke satu nomor atau banyak nomor sekaligus">

  {{-- Flash Messages --}}
  @if (session('success'))
    <div id="flash-msg" class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
      <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
      {{ session('success') }}
    </div>
  @endif

  @if (session('warning'))
    <div id="flash-msg" class="mb-6 flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
      <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
      {{ session('warning') }}
    </div>
  @endif

  @if (session('error'))
    <div id="flash-msg" class="mb-6 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
      <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
      {{ session('error') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
      <ul class="list-inside list-disc space-y-1">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  {{-- No connected devices warning --}}
  @if ($devices->isEmpty())
    <div class="mb-6 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
      <svg class="mt-0.5 size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
      <div>
        <p class="font-semibold">Tidak ada perangkat yang terhubung</p>
        <p class="mt-1">Hubungkan minimal satu perangkat WhatsApp terlebih dahulu di <a href="{{ route('dashboard.devices') }}" class="font-medium underline">halaman Perangkat</a>.</p>
      </div>
    </div>
  @endif

  <div class="grid gap-6 lg:grid-cols-3">

    {{-- ===== LEFT COLUMN: Form ===== --}}
    <div class="lg:col-span-2 space-y-5">

      {{-- Tab switcher --}}
      <div class="flex rounded-xl border border-border bg-surface-card p-1 shadow-card">
        <button type="button" id="tab-single"
          onclick="switchTab('single')"
          class="tab-btn flex-1 rounded-lg px-4 py-2.5 text-sm font-medium transition-all tab-active">
          <span class="flex items-center justify-center gap-2">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
            Kirim ke Satu Nomor
          </span>
        </button>
        <button type="button" id="tab-blast"
          onclick="switchTab('blast')"
          class="tab-btn flex-1 rounded-lg px-4 py-2.5 text-sm font-medium transition-all tab-inactive">
          <span class="flex items-center justify-center gap-2">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
            Blast ke Banyak Nomor
          </span>
        </button>
      </div>

      {{-- ===== SINGLE FORM ===== --}}
      <div id="form-single">
        <form method="POST" action="{{ route('dashboard.send.single') }}" class="space-y-5">
          @csrf
          <input type="hidden" name="template_id" id="single-template-id">

          <div class="rounded-2xl border border-border bg-surface-card p-6 shadow-card space-y-5">
            <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
              <span class="flex size-7 items-center justify-center rounded-lg bg-brand-50 text-brand-600 text-xs font-bold">1</span>
              Pilih Perangkat
            </h2>

            <div>
              <label for="single-device" class="mb-1.5 block text-sm font-medium text-slate-700">Perangkat Pengirim</label>
              <select name="device_id" id="single-device" required
                class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                <option value="">Pilih perangkat...</option>
                @foreach ($devices as $device)
                  <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                    {{ $device->name }} — {{ $device->phone ?? $device->session }}
                  </option>
                @endforeach
              </select>
              @if ($devices->isEmpty())
                <p class="mt-1.5 text-xs text-red-500">Tidak ada perangkat terhubung.</p>
              @endif
            </div>
          </div>

          <div class="rounded-2xl border border-border bg-surface-card p-6 shadow-card space-y-5">
            <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
              <span class="flex size-7 items-center justify-center rounded-lg bg-brand-50 text-brand-600 text-xs font-bold">2</span>
              Tujuan
            </h2>

            <div>
              <label for="single-phone" class="mb-1.5 block text-sm font-medium text-slate-700">Nomor WhatsApp</label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-slate-400">+62</span>
                <input type="tel" name="phone" id="single-phone" required value="{{ old('phone') }}"
                  class="w-full rounded-xl border border-border bg-white py-2.5 pl-11 pr-4 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
                  placeholder="8123456789">
              </div>
              <p class="mt-1.5 text-xs text-slate-400">Format: 08xx atau 628xx — akan dikonversi otomatis.</p>
            </div>
          </div>

          <div class="rounded-2xl border border-border bg-surface-card p-6 shadow-card space-y-5">
            <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
              <span class="flex size-7 items-center justify-center rounded-lg bg-brand-50 text-brand-600 text-xs font-bold">3</span>
              Pesan
            </h2>

            {{-- Template selector --}}
            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-700">Gunakan Template <span class="font-normal text-slate-400">(opsional)</span></label>
              <div class="grid gap-2 sm:grid-cols-2">
                @foreach ($templates as $tmpl)
                  <button type="button"
                    onclick="applyTemplate('single', {{ $tmpl->id }}, {{ Js::from($tmpl->body) }}, {{ Js::from($tmpl->exampleVariables()) }})"
                    class="template-chip group flex items-start gap-3 rounded-xl border border-border p-3 text-left transition-all hover:border-brand-400 hover:bg-brand-50/30"
                    data-id="{{ $tmpl->id }}">
                    <svg class="mt-0.5 size-4 shrink-0 text-brand-400 group-hover:text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    <div class="min-w-0">
                      <p class="truncate text-xs font-semibold text-slate-800">{{ $tmpl->name }}</p>
                      <p class="text-xs text-slate-400">{{ $tmpl->category }}</p>
                    </div>
                  </button>
                @endforeach
              </div>
            </div>

            {{-- Placeholder variable inputs (shown when template selected) --}}
            <div id="single-variables" class="hidden space-y-3 rounded-xl border border-brand-100 bg-brand-50/30 p-4">
              <p class="text-xs font-semibold text-brand-700">Isi Variabel Template</p>
              <div id="single-variable-inputs" class="grid gap-3 sm:grid-cols-2"></div>
            </div>

            <div>
              <label for="single-message" class="mb-1.5 block text-sm font-medium text-slate-700">Isi Pesan</label>
              <textarea name="message" id="single-message" rows="6" required
                class="w-full rounded-xl border border-border bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
                placeholder="Ketik pesan di sini...">{{ old('message') }}</textarea>
              <p id="single-char-count" class="mt-1.5 text-right text-xs text-slate-400">0 karakter</p>
            </div>

            <div class="flex justify-end pt-2">
              <button type="submit" {{ $devices->isEmpty() ? 'disabled' : '' }}
                class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-50">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0121.485 12 59.768 59.768 0 013.27 20.875L5.999 12zm0 0h7.5" /></svg>
                Kirim Pesan
              </button>
            </div>
          </div>
        </form>
      </div>

      {{-- ===== BLAST FORM ===== --}}
      <div id="form-blast" class="hidden">
        <form method="POST" action="{{ route('dashboard.send.blast') }}" class="space-y-5">
          @csrf
          <input type="hidden" name="template_id" id="blast-template-id">

          <div class="rounded-2xl border border-border bg-surface-card p-6 shadow-card space-y-5">
            <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
              <span class="flex size-7 items-center justify-center rounded-lg bg-wa/10 text-wa text-xs font-bold">1</span>
              Pilih Perangkat
            </h2>
            <div>
              <label for="blast-device" class="mb-1.5 block text-sm font-medium text-slate-700">Perangkat Pengirim</label>
              <select name="device_id" id="blast-device" required
                class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                <option value="">Pilih perangkat...</option>
                @foreach ($devices as $device)
                  <option value="{{ $device->id }}">{{ $device->name }} — {{ $device->phone ?? $device->session }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="rounded-2xl border border-border bg-surface-card p-6 shadow-card space-y-5">
            <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
              <span class="flex size-7 items-center justify-center rounded-lg bg-wa/10 text-wa text-xs font-bold">2</span>
              Daftar Nomor Tujuan
            </h2>
            <div>
              <label for="blast-phones" class="mb-1.5 block text-sm font-medium text-slate-700">Nomor WhatsApp</label>
              <textarea name="phones" id="blast-phones" rows="6" required
                oninput="updatePhoneCount()"
                class="w-full rounded-xl border border-border bg-white px-4 py-3 font-mono text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
                placeholder="08123456789&#10;08234567890&#10;628345678901&#10;&#10;Satu nomor per baris, atau pisah dengan koma."></textarea>
              <p id="phone-count" class="mt-1.5 text-xs text-slate-400">0 nomor terdeteksi</p>
            </div>
          </div>

          <div class="rounded-2xl border border-border bg-surface-card p-6 shadow-card space-y-5">
            <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
              <span class="flex size-7 items-center justify-center rounded-lg bg-wa/10 text-wa text-xs font-bold">3</span>
              Pesan
            </h2>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-700">Gunakan Template <span class="font-normal text-slate-400">(opsional)</span></label>
              <div class="grid gap-2 sm:grid-cols-2">
                @foreach ($templates as $tmpl)
                  <button type="button"
                    onclick="applyTemplate('blast', {{ $tmpl->id }}, {{ Js::from($tmpl->body) }}, {{ Js::from($tmpl->exampleVariables()) }})"
                    class="blast-template-chip group flex items-start gap-3 rounded-xl border border-border p-3 text-left transition-all hover:border-brand-400 hover:bg-brand-50/30"
                    data-id="{{ $tmpl->id }}">
                    <svg class="mt-0.5 size-4 shrink-0 text-brand-400 group-hover:text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    <div class="min-w-0">
                      <p class="truncate text-xs font-semibold text-slate-800">{{ $tmpl->name }}</p>
                      <p class="text-xs text-slate-400">{{ $tmpl->category }}</p>
                    </div>
                  </button>
                @endforeach
              </div>
            </div>

            <div id="blast-variables" class="hidden space-y-3 rounded-xl border border-brand-100 bg-brand-50/30 p-4">
              <p class="text-xs font-semibold text-brand-700">Isi Variabel Template <span class="font-normal text-slate-500">(nilai yang sama akan dipakai untuk semua penerima)</span></p>
              <div id="blast-variable-inputs" class="grid gap-3 sm:grid-cols-2"></div>
            </div>

            <div>
              <label for="blast-message" class="mb-1.5 block text-sm font-medium text-slate-700">Isi Pesan</label>
              <textarea name="message" id="blast-message" rows="6" required
                class="w-full rounded-xl border border-border bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
                placeholder="Ketik pesan di sini..."></textarea>
              <p id="blast-char-count" class="mt-1.5 text-right text-xs text-slate-400">0 karakter</p>
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
              <strong>Perhatian:</strong> Fitur blast mengirim pesan satu per satu ke setiap nomor. Pastikan perangkat WhatsApp terhubung dan tidak terblokir.
            </div>

            <div class="flex justify-end pt-2">
              <button type="submit" {{ $devices->isEmpty() ? 'disabled' : '' }}
                class="inline-flex items-center gap-2 rounded-xl bg-wa px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-wa/90 disabled:cursor-not-allowed disabled:opacity-50">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0121.485 12 59.768 59.768 0 013.27 20.875L5.999 12zm0 0h7.5" /></svg>
                Kirim Blast
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    {{-- ===== RIGHT COLUMN: Info Panel ===== --}}
    <div class="space-y-5">

      {{-- Preview panel --}}
      <div class="rounded-2xl border border-border bg-surface-card p-5 shadow-card">
        <h3 class="mb-3 text-sm font-semibold text-slate-800">Preview Pesan</h3>
        <div id="message-preview" class="min-h-[120px] rounded-xl bg-slate-50 p-4 text-sm text-slate-500 italic">
          Pesan akan muncul di sini saat Anda mengetik...
        </div>
        <div class="mt-3 flex items-center justify-between border-t border-border pt-3">
          <p class="text-xs text-slate-400">Tampilan preview</p>
          <div class="flex size-7 items-center justify-center rounded-full bg-wa text-white">
            <svg class="size-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
          </div>
        </div>
      </div>

      {{-- Stat: templates available --}}
      <div class="rounded-2xl border border-border bg-surface-card p-5 shadow-card">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Template Aktif</p>
        <p class="mt-1 text-3xl font-bold text-slate-900">{{ $templates->count() }}</p>
        <a href="{{ route('dashboard.templates') }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-medium text-brand-600 hover:text-brand-700">
          Kelola template
          <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
        </a>
      </div>

      {{-- Connected devices --}}
      <div class="rounded-2xl border border-border bg-surface-card p-5 shadow-card">
        <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Perangkat Terhubung</p>
        @forelse ($devices as $device)
          <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-border' : '' }}">
            <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">
              {{ strtoupper(substr($device->name, 0, 1)) }}
            </span>
            <div class="min-w-0 flex-1">
              <p class="truncate text-sm font-medium text-slate-800">{{ $device->name }}</p>
              <p class="truncate text-xs text-slate-400">{{ $device->phone ?? $device->session }}</p>
            </div>
            <span class="size-2 shrink-0 rounded-full bg-emerald-400"></span>
          </div>
        @empty
          <p class="text-sm text-slate-400">Tidak ada perangkat terhubung.</p>
        @endforelse
      </div>

      {{-- Tips --}}
      <div class="rounded-2xl border border-brand-100 bg-brand-50/50 p-5">
        <p class="mb-2 text-xs font-semibold text-brand-700">Tips Penggunaan</p>
        <ul class="space-y-2 text-xs text-slate-600">
          <li class="flex gap-2"><span class="mt-0.5 text-brand-400">•</span>Format nomor: <strong>08xx</strong> atau <strong>628xx</strong></li>
          <li class="flex gap-2"><span class="mt-0.5 text-brand-400">•</span>Untuk blast, satu nomor per baris atau pisah dengan koma</li>
          <li class="flex gap-2"><span class="mt-0.5 text-brand-400">•</span>Gunakan template untuk pesan yang sering dikirim</li>
          <li class="flex gap-2"><span class="mt-0.5 text-brand-400">•</span>Pesan terkirim akan tercatat di <a href="{{ route('dashboard.messages') }}" class="font-medium text-brand-600 hover:underline">riwayat pesan</a></li>
        </ul>
      </div>
    </div>
  </div>

  <style>
    .tab-active { background-color: white; color: rgb(var(--color-brand-600, 37 99 235)); font-weight: 600; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
    .tab-inactive { color: rgb(100 116 139); }
    .template-chip.selected, .blast-template-chip.selected { border-color: rgb(var(--color-brand-400, 96 165 250)); background-color: rgb(var(--color-brand-50, 239 246 255) / 0.5); }
  </style>

  <script>
    // ==================== TABS ====================
    function switchTab(tab) {
      const isSingle = tab === 'single';

      document.getElementById('form-single').classList.toggle('hidden', !isSingle);
      document.getElementById('form-blast').classList.toggle('hidden', isSingle);

      document.getElementById('tab-single').className = 'tab-btn flex-1 rounded-lg px-4 py-2.5 text-sm font-medium transition-all ' + (isSingle ? 'tab-active' : 'tab-inactive');
      document.getElementById('tab-blast').className = 'tab-btn flex-1 rounded-lg px-4 py-2.5 text-sm font-medium transition-all ' + (!isSingle ? 'tab-active' : 'tab-inactive');

      updatePreview();
    }

    // ==================== PREVIEW ====================
    function getActiveMessage() {
      const isSingle = !document.getElementById('form-single').classList.contains('hidden');
      return isSingle
        ? document.getElementById('single-message').value
        : document.getElementById('blast-message').value;
    }

    function updatePreview() {
      const msg = getActiveMessage().trim();
      const el = document.getElementById('message-preview');
      if (msg) {
        el.classList.remove('italic', 'text-slate-500');
        el.classList.add('text-slate-800', 'whitespace-pre-wrap');
        el.textContent = msg;
      } else {
        el.classList.add('italic', 'text-slate-500');
        el.classList.remove('text-slate-800', 'whitespace-pre-wrap');
        el.textContent = 'Pesan akan muncul di sini saat Anda mengetik...';
      }
    }

    document.getElementById('single-message').addEventListener('input', function() {
      document.getElementById('single-char-count').textContent = this.value.length + ' karakter';
      updatePreview();
    });

    document.getElementById('blast-message').addEventListener('input', function() {
      document.getElementById('blast-char-count').textContent = this.value.length + ' karakter';
      updatePreview();
    });

    // ==================== PHONE COUNT ====================
    function updatePhoneCount() {
      const raw = document.getElementById('blast-phones').value;
      const phones = raw.split(/[\s,]+/).map(p => p.replace(/\D/g, '')).filter(p => p.length > 5);
      const unique = [...new Set(phones)];
      document.getElementById('phone-count').textContent = unique.length + ' nomor terdeteksi';
    }

    // ==================== TEMPLATE APPLY ====================
    function applyTemplate(mode, id, body, exampleVars) {
      const prefix = mode === 'single' ? 'single' : 'blast';
      const chipClass = mode === 'single' ? '.template-chip' : '.blast-template-chip';

      // Mark selected chip
      document.querySelectorAll(chipClass).forEach(el => el.classList.remove('selected'));
      document.querySelector(chipClass + '[data-id="' + id + '"]').classList.add('selected');

      // Store template id
      document.getElementById(prefix + '-template-id').value = id;

      // Build variable inputs
      const vars = Object.keys(exampleVars);
      const variablesContainer = document.getElementById(prefix + '-variables');
      const inputsContainer = document.getElementById(prefix + '-variable-inputs');

      if (vars.length > 0) {
        inputsContainer.innerHTML = '';
        vars.forEach(key => {
          const wrapper = document.createElement('div');
          wrapper.innerHTML = `
            <label class="mb-1 block text-xs font-medium text-slate-600">{${key}}</label>
            <input type="text" data-var="${key}"
              class="var-input w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-slate-900 outline-none focus:border-brand-400 focus:ring-1 focus:ring-brand-400/30"
              placeholder="${exampleVars[key]}">
          `;
          inputsContainer.appendChild(wrapper);
        });

        variablesContainer.classList.remove('hidden');

        // Live update message as variables change
        inputsContainer.querySelectorAll('input[data-var]').forEach(input => {
          input.addEventListener('input', () => rebuildMessage(prefix, body));
        });
      } else {
        variablesContainer.classList.add('hidden');
      }

      // Set raw body into message textarea first, then rebuild
      document.getElementById(prefix + '-message').value = body;
      rebuildMessage(prefix, body);
    }

    function rebuildMessage(prefix, rawBody) {
      const inputsContainer = document.getElementById(prefix + '-variable-inputs');
      let message = rawBody;

      inputsContainer.querySelectorAll('input[data-var]').forEach(input => {
        const key = input.getAttribute('data-var');
        const val = input.value.trim() || ('{' + key + '}');
        message = message.split('{' + key + '}').join(val);
      });

      const textarea = document.getElementById(prefix + '-message');
      textarea.value = message;
      textarea.dispatchEvent(new Event('input'));
    }

    // ==================== FLASH AUTO-HIDE ====================
    const flash = document.getElementById('flash-msg');
    if (flash) {
      setTimeout(() => {
        flash.style.transition = 'opacity 500ms';
        flash.style.opacity = '0';
        setTimeout(() => flash.remove(), 500);
      }, 5000);
    }
  </script>

</x-layouts.dashboard>
