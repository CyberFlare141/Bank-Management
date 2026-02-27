<x-app-layout>
    @php
        $cards = [
            [
                'type' => 'debit',
                'title' => 'Debit Card',
                'desc' => 'Direct account access for daily purchases, ATM withdrawals, and secure online payments.',
                'features' => ['No annual fee', 'Real-time spending alerts', 'ATM + POS enabled'],
                'eligibility' => 'Requires an active account.',
            ],
            [
                'type' => 'credit',
                'title' => 'Credit Card',
                'desc' => 'Flexible spending power with rewards, statement billing cycle, and purchase protection.',
                'features' => ['Reward points', 'Interest-free period', 'Enhanced fraud monitoring'],
                'eligibility' => 'Requires income and employment verification.',
            ],
        ];
    @endphp

    <style>
        .cards-root { min-height: 100vh; background: #050c18; color: #e6efff; padding: 2rem 1.25rem 3rem; }
        .cards-wrap { max-width: 1100px; margin: 0 auto; }
        .cards-top { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
        .cards-title { font-size: 1.9rem; font-weight: 800; letter-spacing: -0.02em; }
        .cards-sub { color: #7f97bd; font-size: 0.92rem; margin-top: 0.3rem; }
        .cards-nav { color: #58a6ff; text-decoration: none; font-size: 0.82rem; border: 1px solid rgba(88,166,255,0.3); border-radius: 8px; padding: 0.4rem 0.8rem; }
        .cards-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; margin-bottom: 1.4rem; }
        .cards-item { background: #091427; border: 1px solid rgba(88,166,255,0.2); border-radius: 16px; padding: 1.2rem; }
        .cards-item h2 { font-size: 1.25rem; margin-bottom: 0.45rem; }
        .cards-item p { color: #8ea4c7; font-size: 0.9rem; line-height: 1.5; }
        .cards-points { margin: 0.8rem 0 0.55rem; padding-left: 1rem; color: #bfd2ef; font-size: 0.85rem; }
        .cards-eligibility { color: #f5c36b; font-size: 0.8rem; margin-bottom: 1rem; }
        .cards-btn { display: inline-block; text-decoration: none; color: #fff; background: linear-gradient(135deg, #1667c5, #2f9cff); font-weight: 700; font-size: 0.82rem; padding: 0.55rem 0.95rem; border-radius: 9px; }
        .cards-alert { margin-bottom: 1rem; padding: 0.75rem 0.9rem; border-radius: 10px; font-size: 0.85rem; border: 1px solid; }
        .cards-alert.ok { border-color: rgba(80,220,140,0.35); color: #50dc8c; background: rgba(80,220,140,0.08); }
        .cards-alert.err { border-color: rgba(248,113,113,0.35); color: #f87171; background: rgba(248,113,113,0.08); }
        .cards-table-wrap { background: #081223; border: 1px solid rgba(88,166,255,0.15); border-radius: 14px; overflow: auto; }
        .cards-table { width: 100%; border-collapse: collapse; min-width: 680px; }
        .cards-table th, .cards-table td { padding: 0.72rem 0.85rem; border-bottom: 1px solid rgba(88,166,255,0.08); text-align: left; font-size: 0.82rem; }
        .cards-table th { color: #7b95bc; text-transform: uppercase; letter-spacing: 0.06em; font-size: 0.7rem; }
        .cards-pill { display: inline-block; border: 1px solid rgba(245,195,107,0.3); color: #f5c36b; background: rgba(245,195,107,0.08); border-radius: 999px; padding: 0.1rem 0.55rem; font-size: 0.68rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.08em; }
        .cards-empty { padding: 1rem; color: #8ea4c7; font-size: 0.85rem; }
        @media (max-width: 860px) { .cards-grid { grid-template-columns: 1fr; } }
    </style>

    <div class="cards-root">
        <div class="cards-wrap">
            <div class="cards-top">
                <div>
                    <h1 class="cards-title">Cards Management</h1>
                    <p class="cards-sub">Choose a card, submit your application, and track review status from one page.</p>
                </div>
                <a href="{{ route('personal.dashboard') }}" class="cards-nav">Back to Dashboard</a>
            </div>

            @if (session('card_success'))
                <div class="cards-alert ok">{{ session('card_success') }}</div>
            @endif

            @if (session('card_error'))
                <div class="cards-alert err">{{ session('card_error') }}</div>
            @endif

            @if (!$hasBankingProfile)
                <div class="cards-alert err">Your customer profile/account is incomplete. Complete profile setup before applying.</div>
            @endif

            <div class="cards-grid">
                @foreach($cards as $card)
                    <article class="cards-item">
                        <h2>{{ $card['title'] }}</h2>
                        <p>{{ $card['desc'] }}</p>
                        <ul class="cards-points">
                            @foreach($card['features'] as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                        <p class="cards-eligibility">Eligibility: {{ $card['eligibility'] }}</p>
                        <a href="{{ route('personal.cards.create', ['cardType' => $card['type']]) }}" class="cards-btn">Apply for {{ $card['title'] }}</a>
                    </article>
                @endforeach
            </div>

            <div class="cards-table-wrap">
                @if ($applications->isEmpty())
                    <div class="cards-empty">No card applications submitted yet.</div>
                @else
                    <table class="cards-table">
                        <thead>
                            <tr>
                                <th>Application ID</th>
                                <th>Card Type</th>
                                <th>Network</th>
                                <th>Submitted</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $application)
                                <tr>
                                    <td style="color:#58a6ff;font-weight:700">{{ $application->application_id }}</td>
                                    <td>{{ ucfirst($application->card_category) }}</td>
                                    <td>{{ $application->card_network }}</td>
                                    <td>{{ $application->created_at?->format('M d, Y h:i A') }}</td>
                                    <td><span class="cards-pill">{{ str_replace('_', ' ', $application->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
