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
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
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

          <div class="rounded-2xl border border-border bg-surface-card p-6 shadow-card space-y-4">
            <div class="flex items-center justify-between">
              <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                <span class="flex size-7 items-center justify-center rounded-lg bg-brand-50 text-brand-600 text-xs font-bold">2</span>
                Tujuan
              </h2>
              <button type="button" onclick="openContactModal('single')"
                class="inline-flex items-center gap-1.5 rounded-xl border border-brand-200 bg-brand-50/70 px-3 py-1.5 text-xs font-semibold text-brand-700 transition-colors hover:bg-brand-100">
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                Pilih dari Appointments
              </button>
            </div>

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

            {{-- Selected contact badge indicator --}}
            <div id="single-selected-contact" class="hidden items-center justify-between rounded-xl border border-brand-200 bg-brand-50/50 p-3 text-xs">
              <div class="flex items-center gap-2.5 min-w-0">
                <span id="single-contact-badge" class="rounded-md bg-brand-100 px-2 py-0.5 text-[10px] font-bold uppercase text-brand-800">Pasien</span>
                <div class="min-w-0">
                  <p id="single-contact-name" class="font-semibold text-slate-900 truncate">-</p>
                  <p id="single-contact-phone" class="text-slate-500 font-mono text-[11px]">-</p>
                </div>
              </div>
              <button type="button" onclick="clearSelectedContact()" class="p-1 text-slate-400 hover:text-red-600" title="Hapus pilihan">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
              </button>
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
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
              <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                <span class="flex size-7 items-center justify-center rounded-lg bg-wa/10 text-wa text-xs font-bold">2</span>
                Daftar Nomor Tujuan
              </h2>

              <button type="button" onclick="openContactModal('blast')"
                class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-800 transition-colors hover:bg-emerald-100">
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                </svg>
                Pilih Kontak Spesifik
              </button>
            </div>

            {{-- Quick Load from Appointments Toolbar --}}
            <div class="rounded-xl border border-border bg-slate-50/80 p-4 space-y-3">
              <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                  <svg class="size-4 text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                  </svg>
                  Ambil Cepat dari Data Appointments:
                </p>
                <span id="blast-load-status" class="text-[11px] font-medium text-slate-500"></span>
              </div>

              {{-- Type selector tabs --}}
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <button type="button" onclick="setBlastTypeFilter('all')" id="btn-type-all"
                  class="blast-type-btn flex flex-col items-center justify-center p-2 rounded-lg border text-center transition-all bg-brand-600 text-white border-brand-600 font-semibold shadow-xs">
                  <span class="text-xs">Semua Kontak</span>
                  <span class="text-[11px] opacity-90 font-mono font-normal">{{ number_format($appointmentCounts['all']) }}</span>
                </button>
                <button type="button" onclick="setBlastTypeFilter('doctor')" id="btn-type-doctor"
                  class="blast-type-btn flex flex-col items-center justify-center p-2 rounded-lg border text-center transition-all bg-white text-slate-700 border-border hover:bg-slate-100">
                  <span class="text-xs font-medium">Dokter</span>
                  <span class="text-[11px] text-slate-500 font-mono">{{ number_format($appointmentCounts['doctor']) }}</span>
                </button>
                <button type="button" onclick="setBlastTypeFilter('employee')" id="btn-type-employee"
                  class="blast-type-btn flex flex-col items-center justify-center p-2 rounded-lg border text-center transition-all bg-white text-slate-700 border-border hover:bg-slate-100">
                  <span class="text-xs font-medium">Karyawan</span>
                  <span class="text-[11px] text-slate-500 font-mono">{{ number_format($appointmentCounts['employee']) }}</span>
                </button>
                <button type="button" onclick="setBlastTypeFilter('patient')" id="btn-type-patient"
                  class="blast-type-btn flex flex-col items-center justify-center p-2 rounded-lg border text-center transition-all bg-white text-slate-700 border-border hover:bg-slate-100">
                  <span class="text-xs font-medium">Pasien</span>
                  <span class="text-[11px] text-slate-500 font-mono">{{ number_format($appointmentCounts['patient']) }}</span>
                </button>
              </div>

              {{-- Actions row --}}
              <div class="flex flex-wrap items-center justify-between gap-3 pt-1 border-t border-slate-200/80">
                <div class="flex items-center gap-2">
                  <label for="blast-limit-select" class="text-xs text-slate-500 whitespace-nowrap">Jumlah:</label>
                  <select id="blast-limit-select" class="rounded-lg border border-border bg-white px-2.5 py-1 text-xs text-slate-700 outline-none focus:border-brand-500">
                    <option value="all">Semua Data</option>
                    <option value="50">50 Nomor</option>
                    <option value="100">100 Nomor</option>
                    <option value="250">250 Nomor</option>
                    <option value="500">500 Nomor</option>
                    <option value="1000">1000 Nomor</option>
                  </select>
                </div>

                <div class="flex items-center gap-2">
                  <button type="button" id="btn-quick-load" onclick="quickLoadAppointmentNumbers()"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-3 py-1.5 text-xs font-semibold text-white shadow-xs hover:bg-brand-700 transition-colors">
                    <svg id="quick-load-spinner" class="size-3.5 hidden animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span id="quick-load-text">Muat Nomor ke Daftar</span>
                  </button>
                  <button type="button" onclick="clearBlastPhones()"
                    class="rounded-lg border border-border bg-white px-2.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 hover:text-red-600 transition-colors" title="Bersihkan daftar nomor">
                    Bersihkan
                  </button>
                </div>
              </div>
            </div>

            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label for="blast-phones" class="block text-sm font-medium text-slate-700">Daftar Nomor WhatsApp</label>
                <p id="phone-count" class="text-xs font-medium text-brand-600">0 nomor terdeteksi</p>
              </div>
              <textarea name="phones" id="blast-phones" rows="6" required
                oninput="updatePhoneCount()"
                class="w-full rounded-xl border border-border bg-white px-4 py-3 font-mono text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
                placeholder="08123456789&#10;08234567890&#10;628345678901&#10;&#10;Satu nomor per baris, atau pisah dengan koma."></textarea>
              <p class="mt-1.5 text-xs text-slate-400">Nomor dengan format 08xx atau 628xx akan otomatis distandarkan saat dikirim.</p>
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

      {{-- Stat: Appointments Contacts --}}
      <div class="rounded-2xl border border-border bg-surface-card p-5 shadow-card">
        <div class="flex items-center justify-between mb-3">
          <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Data Appointments</p>
          <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">Aktif</span>
        </div>
        <p class="text-3xl font-bold text-slate-900">{{ number_format($appointmentCounts['all']) }}</p>
        <p class="text-xs text-slate-500 mt-1">Total kontak dengan nomor WhatsApp</p>

        <div class="mt-4 pt-3 border-t border-border space-y-2 text-xs">
          <div class="flex items-center justify-between">
            <span class="flex items-center gap-1.5 text-slate-600">
              <span class="size-2 rounded-full bg-emerald-500"></span>
              Pasien
            </span>
            <span class="font-semibold text-slate-900 font-mono">{{ number_format($appointmentCounts['patient']) }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="flex items-center gap-1.5 text-slate-600">
              <span class="size-2 rounded-full bg-indigo-500"></span>
              Dokter
            </span>
            <span class="font-semibold text-slate-900 font-mono">{{ number_format($appointmentCounts['doctor']) }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="flex items-center gap-1.5 text-slate-600">
              <span class="size-2 rounded-full bg-amber-500"></span>
              Karyawan
            </span>
            <span class="font-semibold text-slate-900 font-mono">{{ number_format($appointmentCounts['employee']) }}</span>
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
          <li class="flex gap-2"><span class="mt-0.5 text-brand-400">•</span>Pilih kontak dari data appointments untuk mengisi nomor otomatis</li>
          <li class="flex gap-2"><span class="mt-0.5 text-brand-400">•</span>Format nomor: <strong>08xx</strong> atau <strong>628xx</strong></li>
          <li class="flex gap-2"><span class="mt-0.5 text-brand-400">•</span>Gunakan tombol <strong>Muat Nomor</strong> untuk blast seluruh dokter atau karyawan</li>
          <li class="flex gap-2"><span class="mt-0.5 text-brand-400">•</span>Pesan terkirim akan tercatat di <a href="{{ route('dashboard.messages') }}" class="font-medium text-brand-600 hover:underline">riwayat pesan</a></li>
        </ul>
      </div>
    </div>
  </div>

  {{-- ==================== CONTACT SELECTOR MODAL ==================== --}}
  <div id="contact-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" onclick="closeContactModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
      <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-2xl flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between border-b border-border p-5">
          <div>
            <h3 id="contact-modal-title" class="text-base font-semibold text-slate-900">Pilih Kontak dari Appointments</h3>
            <p id="contact-modal-subtitle" class="text-xs text-slate-500 mt-0.5">Cari dan pilih nomor telepon berdasarkan kategori kontak</p>
          </div>
          <button type="button" onclick="closeContactModal()" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        {{-- Filter & Search Bar --}}
        <div class="p-5 border-b border-border space-y-3 bg-slate-50/50">
          {{-- Type filter pills --}}
          <div class="flex flex-wrap gap-2">
            <button type="button" onclick="setModalTypeFilter('all')" id="modal-filter-all"
              class="modal-filter-btn rounded-lg px-3 py-1.5 text-xs font-semibold transition-all bg-brand-600 text-white shadow-xs">
              Semua ({{ number_format($appointmentCounts['all']) }})
            </button>
            <button type="button" onclick="setModalTypeFilter('patient')" id="modal-filter-patient"
              class="modal-filter-btn rounded-lg px-3 py-1.5 text-xs font-medium transition-all bg-white text-slate-600 border border-border hover:bg-slate-100">
              Pasien ({{ number_format($appointmentCounts['patient']) }})
            </button>
            <button type="button" onclick="setModalTypeFilter('doctor')" id="modal-filter-doctor"
              class="modal-filter-btn rounded-lg px-3 py-1.5 text-xs font-medium transition-all bg-white text-slate-600 border border-border hover:bg-slate-100">
              Dokter ({{ number_format($appointmentCounts['doctor']) }})
            </button>
            <button type="button" onclick="setModalTypeFilter('employee')" id="modal-filter-employee"
              class="modal-filter-btn rounded-lg px-3 py-1.5 text-xs font-medium transition-all bg-white text-slate-600 border border-border hover:bg-slate-100">
              Karyawan ({{ number_format($appointmentCounts['employee']) }})
            </button>
          </div>

          {{-- Search Input --}}
          <div class="relative">
            <svg class="absolute inset-y-0 left-3 my-auto size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input type="text" id="contact-search-input" oninput="handleContactSearch(this.value)"
              class="w-full rounded-xl border border-border bg-white py-2.5 pl-9 pr-8 text-xs text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
              placeholder="Cari berdasarkan nama, nomor HP, atau no RM/ID...">
            <button type="button" id="contact-search-clear" onclick="clearModalSearch()" class="hidden absolute inset-y-0 right-3 my-auto text-slate-400 hover:text-slate-600">
              <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
          </div>
        </div>

        {{-- Contact List Results --}}
        <div class="flex-1 overflow-y-auto p-4 divide-y divide-border/60 min-h-[260px] max-h-[380px]" id="contact-list-container">
          <div id="contact-loading" class="flex flex-col items-center justify-center py-12 text-slate-400 gap-2">
            <svg class="size-6 animate-spin text-brand-600" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span class="text-xs">Memuat data kontak...</span>
          </div>
          <div id="contact-empty" class="hidden flex-col items-center justify-center py-12 text-slate-400">
            <svg class="size-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
            <p class="text-xs font-medium">Tidak ada kontak yang ditemukan</p>
          </div>
          <div id="contact-items" class="space-y-1"></div>
        </div>

        {{-- Modal Footer --}}
        <div class="flex items-center justify-between border-t border-border p-4 bg-slate-50/50 rounded-b-2xl">
          <div id="modal-multi-footer-info" class="hidden text-xs text-slate-600">
            <span id="selected-contacts-count" class="font-bold text-brand-700 font-mono">0</span> kontak dipilih
          </div>
          <div class="text-xs text-slate-400" id="modal-single-footer-info">
            Klik tombol "Pilih" pada baris kontak untuk mengisi formulir
          </div>

          <div class="flex items-center gap-2">
            <button type="button" onclick="closeContactModal()"
              class="rounded-xl border border-border bg-white px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors">
              Tutup
            </button>
            <button type="button" id="btn-insert-multi-contacts" onclick="insertSelectedContactsToBlast()"
              class="hidden rounded-xl bg-brand-600 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-brand-700 transition-colors">
              Masukkan ke Daftar Blast
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>

  <style>
    .tab-active { background-color: white; color: rgb(var(--color-brand-600, 37 99 235)); font-weight: 600; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
    .tab-inactive { color: rgb(100 116 139); }
    .template-chip.selected, .blast-template-chip.selected { border-color: rgb(var(--color-brand-400, 96 165 250)); background-color: rgb(var(--color-brand-50, 239 246 255) / 0.5); }
  </style>

  <script>
    // Global state
    let currentModalTarget = 'single'; // 'single' or 'blast'
    let currentModalType = 'all';
    let currentBlastQuickType = 'all';
    let modalSearchQuery = '';
    let searchDebounceTimer = null;
    let selectedContactsMap = new Map(); // id => {name, phone, normalized_phone, type}

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
      const phones = raw.split(/[\s,]+/).map(p => p.replace(/\D/g, '')).filter(p => p.length >= 9);
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

      if (inputsContainer) {
        inputsContainer.querySelectorAll('input[data-var]').forEach(input => {
          const key = input.getAttribute('data-var');
          const val = input.value.trim() || ('{' + key + '}');
          message = message.split('{' + key + '}').join(val);
        });
      }

      const textarea = document.getElementById(prefix + '-message');
      textarea.value = message;
      textarea.dispatchEvent(new Event('input'));
    }

    // ==================== APPOINTMENTS: SINGLE CONTACT PICK ====================
    function selectSingleContact(contact) {
      const phoneInput = document.getElementById('single-phone');
      // Format to normalized number without leading 62 if starts with 62
      let cleanPhone = contact.phone ? contact.phone.replace(/\D/g, '') : '';
      if (cleanPhone.startsWith('62')) {
        cleanPhone = cleanPhone.substring(2);
      } else if (cleanPhone.startsWith('0')) {
        cleanPhone = cleanPhone.substring(1);
      }

      phoneInput.value = cleanPhone;

      // Show selected card
      document.getElementById('single-selected-contact').classList.remove('hidden');
      document.getElementById('single-selected-contact').classList.add('flex');
      document.getElementById('single-contact-name').textContent = contact.name + (contact.ref_id !== '-' ? ' (' + contact.ref_id + ')' : '');
      document.getElementById('single-contact-phone').textContent = contact.formatted_phone;

      const badge = document.getElementById('single-contact-badge');
      badge.textContent = contact.type_label;
      if (contact.type === 'doctor') {
        badge.className = 'rounded-md bg-indigo-100 px-2 py-0.5 text-[10px] font-bold uppercase text-indigo-800';
      } else if (contact.type === 'employee') {
        badge.className = 'rounded-md bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase text-amber-800';
      } else {
        badge.className = 'rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-bold uppercase text-emerald-800';
      }

      // If template has {nama} variable, auto-fill it
      const nameVarInput = document.querySelector('#single-variable-inputs input[data-var="nama"]');
      if (nameVarInput) {
        nameVarInput.value = contact.name;
        nameVarInput.dispatchEvent(new Event('input'));
      }

      closeContactModal();
    }

    function clearSelectedContact() {
      document.getElementById('single-selected-contact').classList.add('hidden');
      document.getElementById('single-selected-contact').classList.remove('flex');
      document.getElementById('single-phone').value = '';
    }

    // ==================== APPOINTMENTS: BLAST QUICK LOAD ====================
    function setBlastTypeFilter(type) {
      currentBlastQuickType = type;
      document.querySelectorAll('.blast-type-btn').forEach(btn => {
        btn.className = 'blast-type-btn flex flex-col items-center justify-center p-2 rounded-lg border text-center transition-all bg-white text-slate-700 border-border hover:bg-slate-100';
      });

      const activeBtn = document.getElementById('btn-type-' + type);
      if (activeBtn) {
        activeBtn.className = 'blast-type-btn flex flex-col items-center justify-center p-2 rounded-lg border text-center transition-all bg-brand-600 text-white border-brand-600 font-semibold shadow-xs';
      }
    }

    function quickLoadAppointmentNumbers() {
      const limit = document.getElementById('blast-limit-select').value;
      const spinner = document.getElementById('quick-load-spinner');
      const btnText = document.getElementById('quick-load-text');
      const statusEl = document.getElementById('blast-load-status');

      spinner.classList.remove('hidden');
      btnText.textContent = 'Memuat...';
      statusEl.textContent = 'Mengambil nomor dari server...';

      const url = `/dashboard/appointments/load-numbers?type=${currentBlastQuickType}&limit=${limit}`;

      fetch(url, {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      })
      .then(res => res.json())
      .then(res => {
        spinner.classList.add('hidden');
        btnText.textContent = 'Muat Nomor ke Daftar';

        if (res.status === 'success' && res.phones) {
          const textarea = document.getElementById('blast-phones');
          const existing = textarea.value.trim();
          const newPhonesText = res.phones.join('\n');

          if (existing !== '') {
            if (confirm(`Tambahkan ${res.phones.length} nomor ke daftar yang sudah ada? (Pilih Batal jika ingin mengganti seluruhnya)`)) {
              textarea.value = existing + '\n' + newPhonesText;
            } else {
              textarea.value = newPhonesText;
            }
          } else {
            textarea.value = newPhonesText;
          }

          updatePhoneCount();
          statusEl.textContent = `✓ ${res.phones.length} nomor berhasil dimuat`;
          setTimeout(() => { statusEl.textContent = ''; }, 4000);
        }
      })
      .catch(() => {
        spinner.classList.add('hidden');
        btnText.textContent = 'Muat Nomor ke Daftar';
        statusEl.textContent = 'Gagal memuat data';
      });
    }

    function clearBlastPhones() {
      if (confirm('Bersihkan seluruh daftar nomor blast?')) {
        document.getElementById('blast-phones').value = '';
        updatePhoneCount();
      }
    }

    // ==================== APPOINTMENTS: MODAL CONTROLLER ====================
    function openContactModal(target) {
      currentModalTarget = target; // 'single' or 'blast'
      currentModalType = 'all';
      modalSearchQuery = '';
      selectedContactsMap.clear();

      document.getElementById('contact-search-input').value = '';
      document.getElementById('contact-search-clear').classList.add('hidden');

      // Setup title & footer buttons based on target
      if (target === 'single') {
        document.getElementById('contact-modal-title').textContent = 'Pilih Kontak Tujuan';
        document.getElementById('contact-modal-subtitle').textContent = 'Klik salah satu kontak untuk mengisi nomor WhatsApp';
        document.getElementById('modal-single-footer-info').classList.remove('hidden');
        document.getElementById('modal-multi-footer-info').classList.add('hidden');
        document.getElementById('btn-insert-multi-contacts').classList.add('hidden');
      } else {
        document.getElementById('contact-modal-title').textContent = 'Pilih Kontak untuk Blast';
        document.getElementById('contact-modal-subtitle').textContent = 'Centang kontak yang ingin dimasukkan ke daftar pengiriman massal';
        document.getElementById('modal-single-footer-info').classList.add('hidden');
        document.getElementById('modal-multi-footer-info').classList.remove('hidden');
        document.getElementById('btn-insert-multi-contacts').classList.remove('hidden');
        updateMultiSelectCount();
      }

      setModalTypeFilter('all');
      document.getElementById('contact-modal').classList.remove('hidden');
      document.body.style.overflow = 'hidden';
      fetchContacts();
    }

    function closeContactModal() {
      document.getElementById('contact-modal').classList.add('hidden');
      document.body.style.overflow = '';
    }

    function setModalTypeFilter(type) {
      currentModalType = type;
      document.querySelectorAll('.modal-filter-btn').forEach(btn => {
        btn.className = 'modal-filter-btn rounded-lg px-3 py-1.5 text-xs font-medium transition-all bg-white text-slate-600 border border-border hover:bg-slate-100';
      });

      const activeBtn = document.getElementById('modal-filter-' + type);
      if (activeBtn) {
        activeBtn.className = 'modal-filter-btn rounded-lg px-3 py-1.5 text-xs font-semibold transition-all bg-brand-600 text-white shadow-xs';
      }

      fetchContacts();
    }

    function handleContactSearch(val) {
      modalSearchQuery = val.trim();
      document.getElementById('contact-search-clear').classList.toggle('hidden', modalSearchQuery === '');

      clearTimeout(searchDebounceTimer);
      searchDebounceTimer = setTimeout(() => {
        fetchContacts();
      }, 300);
    }

    function clearModalSearch() {
      document.getElementById('contact-search-input').value = '';
      document.getElementById('contact-search-clear').classList.add('hidden');
      modalSearchQuery = '';
      fetchContacts();
    }

    function fetchContacts() {
      const container = document.getElementById('contact-items');
      const loading = document.getElementById('contact-loading');
      const empty = document.getElementById('contact-empty');

      container.innerHTML = '';
      loading.classList.remove('hidden');
      empty.classList.add('hidden');

      const url = `/dashboard/appointments/search?type=${currentModalType}&q=${encodeURIComponent(modalSearchQuery)}&limit=30`;

      fetch(url, {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      })
      .then(res => res.json())
      .then(res => {
        loading.classList.add('hidden');
        if (!res.data || res.data.length === 0) {
          empty.classList.remove('hidden');
          return;
        }

        res.data.forEach(contact => {
          const isSelected = selectedContactsMap.has(contact.id);
          const itemDiv = document.createElement('div');
          itemDiv.className = 'flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition-colors gap-3 border border-transparent hover:border-slate-100';

          const badgeColor = contact.type === 'doctor'
            ? 'bg-indigo-100 text-indigo-800'
            : (contact.type === 'employee' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800');

          const initialColor = contact.type === 'doctor'
            ? 'bg-indigo-50 text-indigo-700'
            : (contact.type === 'employee' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700');

          const initial = contact.name ? contact.name.trim().charAt(0).toUpperCase() : '?';

          if (currentModalTarget === 'single') {
            itemDiv.innerHTML = `
              <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="flex size-9 shrink-0 items-center justify-center rounded-xl ${initialColor} text-xs font-bold">
                  ${initial}
                </div>
                <div class="min-w-0 flex-1">
                  <div class="flex items-center gap-2">
                    <p class="font-semibold text-xs text-slate-900 truncate">${contact.name}</p>
                    <span class="rounded px-1.5 py-0.2 text-[9px] font-bold uppercase ${badgeColor}">${contact.type_label}</span>
                  </div>
                  <div class="flex items-center gap-2 text-[11px] text-slate-500 mt-0.5">
                    <span class="font-mono">${contact.formatted_phone}</span>
                    ${contact.ref_id !== '-' ? `<span class="text-slate-400">• ID: ${contact.ref_id}</span>` : ''}
                  </div>
                </div>
              </div>
              <button type="button" class="btn-pick-single shrink-0 rounded-lg bg-brand-50 border border-brand-200 px-3 py-1.5 text-xs font-semibold text-brand-700 hover:bg-brand-600 hover:text-white transition-all">
                Pilih
              </button>
            `;

            itemDiv.querySelector('.btn-pick-single').addEventListener('click', () => {
              selectSingleContact(contact);
            });
          } else {
            // Multi-select for Blast
            itemDiv.innerHTML = `
              <label class="flex items-center gap-3 min-w-0 flex-1 cursor-pointer">
                <input type="checkbox" class="modal-contact-chk rounded border-border text-brand-600 focus:ring-brand-500 size-4"
                  ${isSelected ? 'checked' : ''}>
                <div class="flex size-9 shrink-0 items-center justify-center rounded-xl ${initialColor} text-xs font-bold">
                  ${initial}
                </div>
                <div class="min-w-0 flex-1">
                  <div class="flex items-center gap-2">
                    <p class="font-semibold text-xs text-slate-900 truncate">${contact.name}</p>
                    <span class="rounded px-1.5 py-0.2 text-[9px] font-bold uppercase ${badgeColor}">${contact.type_label}</span>
                  </div>
                  <div class="flex items-center gap-2 text-[11px] text-slate-500 mt-0.5">
                    <span class="font-mono">${contact.formatted_phone}</span>
                    ${contact.ref_id !== '-' ? `<span class="text-slate-400">• ID: ${contact.ref_id}</span>` : ''}
                  </div>
                </div>
              </label>
            `;

            const checkbox = itemDiv.querySelector('.modal-contact-chk');
            checkbox.addEventListener('change', (e) => {
              if (e.target.checked) {
                selectedContactsMap.set(contact.id, contact);
              } else {
                selectedContactsMap.delete(contact.id);
              }
              updateMultiSelectCount();
            });
          }

          container.appendChild(itemDiv);
        });
      })
      .catch(() => {
        loading.classList.add('hidden');
        empty.classList.remove('hidden');
      });
    }

    function updateMultiSelectCount() {
      const countEl = document.getElementById('selected-contacts-count');
      if (countEl) {
        countEl.textContent = selectedContactsMap.size;
      }
    }

    function insertSelectedContactsToBlast() {
      if (selectedContactsMap.size === 0) {
        alert('Pilih minimal satu kontak terlebih dahulu.');
        return;
      }

      const phones = [];
      selectedContactsMap.forEach(contact => {
        if (contact.normalized_phone) {
          phones.push(contact.normalized_phone);
        }
      });

      const textarea = document.getElementById('blast-phones');
      const existing = textarea.value.trim();
      const newPhones = phones.join('\n');

      if (existing !== '') {
        textarea.value = existing + '\n' + newPhones;
      } else {
        textarea.value = newPhones;
      }

      updatePhoneCount();
      closeContactModal();
    }

    // ==================== ESCAPE KEY MODAL ====================
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeContactModal();
      }
    });

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
