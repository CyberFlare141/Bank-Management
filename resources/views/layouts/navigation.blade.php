@php
    $user = Auth::user();
    $fallbackBackUrl = $user?->isAdminUser() ? route('admin.dashboard') : route('dashboard');
    $unreadNotifications = $user?->unreadNotifications()->count() ?? 0;
    $navLinks = [
        ['label' => 'Dashboard', 'route' => route('personal.dashboard'), 'active' => request()->routeIs('personal.dashboard')],
        ['label' => 'Cards', 'route' => route('personal.cards'), 'active' => request()->routeIs('personal.cards*')],
        ['label' => 'Loans', 'route' => route('personal.loan'), 'active' => request()->routeIs('personal.loan*')],
        ['label' => 'Profile', 'route' => route('profile.edit'), 'active' => request()->routeIs('profile.*')],
        ['label' => 'About', 'route' => route('about'), 'active' => request()->routeIs('about')],
        ['label' => 'Contact', 'route' => route('contact.create'), 'active' => request()->routeIs('contact.*')],
    ];
@endphp

<style>
    .shell-nav-wrap {
        position: sticky;
        top: 0;
        z-index: 2100;
        padding: 1rem 1rem 0;
        background: linear-gradient(180deg, rgba(5, 11, 20, 0.92) 0%, rgba(5, 11, 20, 0.78) 70%, rgba(5, 11, 20, 0) 100%);
        backdrop-filter: blur(16px);
        pointer-events: none;
    }
    .shell-nav {
        max-width: 1280px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.85rem 1rem;
        border: 1px solid rgba(96, 165, 250, 0.16);
        border-radius: 22px;
        background:
            radial-gradient(circle at top left, rgba(59, 130, 246, 0.12), transparent 30%),
            rgba(7, 14, 28, 0.82);
        box-shadow: 0 18px 48px rgba(2, 6, 23, 0.38), inset 0 1px 0 rgba(255, 255, 255, 0.03);
        pointer-events: auto;
    }
    .shell-nav-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        text-decoration: none;
        color: #eef4ff;
        min-width: 0;
    }
    .shell-nav-brandmark {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        letter-spacing: 0.04em;
        background: linear-gradient(135deg, #2563eb, #22d3ee);
        box-shadow: 0 12px 28px rgba(37, 99, 235, 0.35);
    }
    .shell-nav-brandtext {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .shell-nav-title {
        font-size: 1rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.03em;
    }
    .shell-nav-subtitle {
        margin-top: 0.2rem;
        font-size: 0.7rem;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #7f93b5;
    }
    .shell-nav-center {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem;
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.55);
    }
    .shell-nav-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 0 0.95rem;
        border-radius: 999px;
        text-decoration: none;
        color: #8aa0c3;
        font-size: 0.84rem;
        font-weight: 700;
        transition: color 0.2s ease, background 0.2s ease, transform 0.2s ease;
    }
    .shell-nav-link:hover {
        color: #e7efff;
        background: rgba(255, 255, 255, 0.05);
        transform: translateY(-1px);
    }
    .shell-nav-link.is-active {
        color: #eff6ff;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.28), rgba(34, 211, 238, 0.18));
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }
    .shell-nav-right {
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }
    .shell-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 40px;
        padding: 0 0.95rem;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.12);
        background: rgba(15, 23, 42, 0.62);
        color: #c8d5eb;
        text-decoration: none;
        font-size: 0.82rem;
        font-weight: 700;
        transition: border-color 0.2s ease, background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }
    .shell-action:hover {
        border-color: rgba(96, 165, 250, 0.35);
        background: rgba(30, 41, 59, 0.82);
        color: #f8fbff;
        transform: translateY(-1px);
    }
    .shell-action.icon-only {
        width: 42px;
        padding: 0;
        position: relative;
    }
    .shell-notif-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 18px;
        height: 18px;
        padding: 0 4px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ef4444;
        color: #fff;
        font-size: 0.62rem;
        font-weight: 800;
        box-shadow: 0 0 0 3px rgba(7, 14, 28, 0.9);
    }
    .shell-profile {
        position: relative;
        list-style: none;
    }
    .shell-profile summary {
        list-style: none;
    }
    .shell-profile summary::-webkit-details-marker {
        display: none;
    }
    .shell-profile-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.7rem;
        min-height: 42px;
        padding: 0.35rem 0.45rem 0.35rem 0.8rem;
        border-radius: 16px;
        border: 1px solid rgba(148, 163, 184, 0.12);
        background: rgba(15, 23, 42, 0.7);
        color: #eef4ff;
        cursor: pointer;
        transition: border-color 0.2s ease, background 0.2s ease;
    }
    .shell-profile-btn:hover {
        border-color: rgba(96, 165, 250, 0.35);
        background: rgba(30, 41, 59, 0.82);
    }
    .shell-profile[open] .shell-profile-btn {
        border-color: rgba(96, 165, 250, 0.35);
        background: rgba(30, 41, 59, 0.82);
    }
    .shell-profile-name {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        line-height: 1.1;
    }
    .shell-profile-name strong {
        font-size: 0.84rem;
    }
    .shell-profile-name span {
        margin-top: 0.15rem;
        font-size: 0.65rem;
        color: #7f93b5;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .shell-avatar {
        width: 30px;
        height: 30px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.95), rgba(168, 85, 247, 0.95));
        color: #fff;
        font-size: 0.76rem;
        font-weight: 800;
    }
    .shell-profile-menu {
        position: absolute;
        top: calc(100% + 0.6rem);
        right: 0;
        width: 220px;
        padding: 0.45rem;
        border-radius: 18px;
        border: 1px solid rgba(96, 165, 250, 0.16);
        background: rgba(7, 14, 28, 0.96);
        box-shadow: 0 20px 50px rgba(2, 6, 23, 0.48);
    }
    .shell-profile-menu a,
    .shell-profile-menu button {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.8rem 0.9rem;
        border: 0;
        border-radius: 12px;
        background: transparent;
        color: #c8d5eb;
        text-decoration: none;
        font: inherit;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        text-align: left;
    }
    .shell-profile-menu a:hover,
    .shell-profile-menu button:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
    }
    .shell-profile-menu button.logout {
        color: #fda4af;
    }
    .shell-mobile-toggle {
        display: none;
        list-style: none;
    }
    .shell-mobile-toggle summary {
        list-style: none;
    }
    .shell-mobile-toggle summary::-webkit-details-marker {
        display: none;
    }
    .shell-mobile-panel {
        display: none;
        max-width: 1280px;
        margin: 0.8rem auto 0;
        padding: 0.8rem;
        border: 1px solid rgba(96, 165, 250, 0.16);
        border-radius: 20px;
        background: rgba(7, 14, 28, 0.94);
        box-shadow: 0 18px 48px rgba(2, 6, 23, 0.38);
        pointer-events: auto;
    }
    .shell-mobile-links {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.45rem;
    }
    .shell-mobile-links .shell-nav-link,
    .shell-mobile-links .shell-action {
        justify-content: flex-start;
        border-radius: 14px;
    }
    .shell-mobile-toggle[open] .shell-mobile-panel {
        display: block;
    }
    @media (max-width: 1100px) {
        .shell-nav-center {
            display: none;
        }
        .shell-mobile-toggle {
            display: inline-flex;
        }
    }
    @media (max-width: 720px) {
        .shell-nav-wrap {
            padding: 0.75rem 0.75rem 0;
        }
        .shell-nav {
            padding: 0.75rem;
        }
        .shell-nav-subtitle,
        .shell-profile-name span,
        .shell-action.back span {
            display: none;
        }
        .shell-action.back {
            width: 42px;
            padding: 0;
        }
    }
