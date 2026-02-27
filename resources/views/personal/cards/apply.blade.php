<x-app-layout>
    @php
        $isCredit = $cardType === 'credit';
    @endphp

    <style>
        .card-form-root { min-height: 100vh; background: #050c18; color: #e7f0ff; padding: 2rem 1.25rem 3rem; }
        .card-form-wrap { max-width: 980px; margin: 0 auto; }
        .card-form-top { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.2rem; }
        .card-form-title { font-size: 1.8rem; font-weight: 800; letter-spacing: -0.02em; }
        .card-form-sub { color: #86a2c8; font-size: 0.9rem; margin-top: 0.3rem; }
        .card-link { color: #58a6ff; text-decoration: none; font-size: 0.82rem; border: 1px solid rgba(88,166,255,0.3); border-radius: 8px; padding: 0.4rem 0.8rem; }
        .card-panel { background: #081325; border: 1px solid rgba(88,166,255,0.18); border-radius: 15px; padding: 1rem; }
        .card-section { margin-bottom: 1rem; }
        .card-section h2 { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.08em; color: #7f98bf; margin-bottom: 0.8rem; }
        .card-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.75rem; }
        .card-field label { display: block; font-size: 0.74rem; color: #8aa7cf; margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.07em; }
        .card-field input, .card-field select, .card-field textarea {
            width: 100%; background: #050d1d; border: 1px solid rgba(88,166,255,0.2); border-radius: 10px; color: #dbe9ff;
            padding: 0.6rem 0.75rem; font-size: 0.86rem; outline: none;
        }
        .card-field textarea { min-height: 84px; resize: vertical; }
        .card-field .err { color: #f87171; font-size: 0.76rem; margin-top: 0.3rem; }
        .card-form-actions { display: flex; justify-content: flex-end; margin-top: 0.9rem; }
        .card-submit { border: 0; color: #fff; background: linear-gradient(135deg, #1566c4, #2e9bff); border-radius: 9px; padding: 0.65rem 1rem; font-weight: 800; font-size: 0.82rem; cursor: pointer; }
        .card-note { font-size: 0.8rem; color: #f5c36b; margin-bottom: 0.9rem; }
        @media (max-width: 820px) { .card-grid { grid-template-columns: 1fr; } }
    </style>

    <div class="card-form-root">
        <div class="card-form-wrap">
            <div class="card-form-top">
                <div>
                    <h1 class="card-form-title">{{ ucfirst($cardType) }} Card Application</h1>
                    <p class="card-form-sub">Complete the form carefully. Application status will be set to Pending Review after submission.</p>
                </div>
                <a href="{{ route('personal.cards') }}" class="card-link">Back to Cards</a>
            </div>

            <form method="POST" action="{{ route('personal.cards.store', ['cardType' => $cardType]) }}" class="card-panel">
                @csrf

                <div class="card-note">All mandatory fields are required.</div>

                <section class="card-section">
                    <h2>Personal Information</h2>
                    <div class="card-grid">
                        <div class="card-field">
                            <label for="full_name">Full Name</label>
                            <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $customer->C_Name ?? $user->name) }}" required>
                            @error('full_name')<div class="err">{{ $message }}</div>@enderror
                        </div>
                        <div class="card-field">
                            <label for="date_of_birth">Date of Birth</label>
                            <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" required>
                            @error('date_of_birth')<div class="err">{{ $message }}</div>@enderror
                        </div>
                        <div class="card-field">
                            <label for="national_id_passport">National ID / Passport Number</label>
                            <input id="national_id_passport" name="national_id_passport" type="text" value="{{ old('national_id_passport') }}" required>
                            @error('national_id_passport')<div class="err">{{ $message }}</div>@enderror
                        </div>
                        <div class="card-field">
                            <label for="contact_number">Contact Number</label>
                            <input id="contact_number" name="contact_number" type="text" value="{{ old('contact_number', $customer->C_PhoneNumber ?? '') }}" required>
                            @error('contact_number')<div class="err">{{ $message }}</div>@enderror
                        </div>
                        <div class="card-field">
                            <label for="email_address">Email Address</label>
                            <input id="email_address" name="email_address" type="email" value="{{ old('email_address', $user->email) }}" required>
                            @error('email_address')<div class="err">{{ $message }}</div>@enderror
                        </div>
                        <div class="card-field">
                            <label for="residential_address">Residential Address</label>
                            <textarea id="residential_address" name="residential_address" required>{{ old('residential_address', $customer->C_Address ?? '') }}</textarea>
                            @error('residential_address')<div class="err">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </section>

                <section class="card-section">
                    <h2>Account Information</h2>
                    <div class="card-grid">
                        <div class="card-field">
                            <label for="existing_account_number">Existing Account Number (If Applicable)</label>
                            <input id="existing_account_number" name="existing_account_number" type="number" value="{{ old('existing_account_number', $account->A_Number ?? '') }}">
                            @error('existing_account_number')<div class="err">{{ $message }}</div>@enderror
                        </div>
                        <div class="card-field">
                            <label for="account_type">Account Type</label>
                            <input id="account_type" name="account_type" type="text" value="{{ old('account_type', $account->account_type ?? 'Personal') }}" required>
                            @error('account_type')<div class="err">{{ $message }}</div>@enderror
                        </div>
                        <div class="card-field">
                            <label for="branch_id">Branch Name</label>
                            <select id="branch_id" name="branch_id" required>
                                <option value="">Select branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->B_ID }}" @selected((string) old('branch_id') === (string) $branch->B_ID)>{{ $branch->B_Name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id')<div class="err">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </section>

                @if ($isCredit)
                    <section class="card-section">
                        <h2>Employment & Financial Information</h2>
                        <div class="card-grid">
                            <div class="card-field">
                                <label for="occupation">Occupation</label>
                                <input id="occupation" name="occupation" type="text" value="{{ old('occupation') }}" required>
                                @error('occupation')<div class="err">{{ $message }}</div>@enderror
                            </div>
                            <div class="card-field">
                                <label for="employer_name">Employer Name</label>
                                <input id="employer_name" name="employer_name" type="text" value="{{ old('employer_name') }}" required>
                                @error('employer_name')<div class="err">{{ $message }}</div>@enderror
                            </div>
                            <div class="card-field">
                                <label for="monthly_income">Monthly Income</label>
                                <input id="monthly_income" name="monthly_income" type="number" min="0" step="0.01" value="{{ old('monthly_income') }}" required>
                                @error('monthly_income')<div class="err">{{ $message }}</div>@enderror
                            </div>
                            <div class="card-field">
                                <label for="source_of_income">Source of Income</label>
                                <input id="source_of_income" name="source_of_income" type="text" value="{{ old('source_of_income') }}" required>
                                @error('source_of_income')<div class="err">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </section>
                @endif

                <section class="card-section">
                    <h2>Card Preferences</h2>
                    <div class="card-grid">
                        <div class="card-field">
                            <label for="card_network">Card Network</label>
                            <select id="card_network" name="card_network" required>
                                <option value="">Select network</option>
                                @foreach (['Visa', 'MasterCard', 'American Express', 'Discover'] as $network)
                                    <option value="{{ $network }}" @selected(old('card_network') === $network)>{{ $network }}</option>
                                @endforeach
                            </select>
                            @error('card_network')<div class="err">{{ $message }}</div>@enderror
                        </div>
                        <div class="card-field">
                            <label for="card_design">Card Design</label>
                            <select id="card_design" name="card_design" required>
                                <option value="">Select design</option>
                                @foreach (['Classic Blue', 'Midnight Black', 'Aurora Silver'] as $design)
                                    <option value="{{ $design }}" @selected(old('card_design') === $design)>{{ $design }}</option>
                                @endforeach
                            </select>
                            @error('card_design')<div class="err">{{ $message }}</div>@enderror
                        </div>
                        <div class="card-field">
                            <label for="delivery_method">Delivery Method</label>
                            <select id="delivery_method" name="delivery_method" required>
                                <option value="">Select delivery method</option>
                                <option value="home_delivery" @selected(old('delivery_method') === 'home_delivery')>Home delivery</option>
                                <option value="branch_pickup" @selected(old('delivery_method') === 'branch_pickup')>Branch pickup</option>
                            </select>
                            @error('delivery_method')<div class="err">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </section>

                <div class="card-form-actions">
                    <button type="submit" class="card-submit">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
