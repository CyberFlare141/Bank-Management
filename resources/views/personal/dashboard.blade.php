<x-app-layout>
    @php
        $balance = (float) ($account->A_Balance ?? 0);
        $totalLoanTaken = (float) ($loanSummary['total_loan_taken'] ?? 0);
        $totalRepaid = (float) ($loanSummary['total_repaid'] ?? 0);
        $loanOutstanding = (float) ($loanSummary['remaining_loan_balance'] ?? 0);
        $availableMoney = (float) ($loanSummary['available_money'] ?? ($balance - $loanOutstanding));
        $creditAvailable = (float) ($creditCard->available_credit ?? 0);
        $creditLimit = (float) ($creditCard->credit_limit ?? 0);
        $lastLogin = optional($user->updated_at)->format('F d, Y \a\t h:i A') ?? now()->format('F d, Y \a\t h:i A');
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap');

        :root {
            --ink:        #020b18;
            --deep:       #040e1e;
            --surface:    #071527;
            --elevated:   #0c1e36;
            --panel:      rgba(9, 19, 35, 0.88);
            --border:     rgba(40, 100, 200, 0.16);
            --border-hi:  rgba(56, 139, 253, 0.4);
            --text:       #ddeeff;
            --muted:      #5a7a9e;
            --accent:     #2d7ef7;
            --accent-glow:rgba(45, 126, 247, 0.3);
            --cyan:       #0dd9f0;
            --cyan-glow:  rgba(13, 217, 240, 0.25);
            --gold:       #e8b84b;
            --gold-glow:  rgba(232, 184, 75, 0.3);
            --emerald:    #10e89a;
            --rose:       #f05978;
            --font-main:  'Outfit', sans-serif;
            --font-mono:  'Space Mono', monospace;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        .db-root {
            font-family: var(--font-main);
            background: var(--ink);
            min-height: calc(100vh - 64px);
            color: var(--text);
            position: relative;
            overflow-x: hidden;
        }

        /* ── AMBIENT BACKGROUND ── */
        .db-canvas {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }
        .db-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
        }
        .db-orb-1 {
            width: 640px; height: 640px;
            background: radial-gradient(circle, rgba(45,126,247,0.13) 0%, transparent 70%);
            top: -200px; left: -150px;
            animation: db-float1 20s ease-in-out infinite alternate;
        }
        .db-orb-2 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(13,217,240,0.09) 0%, transparent 70%);
            top: 30%; right: -100px;
            animation: db-float2 25s ease-in-out infinite alternate;
        }
        .db-orb-3 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(232,184,75,0.07) 0%, transparent 70%);
            bottom: -100px; left: 30%;
            animation: db-float3 18s ease-in-out infinite alternate;
        }
        .db-grid-lines {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(45,126,247,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(45,126,247,0.04) 1px, transparent 1px);
            background-size: 52px 52px;
            mask-image: radial-gradient(ellipse 75% 65% at 50% 50%, black 10%, transparent 85%);
        }
        .db-noise {
            position: absolute;
            inset: 0;
            opacity: 0.025;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
            background-size: 200px 200px;
        }

        @keyframes db-float1 { from { transform: translate(0,0) scale(1); } to { transform: translate(60px,40px) scale(1.12); } }
        @keyframes db-float2 { from { transform: translate(0,0) scale(1); } to { transform: translate(-50px,30px) scale(1.08); } }
        @keyframes db-float3 { from { transform: translate(0,0) scale(1); } to { transform: translate(40px,-30px) scale(1.1); } }

        /* ── LAYOUT ── */
        .db-wrap {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.5rem 5rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* ── BASE CARD ── */
        .db-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 20px;
            backdrop-filter: blur(16px);
            box-shadow: 0 0 0 1px rgba(45,126,247,0.04) inset, 0 20px 40px rgba(2,11,24,0.5);
            opacity: 0;
            transform: translateY(18px);
            animation: db-rise 0.65s cubic-bezier(0.22,1,0.36,1) forwards;
        }
        @keyframes db-rise { to { opacity:1; transform:translateY(0); } }

        /* stagger */
        .db-s1 { animation-delay: 0.06s; }
        .db-s2 { animation-delay: 0.13s; }
        .db-s3 { animation-delay: 0.19s; }
        .db-s4 { animation-delay: 0.25s; }
        .db-s5 { animation-delay: 0.31s; }
        .db-s6 { animation-delay: 0.37s; }
        .db-s7 { animation-delay: 0.43s; }

        /* ── HEADER WELCOME ── */
        .db-header {
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .db-user-row {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .db-avatar {
            width: 56px; height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--accent), var(--cyan));
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 0 24px var(--accent-glow), 0 0 48px rgba(45,126,247,0.12);
            animation: db-avatar-pulse 4s ease-in-out infinite;
        }
        @keyframes db-avatar-pulse {
            0%,100% { box-shadow: 0 0 24px var(--accent-glow); }
            50%      { box-shadow: 0 0 36px var(--cyan-glow), 0 0 60px var(--accent-glow); }
        }
        .db-welcome-label {
            font-size: 0.72rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted);
            font-family: var(--font-mono);
        }
        .db-username {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            background: linear-gradient(90deg, #fff 40%, var(--cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.1;
        }
        .db-last-login {
            font-size: 0.78rem;
            color: var(--muted);
            font-family: var(--font-mono);
            margin-top: 2px;
        }
        .db-header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .db-icon-btn {
            width: 44px; height: 44px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: rgba(9,19,35,0.7);
            display: flex; align-items: center; justify-content: center;
            color: var(--text);
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s, transform 0.2s;
        }
        .db-icon-btn:hover {
            border-color: var(--border-hi);
            background: rgba(45,126,247,0.1);
            transform: translateY(-2px);
        }
        .db-icon-btn svg { width: 18px; height: 18px; }

        /* ── ISLAMIC TOGGLE STRIP ── */
        .db-strip {
            padding: 0.9rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .db-strip-left {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        .db-strip-dot {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(45,126,247,0.25), rgba(13,217,240,0.15));
            border: 1px solid var(--border-hi);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }
        .db-strip-label { font-size: 0.9rem; font-weight: 600; color: var(--text); }
        .db-strip-sub   { font-size: 0.72rem; color: var(--muted); font-family: var(--font-mono); }
        .db-toggle-pill {
            width: 68px; height: 34px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(45,126,247,0.2), rgba(13,217,240,0.15));
            border: 1px solid var(--border-hi);
            position: relative;
            cursor: pointer;
            padding: 3px;
        }
        .db-toggle-knob {
            width: 26px; height: 26px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--cyan));
            box-shadow: 0 0 12px var(--accent-glow);
            margin-left: auto;
        }

        /* ── TABS ── */
        .db-tabs {
            padding: 0.4rem;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.35rem;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: rgba(4,14,30,0.7);
        }
        .db-tab {
            text-align: center;
            padding: 0.6rem 0.5rem;
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--muted);
            cursor: pointer;
            transition: background 0.2s, color 0.2s, transform 0.2s;
        }
        .db-tab:hover { color: var(--text); background: rgba(45,126,247,0.08); }
        .db-tab.active {
            background: linear-gradient(135deg, var(--accent), #1a5fd4);
            color: #fff;
            box-shadow: 0 6px 20px var(--accent-glow);
        }

        /* ── PREMIUM ACCOUNT CARD ── */
        .db-bank-card {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            padding: 1.6rem 1.8rem;
            min-height: 185px;
            background: linear-gradient(135deg, #0d2a5a 0%, #0a1e47 40%, #0c2960 70%, #081636 100%);
            border: 1px solid rgba(45,126,247,0.35);
            box-shadow: 0 0 0 1px rgba(13,217,240,0.1) inset, 0 30px 60px rgba(2,11,24,0.7);
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            opacity: 0;
            transform: translateY(18px);
            animation: db-rise 0.65s cubic-bezier(0.22,1,0.36,1) 0.22s forwards;
        }
        .db-bank-card:hover {
            transform: translateY(-4px) scale(1.005);
            box-shadow: 0 0 0 1px rgba(13,217,240,0.2) inset, 0 40px 80px rgba(2,11,24,0.8), 0 0 40px rgba(45,126,247,0.12);
        }
        /* card decorative circles */
        .db-bank-card::before {
            content: '';
            position: absolute;
            width: 320px; height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(45,126,247,0.18) 0%, transparent 70%);
            top: -120px; right: -80px;
            pointer-events: none;
        }
        .db-bank-card::after {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(13,217,240,0.12) 0%, transparent 70%);
            bottom: -60px; left: -40px;
            pointer-events: none;
        }
        .db-card-inner { position: relative; z-index: 1; height: 100%; }
        .db-card-toprow {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .db-card-actype {
            font-size: 0.72rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(180,210,255,0.7);
            font-family: var(--font-mono);
        }
        .db-card-chip {
            width: 34px; height: 26px;
            border-radius: 5px;
            background: linear-gradient(135deg, #d4a843, #f0cc6e, #b8902e);
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-template-rows: 1fr 1fr 1fr;
            gap: 1px;
            padding: 3px;
        }
        .db-chip-cell {
            background: rgba(160,110,20,0.5);
            border-radius: 1px;
        }
        .db-card-name {
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #e8f4ff;
            margin-top: 1.8rem;
            text-transform: uppercase;
        }
        .db-card-number {
            font-family: var(--font-mono);
            font-size: 0.82rem;
            letter-spacing: 0.15em;
            color: rgba(180,210,255,0.6);
            margin-top: 0.3rem;
        }
        .db-card-bottomrow {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1rem;
        }
        .db-card-bal-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-family: var(--font-main);
            font-size: 0.85rem;
            font-weight: 600;
            color: rgba(180,215,255,0.8);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            border: 1px solid rgba(45,126,247,0.25);
            transition: background 0.2s, border-color 0.2s;
        }
        .db-card-bal-btn:hover {
            background: rgba(45,126,247,0.12);
            border-color: rgba(45,126,247,0.4);
        }
        .db-card-brand {
            text-align: right;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(180,215,255,0.5);
            font-family: var(--font-mono);
        }
        /* card shimmer */
        .db-card-shimmer {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 2;
        }
        .db-card-shimmer::after {
            content: '';
            position: absolute;
            inset-block: 0;
            width: 80px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.07), transparent);
            transform: skewX(-15deg);
            animation: db-sweep 4s ease-in-out infinite;
        }
        @keyframes db-sweep {
            0%   { left: -100px; }
            100% { left: calc(100% + 100px); }
        }

        /* ── ACTION GRID ── */
        .db-actions {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 0.75rem;
        }
        @media (max-width: 900px) { .db-actions { grid-template-columns: repeat(3,1fr); } }
        @media (max-width: 500px) { .db-actions { grid-template-columns: repeat(2,1fr); } }

        .db-action-item {
            padding: 1.1rem 0.5rem 0.9rem;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: rgba(9,19,35,0.7);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.6rem;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s, transform 0.25s, box-shadow 0.25s;
        }
        .db-action-item:hover {
            border-color: var(--border-hi);
            background: rgba(45,126,247,0.07);
            transform: translateY(-4px);
            box-shadow: 0 16px 32px rgba(2,11,24,0.5);
        }
        .db-action-icon {
            width: 40px; height: 40px;
            border-radius: 12px;
            background: rgba(45,126,247,0.1);
            border: 1px solid rgba(45,126,247,0.2);
            display: flex; align-items: center; justify-content: center;
            color: var(--cyan);
        }
        .db-action-icon svg { width: 18px; height: 18px; }
        .db-action-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(180,210,255,0.75);
            text-align: center;
        }

        /* ── SECTIONS GRID ── */
        .db-sections {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }
        .db-sections .db-full { grid-column: 1 / -1; }
        @media (max-width: 768px) { .db-sections { grid-template-columns: 1fr; } .db-sections .db-full { grid-column: 1; } }

        /* ── SECTION CARDS ── */
        .db-sec { padding: 1.5rem; }
        .db-sec-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
            margin-bottom: 1.1rem;
        }
        .db-sec-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: #cfe0ff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .db-sec-title-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            animation: db-blink 2s ease-in-out infinite;
        }
        .dot-blue    { background: var(--accent); box-shadow: 0 0 6px var(--accent); }
        .dot-cyan    { background: var(--cyan); box-shadow: 0 0 6px var(--cyan); }
        .dot-gold    { background: var(--gold); box-shadow: 0 0 6px var(--gold); }
        .dot-emerald { background: var(--emerald); box-shadow: 0 0 6px var(--emerald); }
        @keyframes db-blink {
            0%,100% { opacity:1; transform:scale(1); }
            50%      { opacity:0.4; transform:scale(0.7); }
        }
        .db-manage-link {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-family: var(--font-mono);
            color: var(--accent);
            border: 1px solid rgba(45,126,247,0.3);
            padding: 0.3rem 0.75rem;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.2s, border-color 0.2s;
        }
        .db-manage-link:hover { background: rgba(45,126,247,0.12); border-color: var(--border-hi); }

        /* ── STAT META GRID ── */
        .db-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.65rem;
            margin-bottom: 1rem;
        }
        .db-meta-item {
            padding: 0.75rem;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: rgba(7,14,27,0.6);
            transition: border-color 0.2s;
        }
        .db-meta-item:hover { border-color: rgba(45,126,247,0.25); }
        .db-meta-k {
            font-size: 0.67rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            font-family: var(--font-mono);
        }
        .db-meta-v {
            margin-top: 0.3rem;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
            font-family: var(--font-mono);
        }

        /* ── LOAN LIST ── */
        .db-loan-list { display: flex; flex-direction: column; gap: 0.55rem; }
        .db-loan-item {
            padding: 0.85rem 1rem;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: rgba(7,14,27,0.55);
            transition: border-color 0.2s, transform 0.2s;
        }
        .db-loan-item:hover { border-color: rgba(45,126,247,0.3); transform: translateX(3px); }
        .db-loan-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.82rem;
        }
        .db-loan-row + .db-loan-row { margin-top: 0.35rem; }
        .db-loan-key { color: var(--muted); font-family: var(--font-mono); font-size: 0.75rem; }
        .db-loan-val { color: var(--text); font-weight: 600; font-family: var(--font-mono); }
        .db-loan-id  { color: var(--accent); font-family: var(--font-mono); font-weight: 700; }
        .db-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-family: var(--font-mono);
        }
        .db-badge::before { content:''; width:5px;height:5px;border-radius:50%;display:inline-block; }
        .db-badge-active  { border:1px solid rgba(232,184,75,0.35); background:rgba(232,184,75,0.1); color:#f0cc6e; }
        .db-badge-active::before { background:var(--gold); box-shadow:0 0 5px var(--gold); animation:db-blink 1.5s ease-in-out infinite; }
        .db-badge-closed  { border:1px solid rgba(16,232,154,0.3); background:rgba(16,232,154,0.08); color:#6effc9; }
        .db-badge-closed::before { background:var(--emerald); }

        /* ── EMPTY STATE ── */
        .db-empty {
            text-align: center;
            padding: 2rem;
            color: var(--muted);
            font-size: 0.84rem;
            font-family: var(--font-mono);
        }
        .db-empty-icon { font-size: 1.8rem; margin-bottom: 0.5rem; opacity: 0.4; }

        /* ── TRANSACTIONS TABLE ── */
        .db-table-wrap { overflow-x: auto; }
        .db-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.82rem;
        }
        .db-table thead th {
            padding: 0.6rem 0.9rem;
            text-align: left;
            font-size: 0.65rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            font-family: var(--font-mono);
            background: rgba(4,12,24,0.8);
            border-bottom: 1px solid var(--border);
        }
        .db-table thead th:first-child { border-radius: 8px 0 0 0; }
        .db-table thead th:last-child  { border-radius: 0 8px 0 0; }
        .db-table tbody td {
            padding: 0.75rem 0.9rem;
            border-bottom: 1px solid rgba(40,100,200,0.07);
            color: var(--text);
            font-family: var(--font-mono);
            transition: background 0.15s;
        }
        .db-table tbody tr:hover td { background: rgba(45,126,247,0.04); }
        .db-table tbody tr:last-child td { border-bottom: none; }
        .db-tx-type {
            display: inline-flex;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            background: rgba(45,126,247,0.1);
            border: 1px solid rgba(45,126,247,0.2);
            color: var(--cyan);
        }
        .db-tx-amt { color: var(--emerald); font-weight: 700; }

        /* ── CREDIT CARD VISUAL ── */
        .db-cc-mini {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            padding: 1.1rem 1.3rem;
            background: linear-gradient(135deg, #1a0a3a 0%, #2d1060 50%, #180840 100%);
            border: 1px solid rgba(160,80,255,0.25);
            box-shadow: 0 12px 30px rgba(160,80,255,0.12);
            margin-bottom: 1rem;
        }
        .db-cc-mini::before {
            content:'';
            position:absolute;
            width:200px;height:200px;
            border-radius:50%;
            background: radial-gradient(circle, rgba(160,80,255,0.2), transparent 70%);
            top:-60px;right:-40px;
            pointer-events:none;
        }
        .db-cc-inner { position:relative;z-index:1; }
        .db-cc-label { font-size:0.68rem; letter-spacing:0.12em; text-transform:uppercase; color:rgba(200,170,255,0.6); font-family:var(--font-mono); }
        .db-cc-number { font-family:var(--font-mono); font-size:0.85rem; letter-spacing:0.15em; color:rgba(220,195,255,0.8); margin-top:0.6rem; }
        .db-cc-row { display:flex; align-items:center; justify-content:space-between; margin-top:0.6rem; }
        .db-cc-expiry { font-size:0.75rem; font-family:var(--font-mono); color:rgba(200,170,255,0.7); }
        .db-cc-brand { font-size:0.7rem; font-weight:700; letter-spacing:0.1em; color:rgba(200,170,255,0.5); font-family:var(--font-mono); }

        /* ── PROGRESS BAR ── */
        .db-credit-bar {
            height: 6px;
            border-radius: 999px;
            background: rgba(255,255,255,0.08);
            margin-top: 0.75rem;
            overflow: hidden;
        }
        .db-credit-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--accent), var(--cyan));
            box-shadow: 0 0 8px var(--accent-glow);
            transition: width 1s cubic-bezier(0.22,1,0.36,1);
        }
        .db-credit-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            font-family: var(--font-mono);
            color: var(--muted);
            margin-top: 0.4rem;
        }

        /* scrollbar */
        ::-webkit-scrollbar { width:5px; height:5px; }
        ::-webkit-scrollbar-track { background: var(--ink); }
        ::-webkit-scrollbar-thumb { background: rgba(45,126,247,0.3); border-radius:3px; }

        @media (max-width:768px) {
            .db-header { padding:1.1rem 1.25rem; }
            .db-username { font-size:1.3rem; }
            .db-tabs { grid-template-columns: repeat(2,1fr); }
            .db-sec { padding:1.1rem; }
        }
        @media (prefers-reduced-motion:reduce) {
            *,*::before,*::after { animation:none !important; transition:none !important; }
        }
    </style>

    <div class="db-root">
        <!-- ambient bg -->
        <div class="db-canvas">
            <div class="db-orb db-orb-1"></div>
            <div class="db-orb db-orb-2"></div>
            <div class="db-orb db-orb-3"></div>
            <div class="db-grid-lines"></div>
            <div class="db-noise"></div>
        </div>

        <div class="db-wrap py-6 sm:py-8">

            {{-- WELCOME HEADER --}}
            <div class="db-card db-s1">
                <div class="db-header">
                    <div class="db-user-row">
                        <div class="db-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                        <div>
                            <div class="db-welcome-label">Welcome back</div>
                            <div class="db-username">{{ $user->name }}</div>
                            <div class="db-last-login">{{ $lastLogin }}</div>
                        </div>
                    </div>
                    <div class="db-header-actions">
                        <button class="db-icon-btn" aria-label="Notifications" type="button">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M14.5 18a2.5 2.5 0 0 1-5 0"></path>
                                <path d="M18 16V11a6 6 0 1 0-12 0v5l-2 2h16l-2-2z"></path>
                            </svg>
                        </button>
                        <button class="db-icon-btn" aria-label="Settings" type="button">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1 1 0 0 0 .2 1.1l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1 1 0 0 0-1.1-.2 1 1 0 0 0-.6.9V20a2 2 0 1 1-4 0v-.2a1 1 0 0 0-.7-.9 1 1 0 0 0-1.1.2l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1 1 0 0 0 .2-1.1 1 1 0 0 0-.9-.6H4a2 2 0 1 1 0-4h.2a1 1 0 0 0 .9-.7 1 1 0 0 0-.2-1.1l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1 1 0 0 0 1.1.2H9a1 1 0 0 0 .6-.9V4a2 2 0 1 1 4 0v.2a1 1 0 0 0 .7.9 1 1 0 0 0 1.1-.2l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1 1 0 0 0-.2 1.1V9c0 .4.2.8.6.9H20a2 2 0 1 1 0 4h-.2a1 1 0 0 0-.4.1"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ISLAMIC STRIP --}}
            <div class="db-card db-s2">
                <div class="db-strip">
                    <div class="db-strip-left">
                        <div class="db-strip-dot">☽</div>
                        <div>
                            <div class="db-strip-label">Qibla Pointer</div>
                            <div class="db-strip-sub">Islamic mode enabled</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <span style="font-size:0.85rem;color:var(--muted);font-family:var(--font-mono);">Islamic</span>
                        <div class="db-toggle-pill" role="switch" aria-checked="true">
                            <div class="db-toggle-knob"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABS --}}
            <div class="db-card db-tabs db-s2">
                <div class="db-tab active">Accounts</div>
                <div class="db-tab">Cards</div>
                <div class="db-tab">Deposits</div>
                <div class="db-tab">Investment</div>
            </div>

            {{-- BANK CARD --}}
            <div class="db-bank-card">
                <div class="db-card-shimmer"></div>
                <div class="db-card-inner" x-data="{ show: false }">
                    <div class="db-card-toprow">
                        <span class="db-card-actype">{{ $account?->account_type ?? 'Prime Hasanah Youth' }}</span>
                        <div class="db-card-chip">
                            @for($i=0;$i<9;$i++)<div class="db-chip-cell"></div>@endfor
                        </div>
                    </div>
                    <div class="db-card-name">{{ strtoupper($user->name) }}</div>
                    <div class="db-card-number">{{ $account?->A_Number ?? '•••• •••• •••• ••••' }}</div>
                    <div class="db-card-bottomrow">
                        <button class="db-card-bal-btn" @click="show = !show" type="button">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;" x-show="!show">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;" x-show="show" x-transition>
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                            <span x-show="!show">Tap for Balance</span>
                            <span x-show="show" x-transition style="font-family:var(--font-mono);font-size:1rem;color:#fff;">${{ number_format($balance, 2) }}</span>
                        </button>
                        <div class="db-card-brand">HASANAH<br>ISLAMI BANKING</div>
                    </div>
                </div>
            </div>

            {{-- QUICK ACTIONS --}}
            <div class="db-card db-s3">
                <div style="padding:1.25rem;">
                    <div class="db-actions">
                        <div class="db-action-item">
                            <div class="db-action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 7h10v10"></path><path d="m7 17 10-10"></path></svg>
                            </div>
                            <div class="db-action-label">Fund Transfer</div>
                        </div>
                        <div class="db-action-item">
                            <div class="db-action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 4h14v16H5z"></path><path d="M8 8h8M8 12h8M8 16h5"></path></svg>
                            </div>
                            <div class="db-action-label">Pay Bill</div>
                        </div>
                        <div class="db-action-item">
                            <div class="db-action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="6" y="3" width="12" height="18" rx="2"></rect><path d="M12 8v8M8 12h8"></path></svg>
                            </div>
                            <div class="db-action-label">Recharge</div>
                        </div>
                        <div class="db-action-item">
                            <div class="db-action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 3h9l3 3v15H6z"></path><path d="M9 13h6M9 17h4M9 9h6"></path></svg>
                            </div>
                            <div class="db-action-label">Statements</div>
                        </div>
                        <div class="db-action-item">
                            <div class="db-action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1 1 0 0 0 .2 1.1l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1 1 0 0 0-1.1-.2 1 1 0 0 0-.6.9V20a2 2 0 1 1-4 0v-.2a1 1 0 0 0-.7-.9 1 1 0 0 0-1.1.2l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1 1 0 0 0 .2-1.1 1 1 0 0 0-.9-.6H4a2 2 0 1 1 0-4h.2a1 1 0 0 0 .9-.7 1 1 0 0 0-.2-1.1l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1 1 0 0 0 1.1.2H9a1 1 0 0 0 .6-.9V4a2 2 0 1 1 4 0v.2a1 1 0 0 0 .7.9 1 1 0 0 0 1.1-.2l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1 1 0 0 0-.2 1.1V9c0 .4.2.8.6.9H20a2 2 0 1 1 0 4h-.2a1 1 0 0 0-.4.1"></path></svg>
                            </div>
                            <div class="db-action-label">Account Services</div>
                        </div>
                        <div class="db-action-item">
                            <div class="db-action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 10h18"></path><circle cx="8" cy="15" r="1"></circle><circle cx="12" cy="15" r="1"></circle></svg>
                            </div>
                            <div class="db-action-label">Card Services</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTIONS GRID --}}
            <div class="db-sections">

                {{-- LOANS --}}
                <div class="db-card db-sec db-s4">
                    <div class="db-sec-header">
                        <div class="db-sec-title">
                            <span class="db-sec-title-dot dot-gold"></span>
                            Loans
                        </div>
                        <a href="{{ route('personal.loan') }}" class="db-manage-link">Manage →</a>
                    </div>
                    <div class="db-meta">
                        <div class="db-meta-item">
                            <div class="db-meta-k">Total Taken</div>
                            <div class="db-meta-v">${{ number_format($totalLoanTaken, 2) }}</div>
                        </div>
                        <div class="db-meta-item">
                            <div class="db-meta-k">Repaid</div>
                            <div class="db-meta-v" style="color:var(--emerald)">${{ number_format($totalRepaid, 2) }}</div>
                        </div>
                        <div class="db-meta-item">
                            <div class="db-meta-k">Outstanding</div>
                            <div class="db-meta-v" style="color:var(--gold)">${{ number_format($loanOutstanding, 2) }}</div>
                        </div>
                        <div class="db-meta-item">
                            <div class="db-meta-k">Available</div>
                            <div class="db-meta-v" style="color:var(--cyan)">${{ number_format($availableMoney, 2) }}</div>
                        </div>
                    </div>
                    @if($activeLoans->isEmpty())
                        <div class="db-empty"><div class="db-empty-icon">🔒</div>No active loans.</div>
                    @else
                        <div class="db-loan-list">
                            @foreach($activeLoans as $loan)
                                @php $rem = (float)($loan->remaining_amount ?? $loan->L_Amount); @endphp
                                <div class="db-loan-item">
                                    <div class="db-loan-row">
                                        <span class="db-loan-id">Loan #{{ $loan->L_ID }}</span>
                                        <span class="db-badge {{ $rem > 0 ? 'db-badge-active' : 'db-badge-closed' }}">{{ $rem > 0 ? 'Active' : 'Closed' }}</span>
                                    </div>
                                    <div class="db-loan-row">
                                        <span class="db-loan-key">Amount</span>
                                        <span class="db-loan-val">${{ number_format((float)$loan->L_Amount, 2) }}</span>
                                    </div>
                                    <div class="db-loan-row">
                                        <span class="db-loan-key">Remaining</span>
                                        <span class="db-loan-val" style="color:var(--gold)">${{ number_format($rem, 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- CREDIT CARD --}}
                <div class="db-card db-sec db-s5">
                    <div class="db-sec-header">
                        <div class="db-sec-title">
                            <span class="db-sec-title-dot dot-cyan"></span>
                            Credit Card
                        </div>
                    </div>
                    @if(!$creditCard)
                        <div class="db-empty"><div class="db-empty-icon">💳</div>No credit card found.</div>
                    @else
                        <div class="db-cc-mini">
                            <div class="db-cc-inner">
                                <div class="db-cc-label">Hasanah Credit</div>
                                <div class="db-cc-number">{{ $creditCard->masked_card_number }}</div>
                                <div class="db-cc-row">
                                    <div class="db-cc-expiry">EXP {{ $creditCard->expiry_date }}</div>
                                    <div class="db-cc-brand">ISLAMI BANKING</div>
                                </div>
                            </div>
                        </div>
                        <div class="db-meta">
                            <div class="db-meta-item">
                                <div class="db-meta-k">Credit Limit</div>
                                <div class="db-meta-v">${{ number_format($creditLimit, 2) }}</div>
                            </div>
                            <div class="db-meta-item">
                                <div class="db-meta-k">Available</div>
                                <div class="db-meta-v" style="color:var(--emerald)">${{ number_format($creditAvailable, 2) }}</div>
                            </div>
                        </div>
                        @if($creditLimit > 0)
                            @php $usedPct = min(100, round((($creditLimit - $creditAvailable) / $creditLimit) * 100)); @endphp
                            <div style="margin-top:0.5rem;">
                                <div class="db-credit-bar">
                                    <div class="db-credit-fill" style="width:{{ $usedPct }}%"></div>
                                </div>
                                <div class="db-credit-labels">
                                    <span>Used {{ $usedPct }}%</span>
                                    <span>Available {{ 100 - $usedPct }}%</span>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- RECENT TRANSACTIONS --}}
                <div class="db-card db-sec db-full db-s6">
                    <div class="db-sec-header">
                        <div class="db-sec-title">
                            <span class="db-sec-title-dot dot-blue"></span>
                            Recent Transactions
                        </div>
                    </div>
                    @if($recentTransactions->isEmpty())
                        <div class="db-empty"><div class="db-empty-icon">📂</div>No recent transactions found.</div>
                    @else
                        <div class="db-table-wrap">
                            <table class="db-table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Account</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $tx)
                                        <tr>
                                            <td><span class="db-tx-type">{{ $tx->T_Type }}</span></td>
                                            <td><span class="db-tx-amt">${{ number_format((float) $tx->T_Amount, 2) }}</span></td>
                                            <td>{{ $tx->A_Number }}</td>
                                            <td>{{ optional($tx->T_Date)->format('M d, Y h:i A') ?? $tx->created_at?->format('M d, Y h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>