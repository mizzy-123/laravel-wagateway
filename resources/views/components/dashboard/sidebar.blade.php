<aside
    id="sidebar"
    class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col gradient-brand transition-transform duration-300 lg:translate-x-0"
>
    {{-- Logo --}}
    <div class="flex h-16 items-center justify-between gap-3 border-b border-white/10 px-5">
        <div class="flex items-center gap-3">
            <div class="flex size-10 items-center justify-center rounded-xl bg-white/15 backdrop-blur-sm">
                <svg class="size-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold leading-tight text-white">RS Roemani</p>
                <p class="text-xs text-brand-200">WA Gateway</p>
            </div>
        </div>
        <button id="sidebar-close" type="button" class="rounded-lg p-1.5 text-brand-200 hover:bg-white/10 hover:text-white lg:hidden">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-brand-300">Menu Utama</p>

        <x-dashboard.nav-link
            :href="route('dashboard.index')"
            :active="request()->routeIs('dashboard.index')"
            icon="home"
        >
            Dashboard
        </x-dashboard.nav-link>

        <x-dashboard.nav-link
            :href="route('dashboard.devices')"
            :active="request()->routeIs('dashboard.devices')"
            icon="device"
        >
            Perangkat WA
        </x-dashboard.nav-link>

        <x-dashboard.nav-link
            :href="route('dashboard.messages')"
            :active="request()->routeIs('dashboard.messages')"
            icon="message"
        >
            Pesan
        </x-dashboard.nav-link>

        <x-dashboard.nav-link
            :href="route('dashboard.templates')"
            :active="request()->routeIs('dashboard.templates')"
            icon="template"
        >
            Template Pesan
        </x-dashboard.nav-link>

        <x-dashboard.nav-link
            :href="route('dashboard.send')"
            :active="request()->routeIs('dashboard.send')"
            icon="send"
        >
            Kirim Pesan
        </x-dashboard.nav-link>

        <x-dashboard.nav-link
            :href="route('dashboard.blasts')"
            :active="request()->routeIs('dashboard.blasts*')"
            icon="blast"
        >
            Riwayat Blast
        </x-dashboard.nav-link>

        <div class="my-4 border-t border-white/10"></div>

        <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-brand-300">Sistem</p>

        <x-dashboard.nav-link
            :href="route('dashboard.settings')"
            :active="request()->routeIs('dashboard.settings*')"
            icon="settings"
        >
            Pengaturan
        </x-dashboard.nav-link>
    </nav>

    {{-- Footer --}}
    <div class="border-t border-white/10 p-4">
        <div class="flex items-center gap-3 rounded-xl bg-white/10 p-3 backdrop-blur-sm">
            <div class="flex size-9 items-center justify-center rounded-full bg-wa text-sm font-bold text-white">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name ?? 'Administrator' }}</p>
                <p class="truncate text-xs text-brand-200">{{ auth()->user()->email ?? 'admin@roemani.co.id' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-lg p-1.5 text-brand-200 transition-colors hover:bg-white/10 hover:text-white" title="Keluar">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
