<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | MARS Bank</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #05090f;
            --panel: rgba(8, 16, 30, 0.9);
            --border: rgba(80, 160, 240, 0.16);
            --border-h: rgba(80, 160, 240, 0.34);
            --text: #e8f2ff;
            --muted: #6a87aa;
            --blue: #1868c4;
            --blue-b: #3b9eff;
            --success: #3dd68c;
            --danger: #f87171;
        }

        body {
            font-family: "DM Sans", sans-serif;
            background: var(--bg);
            color: var(--text);
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

        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            z-index: 0;
        }

        .orb-a {
            width: 520px;
            height: 520px;
            top: -140px;
            left: -90px;
            background: radial-gradient(circle, rgba(24,104,196,0.22), transparent 70%);
        }

        .orb-b {
            width: 460px;
            height: 460px;
            right: -100px;
            top: 160px;
            background: radial-gradient(circle, rgba(59,158,255,0.2), transparent 70%);
        }

        .site {
            position: relative;
            z-index: 1;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 40;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
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
            color: #fff;
            box-shadow: 0 0 18px rgba(59,158,255,0.4);
        }

        .brand-text {
            font-family: "Syne", sans-serif;
            font-weight: 800;
            letter-spacing: 0.1em;
            color: #dceeff;
        }

        .main-nav {
            display: flex;
            gap: 0.25rem;
            flex-wrap: wrap;
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

        .btn {
            text-decoration: none;
            border: none;
            border-radius: 10px;
            font-family: "Syne", sans-serif;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--blue), var(--blue-b));
            box-shadow: 0 4px 18px rgba(59,158,255,0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(59,158,255,0.42);
        }

        .btn-secondary {
            color: var(--text);
            background: rgba(14, 26, 48, 0.8);
            border: 1px solid var(--border-h);
        }

        .btn-secondary:hover {
            background: rgba(80,160,240,0.1);
            border-color: rgba(80,160,240,0.5);
        }

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
        .logout-link { color: var(--danger); }

        .container {
            width: calc(100% - 2.2rem);
            max-width: 980px;
            margin: 0 auto;
        }

        .hero {
            padding: 6.5rem 0 2rem;
            text-align: center;
        }

        .hero h1 {
            font-family: "Syne", sans-serif;
            font-size: clamp(2.2rem, 5vw, 3.2rem);
            margin-bottom: 0.8rem;
            line-height: 1.05;
        }

        .hero p {
            max-width: 760px;
            margin: 0 auto;
            color: var(--muted);
            line-height: 1.7;
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: clamp(1.2rem, 2vw, 1.6rem);
            box-shadow: 0 24px 46px rgba(0,0,0,0.36);
            margin-bottom: 3rem;
        }

        .status {
            margin-bottom: 1rem;
            border-radius: 10px;
            padding: 0.75rem 0.9rem;
            border: 1px solid rgba(61,214,140,0.35);
            background: rgba(61,214,140,0.12);
            color: #9cf0c6;
            font-size: 0.95rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.95rem;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        .field label {
            font-weight: 600;
            color: #dceeff;
            font-size: 0.92rem;
        }

        .field input,
        .field select,
        .field textarea {
            background: rgba(7, 14, 27, 0.9);
            border: 1px solid rgba(80,160,240,0.24);
            border-radius: 10px;
            color: #e8f2ff;
            padding: 0.7rem 0.8rem;
            font-family: inherit;
            font-size: 0.94rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: var(--border-h);
            box-shadow: 0 0 0 3px rgba(59,158,255,0.15);
        }

        .field input[readonly] {
            opacity: 0.86;
            cursor: not-allowed;
        }

        .field textarea {
            min-height: 170px;
            resize: vertical;
        }

        .error {
            color: #fda4af;
            font-size: 0.82rem;
        }

        .actions {
            margin-top: 1rem;
            display: flex;
            justify-content: flex-end;
        }

        .actions .btn {
            min-width: 145px;
        }

        @media (max-width: 900px) {
            .topbar {
                justify-content: center;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 700px) {
            .container { width: calc(100% - 1.2rem); }
            .hero { padding-top: 5rem; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="orb orb-a"></div>
    <div class="orb orb-b"></div>
    <div class="page-grid"></div>

    <div class="site">
        @php($contactUnreadNotifications = auth()->user()->unreadNotifications()->count())
        <header class="topbar">
            <a href="{{ route('home') }}" class="brand" aria-label="MARS home">
                <span class="brand-mark">M</span>
                <span class="brand-text">MARS</span>
            </a>

            <nav class="main-nav" aria-label="Main navigation">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('personal.dashboard') }}">Personal Dashboard</a>
                <a href="{{ route('personal.cards') }}">Cards</a>
                <a href="{{ route('personal.loan') }}">Loans</a>
                <a href="{{ route('about') }}">About Us</a>
                <a href="{{ route('contact.create') }}">Contact</a>
                <a href="{{ route('profile.edit') }}">Profile</a>
                @if (Auth::user()?->isAdminUser())
                    <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                @endif
            </nav>

            <div style="display:flex; align-items:center; gap:0.75rem;">
                <button
                    type="button"
                    onclick="window.history.length > 1 ? window.history.back() : window.location.assign('{{ route('home') }}')"
                    class="btn btn-secondary"
                >
                    ← Back
                </button>
                <div class="profile-actions">
                    <a
                        href="{{ route('personal.dashboard') }}"
                        class="btn btn-secondary notif-btn"
                        aria-label="Notifications{{ $contactUnreadNotifications > 0 ? ' (' . $contactUnreadNotifications . ' unread)' : '' }}"
                        title="Notifications"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M14.5 18a2.5 2.5 0 0 1-5 0"></path>
                            <path d="M18 16V11a6 6 0 1 0-12 0v5l-2 2h16l-2-2z"></path>
                        </svg>
                        @if ($contactUnreadNotifications > 0)
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
            </div>
        </header>

        <main class="container">
            <section class="hero">
                <h1>Contact Us</h1>
                <p>
                    If you have any questions, suggestions, or issues regarding our platform, feel free to contact our team.
                    We will get back to you as soon as possible.
                </p>
            </section>

            <section class="card" aria-labelledby="contact-form-title">
                @if (session('status'))
                    <div class="status">{{ session('status') }}</div>
                @endif

                <h2 id="contact-form-title" style="font-family: 'Syne', sans-serif; margin-bottom: 1rem;">Send a Message</h2>

                <form method="POST" action="{{ route('contact.store') }}">
                    @csrf

                    <div class="form-grid">
                        <div class="field">
                            <label for="name">Full Name</label>
                            <input id="name" type="text" value="{{ $user->name }}" readonly>
                        </div>

                        <div class="field">
                            <label for="email">Email Address</label>
                            <input id="email" type="email" value="{{ $user->email }}" readonly>
                        </div>

                        <div class="field full">
                            <label for="recipient">Send To</label>
                            <select id="recipient" name="recipient" required>
                                <option value="all" {{ old('recipient', 'all') === 'all' ? 'selected' : '' }}>All Project Creators</option>
                                <option value="sazid" {{ old('recipient') === 'sazid' ? 'selected' : '' }}>sazid.cse.20230104140@aust.edu</option>
                                <option value="samiul" {{ old('recipient') === 'samiul' ? 'selected' : '' }}>samiul.cse.20230104142@aust.edu</option>
                                <option value="masrafi" {{ old('recipient') === 'masrafi' ? 'selected' : '' }}>masrafi.cse.20230104141@aust.edu</option>
                            </select>
                            @error('recipient')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field full">
                            <label for="subject">Subject</label>
                            <input id="subject" type="text" name="subject" value="{{ old('subject') }}" placeholder="Briefly describe your message purpose" required>
                            @error('subject')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field full">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" placeholder="Write your detailed message here..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
