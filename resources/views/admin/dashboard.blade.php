<x-app-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        .mars-admin {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f1623;
            min-height: 100vh;
            color: #e2e8f0;
            position: relative;
            overflow-x: hidden;
        }

        .mars-admin::before {
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

        .admin-wrap {
            position: relative;
            z-index: 1;
            max-width: 1080px;
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

        .admin-topbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 22px;
        }

        .admin-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .admin-nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 10px;
            border: 1px solid #1e2d45;
            background: rgba(15, 22, 35, 0.65);
            color: #cbd5e0;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .admin-nav-btn:hover {
            border-color: #2a3f5f;
            color: #f1f5f9;
            transform: translateY(-1px);
        }

        .admin-nav-btn.primary {
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.2), rgba(34, 211, 238, 0.12));
            border-color: rgba(56, 189, 248, 0.4);
            color: #e0f2fe;
        }

        .admin-nav-btn.warn {
            border-color: rgba(248, 113, 113, 0.22);
            color: #fca5a5;
            background: rgba(248, 113, 113, 0.08);
        }
        .admin-topbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .admin-notif {
            position: relative;
            width: 40px; height: 40px;
            border-radius: 10px; border: 1px solid #1e2d45;
            background: rgba(15, 22, 35, 0.65);
            color: #cbd5e0; display: inline-flex;
            align-items: center; justify-content: center;
            text-decoration: none;
        }
        .admin-notif-dot {
            position: absolute; top: 8px; right: 8px;
            width: 7px; height: 7px; border-radius: 50%;
            background: #22c55e; border: 2px solid #0f1623;
        }
        .admin-user-menu { position: relative; list-style: none; }
        .admin-user-menu summary { list-style: none; cursor: pointer; }
        .admin-user-menu summary::-webkit-details-marker { display: none; }
        .admin-user-dropdown {
            position: absolute; top: calc(100% + 8px); right: 0;
            min-width: 180px; padding: 6px;
            border-radius: 12px; border: 1px solid #1e2d45;
            background: rgba(15, 22, 35, 0.96); box-shadow: 0 16px 40px rgba(0,0,0,0.35);
        }
        .admin-user-link {
            display: block; width: 100%; padding: 10px 12px;
            border: none; border-radius: 8px; background: none;
            color: #cbd5e0; text-align: left; text-decoration: none;
            cursor: pointer; font: inherit;
        }
        .admin-user-link:hover { background: rgba(56, 189, 248, 0.08); color: #f1f5f9; }
        .admin-user-link.logout { color: #fca5a5; }

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
            margin-bottom: 24px;
        }

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

        .stat-cell { background: #161d2e; padding: 16px 20px; }
        .stat-cell-label { font-size: 10px; font-family: 'JetBrains Mono', monospace; color: #2d3f55; text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 5px; }
        .stat-cell-value { font-size: 15px; font-weight: 700; color: #94a3b8; }
        .stat-cell-value.teal { color: #38bdf8; }
        .stat-cell-value.green { color: #4ade80; }

        @media (max-width: 800px) { .stats-strip { grid-template-columns: repeat(2, 1fr); } }

        .notice {
            display: flex;
            align-items: center;
            gap: 8px;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            font-family: 'JetBrains Mono', monospace;
            margin-bottom: 14px;
        }

        .notice.ok {
            background: rgba(56, 189, 248, 0.06);
            border: 1px solid rgba(56, 189, 248, 0.14);
            color: #38bdf8;
        }

        .notice.err {
            background: rgba(248, 113, 113, 0.08);
            border: 1px solid rgba(248, 113, 113, 0.15);
            color: #f87171;
        }

        .mars-card {
            background: #161d2e;
            border: 1px solid #1e2d45;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
        }

        .mars-card.accented::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, #38bdf8, #22d3ee, transparent);
        }

        .card-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            gap: 10px;
        }

        .card-section-head h3 { font-size: 15px; font-weight: 700; color: #cbd5e0; }
        .card-section-head p { font-size: 12px; color: #374151; margin-top: 3px; }
        .section-count { font-family: 'JetBrains Mono', monospace; color: #38bdf8; font-size: 12px; }

        .table-wrap {
            border: 1px solid #1e2d45;
            border-radius: 10px;
            overflow-x: auto;
            background: #0f1623;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        .admin-table th {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #2d3f55;
            text-align: left;
            padding: 12px 14px;
            border-bottom: 1px solid #1e2d45;
        }

        .admin-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #1a2538;
            color: #cbd5e0;
            font-size: 13px;
        }

        .admin-table tr:last-child td { border-bottom: 0; }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .btn-action {
            border: 1px solid #1e2d45;
            border-radius: 8px;
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-action.accept {
            background: rgba(56, 189, 248, 0.14);
            border-color: rgba(56, 189, 248, 0.35);
            color: #7dd3fc;
        }

        .btn-action.accept:hover { background: rgba(56, 189, 248, 0.22); }

        .btn-action.reject {
            background: rgba(248, 113, 113, 0.09);
            border-color: rgba(248, 113, 113, 0.3);
            color: #fca5a5;
        }

        .btn-action.reject:hover { background: rgba(248, 113, 113, 0.16); }

        .empty {
            border: 1px dashed #1e2d45;
            border-radius: 10px;
            padding: 16px;
            color: #4a5568;
            font-size: 13px;
            text-align: center;
        }
    </style>

    <div class="mars-admin">
        <div class="bg-glow-1"></div>
        <div class="bg-glow-2"></div>

        <div class="admin-wrap">
            @php($adminUnreadNotifications = auth()->user()->unreadNotifications()->count())
            <div class="admin-topbar">
                <div class="admin-nav">
                    <a href="{{ route('home') }}" class="admin-nav-btn">Home</a>
                    <a href="{{ route('personal.dashboard') }}" class="admin-nav-btn">Personal Dashboard</a>
                    <a href="{{ route('personal.cards') }}" class="admin-nav-btn">Cards</a>
                    <a href="{{ route('personal.loan') }}" class="admin-nav-btn">Loans</a>
                    <a href="{{ route('about') }}" class="admin-nav-btn">About Us</a>
                    <a href="{{ route('contact.create') }}" class="admin-nav-btn">Contact</a>
                    <a href="{{ route('profile.edit') }}" class="admin-nav-btn">Profile</a>
                    <a href="{{ route('admin.dashboard') }}" class="admin-nav-btn primary">Admin Dashboard</a>
                </div>
                <div class="admin-topbar-right">
                    <button type="button" onclick="history.back()" class="admin-nav-btn">Back</button>
                    <a
                        href="{{ route('personal.dashboard') }}"
                        class="admin-notif"
                        aria-label="Notifications{{ $adminUnreadNotifications > 0 ? ' (' . $adminUnreadNotifications . ' unread)' : '' }}"
                        title="Notifications"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="18" height="18" aria-hidden="true">
                            <path d="M14.5 18a2.5 2.5 0 0 1-5 0"></path>
                            <path d="M18 16V11a6 6 0 1 0-12 0v5l-2 2h16l-2-2z"></path>
                        </svg>
                        @if ($adminUnreadNotifications > 0)
                            <span class="admin-notif-dot" aria-hidden="true"></span>
                        @endif
                    </a>
                    <details class="admin-user-menu">
                        <summary class="admin-nav-btn">{{ auth()->user()->name }} ▾</summary>
                        <div class="admin-user-dropdown">
                            <a href="{{ route('admin.dashboard') }}" class="admin-user-link">Admin Dashboard</a>
                            <a href="{{ route('profile.edit') }}" class="admin-user-link">Manage Profile</a>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="admin-user-link logout">Admin Logout</button>
                            </form>
                        </div>
                    </details>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="admin-nav-btn warn">Admin Logout</button>
                    </form>
                </div>
            </div>

            <p class="page-eyebrow">Admin Control</p>
            <h1 class="page-title">Admin Dashboard</h1>
            <p class="page-subtitle">Review and process loan and card application requests.</p>

            <div class="stats-strip">
                <div class="stat-cell">
                    <div class="stat-cell-label">Pending Loans</div>
                    <div class="stat-cell-value teal">{{ $pendingLoanRequests->count() }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-cell-label">Pending Cards</div>
                    <div class="stat-cell-value teal">{{ $pendingCardApplications->count() }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-cell-label">Total Pending</div>
                    <div class="stat-cell-value">{{ $pendingLoanRequests->count() + $pendingCardApplications->count() }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-cell-label">Access Level</div>
                    <div class="stat-cell-value green">Admin</div>
                </div>
            </div>

            @if (session('admin_success'))
                <div class="notice ok">{{ session('admin_success') }}</div>
            @endif

            @if (session('admin_error'))
                <div class="notice err">{{ session('admin_error') }}</div>
            @endif

            <section class="mars-card accented">
                <div class="card-section-head">
                    <div>
                        <h3>Loan Applications</h3>
                        <p>Approve or reject loan requests waiting for admin action.</p>
                    </div>
                    <div class="section-count">{{ $pendingLoanRequests->count() }} pending</div>
                </div>

                @if ($pendingLoanRequests->isEmpty())
                    <div class="empty">No pending loan requests.</div>
                @else
                    <div class="table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Requested At</th>
                                    <th style="text-align:right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingLoanRequests as $request)
                                    <tr>
                                        <td>#{{ $request->LR_ID }}</td>
                                        <td>{{ $request->C_ID }}</td>
                                        <td>Tk {{ number_format((float) $request->requested_amount, 2) }}</td>
                                        <td>{{ optional($request->created_at)->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <div class="actions">
                                                <form method="POST" action="{{ route('admin.loans.accept', $request) }}">
                                                    @csrf
                                                    <button type="submit" class="btn-action accept">Accept</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.loans.reject', $request) }}">
                                                    @csrf
                                                    <button type="submit" class="btn-action reject">Reject</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="mars-card accented">
                <div class="card-section-head">
                    <div>
                        <h3>Card Applications</h3>
                        <p>Review submitted card applications and make a decision.</p>
                    </div>
                    <div class="section-count">{{ $pendingCardApplications->count() }} pending</div>
                </div>

                @if ($pendingCardApplications->isEmpty())
                    <div class="empty">No pending card requests.</div>
                @else
                    <div class="table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>App ID</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Network</th>
                                    <th style="text-align:right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingCardApplications as $application)
                                    <tr>
                                        <td>{{ $application->application_id }}</td>
                                        <td>{{ $application->C_ID }}</td>
                                        <td>{{ ucfirst($application->card_category) }}</td>
                                        <td>{{ $application->card_network }}</td>
                                        <td>
                                            <div class="actions">
                                                <form method="POST" action="{{ route('admin.cards.accept', $application) }}">
                                                    @csrf
                                                    <button type="submit" class="btn-action accept">Accept</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.cards.reject', $application) }}">
                                                    @csrf
                                                    <button type="submit" class="btn-action reject">Reject</button>
                                                </form>
                                            </div>
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
