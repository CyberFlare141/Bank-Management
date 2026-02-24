<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MARS Bank | Register</title>
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
                <p class="brand-sub">Open Your Premium Account</p>
            </header>

            <div class="switcher" aria-label="Authentication switch">
                <a href="{{ route('login') }}" class="switcher-link">Login</a>
                <span class="switcher-pill is-active">Register</span>
            </div>

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf

                <div class="field field-name">
                    <label for="name">Full Name</label>
                    <div class="input-shell">
                        <span class="field-icon" aria-hidden="true">U</span>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="field-error" />
                </div>

                <div class="field field-email">
                    <label for="email">Email</label>
                    <div class="input-shell">
                        <span class="field-icon" aria-hidden="true">@</span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="field-error" />
                </div>

                <div class="field field-password">
                    <label for="password">Password</label>
                    <div class="input-shell">
                        <span class="field-icon" aria-hidden="true">*</span>
                        <input id="password" type="password" name="password" required autocomplete="new-password">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="field-error" />
                </div>

                <div class="field field-password">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="input-shell">
                        <span class="field-icon" aria-hidden="true">*</span>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="field-error" />
                </div>

                <button type="submit" class="auth-btn">Create Account</button>
            </form>
        </section>
    </main>
</body>
</html>
