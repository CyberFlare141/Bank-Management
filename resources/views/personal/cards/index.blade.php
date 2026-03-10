<x-app-layout>
    @php
        $cards = [
            [
                'type' => 'debit',
                'title' => 'Debit Card',
                'desc' => 'Direct account access for daily purchases, ATM withdrawals, and secure online payments.',
                'features' => ['No annual fee', 'Real-time spending alerts', 'ATM + POS enabled'],
                'eligibility' => 'Requires an active account.',
                'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                'color_from' => '#1d4ed8',
                'color_to' => '#0ea5e9',
                'chip' => 'Free',
            ],
            [
                'type' => 'credit',
                'title' => 'Credit Card',
                'desc' => 'Flexible spending power with rewards, statement billing cycle, and purchase protection.',
                'features' => ['Reward points', 'Interest-free period', 'Enhanced fraud monitoring'],
                'eligibility' => 'Requires income and employment verification.',
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'color_from' => '#7c3aed',
                'color_to' => '#ec4899',
                'chip' => 'Rewards',
            ],
        ];

        $statusMap = [
            'pending_review' => ['label' => 'Pending Review', 'class' => 'pill-pending'],
            'approved'       => ['label' => 'Approved',       'class' => 'pill-approved'],
            'rejected'       => ['label' => 'Rejected',       'class' => 'pill-rejected'],
            'processing'     => ['label' => 'Processing',     'class' => 'pill-processing'],
        ];
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,400&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #020810;
            --surface: rgba(255,255,255,0.025);
            --border: rgba(100,160,255,0.1);
            --border-h: rgba(100,160,255,0.3);
            --text: #e4eeff;
            --muted: #4d6b99;
            --label: #6b93cc;
            --accent: #3b82f6;
            --accent2: #0ea5e9;
        }

        .cm-root {
            min-height: 100vh;
            background: var(--bg);
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            padding: 2.5rem 1.5rem 5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient blobs */
        .cm-blob {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            filter: blur(0px);
        }
        .cm-blob-1 {
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(59,130,246,0.09) 0%, transparent 65%);
            top: -250px; right: -200px;
            animation: b1 14s ease-in-out infinite alternate;
        }
        .cm-blob-2 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(124,58,237,0.07) 0%, transparent 65%);
            bottom: -100px; left: -150px;
            animation: b2 18s ease-in-out infinite alternate;
        }
        .cm-blob-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(236,72,153,0.05) 0%, transparent 65%);
            top: 50%; left: 50%;
            animation: b3 20s ease-in-out infinite alternate;
        }
        @keyframes b1 { to { transform: translate(-80px, 100px) scale(1.1); } }
        @keyframes b2 { to { transform: translate(60px, -80px) scale(1.15); } }
        @keyframes b3 { to { transform: translate(-120px, 80px) scale(0.9); } }

        /* Noise grain overlay */
        .cm-root::after {
            content: '';
            position: fixed; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
            opacity: 0.4;
        }

        .cm-wrap {
            max-width: 1040px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* ── Header ── */
        .cm-header {
            margin-bottom: 2.75rem;
            animation: fadeUp 0.65s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes fadeUp { from { opacity:0; transform:translateY(22px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

        .cm-eyebrow {
            display: inline-flex; align-items: center; gap: 0.45rem;
            font-size: 0.68rem; font-weight: 600; letter-spacing: 0.18em; text-transform: uppercase;
            color: var(--accent);
            background: rgba(59,130,246,0.08);
            border: 1px solid rgba(59,130,246,0.25);
            border-radius: 100px; padding: 0.3rem 0.8rem;
            margin-bottom: 0.8rem;
        }
        .cm-eyebrow-dot {
            width: 5px; height: 5px; border-radius: 50%;
            background: var(--accent); box-shadow: 0 0 6px var(--accent);
            animation: blink 2s ease-in-out infinite;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }

        .cm-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1.05;
            background: linear-gradient(140deg, #fff 20%, #6baeff 60%, #a78bfa 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .cm-sub {
            font-size: 0.88rem; color: var(--muted); margin-top: 0.55rem; line-height: 1.65;
        }

        /* ── Alerts ── */
        .cm-alert {
            display: flex; align-items: flex-start; gap: 0.7rem;
            border-radius: 12px; padding: 0.85rem 1rem;
            font-size: 0.82rem; line-height: 1.5;
            margin-bottom: 1rem;
            animation: fadeUp 0.4s cubic-bezier(.22,1,.36,1) both;
        }
        .cm-alert.ok { background: rgba(34,197,94,0.07); border: 1px solid rgba(34,197,94,0.25); color: #86efac; }
        .cm-alert.err { background: rgba(248,113,113,0.07); border: 1px solid rgba(248,113,113,0.25); color: #fca5a5; }
        .cm-alert.warn { background: rgba(245,197,66,0.07); border: 1px solid rgba(245,197,66,0.25); color: #fde68a; }
        .cm-alert svg { width:16px; height:16px; flex-shrink:0; margin-top:1px; }

        /* ── Card type grid ── */
        .cm-cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            margin-bottom: 2.75rem;
        }

        .cm-card {
            position: relative;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: rgba(6,14,30,0.7);
            backdrop-filter: blur(16px);
            padding: 1.75rem;
            overflow: hidden;
            transition: border-color 0.3s, transform 0.3s, box-shadow 0.3s;
            animation: fadeUp 0.65s cubic-bezier(.22,1,.36,1) both;
            cursor: default;
        }
        .cm-card:nth-child(2) { animation-delay: 0.1s; }
        .cm-card:hover { border-color: var(--border-h); transform: translateY(-4px); box-shadow: 0 20px 60px rgba(0,0,0,0.4); }

        /* Gradient top bar */
        .cm-card-bar {
            position: absolute; top: 0; left: 0; right: 0; height: 2px;
            border-radius: 20px 20px 0 0;
        }

        /* Corner glow */
        .cm-card-glow {
            position: absolute;
            width: 180px; height: 180px;
            border-radius: 50%;
            top: -60px; right: -60px;
            pointer-events: none;
            opacity: 0.12;
            transition: opacity 0.3s;
        }
        .cm-card:hover .cm-card-glow { opacity: 0.22; }

        /* Mini card visual */
        .cm-card-visual {
            width: 64px; height: 42px;
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.25rem;
            position: relative;
            overflow: hidden;
        }
        .cm-card-visual::before {
            content: '';
            position: absolute; inset: 0;
            opacity: 0.2;
        }
        .cm-card-visual svg { width: 22px; height: 22px; color: #fff; position: relative; z-index: 1; }

        .cm-card-chip {
            position: absolute; top: 1.5rem; right: 1.5rem;
            font-size: 0.62rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;
            padding: 0.2rem 0.55rem; border-radius: 100px;
            border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.6);
            background: rgba(255,255,255,0.05);
        }

        .cm-card h2 {
            font-family: 'Syne', sans-serif;
            font-size: 1.15rem; font-weight: 800; letter-spacing: -0.02em;
            margin-bottom: 0.45rem;
        }
        .cm-card p { font-size: 0.83rem; color: var(--label); line-height: 1.65; }

        .cm-card-features {
            list-style: none;
            display: flex; flex-direction: column; gap: 0.4rem;
            margin: 1.1rem 0;
        }
        .cm-card-features li {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 0.78rem; color: #8ab0d8;
        }
        .cm-card-features li::before {
            content: '';
            width: 5px; height: 5px; border-radius: 50%;
            background: var(--accent); box-shadow: 0 0 5px var(--accent);
            flex-shrink: 0;
        }

        .cm-eligibility {
            font-size: 0.73rem; color: var(--muted);
            margin-bottom: 1.25rem;
            padding: 0.5rem 0.75rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 8px;
        }

        .cm-apply-btn {
            display: inline-flex; align-items: center; gap: 0.45rem;
            text-decoration: none; font-family: 'Syne', sans-serif;
            font-weight: 700; font-size: 0.78rem; letter-spacing: 0.03em;
            color: #fff;
            padding: 0.65rem 1.25rem;
            border-radius: 10px;
            transition: all 0.25s;
        }
        .cm-apply-btn svg { width: 14px; height: 14px; transition: transform 0.2s; }
        .cm-apply-btn:hover svg { transform: translateX(3px); }
        .cm-apply-btn:hover { filter: brightness(1.15); transform: translateY(-1px); }

        /* ── Applications section ── */
        .cm-section-head {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.1rem;
            animation: fadeUp 0.65s 0.2s cubic-bezier(.22,1,.36,1) both;
        }
        .cm-section-title {
            font-family: 'Syne', sans-serif;
            font-size: 1rem; font-weight: 700; letter-spacing: -0.01em;
            display: flex; align-items: center; gap: 0.6rem;
        }
        .cm-section-title-line {
            width: 18px; height: 2px; border-radius: 2px; background: var(--accent);
        }
        .cm-count {
            font-size: 0.7rem; font-weight: 600;
            background: rgba(59,130,246,0.12);
            border: 1px solid rgba(59,130,246,0.2);
            color: var(--accent); border-radius: 100px;
            padding: 0.15rem 0.55rem;
        }

        /* ── Table ── */
        .cm-table-outer {
            background: rgba(6,14,30,0.7);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            backdrop-filter: blur(16px);
            animation: fadeUp 0.65s 0.25s cubic-bezier(.22,1,.36,1) both;
        }
        .cm-table-scroll { overflow-x: auto; }

        .cm-table {
            width: 100%; border-collapse: collapse;
            font-size: 0.82rem;
        }
        .cm-table thead tr {
            background: rgba(255,255,255,0.025);
            border-bottom: 1px solid var(--border);
        }
        .cm-table th {
            padding: 0.85rem 1.1rem;
            text-align: left;
            font-size: 0.67rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--muted);
            white-space: nowrap;
        }
        .cm-table td {
            padding: 0.85rem 1.1rem;
            border-bottom: 1px solid rgba(100,160,255,0.06);
            color: #a8c4e8;
            vertical-align: middle;
        }
        .cm-table tbody tr:last-child td { border-bottom: none; }
        .cm-table tbody tr { transition: background 0.2s; }
        .cm-table tbody tr:hover { background: rgba(59,130,246,0.04); }

        .cm-app-id {
            font-family: 'Syne', sans-serif;
            font-weight: 700; font-size: 0.8rem;
            color: var(--accent2);
            letter-spacing: 0.02em;
        }

        .cm-type-badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            font-size: 0.72rem; font-weight: 600;
            padding: 0.25rem 0.6rem; border-radius: 7px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            color: #c4d9f5;
        }

        /* Status pills */
        .cm-pill {
            display: inline-flex; align-items: center; gap: 0.3rem;
            font-size: 0.7rem; font-weight: 600; letter-spacing: 0.05em; text-transform: capitalize;
            padding: 0.28rem 0.7rem; border-radius: 100px;
        }
        .cm-pill::before { content:''; width:5px; height:5px; border-radius:50%; flex-shrink:0; }
        .pill-pending  { background: rgba(245,197,66,0.1);  border:1px solid rgba(245,197,66,0.3);  color:#fde68a; }
        .pill-pending::before  { background:#f5c542; box-shadow:0 0 4px #f5c542; }
        .pill-approved { background: rgba(34,197,94,0.1);  border:1px solid rgba(34,197,94,0.3);  color:#86efac; }
        .pill-approved::before { background:#22c55e; box-shadow:0 0 4px #22c55e; }
        .pill-rejected { background: rgba(248,113,113,0.1); border:1px solid rgba(248,113,113,0.3); color:#fca5a5; }
        .pill-rejected::before { background:#f87171; box-shadow:0 0 4px #f87171; }
        .pill-processing { background:rgba(59,130,246,0.1); border:1px solid rgba(59,130,246,0.3); color:#93c5fd; }
        .pill-processing::before { background:var(--accent); box-shadow:0 0 4px var(--accent); animation:blink 1.5s ease-in-out infinite; }

        .cm-empty {
            padding: 3.5rem 2rem;
            text-align: center;
            color: var(--muted);
            font-size: 0.85rem;
        }
        .cm-empty-icon {
            width: 48px; height: 48px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
        }
        .cm-empty-icon svg { width:22px; height:22px; color:var(--muted); }

        /* ── Responsive ── */
        @media (max-width: 700px) {
            .cm-cards-grid { grid-template-columns: 1fr; }
            .cm-table th:nth-child(3),
            .cm-table td:nth-child(3) { display: none; }
        }
    </style>

    <div class="cm-root">
        <div class="cm-blob cm-blob-1"></div>
        <div class="cm-blob cm-blob-2"></div>
        <div class="cm-blob cm-blob-3"></div>

        <div class="cm-wrap">

            {{-- Header --}}
            <div class="cm-header">
                <div class="cm-eyebrow"><span class="cm-eyebrow-dot"></span> Card Services</div>
                <h1 class="cm-title">Cards Management</h1>
                <p class="cm-sub">Choose a card, submit your application, and track review status from one place.</p>
            </div>

            {{-- Alerts --}}
            @if (session('card_success'))
                <div class="cm-alert ok">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ session('card_success') }}
                </div>
            @endif
            @if (session('card_error'))
                <div class="cm-alert err">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('card_error') }}
                </div>
            @endif
            @if (!empty($applicationNotifications) && $applicationNotifications->isNotEmpty())
                @foreach ($applicationNotifications as $notification)
                    <div class="cm-alert ok">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <strong>{{ $notification->data['title'] ?? 'Application Update' }}:</strong>&nbsp;{{ $notification->data['message'] ?? 'Your application status has changed.' }}
                    </div>
                @endforeach
            @endif
            @if (!$hasBankingProfile)
                <div class="cm-alert warn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Your customer profile or account is incomplete. Complete profile setup before applying.
                </div>
            @endif

            {{-- Card type cards --}}
            <div class="cm-cards-grid">
                @foreach($cards as $i => $card)
                    <article class="cm-card">
                        <div class="cm-card-bar" style="background: linear-gradient(90deg, {{ $card['color_from'] }}, {{ $card['color_to'] }})"></div>
                        <div class="cm-card-glow" style="background: radial-gradient(circle, {{ $card['color_from'] }} 0%, transparent 70%)"></div>
                        <span class="cm-card-chip">{{ $card['chip'] }}</span>

                        <div class="cm-card-visual" style="background: linear-gradient(135deg, {{ $card['color_from'] }}, {{ $card['color_to'] }})">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="{{ $card['icon'] }}"/></svg>
                        </div>

                        <h2>{{ $card['title'] }}</h2>
                        <p>{{ $card['desc'] }}</p>

                        <ul class="cm-card-features">
                            @foreach($card['features'] as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>

                        <div class="cm-eligibility">⚡ {{ $card['eligibility'] }}</div>

                        <a href="{{ route('personal.cards.create', ['cardType' => $card['type']]) }}"
                           class="cm-apply-btn"
                           style="background: linear-gradient(135deg, {{ $card['color_from'] }}, {{ $card['color_to'] }}); box-shadow: 0 4px 20px {{ $card['color_from'] }}55;">
                            Apply for {{ $card['title'] }}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>
                    </article>
                @endforeach
            </div>

            {{-- Applications table --}}
            <div class="cm-section-head">
                <div class="cm-section-title">
                    <span class="cm-section-title-line"></span>
                    My Applications
                    @if (!$applications->isEmpty())
                        <span class="cm-count">{{ $applications->count() }}</span>
                    @endif
                </div>
            </div>

            <div class="cm-table-outer">
                @if ($applications->isEmpty())
                    <div class="cm-empty">
                        <div class="cm-empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="5" width="20" height="14" rx="3"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        </div>
                        <div style="font-weight:600; color:#6b93cc; margin-bottom:0.3rem;">No applications yet</div>
                        <div>Apply for a card above to get started.</div>
                    </div>
                @else
                    <div class="cm-table-scroll">
                        <table class="cm-table">
                            <thead>
                                <tr>
                                    <th>Application ID</th>
                                    <th>Card Type</th>
                                    <th>Network</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($applications as $application)
                                    @php
                                        $rawStatus = $application->status ?? 'pending_review';
                                        $statusKey = str_replace(' ', '_', strtolower($rawStatus));
                                        $statusInfo = $statusMap[$statusKey] ?? ['label' => ucwords(str_replace('_',' ',$rawStatus)), 'class' => 'pill-pending'];
                                    @endphp
                                    <tr>
                                        <td><span class="cm-app-id">{{ $application->application_id }}</span></td>
                                        <td>
                                            <span class="cm-type-badge">
                                                {{ ucfirst($application->card_category) }}
                                            </span>
                                        </td>
                                        <td>{{ $application->card_network }}</td>
                                        <td style="white-space:nowrap; color:#6b93cc; font-size:0.78rem;">
                                            {{ !empty($application->created_at) ? \Illuminate\Support\Carbon::parse($application->created_at)->format('M d, Y · h:i A') : '—' }}
                                        </td>
                                        <td>
                                            <span class="cm-pill {{ $statusInfo['class'] }}">
                                                {{ $statusInfo['label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>