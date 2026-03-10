<x-app-layout>
    <x-slot name="header">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Exo+2:wght@300;400;600;700;900&display=swap');

            :root {
                --cyan: #00f5ff;
                --cyan-dim: #00b8c4;
                --green: #00ff88;
                --red: #ff3366;
                --amber: #ffaa00;
                --bg: #080c10;
                --bg2: #0d1117;
                --bg3: #111820;
                --border: #1a2535;
                --border-glow: rgba(0,245,255,0.25);
                --text: #c8d8e8;
                --text-dim: #5a7080;
            }

            * { box-sizing: border-box; }

            body {
                background: var(--bg) !important;
                font-family: 'Exo 2', sans-serif !important;
                color: var(--text) !important;
            }

            /* Scanline overlay */
            body::before {
                content: '';
                position: fixed;
                inset: 0;
                background: repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 2px,
                    rgba(0,0,0,0.08) 2px,
                    rgba(0,0,0,0.08) 4px
                );
                pointer-events: none;
                z-index: 9999;
            }

            /* Ambient glow blobs */
            body::after {
                content: '';
                position: fixed;
                inset: 0;
                background:
                    radial-gradient(ellipse 600px 400px at 15% 20%, rgba(0,245,255,0.04) 0%, transparent 70%),
                    radial-gradient(ellipse 500px 350px at 85% 75%, rgba(0,255,136,0.03) 0%, transparent 70%);
                pointer-events: none;
                z-index: 0;
            }

            /* Header override */
            header, nav {
                background: rgba(8,12,16,0.95) !important;
                border-bottom: 1px solid var(--border) !important;
                backdrop-filter: blur(12px) !important;
            }

            .notif-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0;
            }

            .notif-title {
                font-family: 'Share Tech Mono', monospace;
                font-size: 1.1rem;
                color: var(--cyan);
                text-shadow: 0 0 12px rgba(0,245,255,0.6);
                letter-spacing: 0.08em;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .notif-title::before {
                content: '▶';
                font-size: 0.7em;
                animation: blink 1.2s step-end infinite;
            }

            @keyframes blink {
                0%, 100% { opacity: 1; }
                50% { opacity: 0; }
            }

            .filter-group {
                display: flex;
                gap: 8px;
            }

            .filter-btn {
                font-family: 'Share Tech Mono', monospace !important;
                font-size: 0.72rem !important;
                letter-spacing: 0.1em;
                padding: 6px 16px !important;
                border-radius: 2px !important;
                border: 1px solid !important;
                cursor: pointer;
                transition: all 0.2s ease !important;
                text-transform: uppercase;
                position: relative;
                overflow: hidden;
            }

            .filter-btn::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, rgba(255,255,255,0.05), transparent);
                opacity: 0;
                transition: opacity 0.2s;
            }

            .filter-btn:hover::after { opacity: 1; }

            .filter-btn.active {
                background: rgba(0,245,255,0.12) !important;
                border-color: var(--cyan) !important;
                color: var(--cyan) !important;
                box-shadow: 0 0 12px rgba(0,245,255,0.3), inset 0 0 8px rgba(0,245,255,0.05) !important;
                text-shadow: 0 0 8px var(--cyan);
            }

            .filter-btn.inactive {
                background: transparent !important;
                border-color: var(--border) !important;
                color: var(--text-dim) !important;
            }

            .filter-btn.inactive:hover {
                border-color: rgba(0,245,255,0.4) !important;
                color: var(--text) !important;
            }

            /* Main panel */
            .notif-panel {
                background: var(--bg2) !important;
                border: 1px solid var(--border) !important;
                border-radius: 4px !important;
                box-shadow:
                    0 0 0 1px rgba(0,245,255,0.04),
                    0 20px 60px rgba(0,0,0,0.6),
                    inset 0 1px 0 rgba(255,255,255,0.03) !important;
                position: relative;
                overflow: hidden;
            }

            .notif-panel::before {
                content: '';
                position: absolute;
                top: 0; left: 0; right: 0;
                height: 1px;
                background: linear-gradient(90deg, transparent, var(--cyan), transparent);
                opacity: 0.5;
            }

            .meta-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding-bottom: 12px;
                border-bottom: 1px solid var(--border);
                margin-bottom: 4px;
            }

            .unread-badge {
                font-family: 'Share Tech Mono', monospace;
                font-size: 0.75rem;
                color: var(--text-dim);
                letter-spacing: 0.05em;
            }

            .unread-badge span {
                color: var(--green);
                text-shadow: 0 0 8px rgba(0,255,136,0.5);
                font-weight: 700;
            }

            .loading-indicator {
                font-family: 'Share Tech Mono', monospace;
                font-size: 0.72rem;
                color: var(--cyan-dim);
                animation: pulse 1s ease-in-out infinite;
            }

            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.3; }
            }

            /* Notification item */
            .notif-item {
                width: 100%;
                text-align: left;
                border-radius: 3px;
                padding: 14px 16px;
                border: 1px solid;
                cursor: pointer;
                transition: all 0.2s ease;
                position: relative;
                overflow: hidden;
                animation: slideIn 0.35s ease forwards;
                opacity: 0;
                transform: translateX(-12px);
                background: none;
                font-family: 'Exo 2', sans-serif;
            }

            @keyframes slideIn {
                to { opacity: 1; transform: translateX(0); }
            }

            .notif-item.unread {
                border-color: rgba(0,245,255,0.2);
                background: rgba(0,245,255,0.04) !important;
            }

            .notif-item.unread::before {
                content: '';
                position: absolute;
                left: 0; top: 0; bottom: 0;
                width: 2px;
                background: var(--cyan);
                box-shadow: 0 0 8px var(--cyan);
            }

            .notif-item.read {
                border-color: var(--border);
                background: rgba(255,255,255,0.01) !important;
            }

            .notif-item:hover {
                border-color: rgba(0,245,255,0.35) !important;
                background: rgba(0,245,255,0.06) !important;
                box-shadow: 0 0 20px rgba(0,245,255,0.07);
                transform: translateX(3px);
            }

            .notif-item:hover::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(90deg, rgba(0,245,255,0.03), transparent 60%);
                pointer-events: none;
            }

            .notif-title-text {
                font-weight: 700;
                font-size: 0.88rem;
                color: #e0eaf4;
                letter-spacing: 0.02em;
            }

            .notif-message {
                font-size: 0.82rem;
                color: var(--text-dim);
                margin-top: 4px;
                line-height: 1.5;
            }

            .notif-date {
                font-family: 'Share Tech Mono', monospace;
                font-size: 0.68rem;
                color: #334455;
                margin-top: 8px;
                letter-spacing: 0.04em;
            }

            .status-label {
                font-family: 'Share Tech Mono', monospace;
                font-size: 0.65rem;
                letter-spacing: 0.08em;
                color: var(--text-dim);
                text-transform: uppercase;
            }

            .unread-dot {
                display: inline-flex;
                width: 7px;
                height: 7px;
                border-radius: 50%;
                background: var(--green);
                box-shadow: 0 0 6px var(--green);
                animation: dotPulse 2s ease-in-out infinite;
            }

            @keyframes dotPulse {
                0%, 100% { box-shadow: 0 0 4px var(--green); }
                50% { box-shadow: 0 0 12px var(--green), 0 0 20px rgba(0,255,136,0.3); }
            }

            /* Badges */
            .badge {
                font-family: 'Share Tech Mono', monospace;
                font-size: 0.62rem;
                letter-spacing: 0.06em;
                padding: 2px 8px;
                border-radius: 2px;
                text-transform: uppercase;
                border: 1px solid;
            }

            .badge-info {
                color: var(--cyan);
                border-color: rgba(0,245,255,0.3);
                background: rgba(0,245,255,0.07);
            }

            .badge-warning {
                color: var(--amber);
                border-color: rgba(255,170,0,0.3);
                background: rgba(255,170,0,0.07);
            }

            .badge-alert {
                color: var(--red);
                border-color: rgba(255,51,102,0.3);
                background: rgba(255,51,102,0.07);
            }

            /* Load more btn */
            #load-more {
                font-family: 'Share Tech Mono', monospace !important;
                font-size: 0.75rem !important;
                letter-spacing: 0.1em;
                text-transform: uppercase;
                padding: 10px 32px !important;
                border-radius: 2px !important;
                border: 1px solid var(--border) !important;
                background: transparent !important;
                color: var(--text-dim) !important;
                cursor: pointer;
                transition: all 0.2s ease !important;
                position: relative;
                overflow: hidden;
            }

            #load-more:hover {
                border-color: var(--cyan) !important;
                color: var(--cyan) !important;
                box-shadow: 0 0 16px rgba(0,245,255,0.2) !important;
                text-shadow: 0 0 8px var(--cyan);
            }

            #load-more::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, rgba(0,245,255,0.06), transparent);
                opacity: 0;
                transition: opacity 0.2s;
            }

            #load-more:hover::before { opacity: 1; }

            /* Empty state */
            #empty {
                font-family: 'Share Tech Mono', monospace;
                font-size: 0.82rem;
                color: var(--text-dim);
                letter-spacing: 0.04em;
                padding: 40px 0;
            }

            #empty::before {
                content: '// ';
                color: var(--border);
            }

            /* Glitch on page load */
            @keyframes glitch {
                0% { clip-path: inset(0 0 98% 0); transform: translateX(-4px); }
                10% { clip-path: inset(40% 0 50% 0); transform: translateX(4px); }
                20% { clip-path: inset(70% 0 20% 0); transform: translateX(-2px); }
                30% { clip-path: inset(10% 0 80% 0); transform: translateX(3px); }
                40% { clip-path: inset(0 0 0 0); transform: translateX(0); opacity: 0; }
                100% { clip-path: inset(0 0 0 0); transform: translateX(0); opacity: 0; }
            }

            .glitch-layer {
                position: fixed;
                inset: 0;
                background: linear-gradient(135deg, rgba(0,245,255,0.08), transparent);
                pointer-events: none;
                z-index: 9998;
                animation: glitch 0.6s ease forwards;
            }

            /* Scrollbar */
            #notif-list::-webkit-scrollbar { width: 3px; }
            #notif-list::-webkit-scrollbar-track { background: transparent; }
            #notif-list::-webkit-scrollbar-thumb {
                background: var(--border);
                border-radius: 2px;
            }
            #notif-list::-webkit-scrollbar-thumb:hover {
                background: rgba(0,245,255,0.3);
            }

            /* Page container */
            .py-8 { padding-top: 2rem !important; padding-bottom: 2rem !important; }
        </style>

        <div class="notif-header">
            <h2 class="notif-title">NOTIFICATIONS</h2>
            <div class="filter-group">
                <button id="filter-all" type="button" class="filter-btn active">All</button>
                <button id="filter-unread" type="button" class="filter-btn inactive">Unread</button>
            </div>
        </div>
    </x-slot>

    <div class="glitch-layer"></div>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="notif-panel">
                <div class="p-6">
                    <div class="meta-row">
                        <div class="unread-badge">
                            <span id="unread-count">0</span> unread signals
                        </div>
                        <div id="loading" class="loading-indicator hidden">// scanning…</div>
                    </div>

                    <div id="notif-list" class="mt-4 max-h-[70vh] overflow-y-auto space-y-2 pr-1"></div>

                    <div class="mt-5 flex justify-center">
                        <button id="load-more" type="button" class="hidden">
                            ▼ Load more
                        </button>
                    </div>

                    <div id="empty" class="hidden mt-6 text-center">
                        No notifications found.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const listEl = document.getElementById('notif-list');
            const emptyEl = document.getElementById('empty');
            const loadingEl = document.getElementById('loading');
            const loadMoreBtn = document.getElementById('load-more');
            const unreadCountEl = document.getElementById('unread-count');
            const filterAllBtn = document.getElementById('filter-all');
            const filterUnreadBtn = document.getElementById('filter-unread');

            let filter = 'all';
            let page = 1;
            let lastPage = 1;
            let loading = false;
            let itemIndex = 0;

            const setLoading = (value) => {
                loading = value;
                loadingEl.classList.toggle('hidden', !value);
                loadMoreBtn.disabled = value;
                loadMoreBtn.classList.toggle('opacity-60', value);
                loadMoreBtn.classList.toggle('cursor-not-allowed', value);
            };

            const setFilterStyles = () => {
                if (filter === 'unread') {
                    filterUnreadBtn.className = 'filter-btn active';
                    filterAllBtn.className = 'filter-btn inactive';
                } else {
                    filterAllBtn.className = 'filter-btn active';
                    filterUnreadBtn.className = 'filter-btn inactive';
                }
            };

            const formatDateTime = (iso) => {
                if (!iso) return '';
                try {
                    return new Intl.DateTimeFormat(undefined, {
                        year: 'numeric', month: 'short', day: '2-digit',
                        hour: '2-digit', minute: '2-digit',
                    }).format(new Date(iso));
                } catch (e) { return ''; }
            };

            const typeBadge = (type) => {
                const t = (type || 'info').toLowerCase();
                if (t === 'warning') return `<span class="badge badge-warning">⚠ warning</span>`;
                if (['alert','error','danger'].includes(t)) return `<span class="badge badge-alert">✕ alert</span>`;
                return `<span class="badge badge-info">ℹ info</span>`;
            };

            const escapeHtml = (value) => {
                const div = document.createElement('div');
                div.textContent = value == null ? '' : String(value);
                return div.innerHTML;
            };

            const renderNotification = (n) => {
                const unread = !n.is_read;
                const wrapper = document.createElement('button');
                wrapper.type = 'button';
                wrapper.className = `notif-item ${unread ? 'unread' : 'read'}`;
                wrapper.style.animationDelay = `${(itemIndex % 25) * 40}ms`;
                itemIndex++;

                const title = escapeHtml(n.title || 'Notification');
                const message = escapeHtml(n.message || '');
                const created = escapeHtml(formatDateTime(n.created_at));
                const badge = typeBadge(n.type);

                wrapper.innerHTML = `
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="notif-title-text">${title}</span>
                                ${badge}
                                ${unread ? '<span class="unread-dot" aria-label="Unread"></span>' : ''}
                            </div>
                            ${message ? `<div class="notif-message">${message}</div>` : ''}
                            <div class="notif-date">${created}</div>
                        </div>
                        <div class="shrink-0 status-label">${unread ? 'NEW' : '---'}</div>
                    </div>
                `;

                wrapper.addEventListener('click', async () => {
                    if (loading) return;
                    if (!n.is_read) {
                        await markRead(n.id);
                        n.is_read = true;
                        wrapper.classList.remove('unread');
                        wrapper.classList.add('read');
                        const dot = wrapper.querySelector('[aria-label="Unread"]');
                        if (dot) dot.remove();
                        const leftBar = wrapper.querySelector('::before');
                        const status = wrapper.querySelector('.status-label');
                        if (status) status.textContent = '---';
                    }
                    if (n.action_url) window.location.assign(n.action_url);
                });

                listEl.appendChild(wrapper);
            };

            const fetchPage = async ({ reset = false } = {}) => {
                if (loading) return;
                if (reset) {
                    listEl.innerHTML = '';
                    emptyEl.classList.add('hidden');
                    page = 1;
                    lastPage = 1;
                    itemIndex = 0;
                }

                setLoading(true);

                try {
                    const res = await fetch(`/notifications?filter=${encodeURIComponent(filter)}&page=${page}&per_page=25`, {
                        headers: { 'Accept': 'application/json' },
                    });
                    const payload = await res.json();
                    if (!res.ok) throw new Error(payload?.message || 'Failed to load notifications.');

                    const items = Array.isArray(payload?.data) ? payload.data : [];
                    const meta = payload?.meta || {};

                    unreadCountEl.textContent = String(meta.unread_count ?? 0);
                    lastPage = Number(meta.last_page || 1);

                    items.forEach(renderNotification);

                    const totalButtons = listEl.querySelectorAll('button').length;
                    emptyEl.classList.toggle('hidden', totalButtons > 0);
                    loadMoreBtn.classList.toggle('hidden', page >= lastPage);
                } catch (e) {
                    emptyEl.classList.remove('hidden');
                    emptyEl.textContent = 'Connection lost. Refresh to retry.';
                } finally {
                    setLoading(false);
                }
            };

            const markRead = async (id) => {
                try {
                    const res = await fetch(`/notifications/${encodeURIComponent(id)}/read`, {
                        method: 'PATCH',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    });
                    const payload = await res.json();
                    if (res.ok) {
                        const unread = payload?.meta?.unread_count;
                        if (typeof unread !== 'undefined') unreadCountEl.textContent = String(unread);
                    }
                } catch (e) {}
            };

            filterAllBtn.addEventListener('click', async () => { filter = 'all'; setFilterStyles(); await fetchPage({ reset: true }); });
            filterUnreadBtn.addEventListener('click', async () => { filter = 'unread'; setFilterStyles(); await fetchPage({ reset: true }); });
            loadMoreBtn.addEventListener('click', async () => { if (page >= lastPage) return; page += 1; await fetchPage(); });

            setFilterStyles();
            fetchPage({ reset: true });
        })();
    </script>
</x-app-layout>