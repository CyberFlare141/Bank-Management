<x-app-layout>
    <style>
        :root {
            --db-bg: #060c17;
            --db-bg-soft: #0a1324;
            --db-ink: #e4ecff;
            --db-muted: #94a3c4;
            --db-border: rgba(148, 163, 184, 0.22);
            --db-cyan: #34d3ff;
            --db-blue: #4c6fff;
        }

        .db-shell {
            min-height: calc(100vh - 64px);
            background:
                radial-gradient(44rem 30rem at -8% -10%, rgba(52, 211, 255, 0.2), transparent 65%),
                radial-gradient(50rem 35rem at 110% -15%, rgba(76, 111, 255, 0.22), transparent 65%),
                radial-gradient(45rem 28rem at 50% 120%, rgba(45, 212, 191, 0.12), transparent 70%),
                linear-gradient(150deg, var(--db-bg), var(--db-bg-soft));
            position: relative;
            overflow: hidden;
        }

        .db-shell::before,
        .db-shell::after {
            content: "";
            position: absolute;
            border-radius: 9999px;
            filter: blur(50px);
            pointer-events: none;
        }

        .db-shell::before {
            width: 18rem;
            height: 18rem;
            top: 6rem;
            left: -6rem;
            background: rgba(34, 211, 238, 0.16);
            animation: db-float 7s ease-in-out infinite;
        }

        .db-shell::after {
            width: 20rem;
            height: 20rem;
            right: -6rem;
            top: 30%;
            background: rgba(59, 130, 246, 0.18);
            animation: db-float 9s ease-in-out infinite reverse;
        }

        .db-card {
            position: relative;
            border-radius: 1.15rem;
            border: 1px solid var(--db-border);
            background: linear-gradient(170deg, rgba(15, 23, 42, 0.86), rgba(15, 23, 42, 0.58));
            box-shadow: 0 20px 40px rgba(2, 6, 23, 0.45);
            backdrop-filter: blur(11px);
            transition: transform 260ms ease, box-shadow 260ms ease, border-color 260ms ease;
        }

        .db-card::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(140deg, rgba(52, 211, 255, 0.36), rgba(76, 111, 255, 0.08), rgba(52, 211, 255, 0.24));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        .db-card:hover {
            transform: translateY(-5px);
            border-color: rgba(52, 211, 255, 0.44);
            box-shadow: 0 24px 44px rgba(8, 47, 73, 0.4);
        }

        .db-fade {
            opacity: 0;
            transform: translateY(16px);
            animation: db-fade-up 620ms cubic-bezier(.22, .95, .37, 1) forwards;
        }

        .db-balance {
            letter-spacing: -0.03em;
            color: #9de8ff;
            text-shadow: 0 0 16px rgba(34, 211, 238, 0.44);
            animation: db-pulse 2.5s ease-in-out infinite;
        }

        .db-metric {
            border: 1px solid rgba(148, 163, 184, 0.2);
            background: rgba(15, 23, 42, 0.58);
            border-radius: 0.9rem;
            transition: border-color 220ms ease, transform 220ms ease;
        }

        .db-metric:hover {
            transform: translateY(-2px);
            border-color: rgba(52, 211, 255, 0.36);
        }

        .db-status {
            border: 1px solid rgba(74, 222, 128, 0.35);
            background: rgba(16, 185, 129, 0.14);
            color: #7ee6b3;
        }

        .db-table-row {
            transition: background-color 220ms ease, transform 220ms ease;
        }

        .db-table-row:hover {
            background: rgba(30, 41, 59, 0.62);
            transform: translateX(2px);
        }

        @keyframes db-fade-up {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes db-pulse {
            0%, 100% { text-shadow: 0 0 11px rgba(34, 211, 238, 0.35); }
            50% { text-shadow: 0 0 22px rgba(34, 211, 238, 0.65); }
        }

        @keyframes db-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-14px); }
        }
    </style>

    <div class="db-shell py-8 sm:py-10">
        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 sm:mb-8 db-fade" style="animation-delay: 60ms;">
                <p class="text-xs tracking-[0.2em] uppercase text-cyan-300/80 font-semibold">Private Banking</p>
                <h1 class="mt-2 text-2xl sm:text-3xl font-semibold text-[var(--db-ink)]">Personal Dashboard</h1>
                <p class="mt-2 text-sm text-[var(--db-muted)]">Secure account visibility, lending status, and card control in one place.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                <section class="xl:col-span-2 db-card p-5 sm:p-7 db-fade" style="animation-delay: 120ms;">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.16em] text-slate-400">Account Overview</p>
                            <h2 class="mt-2 text-base sm:text-lg text-slate-200 font-medium">Current Balance</h2>
                        </div>
                        <span class="rounded-full border border-cyan-300/35 bg-cyan-400/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-cyan-200">
                            {{ $account?->account_type ?? 'Standard' }}
                        </span>
                    </div>

                    <div
                        class="mt-5 text-3xl sm:text-5xl font-bold db-balance"
                        x-data="{
                            amount: 0,
                            target: {{ (float) ($account->A_Balance ?? 0) }},
                            format(v) { return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(v); },
                            init() {
                                const duration = 1100;
                                const start = performance.now();
                                const tick = (now) => {
                                    const progress = Math.min((now - start) / duration, 1);
                                    this.amount = this.target * (1 - Math.pow(1 - progress, 3));
                                    if (progress < 1) requestAnimationFrame(tick);
                                };
                                requestAnimationFrame(tick);
                            }
                        }"
                        x-init="init()"
                        x-text="format(amount)"
                    >
                        $0.00
                    </div>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <article class="db-metric p-4 sm:p-5">
                            <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Account Number</p>
                            <p class="mt-2 text-sm sm:text-base font-semibold text-slate-100">{{ $account->A_Number ?? 'Not available' }}</p>
                        </article>
                        <article class="db-metric p-4 sm:p-5">
                            <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Account Type</p>
                            <p class="mt-2 text-sm sm:text-base font-semibold text-slate-100">{{ $account->account_type ?? 'Not set' }}</p>
                        </article>
                    </div>
                </section>

                <section class="db-card p-5 sm:p-7 db-fade" style="animation-delay: 180ms;">
                    <h2 class="text-base sm:text-lg text-slate-100 font-semibold">Profile</h2>
                    <div class="mt-5 space-y-4">
                        <div class="db-metric p-4">
                            <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Name</p>
                            <p class="mt-2 text-sm sm:text-base text-slate-100 font-medium">{{ $user->name }}</p>
                        </div>
                        <div class="db-metric p-4">
                            <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Email</p>
                            <p class="mt-2 text-sm sm:text-base text-slate-100 font-medium break-all">{{ $user->email }}</p>
                        </div>
                    </div>
                </section>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
                <section class="db-card p-5 sm:p-7 db-fade" style="animation-delay: 240ms;">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-base sm:text-lg text-slate-100 font-semibold">Active Loans</h2>
                        <span class="rounded-full border border-slate-600 bg-slate-800/60 px-2.5 py-1 text-xs text-slate-300">{{ $activeLoans->count() }} Open</span>
                    </div>
                    @if($activeLoans->isEmpty())
                        <p class="mt-4 text-sm text-slate-400">No active loans found.</p>
                    @else
                        <div class="mt-4 space-y-3">
                            @foreach($activeLoans as $loan)
                                <article class="db-metric p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-sm text-slate-300">Loan #{{ $loan->L_ID }}</p>
                                        <span class="db-status inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.12em]">
                                            {{ ucfirst((string) ($loan->status ?? 'active')) }}
                                        </span>
                                    </div>
                                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <p class="text-slate-400">Loan Amount</p>
                                            <p class="mt-1 font-semibold text-slate-100">${{ number_format((float) $loan->L_Amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-slate-400">Remaining Balance</p>
                                            <p class="mt-1 font-semibold text-cyan-300">${{ number_format((float) ($loan->remaining_amount ?? $loan->L_Amount), 2) }}</p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>

                <section class="db-card p-5 sm:p-7 db-fade" style="animation-delay: 300ms;">
                    <h2 class="text-base sm:text-lg text-slate-100 font-semibold">Credit Card</h2>
                    @if(!$creditCard)
                        <p class="mt-4 text-sm text-slate-400">No credit card found for this account.</p>
                    @else
                        <div class="mt-4 rounded-2xl border border-cyan-400/25 bg-gradient-to-br from-cyan-500/16 to-blue-500/12 p-4 sm:p-5 shadow-[0_0_28px_rgba(8,145,178,0.18)] transition duration-300 hover:shadow-[0_0_36px_rgba(8,145,178,0.26)]">
                            <p class="text-xs tracking-[0.16em] uppercase text-cyan-200/90">Digital Card</p>
                            <p class="mt-4 text-lg sm:text-xl font-semibold tracking-[0.18em] text-slate-100">{{ $creditCard->masked_card_number }}</p>
                            <div class="mt-5 grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-slate-300">Expiry Date</p>
                                    <p class="mt-1 font-semibold text-slate-100">{{ $creditCard->expiry_date }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-300">Status</p>
                                    <p class="mt-1 font-semibold text-cyan-200">{{ ucfirst((string) ($creditCard->status ?? 'active')) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <article class="db-metric p-4">
                                <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Credit Limit</p>
                                <p class="mt-2 text-sm sm:text-base font-semibold text-slate-100">${{ number_format((float) $creditCard->credit_limit, 2) }}</p>
                            </article>
                            <article class="db-metric p-4">
                                <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Available Credit</p>
                                <p class="mt-2 text-sm sm:text-base font-semibold text-cyan-300">${{ number_format((float) $creditCard->available_credit, 2) }}</p>
                            </article>
                        </div>
                    @endif
                </section>
            </div>

            <section class="mt-6 db-card p-5 sm:p-7 db-fade" style="animation-delay: 360ms;">
                <h2 class="text-base sm:text-lg text-slate-100 font-semibold">Recent Transactions</h2>
                @if($recentTransactions->isEmpty())
                    <p class="mt-4 text-sm text-slate-400">No recent transactions found.</p>
                @else
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-700/80 text-left text-slate-400">
                                    <th class="py-2 pr-4 font-medium">Type</th>
                                    <th class="py-2 pr-4 font-medium">Amount</th>
                                    <th class="py-2 pr-4 font-medium">Account</th>
                                    <th class="py-2 font-medium">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                    <tr class="db-table-row border-b border-slate-800/70">
                                        <td class="py-3 pr-4 text-slate-100 font-medium">{{ $transaction->T_Type }}</td>
                                        <td class="py-3 pr-4 text-slate-200">${{ number_format((float) $transaction->T_Amount, 2) }}</td>
                                        <td class="py-3 pr-4 text-slate-300">{{ $transaction->A_Number }}</td>
                                        <td class="py-3 text-slate-300">
                                            {{ optional($transaction->T_Date)->format('M d, Y h:i A') ?? $transaction->created_at?->format('M d, Y h:i A') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
