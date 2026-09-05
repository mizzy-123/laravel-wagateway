@props([
    'title' => 'Dashboard',
    'subtitle' => null,
])

<header class="sticky top-0 z-30 border-b border-border bg-surface-card/80 backdrop-blur-md">
    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4">
            <button
                id="sidebar-open"
                type="button"
                class="rounded-lg border border-border bg-white p-2 text-slate-600 shadow-sm hover:bg-slate-50 lg:hidden"
            >
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            <div>
                <h1 class="text-lg font-semibold text-slate-900 sm:text-xl">{{ $title }}</h1>
                @if ($subtitle)
                    <p class="text-sm text-slate-500">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            {{-- Search --}}
            <div class="hidden sm:block">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input
                        type="search"
                        placeholder="Cari pesan, pasien..."
                        class="w-48 rounded-xl border border-border bg-slate-50 py-2 pl-9 pr-4 text-sm text-slate-700 placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 lg:w-64"
                    >
                </div>
            </div>

            {{-- Notifications --}}
            <button type="button" class="relative rounded-xl border border-border bg-white p-2 text-slate-600 shadow-sm hover:bg-slate-50">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                <span class="absolute -right-0.5 -top-0.5 flex size-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">3</span>
            </button>

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
