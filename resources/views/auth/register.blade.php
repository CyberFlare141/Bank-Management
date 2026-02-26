<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MARS Bank | Register</title>
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
            --warning: #fbbf24;
        }

        html, body {
            min-height: 100%;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ── SCENE ── */
        .scene {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1.1fr;
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

        .hex-bg {
            position: absolute;
            inset: 0;
            opacity: 0.035;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='56' height='100'%3E%3Cpath d='M28 66L0 50V18L28 2l28 16v32z' fill='none' stroke='%234aa0f0' stroke-width='1'/%3E%3C/svg%3E");
            background-size: 56px 100px;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
        }
        .orb-1 { width: 380px; height: 380px; background: radial-gradient(circle, rgba(196,147,74,0.28), transparent 70%); top: -60px; right: -60px; animation: orb-drift 11s ease-in-out infinite; }
        .orb-2 { width: 300px; height: 300px; background: radial-gradient(circle, rgba(24,104,196,0.3), transparent 70%); bottom: 40px; left: -50px; animation: orb-drift 9s ease-in-out infinite reverse; }
        .orb-3 { width: 180px; height: 180px; background: radial-gradient(circle, rgba(61,214,140,0.15), transparent 70%); top: 45%; left: 35%; animation: orb-drift 7s ease-in-out infinite 3s; }

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
            background: linear-gradient(135deg, var(--gold), var(--gold-bright));
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            color: #fff;
            box-shadow: 0 0 24px rgba(196,147,74,0.45);
            flex-shrink: 0;
        }

        .brand-name { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.4rem; letter-spacing: 0.08em; color: #dceeff; }
        .brand-tagline { font-size: 0.75rem; color: var(--muted); letter-spacing: 0.06em; }

        /* Steps progress */
        .left-hero {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2.5rem 0;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(196,147,74,0.15);
            border: 1px solid rgba(240,185,106,0.25);
            border-radius: 999px;
            padding: 0.25rem 0.8rem;
            font-size: 0.7rem;
            color: var(--gold-bright);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 1.2rem;
            width: fit-content;
        }

        .hero-eyebrow-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--gold-bright);
            box-shadow: 0 0 7px var(--gold-bright);
            animation: pulse 2s ease-in-out infinite;
        }

        .hero-headline {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2rem, 3vw, 2.8rem);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -0.03em;
            color: #dceeff;
            margin-bottom: 1rem;
        }

        .hero-headline em {
            font-style: normal;
            background: linear-gradient(135deg, var(--gold-bright), var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-body {
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.7;
            max-width: 360px;
            margin-bottom: 2rem;
        }

        /* Steps list */
        .steps-list {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }

        .step {
            display: flex;
            align-items: flex-start;
            gap: 0.9rem;
            opacity: 0;
            animation: slide-in 0.5s cubic-bezier(0.22,1,0.36,1) forwards;
        }
        .step:nth-child(1) { animation-delay: 0.3s; }
        .step:nth-child(2) { animation-delay: 0.45s; }
        .step:nth-child(3) { animation-delay: 0.6s; }
        .step:nth-child(4) { animation-delay: 0.75s; }

        .step-num {
            width: 30px; height: 30px;
            flex-shrink: 0;
            border-radius: 50%;
            background: rgba(196,147,74,0.15);
            border: 1px solid rgba(240,185,106,0.3);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--gold-bright);
        }

        .step-body {}
        .step-title { font-size: 0.84rem; font-weight: 600; color: #c8daf0; margin-bottom: 0.15rem; }
        .step-desc { font-size: 0.75rem; color: var(--muted); line-height: 1.4; }

        /* Floating card */
        .float-card {
            position: absolute;
            right: -16px;
            bottom: 15%;
            width: 200px;
            background: rgba(10, 22, 42, 0.92);
            border: 1px solid rgba(196,147,74,0.22);
            border-radius: 16px;
            padding: 1rem;
            backdrop-filter: blur(12px);
            animation: float-card 4.5s ease-in-out infinite;
            z-index: 3;
        }

        .float-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem; }
        .float-card-label { font-size: 0.65rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; }
        .float-card-badge { font-size: 0.62rem; background: rgba(61,214,140,0.15); border: 1px solid rgba(61,214,140,0.3); color: var(--success); padding: 0.1rem 0.4rem; border-radius: 999px; font-weight: 700; }
        .float-card-title { font-family: 'Syne', sans-serif; font-size: 0.9rem; font-weight: 700; color: #dceeff; margin-bottom: 0.3rem; }
        .float-card-sub { font-size: 0.7rem; color: var(--muted); }
        .float-card-perks { display: flex; gap: 0.4rem; margin-top: 0.7rem; flex-wrap: wrap; }
        .perk { font-size: 0.62rem; background: rgba(24,104,196,0.15); border: 1px solid rgba(80,160,240,0.2); color: #7ec8f7; padding: 0.15rem 0.45rem; border-radius: 6px; }

        .security-row {
            position: relative;
            z-index: 2;
            display: flex;
            gap: 1.2rem;
        }

        .sec-badge { display: flex; align-items: center; gap: 0.4rem; font-size: 0.72rem; color: var(--muted); }
        .sec-badge-icon { width: 22px; height: 22px; border-radius: 6px; background: rgba(80,160,240,0.1); border: 1px solid rgba(80,160,240,0.15); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; }

        /* ── RIGHT PANEL ── */
        .scene-right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 3rem;
            position: relative;
            background:
                radial-gradient(ellipse at 40% 20%, rgba(196,147,74,0.06), transparent 55%),
                radial-gradient(ellipse at 70% 80%, rgba(24,104,196,0.07), transparent 50%),
                var(--bg);
        }

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

        /* Auth card */
        .auth-card {
            position: relative;
            z-index: 2;
            width: min(580px, 100%);
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 2rem 1.75rem 2.5rem;
            backdrop-filter: blur(16px);
            box-shadow: 0 32px 80px rgba(0,4,16,0.6), 0 0 0 1px rgba(196,147,74,0.06) inset;
            animation: card-enter 0.7s cubic-bezier(0.22,1,0.36,1) 0.1s both;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0; left: 10%; right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold-bright), var(--blue-bright), transparent);
            opacity: 0.45;
            border-radius: 999px;
        }

        .card-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: #dceeff;
            margin-bottom: 0.3rem;
        }

        .card-sub { font-size: 0.82rem; color: var(--muted); }

        /* Switcher */
        .switcher {
            display: flex;
            background: rgba(5, 10, 22, 0.7);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 3px;
            margin-bottom: 1.4rem;
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
            background: linear-gradient(135deg, #9a6f2a, var(--gold));
            color: #fff;
            box-shadow: 0 4px 14px rgba(196,147,74,0.35);
        }

        .switcher a { color: var(--muted); }
        .switcher a:hover { color: var(--text); }

        /* Two-column grid for fields */
        .fields-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 0.9rem;
        }

        .field-full { grid-column: 1 / -1; }

        /* Fields */
        .field { margin-bottom: 0.9rem; }

        .field label {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.35rem;
            transition: color 0.2s;
        }

        .field:focus-within label { color: var(--gold-bright); }

        .input-shell {
            display: flex;
            align-items: center;
            background: rgba(4, 9, 20, 0.75);
            border: 1px solid rgba(80,160,240,0.18);
            border-radius: 11px;
            overflow: hidden;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-shell:focus-within {
            border-color: rgba(240,185,106,0.5);
            box-shadow: 0 0 0 3px rgba(196,147,74,0.1), 0 2px 16px rgba(196,147,74,0.07);
        }

        .field-icon {
            width: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            color: var(--muted);
            flex-shrink: 0;
            border-right: 1px solid rgba(80,160,240,0.1);
            height: 100%;
            padding: 0.65rem 0;
            transition: color 0.2s;
        }

        .input-shell:focus-within .field-icon { color: var(--gold-bright); }

        .input-shell input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: var(--text);
            font-size: 0.88rem;
            font-family: 'DM Sans', sans-serif;
            padding: 0.65rem 0.75rem;
            min-width: 0;
        }

        .input-shell input::placeholder { color: rgba(106,135,170,0.45); }

        /* Password strength */
        .pw-strength-wrap { margin-top: 0.4rem; }
        .pw-strength-bar { height: 3px; border-radius: 3px; background: rgba(80,160,240,0.1); overflow: hidden; }
        .pw-strength-fill { height: 100%; border-radius: 3px; width: 0; transition: width 0.4s ease, background 0.4s ease; }
        .pw-strength-label { font-size: 0.68rem; color: var(--muted); margin-top: 0.25rem; }

        /* Password toggle */
        .pw-toggle {
            background: none; border: none; cursor: pointer;
            padding: 0 0.65rem; color: var(--muted); font-size: 0.82rem; transition: color 0.2s;
        }
        .pw-toggle:hover { color: var(--text); }

        /* Match indicator */
        .pw-match { font-size: 0.7rem; margin-top: 0.3rem; }
        .pw-match.ok { color: var(--success); }
        .pw-match.no { color: var(--danger); }

        /* Account number hint */
        .field-hint {
            font-size: 0.68rem;
            color: var(--muted);
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        /* Field error */
        .field-error {
            margin-top: 0.3rem;
            font-size: 0.74rem;
            color: var(--danger);
        }

        /* Terms */
        .terms-row {
            display: flex;
            align-items: flex-start;
            gap: 0.55rem;
            margin-bottom: 1.1rem;
            margin-top: 0.2rem;
        }

        .terms-row input[type="checkbox"] {
            width: 15px; height: 15px;
            accent-color: var(--gold);
            flex-shrink: 0;
            margin-top: 2px;
            cursor: pointer;
        }

        .terms-row label {
            font-size: 0.76rem;
            color: var(--muted);
            line-height: 1.5;
            cursor: pointer;
        }

        .terms-row a { color: #e8c07a; text-decoration: none; }
        .terms-row a:hover { color: var(--gold-bright); }

        /* Submit */
        .auth-btn {
            width: 100%;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #9a6f2a, var(--gold), var(--gold-bright));
            color: #1a0f00;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 0.95rem;
            letter-spacing: 0.03em;
            padding: 0.85rem;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 24px rgba(196,147,74,0.38), 0 1px 0 rgba(255,255,255,0.2) inset;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 1rem;
        }

        .auth-btn::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
            transform: skewX(-20deg);
            animation: btn-shimmer 3.5s ease-in-out infinite 1.2s;
        }

        .auth-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(196,147,74,0.52), 0 1px 0 rgba(255,255,255,0.2) inset;
        }

        .auth-btn:active { transform: translateY(0); }
        .auth-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .auth-divider {
            display: flex; align-items: center; gap: 0.8rem;
            margin-bottom: 1rem;
            font-size: 0.72rem; color: var(--muted);
        }
        .auth-divider::before, .auth-divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }

        .auth-footer { text-align: center; font-size: 0.8rem; color: var(--muted); }
        .auth-footer a { color: #5aa8e8; text-decoration: none; font-weight: 600; transition: color 0.2s; }
        .auth-footer a:hover { color: var(--blue-bright); }

        .secure-note {
            display: flex; align-items: center; justify-content: center;
            gap: 0.45rem; margin-top: 0.9rem;
            font-size: 0.7rem; color: rgba(106,135,170,0.55);
        }

        .secure-note-dot {
            width: 5px; height: 5px; border-radius: 50%;
            background: var(--success); box-shadow: 0 0 5px var(--success);
            animation: pulse 2.5s ease-in-out infinite;
        }

        /* ── KEYFRAMES ── */
        @keyframes card-enter {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes orb-drift {
            0%, 100% { transform: translate(0, 0); }
            33%  { transform: translate(25px, -35px); }
            66%  { transform: translate(-18px, 18px); }
        }

        @keyframes float-card {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-10px); }
        }

        @keyframes btn-shimmer {
            0%   { left: -100%; }
            40%, 100% { left: 160%; }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.35; }
        }

        @keyframes slide-in {
            from { opacity: 0; transform: translateX(-12px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .scene { grid-template-columns: 1fr; }
            .scene-left { display: none; }
            .scene-right { min-height: 100vh; padding: 2rem 1.2rem; align-items: flex-start; padding-top: 3rem; }
            .fields-grid { grid-template-columns: 1fr; }
            .field-full { grid-column: 1; }
        }

        @media (max-width: 440px) {
            .auth-card { padding: 1.5rem 1.2rem; }
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

        <div class="left-brand">
            <div class="brand-logo">M</div>
            <div>
                <div class="brand-name">MARS</div>
                <div class="brand-tagline">Open Your Premium Account</div>
            </div>
        </div>

        <div class="left-hero">
            <div class="hero-eyebrow">
                <span class="hero-eyebrow-dot"></span>
                Free to Join
            </div>
            <h1 class="hero-headline">
                Banking That<br>
                Works as Hard<br>
                as <em>You Do.</em>
            </h1>
            <p class="hero-body">
                Join over 1.2 million members who trust MARS for instant transfers, intelligent savings, and enterprise-grade security — all from one account.
            </p>

            <div class="steps-list">
                <div class="step">
                    <div class="step-num">1</div>
                    <div class="step-body">
                        <div class="step-title">Create your account</div>
                        <div class="step-desc">Fill in your details — takes under 2 minutes.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <div class="step-body">
                        <div class="step-title">Verify your identity</div>
                        <div class="step-desc">Secure OTP verification via email or SMS.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <div class="step-body">
                        <div class="step-title">Fund your account</div>
                        <div class="step-desc">Deposit and start transacting instantly.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">4</div>
                    <div class="step-body">
                        <div class="step-title">Enjoy full access</div>
                        <div class="step-desc">Loans, savings, cards — everything in one place.</div>
                    </div>
                </div>
            </div>

            <!-- Floating promo card -->
            <div class="float-card">
                <div class="float-card-top">
                    <span class="float-card-label">New Account</span>
                    <span class="float-card-badge">FREE</span>
                </div>
                <div class="float-card-title">MARS Essential</div>
                <div class="float-card-sub">Zero monthly fees, forever</div>
                <div class="float-card-perks">
                    <span class="perk">Instant Transfer</span>
                    <span class="perk">Smart Savings</span>
                    <span class="perk">24/7 Support</span>
                </div>
            </div>
        </div>

        <div class="security-row">
            <div class="sec-badge"><span class="sec-badge-icon">🔒</span> 256-bit SSL</div>
            <div class="sec-badge"><span class="sec-badge-icon">🛡</span> 2FA Ready</div>
            <div class="sec-badge"><span class="sec-badge-icon">✓</span> ISO Certified</div>
        </div>
    </div>

    <!-- ── RIGHT PANEL ── -->
    <div class="scene-right">
        <div class="auth-card">

            <div class="card-header">
                <h2 class="card-title">Create your account</h2>
                <p class="card-sub">Free forever · Takes less than 2 minutes</p>
            </div>

            <div class="switcher" role="tablist">
                <a href="{{ route('login') }}" role="tab">Login</a>
                <span class="is-active" role="tab" aria-selected="true">Register</span>
            </div>

            <form method="POST" action="{{ route('register') }}" id="register-form">
                @csrf

                <div class="fields-grid">
                    <!-- Full Name -->
                    <div class="field field-full">
                        <label for="name">Full Name</label>
                        <div class="input-shell">
                            <span class="field-icon">👤</span>
                            <input id="name" type="text" name="name"
                                value="{{ old('name') }}"
                                placeholder="John Doe"
                                required autofocus autocomplete="name">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="field-error" />
                    </div>

                    <!-- Email -->
                    <div class="field field-full">
                        <label for="email">Email Address</label>
                        <div class="input-shell">
                            <span class="field-icon">@</span>
                            <input id="email" type="email" name="email"
                                value="{{ old('email') }}"
                                placeholder="you@example.com"
                                required autocomplete="username">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="field-error" />
                    </div>

                    <!-- Phone -->
                    <div class="field">
                        <label for="phone_number">Phone Number</label>
                        <div class="input-shell">
                            <span class="field-icon">📞</span>
                            <input id="phone_number" type="text" name="phone_number"
                                value="{{ old('phone_number') }}"
                                placeholder="+880 XXXXXXXXX"
                                required autocomplete="tel">
                        </div>
                        <x-input-error :messages="$errors->get('phone_number')" class="field-error" />
                    </div>

                    <!-- Account Number -->
                    <div class="field">
                        <label for="account_number">Account Number</label>
                        <div class="input-shell">
                            <span class="field-icon">#</span>
                            <input id="account_number" type="text" name="account_number"
                                value="{{ old('account_number') }}"
                                placeholder="11 digits"
                                required inputmode="numeric" pattern="[0-9]{11}" maxlength="11">
                        </div>
                        <div class="field-hint">
                            <span id="acct-count" style="color:var(--gold-bright);font-weight:700">0</span>/11 digits
                        </div>
                        <x-input-error :messages="$errors->get('account_number')" class="field-error" />
                    </div>

                    <!-- Password -->
                    <div class="field">
                        <label for="password">Password</label>
                        <div class="input-shell">
                            <span class="field-icon">🔑</span>
                            <input id="password" type="password" name="password"
                                placeholder="Min 8 chars"
                                required autocomplete="new-password">
                            <button type="button" class="pw-toggle" id="pw-toggle-1">👁</button>
                        </div>
                        <div class="pw-strength-wrap">
                            <div class="pw-strength-bar"><div class="pw-strength-fill" id="pw-fill"></div></div>
                            <div class="pw-strength-label" id="pw-label"></div>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="field-error" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="field">
                        <label for="password_confirmation">Confirm Password</label>
                        <div class="input-shell">
                            <span class="field-icon">🔏</span>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                placeholder="Repeat password"
                                required autocomplete="new-password">
                            <button type="button" class="pw-toggle" id="pw-toggle-2">👁</button>
                        </div>
                        <div class="pw-match" id="pw-match"></div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="field-error" />
                    </div>
                </div>

                <!-- Terms -->
                <div class="terms-row">
                    <input type="checkbox" id="terms" required>
                    <label for="terms">
                        I agree to MARS Bank's <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. My data is protected under applicable banking regulations.
                    </label>
                </div>

                <button type="submit" class="auth-btn" id="register-btn">
                    Create My Account →
                </button>
            </form>

            <div class="auth-divider">already have an account?</div>
            <div class="auth-footer">
                <a href="{{ route('login') }}">Sign in to MARS →</a>
            </div>

            <div class="secure-note">
                <span class="secure-note-dot"></span>
                Your data is encrypted with 256-bit TLS
            </div>
        </div>
    </div>
</div>

<script>
    // Account number digit counter
    const acctInput = document.getElementById('account_number');
    const acctCount = document.getElementById('acct-count');
    acctInput?.addEventListener('input', () => {
        const len = acctInput.value.replace(/\D/g,'').length;
        acctCount.textContent = len;
        acctCount.style.color = len === 11 ? 'var(--success)' : 'var(--gold-bright)';
        acctInput.value = acctInput.value.replace(/\D/g,'');
    });

    // Password strength
    const pwInput = document.getElementById('password');
    const pwFill = document.getElementById('pw-fill');
    const pwLabel = document.getElementById('pw-label');
    const pwConfirm = document.getElementById('password_confirmation');
    const pwMatch = document.getElementById('pw-match');

    const getStrength = pw => {
        let score = 0;
        if (pw.length >= 8) score++;
        if (pw.length >= 12) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/[0-9]/.test(pw)) score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;
        return score;
    };

    const strengthMeta = [
        { label: '', color: 'transparent', width: '0%' },
        { label: 'Too weak', color: 'var(--danger)', width: '20%' },
        { label: 'Weak', color: '#fb923c', width: '40%' },
        { label: 'Fair', color: 'var(--warning)', width: '60%' },
        { label: 'Strong', color: '#a3e635', width: '80%' },
        { label: 'Very strong ✓', color: 'var(--success)', width: '100%' },
    ];

    pwInput?.addEventListener('input', () => {
        const s = Math.min(getStrength(pwInput.value), 5);
        const meta = strengthMeta[pwInput.value ? Math.max(s, 1) : 0];
        pwFill.style.width = pwInput.value ? meta.width : '0%';
        pwFill.style.background = meta.color;
        pwLabel.textContent = pwInput.value ? meta.label : '';
        pwLabel.style.color = meta.color;
        checkMatch();
    });

    const checkMatch = () => {
        if (!pwConfirm.value) { pwMatch.textContent = ''; return; }
        if (pwInput.value === pwConfirm.value) {
            pwMatch.textContent = '✓ Passwords match';
            pwMatch.className = 'pw-match ok';
        } else {
            pwMatch.textContent = '✗ Passwords do not match';
            pwMatch.className = 'pw-match no';
        }
    };

    pwConfirm?.addEventListener('input', checkMatch);

    // Password toggles
    const makeToggle = (btnId, inputEl) => {
        document.getElementById(btnId)?.addEventListener('click', () => {
            const hidden = inputEl.type === 'password';
            inputEl.type = hidden ? 'text' : 'password';
            document.getElementById(btnId).textContent = hidden ? '🙈' : '👁';
        });
    };
    makeToggle('pw-toggle-1', pwInput);
    makeToggle('pw-toggle-2', pwConfirm);

    // Submit loading state
    document.getElementById('register-form')?.addEventListener('submit', function(e) {
        const btn = document.getElementById('register-btn');
        btn.textContent = 'Creating account…';
        btn.disabled = true;
    });
</script>
</body>
</html>