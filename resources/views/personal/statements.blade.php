<x-app-layout>
    @php
        $balance = (float) ($account->A_Balance ?? 0);
        $selectedType = (string) ($filters['type'] ?? '');
        $fromDate = (string) ($filters['from_date'] ?? '');
        $toDate = (string) ($filters['to_date'] ?? '');
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Mono:wght@300;400;500&display=swap');

        :root {
            --bg: #060a12;
            --surface: #0c1220;
            --card: #0f1826;
            --card-hi: #131f30;
            --border: rgba(56, 139, 253, 0.1);
            --border-hi: rgba(56, 139, 253, 0.3);
            --text: #e8f0ff;
            --muted: #4a6080;
            --accent: #3b82f6;
            --accent2: #06b6d4;
            --gold: #f59e0b;
            --green: #10b981;
            --font: 'Syne', sans-serif;
            --mono: 'DM Mono', monospace;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        .mars-root { font-family: var(--font); background: var(--bg); min-height: 100vh; color: var(--text); overflow-x: hidden; }
        .mars-bg { position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }
        .mars-blob { position: absolute; border-radius: 50%; filter: blur(100px); }
        .b1 { width: 700px; height: 700px; background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 65%); top: -200px; left: -200px; }
        .b2 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(6,182,212,0.1) 0%, transparent 65%); top: 40%; right: -150px; }
        .b3 { width: 400px; height: 400px; background: radial-gradient(circle, rgba(245,158,11,0.07) 0%, transparent 65%); bottom: -100px; left: 35%; }
        .mars-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(59,130,246,0.035) 1px, transparent 1px), linear-gradient(90deg, rgba(59,130,246,0.035) 1px, transparent 1px); background-size: 60px 60px; mask-image: radial-gradient(ellipse 80% 70% at 50% 50%, black 0%, transparent 90%); }
        .mars-wrap { position: relative; z-index: 1; max-width: 1280px; margin: 0 auto; padding: 0 1.5rem 5rem; }

        .mars-hero { display: grid; grid-template-columns: 1.15fr .85fr; gap: 1.25rem; margin-bottom: 1.25rem; }
        .mars-card { background: linear-gradient(180deg, rgba(15,24,38,0.96), rgba(12,18,32,0.96)); border: 1px solid var(--border); border-radius: 24px; box-shadow: 0 18px 40px rgba(0,0,0,0.3); }
        .hero-card { padding: 2rem; position: relative; overflow: hidden; }
        .hero-card::after { content: ''; position: absolute; inset: auto -40px -40px auto; width: 220px; height: 220px; border-radius: 50%; background: radial-gradient(circle, rgba(59,130,246,0.12), transparent 70%); }
        .hero-kicker { font-family: var(--mono); font-size: 0.72rem; letter-spacing: 0.18em; text-transform: uppercase; color: rgba(180,210,255,0.55); }
        .hero-title { margin-top: 1rem; font-size: clamp(1.8rem, 4vw, 2.6rem); line-height: 1; }
        .hero-title span { color: #9fd8ff; }
        .hero-sub { margin-top: 0.75rem; max-width: 560px; color: #91a7c5; font-size: 0.9rem; line-height: 1.65; }
        .hero-meta { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.3rem; }
        .hero-chip { padding: 0.45rem 0.7rem; border-radius: 999px; border: 1px solid rgba(59,130,246,0.15); background: rgba(59,130,246,0.08); color: #a9d1ff; font-size: 0.76rem; font-family: var(--mono); }

        .summary-card { padding: 1.4rem; display: grid; gap: 1rem; }
        .summary-row { padding: 1rem 1.1rem; border-radius: 18px; border: 1px solid var(--border); background: rgba(8,12,22,0.55); }
        .summary-label { font-size: 0.72rem; color: #7189ac; text-transform: uppercase; letter-spacing: 0.08em; font-family: var(--mono); }
        .summary-value { margin-top: 0.35rem; font-size: 1.45rem; font-weight: 800; }
        .summary-value.green { color: var(--green); }
        .summary-value.gold { color: var(--gold); }

        .grid-two { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem; }
        .section-card, .filter-card, .table-card { padding: 1.4rem; }
        .section-head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; }
        .section-title { font-size: 1rem; font-weight: 700; }
        .section-note { color: var(--muted); font-size: 0.82rem; }
        .mini-list, .summary-list { display: grid; gap: 0.8rem; }
        .mini-item, .summary-item { padding: 0.9rem 1rem; border-radius: 16px; border: 1px solid var(--border); background: rgba(8,12,22,0.6); }
        .mini-top, .summary-top { display: flex; justify-content: space-between; gap: 1rem; }
        .mini-type, .summary-month { font-size: 0.88rem; font-weight: 700; }
        .mini-date, .summary-count { color: var(--muted); font-size: 0.76rem; font-family: var(--mono); }
        .mini-amount { font-weight: 800; }
        .credit { color: var(--green); }
        .debit { color: var(--gold); }
        .summary-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.7rem; margin-top: 0.85rem; }
        .summary-stat { padding: 0.7rem; border-radius: 14px; background: rgba(15,24,38,0.8); border: 1px solid rgba(59,130,246,0.08); }
        .summary-stat-k { color: var(--muted); font-size: 0.68rem; text-transform: uppercase; font-family: var(--mono); }
        .summary-stat-v { margin-top: 0.25rem; font-size: 0.92rem; font-weight: 700; }

        .filters { display: grid; grid-template-columns: 1fr 1fr 1fr auto auto; gap: 0.9rem; align-items: end; }
        .field label { display: block; margin-bottom: 0.45rem; color: var(--muted); font-size: 0.76rem; letter-spacing: 0.07em; text-transform: uppercase; font-family: var(--mono); }
        .field input, .field select { width: 100%; background: rgba(6,11,24,0.9); border: 1px solid rgba(99,179,237,0.18); border-radius: 12px; color: var(--text); padding: 0.75rem 0.85rem; font-size: 0.9rem; outline: none; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; min-height: 46px; border-radius: 12px; padding: 0.8rem 1rem; border: 1px solid var(--border); text-decoration: none; cursor: pointer; font-family: var(--font); font-weight: 700; }
        .btn-primary { background: linear-gradient(135deg, #1a5fa8, var(--accent)); color: #fff; border-color: transparent; }
        .btn-secondary { background: rgba(15,24,38,0.92); color: var(--text); }

        .table-wrap { overflow-x: auto; border: 1px solid var(--border); border-radius: 18px; }
        .table { width: 100%; border-collapse: collapse; min-width: 760px; }
        .table th { text-align: left; padding: 0.95rem 1rem; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); background: rgba(8,12,22,0.8); font-family: var(--mono); }
        .table td { padding: 1rem; border-top: 1px solid rgba(59,130,246,0.08); font-size: 0.88rem; color: var(--text); }
        .tx-type { font-weight: 700; }
        .tx-meta { color: var(--muted); font-size: 0.75rem; margin-top: 0.25rem; }
        .empty { padding: 2.2rem; border: 1px dashed var(--border); border-radius: 18px; text-align: center; color: var(--muted); }
        .pagination { margin-top: 1rem; }
        .pagination nav > div:first-child { display: none; }
        .pagination nav > div:last-child { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; color: var(--muted); font-size: 0.82rem; }
        .pagination a, .pagination span[aria-current="page"] span, .pagination span[aria-disabled="true"] span { display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 0.8rem; border-radius: 10px; border: 1px solid var(--border); background: rgba(15,24,38,0.9); color: var(--text); text-decoration: none; }
        .pagination span[aria-current="page"] span { background: rgba(59,130,246,0.12); border-color: rgba(59,130,246,0.3); }

        @media (max-width: 1040px) { .mars-hero, .grid-two { grid-template-columns: 1fr; } .filters { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 720px) { .filters, .summary-stats { grid-template-columns: 1fr; } }
    </style>

    <div class="mars-root">
        <div class="mars-bg">
            <div class="mars-blob b1"></div>
            <div class="mars-blob b2"></div>
            <div class="mars-blob b3"></div>
            <div class="mars-grid"></div>
        </div>

        <div class="mars-wrap">
            <section class="mars-hero">
                <article class="mars-card hero-card">
                    <div class="hero-kicker">Transaction Statements</div>
                    <h1 class="hero-title">Review Activity And <span>Download Records</span></h1>
                    <p class="hero-sub">Filter your statement by date and category, inspect mini-statement activity, and export a CSV directly from the same banking workspace.</p>
                    <div class="hero-meta">
                        <span class="hero-chip">Account {{ $account->A_Number }}</span>
                        <span class="hero-chip">Balance Tk {{ number_format($balance, 2) }}</span>
                        <span class="hero-chip">{{ $transactions->total() }} matched transactions</span>
                    </div>
                </article>

                <aside class="mars-card summary-card">
                    <div class="summary-row">
                        <div class="summary-label">Available Balance</div>
                        <div class="summary-value">Tk {{ number_format($balance, 2) }}</div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label">Current Filter</div>
                        <div class="summary-value green">{{ $selectedType !== '' ? ($availableTypes[$selectedType] ?? ucfirst($selectedType)) : 'All Transactions' }}</div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label">Period</div>
                        <div class="summary-value gold">{{ $fromDate !== '' || $toDate !== '' ? trim(($fromDate ?: 'Start') . ' → ' . ($toDate ?: 'Now')) : 'Full History' }}</div>
                    </div>
                </aside>
            </section>

            <section class="grid-two">
                <article class="mars-card section-card">
                    <div class="section-head">
                        <div>
                            <div class="section-title">Mini Statement</div>
                            <div class="section-note">Your five most recent transactions at a glance.</div>
                        </div>
                    </div>
                    <div class="mini-list">
                        @forelse ($miniStatement as $item)
                            @php
                                $isCredit = str_starts_with((string) $item->T_Type, 'Fund Transfer Received')
                                    || str_starts_with((string) $item->T_Type, 'Recharge Received')
                                    || in_array((string) $item->T_Type, ['Loan Disbursement', 'Fixed Deposit Maturity Payout', 'Fixed Deposit Early Break Payout'], true);
                            @endphp
                            <div class="mini-item">
                                <div class="mini-top">
                                    <div>
                                        <div class="mini-type">{{ $item->T_Type }}</div>
                                        <div class="mini-date">{{ optional($item->T_Date)->format('M d, Y · h:i A') }}</div>
                                    </div>
                                    <div class="mini-amount {{ $isCredit ? 'credit' : 'debit' }}">
                                        {{ $isCredit ? '+' : '-' }}Tk {{ number_format((float) $item->T_Amount, 2) }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty">No statement activity found yet.</div>
                        @endforelse
                    </div>
                </article>

                <article class="mars-card section-card">
                    <div class="section-head">
                        <div>
                            <div class="section-title">Monthly Summary</div>
                            <div class="section-note">A quick rollup of credits, debits, and net movement by month.</div>
                        </div>
                    </div>
                    <div class="summary-list">
                        @forelse ($monthlySummaries as $summary)
                            <div class="summary-item">
                                <div class="summary-top">
                                    <div class="summary-month">{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $summary['month'])->format('F Y') }}</div>
                                    <div class="summary-count">{{ $summary['transaction_count'] }} txns</div>
                                </div>
                                <div class="summary-stats">
                                    <div class="summary-stat">
                                        <div class="summary-stat-k">Credits</div>
                                        <div class="summary-stat-v credit">Tk {{ number_format((float) $summary['total_credits'], 2) }}</div>
                                    </div>
                                    <div class="summary-stat">
                                        <div class="summary-stat-k">Debits</div>
                                        <div class="summary-stat-v debit">Tk {{ number_format((float) $summary['total_debits'], 2) }}</div>
                                    </div>
                                    <div class="summary-stat">
                                        <div class="summary-stat-k">Net</div>
                                        <div class="summary-stat-v {{ (float) $summary['net_amount'] >= 0 ? 'credit' : 'debit' }}">Tk {{ number_format((float) $summary['net_amount'], 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty">No monthly statement data is available for the selected filters.</div>
                        @endforelse
                    </div>
                </article>
            </section>

            <section class="mars-card filter-card">
                <div class="section-head">
                    <div>
                        <div class="section-title">Filter Statement</div>
                        <div class="section-note">Adjust the time range or transaction class, then export the same filtered result.</div>
                    </div>
                </div>

                <form method="GET" action="{{ route('personal.statements') }}" class="filters">
                    <div class="field">
                        <label for="from_date">From Date</label>
                        <input id="from_date" type="date" name="from_date" value="{{ $fromDate }}">
                    </div>
                    <div class="field">
                        <label for="to_date">To Date</label>
                        <input id="to_date" type="date" name="to_date" value="{{ $toDate }}">
                    </div>
                    <div class="field">
                        <label for="type">Transaction Type</label>
                        <select id="type" name="type">
                            <option value="">All Types</option>
                            @foreach ($availableTypes as $value => $label)
                                <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="per_page">Rows</label>
                        <select id="per_page" name="per_page">
                            @foreach ([10, 15, 25, 50] as $size)
                                <option value="{{ $size }}" @selected((int) $filters['per_page'] === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('personal.statements.download', ['from_date' => $fromDate, 'to_date' => $toDate, 'type' => $selectedType]) }}" class="btn btn-secondary">Download CSV</a>
                </form>
            </section>

            <section class="mars-card table-card">
                <div class="section-head">
                    <div>
                        <div class="section-title">Statement Details</div>
                        <div class="section-note">Latest matching transactions, newest first.</div>
                    </div>
                </div>

                @if ($transactions->isEmpty())
                    <div class="empty">No transactions match the selected filters.</div>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Transaction</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    @php
                                        $isCredit = str_starts_with((string) $transaction->T_Type, 'Fund Transfer Received')
                                            || str_starts_with((string) $transaction->T_Type, 'Recharge Received')
                                            || in_array((string) $transaction->T_Type, ['Loan Disbursement', 'Fixed Deposit Maturity Payout', 'Fixed Deposit Early Break Payout'], true);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="tx-type">{{ $transaction->T_Type }}</div>
                                            <div class="tx-meta">Ref #{{ $transaction->T_ID }} · Account {{ $transaction->A_Number }}</div>
                                        </td>
                                        <td class="{{ $isCredit ? 'credit' : 'debit' }}">{{ $isCredit ? '+' : '-' }}Tk {{ number_format((float) $transaction->T_Amount, 2) }}</td>
                                        <td>{{ optional($transaction->T_Date)->format('M d, Y · h:i A') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination">{{ $transactions->links() }}</div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
