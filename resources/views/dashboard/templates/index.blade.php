<x-layouts.dashboard title="Template Pesan" subtitle="Kelola template notifikasi untuk pasien RS Roemani">

  {{-- Flash message --}}
  @if (session('success'))
    <div id="flash-message" class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
      <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      {{ session('success') }}
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

  <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <p class="text-sm text-slate-500">{{ $templates->where('status', 'active')->count() }} template aktif dari {{ $templates->count() }} total</p>
    <button type="button" onclick="openCreateModal()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-700">
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
          <x-dashboard.badge :status="$template->status" />
        </div>

        <h3 class="mt-4 font-semibold text-slate-900">{{ $template->name }}</h3>
        <p class="mt-1 text-xs font-medium uppercase tracking-wider text-brand-600">{{ $template->category }}</p>

        <div class="mt-3 rounded-lg bg-slate-50 p-3">
          <p class="line-clamp-2 text-xs text-slate-600">{{ $template->body }}</p>
        </div>

        <div class="mt-4 flex items-center justify-between border-t border-border pt-4">
          <div>
            <p class="text-xs text-slate-500">Digunakan</p>
            <p class="text-sm font-semibold text-slate-900">{{ number_format($template->usage_count) }}x</p>
          </div>
          <div class="text-right">
            <p class="text-xs text-slate-500">Diperbarui</p>
            <p class="text-sm text-slate-600">{{ $template->updated_at->diffForHumans() }}</p>
          </div>
        </div>

        <div class="mt-4 flex gap-2 opacity-0 transition-opacity group-hover:opacity-100">
          <button type="button" onclick="openEditModal({{ $template->id }}, {{ Js::from($template->only(['name', 'category', 'body', 'status'])) }})" class="flex-1 rounded-lg border border-border py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">Edit</button>
          <button type="button" onclick="openPreviewModal({{ $template->id }})" class="flex-1 rounded-lg border border-border py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">Preview</button>
          <button type="button" onclick="openDeleteModal({{ $template->id }}, {{ Js::from($template->name) }})" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">
            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
            </svg>
          </button>
        </div>
      </div>
    @endforeach

    {{-- Create template card --}}
    <button type="button" onclick="openCreateModal()" class="flex min-h-[200px] flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-brand-200 bg-brand-50/30 p-6 text-brand-600 transition-all hover:border-brand-400 hover:bg-brand-50">
      <div class="flex size-12 items-center justify-center rounded-xl bg-brand-100">
        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
      </div>
      <p class="text-sm font-semibold">Buat Template Baru</p>
    </button>
  </div>

  {{-- ==================== CREATE / EDIT MODAL ==================== --}}
  <div id="template-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeTemplateModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
      <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between">
          <h2 id="modal-title" class="text-lg font-semibold text-slate-900">Buat Template Baru</h2>
          <button type="button" onclick="closeTemplateModal()" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form id="template-form" method="POST" class="mt-5 space-y-4">
          @csrf
          <input type="hidden" id="form-method" name="_method" value="POST">

          <div>
            <label for="template-name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Template</label>
            <input type="text" id="template-name" name="name" required
              class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition-colors focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
              placeholder="contoh: Konfirmasi Janji Temu">
          </div>

          <div>
            <label for="template-category" class="mb-1.5 block text-sm font-medium text-slate-700">Kategori</label>
            <select id="template-category" name="category" required
              class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition-colors focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
              <option value="">Pilih kategori...</option>
              <option value="Appointment">Appointment</option>
              <option value="Laboratorium">Laboratorium</option>
              <option value="Follow-up">Follow-up</option>
              <option value="Informasi">Informasi</option>
              <option value="Survey">Survey</option>
              <option value="Billing">Billing</option>
              <option value="Lainnya">Lainnya</option>
            </select>
          </div>

          <div>
            <label for="template-body" class="mb-1.5 block text-sm font-medium text-slate-700">Isi Pesan</label>
            <textarea id="template-body" name="body" rows="5" required
              class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition-colors focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
              placeholder="Halo {nama}, jadwal kontrol Anda pada {tanggal} pukul {waktu} di {poli}..."></textarea>
            <p class="mt-1.5 text-xs text-slate-400">Gunakan <code class="rounded bg-slate-100 px-1 py-0.5 text-brand-600">{placeholder}</code> untuk variabel dinamis, misal: <code class="rounded bg-slate-100 px-1 py-0.5 text-brand-600">{nama}</code>, <code class="rounded bg-slate-100 px-1 py-0.5 text-brand-600">{tanggal}</code>, <code class="rounded bg-slate-100 px-1 py-0.5 text-brand-600">{dokter}</code></p>
          </div>

          <div>
            <label for="template-status" class="mb-1.5 block text-sm font-medium text-slate-700">Status</label>
            <select id="template-status" name="status" required
              class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition-colors focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
              <option value="draft">Draft</option>
              <option value="active">Aktif</option>
            </select>
          </div>

          <div class="flex justify-end gap-3 pt-2">
            <button type="button" onclick="closeTemplateModal()" class="rounded-xl border border-border px-5 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50">Batal</button>
            <button type="submit" id="modal-submit-btn" class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-700">Simpan Template</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- ==================== PREVIEW MODAL ==================== --}}
  <div id="preview-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closePreviewModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
      <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold text-slate-900">Preview Template</h2>
          <button type="button" onclick="closePreviewModal()" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="mt-5 space-y-4">
          <div>
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Nama Template</p>
            <p id="preview-name" class="mt-1 font-semibold text-slate-900">-</p>
          </div>
          <div>
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Kategori</p>
            <p id="preview-category" class="mt-1 text-sm text-brand-600">-</p>
          </div>
          <div>
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Template Asli</p>
            <div id="preview-body" class="mt-1 rounded-lg bg-slate-50 p-3 text-sm text-slate-700">-</div>
          </div>
          <div>
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Contoh Hasil</p>
            <div id="preview-parsed" class="mt-1 rounded-lg border-2 border-brand-100 bg-brand-50/30 p-3 text-sm text-slate-800">-</div>
          </div>
          <div>
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Variabel</p>
            <div id="preview-variables" class="mt-1 flex flex-wrap gap-1.5"></div>
          </div>
        </div>

        <div class="mt-6 flex justify-end">
          <button type="button" onclick="closePreviewModal()" class="rounded-xl border border-border px-5 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  {{-- ==================== DELETE MODAL ==================== --}}
  <div id="delete-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
      <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl" onclick="event.stopPropagation()">
        <div class="flex flex-col items-center text-center">
          <div class="flex size-12 items-center justify-center rounded-full bg-red-100 text-red-600">
            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
          </div>
          <h3 class="mt-4 text-lg font-semibold text-slate-900">Hapus Template</h3>
          <p class="mt-2 text-sm text-slate-500">Apakah Anda yakin ingin menghapus template <strong id="delete-template-name" class="text-slate-700"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
        </div>

        <form id="delete-form" method="POST" class="mt-6 flex justify-center gap-3">
          @csrf
          @method('DELETE')
          <button type="button" onclick="closeDeleteModal()" class="rounded-xl border border-border px-5 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50">Batal</button>
          <button type="submit" class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-700">Hapus</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    // ==================== CREATE MODAL ====================
    function openCreateModal() {
      const modal = document.getElementById('template-modal');
      const form = document.getElementById('template-form');
      const methodInput = document.getElementById('form-method');

      document.getElementById('modal-title').textContent = 'Buat Template Baru';
      document.getElementById('modal-submit-btn').textContent = 'Simpan Template';

      form.action = '{{ route("dashboard.templates.store") }}';
      methodInput.value = 'POST';

      // Reset fields
      document.getElementById('template-name').value = '';
      document.getElementById('template-category').value = '';
      document.getElementById('template-body').value = '';
      document.getElementById('template-status').value = 'draft';

      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    // ==================== EDIT MODAL ====================
    function openEditModal(id, data) {
      const modal = document.getElementById('template-modal');
      const form = document.getElementById('template-form');
      const methodInput = document.getElementById('form-method');

      document.getElementById('modal-title').textContent = 'Edit Template';
      document.getElementById('modal-submit-btn').textContent = 'Perbarui Template';

      form.action = '/dashboard/templates/' + id;
      methodInput.value = 'PUT';

      document.getElementById('template-name').value = data.name;
      document.getElementById('template-category').value = data.category;
      document.getElementById('template-body').value = data.body;
      document.getElementById('template-status').value = data.status;

      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeTemplateModal() {
      document.getElementById('template-modal').classList.add('hidden');
      document.body.style.overflow = '';
    }

    // ==================== PREVIEW MODAL ====================
    function openPreviewModal(id) {
      const modal = document.getElementById('preview-modal');

      // Show loading state
      document.getElementById('preview-name').textContent = 'Memuat...';
      document.getElementById('preview-category').textContent = '';
      document.getElementById('preview-body').textContent = '';
      document.getElementById('preview-parsed').textContent = '';
      document.getElementById('preview-variables').innerHTML = '';

      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';

      fetch('/dashboard/templates/' + id + '/preview', {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      })
      .then(response => response.json())
      .then(data => {
        document.getElementById('preview-name').textContent = data.name;
        document.getElementById('preview-category').textContent = data.category;
        document.getElementById('preview-body').textContent = data.body;
        document.getElementById('preview-parsed').textContent = data.parsed;

        const variablesContainer = document.getElementById('preview-variables');
        variablesContainer.innerHTML = '';

        if (data.variables && Object.keys(data.variables).length > 0) {
          Object.entries(data.variables).forEach(([key, value]) => {
            const tag = document.createElement('span');
            tag.className = 'inline-flex items-center gap-1 rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-700';
            tag.innerHTML = '<code>{' + key + '}</code> → ' + value;
            variablesContainer.appendChild(tag);
          });
        } else {
          variablesContainer.innerHTML = '<span class="text-xs text-slate-400">Tidak ada variabel</span>';
        }
      })
      .catch(() => {
        document.getElementById('preview-name').textContent = 'Gagal memuat preview';
      });
    }

    function closePreviewModal() {
      document.getElementById('preview-modal').classList.add('hidden');
      document.body.style.overflow = '';
    }

    // ==================== DELETE MODAL ====================
    function openDeleteModal(id, name) {
      const modal = document.getElementById('delete-modal');
      const form = document.getElementById('delete-form');

      form.action = '/dashboard/templates/' + id;
      document.getElementById('delete-template-name').textContent = '"' + name + '"';

      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
      document.getElementById('delete-modal').classList.add('hidden');
      document.body.style.overflow = '';
    }

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeTemplateModal();
        closePreviewModal();
        closeDeleteModal();
      }
    });

    // Auto-hide flash message
    const flash = document.getElementById('flash-message');
    if (flash) {
      setTimeout(() => {
        flash.style.transition = 'opacity 500ms';
        flash.style.opacity = '0';
        setTimeout(() => flash.remove(), 500);
      }, 4000);
    }
  </script>

</x-layouts.dashboard>
