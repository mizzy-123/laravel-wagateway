<x-layouts.dashboard title="Pengaturan" subtitle="Kelola profil akun dan konfigurasi WhatsApp Gateway">
  @php
    $activeTab = session('settings_tab', old('_tab', 'profile'));
  @endphp

  @if (session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
      <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      {{ session('success') }}
    </div>
  @endif

  @if (session('error'))
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
      {{ session('error') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
      <ul class="list-inside list-disc space-y-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="mb-6 flex gap-2 rounded-xl border border-border bg-slate-50 p-1">
    <button type="button" data-settings-tab="profile"
      class="settings-tab flex-1 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ $activeTab === 'profile' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
      Profil Akun
    </button>
    <button type="button" data-settings-tab="whatsapp"
      class="settings-tab flex-1 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ $activeTab === 'whatsapp' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
      WhatsApp Gateway
    </button>
  </div>

  {{-- Profile --}}
  <div id="tab-profile" class="{{ $activeTab === 'profile' ? '' : 'hidden' }}">
    <div class="rounded-2xl border border-border bg-surface-card p-6 shadow-card">
      <h3 class="text-base font-semibold text-slate-900">Profil Administrator</h3>
      <p class="mt-1 text-sm text-slate-500">Perbarui nama, email, dan kata sandi akun login.</p>

      <form method="POST" action="{{ route('dashboard.settings.profile') }}" class="mt-6 space-y-5">
        @csrf
        @method('PUT')
        <input type="hidden" name="_tab" value="profile">

        <div class="grid gap-5 sm:grid-cols-2">
          <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama</label>
            <input id="name" name="name" type="text" required value="{{ old('name', $user->name) }}"
              class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
          </div>
          <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
            <input id="email" name="email" type="email" required value="{{ old('email', $user->email) }}"
              class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
          </div>
        </div>

        <div class="border-t border-border pt-5">
          <p class="mb-4 text-sm font-medium text-slate-700">Ganti Kata Sandi <span class="font-normal text-slate-400">(opsional)</span></p>
          <div class="grid gap-5 sm:grid-cols-3">
            <div>
              <label for="current_password" class="mb-1.5 block text-sm font-medium text-slate-700">Kata Sandi Saat Ini</label>
              <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
            </div>
            <div>
              <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Kata Sandi Baru</label>
              <input id="password" name="password" type="password" autocomplete="new-password"
                class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
            </div>
            <div>
              <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-700">Konfirmasi</label>
              <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-2">
          <button type="submit" class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
            Simpan Profil
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- WhatsApp --}}
  <div id="tab-whatsapp" class="{{ $activeTab === 'whatsapp' ? '' : 'hidden' }}">
    <div class="space-y-6">
      <div class="rounded-2xl border border-border bg-surface-card p-6 shadow-card">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
          <div>
            <h3 class="text-base font-semibold text-slate-900">Koneksi WPPConnect</h3>
            <p class="mt-1 text-sm text-slate-500">Konfigurasi server WhatsApp Gateway yang dipakai aplikasi.</p>
          </div>
          <form method="POST" action="{{ route('dashboard.settings.test-connection') }}">
            @csrf
            <button type="submit" class="rounded-xl border border-border bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
              Tes Koneksi
            </button>
          </form>
        </div>

        <form method="POST" action="{{ route('dashboard.settings.whatsapp') }}" class="mt-6 space-y-5">
          @csrf
          @method('PUT')
          <input type="hidden" name="_tab" value="whatsapp">

          <div>
            <label for="base_url" class="mb-1.5 block text-sm font-medium text-slate-700">Base URL</label>
            <input id="base_url" name="base_url" type="url" required value="{{ old('base_url', $whatsapp['base_url']) }}"
              placeholder="http://localhost:21465"
              class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
            <p class="mt-1 text-xs text-slate-400">Contoh: http://localhost:21465</p>
          </div>

          <div class="grid gap-5 sm:grid-cols-2">
            <div>
              <label for="secret_key" class="mb-1.5 block text-sm font-medium text-slate-700">Secret Key</label>
              <input id="secret_key" name="secret_key" type="password" autocomplete="new-password"
                placeholder="{{ $whatsapp['secret_key'] ? '•••••••• (biarkan kosong jika tidak diganti)' : 'Masukkan secret key' }}"
                class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
              <p class="mt-1 text-xs text-slate-400">
                Status:
                @if ($whatsapp['secret_key'])
                  <span class="font-medium text-emerald-600">sudah dikonfigurasi</span>
                @else
                  <span class="font-medium text-amber-600">belum diisi</span>
                @endif
              </p>
            </div>
            <div>
              <label for="webhook_secret" class="mb-1.5 block text-sm font-medium text-slate-700">Webhook Secret</label>
              <input id="webhook_secret" name="webhook_secret" type="password" autocomplete="new-password"
                placeholder="{{ $whatsapp['webhook_secret'] ? '•••••••• (biarkan kosong jika tidak diganti)' : 'Opsional' }}"
                class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
            </div>
          </div>

          <div>
            <label for="webhook_url" class="mb-1.5 block text-sm font-medium text-slate-700">Webhook URL</label>
            <input id="webhook_url" name="webhook_url" type="url" value="{{ old('webhook_url', $whatsapp['webhook_url']) }}"
              placeholder="{{ url('/api/webhook/whatsapp') }}"
              class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
          </div>

          <div class="grid gap-5 sm:grid-cols-2">
            <div>
              <label for="connect_timeout" class="mb-1.5 block text-sm font-medium text-slate-700">Connect Timeout (detik)</label>
              <input id="connect_timeout" name="connect_timeout" type="number" min="1" max="60" required
                value="{{ old('connect_timeout', $whatsapp['connect_timeout']) }}"
                class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
            </div>
            <div>
              <label for="timeout" class="mb-1.5 block text-sm font-medium text-slate-700">HTTP Timeout (detik)</label>
              <input id="timeout" name="timeout" type="number" min="5" max="300" required
                value="{{ old('timeout', $whatsapp['timeout']) }}"
                class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
            </div>
          </div>

          <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
            Perubahan disimpan ke file <code class="font-mono">.env</code> dan cache konfigurasi akan dibersihkan otomatis.
          </div>

          <div class="flex justify-end pt-2">
            <button type="submit" class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
              Simpan Pengaturan Gateway
            </button>
          </div>
        </form>
      </div>

      <div class="rounded-2xl border border-border bg-surface-card p-6 shadow-card">
        <h3 class="text-base font-semibold text-slate-900">Ringkasan Aplikasi</h3>
        <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
          <div class="rounded-xl bg-slate-50 p-4">
            <dt class="text-xs uppercase tracking-wider text-slate-400">App Name</dt>
            <dd class="mt-1 font-medium text-slate-800">{{ config('app.name') }}</dd>
          </div>
          <div class="rounded-xl bg-slate-50 p-4">
            <dt class="text-xs uppercase tracking-wider text-slate-400">Environment</dt>
            <dd class="mt-1 font-medium text-slate-800">{{ config('app.env') }}</dd>
          </div>
          <div class="rounded-xl bg-slate-50 p-4">
            <dt class="text-xs uppercase tracking-wider text-slate-400">App URL</dt>
            <dd class="mt-1 break-all font-medium text-slate-800">{{ config('app.url') }}</dd>
          </div>
          <div class="rounded-xl bg-slate-50 p-4">
            <dt class="text-xs uppercase tracking-wider text-slate-400">Timezone</dt>
            <dd class="mt-1 font-medium text-slate-800">{{ config('app.timezone') }}</dd>
          </div>
        </dl>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const tabs = document.querySelectorAll('[data-settings-tab]');
      const panels = {
        profile: document.getElementById('tab-profile'),
        whatsapp: document.getElementById('tab-whatsapp'),
      };

      const activate = (name) => {
        tabs.forEach((tab) => {
          const active = tab.dataset.settingsTab === name;
          tab.classList.toggle('bg-white', active);
          tab.classList.toggle('text-slate-900', active);
          tab.classList.toggle('shadow-sm', active);
          tab.classList.toggle('text-slate-500', !active);
        });
        Object.entries(panels).forEach(([key, panel]) => {
          panel?.classList.toggle('hidden', key !== name);
        });
      };

      tabs.forEach((tab) => {
        tab.addEventListener('click', () => activate(tab.dataset.settingsTab));
      });
    });
  </script>
</x-layouts.dashboard>
