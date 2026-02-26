<x-app-layout>
    @php
        $balance = (float) ($account->A_Balance ?? 0);
        $totalLoanTaken = (float) ($loanSummary['total_loan_taken'] ?? 0);
        $totalRepaid = (float) ($loanSummary['total_repaid'] ?? 0);
        $loanOutstanding = (float) ($loanSummary['remaining_loan_balance'] ?? 0);
        $availableMoney = (float) ($loanSummary['available_money'] ?? ($balance - $loanOutstanding));
        $creditAvailable = (float) ($creditCard->available_credit ?? 0);
        $creditLimit = (float) ($creditCard->credit_limit ?? 0);
        $lastLogin = optional($user->updated_at)->format('F d, Y \a\t h:i A') ?? now()->format('F d, Y \a\t h:i A');
    @endphp

    <style>
        :root {
            --pd-bg-main: #05080f;
            --pd-bg-soft: #101a2c;
            --pd-panel: rgba(14, 21, 36, 0.82);
            --pd-line: rgba(148, 163, 184, 0.24);
            --pd-text: #f8fafc;
            --pd-muted: #9aa9c2;
            --pd-blue: #0f4f89;
            --pd-blue-soft: #1b6db0;
            --pd-gold: #b7945f;
            --pd-gold-soft: #d3b180;
            --pd-success: #6ee7b7;
            --pd-danger: #fda4af;
            --pd-radius: 18px;
            --pd-shadow: 0 22px 44px rgba(2, 8, 23, 0.55);
        }

        .pd-root {
            min-height: calc(100vh - 64px);
            position: relative;
            overflow: hidden;
            color: var(--pd-text);
            background:
                radial-gradient(circle at 10% 8%, rgba(56, 189, 248, 0.2), transparent 30%),
                radial-gradient(circle at 90% 14%, rgba(183, 148, 95, 0.16), transparent 32%),
                linear-gradient(150deg, var(--pd-bg-main), #0d1527 52%, var(--pd-bg-soft));
            background-size: 140% 140%;
            animation: pd-gradient-drift 14s ease-in-out infinite;
        }

        .pd-root::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(148, 163, 184, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, 0.06) 1px, transparent 1px);
            background-size: 46px 46px;
            mask-image: radial-gradient(circle at center, black 30%, transparent 82%);
            pointer-events: none;
        }

        .pd-enter {
            opacity: 0;
            transform: translateY(14px) scale(0.99);
            animation: pd-section-enter 0.68s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        .pd-d1 { animation-delay: 0.08s; }
        .pd-d2 { animation-delay: 0.16s; }
        .pd-d3 { animation-delay: 0.24s; }
        .pd-d4 { animation-delay: 0.31s; }

        .pd-panel {
            border: 1px solid var(--pd-line);
            border-radius: var(--pd-radius);
            background: var(--pd-panel);
            box-shadow: var(--pd-shadow);
            backdrop-filter: blur(8px);
        }

        .pd-welcome {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.05rem 1.2rem;
        }

        .pd-user {
            display: flex;
            align-items: center;
            gap: 0.95rem;
        }

        .pd-avatar {
            width: 68px;
            height: 68px;
            border-radius: 999px;
            border: 3px solid var(--pd-gold-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e9d7bf;
            background: radial-gradient(circle at 30% 30%, rgba(211, 177, 128, 0.35), rgba(183, 148, 95, 0.18));
            box-shadow: 0 10px 24px rgba(183, 148, 95, 0.25);
            font-size: 1.35rem;
            font-weight: 700;
        }

        .pd-welcome h1 {
            margin: 0;
            color: #d7b98e;
            font-size: 1.75rem;
            line-height: 1.1;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .pd-sub {
            margin: 0.22rem 0 0;
            color: var(--pd-muted);
            font-size: 0.92rem;
        }

        .pd-bell {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            border: 1px solid var(--pd-line);
            background: rgba(15, 23, 42, 0.65);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #f0d3a6;
            transition: transform 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
        }

        .pd-bell:hover {
            transform: translateY(-2px);
            border-color: rgba(211, 177, 128, 0.55);
            background: rgba(183, 148, 95, 0.14);
        }

        .pd-toggle {
            margin-top: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.95rem 1.05rem;
        }

        .pd-toggle-left {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            color: #dce8f8;
            font-weight: 600;
        }

        .pd-dot {
            width: 40px;
            height: 40px;
            border-radius: 999px;
            background: linear-gradient(135deg, #0f4f89, #2d82c5);
            box-shadow: 0 10px 24px rgba(33, 114, 176, 0.35);
        }

        .pd-switch {
            width: 72px;
            height: 38px;
            border-radius: 999px;
            border: 1px solid rgba(183, 148, 95, 0.35);
            background: rgba(183, 148, 95, 0.22);
            padding: 4px;
            position: relative;
            overflow: hidden;
        }

        .pd-switch::after {
            content: "";
            width: 28px;
            height: 28px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--pd-gold-soft), var(--pd-gold));
            display: block;
            margin-left: auto;
            box-shadow: 0 8px 16px rgba(183, 148, 95, 0.4);
            animation: pd-switch-pop 0.5s ease;
        }

        .pd-tabs {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.5rem;
            padding: 0.45rem;
            border-radius: 14px;
            border: 1px solid var(--pd-line);
            background: rgba(15, 23, 42, 0.55);
        }

        .pd-tab {
            text-align: center;
            border-radius: 12px;
            padding: 0.58rem 0.4rem;
            color: #d2e4fb;
            font-weight: 600;
            font-size: 0.94rem;
            transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
        }

        .pd-tab.active {
            background: linear-gradient(135deg, var(--pd-gold), var(--pd-gold-soft));
            color: #fff8ef;
            box-shadow: 0 8px 18px rgba(183, 148, 95, 0.34);
        }

        .pd-tab:hover {
            transform: translateY(-1px);
            background-color: rgba(56, 189, 248, 0.12);
        }

        .pd-account-card {
            margin-top: 1rem;
            position: relative;
            border-radius: 24px;
            padding: 1.3rem 1.2rem 1.05rem;
            border: 1px solid rgba(211, 177, 128, 0.42);
            background:
                linear-gradient(120deg, rgba(183, 148, 95, 0.9), rgba(183, 148, 95, 0.76)),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.2), transparent 50%);
            box-shadow: 0 22px 40px rgba(55, 38, 19, 0.34);
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .pd-account-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 28px 48px rgba(55, 38, 19, 0.44);
        }

        .pd-account-card::after {
            content: "";
            position: absolute;
            top: -30%;
            right: -10%;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.22), transparent 70%);
        }

        .pd-card-top,
        .pd-card-mid,
        .pd-card-bottom {
            position: relative;
            z-index: 1;
        }

        .pd-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #fff8ef;
            font-size: 0.92rem;
            font-weight: 600;
        }

        .pd-card-mid {
            margin-top: 0.7rem;
            padding-top: 0.7rem;
            border-top: 1px solid rgba(255, 255, 255, 0.55);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
        }

        .pd-card-mid p {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 600;
            color: #fffdf8;
            letter-spacing: 0.03em;
        }

        .pd-brand {
            text-align: right;
            line-height: 1.15;
            color: #fff8ef;
            font-weight: 700;
            font-size: 0.94rem;
        }

        .pd-card-bottom {
            margin-top: 2.2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .pd-balance-btn {
            border: 0;
            color: #fff8ef;
            background: transparent;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            cursor: pointer;
            padding: 0;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }

        .pd-balance-btn:hover {
            transform: translateX(2px);
            opacity: 0.9;
        }

        .pd-chevron {
            font-size: 1.8rem;
            line-height: 1;
            color: #fef1df;
        }

        .pd-shimmer {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .pd-shimmer::after {
            content: "";
            position: absolute;
            top: -160%;
            left: -55%;
            width: 40%;
            height: 340%;
            transform: rotate(18deg);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.16), transparent);
            animation: pd-shimmer 2.7s ease-in-out infinite;
        }

        .pd-grid {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.8rem;
        }

        .pd-action {
            padding: 0.95rem 0.6rem;
            text-align: center;
            border-radius: 14px;
            border: 1px solid var(--pd-line);
            background: rgba(12, 20, 34, 0.76);
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .pd-action:hover {
            transform: translateY(-3px);
            border-color: rgba(56, 189, 248, 0.42);
            box-shadow: 0 16px 30px rgba(2, 8, 23, 0.48);
        }

        .pd-action svg {
            width: 36px;
            height: 36px;
            margin: 0 auto;
            color: var(--pd-gold-soft);
        }

        .pd-action p {
            margin: 0.55rem 0 0;
            color: #d8e8fc;
            font-weight: 600;
            font-size: 0.92rem;
        }

        .pd-sections {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.9rem;
        }

        .pd-card {
            padding: 1rem;
        }

        .pd-card h3 {
            margin: 0;
            font-size: 1rem;
            color: #dceafc;
        }

        .pd-meta {
            margin-top: 0.72rem;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.6rem;
        }

        .pd-meta-item {
            padding: 0.68rem;
            border-radius: 12px;
            border: 1px solid var(--pd-line);
            background: rgba(7, 15, 28, 0.52);
        }

        .pd-meta-item p {
            margin: 0;
        }

        .pd-meta-item .k {
            color: var(--pd-muted);
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .pd-meta-item .v {
            margin-top: 0.28rem;
            font-weight: 600;
            color: #e5efff;
            font-size: 0.9rem;
        }

        .pd-list {
            margin-top: 0.72rem;
            display: grid;
            gap: 0.55rem;
        }

        .pd-list-item {
            padding: 0.7rem;
            border-radius: 12px;
            border: 1px solid var(--pd-line);
            background: rgba(7, 15, 28, 0.52);
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .pd-list-item:hover {
            transform: translateX(2px);
            border-color: rgba(56, 189, 248, 0.4);
        }

        .pd-list-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.6rem;
            font-size: 0.84rem;
        }

        .pd-list-row + .pd-list-row {
            margin-top: 0.32rem;
        }

        .pd-list-row .label {
            color: var(--pd-muted);
        }

        .pd-list-row .value {
            color: #e5efff;
            font-weight: 600;
            text-align: right;
        }

        .pd-status {
            display: inline-flex;
            border-radius: 999px;
            padding: 0.18rem 0.55rem;
            font-size: 0.67rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border: 1px solid rgba(110, 231, 183, 0.4);
            color: var(--pd-success);
            background: rgba(110, 231, 183, 0.12);
            font-weight: 700;
        }

        .pd-table-wrap {
            margin-top: 0.72rem;
            overflow-x: auto;
        }

        .pd-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.84rem;
        }

        .pd-table th,
        .pd-table td {
            padding: 0.56rem 0.42rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        }

        .pd-table th {
            color: var(--pd-muted);
            font-weight: 600;
            text-align: left;
        }

        .pd-table tr {
            transition: background-color 0.2s ease;
        }

        .pd-table tr:hover {
            background: rgba(17, 31, 56, 0.6);
        }

        .pd-empty {
            margin-top: 0.6rem;
            color: var(--pd-muted);
            font-size: 0.86rem;
        }

        @keyframes pd-section-enter {
            from {
                opacity: 0;
                transform: translateY(16px) scale(0.99);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes pd-gradient-drift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes pd-shimmer {
            0% { left: -55%; }
            100% { left: 120%; }
        }

        @keyframes pd-switch-pop {
            0% { transform: scale(0.86); }
            100% { transform: scale(1); }
        }

        @media (min-width: 1024px) {
            .pd-sections {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .pd-sections .pd-card:last-child {
                grid-column: span 2;
            }
        }

        @media (max-width: 768px) {
            .pd-welcome {
                padding: 0.9rem;
            }

            .pd-avatar {
                width: 56px;
                height: 56px;
                font-size: 1.12rem;
            }

            .pd-welcome h1 {
                font-size: 1.28rem;
            }

            .pd-tabs {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .pd-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .pd-meta {
                grid-template-columns: 1fr;
            }

            .pd-table {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 520px) {
            .pd-grid {
                grid-template-columns: 1fr;
            }

            .pd-card-mid {
                flex-direction: column;
            }

            .pd-brand {
                text-align: left;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>

    <div class="pd-root py-6 sm:py-8">
        <div class="relative z-10 mx-auto w-[min(1140px,92%)]">
            <section class="pd-panel pd-welcome pd-enter pd-d1">
                <div class="pd-user">
                    <div class="pd-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div>
                        <p class="text-sm text-[#d7b98e] mb-0">Welcome</p>
                        <h1>{{ $user->name }}</h1>
                        <p class="pd-sub">Last Login On {{ $lastLogin }}</p>
                    </div>
                </div>
                <button type="button" class="pd-bell" aria-label="Notifications">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M14.5 18a2.5 2.5 0 0 1-5 0"></path>
                        <path d="M18 16V11a6 6 0 1 0-12 0v5l-2 2h16l-2-2z"></path>
                    </svg>
                </button>
            </section>

            <section class="pd-panel pd-toggle pd-enter pd-d2">
                <div class="pd-toggle-left">
                    <span class="pd-dot" aria-hidden="true"></span>
                    <div>
                        <p class="mb-0 text-base">Qibla Pointer</p>
                        <p class="mb-0 text-xs text-slate-400">Islamic mode enabled</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-lg text-[#d8e8fc]">Islamic</span>
                    <span class="pd-switch" aria-hidden="true"></span>
                </div>
            </section>

            <section class="pd-tabs pd-enter pd-d2">
                <div class="pd-tab active">Accounts</div>
                <div class="pd-tab">Cards</div>
                <div class="pd-tab">Deposits</div>
                <div class="pd-tab">Investment</div>
            </section>

            <section
                class="pd-account-card pd-enter pd-d3"
                x-data="{ revealBalance: false }"
            >
                <div class="pd-shimmer"></div>
                <div class="pd-card-top">
                    <span>{{ $account?->account_type ?? 'Prime Hasanah Youth' }}</span>
                    <span>{{ $account?->A_Number ?? '0000000000000' }}</span>
                </div>

                <div class="pd-card-mid">
                    <p>{{ strtoupper($user->name) }}</p>
                    <div class="pd-brand">
                        <div>HASANAH</div>
                        <div class="text-xs font-medium">ISLAMI BANKING</div>
                    </div>
                </div>

                <div class="pd-card-bottom">
                    <button class="pd-balance-btn" @click="revealBalance = !revealBalance" type="button">
                        <span x-show="!revealBalance">Tap for Balance</span>
                        <span x-show="revealBalance" x-transition>
                            {{ '$' . number_format($balance, 2) }}
                        </span>
                    </button>
                    <span class="pd-chevron">&rsaquo;</span>
                </div>
            </section>

            <section class="pd-grid pd-enter pd-d3">
                <article class="pd-action">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M7 7h10v10"></path><path d="m7 17 10-10"></path><path d="M4 6h3M17 18h3"></path></svg>
                    <p>Fund Transfer</p>
                </article>
                <article class="pd-action">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 4h14v16H5z"></path><path d="M8 8h8M8 12h8M8 16h5"></path></svg>
                    <p>Pay Bill</p>
                </article>
                <article class="pd-action">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="6" y="3" width="12" height="18" rx="2"></rect><path d="M12 8v8M8 12h8"></path></svg>
                    <p>Recharge</p>
                </article>
                <article class="pd-action">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M6 3h9l3 3v15H6z"></path><path d="M9 13h6M9 17h4M9 9h6"></path></svg>
                    <p>Statements</p>
                </article>
                <article class="pd-action">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1 1 0 0 0 .2 1.1l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1 1 0 0 0-1.1-.2 1 1 0 0 0-.6.9V20a2 2 0 1 1-4 0v-.2a1 1 0 0 0-.7-.9 1 1 0 0 0-1.1.2l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1 1 0 0 0 .2-1.1 1 1 0 0 0-.9-.6H4a2 2 0 1 1 0-4h.2a1 1 0 0 0 .9-.7 1 1 0 0 0-.2-1.1l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1 1 0 0 0 1.1.2H9a1 1 0 0 0 .6-.9V4a2 2 0 1 1 4 0v.2a1 1 0 0 0 .7.9 1 1 0 0 0 1.1-.2l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1 1 0 0 0-.2 1.1V9c0 .4.2.8.6.9H20a2 2 0 1 1 0 4h-.2a1 1 0 0 0-.4.1"></path></svg>
                    <p>Account Services</p>
                </article>
                <article class="pd-action">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 10h18"></path><circle cx="8" cy="15" r="1"></circle><circle cx="12" cy="15" r="1"></circle></svg>
                    <p>Card Services</p>
                </article>
            </section>

            <section class="pd-sections pd-enter pd-d4">
                <article class="pd-panel pd-card">
                    <h3>Loans</h3>
                    <div class="pd-meta">
                        <div class="pd-meta-item">
                            <p class="k">Total Loan Taken</p>
                            <p class="v">${{ number_format($totalLoanTaken, 2) }}</p>
                        </div>
                        <div class="pd-meta-item">
                            <p class="k">Total Repaid</p>
                            <p class="v">${{ number_format($totalRepaid, 2) }}</p>
                        </div>
                        <div class="pd-meta-item">
                            <p class="k">Remaining Balance</p>
                            <p class="v">${{ number_format($loanOutstanding, 2) }}</p>
                        </div>
                        <div class="pd-meta-item">
                            <p class="k">Available Money</p>
                            <p class="v">${{ number_format($availableMoney, 2) }}</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('personal.loan') }}" class="inline-flex rounded-md border border-[#365074] px-3 py-1.5 text-xs font-semibold text-[#d8e8fc] hover:bg-[#1a2f52]">
                            Manage Loans
                        </a>
                    </div>
                    @if($activeLoans->isEmpty())
                        <p class="pd-empty">No active loans found.</p>
                    @else
                        <div class="pd-list">
                            @foreach($activeLoans as $loan)
                                <div class="pd-list-item">
                                    <div class="pd-list-row">
                                        <span class="label">Loan #{{ $loan->L_ID }}</span>
                                        <span class="pd-status">{{ ucfirst((string) ($loan->status ?? 'active')) }}</span>
                                    </div>
                                    <div class="pd-list-row">
                                        <span class="label">Amount</span>
                                        <span class="value">${{ number_format((float) $loan->L_Amount, 2) }}</span>
                                    </div>
                                    <div class="pd-list-row">
                                        <span class="label">Remaining</span>
                                        <span class="value">${{ number_format((float) ($loan->remaining_amount ?? $loan->L_Amount), 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </article>

                <article class="pd-panel pd-card">
                    <h3>Credit Card</h3>
                    @if(!$creditCard)
                        <p class="pd-empty">No credit card found for this account.</p>
                    @else
                        <div class="pd-meta">
                            <div class="pd-meta-item">
                                <p class="k">Card Number</p>
                                <p class="v">{{ $creditCard->masked_card_number }}</p>
                            </div>
                            <div class="pd-meta-item">
                                <p class="k">Expiry</p>
                                <p class="v">{{ $creditCard->expiry_date }}</p>
                            </div>
                            <div class="pd-meta-item">
                                <p class="k">Credit Limit</p>
                                <p class="v">${{ number_format($creditLimit, 2) }}</p>
                            </div>
                            <div class="pd-meta-item">
                                <p class="k">Available</p>
                                <p class="v">${{ number_format($creditAvailable, 2) }}</p>
                            </div>
                        </div>
                    @endif
                </article>

                <article class="pd-panel pd-card">
                    <h3>Recent Transactions</h3>
                    @if($recentTransactions->isEmpty())
                        <p class="pd-empty">No recent transactions found.</p>
                    @else
                        <div class="pd-table-wrap">
                            <table class="pd-table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Account</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->T_Type }}</td>
                                            <td>${{ number_format((float) $transaction->T_Amount, 2) }}</td>
                                            <td>{{ $transaction->A_Number }}</td>
                                            <td>{{ optional($transaction->T_Date)->format('M d, Y h:i A') ?? $transaction->created_at?->format('M d, Y h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </article>
            </section>
        </div>
    </div>
</x-app-layout>
