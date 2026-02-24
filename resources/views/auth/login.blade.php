<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MARS Bank | Login</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-page">
    <div class="bg-orb orb-a" aria-hidden="true"></div>
    <div class="bg-orb orb-b" aria-hidden="true"></div>

    <main class="auth-wrap">
        <section class="auth-card auth-enter">
            <header class="auth-header">
                <a href="/" class="brand" aria-label="MARS Home">
                    <span class="brand-mark">M</span>
                    <span class="brand-text">MARS</span>
                </a>
                <p class="brand-sub">Secure Digital Banking</p>
            </header>

            <div class="switcher" aria-label="Authentication switch">
                <span class="switcher-pill is-active">Login</span>
                <a href="{{ route('register') }}" class="switcher-link">Register</a>
            </div>

            <x-auth-session-status class="status-message" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf

                <div class="field field-email">
                    <label for="email">Email</label>
                    <div class="input-shell">
                        <span class="field-icon" aria-hidden="true">@</span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="field-error" />
                </div>

                <div class="field field-password">
                    <label for="password">Password</label>
                    <div class="input-shell">
                        <span class="field-icon" aria-hidden="true">*</span>
                        <input id="password" type="password" name="password" required autocomplete="current-password">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="field-error" />
                </div>

                <div class="meta-row">
                    <label for="remember_me" class="checkbox-wrap">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="meta-link" href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="auth-btn">Log In</button>
            </form>
        </section>
    </main>
</body>
</html>
