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
        @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Mono:wght@300;400;500&display=swap');

        :root {
            --bg:          #060a12;
            --surface:     #0c1220;
            --card:        #0f1826;
            --card-hi:     #131f30;
            --border:      rgba(56, 139, 253, 0.1);
            --border-hi:   rgba(56, 139, 253, 0.3);
            --text:        #e8f0ff;
            --muted:       #4a6080;
            --accent:      #3b82f6;
            --accent2:     #06b6d4;
            --gold:        #f59e0b;
            --green:       #10b981;
            --rose:        #f43f5e;
            --font:        'Syne', sans-serif;
            --mono:        'DM Mono', monospace;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        .mars-root {
            font-family: var(--font);
            background: var(--bg);
            min-height: 100vh;
            color: var(--text);
            overflow-x: hidden;
        }

        /* ═══ AMBIENT BACKGROUND ═══ */
        .mars-bg {
            position: fixed; inset: 0;
            pointer-events: none; z-index: 0;
            overflow: hidden;
        }
        .mars-blob {
            position: absolute; border-radius: 50%;
            filter: blur(100px); will-change: transform;
        }
        .b1 {
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 65%);
            top: -200px; left: -200px;
            animation: bfloat1 22s ease-in-out infinite alternate;
        }
        .b2 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(6,182,212,0.1) 0%, transparent 65%);
            top: 40%; right: -150px;
            animation: bfloat2 28s ease-in-out infinite alternate;
        }
        .b3 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(245,158,11,0.07) 0%, transparent 65%);
            bottom: -100px; left: 35%;
            animation: bfloat3 19s ease-in-out infinite alternate;
        }
        .mars-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(59,130,246,0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59,130,246,0.035) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse 80% 70% at 50% 50%, black 0%, transparent 90%);
        }

        @keyframes bfloat1 { from { transform: translate(0,0); } to { transform: translate(80px, 60px); } }
        @keyframes bfloat2 { from { transform: translate(0,0); } to { transform: translate(-60px, 40px); } }
        @keyframes bfloat3 { from { transform: translate(0,0); } to { transform: translate(50px,-50px); } }

        /* ═══ LAYOUT ═══ */
        .mars-wrap {
            position: relative; z-index: 1;
            max-width: 1280px; margin: 0 auto;
            padding: 0 1.5rem 5rem;
        }

        /* ═══ TOP NAV ═══ */
        .mars-nav {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.25rem 0;
            border-bottom: 1px solid var(--border);
            margin-bottom: 2.5rem;
            opacity: 0; animation: fadeUp 0.6s ease 0.1s forwards;
        }
        .mars-logo {
            display: flex; align-items: center; gap: 0.6rem;
            font-size: 1.2rem; font-weight: 800; letter-spacing: -0.02em;
            color: var(--text);
        }
        .mars-logo-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem; font-weight: 900; color: #fff;
            box-shadow: 0 0 20px rgba(59,130,246,0.4);
        }
        .mars-nav-links {
            display: flex; align-items: center; gap: 0.5rem;
        }
        .mars-nav-link {
            padding: 0.5rem 1rem; border-radius: 8px;
            font-size: 0.85rem; font-weight: 600; color: var(--muted);
            text-decoration: none; transition: color 0.2s, background 0.2s;
            cursor: pointer; border: none; background: none;
        }
        .mars-nav-link:hover { color: var(--text); background: rgba(255,255,255,0.04); }
        .mars-nav-link.active { color: var(--text); background: rgba(59,130,246,0.1); }
        .mars-nav-right { display: flex; align-items: center; gap: 0.75rem; }
        .mars-avatar {
            width: 40px; height: 40px; border-radius: 12px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem; font-weight: 800; color: #fff;
            box-shadow: 0 0 0 2px rgba(59,130,246,0.3);
            cursor: pointer;
        }
        .mars-notif {
            width: 40px; height: 40px; border-radius: 12px;
            border: 1px solid var(--border); background: var(--card);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: border-color 0.2s, background 0.2s;
            position: relative; color: var(--muted);
        }
        .mars-notif:hover { border-color: var(--border-hi); color: var(--text); background: var(--card-hi); }
        .mars-notif-dot {
            position: absolute; top: 8px; right: 8px;
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--accent); border: 2px solid var(--bg);
            animation: notifPulse 2s ease-in-out infinite;
        }
        @keyframes notifPulse { 0%,100% { box-shadow: 0 0 0 0 rgba(59,130,246,0.5); } 50% { box-shadow: 0 0 0 5px rgba(59,130,246,0); } }

        /* ═══ HERO ROW ═══ */
        .mars-hero {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 1.25rem; margin-bottom: 1.25rem;
        }
        @media (max-width: 900px) { .mars-hero { grid-template-columns: 1fr; } }

        /* ═══ MAIN BALANCE CARD ═══ */
        .mars-balance-card {
            position: relative; border-radius: 24px; overflow: hidden;
            padding: 2rem;
            background: linear-gradient(135deg, #0d1f3c 0%, #0a1628 40%, #0e2240 75%, #060f1e 100%);
            border: 1px solid rgba(59,130,246,0.25);
            box-shadow: 0 0 0 1px rgba(6,182,212,0.06) inset, 0 40px 80px rgba(0,0,0,0.6);
            opacity: 0; animation: fadeUp 0.7s cubic-bezier(0.22,1,0.36,1) 0.18s forwards;
            cursor: pointer; transition: transform 0.35s, box-shadow 0.35s;
        }
        .mars-balance-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0 0 1px rgba(59,130,246,0.3) inset, 0 50px 100px rgba(0,0,0,0.7), 0 0 50px rgba(59,130,246,0.1);
        }
        .bc-shine {
            position: absolute; inset: 0; pointer-events: none;
            overflow: hidden;
        }
        .bc-shine::after {
            content: '';
            position: absolute; top: 0; bottom: 0; width: 120px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.055), transparent);
            transform: skewX(-20deg);
            animation: shine 5s ease-in-out infinite;
        }
        @keyframes shine { 0% { left: -150px; } 100% { left: calc(100% + 150px); } }
        .bc-deco1 {
            position: absolute; width: 380px; height: 380px; border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,0.15), transparent 70%);
            top: -140px; right: -100px; pointer-events: none;
        }
        .bc-deco2 {
            position: absolute; width: 240px; height: 240px; border-radius: 50%;
            background: radial-gradient(circle, rgba(6,182,212,0.12), transparent 70%);
            bottom: -80px; left: -60px; pointer-events: none;
        }
        .bc-inner { position: relative; z-index: 1; height: 100%; }
        .bc-top { display: flex; align-items: flex-start; justify-content: space-between; }
        .bc-actype {
            font-size: 0.7rem; letter-spacing: 0.15em; text-transform: uppercase;
            color: rgba(180,210,255,0.55); font-family: var(--mono); font-weight: 400;
        }
        .bc-chip {
            width: 38px; height: 28px; border-radius: 6px;
            background: linear-gradient(135deg, #d4a843, #f5d87a, #c4962e);
            box-shadow: 0 3px 10px rgba(0,0,0,0.4);
            display: grid; grid-template-columns: 1fr 1fr 1fr;
            grid-template-rows: 1fr 1fr 1fr; gap: 2px; padding: 4px;
        }
        .bc-chip-cell { background: rgba(150,100,15,0.45); border-radius: 1px; }
        .bc-name {
            margin-top: 2.5rem; font-size: 1rem; font-weight: 700;
            letter-spacing: 0.06em; text-transform: uppercase;
            color: rgba(230,245,255,0.9);
        }
        .bc-number {
            font-family: var(--mono); font-size: 0.8rem; letter-spacing: 0.18em;
            color: rgba(160,200,255,0.5); margin-top: 0.35rem;
        }
        .bc-bottom {
            display: flex; align-items: flex-end; justify-content: space-between;
            margin-top: 1.75rem;
        }
        .bc-bal-label { font-size: 0.65rem; letter-spacing: 0.1em; text-transform: uppercase; color: rgba(160,200,255,0.5); font-family: var(--mono); }
        .bc-bal-amount {
            font-size: 2.2rem; font-weight: 800; letter-spacing: -0.03em;
            color: #fff; margin-top: 0.2rem; line-height: 1;
        }
        .bc-bal-amount span { color: rgba(255,255,255,0.3); font-size: 1.5rem; }
        .bc-change {
            display: inline-flex; align-items: center; gap: 0.3rem;
            padding: 0.25rem 0.6rem; border-radius: 20px; margin-top: 0.5rem;
            font-size: 0.72rem; font-weight: 600; font-family: var(--mono);
            background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25);
            color: var(--green);
        }
        .bc-brand {
            font-size: 0.62rem; letter-spacing: 0.12em; text-transform: uppercase;
            color: rgba(160,200,255,0.35); font-family: var(--mono); text-align: right; line-height: 1.5;
        }

        /* ═══ MINI STATS ═══ */
        .mars-stats-col {
            display: grid; grid-template-rows: repeat(3, 1fr);
            gap: 0.85rem;
            opacity: 0; animation: fadeUp 0.7s cubic-bezier(0.22,1,0.36,1) 0.26s forwards;
        }
        .mars-stat {
            padding: 1.2rem 1.5rem; border-radius: 18px;
            background: var(--card); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            gap: 1rem; transition: border-color 0.25s, transform 0.25s;
            cursor: pointer;
        }
        .mars-stat:hover { border-color: var(--border-hi); transform: translateX(4px); }
        .mars-stat-left { display: flex; align-items: center; gap: 0.75rem; }
        .mars-stat-icon {
            width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .si-blue { background: rgba(59,130,246,0.12); border: 1px solid rgba(59,130,246,0.2); color: var(--accent); }
        .si-gold { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2); color: var(--gold); }
        .si-green { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: var(--green); }
        .mars-stat-icon svg { width: 18px; height: 18px; }
        .mars-stat-label { font-size: 0.75rem; color: var(--muted); font-weight: 500; }
        .mars-stat-val { font-size: 1.05rem; font-weight: 700; font-family: var(--mono); color: var(--text); margin-top: 0.1rem; }
        .mars-stat-badge {
            font-size: 0.7rem; font-weight: 600; font-family: var(--mono);
            padding: 0.2rem 0.55rem; border-radius: 6px;
        }
        .badge-up { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: var(--green); }
        .badge-warn { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2); color: var(--gold); }
        .badge-neutral { background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.2); color: var(--accent); }

        /* ═══ QUICK ACTIONS ═══ */
        .mars-actions-row {
            display: grid; grid-template-columns: repeat(6, 1fr);
            gap: 0.85rem; margin-bottom: 1.25rem;
            opacity: 0; animation: fadeUp 0.7s cubic-bezier(0.22,1,0.36,1) 0.34s forwards;
        }
        @media (max-width: 900px) { .mars-actions-row { grid-template-columns: repeat(3,1fr); } }
        @media (max-width: 500px) { .mars-actions-row { grid-template-columns: repeat(2,1fr); } }

        .mars-action {
            padding: 1.25rem 0.5rem 1rem;
            border-radius: 18px; border: 1px solid var(--border);
            background: var(--card);
            display: flex; flex-direction: column; align-items: center; gap: 0.65rem;
            cursor: pointer;
            transition: border-color 0.25s, background 0.25s, transform 0.3s, box-shadow 0.3s;
        }
        .mars-action:hover {
            border-color: var(--border-hi);
            background: var(--card-hi);
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.5), 0 0 20px rgba(59,130,246,0.08);
        }
        .mars-action-icon {
            width: 44px; height: 44px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.15);
            color: var(--accent2); transition: background 0.2s, border-color 0.2s;
        }
        .mars-action:hover .mars-action-icon {
            background: rgba(59,130,246,0.15); border-color: rgba(59,130,246,0.35);
        }
        .mars-action-icon svg { width: 20px; height: 20px; }
        .mars-action-label { font-size: 0.73rem; font-weight: 600; color: var(--muted); text-align: center; transition: color 0.2s; }
        .mars-action:hover .mars-action-label { color: var(--text); }

        /* ═══ SECTION GRID ═══ */
        .mars-grid-layout {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }
        .mars-full { grid-column: 1/-1; }
        @media (max-width: 768px) { .mars-grid-layout { grid-template-columns: 1fr; } .mars-full { grid-column: 1; } }

        /* ═══ CARD BASE ═══ */
        .mars-card {
            border-radius: 20px; border: 1px solid var(--border);
            background: var(--card); padding: 1.5rem;
            opacity: 0; animation: fadeUp 0.7s cubic-bezier(0.22,1,0.36,1) forwards;
            transition: border-color 0.25s;
        }
        .mars-card:hover { border-color: rgba(59,130,246,0.18); }
        .d1 { animation-delay: 0.4s; }
        .d2 { animation-delay: 0.48s; }
        .d3 { animation-delay: 0.56s; }
        .d4 { animation-delay: 0.64s; }

        .mars-sec-hdr {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.25rem; padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }
        .mars-sec-title {
            font-size: 0.9rem; font-weight: 700; color: var(--text);
            display: flex; align-items: center; gap: 0.5rem;
        }
        .mars-title-pip {
            width: 8px; height: 8px; border-radius: 50%;
            animation: pip 2.5s ease-in-out infinite;
        }
        .pip-blue { background: var(--accent); box-shadow: 0 0 6px var(--accent); }
        .pip-cyan { background: var(--accent2); box-shadow: 0 0 6px var(--accent2); }
        .pip-gold { background: var(--gold); box-shadow: 0 0 6px var(--gold); }
        .pip-green { background: var(--green); box-shadow: 0 0 6px var(--green); }
        @keyframes pip { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:0.35; transform:scale(0.65); } }

        .mars-link-btn {
            font-size: 0.7rem; font-weight: 600; letter-spacing: 0.08em;
            text-transform: uppercase; font-family: var(--mono);
            color: var(--accent); text-decoration: none;
            padding: 0.3rem 0.7rem; border-radius: 8px;
            border: 1px solid rgba(59,130,246,0.25);
            transition: background 0.2s, border-color 0.2s;
        }
        .mars-link-btn:hover { background: rgba(59,130,246,0.1); border-color: var(--border-hi); }

        /* ═══ META GRID ═══ */
        .mars-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; margin-bottom: 1rem; }
        .mars-meta-item {
            padding: 0.9rem; border-radius: 12px;
            background: rgba(6,10,20,0.6); border: 1px solid var(--border);
            transition: border-color 0.2s;
        }
        .mars-meta-item:hover { border-color: rgba(59,130,246,0.2); }
        .mars-meta-k { font-size: 0.65rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); font-family: var(--mono); }
        .mars-meta-v { margin-top: 0.3rem; font-size: 1.05rem; font-weight: 700; font-family: var(--mono); color: var(--text); }

        /* ═══ LOAN ITEMS ═══ */
        .mars-loan-list { display: flex; flex-direction: column; gap: 0.6rem; }
        .mars-loan-item {
            padding: 1rem; border-radius: 12px;
            background: rgba(6,10,20,0.5); border: 1px solid var(--border);
            transition: border-color 0.2s, transform 0.2s;
        }
        .mars-loan-item:hover { border-color: rgba(59,130,246,0.25); transform: translateX(3px); }
        .ml-row { display: flex; align-items: center; justify-content: space-between; font-size: 0.82rem; }
        .ml-row + .ml-row { margin-top: 0.35rem; }
        .ml-key { color: var(--muted); font-family: var(--mono); font-size: 0.72rem; }
        .ml-val { color: var(--text); font-weight: 600; font-family: var(--mono); }
        .ml-id { color: var(--accent); font-family: var(--mono); font-weight: 700; }

        .mars-badge {
            display: inline-flex; align-items: center; gap: 0.3rem;
            padding: 0.2rem 0.6rem; border-radius: 999px;
            font-size: 0.62rem; font-weight: 700; letter-spacing: 0.08em;
            text-transform: uppercase; font-family: var(--mono);
        }
        .mars-badge::before { content:''; width:5px; height:5px; border-radius:50%; display:inline-block; }
        .badge-active { border:1px solid rgba(245,158,11,0.3); background:rgba(245,158,11,0.08); color:#fbbf24; }
        .badge-active::before { background:var(--gold); animation:pip 1.5s ease-in-out infinite; }
        .badge-closed { border:1px solid rgba(16,185,129,0.25); background:rgba(16,185,129,0.07); color:#34d399; }
        .badge-closed::before { background:var(--green); }

        /* ═══ CREDIT CARD VISUAL ═══ */
        .mars-cc {
            position: relative; border-radius: 16px; overflow: hidden;
            padding: 1.25rem 1.5rem; margin-bottom: 1rem;
            background: linear-gradient(135deg, #150828 0%, #1e0c3a 50%, #100621 100%);
            border: 1px solid rgba(139,92,246,0.2);
            box-shadow: 0 12px 30px rgba(139,92,246,0.1);
        }
        .mars-cc::before {
            content:''; position:absolute; width:220px; height:220px; border-radius:50%;
            background:radial-gradient(circle, rgba(139,92,246,0.18), transparent 70%);
            top:-70px; right:-50px; pointer-events:none;
        }
        .cc-inner { position:relative; z-index:1; }
        .cc-label { font-size:0.65rem; letter-spacing:0.15em; text-transform:uppercase; color:rgba(200,170,255,0.5); font-family:var(--mono); }
        .cc-num { font-family:var(--mono); font-size:0.9rem; letter-spacing:0.2em; color:rgba(220,195,255,0.75); margin-top:0.7rem; }
        .cc-row { display:flex; align-items:center; justify-content:space-between; margin-top:0.75rem; }
        .cc-exp { font-size:0.72rem; font-family:var(--mono); color:rgba(200,170,255,0.6); }
        .cc-brand { font-size:0.68rem; font-weight:700; letter-spacing:0.1em; color:rgba(200,170,255,0.35); font-family:var(--mono); }

        /* ═══ CREDIT BAR ═══ */
        .mars-bar-wrap { margin-top: 0.75rem; }
        .mars-bar {
            height: 6px; border-radius: 999px;
            background: rgba(255,255,255,0.07); overflow: hidden;
        }
        .mars-bar-fill {
            height: 100%; border-radius: 999px;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
            box-shadow: 0 0 10px rgba(59,130,246,0.5);
            animation: fillIn 1.2s cubic-bezier(0.22,1,0.36,1) 0.8s both;
        }
        @keyframes fillIn { from { width: 0 !important; } }
        .mars-bar-labels { display:flex; justify-content:space-between; font-size:0.68rem; font-family:var(--mono); color:var(--muted); margin-top:0.4rem; }

        /* ═══ TRANSACTIONS ═══ */
        .mars-tx-list { display: flex; flex-direction: column; gap: 0.5rem; }
        .mars-tx-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.9rem 1rem; border-radius: 12px;
            background: rgba(6,10,20,0.5); border: 1px solid var(--border);
            transition: border-color 0.2s, background 0.2s;
            cursor: pointer;
        }
        .mars-tx-item:hover { border-color: rgba(59,130,246,0.2); background: rgba(15,24,40,0.8); }
        .mars-tx-left { display: flex; align-items: center; gap: 0.85rem; }
        .mars-tx-icon {
            width: 38px; height: 38px; border-radius: 12px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.15);
            color: var(--accent2);
        }
        .mars-tx-icon svg { width: 16px; height: 16px; }
        .mars-tx-type { font-size: 0.82rem; font-weight: 600; color: var(--text); }
        .mars-tx-acc { font-size: 0.7rem; font-family: var(--mono); color: var(--muted); margin-top: 0.1rem; }
        .mars-tx-right { text-align: right; }
        .mars-tx-amt { font-size: 0.9rem; font-weight: 700; font-family: var(--mono); color: var(--green); }
        .mars-tx-date { font-size: 0.68rem; font-family: var(--mono); color: var(--muted); margin-top: 0.1rem; }

        /* ═══ EMPTY ═══ */
        .mars-empty { text-align:center; padding:2.5rem; color:var(--muted); font-size:0.82rem; font-family:var(--mono); }
        .mars-empty-icon { font-size:2rem; margin-bottom:0.6rem; opacity:0.35; display:block; }

        /* ═══ CHART AREA ═══ */
        .mars-chart-wrap { height: 100px; position: relative; overflow: hidden; margin-bottom: 1rem; }
        .mars-chart-svg { width:100%; height:100%; }
        .chart-line {
            fill: none; stroke: url(#lineGrad); stroke-width: 2.5;
            stroke-linecap: round; stroke-linejoin: round;
            stroke-dasharray: 1200; stroke-dashoffset: 1200;
            animation: drawLine 2s cubic-bezier(0.22,1,0.36,1) 0.8s forwards;
        }
        .chart-fill { opacity: 0.15; fill: url(#areaGrad); }
        @keyframes drawLine { to { stroke-dashoffset: 0; } }

        /* ═══ SCROLL BAR ═══ */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: rgba(59,130,246,0.25); border-radius: 2px; }

        /* ═══ KEYFRAME ═══ */
        @keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } }

        @media (max-width: 640px) {
            .mars-nav-links { display: none; }
            .bc-bal-amount { font-size: 1.8rem; }
        }
    </style>

    <div class="mars-root">
        <div class="mars-bg">
            <div class="mars-blob b1"></div>
            <div class="mars-blob b2"></div>
            <div class="mars-blob b3"></div>
            <div class="mars-grid"></div>
        </div>

        <div class="mars-wrap">

            {{-- TOP NAV --}}
            <nav class="mars-nav">
                <div class="mars-logo">
                    <div class="mars-logo-icon">M</div>
                    MARS
                </div>
                <div class="mars-nav-links">
                    <span class="mars-nav-link active">Dashboard</span>
                    <a href="{{ route('home') }}" class="mars-nav-link">Home</a>
                    <a href="{{ route('profile.edit') }}" class="mars-nav-link">Profile</a>
                    <a href="{{ route('personal.dashboard') }}" class="mars-nav-link">Personal</a>
                </div>
                <div class="mars-nav-right">
                    <div class="mars-notif" aria-label="Notifications">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="18" height="18">
                            <path d="M14.5 18a2.5 2.5 0 0 1-5 0"></path>
                            <path d="M18 16V11a6 6 0 1 0-12 0v5l-2 2h16l-2-2z"></path>
                        </svg>
                        <div class="mars-notif-dot"></div>
                    </div>
                    <div class="mars-avatar" title="{{ $user->name }}">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                </div>
            </nav>

            {{-- HERO ROW --}}
            <div class="mars-hero">

                {{-- MAIN BALANCE CARD --}}
                <div class="mars-balance-card">
                    <div class="bc-shine"></div>
                    <div class="bc-deco1"></div>
                    <div class="bc-deco2"></div>
                    <div class="bc-inner" x-data="{ show: false }">
                        <div class="bc-top">
                            <span class="bc-actype">{{ $account?->account_type ?? 'Prime Savings Account' }}</span>
                            <div class="bc-chip">@for($i=0;$i<9;$i++)<div class="bc-chip-cell"></div>@endfor</div>
                        </div>
                        <div class="bc-name">{{ strtoupper($user->name) }}</div>
                        <div class="bc-number">{{ $account?->A_Number ?? '•••• •••• •••• ••••' }}</div>
                        <div class="bc-bottom">
                            <div>
                                <div class="bc-bal-label">Available Balance</div>
                                <div class="bc-bal-amount" x-show="!show" style="cursor:pointer;" @click="show=!show">
                                    <span>$</span>••••••
                                </div>
                                <div class="bc-bal-amount" x-show="show" x-transition @click="show=!show" style="cursor:pointer;">
                                    ${{ number_format($balance, 2) }}
                                </div>
                                <div class="bc-change">↑ 12.4% this month</div>
                            </div>
                            <div class="bc-brand">MARS BANK<br>DIGITAL</div>
                        </div>
                    </div>
                </div>

                {{-- MINI STATS COL --}}
                <div class="mars-stats-col">
                    <div class="mars-stat">
                        <div class="mars-stat-left">
                            <div class="mars-stat-icon si-blue">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                            </div>
                            <div>
                                <div class="mars-stat-label">Loan Outstanding</div>
                                <div class="mars-stat-val">${{ number_format($loanOutstanding, 2) }}</div>
                            </div>
                        </div>
                        <span class="mars-stat-badge badge-warn">Active</span>
                    </div>
                    <div class="mars-stat">
                        <div class="mars-stat-left">
                            <div class="mars-stat-icon si-green">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                            </div>
                            <div>
                                <div class="mars-stat-label">Total Repaid</div>
                                <div class="mars-stat-val" style="color:var(--green)">${{ number_format($totalRepaid, 2) }}</div>
                            </div>
                        </div>
                        <span class="mars-stat-badge badge-up">↑ Good</span>
                    </div>
                    <div class="mars-stat">
                        <div class="mars-stat-left">
                            <div class="mars-stat-icon si-gold">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="5" width="20" height="14" rx="2"></rect><path d="M2 10h20"></path></svg>
                            </div>
                            <div>
                                <div class="mars-stat-label">Credit Available</div>
                                <div class="mars-stat-val" style="color:var(--gold)">${{ number_format($creditAvailable, 2) }}</div>
                            </div>
                        </div>
                        <span class="mars-stat-badge badge-neutral">Card</span>
                    </div>
                </div>
            </div>

            {{-- QUICK ACTIONS --}}
            <div class="mars-actions-row">
                <div class="mars-action">
                    <div class="mars-action-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 7h10v10"></path><path d="m7 17 10-10"></path></svg>
                    </div>
                    <div class="mars-action-label">Fund Transfer</div>
                </div>
                <div class="mars-action">
                    <div class="mars-action-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 4h14v16H5z"></path><path d="M8 8h8M8 12h8M8 16h5"></path></svg>
                    </div>
                    <div class="mars-action-label">Pay Bill</div>
                </div>
                <div class="mars-action">
                    <div class="mars-action-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="6" y="3" width="12" height="18" rx="2"></rect><path d="M12 8v8M8 12h8"></path></svg>
                    </div>
                    <div class="mars-action-label">Recharge</div>
                </div>
                <div class="mars-action">
                    <div class="mars-action-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 3h9l3 3v15H6z"></path><path d="M9 13h6M9 17h4M9 9h6"></path></svg>
                    </div>
                    <div class="mars-action-label">Statements</div>
                </div>
                <div class="mars-action">
                    <div class="mars-action-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    </div>
                    <div class="mars-action-label">Account</div>
                </div>
                <div class="mars-action">
                    <div class="mars-action-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1 1 0 0 0 .2 1.1l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1 1 0 0 0-1.1-.2 1 1 0 0 0-.6.9V20a2 2 0 1 1-4 0v-.2a1 1 0 0 0-.7-.9 1 1 0 0 0-1.1.2l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1 1 0 0 0 .2-1.1 1 1 0 0 0-.9-.6H4a2 2 0 1 1 0-4h.2a1 1 0 0 0 .9-.7 1 1 0 0 0-.2-1.1l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1 1 0 0 0 1.1.2H9a1 1 0 0 0 .6-.9V4a2 2 0 1 1 4 0v.2a1 1 0 0 0 .7.9 1 1 0 0 0 1.1-.2l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1 1 0 0 0-.2 1.1V9c0 .4.2.8.6.9H20a2 2 0 1 1 0 4h-.2a1 1 0 0 0-.4.1"></path></svg>
                    </div>
                    <div class="mars-action-label">Settings</div>
                </div>
            </div>

            {{-- SECTION GRID --}}
            <div class="mars-grid-layout">

                {{-- LOANS --}}
                <div class="mars-card d1">
                    <div class="mars-sec-hdr">
                        <div class="mars-sec-title"><span class="mars-title-pip pip-gold"></span>Loans</div>
                        <a href="{{ route('personal.loan') }}" class="mars-link-btn">Manage →</a>
                    </div>
                    <div class="mars-meta">
                        <div class="mars-meta-item">
                            <div class="mars-meta-k">Total Taken</div>
                            <div class="mars-meta-v">${{ number_format($totalLoanTaken, 2) }}</div>
                        </div>
                        <div class="mars-meta-item">
                            <div class="mars-meta-k">Repaid</div>
                            <div class="mars-meta-v" style="color:var(--green)">${{ number_format($totalRepaid, 2) }}</div>
                        </div>
                        <div class="mars-meta-item">
                            <div class="mars-meta-k">Outstanding</div>
                            <div class="mars-meta-v" style="color:var(--gold)">${{ number_format($loanOutstanding, 2) }}</div>
                        </div>
                        <div class="mars-meta-item">
                            <div class="mars-meta-k">Available</div>
                            <div class="mars-meta-v" style="color:var(--accent2)">${{ number_format($availableMoney, 2) }}</div>
                        </div>
                    </div>
                    @if($activeLoans->isEmpty())
                        <div class="mars-empty"><span class="mars-empty-icon">🔒</span>No active loans.</div>
                    @else
                        <div class="mars-loan-list">
                            @foreach($activeLoans as $loan)
                                @php $rem = (float)($loan->remaining_amount ?? $loan->L_Amount); @endphp
                                <div class="mars-loan-item">
                                    <div class="ml-row">
                                        <span class="ml-id">Loan #{{ $loan->L_ID }}</span>
                                        <span class="mars-badge {{ $rem > 0 ? 'badge-active' : 'badge-closed' }}">{{ $rem > 0 ? 'Active' : 'Closed' }}</span>
                                    </div>
                                    <div class="ml-row"><span class="ml-key">Amount</span><span class="ml-val">${{ number_format((float)$loan->L_Amount, 2) }}</span></div>
                                    <div class="ml-row"><span class="ml-key">Remaining</span><span class="ml-val" style="color:var(--gold)">${{ number_format($rem, 2) }}</span></div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- CREDIT CARD --}}
                <div class="mars-card d2">
                    <div class="mars-sec-hdr">
                        <div class="mars-sec-title"><span class="mars-title-pip pip-cyan"></span>Credit Card</div>
                    </div>
                    @if(!$creditCard)
                        <div class="mars-empty"><span class="mars-empty-icon">💳</span>No credit card found.</div>
                    @else
                        <div class="mars-cc">
                            <div class="cc-inner">
                                <div class="cc-label">Mars Credit</div>
                                <div class="cc-num">{{ $creditCard->masked_card_number }}</div>
                                <div class="cc-row">
                                    <div class="cc-exp">EXP {{ $creditCard->expiry_date }}</div>
                                    <div class="cc-brand">MARS BANK</div>
                                </div>
                            </div>
                        </div>
                        <div class="mars-meta">
                            <div class="mars-meta-item">
                                <div class="mars-meta-k">Credit Limit</div>
                                <div class="mars-meta-v">${{ number_format($creditLimit, 2) }}</div>
                            </div>
                            <div class="mars-meta-item">
                                <div class="mars-meta-k">Available</div>
                                <div class="mars-meta-v" style="color:var(--green)">${{ number_format($creditAvailable, 2) }}</div>
                            </div>
                        </div>
                        @if($creditLimit > 0)
                            @php $usedPct = min(100, round((($creditLimit - $creditAvailable) / $creditLimit) * 100)); @endphp
                            <div class="mars-bar-wrap">
                                <div class="mars-bar">
                                    <div class="mars-bar-fill" style="width:{{ $usedPct }}%"></div>
                                </div>
                                <div class="mars-bar-labels">
                                    <span>Used {{ $usedPct }}%</span>
                                    <span>Available {{ 100 - $usedPct }}%</span>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- RECENT TRANSACTIONS --}}
                <div class="mars-card mars-full d3">
                    <div class="mars-sec-hdr">
                        <div class="mars-sec-title"><span class="mars-title-pip pip-blue"></span>Recent Transactions</div>
                        <span style="font-size:0.7rem;font-family:var(--mono);color:var(--muted);">Last 30 days</span>
                    </div>
                    @if($recentTransactions->isEmpty())
                        <div class="mars-empty"><span class="mars-empty-icon">📂</span>No recent transactions found.</div>
                    @else
                        <div class="mars-tx-list">
                            @foreach($recentTransactions as $tx)
                                <div class="mars-tx-item">
                                    <div class="mars-tx-left">
                                        <div class="mars-tx-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                        </div>
                                        <div>
                                            <div class="mars-tx-type">{{ $tx->T_Type }}</div>
                                            <div class="mars-tx-acc">{{ $tx->A_Number }}</div>
                                        </div>
                                    </div>
                                    <div class="mars-tx-right">
                                        <div class="mars-tx-amt">+${{ number_format((float) $tx->T_Amount, 2) }}</div>
                                        <div class="mars-tx-date">{{ optional($tx->T_Date)->format('M d, Y') ?? $tx->created_at?->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>