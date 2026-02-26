<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MARS Bank | Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #05090f;
            --panel: rgba(8, 16, 30, 0.92);
            --border: rgba(80, 160, 240, 0.13);
            --border-focus: rgba(80, 160, 240, 0.45);
            --text: #e8f2ff;
            --muted: #6a87aa;
            --blue: #1868c4;
            --blue-bright: #3b9eff;
            --gold: #c4934a;
            --gold-bright: #f0b96a;
            --success: #3dd68c;
            --danger: #f87171;
        }

        html, body {
            height: 100%;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ── SCENE ── */
        .scene {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            position: relative;
        }

        /* ── LEFT PANEL ── */
        .scene-left {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2.5rem 3rem;
            overflow: hidden;
            background: linear-gradient(155deg, #06101e 0%, #0a1628 60%, #060d1a 100%);
        }

        .scene-left::after {
            content: '';
            position: absolute;
            right: 0; top: 0; bottom: 0;
            width: 1px;
            background: linear-gradient(180deg, transparent, rgba(80,160,240,0.3) 30%, rgba(196,147,74,0.3) 70%, transparent);
        }

        /* Hex grid bg */
        .hex-bg {
            position: absolute;
            inset: 0;
            opacity: 0.035;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='56' height='100'%3E%3Cpath d='M28 66L0 50V18L28 2l28 16v32z' fill='none' stroke='%234aa0f0' stroke-width='1'/%3E%3C/svg%3E");
            background-size: 56px 100px;
        }

        /* Animated orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
        }
        .orb-1 { width: 420px; height: 420px; background: radial-gradient(circle, rgba(24,104,196,0.35), transparent 70%); top: -80px; left: -80px; animation: orb-drift 10s ease-in-out infinite; }
        .orb-2 { width: 320px; height: 320px; background: radial-gradient(circle, rgba(196,147,74,0.22), transparent 70%); bottom: 0; right: -60px; animation: orb-drift 13s ease-in-out infinite reverse; }
        .orb-3 { width: 200px; height: 200px; background: radial-gradient(circle, rgba(59,158,255,0.18), transparent 70%); top: 50%; left: 40%; animation: orb-drift 8s ease-in-out infinite 2s; }

        /* Left top brand */
        .left-brand {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }

        .brand-logo {
            width: 44px; height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--blue), var(--blue-bright));
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            color: #fff;
            box-shadow: 0 0 24px rgba(59,158,255,0.4);
            flex-shrink: 0;
        }

        .brand-name {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.4rem;
            letter-spacing: 0.08em;
            color: #dceeff;
        }

        .brand-tagline {
            font-size: 0.75rem;
            color: var(--muted);
            letter-spacing: 0.06em;
        }

        /* Left center hero */
        .left-hero {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 0;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(24,104,196,0.15);
            border: 1px solid rgba(80,160,240,0.22);
            border-radius: 999px;
            padding: 0.25rem 0.8rem;
            font-size: 0.7rem;
            color: #7ec8f7;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 1.2rem;
            width: fit-content;
        }

        .hero-eyebrow-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 7px var(--success);
            animation: pulse 2s ease-in-out infinite;
        }

        .hero-headline {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2.2rem, 3.5vw, 3rem);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -0.03em;
            color: #dceeff;
            margin-bottom: 1.2rem;
        }

        .hero-headline em {
            font-style: normal;
            background: linear-gradient(135deg, var(--gold-bright), var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-body {
            color: var(--muted);
            font-size: 0.92rem;
            line-height: 1.7;
            max-width: 380px;
            margin-bottom: 2.5rem;
        }

        /* Stats row */
        .stat-row {
            display: flex;
            gap: 1.5rem;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .stat-value {
            font-family: 'Syne', sans-serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: #dceeff;
        }

        .stat-label {
            font-size: 0.7rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }

        .stat-divider {
            width: 1px;
            background: var(--border);
            align-self: stretch;
        }

        /* Floating card decoration */
        .float-card {
            position: absolute;
            right: -20px;
            top: 50%;
            transform: translateY(-50%);
            width: 180px;
            background: rgba(10, 22, 42, 0.9);
            border: 1px solid rgba(80,160,240,0.2);
            border-radius: 16px;
            padding: 1rem;
            backdrop-filter: blur(12px);
            animation: float-card 4s ease-in-out infinite;
            z-index: 3;
        }

        .float-card-label { font-size: 0.65rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.4rem; }
        .float-card-amount { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 700; color: var(--success); }
        .float-card-sub { font-size: 0.68rem; color: var(--muted); margin-top: 0.2rem; }
        .float-card-bar { margin-top: 0.7rem; height: 3px; border-radius: 3px; background: rgba(80,160,240,0.15); overflow: hidden; }
        .float-card-bar-fill { height: 100%; width: 72%; background: linear-gradient(90deg, var(--blue), var(--blue-bright)); border-radius: 3px; animation: bar-grow 2.5s ease-out 0.5s both; }

        /* Left bottom security badges */
        .security-row {
            position: relative;
            z-index: 2;
            display: flex;
            gap: 1.2rem;
        }

        .sec-badge {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.72rem;
            color: var(--muted);
        }

        .sec-badge-icon {
            width: 22px; height: 22px;
            border-radius: 6px;
            background: rgba(80,160,240,0.1);
            border: 1px solid rgba(80,160,240,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem;
        }

        /* ── RIGHT PANEL (form) ── */
        .scene-right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 3rem;
            position: relative;
            background: radial-gradient(ellipse at 60% 30%, rgba(24,104,196,0.07), transparent 60%),
                        radial-gradient(ellipse at 30% 80%, rgba(196,147,74,0.05), transparent 50%),
                        var(--bg);
        }

        /* Right subtle grid */
        .scene-right::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(80,160,240,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(80,160,240,0.04) 1px, transparent 1px);
            background-size: 44px 44px;
            pointer-events: none;
        }

        /* Form card */
        .auth-card {
            position: relative;
            z-index: 2;
            width: min(440px, 100%);
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 2.2rem 2rem;
            backdrop-filter: blur(16px);
            box-shadow: 0 32px 80px rgba(0,4,16,0.6), 0 0 0 1px rgba(80,160,240,0.06) inset;
            animation: card-enter 0.7s cubic-bezier(0.22,1,0.36,1) 0.1s both;
        }

        /* Top shimmer line */
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0; left: 10%; right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--blue-bright), var(--gold-bright), transparent);
            opacity: 0.5;
            border-radius: 999px;
        }

        /* Card header */
        .card-header {
            text-align: center;
            margin-bottom: 1.8rem;
        }

        .card-header-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 0.8rem;
        }

        .card-brand-m {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--blue), var(--blue-bright));
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1rem;
            color: #fff;
            box-shadow: 0 0 16px rgba(59,158,255,0.35);
        }

        .card-brand-name {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            letter-spacing: 0.1em;
            color: #dceeff;
        }

        .card-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.45rem;
            font-weight: 700;
            color: #dceeff;
            margin-bottom: 0.3rem;
        }

        .card-sub {
            font-size: 0.82rem;
            color: var(--muted);
        }

        /* Switcher */
        .switcher {
            display: flex;
            background: rgba(5, 10, 22, 0.7);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 3px;
            margin-bottom: 1.5rem;
            gap: 3px;
        }

        .switcher a, .switcher span {
            flex: 1;
            text-align: center;
            padding: 0.5rem;
            border-radius: 8px;
            font-size: 0.83rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s;
            font-family: 'Syne', sans-serif;
        }

        .switcher .is-active {
            background: linear-gradient(135deg, var(--blue), var(--blue-bright));
            color: #fff;
            box-shadow: 0 4px 14px rgba(59,158,255,0.3);
        }

        .switcher a {
            color: var(--muted);
        }

        .switcher a:hover { color: var(--text); }

        /* Session status */
        .status-message {
            font-size: 0.82rem;
            color: var(--success);
            background: rgba(61, 214, 140, 0.08);
            border: 1px solid rgba(61, 214, 140, 0.25);
            border-radius: 8px;
            padding: 0.6rem 0.8rem;
            margin-bottom: 1rem;
        }

        /* Fields */
        .field {
            margin-bottom: 1rem;
        }

        .field label {
            display: block;
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.4rem;
            transition: color 0.2s;
        }

        .field:focus-within label { color: #7ec8f7; }

        .input-shell {
            display: flex;
            align-items: center;
            background: rgba(4, 9, 20, 0.75);
            border: 1px solid rgba(80,160,240,0.18);
            border-radius: 12px;
            overflow: hidden;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-shell:focus-within {
            border-color: rgba(80,160,240,0.55);
            box-shadow: 0 0 0 3px rgba(59,158,255,0.1), 0 2px 16px rgba(59,158,255,0.08);
        }

        .field-icon {
            width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            color: var(--muted);
            flex-shrink: 0;
            border-right: 1px solid rgba(80,160,240,0.1);
            height: 100%;
            padding: 0.7rem 0;
            transition: color 0.2s;
            font-family: monospace;
            font-weight: 700;
        }

        .input-shell:focus-within .field-icon { color: #7ec8f7; }

       .input-shell input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    color: var(--text);
    font-size: 0.9rem;
    font-family: 'DM Sans', sans-serif;
    padding: 0.7rem 0.9rem;
}

.input-shell input:-webkit-autofill,
.input-shell input:-webkit-autofill:hover,
.input-shell input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0 1000px #04091400 inset !important;
    box-shadow: 0 0 0 1000px rgba(4, 9, 20, 0.75) inset !important;
    -webkit-text-fill-color: #e8f2ff !important;
    background-color: transparent !important;
    transition: background-color 5000s ease-in-out 0s;
}

        .input-shell input::placeholder { color: rgba(106,135,170,0.5); }

        /* Password toggle */
        .pw-toggle {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0 0.75rem;
            color: var(--muted);
            font-size: 0.85rem;
            transition: color 0.2s;
        }
        .pw-toggle:hover { color: var(--text); }

        /* Field error */
        .field-error {
            margin-top: 0.35rem;
            font-size: 0.76rem;
            color: var(--danger);
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        /* Meta row */
        .meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.3rem;
            margin-top: -0.2rem;
        }

        .checkbox-wrap {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: var(--muted);
            cursor: pointer;
            user-select: none;
        }

        .checkbox-wrap input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--blue-bright);
            cursor: pointer;
        }

        .forgot-link {
            font-size: 0.79rem;
            color: #5aa8e8;
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: var(--blue-bright); }

        /* Submit button */
        .auth-btn {
            width: 100%;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #1455a8, var(--blue-bright));
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.04em;
            padding: 0.85rem;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 24px rgba(59,158,255,0.32), 0 1px 0 rgba(255,255,255,0.12) inset;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 1.2rem;
        }

        .auth-btn::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transform: skewX(-20deg);
            animation: btn-shimmer 3.5s ease-in-out infinite 1s;
        }

        .auth-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(59,158,255,0.45), 0 1px 0 rgba(255,255,255,0.12) inset;
        }

        .auth-btn:active { transform: translateY(0); }

        /* Divider */
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 1.2rem;
            font-size: 0.72rem;
            color: var(--muted);
        }
        .auth-divider::before, .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* Register nudge */
        .auth-footer {
            text-align: center;
            font-size: 0.8rem;
            color: var(--muted);
        }

        .auth-footer a {
            color: #5aa8e8;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .auth-footer a:hover { color: var(--blue-bright); }

        /* Secure note */
        .secure-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            margin-top: 1rem;
            font-size: 0.72rem;
            color: rgba(106,135,170,0.6);
        }

        .secure-note-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 5px var(--success);
            animation: pulse 2.5s ease-in-out infinite;
        }

        /* ── KEYFRAMES ── */
        @keyframes card-enter {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes orb-drift {
            0%, 100% { transform: translate(0, 0); }
            33%  { transform: translate(30px, -40px); }
            66%  { transform: translate(-20px, 20px); }
        }

        @keyframes float-card {
            0%, 100% { transform: translateY(-50%) translateX(0); }
            50%       { transform: translateY(calc(-50% - 10px)) translateX(4px); }
        }

        @keyframes bar-grow {
            from { width: 0; }
            to   { width: 72%; }
        }

        @keyframes btn-shimmer {
            0%   { left: -100%; }
            40%, 100% { left: 160%; }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.35; }
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 860px) {
            .scene { grid-template-columns: 1fr; }
            .scene-left { display: none; }
            .scene-right { min-height: 100vh; padding: 2rem 1.2rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>
<div class="scene">

    <!-- ── LEFT PANEL ── -->
    <div class="scene-left">
        <div class="hex-bg"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <!-- Brand -->
        <div class="left-brand">
            <div class="brand-logo">M</div>
            <div>
                <div class="brand-name">MARS</div>
                <div class="brand-tagline">Secure Digital Banking</div>
            </div>
        </div>

        <!-- Hero -->
        <div class="left-hero">
            <div class="hero-eyebrow">
                <span class="hero-eyebrow-dot"></span>
                System Operational
            </div>
            <h1 class="hero-headline">
                Future-Ready<br>
                Banking Built<br>
                for <em>Modern</em> Life.
            </h1>
            <p class="hero-body">
                MARS delivers enterprise-grade security with instant transfers, intelligent spending insights, and full account control — all in one unified platform.
            </p>
            <div class="stat-row">
                <div class="stat-item">
                    <span class="stat-value">99.98%</span>
                    <span class="stat-label">Uptime</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-value">2.4s</span>
                    <span class="stat-label">Avg Transfer</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-value">1.2M+</span>
                    <span class="stat-label">Accounts</span>
                </div>
            </div>

            <!-- Floating decoration card -->
            <div class="float-card">
                <div class="float-card-label">Available Balance</div>
                <div class="float-card-amount">৳ 84,250.00</div>
                <div class="float-card-sub">↑ 12.4% this month</div>
                <div class="float-card-bar"><div class="float-card-bar-fill"></div></div>
            </div>
        </div>

        <!-- Security badges -->
        <div class="security-row">
            <div class="sec-badge">
                <span class="sec-badge-icon">🔒</span>
                256-bit SSL
            </div>
            <div class="sec-badge">
                <span class="sec-badge-icon">🛡</span>
                2FA Protected
            </div>
            <div class="sec-badge">
                <span class="sec-badge-icon">✓</span>
                ISO Certified
            </div>
        </div>
    </div>

    <!-- ── RIGHT PANEL ── -->
    <div class="scene-right">
        <div class="auth-card">

            <!-- Card header (mobile only brand) -->
            <div class="card-header">
                <div class="card-brand-name" style="font-family:'Syne',sans-serif;font-weight:800;font-size:0.85rem;letter-spacing:0.1em;color:var(--muted);margin-bottom:0.8rem;display:none" id="mobile-brand">MARS BANK</div>
                <h2 class="card-title">Welcome back</h2>
                <p class="card-sub">Sign in to your account to continue</p>
            </div>

            <!-- Switcher -->
            <div class="switcher" role="tablist">
                <span class="is-active" role="tab" aria-selected="true">Login</span>
                <a href="{{ route('register') }}" role="tab">Register</a>
            </div>

            <!-- Session status -->
            <x-auth-session-status class="status-message" :status="session('status')" />

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

                <div class="field">
                    <label for="account_number">Account Number</label>
                    <div class="input-shell">
                        <span class="field-icon">#</span>
                        <input id="account_number" type="text" name="account_number"
                            value="{{ old('account_number') }}"
                            placeholder="e.g. 1000XXXXXXX"
                            required autofocus autocomplete="off">
                    </div>
                    <x-input-error :messages="$errors->get('account_number')" class="field-error" />
                </div>

                <div class="field">
                    <label for="email">Email Address</label>
                    <div class="input-shell">
                        <span class="field-icon">@</span>
                        <input id="email" type="email" name="email"
                            value="{{ old('email') }}"
                            placeholder="you@example.com"
                            autocomplete="username">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="field-error" />
                </div>

                <div class="field">
                    <label for="phone_number">Phone Number</label>
                    <div class="input-shell">
                        <span class="field-icon">📞</span>
                        <input id="phone_number" type="text" name="phone_number"
                            value="{{ old('phone_number') }}"
                            placeholder="+880 XXXXXXXXXX"
                            autocomplete="tel">
                    </div>
                    <x-input-error :messages="$errors->get('phone_number')" class="field-error" />
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-shell">
                        <span class="field-icon">🔑</span>
                        <input id="password" type="password" name="password"
                            placeholder="••••••••••••"
                            required autocomplete="current-password">
                        <button type="button" class="pw-toggle" id="pw-toggle" aria-label="Toggle password visibility">👁</button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="field-error" />
                </div>

                <div class="meta-row">
                    <label for="remember_me" class="checkbox-wrap">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="auth-btn" id="login-btn">
                    Sign In to MARS
                </button>
            </form>

            <div class="auth-divider">or</div>
            <div class="auth-footer">
                Don't have an account?
                <a href="{{ route('register') }}">Create one free</a>
            </div>

            <div class="secure-note">
                <span class="secure-note-dot"></span>
                Secured with 256-bit TLS encryption
            </div>
        </div>
    </div>
</div>

<script>
    // Password toggle
    const pwToggle = document.getElementById('pw-toggle');
    const pwInput = document.getElementById('password');
    pwToggle?.addEventListener('click', () => {
        const isHidden = pwInput.type === 'password';
        pwInput.type = isHidden ? 'text' : 'password';
        pwToggle.textContent = isHidden ? '🙈' : '👁';
    });

    // Button loading state
    document.getElementById('login-form')?.addEventListener('submit', function() {
        const btn = document.getElementById('login-btn');
        btn.textContent = 'Signing in…';
        btn.style.opacity = '0.75';
        btn.disabled = true;
    });

    // Show mobile brand on small screens
    if (window.innerWidth <= 860) {
        document.getElementById('mobile-brand').style.display = 'block';
    }
</script>
</body>
</html>