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

        $this->ensureDefaultDhakaBranches();
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
            'contact_number' => ['required', 'regex:/^\+?[0-9\s\-()]{8,25}$/'],
            'email_address' => ['required', 'email', 'max:255'],
            'residential_address' => ['required', 'string', 'max:1000'],

            'existing_account_number' => [
                'nullable',
                'integer',
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
            'contact_number.regex' => 'Contact number format is invalid.',
        ]);

        $this->ensureDefaultDhakaBranches();

        $existingAccountNumber = null;
        if ($account && !empty($validated['existing_account_number']) && (int) $validated['existing_account_number'] === (int) $account->A_Number) {
            $existingAccountNumber = (int) $validated['existing_account_number'];
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
            'existing_account_number' => $existingAccountNumber,
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

    private function ensureDefaultDhakaBranches(): void
    {
        if (Branch::query()->exists()) {
            return;
        }

        $branches = [
            ['B_Name' => 'Dhanmondi Branch', 'B_Location' => 'Dhanmondi, Dhaka', 'IFSC_Code' => 'DHKMARS001'],
            ['B_Name' => 'Gulshan Branch', 'B_Location' => 'Gulshan, Dhaka', 'IFSC_Code' => 'DHKMARS002'],
            ['B_Name' => 'Uttara Branch', 'B_Location' => 'Uttara, Dhaka', 'IFSC_Code' => 'DHKMARS003'],
            ['B_Name' => 'Mirpur Branch', 'B_Location' => 'Mirpur, Dhaka', 'IFSC_Code' => 'DHKMARS004'],
            ['B_Name' => 'Motijheel Branch', 'B_Location' => 'Motijheel, Dhaka', 'IFSC_Code' => 'DHKMARS005'],
        ];

        foreach ($branches as $branch) {
            Branch::query()->firstOrCreate(
                ['IFSC_Code' => $branch['IFSC_Code']],
                $branch
            );
        }
    }
}
