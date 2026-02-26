<x-app-layout>
    <x-slot name="header"></x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        .mars-profile {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f1623;
            min-height: 100vh;
            color: #e2e8f0;
            position: relative;
            overflow-x: hidden;
        }

        /* Subtle bg grid like MARS homepage */
        .mars-profile::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(56, 189, 248, 0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(56, 189, 248, 0.015) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        /* Glow blobs matching MARS site */
        .bg-glow-1 {
            position: fixed;
            top: -200px;
            left: 50%;
            transform: translateX(-50%);
            width: 900px;
            height: 500px;
            background: radial-gradient(ellipse, rgba(56, 189, 248, 0.07) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .bg-glow-2 {
            position: fixed;
            bottom: -100px;
            right: -200px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.05) 0%, transparent 65%);
            pointer-events: none;
            z-index: 0;
        }

        .profile-wrap {
            position: relative;
            z-index: 1;
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px 80px;
        }

        .page-eyebrow {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 500;
            color: #38bdf8;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .page-eyebrow::before {
            content: '';
            display: inline-block;
            width: 20px;
            height: 1px;
            background: #38bdf8;
        }

        .page-title {
            font-size: 30px;
            font-weight: 800;
            color: #f1f5f9;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .page-subtitle {
            font-size: 14px;
            color: #4a5568;
            margin-bottom: 36px;
        }

        /* MARS-style card */
        .mars-card {
            background: #161d2e;
            border: 1px solid #1e2d45;
            border-radius: 12px;
            padding: 28px 32px;
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
            transition: border-color 0.25s;
        }

        .mars-card:hover { border-color: #2a3f5f; }

        .mars-card.accented::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, #38bdf8, #22d3ee, transparent);
        }

        /* Identity header */
        .identity-card {
            background: linear-gradient(135deg, #161d2e 0%, #1a2540 100%);
            border: 1px solid #1e2d45;
            border-radius: 12px;
            padding: 28px 32px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            position: relative;
            overflow: hidden;
        }

        .identity-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, #38bdf8, #22d3ee 40%, transparent);
        }

        .identity-left { display: flex; align-items: center; gap: 18px; }

        .id-avatar {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, #38bdf8, #0ea5e9);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 800; color: #0f1623;
            flex-shrink: 0;
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.25);
        }

        .id-info h2 { font-size: 18px; font-weight: 700; color: #f1f5f9; }
        .id-info p { font-size: 13px; color: #4a5568; font-family: 'JetBrains Mono', monospace; margin-top: 3px; }

        .id-badges { display: flex; gap: 8px; flex-wrap: wrap; }

        .id-badge {
            background: rgba(56, 189, 248, 0.08);
            border: 1px solid rgba(56, 189, 248, 0.18);
            color: #38bdf8;
            font-size: 11px; font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
            padding: 5px 12px; border-radius: 6px; letter-spacing: 0.5px;
        }

        .id-badge.green {
            background: rgba(34, 197, 94, 0.08);
            border-color: rgba(34, 197, 94, 0.18);
            color: #4ade80;
        }

        /* Stats strip */
        .stats-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: #1e2d45;
            border: 1px solid #1e2d45;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        @media (max-width: 640px) { .stats-strip { grid-template-columns: repeat(2, 1fr); } }

        .stat-cell { background: #161d2e; padding: 16px 20px; }
        .stat-cell-label { font-size: 10px; font-family: 'JetBrains Mono', monospace; color: #2d3f55; text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 5px; }
        .stat-cell-value { font-size: 15px; font-weight: 700; color: #94a3b8; }
        .stat-cell-value.teal { color: #38bdf8; }
        .stat-cell-value.green { color: #4ade80; }

        /* Card section head */
        .card-section-head {
            display: flex; align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px; gap: 12px;
        }

        .card-section-head h3 { font-size: 15px; font-weight: 700; color: #cbd5e0; }
        .card-section-head p { font-size: 12px; color: #374151; margin-top: 3px; }

        .section-icon {
            width: 36px; height: 36px;
            background: rgba(56, 189, 248, 0.08);
            border: 1px solid rgba(56, 189, 248, 0.15);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }

        .section-icon svg { width: 16px; height: 16px; color: #38bdf8; }
        .section-icon.red { background: rgba(248, 113, 113, 0.08); border-color: rgba(248, 113, 113, 0.15); }
        .section-icon.red svg { color: #f87171; }

        /* Fields */
        .field-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media (max-width: 580px) { .field-grid-2 { grid-template-columns: 1fr; } }

        .field-wrap { margin-bottom: 14px; }
        .field-wrap:last-child { margin-bottom: 0; }

        .field-label {
            display: block; font-size: 11px; font-weight: 600;
            color: #374151; text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 7px; font-family: 'JetBrains Mono', monospace;
        }

        .field-input {
            width: 100%; background: #0f1623;
            border: 1px solid #1e2d45; border-radius: 8px;
            padding: 11px 14px; color: #cbd5e0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px; font-weight: 500;
            transition: all 0.2s; outline: none;
        }

        .field-input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.08);
        }

        .field-input::placeholder { color: #1e2d45; }

        .field-input[type="password"] {
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 3px;
        }

        .field-error { font-size: 12px; color: #f87171; margin-top: 5px; font-family: 'JetBrains Mono', monospace; }

        /* Notices */
        .notice-warn {
            display: flex; align-items: center; gap: 10px;
            background: rgba(251, 191, 36, 0.05);
            border: 1px solid rgba(251, 191, 36, 0.12);
            border-radius: 8px; padding: 10px 14px; margin-top: 8px;
            font-size: 13px; color: #92702a;
        }

        .notice-warn svg { width: 14px; height: 14px; color: #fbbf24; flex-shrink: 0; }

        .notice-warn button {
            color: #fbbf24; background: none; border: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px; font-weight: 600; cursor: pointer; padding: 0;
        }

        .notice-success {
            display: flex; align-items: center; gap: 8px;
            background: rgba(56, 189, 248, 0.06);
            border: 1px solid rgba(56, 189, 248, 0.14);
            border-radius: 8px; padding: 10px 14px;
            font-size: 12px; color: #38bdf8;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Action row */
        .action-row { display: flex; align-items: center; gap: 12px; margin-top: 24px; flex-wrap: wrap; }

        /* Buttons — MARS style */
        .btn-mars-primary {
            background: #38bdf8; color: #0f1623;
            border: none; border-radius: 8px; padding: 11px 24px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px; font-weight: 700; cursor: pointer;
            transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 7px;
        }

        .btn-mars-primary:hover {
            background: #7dd3fc;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(56, 189, 248, 0.25);
        }

        .btn-mars-primary svg { width: 15px; height: 15px; }

        .btn-mars-ghost {
            background: transparent; color: #4a5568;
            border: 1px solid #1e2d45; border-radius: 8px; padding: 11px 20px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;
        }

        .btn-mars-ghost:hover { border-color: #2a3f5f; color: #718096; }

        .btn-mars-danger {
            background: transparent; color: #f87171;
            border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 8px; padding: 11px 24px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 7px;
        }

        .btn-mars-danger:hover {
            background: rgba(248, 113, 113, 0.06);
            border-color: rgba(248, 113, 113, 0.4);
            box-shadow: 0 0 16px rgba(248, 113, 113, 0.08);
        }

        .btn-mars-danger svg { width: 15px; height: 15px; }

        /* Danger card */
        .mars-card.danger { border-color: rgba(248, 113, 113, 0.1); }

        .mars-card.danger::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, #f87171, #fca5a5 40%, transparent);
        }

        .danger-description { font-size: 13px; color: #374151; line-height: 1.75; margin-bottom: 22px; }

        /* Modal */
        .mars-modal-bg {
            position: fixed; inset: 0;
            background: rgba(8, 12, 20, 0.85);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none; transition: opacity 0.25s;
        }

        .mars-modal-bg.open { opacity: 1; pointer-events: all; }

        .mars-modal {
            background: #161d2e; border: 1px solid #1e2d45;
            border-radius: 14px; padding: 36px;
            max-width: 440px; width: 92%;
            position: relative; overflow: hidden;
            transform: translateY(16px); transition: transform 0.25s;
        }

        .mars-modal-bg.open .mars-modal { transform: translateY(0); }

        .mars-modal::after {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, #f87171, #fca5a5 40%, transparent);
        }

        .modal-danger-icon {
            width: 44px; height: 44px;
            background: rgba(248, 113, 113, 0.08);
            border: 1px solid rgba(248, 113, 113, 0.18);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center; margin-bottom: 18px;
        }

        .modal-danger-icon svg { width: 20px; height: 20px; color: #f87171; }

        .modal-title { font-size: 18px; font-weight: 800; color: #f1f5f9; margin-bottom: 8px; }
        .modal-body { font-size: 13px; color: #374151; line-height: 1.7; margin-bottom: 22px; }
        .modal-footer { display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; }

        /* Animations */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .identity-card { animation: fadeUp 0.4s ease both; }
        .stats-strip    { animation: fadeUp 0.4s ease 0.07s both; }
        .section-info   { animation: fadeUp 0.4s ease 0.12s both; }
        .section-pass   { animation: fadeUp 0.4s ease 0.17s both; }
        .section-danger { animation: fadeUp 0.4s ease 0.22s both; }
    </style>

    <div class="mars-profile">
        <div class="bg-glow-1"></div>
        <div class="bg-glow-2"></div>

        <div class="profile-wrap">

            <p class="page-eyebrow">Account Management</p>
            <h1 class="page-title">Your Profile</h1>
            <p class="page-subtitle">Manage your personal details, security credentials, and account settings.</p>

            {{-- Identity Card --}}
            <div class="identity-card">
                <div class="identity-left">
                    <div class="id-avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                    <div class="id-info">
                        <h2>{{ $user->name }}</h2>
                        <p>{{ $user->email }}</p>
                    </div>
                </div>
                <div class="id-badges">
                    <span class="id-badge green">● Active</span>
                    <span class="id-badge">Premium</span>
                    <span class="id-badge">2FA On</span>
                </div>
            </div>

            {{-- Stats Strip --}}
            <div class="stats-strip">
                <div class="stat-cell">
                    <div class="stat-cell-label">Account Type</div>
                    <div class="stat-cell-value">Smart Savings</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-cell-label">Member Since</div>
                    <div class="stat-cell-value">Jan 2021</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-cell-label">Security Score</div>
                    <div class="stat-cell-value teal">92 / 100</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-cell-label">Status</div>
                    <div class="stat-cell-value green">Verified</div>
                </div>
            </div>

            {{-- Profile Information --}}
            <div class="mars-card accented section-info">
                <div class="card-section-head">
                    <div>
                        <h3>Personal Information</h3>
                        <p>Update your name, email and contact details</p>
                    </div>
                    <div class="section-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                    </div>
                </div>

                <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <div class="field-grid-2">
                        <div class="field-wrap">
                            <label class="field-label" for="name">Full Name</label>
                            <input id="name" name="name" type="text" class="field-input"
                                value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                                placeholder="Your full name" />
                            @error('name')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="field-wrap">
                            <label class="field-label" for="phone_number">Phone Number</label>
                            <input id="phone_number" name="phone_number" type="text" class="field-input"
                                value="{{ old('phone_number', $user->customer?->C_PhoneNumber) }}"
                                autocomplete="tel" placeholder="+880 1XX XXXXXXX" />
                            @error('phone_number')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="field-wrap" style="margin-top:14px;">
                        <label class="field-label" for="email">Email Address</label>
                        <input id="email" name="email" type="email" class="field-input"
                            value="{{ old('email', $user->email) }}" required autocomplete="username"
                            placeholder="you@example.com" />
                        @error('email')<p class="field-error">{{ $message }}</p>@enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="notice-warn">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l6.255-10.876c.866-1.5 3.032-1.5 3.898 0l5.453 9.5zM12 15.75h.008v.008H12v-.008z"/>
                                </svg>
                                Email unverified —
                                <button form="send-verification">Resend link →</button>
                            </div>
                            @if (session('status') === 'verification-link-sent')
                                <div class="notice-success" style="margin-top:8px;">✓ Verification link sent</div>
                            @endif
                        @endif
                    </div>

                    <div class="action-row">
                        <button type="submit" class="btn-mars-primary">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Save Changes
                        </button>
                        @if (session('status') === 'profile-updated')
                            <div class="notice-success" x-data="{show:true}" x-show="show" x-transition x-init="setTimeout(()=>show=false,2500)">
                                ✓ Saved
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Update Password --}}
            <div class="mars-card accented section-pass">
                <div class="card-section-head">
                    <div>
                        <h3>Security & Password</h3>
                        <p>Use a long, unique password to protect your account</p>
                    </div>
                    <div class="section-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                    </div>
                </div>

                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div class="field-wrap">
                        <label class="field-label" for="current_password">Current Password</label>
                        <input id="current_password" name="current_password" type="password"
                            class="field-input" autocomplete="current-password" placeholder="••••••••••••" />
                        @error('current_password', 'updatePassword')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="field-grid-2" style="margin-top:14px;">
                        <div class="field-wrap">
                            <label class="field-label" for="new_password">New Password</label>
                            <input id="new_password" name="password" type="password"
                                class="field-input" autocomplete="new-password" placeholder="••••••••••••" />
                            @error('password', 'updatePassword')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="field-wrap">
                            <label class="field-label" for="password_confirmation">Confirm New Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                class="field-input" autocomplete="new-password" placeholder="••••••••••••" />
                            @error('password_confirmation', 'updatePassword')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="action-row">
                        <button type="submit" class="btn-mars-primary">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                            Update Password
                        </button>
                        @if (session('status') === 'password-updated')
                            <div class="notice-success" x-data="{show:true}" x-show="show" x-transition x-init="setTimeout(()=>show=false,2500)">
                                ✓ Password updated
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Delete Account --}}
            <div class="mars-card danger section-danger">
                <div class="card-section-head">
                    <div>
                        <h3>Close Account</h3>
                        <p>Permanently delete your MARS Bank account</p>
                    </div>
                    <div class="section-icon red">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </div>
                </div>

                <p class="danger-description">
                    Closing your account will permanently erase all data — including transaction history, saved payees, and documents.
                    All pending transfers will be cancelled. Please download your statements before proceeding.
                </p>

                <button class="btn-mars-danger" onclick="document.getElementById('deleteModal').classList.add('open')">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                    Close My Account
                </button>
            </div>

        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteModal" class="mars-modal-bg">
        <div class="mars-modal">
            <div class="modal-danger-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
            </div>

            <h2 class="modal-title">Close Your Account?</h2>
            <p class="modal-body">
                This will immediately and permanently delete your MARS Bank account and all associated financial data.
                This cannot be undone. Enter your password to confirm.
            </p>

            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="field-wrap">
                    <label class="field-label" for="del_password">Your Password</label>
                    <input id="del_password" name="password" type="password"
                        class="field-input" placeholder="Confirm with your password" />
                    @error('password', 'userDeletion')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-mars-ghost"
                        onclick="document.getElementById('deleteModal').classList.remove('open')">
                        Cancel
                    </button>
                    <button type="submit" class="btn-mars-danger">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Yes, Close Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('open');
        });

        @if ($errors->userDeletion->isNotEmpty())
            document.getElementById('deleteModal').classList.add('open');
        @endif
    </script>
</x-app-layout>