<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MARS Bank | Digital Banking</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
    <div class="page-transition" aria-hidden="true"></div>
    <div class="page-bg" aria-hidden="true"></div>
    <div class="page-grid" aria-hidden="true"></div>

    <div class="site">
        <header class="container topbar anim-base anim-d1">
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

            <nav class="auth-nav" aria-label="Authentication actions">
                @auth
                    <details class="profile-menu">
                        <summary class="btn btn-secondary profile-trigger">{{ Auth::user()->name }}</summary>
                        <div class="profile-dropdown">
                            <a href="{{ route('profile.edit') }}" class="dropdown-link">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-link logout-link">Logout</button>
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
            <section id="personal" class="container hero anim-base anim-d2">
                <div class="hero-content">
                    <p class="eyebrow">Future-ready banking platform</p>
                    <h1>Premium digital banking built for modern finance.</h1>
                    <p class="subtitle">
                        MARS delivers secure transactions, intelligent insights, and fast account control
                        in one unified platform for individuals and businesses.
                    </p>
                    <div class="hero-actions">
                        @auth
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary">Manage Profile</a>
                            <form method="POST" action="{{ route('logout') }}" class="inline-action">
                                @csrf
                                <button type="submit" class="btn btn-secondary">Secure Logout</button>
                            </form>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary">Open an Account</a>
                            <a href="{{ route('login') }}" class="btn btn-secondary">Sign In</a>
                        @endauth
                    </div>
                </div>

                <aside class="hero-panel">
                    <h2>Performance Snapshot</h2>
                    <div class="loading-line" aria-hidden="true"></div>
                    <div class="metric-list">
                        <article class="metric">
                            <p class="metric-label">Transfer Success Rate</p>
                            <p class="metric-value">99.98%</p>
                        </article>
                        <article class="metric">
                            <p class="metric-label">Average Transfer Time</p>
                            <p class="metric-value">2.4s</p>
                        </article>
                        <article class="metric">
                            <p class="metric-label">Protected Accounts</p>
                            <p class="metric-value">1.2M+</p>
                        </article>
                    </div>
                </aside>
            </section>

            <section id="business" class="container section anim-base anim-d3">
                <div class="section-head">
                    <h2>Banking Solutions</h2>
                    <p>Clean, reliable products designed for everyday and long-term financial goals.</p>
                </div>

                <div class="card-grid">
                    <article id="cards" class="card">
                        <h3>Smart Savings</h3>
                        <p>High-yield savings with adaptive interest and instant liquidity controls.</p>
                        <a href="#">Explore</a>
                    </article>
                    <article class="card">
                        <h3>Credit Elite</h3>
                        <p>Premium card program with rewards, security controls, and travel benefits.</p>
                        <a href="#">Explore</a>
                    </article>
                    <article id="loans" class="card">
                        <h3>Home Finance</h3>
                        <p>Transparent mortgage process with fast pre-approval and flexible tenures.</p>
                        <a href="{{ auth()->check() ? route('personal.loan') : route('login') }}">Explore</a>
                    </article>
                </div>
            </section>

            <section id="insights" class="container section split anim-base anim-d4">
                <article class="panel">
                    <h3>Why MARS</h3>
                    <ul>
                        <li>Enterprise-grade security and fraud monitoring</li>
                        <li>Intuitive controls for cards, transfers, and limits</li>
                        <li>24/7 digital support with real-time notifications</li>
                    </ul>
                </article>
                <article class="panel highlight">
                    <p class="eyebrow">New</p>
                    <h3>MARS Neo App</h3>
                    <p>Track spending, set budgets, and lock cards instantly from mobile.</p>
                    <a href="#">Get Early Access</a>
                </article>
            </section>
        </main>

        <footer class="container footer anim-base anim-d5">
            <div>
                <p class="footer-brand">MARS Bank</p>
                <p class="footer-text">Premium digital banking experiences, built for confidence.</p>
            </div>
            <div class="footer-links">
                <a href="#">Privacy</a>
                <a href="#">Security</a>
                <a href="#">Support</a>
            </div>
        </footer>
    </div>
</body>
</html>
