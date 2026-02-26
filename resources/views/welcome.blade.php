<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MARS Bank | Digital Banking</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #05090f;
            --bg2: #080f1d;
            --panel: rgba(8, 16, 30, 0.85);
            --border: rgba(80, 160, 240, 0.12);
            --border-h: rgba(80, 160, 240, 0.35);
            --text: #e8f2ff;
            --muted: #6a87aa;
            --blue: #1868c4;
            --blue-b: #3b9eff;
            --gold: #c4934a;
            --gold-b: #f0b96a;
            --success: #3dd68c;
            --danger: #f87171;
            --r: 18px;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* ── CANVAS BG ── */
        #hero-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            opacity: 0.55;
        }

        /* ── GRID OVERLAY ── */
        .page-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(80,160,240,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(80,160,240,0.04) 1px, transparent 1px);
            background-size: 50px 50px;
            mask-image: radial-gradient(ellipse at 50% 40%, black 20%, transparent 75%);
        }

        .site { position: relative; z-index: 1; }

        /* ── TOPBAR ── */
        .topbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 4vw;
            background: rgba(5, 9, 15, 0.75);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            transition: background 0.3s;
        }

        .topbar.scrolled {
            background: rgba(5, 9, 15, 0.95);
            border-bottom-color: rgba(80,160,240,0.2);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
        }

        .brand-mark {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--blue), var(--blue-b));
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1rem;
            color: #fff;
            box-shadow: 0 0 18px rgba(59,158,255,0.4);
        }

        .brand-text {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.15rem;
            letter-spacing: 0.1em;
            color: #dceeff;
        }

        .main-nav {
            display: flex;
            gap: 0.25rem;
        }

        .main-nav a {
            color: var(--muted);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 0.45rem 0.85rem;
            border-radius: 8px;
            transition: color 0.2s, background 0.2s;
        }

        .main-nav a:hover {
            color: var(--text);
            background: rgba(80,160,240,0.08);
        }

        .auth-nav { display: flex; align-items: center; gap: 0.6rem; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            text-decoration: none;
            border: none;
            border-radius: 10px;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.82rem;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: all 0.22s;
            letter-spacing: 0.02em;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--blue), var(--blue-b));
            color: #fff;
            box-shadow: 0 4px 18px rgba(59,158,255,0.28);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 26px rgba(59,158,255,0.42);
        }

        .btn-secondary {
            background: rgba(14, 26, 48, 0.8);
            border: 1px solid var(--border-h);
            color: var(--text);
        }

        .btn-secondary:hover {
            background: rgba(80,160,240,0.1);
            border-color: rgba(80,160,240,0.5);
        }

        .btn-gold {
            background: linear-gradient(135deg, #9a6f2a, var(--gold-b));
            color: #1a0f00;
            font-weight: 800;
            box-shadow: 0 4px 18px rgba(196,147,74,0.35);
        }

        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(196,147,74,0.5);
        }

        .btn-lg {
            padding: 0.8rem 1.8rem;
            font-size: 0.95rem;
            border-radius: 12px;
        }

        /* Profile dropdown */
        .profile-menu { position: relative; list-style: none; }
        .profile-menu summary { cursor: pointer; list-style: none; }
        .profile-menu summary::-webkit-details-marker { display: none; }

        .profile-dropdown {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            min-width: 160px;
            background: rgba(9,18,36,0.98);
            border: 1px solid var(--border-h);
            border-radius: 12px;
            padding: 0.4rem;
            backdrop-filter: blur(12px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.5);
        }

        .dropdown-link {
            display: block;
            padding: 0.55rem 0.75rem;
            color: var(--muted);
            text-decoration: none;
            font-size: 0.84rem;
            border-radius: 8px;
            transition: all 0.18s;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-family: inherit;
        }

        .dropdown-link:hover { background: rgba(80,160,240,0.1); color: var(--text); }
        .logout-link { color: var(--danger); }
        .logout-link:hover { background: rgba(248,113,113,0.08); color: var(--danger); }

        /* ── HERO ── */
        .hero-section {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 8rem 4vw 4rem;
            position: relative;
            overflow: hidden;
        }

        /* Orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            animation: orb-drift 12s ease-in-out infinite;
        }

        .orb-a { width: 600px; height: 600px; background: radial-gradient(circle, rgba(24,104,196,0.22), transparent 70%); top: -150px; left: -100px; animation-delay: 0s; }
        .orb-b { width: 450px; height: 450px; background: radial-gradient(circle, rgba(196,147,74,0.18), transparent 70%); top: 100px; right: -80px; animation-delay: -4s; }
        .orb-c { width: 300px; height: 300px; background: radial-gradient(circle, rgba(61,214,140,0.12), transparent 70%); bottom: 50px; left: 30%; animation-delay: -7s; }

        .hero-inner {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        /* Hero left */
        .hero-left { position: relative; z-index: 2; }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(24,104,196,0.12);
            border: 1px solid rgba(80,160,240,0.22);
            border-radius: 999px;
            padding: 0.3rem 0.9rem;
            font-size: 0.72rem;
            color: #7ec8f7;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 1.4rem;
            opacity: 0;
            animation: fade-up 0.7s cubic-bezier(0.22,1,0.36,1) 0.2s forwards;
        }

        .eyebrow-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 7px var(--success);
            animation: pulse-dot 2s ease-in-out infinite;
        }

        .hero-h1 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2.6rem, 5vw, 4.2rem);
            font-weight: 800;
            line-height: 1.0;
            letter-spacing: -0.03em;
            color: #dceeff;
            margin-bottom: 1.4rem;
            opacity: 0;
            animation: fade-up 0.7s cubic-bezier(0.22,1,0.36,1) 0.35s forwards;
        }

        .hero-h1 .gold-word {
            background: linear-gradient(135deg, var(--gold-b), var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-h1 .blue-word {
            background: linear-gradient(135deg, #7ec8f7, var(--blue-b));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-sub {
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.7;
            max-width: 480px;
            margin-bottom: 2.2rem;
            opacity: 0;
            animation: fade-up 0.7s cubic-bezier(0.22,1,0.36,1) 0.5s forwards;
        }

        .hero-actions {
            display: flex;
            gap: 0.9rem;
            flex-wrap: wrap;
            opacity: 0;
            animation: fade-up 0.7s cubic-bezier(0.22,1,0.36,1) 0.65s forwards;
        }

        .inline-action { display: inline; }

        /* Trust badges */
        .trust-row {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-top: 2.5rem;
            opacity: 0;
            animation: fade-up 0.7s cubic-bezier(0.22,1,0.36,1) 0.8s forwards;
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.76rem;
            color: var(--muted);
        }

        .trust-icon {
            width: 24px; height: 24px;
            border-radius: 6px;
            background: rgba(80,160,240,0.1);
            border: 1px solid rgba(80,160,240,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem;
        }

        /* ── HERO RIGHT – dashboard mockup ── */
        .hero-right {
            position: relative;
            z-index: 2;
            opacity: 0;
            animation: fade-left 0.8s cubic-bezier(0.22,1,0.36,1) 0.5s forwards;
        }

        .dashboard-mock {
            background: rgba(8,16,30,0.9);
            border: 1px solid var(--border-h);
            border-radius: 24px;
            padding: 1.5rem;
            backdrop-filter: blur(16px);
            box-shadow: 0 40px 100px rgba(0,0,0,0.6), 0 0 0 1px rgba(80,160,240,0.06) inset;
            position: relative;
            overflow: hidden;
        }

        .dashboard-mock::before {
            content: '';
            position: absolute;
            top: 0; left: 15%; right: 15%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--blue-b), transparent);
            opacity: 0.5;
        }

        .mock-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.2rem;
        }

        .mock-title {
            font-family: 'Syne', sans-serif;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--muted);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .mock-dots { display: flex; gap: 0.3rem; }
        .mock-dot { width: 8px; height: 8px; border-radius: 50%; }
        .dot-r { background: #f87171; }
        .dot-y { background: #fbbf24; }
        .dot-g { background: #4ade80; }

        .mock-balance {
            margin-bottom: 1.5rem;
        }

        .mock-bal-label { font-size: 0.7rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.3rem; }
        .mock-bal-amount {
            font-family: 'Syne', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: #dceeff;
            line-height: 1;
        }

        .mock-bal-change {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.75rem;
            color: var(--success);
            margin-top: 0.3rem;
        }

        /* Mini chart */
        .mock-chart {
            height: 60px;
            position: relative;
            margin-bottom: 1.2rem;
            overflow: hidden;
        }

        .mock-chart svg { width: 100%; height: 100%; }

        .chart-path {
            stroke: var(--blue-b);
            stroke-width: 2;
            fill: none;
            stroke-dasharray: 600;
            stroke-dashoffset: 600;
            animation: draw-line 2s ease-out 1.2s forwards;
        }

        .chart-fill {
            fill: url(#chart-grad);
            opacity: 0;
            animation: fade-in-fill 0.5s ease-out 3s forwards;
        }

        /* Transactions */
        .mock-tx-label { font-size: 0.68rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.7rem; }

        .mock-tx { display: flex; flex-direction: column; gap: 0.5rem; }

        .tx-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0.6rem;
            border-radius: 10px;
            background: rgba(5,10,22,0.6);
            border: 1px solid rgba(80,160,240,0.07);
            opacity: 0;
            animation: fade-up 0.4s ease forwards;
        }

        .tx-row:nth-child(1) { animation-delay: 1.5s; }
        .tx-row:nth-child(2) { animation-delay: 1.7s; }
        .tx-row:nth-child(3) { animation-delay: 1.9s; }

        .tx-left { display: flex; align-items: center; gap: 0.6rem; }

        .tx-icon {
            width: 28px; height: 28px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .tx-name { font-size: 0.78rem; font-weight: 600; color: #c5d8f0; }
        .tx-date { font-size: 0.65rem; color: var(--muted); }

        .tx-amount { font-family: 'Syne', sans-serif; font-size: 0.82rem; font-weight: 700; }
        .tx-pos { color: var(--success); }
        .tx-neg { color: var(--danger); }

        /* Floating stat cards */
        .float-stat {
            position: absolute;
            background: rgba(8,18,36,0.95);
            border: 1px solid var(--border-h);
            border-radius: 14px;
            padding: 0.8rem 1rem;
            backdrop-filter: blur(12px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        .float-stat-1 {
            top: -20px; right: -20px;
            animation: float-a 3.5s ease-in-out infinite;
        }

        .float-stat-2 {
            bottom: -15px; left: -25px;
            animation: float-b 4s ease-in-out infinite;
        }

        .fs-label { font-size: 0.62rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.3rem; }
        .fs-value { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: #dceeff; }
        .fs-sub { font-size: 0.65rem; margin-top: 0.15rem; }

        /* ── STATS TICKER ── */
        .ticker-section {
            padding: 0;
            overflow: hidden;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            background: rgba(8,16,30,0.6);
        }

        .ticker-track {
            display: flex;
            animation: ticker 25s linear infinite;
            width: max-content;
        }

        .ticker-track:hover { animation-play-state: paused; }

        .ticker-item {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.9rem 2.5rem;
            border-right: 1px solid var(--border);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .ticker-label { font-size: 0.72rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.07em; }
        .ticker-value { font-family: 'Syne', sans-serif; font-size: 0.88rem; font-weight: 700; color: #dceeff; }
        .ticker-badge { font-size: 0.65rem; padding: 0.12rem 0.45rem; border-radius: 999px; font-weight: 700; }
        .badge-up { background: rgba(61,214,140,0.15); color: var(--success); border: 1px solid rgba(61,214,140,0.25); }
        .badge-live { background: rgba(59,158,255,0.15); color: var(--blue-b); border: 1px solid rgba(59,158,255,0.25); animation: pulse-badge 2s ease-in-out infinite; }

        /* ── PRODUCTS ── */
        .section { padding: 6rem 4vw; }

        .section-inner { max-width: 1200px; margin: 0 auto; }

        .section-head {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .section-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.7rem;
            color: var(--gold-b);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-weight: 600;
            margin-bottom: 0.8rem;
        }

        .section-head h2 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(1.8rem, 3vw, 2.8rem);
            font-weight: 800;
            color: #dceeff;
            letter-spacing: -0.02em;
            margin-bottom: 0.7rem;
        }

        .section-head p { color: var(--muted); font-size: 1rem; max-width: 500px; margin: 0 auto; line-height: 1.6; }

        /* Product cards */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.2rem;
        }

        .product-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 1.8rem;
            position: relative;
            overflow: hidden;
            transition: border-color 0.3s, transform 0.3s, box-shadow 0.3s;
            cursor: default;
        }

        .product-card:hover {
            border-color: var(--border-h);
            transform: translateY(-6px);
            box-shadow: 0 24px 60px rgba(0,0,0,0.4);
        }

        .product-card::after {
            content: '';
            position: absolute;
            top: -200%; left: -50%;
            width: 40%; height: 400%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.04), transparent);
            transform: rotate(15deg);
            animation: shimmer 3.5s ease-in-out infinite;
        }

        .product-card.featured {
            border-color: rgba(196,147,74,0.3);
            background: linear-gradient(135deg, rgba(8,16,30,0.9), rgba(30,20,8,0.5));
        }

        .product-card.featured:hover {
            border-color: rgba(240,185,106,0.5);
            box-shadow: 0 24px 60px rgba(196,147,74,0.15);
        }

        .product-card.featured::before {
            content: 'FEATURED';
            position: absolute;
            top: 1rem; right: 1rem;
            font-size: 0.6rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            color: var(--gold-b);
            background: rgba(196,147,74,0.15);
            border: 1px solid rgba(240,185,106,0.25);
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
        }

        .product-icon {
            width: 48px; height: 48px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 1.2rem;
        }

        .icon-blue { background: rgba(24,104,196,0.2); border: 1px solid rgba(80,160,240,0.2); }
        .icon-gold { background: rgba(196,147,74,0.2); border: 1px solid rgba(240,185,106,0.2); }
        .icon-green { background: rgba(61,214,140,0.15); border: 1px solid rgba(61,214,140,0.2); }

        .product-card h3 {
            font-family: 'Syne', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: #dceeff;
            margin-bottom: 0.6rem;
        }

        .product-card p { color: var(--muted); font-size: 0.86rem; line-height: 1.6; margin-bottom: 1.4rem; }

        .product-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--blue-b);
            text-decoration: none;
            transition: gap 0.2s, color 0.2s;
        }

        .product-card.featured .product-link { color: var(--gold-b); }
        .product-link:hover { gap: 0.6rem; }
        .product-link-arrow { transition: transform 0.2s; }
        .product-link:hover .product-link-arrow { transform: translateX(3px); }

        /* ── METRICS BAND ── */
        .metrics-section {
            padding: 5rem 4vw;
            background: linear-gradient(135deg, rgba(8,16,30,0.8), rgba(5,10,20,0.9));
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .metrics-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: var(--border);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
        }

        .metric-card {
            background: rgba(8,16,30,0.95);
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: background 0.3s;
        }

        .metric-card:hover { background: rgba(14,28,52,0.95); }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0; left: 20%; right: 20%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--blue-b), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .metric-card:hover::before { opacity: 0.5; }

        .metric-icon { font-size: 1.5rem; margin-bottom: 0.8rem; }

        .metric-num {
            font-family: 'Syne', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: #dceeff;
            line-height: 1;
            margin-bottom: 0.4rem;
        }

        .metric-num .metric-accent { color: var(--blue-b); }
        .metric-card:nth-child(3) .metric-num .metric-accent { color: var(--gold-b); }

        .metric-label { font-size: 0.78rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; }

        /* ── WHY MARS ── */
        .why-section { padding: 6rem 4vw; }

        .why-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .why-left {}

        .why-features { display: flex; flex-direction: column; gap: 1rem; margin-top: 2rem; }

        .feature-row {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.1rem;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: rgba(8,16,30,0.5);
            transition: border-color 0.3s, background 0.3s, transform 0.3s;
            cursor: default;
        }

        .feature-row:hover {
            border-color: var(--border-h);
            background: rgba(14,28,52,0.6);
            transform: translateX(6px);
        }

        .feature-icon-wrap {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .feature-title { font-size: 0.9rem; font-weight: 600; color: #c5d8f0; margin-bottom: 0.25rem; }
        .feature-desc { font-size: 0.8rem; color: var(--muted); line-height: 1.5; }

        /* Why right – Neo App card */
        .neo-card {
            background: linear-gradient(135deg, rgba(8,16,30,0.95), rgba(15,30,60,0.8));
            border: 1px solid rgba(59,158,255,0.25);
            border-radius: 24px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 32px 80px rgba(0,0,0,0.4);
        }

        .neo-card::before {
            content: '';
            position: absolute;
            top: 0; left: 10%; right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--blue-b), transparent);
            opacity: 0.5;
        }

        .neo-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(61,214,140,0.12);
            border: 1px solid rgba(61,214,140,0.25);
            color: var(--success);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            margin-bottom: 1rem;
        }

        .neo-card h3 {
            font-family: 'Syne', sans-serif;
            font-size: 1.9rem;
            font-weight: 800;
            color: #dceeff;
            letter-spacing: -0.02em;
            margin-bottom: 0.8rem;
            line-height: 1.1;
        }

        .neo-card p { color: var(--muted); font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.8rem; }

        .neo-features { display: flex; flex-direction: column; gap: 0.6rem; margin-bottom: 1.8rem; }

        .neo-feat {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.84rem;
            color: #a0bcd8;
        }

        .neo-feat::before {
            content: '✓';
            width: 18px; height: 18px;
            border-radius: 50%;
            background: rgba(61,214,140,0.15);
            border: 1px solid rgba(61,214,140,0.3);
            color: var(--success);
            font-size: 0.65rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        /* Phone mockup in neo card */
        .phone-mockup {
            position: absolute;
            right: -10px; bottom: -10px;
            width: 120px;
            opacity: 0.15;
            font-size: 8rem;
            line-height: 1;
            filter: blur(1px);
        }

        /* ── SECURITY SECTION ── */
        .security-section {
            padding: 5rem 4vw;
            background: radial-gradient(ellipse at 50% 0%, rgba(24,104,196,0.08), transparent 60%),
                        linear-gradient(180deg, var(--bg), var(--bg2));
        }

        .security-inner { max-width: 1200px; margin: 0 auto; }

        .security-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 3rem;
        }

        .sec-card {
            background: rgba(8,16,30,0.7);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 1.5rem;
            transition: border-color 0.3s, transform 0.3s;
        }

        .sec-card:hover {
            border-color: var(--border-h);
            transform: translateY(-4px);
        }

        .sec-card-icon {
            font-size: 1.6rem;
            margin-bottom: 0.9rem;
        }

        .sec-card h4 {
            font-family: 'Syne', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            color: #c5d8f0;
            margin-bottom: 0.5rem;
        }

        .sec-card p { font-size: 0.82rem; color: var(--muted); line-height: 1.5; }

        /* ── CTA SECTION ── */
        .cta-section {
            padding: 6rem 4vw;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-bg {
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 50% 50%, rgba(24,104,196,0.12), transparent 65%);
            pointer-events: none;
        }

        .cta-inner {
            position: relative;
            z-index: 1;
            max-width: 640px;
            margin: 0 auto;
        }

        .cta-inner h2 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            color: #dceeff;
            letter-spacing: -0.025em;
            margin-bottom: 1rem;
            line-height: 1.1;
        }

        .cta-inner h2 span {
            background: linear-gradient(135deg, var(--gold-b), var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .cta-inner p { color: var(--muted); font-size: 1rem; margin-bottom: 2rem; line-height: 1.6; }

        .cta-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }

        .cta-note { margin-top: 1rem; font-size: 0.76rem; color: rgba(106,135,170,0.6); }

        /* ── FOOTER ── */
        .footer {
            padding: 3rem 4vw 2rem;
            border-top: 1px solid var(--border);
            background: rgba(5,9,15,0.9);
        }

        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2.5rem;
        }

        .footer-brand-text {
            font-family: 'Syne', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: #dceeff;
            margin-bottom: 0.5rem;
        }

        .footer-tagline { font-size: 0.82rem; color: var(--muted); line-height: 1.6; max-width: 220px; }

        .footer-col h5 {
            font-family: 'Syne', sans-serif;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 1rem;
        }

        .footer-col a {
            display: block;
            font-size: 0.84rem;
            color: rgba(106,135,170,0.8);
            text-decoration: none;
            margin-bottom: 0.55rem;
            transition: color 0.2s;
        }

        .footer-col a:hover { color: var(--text); }

        .footer-bottom {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
            font-size: 0.76rem;
            color: rgba(106,135,170,0.5);
        }

        .footer-secure {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .secure-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 5px var(--success);
            animation: pulse-dot 2.5s ease-in-out infinite;
        }

        /* ── KEYFRAMES ── */
        @keyframes fade-up {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes fade-left {
            from { opacity: 0; transform: translateX(30px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        @keyframes orb-drift {
            0%, 100% { transform: translate(0,0); }
            33% { transform: translate(30px, -40px); }
            66% { transform: translate(-20px, 20px); }
        }

        @keyframes float-a {
            0%, 100% { transform: translateY(0) rotate(-1deg); }
            50% { transform: translateY(-10px) rotate(1deg); }
        }

        @keyframes float-b {
            0%, 100% { transform: translateY(0) rotate(1deg); }
            50% { transform: translateY(-8px) rotate(-1deg); }
        }

        @keyframes shimmer {
            0% { left: -50%; }
            100% { left: 130%; }
        }

        @keyframes ticker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        @keyframes draw-line {
            to { stroke-dashoffset: 0; }
        }

        @keyframes fade-in-fill {
            to { opacity: 1; }
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        @keyframes pulse-badge {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        @keyframes count-up {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── SCROLL ANIMATIONS ── */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.7s cubic-bezier(0.22,1,0.36,1), transform 0.7s cubic-bezier(0.22,1,0.36,1);
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            .hero-inner { grid-template-columns: 1fr; gap: 3rem; }
            .hero-right { display: none; }
            .products-grid { grid-template-columns: 1fr 1fr; }
            .metrics-inner { grid-template-columns: repeat(2, 1fr); }
            .why-inner { grid-template-columns: 1fr; }
            .security-grid { grid-template-columns: 1fr 1fr; }
            .footer-inner { grid-template-columns: 1fr 1fr; gap: 2rem; }
        }

        @media (max-width: 700px) {
            .main-nav { display: none; }
            .products-grid { grid-template-columns: 1fr; }
            .metrics-inner { grid-template-columns: 1fr 1fr; }
            .security-grid { grid-template-columns: 1fr; }
            .footer-inner { grid-template-columns: 1fr; }
            .cta-actions { flex-direction: column; align-items: center; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
            .reveal { opacity: 1; transform: none; }
        }
    </style>
</head>
<body>

<canvas id="hero-canvas"></canvas>
<div class="page-grid"></div>

<div class="site">

    <!-- ── TOPBAR ── -->
    <header class="topbar" id="topbar">
        <a href="{{ route('home') }}" class="brand" aria-label="MARS home">
            <span class="brand-mark">M</span>
            <span class="brand-text">MARS</span>
        </a>

        <nav class="main-nav" aria-label="Main navigation">
            <a href="{{ route('personal.dashboard') }}">Personal</a>
            <a href="#business">Business</a>
            <a href="#cards">Cards</a>
            <a href="{{ auth()->check() ? route('personal.loan') : route('login') }}">Loans</a>
            <a href="#insights">Insights</a>
        </nav>

        <nav class="auth-nav" aria-label="Auth">
            @auth
                <details class="profile-menu">
                    <summary class="btn btn-secondary profile-trigger">{{ Auth::user()->name }} ▾</summary>
                    <div class="profile-dropdown">
                        <a href="{{ route('profile.edit') }}" class="dropdown-link">⚙ Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-link logout-link">↩ Logout</button>
                        </form>
                    </div>
                </details>
            @else
                <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
            @endauth
        </nav>
    </header>

    <main>

        <!-- ── HERO ── -->
        <section class="hero-section">
            <div class="orb orb-a"></div>
            <div class="orb orb-b"></div>
            <div class="orb orb-c"></div>

            <div class="hero-inner">
                <div class="hero-left">
                    <div class="hero-eyebrow">
                        <span class="eyebrow-dot"></span>
                        Future-Ready Banking Platform
                    </div>
                    <h1 class="hero-h1">
                        Premium<br>
                        <span class="gold-word">Digital</span> Banking<br>
                        for <span class="blue-word">Modern</span> Life.
                    </h1>
                    <p class="hero-sub">
                        MARS delivers secure transactions, intelligent insights, and fast account control in one unified platform for individuals and businesses.
                    </p>
                    <div class="hero-actions">
                        @auth
                            <a href="{{ route('profile.edit') }}" class="btn btn-gold btn-lg">Manage Profile</a>
                            <form method="POST" action="{{ route('logout') }}" class="inline-action">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-lg">Secure Logout</button>
                            </form>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-gold btn-lg">Open an Account →</a>
                            <a href="{{ route('login') }}" class="btn btn-secondary btn-lg">Sign In</a>
                        @endauth
                    </div>
                    <div class="trust-row">
                        <div class="trust-item"><span class="trust-icon">🔒</span> 256-bit SSL</div>
                        <div class="trust-item"><span class="trust-icon">🛡</span> Fraud Protected</div>
                        <div class="trust-item"><span class="trust-icon">⚡</span> Instant Transfer</div>
                    </div>
                </div>

                <!-- Dashboard mockup -->
                <div class="hero-right">
                    <div style="position:relative; padding: 20px 30px 20px 20px;">
                        <!-- Floating stats -->
                        <div class="float-stat float-stat-1">
                            <div class="fs-label">Transfer Rate</div>
                            <div class="fs-value" style="color:var(--success)">99.98%</div>
                            <div class="fs-sub" style="color:var(--success)">↑ All systems go</div>
                        </div>
                        <div class="float-stat float-stat-2">
                            <div class="fs-label">Avg. Speed</div>
                            <div class="fs-value">2.4s</div>
                            <div class="fs-sub" style="color:var(--muted)">Per transaction</div>
                        </div>

                        <div class="dashboard-mock">
                            <div class="mock-header">
                                <span class="mock-title">Account Overview</span>
                                <div class="mock-dots">
                                    <div class="mock-dot dot-r"></div>
                                    <div class="mock-dot dot-y"></div>
                                    <div class="mock-dot dot-g"></div>
                                </div>
                            </div>

                            <div class="mock-balance">
                                <div class="mock-bal-label">Available Balance</div>
                                <div class="mock-bal-amount">৳ 84,250.00</div>
                                <div class="mock-bal-change">↑ 12.4% this month</div>
                            </div>

                            <div class="mock-chart">
                                <svg viewBox="0 0 300 60" preserveAspectRatio="none">
                                    <defs>
                                        <linearGradient id="chart-grad" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#3b9eff" stop-opacity="0.3"/>
                                            <stop offset="100%" stop-color="#3b9eff" stop-opacity="0"/>
                                        </linearGradient>
                                    </defs>
                                    <path class="chart-fill" d="M0,50 L30,42 L60,38 L90,44 L120,30 L150,25 L180,20 L210,28 L240,15 L270,10 L300,8 L300,60 L0,60 Z"/>
                                    <path class="chart-path" d="M0,50 L30,42 L60,38 L90,44 L120,30 L150,25 L180,20 L210,28 L240,15 L270,10 L300,8"/>
                                </svg>
                            </div>

                            <div class="mock-tx-label">Recent Transactions</div>
                            <div class="mock-tx">
                                <div class="tx-row">
                                    <div class="tx-left">
                                        <div class="tx-icon icon-green">↓</div>
                                        <div><div class="tx-name">Salary Credit</div><div class="tx-date">Today, 9:00 AM</div></div>
                                    </div>
                                    <div class="tx-amount tx-pos">+৳ 45,000</div>
                                </div>
                                <div class="tx-row">
                                    <div class="tx-left">
                                        <div class="tx-icon icon-blue">↑</div>
                                        <div><div class="tx-name">Transfer Sent</div><div class="tx-date">Yesterday</div></div>
                                    </div>
                                    <div class="tx-amount tx-neg">-৳ 2,500</div>
                                </div>
                                <div class="tx-row">
                                    <div class="tx-left">
                                        <div class="tx-icon icon-gold">★</div>
                                        <div><div class="tx-name">Cashback Reward</div><div class="tx-date">Dec 20</div></div>
                                    </div>
                                    <div class="tx-amount tx-pos">+৳ 320</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── TICKER ── -->
        <div class="ticker-section">
            <div class="ticker-track" id="ticker">
                <div class="ticker-item"><span class="ticker-label">Transfer Success</span><span class="ticker-value">99.98%</span><span class="ticker-badge badge-up">▲ Live</span></div>
                <div class="ticker-item"><span class="ticker-label">Active Accounts</span><span class="ticker-value">1.2M+</span><span class="ticker-badge badge-live">●</span></div>
                <div class="ticker-item"><span class="ticker-label">Avg Transfer Time</span><span class="ticker-value">2.4s</span><span class="ticker-badge badge-up">Fast</span></div>
                <div class="ticker-item"><span class="ticker-label">Daily Transactions</span><span class="ticker-value">280K+</span><span class="ticker-badge badge-live">Live</span></div>
                <div class="ticker-item"><span class="ticker-label">Fraud Blocked (24h)</span><span class="ticker-value">৳ 4.2M</span><span class="ticker-badge badge-up">Protected</span></div>
                <div class="ticker-item"><span class="ticker-label">System Uptime</span><span class="ticker-value">100%</span><span class="ticker-badge badge-live">●</span></div>
                <div class="ticker-item"><span class="ticker-label">Customer Satisfaction</span><span class="ticker-value">4.9 / 5</span><span class="ticker-badge badge-up">★★★★★</span></div>
                <!-- duplicate for seamless loop -->
                <div class="ticker-item"><span class="ticker-label">Transfer Success</span><span class="ticker-value">99.98%</span><span class="ticker-badge badge-up">▲ Live</span></div>
                <div class="ticker-item"><span class="ticker-label">Active Accounts</span><span class="ticker-value">1.2M+</span><span class="ticker-badge badge-live">●</span></div>
                <div class="ticker-item"><span class="ticker-label">Avg Transfer Time</span><span class="ticker-value">2.4s</span><span class="ticker-badge badge-up">Fast</span></div>
                <div class="ticker-item"><span class="ticker-label">Daily Transactions</span><span class="ticker-value">280K+</span><span class="ticker-badge badge-live">Live</span></div>
                <div class="ticker-item"><span class="ticker-label">Fraud Blocked (24h)</span><span class="ticker-value">৳ 4.2M</span><span class="ticker-badge badge-up">Protected</span></div>
                <div class="ticker-item"><span class="ticker-label">System Uptime</span><span class="ticker-value">100%</span><span class="ticker-badge badge-live">●</span></div>
                <div class="ticker-item"><span class="ticker-label">Customer Satisfaction</span><span class="ticker-value">4.9 / 5</span><span class="ticker-badge badge-up">★★★★★</span></div>
            </div>
        </div>

        <!-- ── PRODUCTS ── -->
        <section id="business" class="section">
            <div class="section-inner">
                <div class="section-head reveal">
                    <div class="section-eyebrow">⚡ Banking Solutions</div>
                    <h2>Everything You Need<br>in One Account</h2>
                    <p>Clean, reliable products designed for everyday and long-term financial goals.</p>
                </div>
                <div class="products-grid">
                    <article id="cards" class="product-card reveal reveal-delay-1">
                        <div class="product-icon icon-green">💰</div>
                        <h3>Smart Savings</h3>
                        <p>High-yield savings with adaptive interest rates and instant liquidity controls. Grow your wealth automatically.</p>
                        <a href="#" class="product-link">Explore <span class="product-link-arrow">→</span></a>
                    </article>
                    <article class="product-card featured reveal reveal-delay-2">
                        <div class="product-icon icon-gold">💳</div>
                        <h3>Credit Elite</h3>
                        <p>Premium card program with cashback rewards, travel benefits, and advanced security controls.</p>
                        <a href="#" class="product-link">Explore <span class="product-link-arrow">→</span></a>
                    </article>
                    <article id="loans" class="product-card reveal reveal-delay-3">
                        <div class="product-icon icon-blue">🏠</div>
                        <h3>Home Finance</h3>
                        <p>Transparent mortgage process with fast pre-approval and flexible tenures that suit your life.</p>
                        <a href="{{ auth()->check() ? route('personal.loan') : route('login') }}" class="product-link">Explore <span class="product-link-arrow">→</span></a>
                    </article>
                    <article class="product-card reveal reveal-delay-1">
                        <div class="product-icon icon-blue">⚡</div>
                        <h3>Instant Transfers</h3>
                        <p>Send money to any account in seconds, 24/7. No delays, no hidden fees, full transparency.</p>
                        <a href="{{ auth()->check() ? route('personal.dashboard') : route('login') }}" class="product-link">Explore <span class="product-link-arrow">→</span></a>
                    </article>
                    <article class="product-card reveal reveal-delay-2">
                        <div class="product-icon icon-green">📊</div>
                        <h3>Smart Insights</h3>
                        <p>AI-powered spending analysis, budget alerts, and financial health scores updated in real time.</p>
                        <a href="#insights" class="product-link">Explore <span class="product-link-arrow">→</span></a>
                    </article>
                    <article class="product-card reveal reveal-delay-3">
                        <div class="product-icon icon-gold">🔒</div>
                        <h3>Instant Loans</h3>
                        <p>Get approved in minutes with no document uploads. Password + OTP secured disbursement directly to your account.</p>
                        <a href="{{ auth()->check() ? route('personal.loan') : route('login') }}" class="product-link">Explore <span class="product-link-arrow">→</span></a>
                    </article>
                </div>
            </div>
        </section>

        <!-- ── METRICS ── -->
        <div class="metrics-section">
            <div class="metrics-inner">
                <div class="metric-card reveal">
                    <div class="metric-icon">⚡</div>
                    <div class="metric-num"><span class="metric-accent" data-target="99.98">0</span>%</div>
                    <div class="metric-label">Transfer Success Rate</div>
                </div>
                <div class="metric-card reveal reveal-delay-1">
                    <div class="metric-icon">🕐</div>
                    <div class="metric-num"><span class="metric-accent" data-target="2.4">0</span>s</div>
                    <div class="metric-label">Avg Transfer Time</div>
                </div>
                <div class="metric-card reveal reveal-delay-2">
                    <div class="metric-icon">👥</div>
                    <div class="metric-num"><span class="metric-accent" data-target="1.2">0</span>M+</div>
                    <div class="metric-label">Protected Accounts</div>
                </div>
                <div class="metric-card reveal reveal-delay-3">
                    <div class="metric-icon">🌍</div>
                    <div class="metric-num"><span class="metric-accent" data-target="280">0</span>K+</div>
                    <div class="metric-label">Daily Transactions</div>
                </div>
            </div>
        </div>

        <!-- ── WHY MARS ── -->
        <section id="insights" class="why-section">
            <div class="why-inner">
                <div class="why-left reveal">
                    <div class="section-eyebrow">🛡 Why MARS</div>
                    <h2 style="font-family:'Syne',sans-serif;font-size:clamp(1.8rem,3vw,2.5rem);font-weight:800;color:#dceeff;letter-spacing:-0.02em;margin-bottom:0.5rem;line-height:1.1">Built Different.<br>For <span style="background:linear-gradient(135deg,var(--gold-b),var(--gold));-webkit-background-clip:text;-webkit-text-fill-color:transparent">You.</span></h2>
                    <p style="color:var(--muted);font-size:0.9rem;line-height:1.6;margin-bottom:0">Every feature is designed to give you more control and confidence over your money.</p>

                    <div class="why-features">
                        <div class="feature-row">
                            <div class="feature-icon-wrap icon-blue">🔒</div>
                            <div>
                                <div class="feature-title">Enterprise-Grade Security</div>
                                <div class="feature-desc">256-bit TLS encryption, real-time fraud detection, and multi-factor authentication on every action.</div>
                            </div>
                        </div>
                        <div class="feature-row">
                            <div class="feature-icon-wrap icon-green">⚡</div>
                            <div>
                                <div class="feature-title">Instant Everything</div>
                                <div class="feature-desc">Transfers, loan disbursements, card controls — all processed in seconds, never hours.</div>
                            </div>
                        </div>
                        <div class="feature-row">
                            <div class="feature-icon-wrap icon-gold">📊</div>
                            <div>
                                <div class="feature-title">Intelligent Insights</div>
                                <div class="feature-desc">Real-time spending analytics, budget alerts, and financial health scoring powered by smart data.</div>
                            </div>
                        </div>
                        <div class="feature-row">
                            <div class="feature-icon-wrap icon-blue">🌐</div>
                            <div>
                                <div class="feature-title">24/7 Digital Support</div>
                                <div class="feature-desc">Round-the-clock assistance with real-time push notifications and in-app chat support.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="reveal reveal-delay-2">
                    <div class="neo-card">
                        <div class="neo-badge">✦ New</div>
                        <h3>MARS<br>Neo App</h3>
                        <p>Track spending, set budgets, lock your cards, and manage everything — all from your mobile in real time.</p>
                        <div class="neo-features">
                            <div class="neo-feat">Instant card freeze / unfreeze</div>
                            <div class="neo-feat">Smart budget tracking with alerts</div>
                            <div class="neo-feat">Biometric login & 2FA</div>
                            <div class="neo-feat">Spend categorisation & reports</div>
                            <div class="neo-feat">Loan & repayment management</div>
                        </div>
                        <a href="#" class="btn btn-gold">Get Early Access →</a>
                        <div class="phone-mockup">📱</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── SECURITY ── -->
        <section class="security-section">
            <div class="security-inner">
                <div class="section-head reveal">
                    <div class="section-eyebrow">🔐 Security First</div>
                    <h2>Your Money is Safe<br>with MARS</h2>
                    <p>We use the same security standards trusted by global financial institutions.</p>
                </div>
                <div class="security-grid">
                    <div class="sec-card reveal">
                        <div class="sec-card-icon">🔐</div>
                        <h4>End-to-End Encryption</h4>
                        <p>Every transaction and data packet is encrypted with 256-bit TLS — the same standard used by leading global banks.</p>
                    </div>
                    <div class="sec-card reveal reveal-delay-1">
                        <div class="sec-card-icon">🧠</div>
                        <h4>AI Fraud Detection</h4>
                        <p>Our machine learning engine monitors millions of signals in real-time to detect and block suspicious activity instantly.</p>
                    </div>
                    <div class="sec-card reveal reveal-delay-2">
                        <div class="sec-card-icon">📲</div>
                        <h4>Multi-Factor Auth</h4>
                        <p>OTP verification, biometric login, and device trust ensure only you can access your account.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── CTA ── -->
        <section class="cta-section">
            <div class="cta-bg"></div>
            <div class="cta-inner reveal">
                <h2>Ready to Bank<br><span>Smarter?</span></h2>
                <p>Join over 1.2 million people who've already made the switch to MARS. Open your account in under 2 minutes.</p>
                <div class="cta-actions">
                    <a href="{{ route('register') }}" class="btn btn-gold btn-lg">Open Free Account →</a>
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-lg">Sign In</a>
                </div>
                <p class="cta-note">✓ No monthly fees &nbsp;·&nbsp; ✓ No hidden charges &nbsp;·&nbsp; ✓ Cancel anytime</p>
            </div>
        </section>

    </main>

    <!-- ── FOOTER ── -->
    <footer class="footer">
        <div class="footer-inner">
            <div>
                <div class="brand" style="margin-bottom:0.8rem">
                    <span class="brand-mark" style="width:32px;height:32px;font-size:0.9rem">M</span>
                    <span class="brand-text" style="font-size:1rem">MARS</span>
                </div>
                <p class="footer-tagline">Premium digital banking experiences, built for confidence and modern finance.</p>
            </div>
            <div class="footer-col">
                <h5>Products</h5>
                <a href="#">Smart Savings</a>
                <a href="#">Credit Elite</a>
                <a href="{{ auth()->check() ? route('personal.loan') : route('login') }}">Instant Loans</a>
                <a href="#">Home Finance</a>
            </div>
            <div class="footer-col">
                <h5>Company</h5>
                <a href="#">About MARS</a>
                <a href="#">Careers</a>
                <a href="#">Press</a>
                <a href="#">Blog</a>
            </div>
            <div class="footer-col">
                <h5>Legal</h5>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Security</a>
                <a href="#">Support</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© 2026 MARS Bank. All rights reserved.</span>
            <div class="footer-secure">
                <span class="secure-dot"></span>
                All systems operational · 256-bit TLS secured
            </div>
        </div>
    </footer>

</div>

<script>
// ── Particle canvas ──
const canvas = document.getElementById('hero-canvas');
const ctx = canvas.getContext('2d');
let W, H, particles = [];

const resize = () => {
    W = canvas.width = window.innerWidth;
    H = canvas.height = window.innerHeight;
};
resize();
window.addEventListener('resize', resize);

for (let i = 0; i < 80; i++) {
    particles.push({
        x: Math.random() * window.innerWidth,
        y: Math.random() * window.innerHeight,
        r: Math.random() * 1.5 + 0.2,
        vx: (Math.random() - 0.5) * 0.3,
        vy: (Math.random() - 0.5) * 0.3,
        a: Math.random() * 0.4 + 0.05
    });
}

const animParticles = () => {
    ctx.clearRect(0, 0, W, H);
    particles.forEach(p => {
        p.x += p.vx; p.y += p.vy;
        if (p.x < 0) p.x = W;
        if (p.x > W) p.x = 0;
        if (p.y < 0) p.y = H;
        if (p.y > H) p.y = 0;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(99,179,237,${p.a})`;
        ctx.fill();
    });
    requestAnimationFrame(animParticles);
};
animParticles();

// ── Topbar scroll ──
const topbar = document.getElementById('topbar');
window.addEventListener('scroll', () => {
    topbar.classList.toggle('scrolled', window.scrollY > 40);
});

// ── Scroll reveal ──
const revealEls = document.querySelectorAll('.reveal');
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
            observer.unobserve(e.target);
        }
    });
}, { threshold: 0.12 });
revealEls.forEach(el => observer.observe(el));

// ── Animated number counters ──
const counters = document.querySelectorAll('[data-target]');
const counterObserver = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (!e.isIntersecting) return;
        const el = e.target;
        const target = parseFloat(el.dataset.target);
        const isDecimal = target % 1 !== 0;
        const duration = 1800;
        const start = performance.now();
        const update = (now) => {
            const p = Math.min((now - start) / duration, 1);
            const ease = 1 - Math.pow(1 - p, 3);
            const val = target * ease;
            el.textContent = isDecimal ? val.toFixed(1) : Math.floor(val);
            if (p < 1) requestAnimationFrame(update);
            else el.textContent = isDecimal ? target.toFixed(target === 2.4 ? 1 : 2) : target;
        };
        requestAnimationFrame(update);
        counterObserver.unobserve(el);
    });
}, { threshold: 0.5 });
counters.forEach(c => counterObserver.observe(c));

// ── Mouse parallax on hero mockup ──
document.addEventListener('mousemove', (e) => {
    const x = (e.clientX / window.innerWidth - 0.5) * 12;
    const y = (e.clientY / window.innerHeight - 0.5) * 8;
    const mock = document.querySelector('.hero-right');
    if (mock) mock.style.transform = `perspective(1000px) rotateY(${-x * 0.3}deg) rotateX(${y * 0.3}deg)`;
});
</script>
</body>
</html>