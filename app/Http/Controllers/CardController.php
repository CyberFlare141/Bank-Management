<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CardApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $customer = $user->customer;

        $applications = $customer
            ? CardApplication::query()
                ->where('C_ID', (int) $customer->C_ID)
                ->latest('created_at')
                ->get()
            : collect();

        return view('personal.cards.index', [
            'applications' => $applications,
            'hasBankingProfile' => (bool) ($user->customer && $user->account),
        ]);
    }

    public function create(string $cardType): View
    {
        abort_unless(in_array($cardType, ['debit', 'credit'], true), 404);

        $user = auth()->user();
        $customer = $user->customer;
        $account = $user->account;

        $branches = Branch::query()->orderBy('B_Name')->get();

        return view('personal.cards.apply', [
            'cardType' => $cardType,
            'user' => $user,
            'customer' => $customer,
            'account' => $account,
            'branches' => $branches,
        ]);
    }

    public function store(Request $request, string $cardType): RedirectResponse
    {
        abort_unless(in_array($cardType, ['debit', 'credit'], true), 404);

        $user = auth()->user();
        $customer = $user->customer;
        $account = $user->account;

        if (!$customer) {
            return back()
                ->withInput()
                ->with('card_error', 'Customer profile is missing. Please complete your profile first.');
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'national_id_passport' => ['required', 'string', 'max:60'],
            'contact_number' => ['required', 'regex:/^[0-9]{8,20}$/'],
            'email_address' => ['required', 'email', 'max:255'],
            'residential_address' => ['required', 'string', 'max:1000'],

            'existing_account_number' => [
                'nullable',
                'integer',
                Rule::exists('accounts', 'A_Number'),
            ],
            'account_type' => ['required', 'string', 'max:100'],
            'branch_id' => ['required', 'integer', Rule::exists('branches', 'B_ID')],

            'card_network' => ['required', Rule::in(['Visa', 'MasterCard', 'American Express', 'Discover'])],
            'card_design' => ['required', Rule::in(['Classic Blue', 'Midnight Black', 'Aurora Silver'])],
            'delivery_method' => ['required', Rule::in(['home_delivery', 'branch_pickup'])],

            'occupation' => [$cardType === 'credit' ? 'required' : 'nullable', 'string', 'max:255'],
            'employer_name' => [$cardType === 'credit' ? 'required' : 'nullable', 'string', 'max:255'],
            'monthly_income' => [$cardType === 'credit' ? 'required' : 'nullable', 'numeric', 'min:0'],
            'source_of_income' => [$cardType === 'credit' ? 'required' : 'nullable', 'string', 'max:255'],
        ], [
            'contact_number.regex' => 'Contact number must contain only digits (8 to 20 characters).',
        ]);

        if ($account && !empty($validated['existing_account_number'])) {
            if ((int) $validated['existing_account_number'] !== (int) $account->A_Number) {
                return back()
                    ->withInput()
                    ->withErrors(['existing_account_number' => 'The selected account does not belong to your profile.']);
            }
        }

        $branch = Branch::query()->findOrFail((int) $validated['branch_id']);
        $applicationId = $this->generateApplicationId();

        CardApplication::create([
            'C_ID' => (int) $customer->C_ID,
            'B_ID' => (int) $branch->B_ID,
            'application_id' => $applicationId,
            'card_category' => $cardType,
            'card_network' => $validated['card_network'],
            'card_design' => $validated['card_design'],
            'delivery_method' => $validated['delivery_method'],
            'full_name' => $validated['full_name'],
            'date_of_birth' => $validated['date_of_birth'],
            'national_id_passport' => $validated['national_id_passport'],
            'contact_number' => $validated['contact_number'],
            'email_address' => $validated['email_address'],
            'residential_address' => $validated['residential_address'],
            'existing_account_number' => $validated['existing_account_number'] ?? null,
            'account_type' => $validated['account_type'],
            'branch_name' => $branch->B_Name,
            'occupation' => $validated['occupation'] ?? null,
            'employer_name' => $validated['employer_name'] ?? null,
            'monthly_income' => $validated['monthly_income'] ?? null,
            'source_of_income' => $validated['source_of_income'] ?? null,
            'status' => 'pending_review',
        ]);

        Log::info('Card application submitted.', [
            'user_id' => (int) $user->id,
            'customer_id' => (int) $customer->C_ID,
            'application_id' => $applicationId,
            'card_category' => $cardType,
        ]);

        return redirect()
            ->route('personal.cards')
            ->with('card_success', 'Application submitted successfully. Your Application ID is ' . $applicationId . '. Status: Pending Review.');
    }

    private function generateApplicationId(): string
    {
        do {
            $reference = 'CARD-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        } while (CardApplication::query()->where('application_id', $reference)->exists());

        return $reference;
    }
}
