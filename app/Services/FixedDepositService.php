<?php

namespace App\Services;

use App\Models\DepositProduct;
use App\Models\FixedDeposit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FixedDepositService
{
    public function listProducts(bool $activeOnly = true): Collection
    {
        return DepositProduct::query()
            ->when($activeOnly, fn ($query) => $query->where('status', 'active'))
            ->orderBy('term_months')
            ->orderBy('minimum_amount')
            ->get();
    }

    public function createProduct(int $userId, array $payload): DepositProduct
    {
        $payload['created_by'] = $userId;
        $payload['product_code'] = strtoupper((string) $payload['product_code']);

        return DepositProduct::create($payload);
    }

    public function updateProduct(DepositProduct $product, array $payload): DepositProduct
    {
        if (isset($payload['product_code'])) {
            $payload['product_code'] = strtoupper((string) $payload['product_code']);
        }

        $product->fill($payload)->save();

        return $product->fresh();
    }

    public function createFixedDeposit(int $customerId, int $accountNumber, array $payload): FixedDeposit
    {
        /** @var DepositProduct|null $product */
        $product = DepositProduct::query()->find($payload['product_id']);

        if (!$product || $product->status !== 'active') {
            throw ValidationException::withMessages([
                'product_id' => 'Selected deposit product is not available.',
            ]);
        }

        $amount = round((float) $payload['amount'], 2);

        if ($amount < (float) $product->minimum_amount) {
            throw ValidationException::withMessages([
                'amount' => 'Amount is below the minimum deposit amount.',
            ]);
        }

        if ($product->maximum_amount !== null && $amount > (float) $product->maximum_amount) {
            throw ValidationException::withMessages([
                'amount' => 'Amount exceeds the maximum deposit amount.',
            ]);
        }

        return DB::transaction(function () use ($customerId, $accountNumber, $product, $amount): FixedDeposit {
            $account = DB::selectOne(
                'SELECT A_Number, A_Balance FROM accounts WHERE A_Number = ? FOR UPDATE',
                [$accountNumber]
            );

            if (!$account) {
                throw ValidationException::withMessages([
                    'account' => 'Source account was not found.',
                ]);
            }

            if ((float) $account->A_Balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient balance to create this fixed deposit.',
                ]);
            }

            $startedAt = now()->toDateString();
            $maturityDate = now()->addMonths((int) $product->term_months)->toDateString();
            $projectedInterest = $this->calculateProjectedInterest($amount, (float) $product->annual_interest_rate, (int) $product->term_months);
            $maturityAmount = round($amount + $projectedInterest, 2);

            DB::update(
                'UPDATE accounts SET A_Balance = A_Balance - ?, updated_at = NOW() WHERE A_Number = ?',
                [$amount, $accountNumber]
            );

            DB::insert(
                'INSERT INTO transactions (A_Number, C_ID, T_Type, T_Amount, T_Date, created_at, updated_at)
                 VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())',
                [$accountNumber, $customerId, 'Fixed Deposit Booking', $amount]
            );

            $deposit = FixedDeposit::create([
                'product_id' => $product->id,
                'C_ID' => $customerId,
                'A_Number' => $accountNumber,
                'principal_amount' => $amount,
                'annual_interest_rate' => $product->annual_interest_rate,
                'term_months' => $product->term_months,
                'started_at' => $startedAt,
                'maturity_date' => $maturityDate,
                'projected_interest' => $projectedInterest,
                'maturity_amount' => $maturityAmount,
                'status' => 'active',
            ]);

            return $deposit->load('product');
        });
    }

    public function listCustomerDeposits(int $customerId): Collection
    {
        return FixedDeposit::query()
            ->with('product')
            ->where('C_ID', $customerId)
            ->latest('created_at')
            ->get()
            ->map(function (FixedDeposit $deposit) {
                $this->syncMaturityStatus($deposit);

                return $this->appendComputedFields($deposit->fresh(['product']));
            });
    }

    public function getCustomerDeposit(int $customerId, int $depositId): FixedDeposit
    {
        $deposit = FixedDeposit::query()
            ->with('product')
            ->where('C_ID', $customerId)
            ->findOrFail($depositId);

        $this->syncMaturityStatus($deposit);

        return $this->appendComputedFields($deposit->fresh(['product']));
    }

    public function breakOrCloseDeposit(int $customerId, int $depositId): array
    {
        /** @var FixedDeposit $deposit */
        $deposit = FixedDeposit::query()
            ->with('product')
            ->where('C_ID', $customerId)
            ->findOrFail($depositId);

        return DB::transaction(function () use ($deposit): array {
            $deposit->refresh();
            $deposit->load('product');
            $this->syncMaturityStatus($deposit);

            if (in_array($deposit->status, ['closed', 'broken'], true)) {
                throw ValidationException::withMessages([
                    'deposit' => 'This fixed deposit has already been settled.',
                ]);
            }

            $account = DB::selectOne(
                'SELECT A_Number FROM accounts WHERE A_Number = ? FOR UPDATE',
                [$deposit->A_Number]
            );

            if (!$account) {
                throw ValidationException::withMessages([
                    'account' => 'Linked account was not found.',
                ]);
            }

            $isMatured = Carbon::parse($deposit->maturity_date)->isPast() || Carbon::parse($deposit->maturity_date)->isToday();
            $interest = $isMatured
                ? (float) $deposit->projected_interest
                : $this->calculateAccruedInterest($deposit);
            $penalty = 0.0;
            $transactionType = 'Fixed Deposit Maturity Payout';
            $newStatus = 'closed';
            $closureReason = 'maturity';

            if (!$isMatured) {
                if (!$deposit->product->allow_early_break) {
                    throw ValidationException::withMessages([
                        'deposit' => 'This product cannot be broken before maturity.',
                    ]);
                }

                $penalty = round($interest * ((float) $deposit->product->early_break_penalty_rate / 100), 2);
                $transactionType = 'Fixed Deposit Early Break Payout';
                $newStatus = 'broken';
                $closureReason = 'early_break';
            }

            $netInterest = max(round($interest - $penalty, 2), 0);
            $payout = round((float) $deposit->principal_amount + $netInterest, 2);

            DB::update(
                'UPDATE accounts SET A_Balance = A_Balance + ?, updated_at = NOW() WHERE A_Number = ?',
                [$payout, $deposit->A_Number]
            );

            DB::insert(
                'INSERT INTO transactions (A_Number, C_ID, T_Type, T_Amount, T_Date, created_at, updated_at)
                 VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())',
                [$deposit->A_Number, $deposit->C_ID, $transactionType, $payout]
            );

            $deposit->update([
                'status' => $newStatus,
                'interest_paid' => $netInterest,
                'penalty_amount' => $penalty,
                'payout_amount' => $payout,
                'closure_reason' => $closureReason,
                'closed_at' => now(),
            ]);

            return [
                'deposit' => $this->appendComputedFields($deposit->fresh(['product'])),
                'settlement' => [
                    'principal_amount' => round((float) $deposit->principal_amount, 2),
                    'gross_interest' => round($interest, 2),
                    'penalty_amount' => $penalty,
                    'net_interest' => $netInterest,
                    'payout_amount' => $payout,
                    'status' => $newStatus,
                ],
            ];
        });
    }

    public function calculateProjectedInterest(float $principal, float $annualInterestRate, int $termMonths): float
    {
        return round($principal * ($annualInterestRate / 100) * ($termMonths / 12), 2);
    }

    private function calculateAccruedInterest(FixedDeposit $deposit): float
    {
        $start = Carbon::parse($deposit->started_at);
        $maturity = Carbon::parse($deposit->maturity_date);
        $elapsedDays = max($start->diffInDays(now()), 0);
        $totalDays = max($start->diffInDays($maturity), 1);

        return round(((float) $deposit->projected_interest) * min($elapsedDays / $totalDays, 1), 2);
    }

    private function syncMaturityStatus(FixedDeposit $deposit): void
    {
        if ($deposit->status === 'active' && (Carbon::parse($deposit->maturity_date)->isPast() || Carbon::parse($deposit->maturity_date)->isToday())) {
            $deposit->update(['status' => 'matured']);
        }
    }

    private function appendComputedFields(FixedDeposit $deposit): FixedDeposit
    {
        $maturityDate = Carbon::parse($deposit->maturity_date);
        $today = now()->startOfDay();
        $isSettled = in_array($deposit->status, ['closed', 'broken'], true);
        $grossInterest = $isSettled
            ? ((float) $deposit->interest_paid + (float) $deposit->penalty_amount)
            : ($deposit->status === 'matured'
                ? (float) $deposit->projected_interest
                : $this->calculateAccruedInterest($deposit));

        $deposit->setAttribute('days_until_maturity', $isSettled ? 0 : max($today->diffInDays($maturityDate, false), 0));
        $deposit->setAttribute('is_matured', $deposit->status === 'matured' || ($isSettled && $deposit->closure_reason === 'maturity'));
        $deposit->setAttribute('gross_interest', round($grossInterest, 2));
        $deposit->setAttribute('current_value', round((float) $deposit->principal_amount + max($grossInterest - (float) $deposit->penalty_amount, 0), 2));

        return $deposit;
    }
}
