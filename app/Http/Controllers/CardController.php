<?php

namespace App\Http\Controllers;

use App\Services\AccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class CardController extends Controller
{
    public function __construct(private readonly AccountService $accountService)
    {
    }

    public function index(): View
    {
        $user = auth()->user();
        $context = $this->accountService->getUserBankingContext((string) $user->email);
        $applications = $context
            ? $this->accountService->getCardApplications((int) $context->C_ID)
            : [];

        $applicationNotifications = $user->unreadNotifications()
            ->latest()
            ->limit(5)
            ->get();

        return view('personal.cards.index', [
            'applications' => collect($applications),
            'hasBankingProfile' => (bool) $context,
            'applicationNotifications' => $applicationNotifications,
        ]);
    }

    public function create(string $cardType): View
    {
        abort_unless(in_array($cardType, ['debit', 'credit'], true), 404);

        $user = auth()->user();
        $context = $this->accountService->getUserBankingContext((string) $user->email);

        return view('personal.cards.apply', [
            'cardType' => $cardType,
            'user' => $user,
            'customer' => $context,
            'account' => $context,
            'branches' => collect($this->accountService->getBranchesWithBootstrap()),
        ]);
    }

    public function store(Request $request, string $cardType): RedirectResponse
    {
        abort_unless(in_array($cardType, ['debit', 'credit'], true), 404);

        $user = auth()->user();
        $context = $this->accountService->getUserBankingContext((string) $user->email);

        if (!$context) {
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
            'existing_account_number' => ['nullable', 'integer'],
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

        $existingAccountNumber = null;
        if (!empty($validated['existing_account_number']) && (int) $validated['existing_account_number'] === (int) $context->A_Number) {
            $existingAccountNumber = (int) $validated['existing_account_number'];
        }

        try {
            $applicationId = $this->accountService->createCardApplication((int) $context->C_ID, [
                'card_category' => $cardType,
                'branch_id' => (int) $validated['branch_id'],
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
                'occupation' => $validated['occupation'] ?? null,
                'employer_name' => $validated['employer_name'] ?? null,
                'monthly_income' => $validated['monthly_income'] ?? null,
                'source_of_income' => $validated['source_of_income'] ?? null,
            ]);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('card_error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Card application failed.', [
                'user_id' => (int) $user->id,
                'error' => $e->getMessage(),
            ]);
            return back()->withInput()->with('card_error', 'Application failed. Please try again.');
        }

        Log::info('Card application submitted.', [
            'user_id' => (int) $user->id,
            'customer_id' => (int) $context->C_ID,
            'card_category' => $cardType,
        ]);

        return redirect()
            ->route('personal.cards')
            ->with('card_success', 'Application submitted successfully. Your Application ID is ' . $applicationId . '. Status: Pending Review.');
    }
}
