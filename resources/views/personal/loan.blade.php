<x-app-layout>
    @php
        $balance = (float) ($account->A_Balance ?? 0);
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #060b18;
            --bg2: #0b1425;
            --panel: rgba(11, 20, 37, 0.85);
            --border: rgba(99, 179, 237, 0.12);
            --border-glow: rgba(99, 179, 237, 0.35);
            --text: #eaf2ff;
            --muted: #7b93b8;
            --blue: #1a6fc4;
            --blue-glow: #2d8ff5;
            --gold: #c49a4a;
            --gold-light: #e8c07a;
            --success: #4ade80;
            --danger: #f87171;
            --radius: 20px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body { font-family: 'DM Sans', sans-serif; }

        .mars-root {
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background */
        .mars-bg {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }

        .mars-bg-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: orb-float 8s ease-in-out infinite;
        }

        .orb1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #1a6fc4, transparent 70%);
            top: -100px; left: -100px;
            animation-delay: 0s;
        }

        .orb2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #c49a4a, transparent 70%);
            top: 200px; right: -80px;
            animation-delay: -3s;
        }

        .orb3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, #2d8ff5, transparent 70%);
            bottom: 100px; left: 30%;
            animation-delay: -5s;
        }

        .mars-grid-overlay {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(99, 179, 237, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99, 179, 237, 0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            z-index: 0;
        }

        /* Particle canvas */
        #particles { position: fixed; inset: 0; z-index: 0; pointer-events: none; opacity: 0.4; }

        .mars-content {
            position: relative;
            z-index: 10;
            max-width: 1160px;
            margin: 0 auto;
            padding: 0 1.5rem 4rem;
        }

        /* Topbar */
        .mars-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.2rem 0;
            border-bottom: 1px solid var(--border);
            margin-bottom: 2rem;
        }

        .mars-breadcrumb {
            display: flex;
            gap: 1.5rem;
            font-size: 0.82rem;
            color: var(--muted);
        }

        .mars-breadcrumb a {
            color: var(--muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .mars-breadcrumb a:hover { color: var(--text); }

        .mars-back-btn {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--muted);
            font-size: 0.82rem;
            padding: 0.4rem 0.9rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
        }

        .mars-back-btn:hover {
            border-color: var(--border-glow);
            color: var(--text);
        }

        /* Hero header */
        .mars-hero {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 2rem;
            padding: 2rem 2rem 1.8rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--panel);
            backdrop-filter: blur(12px);
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
            opacity: 0;
            animation: slide-up 0.6s cubic-bezier(0.22,1,0.36,1) 0.1s forwards;
        }

        .mars-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--blue-glow), var(--gold), transparent);
            opacity: 0.6;
        }

        .mars-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(26, 111, 196, 0.15);
            border: 1px solid rgba(99, 179, 237, 0.25);
            border-radius: 999px;
            padding: 0.25rem 0.75rem;
            font-size: 0.7rem;
            color: #7ec8f7;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 0.8rem;
        }

        .mars-hero-badge::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #4ade80;
            box-shadow: 0 0 6px #4ade80;
            animation: pulse-dot 2s ease-in-out infinite;
        }

        .mars-hero h1 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            font-weight: 800;
            color: #dceeff;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .mars-hero h1 span {
            background: linear-gradient(135deg, var(--gold-light), var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .mars-hero-sub {
            margin-top: 0.5rem;
            color: var(--muted);
            font-size: 0.88rem;
            max-width: 500px;
        }

        .mars-hero-amount {
            text-align: right;
            flex-shrink: 0;
        }

        .mars-hero-amount .label {
            font-size: 0.72rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .mars-hero-amount .value {
            font-family: 'Syne', sans-serif;
            font-size: 1.9rem;
            font-weight: 700;
            color: var(--gold-light);
            line-height: 1;
            margin-top: 0.2rem;
        }

        /* Alerts */
        .mars-alert {
            padding: 0.85rem 1.1rem;
            border-radius: 12px;
            font-size: 0.85rem;
            border: 1px solid;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            animation: slide-up 0.4s ease forwards;
        }

        .mars-alert.ok { border-color: rgba(74, 222, 128, 0.3); color: var(--success); background: rgba(74, 222, 128, 0.08); }
        .mars-alert.err { border-color: rgba(248, 113, 113, 0.3); color: var(--danger); background: rgba(248, 113, 113, 0.08); }

        .mars-alert::before {
            content: '';
            width: 8px; height: 8px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
        }

        /* Stats grid */
        .mars-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .mars-stat {
            padding: 1.1rem 1.2rem;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: rgba(9, 16, 32, 0.6);
            position: relative;
            overflow: hidden;
            cursor: default;
            transition: border-color 0.3s, transform 0.3s;
            opacity: 0;
            animation: slide-up 0.5s cubic-bezier(0.22,1,0.36,1) forwards;
        }

        .mars-stat:nth-child(1) { animation-delay: 0.2s; }
        .mars-stat:nth-child(2) { animation-delay: 0.28s; }
        .mars-stat:nth-child(3) { animation-delay: 0.36s; }
        .mars-stat:nth-child(4) { animation-delay: 0.44s; }

        .mars-stat:hover {
            border-color: var(--border-glow);
            transform: translateY(-3px);
        }

        .mars-stat::after {
            content: '';
            position: absolute;
            top: -200%; left: -60%;
            width: 50%; height: 400%;
            transform: rotate(20deg);
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.04), transparent);
            animation: shimmer 3s ease-in-out infinite;
        }

        .mars-stat-icon {
            font-size: 1.1rem;
            margin-bottom: 0.6rem;
            opacity: 0.7;
        }

        .mars-stat-label {
            font-size: 0.7rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 600;
        }

        .mars-stat-value {
            margin-top: 0.4rem;
            font-family: 'Syne', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: #dceeff;
        }

        .mars-stat-sub {
            margin-top: 0.2rem;
            font-size: 0.72rem;
            color: var(--muted);
        }

        /* Main sections */
        .mars-grid2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .mars-card {
            padding: 1.5rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--panel);
            backdrop-filter: blur(10px);
            opacity: 0;
            animation: slide-up 0.55s cubic-bezier(0.22,1,0.36,1) 0.45s forwards;
            transition: border-color 0.3s;
        }

        .mars-card:hover { border-color: var(--border-glow); }

        .mars-card-header {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            margin-bottom: 1rem;
        }

        .mars-card-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .icon-blue { background: rgba(26, 111, 196, 0.2); border: 1px solid rgba(99, 179, 237, 0.2); }
        .icon-gold { background: rgba(196, 154, 74, 0.2); border: 1px solid rgba(232, 192, 122, 0.2); }

        .mars-card h2 {
            font-family: 'Syne', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: #dceeff;
        }

        .mars-card-desc {
            font-size: 0.8rem;
            color: var(--muted);
            margin-top: 0.2rem;
        }

        /* Amount display */
        .mars-amount-range {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.7rem 0.9rem;
            background: rgba(26, 111, 196, 0.08);
            border: 1px solid rgba(99, 179, 237, 0.15);
            border-radius: 10px;
            margin-bottom: 1rem;
            font-size: 0.8rem;
            color: #7ec8f7;
        }

        /* Input styling */
        .mars-field { margin-bottom: 1rem; }

        .mars-field label {
            display: block;
            font-size: 0.76rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.07em;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        .mars-input {
            width: 100%;
            background: rgba(6, 11, 24, 0.8);
            border: 1px solid rgba(99, 179, 237, 0.2);
            border-radius: 10px;
            color: var(--text);
            padding: 0.65rem 0.85rem;
            font-size: 0.9rem;
            font-family: 'DM Sans', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .mars-input:focus {
            border-color: rgba(99, 179, 237, 0.5);
            box-shadow: 0 0 0 3px rgba(45, 143, 245, 0.1);
        }

        /* Slider */
        .mars-slider-wrap { position: relative; margin-top: 0.5rem; }

        .mars-slider {
            width: 100%;
            -webkit-appearance: none;
            height: 4px;
            border-radius: 4px;
            background: linear-gradient(90deg, var(--blue-glow) var(--pct, 100%), rgba(99,179,237,0.15) var(--pct, 100%));
            outline: none;
            cursor: pointer;
        }

        .mars-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 18px; height: 18px;
            border-radius: 50%;
            background: var(--blue-glow);
            border: 2px solid #fff;
            box-shadow: 0 0 10px rgba(45,143,245,0.6);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .mars-slider::-webkit-slider-thumb:hover { transform: scale(1.2); }

        /* Button */
        .mars-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
            border-radius: 10px;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.88rem;
            padding: 0.7rem 1.3rem;
            cursor: pointer;
            transition: all 0.25s;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .mars-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
            opacity: 0;
            transition: opacity 0.2s;
        }

        .mars-btn:hover::after { opacity: 1; }

        .mars-btn-primary {
            background: linear-gradient(135deg, #1a5fa8, var(--blue-glow));
            color: #fff;
            box-shadow: 0 4px 20px rgba(45, 143, 245, 0.3);
            width: 100%;
            margin-top: 0.5rem;
        }

        .mars-btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(45, 143, 245, 0.45);
        }

        .mars-btn-primary:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .mars-btn-secondary {
            background: rgba(20, 35, 60, 0.8);
            border: 1px solid var(--border);
            color: var(--muted);
        }

        .mars-btn-gold {
            background: linear-gradient(135deg, #9a6f2a, var(--gold));
            color: #fff;
            box-shadow: 0 4px 20px rgba(196, 154, 74, 0.3);
            width: 100%;
            margin-top: 0.5rem;
        }

        .mars-btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(196, 154, 74, 0.45);
        }

        /* Select */
        .mars-select {
            width: 100%;
            background: rgba(6, 11, 24, 0.8);
            border: 1px solid rgba(99, 179, 237, 0.2);
            border-radius: 10px;
            color: var(--text);
            padding: 0.65rem 0.85rem;
            font-size: 0.9rem;
            font-family: 'DM Sans', sans-serif;
            outline: none;
            cursor: pointer;
        }

        .mars-empty {
            padding: 2rem;
            text-align: center;
            color: var(--muted);
            font-size: 0.85rem;
        }

        .mars-empty-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            opacity: 0.4;
        }

        /* Tables */
        .mars-table-section {
            margin-bottom: 1rem;
            opacity: 0;
            animation: slide-up 0.55s cubic-bezier(0.22,1,0.36,1) 0.55s forwards;
        }

        .mars-table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .mars-table-title {
            font-family: 'Syne', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: #dceeff;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .mars-count-badge {
            background: rgba(26, 111, 196, 0.2);
            border: 1px solid rgba(99, 179, 237, 0.2);
            border-radius: 999px;
            padding: 0.1rem 0.5rem;
            font-size: 0.68rem;
            color: #7ec8f7;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
        }

        .mars-table-wrap {
            overflow-x: auto;
            border-radius: 14px;
            border: 1px solid var(--border);
        }

        .mars-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.83rem;
        }

        .mars-table thead {
            background: rgba(9, 16, 32, 0.8);
        }

        .mars-table th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.7rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 600;
            border-bottom: 1px solid var(--border);
        }

        .mars-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(99, 179, 237, 0.06);
            color: #c5d8f0;
        }

        .mars-table tbody tr {
            transition: background 0.2s;
        }

        .mars-table tbody tr:hover {
            background: rgba(26, 111, 196, 0.07);
        }

        .mars-table tbody tr:last-child td { border-bottom: none; }

        /* Status pills */
        .mars-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            border-radius: 999px;
            padding: 0.18rem 0.6rem;
            font-size: 0.66rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .mars-pill::before {
            content: '';
            width: 5px; height: 5px;
            border-radius: 50%;
            background: currentColor;
        }

        .pill-processing {
            border: 1px solid rgba(251, 191, 36, 0.35);
            color: #fbbf24;
            background: rgba(251, 191, 36, 0.1);
        }

        .pill-accepted {
            border: 1px solid rgba(74, 222, 128, 0.35);
            color: var(--success);
            background: rgba(74, 222, 128, 0.1);
        }

        .pill-rejected {
            border: 1px solid rgba(248, 113, 113, 0.35);
            color: var(--danger);
            background: rgba(248, 113, 113, 0.1);
        }

        .pill-active {
            border: 1px solid rgba(74, 222, 128, 0.35);
            color: var(--success);
            background: rgba(74, 222, 128, 0.1);
        }

        .pill-closed {
            border: 1px solid rgba(148, 163, 184, 0.3);
            color: #94a3b8;
            background: rgba(148, 163, 184, 0.08);
        }

        /* Modals */
        .mars-overlay {
            position: fixed;
            inset: 0;
            background: rgba(2, 8, 20, 0.85);
            backdrop-filter: blur(6px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 1rem;
        }

        .mars-overlay.open { display: flex; }

        .mars-modal {
            width: min(460px, 100%);
            background: rgba(9, 16, 32, 0.98);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 1.75rem;
            box-shadow: 0 32px 80px rgba(0,0,0,0.6);
            animation: modal-in 0.35s cubic-bezier(0.34,1.56,0.64,1) forwards;
            position: relative;
            overflow: hidden;
        }

        .mars-modal::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--blue-glow), transparent);
        }

        .mars-modal-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 1rem;
            background: rgba(26, 111, 196, 0.15);
            border: 1px solid rgba(99, 179, 237, 0.2);
        }

        .mars-modal h3 {
            font-family: 'Syne', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: #dceeff;
            margin-bottom: 0.4rem;
        }

        .mars-modal p {
            color: var(--muted);
            font-size: 0.84rem;
            line-height: 1.5;
        }

        .mars-modal-actions {
            display: flex;
            gap: 0.6rem;
            margin-top: 1.2rem;
        }

        .mars-modal-actions .mars-btn { flex: 1; }

        .mars-error-msg {
            color: var(--danger);
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: none;
            padding: 0.5rem 0.75rem;
            background: rgba(248, 113, 113, 0.08);
            border: 1px solid rgba(248, 113, 113, 0.2);
            border-radius: 8px;
        }

        /* OTP input special */
        .otp-input {
            letter-spacing: 0.4em;
            font-size: 1.4rem;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            text-align: center;
        }

        /* Divider */
        .mars-divider {
            height: 1px;
            background: var(--border);
            margin: 1.5rem 0;
        }

        /* Section label */
        .mars-section-label {
            font-size: 0.72rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .mars-section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* Keyframes */
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes modal-in {
            from { opacity: 0; transform: scale(0.92) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        @keyframes orb-float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(20px, -30px) scale(1.05); }
            66% { transform: translate(-15px, 15px) scale(0.95); }
        }

        @keyframes shimmer {
            0% { left: -60%; }
            100% { left: 130%; }
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        @keyframes count-up { from { opacity: 0; } to { opacity: 1; } }

        /* Responsive */
        @media (max-width: 900px) {
            .mars-stats { grid-template-columns: repeat(2, 1fr); }
            .mars-grid2 { grid-template-columns: 1fr; }
        }

        @media (max-width: 540px) {
            .mars-stats { grid-template-columns: 1fr; }
            .mars-hero { flex-direction: column; align-items: flex-start; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>

    <div class="mars-root py-6 sm:py-8">
        <!-- Background -->
        <div class="mars-bg">
            <div class="mars-bg-orb orb1"></div>
            <div class="mars-bg-orb orb2"></div>
            <div class="mars-bg-orb orb3"></div>
        </div>
        <div class="mars-grid-overlay"></div>
        <canvas id="particles"></canvas>

        <div class="mars-content">
            <!-- Topbar -->
            <nav class="mars-topbar">
                <div class="mars-breadcrumb">
                    <a href="{{ route('home') }}">🏠 Home</a>
                    <a href="{{ route('profile.edit') }}">👤 Profile</a>
                    <span style="color:#7b93b8">/ Loans</span>
                </div>
                <button type="button" onclick="history.back()" class="mars-back-btn">← Back</button>
            </nav>

            <!-- Hero -->
            <div class="mars-hero">
                <div>
                    <div class="mars-hero-badge">Instant Finance</div>
                    <h1>Loan <span>Management</span></h1>
                    <p class="mars-hero-sub">Request up to Tk {{ number_format((float)$instantLoanMaxAmount, 2) }} instantly. Secured with password + OTP dual verification.</p>
                </div>
                <div class="mars-hero-amount">
                    <p class="label">Max Available</p>
                    <p class="value">Tk {{ number_format((float)$instantLoanMaxAmount, 0) }}</p>
                </div>
            </div>

            <!-- Alerts -->
            @if (session('loan_success'))
                <div class="mars-alert ok">{{ session('loan_success') }}</div>
            @endif
            @if (session('loan_error'))
                <div class="mars-alert err">{{ session('loan_error') }}</div>
            @endif
            @if ($errors->any())
                <div class="mars-alert err">
                    @foreach ($errors->all() as $error)
                        <span>{{ $error }}</span>
                    @endforeach
                </div>
            @endif

            <!-- Stats -->
            <div class="mars-stats">
                <article class="mars-stat">
                    <div class="mars-stat-icon">📊</div>
                    <p class="mars-stat-label">Total Loan Taken</p>
                    <p class="mars-stat-value" data-count="{{ (float)$loanSummary['total_loan_taken'] }}">Tk {{ number_format((float)$loanSummary['total_loan_taken'], 2) }}</p>
                </article>
                <article class="mars-stat">
                    <div class="mars-stat-icon">✅</div>
                    <p class="mars-stat-label">Total Repaid</p>
                    <p class="mars-stat-value">Tk {{ number_format((float)$loanSummary['total_repaid'], 2) }}</p>
                </article>
                <article class="mars-stat">
                    <div class="mars-stat-icon">⏳</div>
                    <p class="mars-stat-label">Remaining Balance</p>
                    <p class="mars-stat-value" style="color: #f87171;">Tk {{ number_format((float)$loanSummary['remaining_loan_balance'], 2) }}</p>
                </article>
                <article class="mars-stat">
                    <div class="mars-stat-icon">💳</div>
                    <p class="mars-stat-label">Available Money</p>
                    <p class="mars-stat-value" style="color: #4ade80;">Tk {{ number_format((float)$loanSummary['available_money'], 2) }}</p>
                    <p class="mars-stat-sub">Balance: Tk {{ number_format($balance, 2) }}</p>
                </article>
            </div>

            <!-- Action Cards -->
            <div class="mars-section-label">Actions</div>
            <div class="mars-grid2">
                <!-- Take Loan -->
                <article class="mars-card">
                    <div class="mars-card-header">
                        <div class="mars-card-icon icon-blue">💰</div>
                        <div>
                            <h2>Take Instant Loan</h2>
                            <p class="mars-card-desc">No document upload required</p>
                        </div>
                    </div>

                    <div class="mars-amount-range">
                        <span>Min: <strong>Tk {{ number_format((float)$instantLoanMinAmount, 0) }}</strong></span>
                        <span style="opacity:0.4">━━━━━━</span>
                        <span>Max: <strong>Tk {{ number_format((float)$instantLoanMaxAmount, 0) }}</strong></span>
                    </div>

                    <form id="loan-request-form">
                        <div class="mars-field">
                            <label for="requested_amount">Loan Amount</label>
                            <input
                                id="requested_amount"
                                name="requested_amount"
                                type="number"
                                min="{{ (float)$instantLoanMinAmount }}"
                                max="{{ (float)$instantLoanMaxAmount }}"
                                step="0.01"
                                value="{{ old('requested_amount', (float)$instantLoanMaxAmount) }}"
                                required
                                class="mars-input"
                                placeholder="Enter amount..."
                            >
                        </div>
                        <div class="mars-field" style="margin-bottom:0.5rem">
                            <label>Adjust Amount</label>
                            <input type="range"
                                id="amount_slider"
                                class="mars-slider"
                                min="{{ (float)$instantLoanMinAmount }}"
                                max="{{ (float)$instantLoanMaxAmount }}"
                                step="100"
                                value="{{ old('requested_amount', (float)$instantLoanMaxAmount) }}"
                                style="margin-top:0.5rem"
                            >
                        </div>

                        @if (!$canRequestLoan)
                            <p style="font-size:0.8rem;color:var(--danger);margin-bottom:0.5rem">⚠ Create your customer profile and account first.</p>
                        @elseif (!$hasOtpEmail)
                            <p style="font-size:0.8rem;color:var(--danger);margin-bottom:0.5rem">⚠ Add email to your profile to receive OTP.</p>
                        @endif

                        <button type="button" id="request-loan-btn" class="mars-btn mars-btn-primary" @disabled(!$canRequestLoan || !$hasOtpEmail)>
                            <span>⚡</span> Request Loan Now
                        </button>
                    </form>
                </article>

                <!-- Repayment -->
                <article class="mars-card">
                    <div class="mars-card-header">
                        <div class="mars-card-icon icon-gold">💸</div>
                        <div>
                            <h2>Make Repayment</h2>
                            <p class="mars-card-desc">Pay off your active loans</p>
                        </div>
                    </div>

                    @if ($activeLoans->isEmpty())
                        <div class="mars-empty">
                            <div class="mars-empty-icon">🎉</div>
                            <p>No active loans. You're debt-free!</p>
                        </div>
                    @else
                        <form method="POST" action="{{ route('personal.loan.repay') }}" class="space-y-3">
                            @csrf
                            <div class="mars-field">
                                <label for="loan_id">Select Loan</label>
                                <select id="loan_id" name="loan_id" required class="mars-select">
                                    @foreach ($activeLoans as $loan)
                                        <option value="{{ $loan->L_ID }}" @selected((string)old('loan_id') === (string)$loan->L_ID)>
                                            Loan #{{ $loan->L_ID }} · Remaining Tk {{ number_format((float)($loan->remaining_amount ?? $loan->L_Amount), 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mars-field">
                                <label for="repayment_amount">Repayment Amount (Tk)</label>
                                <input id="repayment_amount" name="repayment_amount" type="number" min="0.01" step="0.01" value="{{ old('repayment_amount') }}" required class="mars-input" placeholder="0.00">
                            </div>
                            <button type="submit" class="mars-btn mars-btn-gold">
                                <span>💳</span> Submit Repayment
                            </button>
                        </form>
                    @endif
                </article>
            </div>

            <!-- Loan Request History -->
            <div class="mars-section-label" style="margin-top:1.5rem">History & Records</div>
            <div class="mars-card mars-table-section" style="margin-bottom:1rem;">
                <div class="mars-table-header">
                    <div class="mars-table-title">
                        📋 Loan Request History
                        @if (!$loanRequests->isEmpty())
                            <span class="mars-count-badge">{{ $loanRequests->count() }} records</span>
                        @endif
                    </div>
                </div>

                @if ($loanRequests->isEmpty())
                    <div class="mars-empty">
                        <div class="mars-empty-icon">📭</div>
                        <p>No loan requests found yet.</p>
                    </div>
                @else
                    <div class="mars-table-wrap">
                        <table class="mars-table">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Requested</th>
                                    <th>Processed</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loanRequests as $loanRequest)
                                    @php $st = strtolower((string)$loanRequest->status); @endphp
                                    <tr>
                                        <td style="color:#7ec8f7;font-weight:600">#{{ $loanRequest->LR_ID }}</td>
                                        <td style="font-weight:600">Tk {{ number_format((float)$loanRequest->requested_amount, 2) }}</td>
                                        <td>
                                            <span class="mars-pill pill-{{ in_array($st,['processing','accepted','rejected'],true) ? $st : 'processing' }}">
                                                {{ ucfirst($st) }}
                                            </span>
                                        </td>
                                        <td>{{ $loanRequest->created_at?->format('M d, Y · h:i A') }}</td>
                                        <td>{{ $loanRequest->processed_at?->format('M d, Y · h:i A') ?? '—' }}</td>
                                        <td style="color:var(--muted)">{{ $loanRequest->decision_note ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Loan Records -->
            <div class="mars-card mars-table-section">
                <div class="mars-table-header">
                    <div class="mars-table-title">
                        🏦 Loan Records
                        @if (!$loans->isEmpty())
                            <span class="mars-count-badge">{{ $loans->count() }} loans</span>
                        @endif
                    </div>
                </div>

                @if ($loans->isEmpty())
                    <div class="mars-empty">
                        <div class="mars-empty-icon">📂</div>
                        <p>No loans found for this account.</p>
                    </div>
                @else
                    <div class="mars-table-wrap">
                        <table class="mars-table">
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
                                    @php
                                        $remaining = (float)($loan->remaining_amount ?? $loan->L_Amount);
                                        $loanStatus = $remaining > 0 ? 'active' : 'closed';
                                    @endphp
                                    <tr>
                                        <td style="color:#7ec8f7;font-weight:600">#{{ $loan->L_ID }}</td>
                                        <td>{{ $loan->L_Type }}</td>
                                        <td>Tk {{ number_format((float)$loan->L_Amount, 2) }}</td>
                                        <td style="{{ $remaining > 0 ? 'color:#f87171' : 'color:#4ade80' }}; font-weight:600">Tk {{ number_format($remaining, 2) }}</td>
                                        <td>
                                            <span class="mars-pill pill-{{ $loanStatus }}">
                                                {{ $remaining > 0 ? 'Active' : 'Closed' }}
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

    <!-- Password Modal -->
    <div id="password-modal" class="mars-overlay" aria-hidden="true">
        <div class="mars-modal">
            <div class="mars-modal-icon">🔐</div>
            <h3>Confirm Password</h3>
            <p>Enter your account password to proceed with the loan request.</p>
            <div class="mars-field" style="margin-top:1rem">
                <label for="loan_password">Account Password</label>
                <input id="loan_password" type="password" class="mars-input" placeholder="••••••••" autocomplete="current-password">
            </div>
            <div id="password-error" class="mars-error-msg"></div>
            <div class="mars-modal-actions">
                <button id="cancel-password-btn" class="mars-btn mars-btn-secondary">Cancel</button>
                <button id="submit-password-btn" class="mars-btn mars-btn-primary" style="margin:0">Verify →</button>
            </div>
        </div>
    </div>

    <!-- OTP Modal -->
    <div id="otp-modal" class="mars-overlay" aria-hidden="true">
        <div class="mars-modal">
            <div class="mars-modal-icon">📱</div>
            <h3>OTP Verification</h3>
            <p id="otp-hint">Enter the 6-digit OTP sent to your email.</p>
            <div class="mars-field" style="margin-top:1rem">
                <label for="loan_otp">One-Time Password</label>
                <input id="loan_otp" type="text" maxlength="6" inputmode="numeric" class="mars-input otp-input" placeholder="000000" autocomplete="one-time-code">
            </div>
            <div id="otp-error" class="mars-error-msg"></div>
            <div class="mars-modal-actions">
                <button id="cancel-otp-btn" class="mars-btn mars-btn-secondary">Cancel</button>
                <button id="submit-otp-btn" class="mars-btn mars-btn-primary" style="margin:0">Verify OTP →</button>
            </div>
        </div>
    </div>

    <script>
    (() => {
        // Particle system
        const canvas = document.getElementById('particles');
        const ctx = canvas.getContext('2d');
        let particles = [];

        const resize = () => { canvas.width = window.innerWidth; canvas.height = window.innerHeight; };
        resize();
        window.addEventListener('resize', resize);

        for (let i = 0; i < 60; i++) {
            particles.push({
                x: Math.random() * window.innerWidth,
                y: Math.random() * window.innerHeight,
                r: Math.random() * 1.5 + 0.3,
                vx: (Math.random() - 0.5) * 0.25,
                vy: (Math.random() - 0.5) * 0.25,
                alpha: Math.random() * 0.5 + 0.1
            });
        }

        const animParticles = () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => {
                p.x += p.vx; p.y += p.vy;
                if (p.x < 0) p.x = canvas.width;
                if (p.x > canvas.width) p.x = 0;
                if (p.y < 0) p.y = canvas.height;
                if (p.y > canvas.height) p.y = 0;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(99,179,237,${p.alpha})`;
                ctx.fill();
            });
            requestAnimationFrame(animParticles);
        };
        animParticles();

        // Slider sync
        const amountInput = document.getElementById('requested_amount');
        const slider = document.getElementById('amount_slider');
        const minA = parseFloat('{{ (float)$instantLoanMinAmount }}');
        const maxA = parseFloat('{{ (float)$instantLoanMaxAmount }}');

        const updateSliderBg = (val) => {
            const pct = ((val - minA) / (maxA - minA)) * 100;
            slider.style.setProperty('--pct', pct + '%');
        };
        updateSliderBg(slider.value);

        slider.addEventListener('input', () => {
            amountInput.value = slider.value;
            updateSliderBg(slider.value);
        });

        amountInput.addEventListener('input', () => {
            slider.value = amountInput.value;
            updateSliderBg(amountInput.value);
        });

        // Loan request logic
        const requestBtn = document.getElementById('request-loan-btn');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const pwModal = document.getElementById('password-modal');
        const otpModal = document.getElementById('otp-modal');
        const pwInput = document.getElementById('loan_password');
        const otpInput = document.getElementById('loan_otp');
        const pwError = document.getElementById('password-error');
        const otpError = document.getElementById('otp-error');
        const otpHint = document.getElementById('otp-hint');

        let pending = false;

        const openModal = m => { m.classList.add('open'); m.setAttribute('aria-hidden','false'); };
        const closeModal = m => { m.classList.remove('open'); m.setAttribute('aria-hidden','true'); };
        const showErr = (el, msg) => { el.textContent = msg; el.style.display = 'block'; };
        const clearErr = el => { el.textContent = ''; el.style.display = 'none'; };
        const setLoading = v => {
            pending = v;
            document.getElementById('submit-password-btn').disabled = v;
            document.getElementById('submit-otp-btn').disabled = v;
        };

        const parseAmt = () => Number(amountInput.value);
        const validateAmt = () => {
            const a = parseAmt();
            if (!Number.isFinite(a) || a < minA || a > maxA)
                return `Amount must be between Tk ${minA.toFixed(2)} and Tk ${maxA.toFixed(2)}.`;
            return '';
        };

        if (requestBtn) requestBtn.addEventListener('click', () => {
            if (pending) return;
            const err = validateAmt();
            clearErr(pwError);
            pwInput.value = '';
            openModal(pwModal);
            if (err) { showErr(pwError, err); return; }
            setTimeout(() => pwInput.focus(), 50);
        });

        document.getElementById('cancel-password-btn').addEventListener('click', () => { if (!pending) closeModal(pwModal); });
        document.getElementById('cancel-otp-btn').addEventListener('click', () => { if (!pending) closeModal(otpModal); });

        // Password verify
        document.getElementById('submit-password-btn').addEventListener('click', async () => {
            if (pending) return;
            clearErr(pwError);
            const err = validateAmt();
            if (err) { showErr(pwError, err); return; }
            const pw = pwInput.value.trim();
            if (!pw) { showErr(pwError, 'Password is required.'); return; }
            setLoading(true);
            try {
                const res = await fetch('{{ route('personal.loan.request-password') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ requested_amount: parseAmt(), password: pw })
                });
                const data = await res.json();
                if (!res.ok) { showErr(pwError, data?.message || 'Password verification failed.'); return; }
                closeModal(pwModal);
                otpInput.value = '';
                clearErr(otpError);
                otpHint.textContent = `Enter the 6-digit OTP sent to ${data.masked_email}. Expires in 5 minutes.`;
                openModal(otpModal);
                setTimeout(() => otpInput.focus(), 50);
            } catch(e) { showErr(pwError, 'Network error. Please try again.'); }
            finally { setLoading(false); }
        });

        // OTP verify
        document.getElementById('submit-otp-btn').addEventListener('click', async () => {
            if (pending) return;
            clearErr(otpError);
            const otp = otpInput.value.trim();
            if (!/^\d{6}$/.test(otp)) { showErr(otpError, 'Enter a valid 6-digit OTP.'); return; }
            setLoading(true);
            try {
                const res = await fetch('{{ route('personal.loan.verify-otp') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ otp })
                });
                const data = await res.json();
                if (!res.ok) {
                    const att = Number.isInteger(data?.attempts_remaining) ? ` Attempts left: ${data.attempts_remaining}.` : '';
                    showErr(otpError, (data?.message || 'OTP verification failed.') + att);
                    return;
                }
                closeModal(otpModal);
                window.location.reload();
            } catch(e) { showErr(otpError, 'Network error. Please try again.'); }
            finally { setLoading(false); }
        });

        // Enter key support
        pwInput?.addEventListener('keydown', e => { if (e.key === 'Enter') document.getElementById('submit-password-btn').click(); });
        otpInput?.addEventListener('keydown', e => { if (e.key === 'Enter') document.getElementById('submit-otp-btn').click(); });

    })();
    </script>
</x-app-layout>