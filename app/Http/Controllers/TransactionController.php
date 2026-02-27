<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\QuickAction;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function transfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_identifier' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:255'],
            'quick_action_password' => ['required', 'string'],
        ]);

        $user = auth()->user();
        $customer = $user->customer;
        $account = $user->account;

        if (!$customer || !$account) {
            return $this->quickActionFail('Customer profile or account is missing.');
        }

        if ($passwordError = $this->validateQuickActionPassword((string) $validated['quick_action_password'], (string) $user->password)) {
            return $this->quickActionFail($passwordError);
        }

        $amount = $this->normalizeAmount($validated['amount']);
        $recipientIdentifier = trim((string) $validated['recipient_identifier']);
        $note = trim((string) ($validated['note'] ?? ''));
        $reference = $this->generateReference('FT');

        try {
            $result = DB::transaction(function () use ($user, $account, $customer, $amount, $recipientIdentifier, $note, $reference): array {
                $sender = Account::query()
                    ->where('A_Number', (int) $account->A_Number)
                    ->lockForUpdate()
                    ->first();

                if (!$sender) {
                    throw ValidationException::withMessages([
                        'transfer' => 'Source account was not found.',
                    ]);
                }

                if ($this->hasInsufficientBalance($sender->A_Balance, $amount)) {
                    throw ValidationException::withMessages([
                        'transfer' => 'Insufficient balance for this transfer.',
                    ]);
                }

                $internalRecipient = Account::query()
                    ->where('A_Number', $recipientIdentifier)
                    ->lockForUpdate()
                    ->first();

                if ($internalRecipient && (int) $internalRecipient->A_Number === (int) $sender->A_Number) {
                    throw ValidationException::withMessages([
                        'transfer' => 'You cannot transfer funds to your own account.',
                    ]);
                }

                $sender->decrement('A_Balance', $amount);

                if ($internalRecipient) {
                    $internalRecipient->increment('A_Balance', $amount);
                }

                $senderType = $internalRecipient
                    ? 'Fund Transfer to ' . $internalRecipient->A_Number
                    : 'External Transfer to ' . $recipientIdentifier;

                if ($note !== '') {
                    $senderType .= ' | Note: ' . $note;
                }

                $senderTransaction = Transaction::create([
                    'A_Number' => (int) $sender->A_Number,
                    'C_ID' => (int) $customer->C_ID,
                    'T_Type' => $senderType,
                    'T_Amount' => $amount,
                    'T_Date' => now(),
                ]);

                if ($internalRecipient) {
                    Transaction::create([
                        'A_Number' => (int) $internalRecipient->A_Number,
                        'C_ID' => (int) $internalRecipient->C_ID,
                        'T_Type' => 'Fund Transfer Received from ' . $sender->A_Number,
                        'T_Amount' => $amount,
                        'T_Date' => now(),
                    ]);
                }

                QuickAction::create([
                    'reference' => $reference,
                    'user_id' => (int) $user->id,
                    'C_ID' => (int) $customer->C_ID,
                    'A_Number' => (int) $sender->A_Number,
                    'transaction_id' => (int) $senderTransaction->T_ID,
                    'action_type' => 'fund_transfer',
                    'channel' => $internalRecipient ? 'internal' : 'external',
                    'recipient_identifier' => (string) ($internalRecipient?->A_Number ?? $recipientIdentifier),
                    'amount' => $amount,
                    'note' => $note !== '' ? $note : null,
                    'status' => 'success',
                    'meta' => [
                        'internal_recipient' => $internalRecipient ? (int) $internalRecipient->A_Number : null,
                    ],
                    'performed_at' => now(),
                ]);

                $sender->refresh();

                return [
                    'reference' => $reference,
                    'title' => 'Fund Transfer Completed',
                    'amount' => (float) $amount,
                    'balance' => (float) $sender->A_Balance,
                    'meta' => $internalRecipient ? 'Internal account transfer' : 'External account/wallet transfer',
                ];
            });
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
        $customer = $user->customer;
        $account = $user->account;

        if (!$customer || !$account) {
            return $this->quickActionFail('Customer profile or account is missing.');
        }

        if ($passwordError = $this->validateQuickActionPassword((string) $validated['quick_action_password'], (string) $user->password)) {
            return $this->quickActionFail($passwordError);
        }

        $amount = $this->normalizeAmount($validated['amount']);
        $billType = strtolower((string) $validated['bill_type']);
        $billNumber = trim((string) $validated['bill_number']);
        $reference = $this->generateReference('BILL');

        try {
            $result = DB::transaction(function () use ($user, $account, $customer, $amount, $billType, $billNumber, $reference): array {
                $sender = Account::query()
                    ->where('A_Number', (int) $account->A_Number)
                    ->lockForUpdate()
                    ->first();

                if (!$sender) {
                    throw ValidationException::withMessages([
                        'bill' => 'Source account was not found.',
                    ]);
                }

                if ($this->hasInsufficientBalance($sender->A_Balance, $amount)) {
                    throw ValidationException::withMessages([
                        'bill' => 'Insufficient balance to pay this bill.',
                    ]);
                }

                $sender->decrement('A_Balance', $amount);

                $senderTransaction = Transaction::create([
                    'A_Number' => (int) $sender->A_Number,
                    'C_ID' => (int) $customer->C_ID,
                    'T_Type' => 'Bill Payment - ' . ucfirst($billType) . ' (' . $billNumber . ')',
                    'T_Amount' => $amount,
                    'T_Date' => now(),
                ]);

                QuickAction::create([
                    'reference' => $reference,
                    'user_id' => (int) $user->id,
                    'C_ID' => (int) $customer->C_ID,
                    'A_Number' => (int) $sender->A_Number,
                    'transaction_id' => (int) $senderTransaction->T_ID,
                    'action_type' => 'bill_payment',
                    'channel' => 'provider',
                    'bill_type' => $billType,
                    'bill_number' => $billNumber,
                    'amount' => $amount,
                    'status' => 'success',
                    'performed_at' => now(),
                ]);

                $sender->refresh();

                return [
                    'reference' => $reference,
                    'title' => 'Bill Paid Successfully',
                    'amount' => (float) $amount,
                    'balance' => (float) $sender->A_Balance,
                    'meta' => ucfirst($billType) . ' bill #' . $billNumber,
                ];
            });
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
        $customer = $user->customer;
        $account = $user->account;

        if (!$customer || !$account) {
            return $this->quickActionFail('Customer profile or account is missing.');
        }

        if ($passwordError = $this->validateQuickActionPassword((string) $validated['quick_action_password'], (string) $user->password)) {
            return $this->quickActionFail($passwordError);
        }

        $amount = $this->normalizeAmount($validated['amount']);
        $rechargeApp = (string) $validated['recharge_app'];
        $recipient = trim((string) $validated['recipient']);
        $reference = $this->generateReference('RCH');

        try {
            $result = DB::transaction(function () use ($user, $account, $customer, $amount, $rechargeApp, $recipient, $reference): array {
                $sender = Account::query()
                    ->where('A_Number', (int) $account->A_Number)
                    ->lockForUpdate()
                    ->first();

                if (!$sender) {
                    throw ValidationException::withMessages([
                        'recharge' => 'Source account was not found.',
                    ]);
                }

                $sender->increment('A_Balance', $amount);

                $senderTransaction = Transaction::create([
                    'A_Number' => (int) $sender->A_Number,
                    'C_ID' => (int) $customer->C_ID,
                    'T_Type' => 'Recharge Received - ' . $rechargeApp . ' (' . $recipient . ')',
                    'T_Amount' => $amount,
                    'T_Date' => now(),
                ]);

                QuickAction::create([
                    'reference' => $reference,
                    'user_id' => (int) $user->id,
                    'C_ID' => (int) $customer->C_ID,
                    'A_Number' => (int) $sender->A_Number,
                    'transaction_id' => (int) $senderTransaction->T_ID,
                    'action_type' => 'recharge',
                    'channel' => 'wallet',
                    'recipient_identifier' => $recipient,
                    'provider' => $rechargeApp,
                    'amount' => $amount,
                    'status' => 'success',
                    'performed_at' => now(),
                ]);

                $sender->refresh();

                return [
                    'reference' => $reference,
                    'title' => 'Recharge Successful',
                    'amount' => (float) $amount,
                    'balance' => (float) $sender->A_Balance,
                    'meta' => $rechargeApp . ' recipient ' . $recipient,
                ];
            });
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

    private function validateQuickActionPassword(string $quickActionPassword, string $currentHash): ?string
    {
        if (!Hash::check($quickActionPassword, $currentHash)) {
            return 'Security password is incorrect.';
        }

        return null;
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

    private function generateReference(string $prefix): string
    {
        return $prefix . '-' . now()->format('YmdHis') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    private function normalizeAmount(float|int|string $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }

    private function hasInsufficientBalance(float|int|string $balance, float|int|string $amount): bool
    {
        return $this->toMinorUnits($balance) < $this->toMinorUnits($amount);
    }

    private function toMinorUnits(float|int|string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }
}
