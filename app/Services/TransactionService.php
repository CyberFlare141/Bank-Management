<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    public function transfer(
        int $userId,
        int $customerId,
        int $accountNumber,
        string $recipientIdentifier,
        float $amount,
        string $note = ''
    ): array {
        $normalizedAmount = $this->normalizeAmount($amount);
        $reference = $this->generateReference('FT');

        return $this->runSerializableTransaction(function () use ($userId, $customerId, $accountNumber, $recipientIdentifier, $normalizedAmount, $note, $reference): array {
            $sender = DB::selectOne(
                'SELECT A_Number, C_ID, A_Balance FROM accounts WHERE A_Number = ? FOR UPDATE',
                [$accountNumber]
            );

            if (!$sender) {
                throw ValidationException::withMessages([
                    'transfer' => 'Source account was not found.',
                ]);
            }

            if ($this->hasInsufficientBalance((float) $sender->A_Balance, $normalizedAmount)) {
                throw ValidationException::withMessages([
                    'transfer' => 'Insufficient balance for this transfer.',
                ]);
            }

            $internalRecipient = DB::selectOne(
                'SELECT A_Number, C_ID FROM accounts WHERE A_Number = ? FOR UPDATE',
                [$recipientIdentifier]
            );

            if ($internalRecipient && (int) $internalRecipient->A_Number === (int) $sender->A_Number) {
                throw ValidationException::withMessages([
                    'transfer' => 'You cannot transfer funds to your own account.',
                ]);
            }

            DB::insert(
                'INSERT INTO transactions (A_Number, C_ID, T_Type, T_Amount, T_Date, created_at, updated_at)
                 VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())',
                [(int) $sender->A_Number, $customerId, 'transfer_out', $normalizedAmount]
            );

            if ($internalRecipient) {
                DB::insert(
                    'INSERT INTO transactions (A_Number, C_ID, T_Type, T_Amount, T_Date, created_at, updated_at)
                     VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())',
                    [(int) $internalRecipient->A_Number, (int) $internalRecipient->C_ID, 'transfer_in', $normalizedAmount]
                );
            }

            DB::insert(
                'INSERT INTO quick_actions
                    (reference, user_id, C_ID, A_Number, action_type, channel, recipient_identifier, amount, note, status, meta, performed_at, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())',
                [
                    $reference,
                    $userId,
                    $customerId,
                    (int) $sender->A_Number,
                    'fund_transfer',
                    $internalRecipient ? 'internal' : 'external',
                    $internalRecipient ? (string) $internalRecipient->A_Number : $recipientIdentifier,
                    $normalizedAmount,
                    $note !== '' ? $note : null,
                    'success',
                    json_encode([
                        'internal_recipient' => $internalRecipient ? (int) $internalRecipient->A_Number : null,
                    ], JSON_THROW_ON_ERROR),
                ]
            );

            $updatedSender = DB::selectOne(
                'SELECT A_Balance FROM accounts WHERE A_Number = ?',
                [(int) $sender->A_Number]
            );

            return [
                'reference' => $reference,
                'title' => 'Fund Transfer Completed',
                'amount' => (float) $normalizedAmount,
                'balance' => (float) ($updatedSender->A_Balance ?? 0),
                'meta' => $internalRecipient ? 'Internal account transfer' : 'External account/wallet transfer',
            ];
        });
    }

    public function payBill(
        int $userId,
        int $customerId,
        int $accountNumber,
        string $billType,
        string $billNumber,
        float $amount
    ): array {
        $normalizedAmount = $this->normalizeAmount($amount);
        $reference = $this->generateReference('BILL');

        return $this->runSerializableTransaction(function () use ($userId, $customerId, $accountNumber, $billType, $billNumber, $normalizedAmount, $reference): array {
            $sender = DB::selectOne(
                'SELECT A_Number, A_Balance FROM accounts WHERE A_Number = ? FOR UPDATE',
                [$accountNumber]
            );

            if (!$sender) {
                throw ValidationException::withMessages([
                    'bill' => 'Source account was not found.',
                ]);
            }

            if ($this->hasInsufficientBalance((float) $sender->A_Balance, $normalizedAmount)) {
                throw ValidationException::withMessages([
                    'bill' => 'Insufficient balance to pay this bill.',
                ]);
            }

            DB::insert(
                'INSERT INTO transactions (A_Number, C_ID, T_Type, T_Amount, T_Date, created_at, updated_at)
                 VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())',
                [(int) $sender->A_Number, $customerId, 'bill_payment', $normalizedAmount]
            );

            DB::insert(
                'INSERT INTO quick_actions
                    (reference, user_id, C_ID, A_Number, action_type, channel, bill_type, bill_number, amount, status, performed_at, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())',
                [
                    $reference,
                    $userId,
                    $customerId,
                    (int) $sender->A_Number,
                    'bill_payment',
                    'provider',
                    $billType,
                    $billNumber,
                    $normalizedAmount,
                    'success',
                ]
            );

            $updatedSender = DB::selectOne(
                'SELECT A_Balance FROM accounts WHERE A_Number = ?',
                [(int) $sender->A_Number]
            );

            return [
                'reference' => $reference,
                'title' => 'Bill Paid Successfully',
                'amount' => (float) $normalizedAmount,
                'balance' => (float) ($updatedSender->A_Balance ?? 0),
                'meta' => ucfirst($billType) . ' bill #' . $billNumber,
            ];
        });
    }

    public function recharge(
        int $userId,
        int $customerId,
        int $accountNumber,
        string $rechargeApp,
        string $recipient,
        float $amount
    ): array {
        $normalizedAmount = $this->normalizeAmount($amount);
        $reference = $this->generateReference('RCH');

        return $this->runSerializableTransaction(function () use ($userId, $customerId, $accountNumber, $rechargeApp, $recipient, $normalizedAmount, $reference): array {
            $sender = DB::selectOne(
                'SELECT A_Number FROM accounts WHERE A_Number = ? FOR UPDATE',
                [$accountNumber]
            );

            if (!$sender) {
                throw ValidationException::withMessages([
                    'recharge' => 'Source account was not found.',
                ]);
            }

            DB::insert(
                'INSERT INTO transactions (A_Number, C_ID, T_Type, T_Amount, T_Date, created_at, updated_at)
                 VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())',
                [(int) $sender->A_Number, $customerId, 'recharge_credit', $normalizedAmount]
            );

            DB::insert(
                'INSERT INTO quick_actions
                    (reference, user_id, C_ID, A_Number, action_type, channel, recipient_identifier, provider, amount, status, performed_at, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())',
                [
                    $reference,
                    $userId,
                    $customerId,
                    (int) $sender->A_Number,
                    'recharge',
                    'wallet',
                    $recipient,
                    $rechargeApp,
                    $normalizedAmount,
                    'success',
                ]
            );

            $updatedSender = DB::selectOne(
                'SELECT A_Balance FROM accounts WHERE A_Number = ?',
                [(int) $sender->A_Number]
            );

            return [
                'reference' => $reference,
                'title' => 'Recharge Successful',
                'amount' => (float) $normalizedAmount,
                'balance' => (float) ($updatedSender->A_Balance ?? 0),
                'meta' => $rechargeApp . ' recipient ' . $recipient,
            ];
        });
    }

    private function generateReference(string $prefix): string
    {
        return $prefix . '-' . now()->format('YmdHis') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    private function normalizeAmount(float|int|string $amount): float
    {
        return round((float) $amount, 2);
    }

    private function hasInsufficientBalance(float|int|string $balance, float|int|string $amount): bool
    {
        return $this->toMinorUnits($balance) < $this->toMinorUnits($amount);
    }

    private function toMinorUnits(float|int|string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    /**
     * Run a closure inside a high‑isolation transaction with retry.
     * Uses SERIALIZABLE for core money movements to prevent phantom/dirty reads.
     */
    private function runSerializableTransaction(callable $callback): mixed
    {
        if (DB::transactionLevel() > 0) {
            return $callback();
        }

        return DB::transaction(function () use ($callback) {
            DB::statement('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
            return $callback();
        }, 3);
    }
}
