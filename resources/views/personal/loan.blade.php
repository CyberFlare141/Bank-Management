<x-app-layout>
    @php
        $balance = (float) ($account->A_Balance ?? 0);
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Mono:wght@300;400;500&display=swap');

        :root {
            --ink:       #020917;
            --deep:      #050f20;
            --surface:   #081428;
            --panel:     #0c1d3a;
            --border:    rgba(56, 139, 253, 0.18);
            --border-hi: rgba(56, 139, 253, 0.45);
            --text:      #d8e8ff;
            --muted:     #6b8cb5;
            --accent:    #388bfd;
            --cyan:      #29d4f5;
            --emerald:   #23e6a0;
            --amber:     #f5c842;
            --danger:    #ff5c7a;
            --glow-a:    rgba(56, 139, 253, 0.35);
            --glow-c:    rgba(41, 212, 245, 0.25);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        .lb-root {
            font-family: 'Syne', sans-serif;
            background: var(--ink);
            min-height: calc(100vh - 64px);
            color: var(--text);
            position: relative;
            overflow-x: hidden;
        }

        /* ── ANIMATED BACKGROUND ── */
        .lb-bg {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .lb-bg::before {
            content: '';
            position: absolute;
            width: 900px; height: 900px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(56,139,253,0.12) 0%, transparent 70%);
            top: -300px; left: -200px;
            animation: lb-orb1 18s ease-in-out infinite alternate;
        }
        .lb-bg::after {
            content: '';
            position: absolute;
            width: 700px; height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(41,212,245,0.09) 0%, transparent 70%);
            bottom: -200px; right: -150px;
            animation: lb-orb2 22s ease-in-out infinite alternate;
        }
        .lb-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(56,139,253,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(56,139,253,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse 80% 60% at 50% 50%, black 20%, transparent 90%);
        }
        .lb-scan {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg,
                transparent 0%,
                rgba(56,139,253,0.03) 50%,
                transparent 100%
            );
            background-size: 100% 200px;
            animation: lb-scan 8s linear infinite;
        }

        @keyframes lb-orb1 {
            from { transform: translate(0,0) scale(1); }
            to   { transform: translate(80px, 60px) scale(1.15); }
        }
        @keyframes lb-orb2 {
            from { transform: translate(0,0) scale(1); }
            to   { transform: translate(-60px, -40px) scale(1.1); }
        }
        @keyframes lb-scan {
            0%   { background-position: 0 -200px; }
            100% { background-position: 0 100vh; }
        }

        /* ── LAYOUT ── */
        .lb-wrap {
            position: relative;
            z-index: 1;
            max-width: 1280px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 4rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* ── PANELS ── */
        .lb-card {
            background: linear-gradient(135deg, rgba(12,29,58,0.95) 0%, rgba(8,20,40,0.95) 100%);
            border: 1px solid var(--border);
            border-radius: 18px;
            backdrop-filter: blur(12px);
            box-shadow:
                0 0 0 1px rgba(56,139,253,0.06) inset,
                0 24px 48px rgba(2,9,23,0.6);
            opacity: 0;
            transform: translateY(20px);
            animation: lb-rise 0.6s cubic-bezier(0.22,1,0.36,1) forwards;
        }
        .lb-card:nth-child(1) { animation-delay: 0.05s; }
        .lb-card:nth-child(2) { animation-delay: 0.10s; }
        .lb-card:nth-child(3) { animation-delay: 0.15s; }
        .lb-card:nth-child(4) { animation-delay: 0.20s; }
        .lb-card:nth-child(5) { animation-delay: 0.25s; }
        .lb-card:nth-child(6) { animation-delay: 0.30s; }

        @keyframes lb-rise {
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── HEADER ── */
        .lb-header {
            padding: 2rem 2.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        .lb-header-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--accent), var(--cyan));
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
            box-shadow: 0 0 24px var(--glow-a);
            animation: lb-pulse-icon 3s ease-in-out infinite;
        }
        @keyframes lb-pulse-icon {
            0%,100% { box-shadow: 0 0 24px var(--glow-a); }
            50%      { box-shadow: 0 0 40px var(--glow-c), 0 0 60px var(--glow-a); }
        }
        .lb-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #fff 30%, var(--cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .lb-subtitle {
            font-size: 0.85rem;
            color: var(--muted);
            margin-top: 2px;
            font-weight: 400;
        }

        /* ── ALERTS ── */
        .lb-alert {
            padding: 0.85rem 1.25rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-family: 'DM Mono', monospace;
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .lb-alert::before { font-size: 1rem; }
        .lb-alert-ok  { border:1px solid rgba(35,230,160,0.35); background:rgba(35,230,160,0.08); color:#6effc9; }
        .lb-alert-ok::before { content:'✓'; color: var(--emerald); }
        .lb-alert-err { border:1px solid rgba(255,92,122,0.35); background:rgba(255,92,122,0.08); color:#ffa0b4; }
        .lb-alert-err::before { content:'✕'; color: var(--danger); }

        /* ── STAT GRID ── */
        .lb-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }
        @media (max-width: 1024px) { .lb-stats { grid-template-columns: repeat(2,1fr); } }
        @media (max-width: 640px)  { .lb-stats { grid-template-columns: 1fr; } }

        .lb-stat {
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            cursor: default;
            transition: border-color 0.3s, box-shadow 0.3s, transform 0.3s;
        }
        .lb-stat:hover {
            border-color: var(--border-hi);
            box-shadow: 0 0 32px rgba(56,139,253,0.18), 0 24px 48px rgba(2,9,23,0.6);
            transform: translateY(-3px);
        }
        .lb-stat-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        .lb-stat-label {
            font-size: 0.7rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            font-family: 'DM Mono', monospace;
        }
        .lb-stat-val {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 0.4rem;
            letter-spacing: -0.02em;
        }
        .lb-stat-note {
            font-size: 0.72rem;
            color: var(--muted);
            margin-top: 0.35rem;
            font-family: 'DM Mono', monospace;
        }

        /* shimmer sweep */
        .lb-stat::after {
            content: '';
            position: absolute;
            inset-block: 0;
            width: 60px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
            transform: skewX(-15deg);
            animation: lb-sweep 3.5s ease-in-out infinite;
        }
        @keyframes lb-sweep {
            0%   { left: -80px; }
            100% { left: calc(100% + 80px); }
        }

        .c-blue    { color: var(--accent); }
        .c-cyan    { color: var(--cyan); }
        .c-emerald { color: var(--emerald); }
        .c-amber   { color: var(--amber); }
        .c-danger  { color: var(--danger); }
        .bg-blue   { background: rgba(56,139,253,0.12); }
        .bg-cyan   { background: rgba(41,212,245,0.12); }
        .bg-emerald{ background: rgba(35,230,160,0.12); }
        .bg-amber  { background: rgba(245,200,66,0.12); }

        /* ── FORMS GRID ── */
        .lb-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        @media (max-width: 768px) { .lb-form-grid { grid-template-columns: 1fr; } }

        .lb-form-card { padding: 2rem 2.5rem; }

        .lb-form-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #e8f0ff;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }
        .lb-form-title span {
            width: 8px; height: 8px;
            border-radius: 50%;
            display: inline-block;
            animation: lb-dot 2s ease-in-out infinite;
        }
        .dot-blue   { background: var(--accent); box-shadow: 0 0 8px var(--accent); }
        .dot-emerald{ background: var(--emerald); box-shadow: 0 0 8px var(--emerald); }
        @keyframes lb-dot {
            0%,100% { opacity: 1; transform: scale(1); }
            50%      { opacity: 0.5; transform: scale(0.7); }
        }

        .lb-field { margin-bottom: 1.1rem; }
        .lb-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            color: var(--muted);
            font-family: 'DM Mono', monospace;
            margin-bottom: 0.45rem;
        }
        .lb-input, .lb-select {
            width: 100%;
            background: rgba(5,15,32,0.8);
            border: 1px solid rgba(56,139,253,0.2);
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            color: var(--text);
            font-family: 'DM Mono', monospace;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .lb-input:focus, .lb-select:focus {
            outline: none;
            border-color: var(--accent);
            background: rgba(5,15,32,0.95);
            box-shadow: 0 0 0 3px rgba(56,139,253,0.18), 0 0 16px rgba(56,139,253,0.12);
        }
        .lb-select option { background: #0c1d3a; color: var(--text); }

        /* ── BUTTONS ── */
        .lb-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.4rem;
            border-radius: 10px;
            font-family: 'Syne', sans-serif;
            font-size: 0.875rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
        }
        .lb-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.2s;
        }
        .lb-btn:hover { transform: translateY(-2px); filter: brightness(1.1); }
        .lb-btn:hover::after { opacity: 1; }
        .lb-btn:active { transform: translateY(0); }

        .lb-btn-blue {
            background: linear-gradient(135deg, #1d6bfa, #388bfd);
            color: #fff;
            box-shadow: 0 4px 18px rgba(56,139,253,0.35), 0 0 0 1px rgba(56,139,253,0.3);
        }
        .lb-btn-blue:hover {
            box-shadow: 0 8px 28px rgba(56,139,253,0.55), 0 0 0 1px rgba(56,139,253,0.5);
        }
        .lb-btn-emerald {
            background: linear-gradient(135deg, #0d8a62, #23e6a0);
            color: #012a1e;
            box-shadow: 0 4px 18px rgba(35,230,160,0.3);
        }
        .lb-btn-emerald:hover {
            box-shadow: 0 8px 28px rgba(35,230,160,0.5);
        }

        /* ── TABLE ── */
        .lb-table-wrap { padding: 2rem 2.5rem; }
        .lb-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.875rem;
            margin-top: 1.25rem;
        }
        .lb-table thead th {
            padding: 0.7rem 1rem;
            text-align: left;
            font-size: 0.68rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            font-family: 'DM Mono', monospace;
            background: rgba(5,15,32,0.7);
            border-bottom: 1px solid var(--border);
        }
        .lb-table thead th:first-child { border-radius: 10px 0 0 0; }
        .lb-table thead th:last-child  { border-radius: 0 10px 0 0; }
        .lb-table tbody td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid rgba(56,139,253,0.07);
            color: var(--text);
            font-family: 'DM Mono', monospace;
            transition: background 0.2s;
        }
        .lb-table tbody tr:hover td {
            background: rgba(56,139,253,0.05);
        }
        .lb-table tbody tr:last-child td { border-bottom: none; }

        .lb-id {
            color: var(--accent);
            font-weight: 500;
        }

        .lb-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        .lb-badge::before {
            content: '';
            width: 5px; height: 5px;
            border-radius: 50%;
            display: inline-block;
        }
        .lb-badge-active  {
            background: rgba(245,200,66,0.12);
            border: 1px solid rgba(245,200,66,0.3);
            color: #f5d060;
        }
        .lb-badge-active::before  { background: var(--amber); box-shadow: 0 0 6px var(--amber); animation: lb-dot 1.5s ease-in-out infinite; }
        .lb-badge-closed  {
            background: rgba(35,230,160,0.1);
            border: 1px solid rgba(35,230,160,0.25);
            color: #6effc9;
        }
        .lb-badge-closed::before { background: var(--emerald); }

        /* ── EMPTY STATE ── */
        .lb-empty {
            text-align: center;
            padding: 2.5rem;
            color: var(--muted);
            font-size: 0.875rem;
        }
        .lb-empty-icon { font-size: 2rem; margin-bottom: 0.75rem; opacity: 0.4; }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--ink); }
        ::-webkit-scrollbar-thumb { background: rgba(56,139,253,0.3); border-radius: 3px; }

        /* ── REDUCED MOTION ── */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>

    <div class="lb-root">
        <!-- animated bg -->
        <div class="lb-bg">
            <div class="lb-grid"></div>
            <div class="lb-scan"></div>
        </div>

        <div class="lb-wrap">

            {{-- HEADER --}}
            <div class="lb-card">
                <div class="lb-header">
                    <div class="lb-header-icon">🏦</div>
                    <div>
                        <div class="lb-title">Loan Management</div>
                        <div class="lb-subtitle">Take a loan, make repayments, and track your current loan position.</div>
                    </div>
                </div>
            </div>

            {{-- ALERTS --}}
            @if (session('loan_success'))
                <div class="lb-alert lb-alert-ok lb-card" style="padding:1rem 1.5rem;">
                    {{ session('loan_success') }}
                </div>
            @endif
            @if (session('loan_error'))
                <div class="lb-alert lb-alert-err lb-card" style="padding:1rem 1.5rem;">
                    {{ session('loan_error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="lb-alert lb-alert-err lb-card" style="padding:1rem 1.5rem; display:block;">
                    <ul style="list-style:disc;padding-left:1.2rem;margin-top:0.4rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- STAT CARDS --}}
            <div class="lb-stats">
                <div class="lb-card lb-stat">
                    <div class="lb-stat-icon bg-blue"><span class="c-blue">💳</span></div>
                    <div class="lb-stat-label">Total Loan Taken</div>
                    <div class="lb-stat-val c-blue">${{ number_format((float) $loanSummary['total_loan_taken'], 2) }}</div>
                </div>
                <div class="lb-card lb-stat">
                    <div class="lb-stat-icon bg-emerald"><span class="c-emerald">✓</span></div>
                    <div class="lb-stat-label">Total Repaid</div>
                    <div class="lb-stat-val c-emerald">${{ number_format((float) $loanSummary['total_repaid'], 2) }}</div>
                </div>
                <div class="lb-card lb-stat">
                    <div class="lb-stat-icon bg-amber"><span class="c-amber">⏳</span></div>
                    <div class="lb-stat-label">Remaining Balance</div>
                    <div class="lb-stat-val c-amber">${{ number_format((float) $loanSummary['remaining_loan_balance'], 2) }}</div>
                </div>
                <div class="lb-card lb-stat">
                    <div class="lb-stat-icon bg-cyan"><span class="c-cyan">◈</span></div>
                    <div class="lb-stat-label">Available Money</div>
                    <div class="lb-stat-val {{ $loanSummary['available_money'] >= 0 ? 'c-cyan' : 'c-danger' }}">
                        ${{ number_format((float) $loanSummary['available_money'], 2) }}
                    </div>
                    <div class="lb-stat-note">Account · ${{ number_format($balance, 2) }}</div>
                </div>
            </div>

            {{-- FORMS --}}
            <div class="lb-form-grid">
                {{-- TAKE LOAN --}}
                <div class="lb-card lb-form-card">
                    <div class="lb-form-title">
                        <span class="lb-badge-dot dot-blue" style="width:8px;height:8px;border-radius:50%;background:var(--accent);display:inline-block;animation:lb-dot 2s ease-in-out infinite;box-shadow:0 0 8px var(--accent);"></span>
                        Apply for a Loan
                    </div>
                    <form method="POST" action="{{ route('personal.loan.take') }}">
                        @csrf
                        <div class="lb-field">
                            <label for="loan_type" class="lb-label">Loan Type</label>
                            <input id="loan_type" name="loan_type" type="text"
                                   value="{{ old('loan_type', 'Personal Loan') }}"
                                   class="lb-input">
                        </div>
                        <div class="lb-field">
                            <label for="loan_amount" class="lb-label">Loan Amount ($)</label>
                            <input id="loan_amount" name="loan_amount" type="number"
                                   min="0.01" step="0.01"
                                   value="{{ old('loan_amount') }}"
                                   required class="lb-input" placeholder="0.00">
                        </div>
                        <div class="lb-field">
                            <label for="interest_rate" class="lb-label">Interest Rate (%)</label>
                            <input id="interest_rate" name="interest_rate" type="number"
                                   min="0" max="100" step="0.01"
                                   value="{{ old('interest_rate', '0') }}"
                                   class="lb-input" placeholder="0.00">
                        </div>
                        <button type="submit" class="lb-btn lb-btn-blue" style="margin-top:0.5rem;">
                            <span>⚡</span> Approve Loan
                        </button>
                    </form>
                </div>

                {{-- REPAY --}}
                <div class="lb-card lb-form-card">
                    <div class="lb-form-title">
                        <span style="width:8px;height:8px;border-radius:50%;background:var(--emerald);display:inline-block;animation:lb-dot 2s ease-in-out infinite 0.4s;box-shadow:0 0 8px var(--emerald);"></span>
                        Make a Repayment
                    </div>
                    @if ($activeLoans->isEmpty())
                        <div class="lb-empty">
                            <div class="lb-empty-icon">🔒</div>
                            No active loans available for repayment.
                        </div>
                    @else
                        <form method="POST" action="{{ route('personal.loan.repay') }}">
                            @csrf
                            <div class="lb-field">
                                <label for="loan_id" class="lb-label">Select Loan</label>
                                <select id="loan_id" name="loan_id" required class="lb-select">
                                    @foreach ($activeLoans as $loan)
                                        <option value="{{ $loan->L_ID }}" @selected((string) old('loan_id') === (string) $loan->L_ID)>
                                            Loan #{{ $loan->L_ID }} — Remaining ${{ number_format((float) ($loan->remaining_amount ?? $loan->L_Amount), 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="lb-field">
                                <label for="repayment_amount" class="lb-label">Repayment Amount ($)</label>
                                <input id="repayment_amount" name="repayment_amount" type="number"
                                       min="0.01" step="0.01"
                                       value="{{ old('repayment_amount') }}"
                                       required class="lb-input" placeholder="0.00">
                            </div>
                            <button type="submit" class="lb-btn lb-btn-emerald" style="margin-top:0.5rem;">
                                <span>↗</span> Submit Repayment
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- TABLE --}}
            <div class="lb-card">
                <div class="lb-table-wrap">
                    <div class="lb-form-title">
                        <span style="width:8px;height:8px;border-radius:50%;background:var(--cyan);display:inline-block;animation:lb-dot 2s ease-in-out infinite 0.8s;box-shadow:0 0 8px var(--cyan);"></span>
                        Loan Records
                    </div>
                    @if ($loans->isEmpty())
                        <div class="lb-empty">
                            <div class="lb-empty-icon">📂</div>
                            No loans found for this account.
                        </div>
                    @else
                        <div style="overflow-x:auto;">
                            <table class="lb-table">
                                <thead>
                                    <tr>
                                        <th>Loan ID</th>
                                        <th>Type</th>
                                        <th>Total Amount</th>
                                        <th>Remaining</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loans as $loan)
                                        @php $rem = (float) ($loan->remaining_amount ?? $loan->L_Amount); @endphp
                                        <tr>
                                            <td><span class="lb-id">#{{ $loan->L_ID }}</span></td>
                                            <td>{{ $loan->L_Type }}</td>
                                            <td>${{ number_format((float) $loan->L_Amount, 2) }}</td>
                                            <td>
                                                <span class="{{ $rem > 0 ? 'c-amber' : 'c-emerald' }}">
                                                    ${{ number_format($rem, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="lb-badge {{ $rem > 0 ? 'lb-badge-active' : 'lb-badge-closed' }}">
                                                    {{ $rem > 0 ? 'Active' : 'Closed' }}
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
    </div>
</x-app-layout>