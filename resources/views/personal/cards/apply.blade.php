<x-app-layout>
    @php
        $isCredit = $cardType === 'credit';
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #02060f;
            --surface: rgba(255,255,255,0.03);
            --border: rgba(120,180,255,0.12);
            --border-hover: rgba(120,180,255,0.35);
            --accent: #3b82f6;
            --accent-glow: rgba(59,130,246,0.35);
            --accent-2: #06b6d4;
            --text: #e8f0ff;
            --muted: #5a7499;
            --label: #7ba3d0;
            --error: #f87171;
            --gold: #f5c542;
        }

        .caf-root {
            min-height: 100vh;
            background: var(--bg);
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            padding: 2.5rem 1.5rem 5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient background orbs */
        .caf-root::before, .caf-root::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
        .caf-root::before {
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(59,130,246,0.1) 0%, transparent 70%);
            top: -200px; right: -200px;
            animation: orb1 12s ease-in-out infinite alternate;
        }
        .caf-root::after {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(6,182,212,0.07) 0%, transparent 70%);
            bottom: -150px; left: -150px;
            animation: orb2 15s ease-in-out infinite alternate;
        }
        @keyframes orb1 { from { transform: translate(0,0) scale(1); } to { transform: translate(-60px, 80px) scale(1.15); } }
        @keyframes orb2 { from { transform: translate(0,0) scale(1); } to { transform: translate(80px, -60px) scale(1.2); } }

        .caf-wrap {
            max-width: 960px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* ── Header ── */
        .caf-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2.5rem;
            animation: slideDown 0.6s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes slideDown { from { opacity:0; transform:translateY(-20px); } to { opacity:1; transform:translateY(0); } }

        .caf-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.7rem;
            font-weight: 500;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--accent);
            background: rgba(59,130,246,0.1);
            border: 1px solid rgba(59,130,246,0.3);
            border-radius: 100px;
            padding: 0.3rem 0.75rem;
            margin-bottom: 0.65rem;
        }
        .caf-badge::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 8px var(--accent);
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:0.4; } }

        .caf-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.1;
            background: linear-gradient(135deg, #fff 30%, #7ab4ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .caf-subtitle {
            font-size: 0.84rem;
            color: var(--muted);
            margin-top: 0.4rem;
            line-height: 1.6;
        }

        .caf-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--label);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.55rem 1rem;
            transition: all 0.25s ease;
            white-space: nowrap;
            backdrop-filter: blur(8px);
        }
        .caf-back-btn:hover { color: var(--text); border-color: var(--border-hover); background: rgba(255,255,255,0.06); transform: translateY(-1px); }
        .caf-back-btn svg { width:14px; height:14px; }

        /* ── Progress strip ── */
        .caf-progress {
            display: flex;
            gap: 0;
            margin-bottom: 2rem;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border);
            animation: slideDown 0.6s 0.1s cubic-bezier(.22,1,.36,1) both;
        }
        .caf-step {
            flex: 1;
            padding: 0.65rem 1rem;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted);
            background: var(--surface);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-right: 1px solid var(--border);
            transition: all 0.3s;
        }
        .caf-step:last-child { border-right: none; }
        .caf-step.active { color: var(--accent); background: rgba(59,130,246,0.08); }
        .caf-step-num {
            width: 20px; height: 20px;
            border-radius: 50%;
            border: 1px solid currentColor;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.65rem;
            flex-shrink: 0;
        }
        .caf-step.active .caf-step-num { background: var(--accent); border-color: var(--accent); color: #fff; }

        /* ── Form panel ── */
        .caf-form {
            background: rgba(8,18,38,0.8);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            backdrop-filter: blur(20px);
            animation: fadeUp 0.7s 0.15s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes fadeUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }

        /* ── Section ── */
        .caf-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border);
        }
        .caf-section:last-of-type { border-bottom: none; margin-bottom: 0; }

        .caf-section-head {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }
        .caf-section-icon {
            width: 34px; height: 34px;
            border-radius: 10px;
            background: rgba(59,130,246,0.12);
            border: 1px solid rgba(59,130,246,0.25);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .caf-section-icon svg { width:16px; height:16px; color: var(--accent); }

        .caf-section-label {
            font-family: 'Syne', sans-serif;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--label);
        }

        /* ── Grid & fields ── */
        .caf-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .caf-field-wide { grid-column: 1 / -1; }

        .caf-field { display: flex; flex-direction: column; gap: 0.35rem; }
        .caf-field label {
            font-size: 0.71rem;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--label);
        }
        .caf-field input,
        .caf-field select,
        .caf-field textarea {
            width: 100%;
            background: rgba(2,6,15,0.6);
            border: 1px solid var(--border);
            border-radius: 11px;
            color: var(--text);
            padding: 0.7rem 0.9rem;
            font-size: 0.875rem;
            font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            -webkit-appearance: none;
        }
        .caf-field select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235a7499' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.85rem center;
            padding-right: 2.2rem;
            cursor: pointer;
        }
        .caf-field input::placeholder { color: #2d4060; }
        .caf-field textarea { min-height: 88px; resize: vertical; }
        .caf-field input:focus,
        .caf-field select:focus,
        .caf-field textarea:focus {
            border-color: rgba(59,130,246,0.6);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1), inset 0 1px 0 rgba(255,255,255,0.03);
            background: rgba(10,20,50,0.7);
        }
        .caf-field input:hover:not(:focus),
        .caf-field select:hover:not(:focus),
        .caf-field textarea:hover:not(:focus) {
            border-color: rgba(120,180,255,0.25);
        }

        .caf-err {
            font-size: 0.72rem;
            color: var(--error);
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        .caf-err::before { content:'⚠'; font-size:0.7rem; }

        /* ── Notice ── */
        .caf-notice {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            background: rgba(245,197,66,0.06);
            border: 1px solid rgba(245,197,66,0.2);
            border-radius: 11px;
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            color: var(--gold);
            margin-bottom: 1.5rem;
        }
        .caf-notice svg { width:16px; height:16px; flex-shrink:0; }

        /* ── Submit row ── */
        .caf-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }

        .caf-submit {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 0;
            color: #fff;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #0ea5e9 100%);
            border-radius: 12px;
            padding: 0.8rem 1.75rem;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 0.04em;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 24px rgba(37,99,235,0.35), 0 1px 0 rgba(255,255,255,0.15) inset;
        }
        .caf-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(37,99,235,0.5), 0 1px 0 rgba(255,255,255,0.15) inset;
        }
        .caf-submit:active { transform: translateY(0); }
        .caf-submit svg { width:15px; height:15px; transition: transform 0.2s; }
        .caf-submit:hover svg { transform: translateX(3px); }

        .caf-secure {
            font-size: 0.72rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        .caf-secure svg { width:12px; height:12px; }

        @media (max-width: 680px) {
            .caf-grid { grid-template-columns: 1fr; }
            .caf-field-wide { grid-column: 1; }
            .caf-header { flex-direction: column; gap: 1rem; }
            .caf-form { padding: 1.25rem; }
            .caf-progress { display: none; }
            .caf-actions { flex-direction: column-reverse; align-items: stretch; }
            .caf-submit { justify-content: center; }
        }
    </style>

    <div class="caf-root">
        <div class="caf-wrap">

            {{-- Header --}}
            <div class="caf-header">
                <div>
                    <div class="caf-badge">
                        {{ $isCredit ? 'Credit' : 'Debit' }} Card
                    </div>
                    <h1 class="caf-title">Card Application</h1>
                    <p class="caf-subtitle">Fill in the details below. Your application will be<br>set to <strong style="color:#7ab4ff">Pending Review</strong> upon submission.</p>
                </div>
                <a href="{{ route('personal.cards') }}" class="caf-back-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    Back to Cards
                </a>
            </div>

            {{-- Progress steps --}}
            <div class="caf-progress">
                <div class="caf-step active">
                    <span class="caf-step-num">1</span> Personal Info
                </div>
                <div class="caf-step active">
                    <span class="caf-step-num">2</span> Account
                </div>
                @if($isCredit)
                <div class="caf-step active">
                    <span class="caf-step-num">3</span> Employment
                </div>
                @endif
                <div class="caf-step active">
                    <span class="caf-step-num">{{ $isCredit ? 4 : 3 }}</span> Preferences
                </div>
            </div>

            <form method="POST" action="{{ route('personal.cards.store', ['cardType' => $cardType]) }}" class="caf-form">
                @csrf

                <div class="caf-notice">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    All mandatory fields are required before submission.
                </div>

                {{-- Personal Information --}}
                <section class="caf-section">
                    <div class="caf-section-head">
                        <div class="caf-section-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <span class="caf-section-label">Personal Information</span>
                    </div>
                    <div class="caf-grid">
                        <div class="caf-field">
                            <label for="full_name">Full Name</label>
                            <input id="full_name" name="full_name" type="text" placeholder="John Doe" value="{{ old('full_name', $customer->C_Name ?? $user->name) }}" required>
                            @error('full_name')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="caf-field">
                            <label for="date_of_birth">Date of Birth</label>
                            <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" required>
                            @error('date_of_birth')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="caf-field">
                            <label for="national_id_passport">National ID / Passport</label>
                            <input id="national_id_passport" name="national_id_passport" type="text" placeholder="ID or Passport number" value="{{ old('national_id_passport') }}" required>
                            @error('national_id_passport')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="caf-field">
                            <label for="contact_number">Contact Number</label>
                            <input id="contact_number" name="contact_number" type="text" placeholder="+1 (555) 000-0000" value="{{ old('contact_number', $customer->C_PhoneNumber ?? '') }}" required>
                            @error('contact_number')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="caf-field">
                            <label for="email_address">Email Address</label>
                            <input id="email_address" name="email_address" type="email" placeholder="you@example.com" value="{{ old('email_address', $user->email) }}" required>
                            @error('email_address')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="caf-field">
                            <label for="residential_address">Residential Address</label>
                            <textarea id="residential_address" name="residential_address" placeholder="Street, City, Country" required>{{ old('residential_address', $customer->C_Address ?? '') }}</textarea>
                            @error('residential_address')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </section>

                {{-- Account Information --}}
                <section class="caf-section">
                    <div class="caf-section-head">
                        <div class="caf-section-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="5" width="20" height="14" rx="3"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        </div>
                        <span class="caf-section-label">Account Information</span>
                    </div>
                    <div class="caf-grid">
                        <div class="caf-field">
                            <label for="existing_account_number">Existing Account Number <span style="color:var(--muted);font-size:0.65rem;">(optional)</span></label>
                            <input id="existing_account_number" name="existing_account_number" type="number" placeholder="If applicable" value="{{ old('existing_account_number', $account->A_Number ?? '') }}">
                            @error('existing_account_number')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="caf-field">
                            <label for="account_type">Account Type</label>
                            <input id="account_type" name="account_type" type="text" placeholder="e.g. Personal" value="{{ old('account_type', $account->account_type ?? 'Personal') }}" required>
                            @error('account_type')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="caf-field caf-field-wide">
                            <label for="branch_id">Branch Name</label>
                            <select id="branch_id" name="branch_id" required>
                                <option value="">Select a branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->B_ID }}" @selected((string) old('branch_id') === (string) $branch->B_ID)>{{ $branch->B_Name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </section>

                {{-- Employment (credit only) --}}
                @if ($isCredit)
                <section class="caf-section">
                    <div class="caf-section-head">
                        <div class="caf-section-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="12"/></svg>
                        </div>
                        <span class="caf-section-label">Employment & Financial Information</span>
                    </div>
                    <div class="caf-grid">
                        <div class="caf-field">
                            <label for="occupation">Occupation</label>
                            <input id="occupation" name="occupation" type="text" placeholder="e.g. Software Engineer" value="{{ old('occupation') }}" required>
                            @error('occupation')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="caf-field">
                            <label for="employer_name">Employer Name</label>
                            <input id="employer_name" name="employer_name" type="text" placeholder="Company name" value="{{ old('employer_name') }}" required>
                            @error('employer_name')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="caf-field">
                            <label for="monthly_income">Monthly Income</label>
                            <input id="monthly_income" name="monthly_income" type="number" min="0" step="0.01" placeholder="0.00" value="{{ old('monthly_income') }}" required>
                            @error('monthly_income')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="caf-field">
                            <label for="source_of_income">Source of Income</label>
                            <input id="source_of_income" name="source_of_income" type="text" placeholder="e.g. Salary, Freelance" value="{{ old('source_of_income') }}" required>
                            @error('source_of_income')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </section>
                @endif

                {{-- Card Preferences --}}
                <section class="caf-section">
                    <div class="caf-section-head">
                        <div class="caf-section-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>
                        <span class="caf-section-label">Card Preferences</span>
                    </div>
                    <div class="caf-grid">
                        <div class="caf-field">
                            <label for="card_network">Card Network</label>
                            <select id="card_network" name="card_network" required>
                                <option value="">Select network</option>
                                @foreach (['Visa', 'MasterCard', 'American Express', 'Discover'] as $network)
                                    <option value="{{ $network }}" @selected(old('card_network') === $network)>{{ $network }}</option>
                                @endforeach
                            </select>
                            @error('card_network')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="caf-field">
                            <label for="card_design">Card Design</label>
                            <select id="card_design" name="card_design" required>
                                <option value="">Select design</option>
                                @foreach (['Classic Blue', 'Midnight Black', 'Aurora Silver'] as $design)
                                    <option value="{{ $design }}" @selected(old('card_design') === $design)>{{ $design }}</option>
                                @endforeach
                            </select>
                            @error('card_design')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                        <div class="caf-field caf-field-wide">
                            <label for="delivery_method">Delivery Method</label>
                            <select id="delivery_method" name="delivery_method" required>
                                <option value="">Select delivery method</option>
                                <option value="home_delivery" @selected(old('delivery_method') === 'home_delivery')>🚚 Home Delivery</option>
                                <option value="branch_pickup" @selected(old('delivery_method') === 'branch_pickup')>🏦 Branch Pickup</option>
                            </select>
                            @error('delivery_method')<div class="caf-err">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </section>

                {{-- Actions --}}
                <div class="caf-actions">
                    <span class="caf-secure">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        256-bit SSL encrypted
                    </span>
                    <button type="submit" class="caf-submit">
                        Submit Application
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>