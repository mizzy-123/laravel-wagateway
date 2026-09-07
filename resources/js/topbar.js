const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const debounce = (fn, wait = 300) => {
    let timer;
    return (...args) => {
        window.clearTimeout(timer);
        timer = window.setTimeout(() => fn(...args), wait);
    };
};

const escapeHtml = (value) => String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

const initTopbarSearch = () => {
    const root = document.getElementById('topbar-search');
    if (!root) return;

    const input = document.getElementById('topbar-search-input');
    const results = document.getElementById('topbar-search-results');
    const panel = document.getElementById('topbar-search-panel');
    const toggle = document.getElementById('topbar-search-toggle');
    const url = root.dataset.searchUrl;

    if (!input || !results || !url) return;

    const showResults = () => results.classList.remove('hidden');
    const hideResults = () => results.classList.add('hidden');

    const renderEmpty = (message) => {
        results.innerHTML = `<p class="px-3 py-6 text-center text-sm text-slate-400">${escapeHtml(message)}</p>`;
        showResults();
    };

    const renderGroups = (groups) => {
        if (!groups.length) {
            renderEmpty('Tidak ada hasil ditemukan.');
            return;
        }

        results.innerHTML = groups.map((group) => `
            <div class="mb-2 last:mb-0">
                <p class="px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-slate-400">${escapeHtml(group.label)}</p>
                ${group.items.map((item) => `
                    <a href="${escapeHtml(item.url)}" class="block rounded-xl px-3 py-2 transition-colors hover:bg-slate-50">
                        <p class="text-sm font-medium text-slate-900">${escapeHtml(item.title)}</p>
                        <p class="truncate text-xs text-slate-500">${escapeHtml(item.subtitle)}</p>
                        <p class="mt-0.5 text-[11px] text-slate-400">${escapeHtml(item.meta)}</p>
                    </a>
                `).join('')}
            </div>
        `).join('');
        showResults();
    };

    const runSearch = async (query) => {
        if (query.length < 2) {
            renderEmpty('Ketik minimal 2 karakter...');
            return;
        }

        results.innerHTML = `<p class="px-3 py-6 text-center text-sm text-slate-400">Mencari...</p>`;
        showResults();

        try {
            const response = await fetch(`${url}?q=${encodeURIComponent(query)}`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Gagal mencari.');
            }

            const data = await response.json();
            renderGroups(data.groups || []);
        } catch (error) {
            renderEmpty(error.message || 'Gagal mencari.');
        }
    };

    const debouncedSearch = debounce((value) => runSearch(value.trim()), 300);

    input.addEventListener('input', (event) => {
        debouncedSearch(event.target.value);
    });

    input.addEventListener('focus', () => {
        if (input.value.trim().length >= 2 || results.childElementCount > 0) {
            showResults();
        }
    });

    toggle?.addEventListener('click', () => {
        panel?.classList.toggle('hidden');
        if (!panel?.classList.contains('hidden')) {
            input.focus();
        }
    });

    document.addEventListener('click', (event) => {
        if (!root.contains(event.target)) {
            hideResults();
            if (window.innerWidth < 640) {
                panel?.classList.add('hidden');
            }
        }
    });
};

const initTopbarNotifications = () => {
    const root = document.getElementById('topbar-notifications');
    if (!root) return;

    const toggle = document.getElementById('topbar-notifications-toggle');
    const panel = document.getElementById('topbar-notifications-panel');
    const list = document.getElementById('topbar-notifications-list');
    const badge = document.getElementById('topbar-notifications-badge');
    const subtitle = document.getElementById('topbar-notifications-subtitle');
    const markReadBtn = document.getElementById('topbar-notifications-mark-read');
    const url = root.dataset.notificationsUrl;
    const readUrl = root.dataset.notificationsReadUrl;

    if (!toggle || !panel || !list || !url) return;

    let open = false;

    const setBadge = (count) => {
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : String(count);
            badge.classList.remove('hidden');
            badge.classList.add('flex');
        } else {
            badge.classList.add('hidden');
            badge.classList.remove('flex');
        }
    };

    const renderItems = (items, unreadCount) => {
        if (subtitle) {
            subtitle.textContent = unreadCount > 0
                ? `${unreadCount} belum dibaca`
                : (items.length ? 'Semua sudah dibaca' : 'Tidak ada notifikasi');
        }

        if (!items.length) {
            list.innerHTML = `<p class="px-4 py-8 text-center text-sm text-slate-400">Tidak ada notifikasi saat ini.</p>`;
            return;
        }

        list.innerHTML = items.map((item) => `
            <a href="${escapeHtml(item.url)}" class="block border-b border-border px-4 py-3 transition-colors hover:bg-slate-50 last:border-b-0">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-slate-900">${escapeHtml(item.title)}</p>
                        <p class="mt-0.5 text-xs text-slate-500">${escapeHtml(item.body)}</p>
                    </div>
                    <span class="shrink-0 text-[11px] text-slate-400 whitespace-nowrap">${escapeHtml(item.time_label)}</span>
                </div>
            </a>
        `).join('');
    };

    const loadNotifications = async () => {
        try {
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Gagal memuat notifikasi.');
            }

            const data = await response.json();
            setBadge(data.unread_count || 0);
            renderItems(data.items || [], data.unread_count || 0);
        } catch (error) {
            if (subtitle) subtitle.textContent = 'Gagal memuat';
            list.innerHTML = `<p class="px-4 py-8 text-center text-sm text-red-500">${escapeHtml(error.message || 'Gagal memuat notifikasi.')}</p>`;
        }
    };

    const markAllRead = async () => {
        if (!readUrl) return;

        try {
            const response = await fetch(readUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken() || '',
                },
                body: JSON.stringify({}),
            });

            if (!response.ok) {
                throw new Error('Gagal menandai notifikasi.');
            }

            setBadge(0);
            if (subtitle) subtitle.textContent = 'Semua sudah dibaca';
        } catch (error) {
            alert(error.message || 'Gagal menandai notifikasi.');
        }
    };

    const setOpen = (nextOpen) => {
        open = nextOpen;
        panel.classList.toggle('hidden', !open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) {
            loadNotifications();
        }
    };

    toggle.addEventListener('click', (event) => {
        event.stopPropagation();
        setOpen(!open);
    });

    markReadBtn?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        markAllRead();
    });

    document.addEventListener('click', (event) => {
        if (!root.contains(event.target)) {
            setOpen(false);
        }
    });

    loadNotifications();
    window.setInterval(loadNotifications, 60000);
};

document.addEventListener('DOMContentLoaded', () => {
    initTopbarSearch();
    initTopbarNotifications();
});
