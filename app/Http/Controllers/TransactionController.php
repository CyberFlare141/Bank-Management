<?php

namespace App\Http\Controllers;

use App\Services\AccountService;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    private const RECHARGE_MAX_AMOUNT = 50000;
    private const BILL_TYPES = [
        'electricity',
        'water',
        'education',
        'utility',
        'government',
    ];
    private const RECHARGE_APPS = [
        'Bkash',
        'Rocket',
        'GPay',
    ];

    public function __construct(
        private readonly TransactionService $transactionService,
        private readonly AccountService $accountService
    ) {
    }

    public function transfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_identifier' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:255'],
            'quick_action_password' => ['required', 'string'],
        ]);

        $user = auth()->user();
        $context = $this->accountService->getUserBankingContext((string) $user->email);
        if (!$context) {
            return $this->quickActionFail('Customer profile or account is missing.');
        }

        if (!Hash::check((string) $validated['quick_action_password'], (string) $user->password)) {
            return $this->quickActionFail('Security password is incorrect.');
        }

        try {
            $result = $this->transactionService->transfer(
                (int) $user->id,
                (int) $context->C_ID,
                (int) $context->A_Number,
                trim((string) $validated['recipient_identifier']),
                (float) $validated['amount'],
                trim((string) ($validated['note'] ?? ''))
            );
        } catch (ValidationException $e) {
            return $this->quickActionFail($e->validator->errors()->first() ?: 'Transfer failed.');
        } catch (\Throwable $e) {
            Log::error('Fund transfer failed.', [
                'user_id' => (int) $user->id,
                'error' => $e->getMessage(),
            ]);
            return $this->quickActionFail('Transfer failed. Please try again.');
        }

        return $this->quickActionSuccess('Fund transfer completed successfully.', $result);
    }

    public function payBill(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bill_type' => ['required', Rule::in(self::BILL_TYPES)],
            'bill_number' => ['required', 'string', 'max:80', 'regex:/^[A-Za-z0-9\-_]+$/'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'quick_action_password' => ['required', 'string'],
        ]);

        $user = auth()->user();
        $context = $this->accountService->getUserBankingContext((string) $user->email);
        if (!$context) {
            return $this->quickActionFail('Customer profile or account is missing.');
        }

        if (!Hash::check((string) $validated['quick_action_password'], (string) $user->password)) {
            return $this->quickActionFail('Security password is incorrect.');
        }

        try {
            $result = $this->transactionService->payBill(
                (int) $user->id,
                (int) $context->C_ID,
                (int) $context->A_Number,
                strtolower((string) $validated['bill_type']),
                trim((string) $validated['bill_number']),
                (float) $validated['amount']
            );
        } catch (ValidationException $e) {
            return $this->quickActionFail($e->validator->errors()->first() ?: 'Bill payment failed.');
        } catch (\Throwable $e) {
            Log::error('Bill payment failed.', [
                'user_id' => (int) $user->id,
                'error' => $e->getMessage(),
            ]);
            return $this->quickActionFail('Bill payment failed. Please try again.');
        }

        return $this->quickActionSuccess('Bill payment completed successfully.', $result);
    }

    public function recharge(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recharge_app' => ['required', Rule::in(self::RECHARGE_APPS)],
            'recipient' => ['required', 'string', 'max:80'],
            'amount' => ['required', 'numeric', 'min:1', 'max:' . self::RECHARGE_MAX_AMOUNT],
            'quick_action_password' => ['required', 'string'],
        ]);

        $user = auth()->user();
        $context = $this->accountService->getUserBankingContext((string) $user->email);
        if (!$context) {
            return $this->quickActionFail('Customer profile or account is missing.');
        }

        if (!Hash::check((string) $validated['quick_action_password'], (string) $user->password)) {
            return $this->quickActionFail('Security password is incorrect.');
        }

        try {
            $result = $this->transactionService->recharge(
                (int) $user->id,
                (int) $context->C_ID,
                (int) $context->A_Number,
                (string) $validated['recharge_app'],
                trim((string) $validated['recipient']),
                (float) $validated['amount']
            );
        } catch (ValidationException $e) {
            return $this->quickActionFail($e->validator->errors()->first() ?: 'Recharge failed.');
        } catch (\Throwable $e) {
            Log::error('Recharge failed.', [
                'user_id' => (int) $user->id,
                'error' => $e->getMessage(),
            ]);
            return $this->quickActionFail('Recharge failed. Please try again.');
        }

        return $this->quickActionSuccess('Recharge completed successfully.', $result);
    }

    private function quickActionSuccess(string $message, array $receipt): RedirectResponse
    {
        return redirect()
            ->route('personal.dashboard')
            ->with('quick_action_success', $message)
            ->with('quick_action_receipt', $receipt);
    }

    private function quickActionFail(string $message): RedirectResponse
    {
        return redirect()
            ->route('personal.dashboard')
            ->with('quick_action_error', $message)
            ->withInput();
    }
}
