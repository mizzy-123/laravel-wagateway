@props([
    'title' => 'Dashboard',
    'subtitle' => null,
])

<header class="sticky top-0 z-30 border-b border-border bg-surface-card/80 backdrop-blur-md">
    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-4">
            <button
                id="sidebar-open"
                type="button"
                class="rounded-lg border border-border bg-white p-2 text-slate-600 shadow-sm hover:bg-slate-50 lg:hidden"
            >
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            <div class="min-w-0">
                <h1 class="truncate text-lg font-semibold text-slate-900 sm:text-xl">{{ $title }}</h1>
                @if ($subtitle)
                    <p class="truncate text-sm text-slate-500">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            {{-- Search --}}
            <div class="relative" id="topbar-search" data-search-url="{{ route('dashboard.search') }}">
                <button
                    type="button"
                    id="topbar-search-toggle"
                    class="rounded-xl border border-border bg-white p-2 text-slate-600 shadow-sm hover:bg-slate-50 sm:hidden"
                    aria-label="Cari"
                >
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </button>

                <div id="topbar-search-panel" class="absolute right-0 top-full z-40 mt-2 hidden w-[min(100vw-2rem,22rem)] sm:relative sm:top-0 sm:mt-0 sm:block sm:w-auto">
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input
                            id="topbar-search-input"
                            type="search"
                            placeholder="Cari pesan, pasien, perangkat..."
                            autocomplete="off"
                            class="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-4 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 sm:w-48 sm:bg-slate-50 sm:shadow-none lg:w-64"
                        >
                    </div>

                    <div id="topbar-search-results" class="absolute left-0 right-0 top-full z-50 mt-2 hidden max-h-96 overflow-y-auto rounded-2xl border border-border bg-white p-2 shadow-lg">
                        <p class="px-3 py-6 text-center text-sm text-slate-400" data-search-empty>Ketik minimal 2 karakter...</p>
                    </div>
                </div>
            </div>

            {{-- Notifications --}}
            <div
                class="relative"
                id="topbar-notifications"
                data-notifications-url="{{ route('dashboard.notifications') }}"
                data-notifications-read-url="{{ route('dashboard.notifications.read') }}"
            >
                <button
                    type="button"
                    id="topbar-notifications-toggle"
                    class="relative rounded-xl border border-border bg-white p-2 text-slate-600 shadow-sm hover:bg-slate-50"
                    aria-label="Notifikasi"
                    aria-expanded="false"
                >
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <span
                        id="topbar-notifications-badge"
                        class="absolute -right-0.5 -top-0.5 hidden min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-4 text-white"
                    >0</span>
                </button>

                <div
                    id="topbar-notifications-panel"
                    class="absolute right-0 top-full z-50 mt-2 hidden w-[min(100vw-2rem,22rem)] overflow-hidden rounded-2xl border border-border bg-white shadow-lg"
                >
                    <div class="flex items-center justify-between border-b border-border px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Notifikasi</p>
                            <p class="text-xs text-slate-500" id="topbar-notifications-subtitle">Memuat...</p>
                        </div>
                        <button
                            type="button"
                            id="topbar-notifications-mark-read"
                            class="text-xs font-medium text-brand-600 hover:text-brand-700"
                        >
                            Tandai dibaca
                        </button>
                    </div>
                    <div id="topbar-notifications-list" class="max-h-80 overflow-y-auto">
                        <p class="px-4 py-8 text-center text-sm text-slate-400">Memuat notifikasi...</p>
                    </div>
                </div>
            </div>

            {{-- Quick action --}}
            <a href="{{ route('dashboard.send') }}" class="hidden items-center gap-2 rounded-xl bg-brand-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-700 sm:flex">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Kirim Pesan
            </a>
        </div>
    </div>
</header>
