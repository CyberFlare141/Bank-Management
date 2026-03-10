<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | MARS Bank</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #05090f;
            --panel: rgba(8, 16, 30, 0.88);
            --border: rgba(80, 160, 240, 0.14);
            --border-h: rgba(80, 160, 240, 0.35);
            --text: #e8f2ff;
            --muted: #6a87aa;
            --blue: #1868c4;
            --blue-b: #3b9eff;
            --gold: #c4934a;
            --gold-b: #f0b96a;
            --success: #3dd68c;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: "DM Sans", sans-serif;
            color: var(--text);
            background: var(--bg);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .page-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(80,160,240,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(80,160,240,0.04) 1px, transparent 1px);
            background-size: 50px 50px;
            mask-image: radial-gradient(ellipse at 50% 35%, black 25%, transparent 80%);
        }

        .site { position: relative; z-index: 1; }

        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            opacity: 0.75;
            z-index: 0;
        }

        .orb-a {
            width: 520px;
            height: 520px;
            top: -140px;
            left: -80px;
            background: radial-gradient(circle, rgba(24,104,196,0.22), transparent 70%);
        }

        .orb-b {
            width: 460px;
            height: 460px;
            right: -100px;
            top: 180px;
            background: radial-gradient(circle, rgba(196,147,74,0.18), transparent 70%);
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 4vw;
            background: rgba(5, 9, 15, 0.8);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
        }

        .brand-mark {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--blue), var(--blue-b));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Syne", sans-serif;
            font-weight: 800;
            font-size: 1rem;
            color: #fff;
            box-shadow: 0 0 18px rgba(59,158,255,0.4);
        }

        .brand-text {
            font-family: "Syne", sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            letter-spacing: 0.1em;
            color: #dceeff;
        }

        .main-nav {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            flex-wrap: wrap;
            justify-content: center;
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
        .profile-actions { display: flex; align-items: center; gap: 0.6rem; }
        .notif-btn {
            position: relative;
            width: 42px;
            height: 42px;
            padding: 0;
            border-radius: 12px;
        }
        .notif-btn svg { width: 18px; height: 18px; }
        .notif-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--success);
            box-shadow: 0 0 0 2px rgba(5, 9, 15, 0.95);
        }
        .profile-menu { position: relative; list-style: none; }
        .profile-menu summary { cursor: pointer; list-style: none; }
        .profile-menu summary::-webkit-details-marker { display: none; }
        .profile-dropdown {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            min-width: 170px;
            background: rgba(9,18,36,0.98);
            border: 1px solid var(--border-h);
            border-radius: 12px;
            padding: 0.4rem;
            backdrop-filter: blur(12px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.5);
        }
        .dropdown-link {
            display: block;
            width: 100%;
            padding: 0.55rem 0.75rem;
            border: none;
            border-radius: 8px;
            background: none;
            color: var(--muted);
            text-align: left;
            text-decoration: none;
            cursor: pointer;
            font: inherit;
        }
        .dropdown-link:hover { background: rgba(80,160,240,0.1); color: var(--text); }
        .logout-link { color: #f87171; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: none;
            border-radius: 10px;
            font-family: "Syne", sans-serif;
            font-weight: 700;
            font-size: 0.82rem;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: all 0.2s;
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

        .container {
            max-width: 1150px;
            margin: 0 auto;
            width: calc(100% - 2.5rem);
        }

        .hero {
            padding: 7rem 0 3rem;
            text-align: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.7rem;
            color: #7ec8f7;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            border: 1px solid rgba(80,160,240,0.25);
            background: rgba(24,104,196,0.1);
            border-radius: 999px;
            padding: 0.32rem 0.85rem;
            margin-bottom: 1.1rem;
        }

        .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 8px var(--success);
        }

        .hero h1 {
            font-family: "Syne", sans-serif;
            font-size: clamp(2.3rem, 5vw, 3.6rem);
            line-height: 1.05;
            letter-spacing: -0.02em;
            margin-bottom: 1rem;
        }

        .hero h1 span {
            background: linear-gradient(135deg, var(--gold-b), var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            max-width: 850px;
            margin: 0 auto;
            color: var(--muted);
            font-size: 1.03rem;
            line-height: 1.75;
        }

        .section {
            margin-top: 1.6rem;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: clamp(1.25rem, 3vw, 2rem);
            backdrop-filter: blur(8px);
            box-shadow: 0 24px 50px rgba(0,0,0,0.35);
        }

        .section h2 {
            font-family: "Syne", sans-serif;
            font-size: clamp(1.5rem, 2.6vw, 2rem);
            margin-bottom: 0.7rem;
            color: #dceeff;
        }

        .section p {
            color: var(--muted);
            line-height: 1.75;
        }

        .team-grid {
            margin-top: 1.2rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .member-card {
            background: rgba(7, 14, 27, 0.9);
            border: 1px solid rgba(80,160,240,0.17);
            border-radius: 16px;
            padding: 1rem;
            transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
            box-shadow: 0 16px 30px rgba(0,0,0,0.3);
        }

        .member-card:hover {
            transform: translateY(-5px);
            border-color: rgba(80,160,240,0.38);
            box-shadow: 0 22px 35px rgba(0,0,0,0.42);
        }

        .member-photo {
            width: 86px;
            height: 86px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(80,160,240,0.35);
            margin-bottom: 0.85rem;
        }

        .member-name {
            font-family: "Syne", sans-serif;
            font-size: 1.05rem;
            color: #e7f1ff;
        }

        .member-role {
            display: inline-block;
            margin: 0.45rem 0 0.65rem;
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: #8ecfff;
            background: rgba(24,104,196,0.14);
            border: 1px solid rgba(80,160,240,0.28);
            border-radius: 999px;
            padding: 0.2rem 0.62rem;
        }

        .member-bio {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
        }

        .socials {
            display: flex;
            gap: 0.5rem;
        }

        .social-link {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            display: grid;
            place-items: center;
            color: #a9d8ff;
            background: rgba(24,104,196,0.12);
            border: 1px solid rgba(80,160,240,0.22);
            text-decoration: none;
            transition: background 0.2s, border-color 0.2s;
        }

        .social-link:hover {
            background: rgba(24,104,196,0.2);
            border-color: rgba(80,160,240,0.4);
        }

        .cta {
            margin: 1.6rem 0 3.5rem;
            padding: 1.8rem 1.2rem;
            border-radius: 20px;
            border: 1px solid rgba(240,185,106,0.3);
            background: linear-gradient(135deg, rgba(154,111,42,0.26), rgba(24,104,196,0.2));
            box-shadow: 0 24px 46px rgba(0,0,0,0.35);
            text-align: center;
        }

        .cta h3 {
            font-family: "Syne", sans-serif;
            font-size: clamp(1.35rem, 2.5vw, 2rem);
            margin-bottom: 0.45rem;
        }

        .cta p {
            color: #b2c9e6;
            margin-bottom: 1rem;
        }

        .cta .btn {
            font-size: 0.9rem;
            padding: 0.62rem 1.05rem;
        }

        @media (max-width: 1050px) {
            .topbar {
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.75rem;
            }

            .team-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 680px) {
            .container { width: calc(100% - 1.4rem); }
            .hero { padding-top: 5rem; }
            .team-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="orb orb-a"></div>
    <div class="orb orb-b"></div>
    <div class="page-grid"></div>

    <div class="site">
        @php
            $aboutUnreadNotifications = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
        @endphp
        <header class="topbar">
            <a href="{{ route('home') }}" class="brand" aria-label="MARS home">
                <span class="brand-mark">M</span>
                <span class="brand-text">MARS</span>
            </a>

            <nav class="main-nav" aria-label="Main navigation">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ auth()->check() ? route('personal.dashboard') : route('login') }}">Personal Dashboard</a>
                <a href="{{ auth()->check() ? route('personal.cards') : route('login') }}">Cards</a>
                <a href="{{ auth()->check() ? route('personal.loan') : route('login') }}">Loans</a>
                <a href="{{ route('about') }}">About Us</a>
                <a href="{{ auth()->check() ? route('contact.create') : route('login') }}">Contact</a>
                <a href="{{ auth()->check() ? route('profile.edit') : route('login') }}">Profile</a>
                @auth
                    @if (Auth::user()?->isAdminUser())
                        <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                    @endif
                @endauth
            </nav>

            <nav class="auth-nav" aria-label="Auth">
                <button
                    type="button"
                    onclick="window.history.length > 1 ? window.history.back() : window.location.assign('{{ route('home') }}')"
                    class="btn btn-secondary"
                >
                    ← Back
                </button>
                @auth
                    <div class="profile-actions">
                        <a
                            href="{{ route('personal.dashboard') }}"
                            class="btn btn-secondary notif-btn"
                            aria-label="Notifications{{ $aboutUnreadNotifications > 0 ? ' (' . $aboutUnreadNotifications . ' unread)' : '' }}"
                            title="Notifications"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M14.5 18a2.5 2.5 0 0 1-5 0"></path>
                                <path d="M18 16V11a6 6 0 1 0-12 0v5l-2 2h16l-2-2z"></path>
                            </svg>
                            @if ($aboutUnreadNotifications > 0)
                                <span class="notif-badge" aria-hidden="true"></span>
                            @endif
                        </a>
                        <details class="profile-menu">
                            <summary class="btn btn-secondary">
                                {{ Auth::user()->name }} ▾
                            </summary>
                            <div class="profile-dropdown">
                                @if (Auth::user()?->isAdminUser())
                                    <a href="{{ route('admin.dashboard') }}" class="dropdown-link">Admin Dashboard</a>
                                @endif
                                <a href="{{ route('profile.edit') }}" class="dropdown-link">Manage Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-link logout-link">Logout</button>
                                </form>
                            </div>
                        </details>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                @endauth
            </nav>
        </header>

        <main class="container">
            <section class="hero">
                <div class="eyebrow">
                    <span class="dot"></span>
                    Team and Mission
                </div>
                <h1>About <span>MARS</span></h1>
                <p>
                    We are a passionate team of developers dedicated to building secure, reliable, and user-friendly digital solutions.
                    Our goal is to simplify financial management through modern technology and intuitive design.
                </p>
            </section>

            <section class="section" id="mission" aria-labelledby="mission-title">
                <h2 id="mission-title">Our Mission and Vision</h2>
                <p>
                    Our mission is to create efficient digital systems that improve accessibility, security, and convenience for users
                    managing financial services online. We envision a platform where people can confidently manage personal finance
                    through fast services, clear workflows, and strong protection standards.
                </p>
            </section>

            <section class="section" id="team" aria-labelledby="team-title">
                <h2 id="team-title">Meet the Team</h2>
                <p>Our core engineering and operations group works together to deliver a secure and smooth banking experience.</p>

                <div class="team-grid">
                    <article class="member-card">
                        <img
                            class="member-photo"
                            src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80"
                            alt="Portrait of Abdul Mumit Sazid"
                            loading="lazy"
                        >
                        <h3 class="member-name">Abdul Mumit Sazid</h3>
                        <span class="member-role">Backend Developer</span>
                        <p class="member-bio">
                            Abdul specializes in backend architecture and database management. He develops secure APIs and keeps
                            data flows efficient and reliable across core banking features.
                        </p>
                        <div class="socials">
                            <a class="social-link" href="#" aria-label="Abdul on LinkedIn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.94 8.5H3.56V20h3.38zM5.25 3A2.25 2.25 0 1 0 5.25 7.5 2.25 2.25 0 0 0 5.25 3zm15.19 9.73C20.44 9.8 18.84 8 16.07 8c-1.41 0-2.36.78-2.75 1.33V8.5H9.94V20h3.38v-6.2c0-1.63.31-3.21 2.33-3.21 1.99 0 2.02 1.86 2.02 3.32V20h3.38z"/></svg>
                            </a>
                            <a class="social-link" href="#" aria-label="Abdul on GitHub">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 .5a12 12 0 0 0-3.79 23.39c.6.11.82-.26.82-.58v-2.23c-3.34.73-4.04-1.42-4.04-1.42-.55-1.38-1.33-1.74-1.33-1.74-1.08-.74.08-.73.08-.73 1.2.08 1.83 1.24 1.83 1.24 1.06 1.84 2.8 1.31 3.49 1 .11-.78.41-1.3.74-1.59-2.66-.31-5.46-1.35-5.46-6 0-1.32.47-2.4 1.23-3.24-.12-.31-.53-1.57.12-3.28 0 0 1.01-.33 3.3 1.24a11.3 11.3 0 0 1 6 0c2.28-1.57 3.29-1.24 3.29-1.24.65 1.71.24 2.97.12 3.28.76.84 1.23 1.92 1.23 3.24 0 4.66-2.8 5.69-5.47 5.99.43.38.82 1.12.82 2.27v3.36c0 .32.22.69.83.58A12 12 0 0 0 12 .5z"/></svg>
                            </a>
                        </div>
                    </article>

                    <article class="member-card">
                        <img
                            class="member-photo"
                            src="https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=300&q=80"
                            alt="Portrait of Ayesha Rahman"
                            loading="lazy"
                        >
                        <h3 class="member-name">Ayesha Rahman</h3>
                        <span class="member-role">Frontend Developer</span>
                        <p class="member-bio">
                            Ayesha builds responsive and user-focused interfaces. She ensures smooth interactions and clear visual
                            feedback for users across desktop and mobile experiences.
                        </p>
                        <div class="socials">
                            <a class="social-link" href="#" aria-label="Ayesha on LinkedIn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.94 8.5H3.56V20h3.38zM5.25 3A2.25 2.25 0 1 0 5.25 7.5 2.25 2.25 0 0 0 5.25 3zm15.19 9.73C20.44 9.8 18.84 8 16.07 8c-1.41 0-2.36.78-2.75 1.33V8.5H9.94V20h3.38v-6.2c0-1.63.31-3.21 2.33-3.21 1.99 0 2.02 1.86 2.02 3.32V20h3.38z"/></svg>
                            </a>
                            <a class="social-link" href="#" aria-label="Ayesha on GitHub">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 .5a12 12 0 0 0-3.79 23.39c.6.11.82-.26.82-.58v-2.23c-3.34.73-4.04-1.42-4.04-1.42-.55-1.38-1.33-1.74-1.33-1.74-1.08-.74.08-.73.08-.73 1.2.08 1.83 1.24 1.83 1.24 1.06 1.84 2.8 1.31 3.49 1 .11-.78.41-1.3.74-1.59-2.66-.31-5.46-1.35-5.46-6 0-1.32.47-2.4 1.23-3.24-.12-.31-.53-1.57.12-3.28 0 0 1.01-.33 3.3 1.24a11.3 11.3 0 0 1 6 0c2.28-1.57 3.29-1.24 3.29-1.24.65 1.71.24 2.97.12 3.28.76.84 1.23 1.92 1.23 3.24 0 4.66-2.8 5.69-5.47 5.99.43.38.82 1.12.82 2.27v3.36c0 .32.22.69.83.58A12 12 0 0 0 12 .5z"/></svg>
                            </a>
                        </div>
                    </article>

                    <article class="member-card">
                        <img
                            class="member-photo"
                            src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&q=80"
                            alt="Portrait of Tanvir Hasan"
                            loading="lazy"
                        >
                        <h3 class="member-name">Tanvir Hasan</h3>
                        <span class="member-role">DevOps Engineer</span>
                        <p class="member-bio">
                            Tanvir manages infrastructure, delivery pipelines, and runtime stability. He ensures reliable deployment
                            with containerized workflows and secure cloud operations.
                        </p>
                        <div class="socials">
                            <a class="social-link" href="#" aria-label="Tanvir on LinkedIn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.94 8.5H3.56V20h3.38zM5.25 3A2.25 2.25 0 1 0 5.25 7.5 2.25 2.25 0 0 0 5.25 3zm15.19 9.73C20.44 9.8 18.84 8 16.07 8c-1.41 0-2.36.78-2.75 1.33V8.5H9.94V20h3.38v-6.2c0-1.63.31-3.21 2.33-3.21 1.99 0 2.02 1.86 2.02 3.32V20h3.38z"/></svg>
                            </a>
                            <a class="social-link" href="#" aria-label="Tanvir on GitHub">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 .5a12 12 0 0 0-3.79 23.39c.6.11.82-.26.82-.58v-2.23c-3.34.73-4.04-1.42-4.04-1.42-.55-1.38-1.33-1.74-1.33-1.74-1.08-.74.08-.73.08-.73 1.2.08 1.83 1.24 1.83 1.24 1.06 1.84 2.8 1.31 3.49 1 .11-.78.41-1.3.74-1.59-2.66-.31-5.46-1.35-5.46-6 0-1.32.47-2.4 1.23-3.24-.12-.31-.53-1.57.12-3.28 0 0 1.01-.33 3.3 1.24a11.3 11.3 0 0 1 6 0c2.28-1.57 3.29-1.24 3.29-1.24.65 1.71.24 2.97.12 3.28.76.84 1.23 1.92 1.23 3.24 0 4.66-2.8 5.69-5.47 5.99.43.38.82 1.12.82 2.27v3.36c0 .32.22.69.83.58A12 12 0 0 0 12 .5z"/></svg>
                            </a>
                        </div>
                    </article>
                </div>
            </section>

            <section class="cta" id="contact" aria-labelledby="contact-title">
                <h3 id="contact-title">Ready to Connect With the Team?</h3>
                <p>Have feedback or collaboration ideas? Reach out to us and we will respond as soon as possible.</p>
                <a href="{{ auth()->check() ? route('contact.create') : route('login') }}" class="btn btn-primary">Contact Us</a>
            </section>
        </main>
    </div>
</body>
</html>