</style>

<nav class="shell-nav-wrap">
    <div class="shell-nav">
        <a href="{{ route('home') }}" class="shell-nav-brand">
            <span class="shell-nav-brandmark">M</span>
            <span class="shell-nav-brandtext">
                <span class="shell-nav-title">MARS Bank</span>
                <span class="shell-nav-subtitle">Digital Banking Hub</span>
            </span>
        </a>

        <div class="shell-nav-center" aria-label="Primary navigation">
            @foreach ($navLinks as $link)
                <a href="{{ $link['route'] }}" class="shell-nav-link {{ $link['active'] ? 'is-active' : '' }}">{{ $link['label'] }}</a>
            @endforeach
            @if ($user?->isAdminUser())
                <a href="{{ route('admin.dashboard') }}" class="shell-nav-link {{ request()->routeIs('admin.*') ? 'is-active' : '' }}">Admin</a>
            @endif
        </div>

        <div class="shell-nav-right">
            <button
                type="button"
                class="shell-action back"
                onclick="window.history.length > 1 ? window.history.back() : window.location.assign('{{ $fallbackBackUrl }}')"
                title="Go back"
            >
                <span aria-hidden="true">←</span>
                <span>Back</span>
            </button>

            <a href="{{ route('notifications.index') }}" class="shell-action icon-only" aria-label="Notifications">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M14.5 18a2.5 2.5 0 0 1-5 0"></path>
                    <path d="M18 16V11a6 6 0 1 0-12 0v5l-2 2h16l-2-2z"></path>
                </svg>
                @if ($unreadNotifications > 0)
                    <span class="shell-notif-badge">{{ min($unreadNotifications, 99) }}</span>
                @endif
            </a>

            <details class="shell-profile">
                <summary class="shell-profile-btn">
                    <span class="shell-profile-name">
                        <strong>{{ $user?->name }}</strong>
                        <span>{{ $user?->isAdminUser() ? 'Admin access' : 'Personal banking' }}</span>
                    </span>
                    <span class="shell-avatar">{{ strtoupper(substr((string) ($user?->name ?? 'U'), 0, 1)) }}</span>
                </summary>

                <div class="shell-profile-menu">
                    @if ($user?->isAdminUser())
                        <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                    @endif
                    <a href="{{ route('profile.edit') }}">Manage Profile</a>
                    <a href="{{ route('notifications.index') }}">Notifications</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout">Log Out</button>
                    </form>
                </div>
            </details>

            <details class="shell-mobile-toggle">
                <summary class="shell-action icon-only" aria-label="Open navigation">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 7h16M4 12h16M4 17h16"></path>
                    </svg>
                </summary>
                <div class="shell-mobile-panel">
                    <div class="shell-mobile-links">
                        @foreach ($navLinks as $link)
                            <a href="{{ $link['route'] }}" class="shell-nav-link {{ $link['active'] ? 'is-active' : '' }}">{{ $link['label'] }}</a>
                        @endforeach
                        @if ($user?->isAdminUser())
                            <a href="{{ route('admin.dashboard') }}" class="shell-nav-link {{ request()->routeIs('admin.*') ? 'is-active' : '' }}">Admin Dashboard</a>
                        @endif
                    </div>
                </div>
            </details>
        </div>
    </div>
</nav>
