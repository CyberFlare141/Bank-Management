<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MARS Bank | Reset Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #05090f;
            --panel: rgba(8, 16, 30, 0.92);
            --border: rgba(80, 160, 240, 0.13);
            --text: #e8f2ff;
            --muted: #6a87aa;
            --blue: #1868c4;
            --blue-b: #3b9eff;
            --gold: #c4934a;
            --gold-b: #f0b96a;
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

        /* ── BACKGROUND ── */
        .bg-layer {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }

        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(80,160,240,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(80,160,240,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse at 50% 50%, black 30%, transparent 75%);
        }

        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            animation: orb-drift 12s ease-in-out infinite;
        }

        .orb-1 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(24,104,196,0.2), transparent 70%); top: -150px; left: -100px; animation-delay: 0s; }
        .orb-2 { width: 350px; height: 350px; background: radial-gradient(circle, rgba(196,147,74,0.15), transparent 70%); bottom: -80px; right: -80px; animation-delay: -5s; }

        /* ── LAYOUT ── */
        .page {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.2rem;
        }

        /* ── BRAND TOP ── */
        .brand-top {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 2.5rem;
            opacity: 0;
            animation: fade-up 0.6s cubic-bezier(0.22,1,0.36,1) 0.1s forwards;
        }

        .brand-mark {
            width: 38px; height: 38px;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--blue), var(--blue-b));
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1rem;
            color: #fff;
            box-shadow: 0 0 20px rgba(59,158,255,0.4);
            text-decoration: none;
        }

        .brand-name {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            letter-spacing: 0.1em;
            color: #dceeff;
            text-decoration: none;
        }

        /* ── CARD ── */
        .card {
            width: min(420px, 100%);
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 2.5rem 2.2rem;
            backdrop-filter: blur(16px);
            box-shadow: 0 40px 100px rgba(0,0,0,0.55), 0 0 0 1px rgba(80,160,240,0.05) inset;
            position: relative;
            overflow: hidden;
            opacity: 0;
            animation: fade-up 0.7s cubic-bezier(0.22,1,0.36,1) 0.2s forwards;
        }

        /* Top shimmer line */
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 10%; right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--blue-b), var(--gold-b), transparent);
            opacity: 0.45;
            border-radius: 999px;
        }

        /* Subtle inner glow */
        .card::after {
            content: '';
            position: absolute;
            top: -60px; left: 50%;
            transform: translateX(-50%);
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(59,158,255,0.06), transparent 70%);
            pointer-events: none;
        }

        /* ── ICON ── */
        .card-icon {
            width: 60px; height: 60px;
            border-radius: 18px;
            background: rgba(24,104,196,0.15);
            border: 1px solid rgba(80,160,240,0.25);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 24px rgba(59,158,255,0.15);
            animation: icon-pulse 3s ease-in-out infinite;
        }

        /* ── HEADER ── */
        .card-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.45rem;
            font-weight: 800;
            color: #dceeff;
            text-align: center;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }

        .card-desc {
            font-size: 0.84rem;
            color: var(--muted);
            text-align: center;
            line-height: 1.6;
            margin-bottom: 1.8rem;
            padding: 0 0.5rem;
        }

        /* ── STATUS ── */
        .status-msg {
            background: rgba(61,214,140,0.08);
            border: 1px solid rgba(61,214,140,0.25);
            color: var(--success);
            font-size: 0.82rem;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            line-height: 1.5;
        }

        .status-msg::before {
            content: '✓';
            width: 16px; height: 16px;
            border-radius: 50%;
            background: rgba(61,214,140,0.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.65rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ── FIELD ── */
        .field { margin-bottom: 1.2rem; }

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
            transition: border-color 0.25s, box-shadow 0.25s;
        }

        .input-shell:focus-within {
            border-color: rgba(80,160,240,0.55);
            box-shadow: 0 0 0 3px rgba(59,158,255,0.1), 0 2px 16px rgba(59,158,255,0.08);
        }

        .input-icon {
            width: 44px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem;
            color: var(--muted);
            flex-shrink: 0;
            border-right: 1px solid rgba(80,160,240,0.1);
            padding: 0.75rem 0;
            transition: color 0.2s;
        }

        .input-shell:focus-within .input-icon { color: #7ec8f7; }

        .input-shell input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: var(--text);
            font-size: 0.9rem;
            font-family: 'DM Sans', sans-serif;
            padding: 0.75rem 0.9rem;
        }

        .input-shell input::placeholder { color: rgba(106,135,170,0.45); }

        .input-shell input:-webkit-autofill,
        .input-shell input:-webkit-autofill:hover,
        .input-shell input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px rgba(4,9,20,0.75) inset !important;
            -webkit-text-fill-color: #e8f2ff !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* ── ERROR ── */
        .field-error {
            margin-top: 0.35rem;
            font-size: 0.76rem;
            color: var(--danger);
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        /* ── SUBMIT ── */
        .submit-btn {
            width: 100%;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #1455a8, var(--blue-b));
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.03em;
            padding: 0.85rem;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 24px rgba(59,158,255,0.32);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 0.4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transform: skewX(-20deg);
            animation: btn-shimmer 3.5s ease-in-out infinite 1s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(59,158,255,0.45);
        }

        .submit-btn:active { transform: translateY(0); }
        .submit-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        /* Spinner */
        .spinner {
            display: none;
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        /* ── DIVIDER ── */
        .divider {
            display: flex; align-items: center; gap: 0.8rem;
            margin: 1.4rem 0 1rem;
            font-size: 0.72rem; color: var(--muted);
        }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }

        /* ── BACK LINK ── */
        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            font-size: 0.83rem;
            color: var(--muted);
            text-decoration: none;
            transition: color 0.2s;
            font-weight: 500;
        }

        .back-link:hover { color: var(--text); }

        .back-arrow {
            width: 24px; height: 24px;
            border-radius: 6px;
            background: rgba(80,160,240,0.08);
            border: 1px solid rgba(80,160,240,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem;
            transition: background 0.2s, border-color 0.2s;
        }

        .back-link:hover .back-arrow {
            background: rgba(80,160,240,0.15);
            border-color: rgba(80,160,240,0.35);
        }

        /* ── SECURE NOTE ── */
        .secure-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            margin-top: 1.8rem;
            font-size: 0.7rem;
            color: rgba(106,135,170,0.5);
            opacity: 0;
            animation: fade-up 0.6s ease 0.6s forwards;
        }

        .secure-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 5px var(--success);
            animation: pulse 2.5s ease-in-out infinite;
        }

        /* ── KEYFRAMES ── */
        @keyframes fade-up {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes orb-drift {
            0%, 100% { transform: translate(0,0); }
            33% { transform: translate(25px,-35px); }
            66% { transform: translate(-20px, 18px); }
        }

        @keyframes btn-shimmer {
            0%   { left: -100%; }
            40%, 100% { left: 160%; }
        }

        @keyframes icon-pulse {
            0%, 100% { box-shadow: 0 8px 24px rgba(59,158,255,0.15); }
            50% { box-shadow: 0 8px 32px rgba(59,158,255,0.35); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="bg-grid"></div>

<div class="page">

    <!-- Brand -->
    <a href="{{ route('home') }}" class="brand-top" aria-label="MARS Home">
        <span class="brand-mark">M</span>
        <span class="brand-name">MARS</span>
    </a>

    <!-- Card -->
    <div class="card">

        <div class="card-icon">🔑</div>

        <h1 class="card-title">Forgot Password?</h1>
        <p class="card-desc">No problem. Enter your email and we'll send you a secure reset link instantly.</p>

        <!-- Session status -->
        @if (session('status'))
            <div class="status-msg">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" id="reset-form">
            @csrf

            <div class="field">
                <label for="email">Email Address</label>
                <div class="input-shell">
                    <span class="input-icon">@</span>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        required
                        autofocus
                        autocomplete="email"
                    >
                </div>
                <x-input-error :messages="$errors->get('email')" class="field-error" />
            </div>

            <button type="submit" class="submit-btn" id="submit-btn">
                <div class="spinner" id="spinner"></div>
                <span id="btn-text">Send Reset Link →</span>
            </button>
        </form>

        <div class="divider">or go back</div>

        <a href="{{ route('login') }}" class="back-link">
            <span class="back-arrow">←</span>
            Back to Login
        </a>
    </div>

    <div class="secure-note">
        <span class="secure-dot"></span>
        Reset links expire in 60 minutes · 256-bit TLS secured
    </div>

</div>

<script>
    document.getElementById('reset-form').addEventListener('submit', function () {
        const btn = document.getElementById('submit-btn');
        const spinner = document.getElementById('spinner');
        const text = document.getElementById('btn-text');
        btn.disabled = true;
        spinner.style.display = 'block';
        text.textContent = 'Sending…';
    });
</script>
</body>
</html>