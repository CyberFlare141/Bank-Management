<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    private const MONTHLY_INTEREST_ELIGIBLE_MAX_AMOUNT = 30000;
    private const MONTHLY_INTEREST_RATE = 0.09;
    private const PROCESSING_DELAY_SECONDS = 10;

    public function approvedLoanTotalsByBranch(): array
    {
        return DB::select(
            'SELECT
                b.B_ID,
                b.B_Name,
                b.IFSC_Code,
                COUNT(l.L_ID) AS approved_loan_count,
                COALESCE(SUM(l.L_Amount), 0) AS total_approved_loan_amount
             FROM branches b
             LEFT JOIN loans l ON l.B_ID = b.B_ID AND LOWER(l.status) = ?
             GROUP BY b.B_ID, b.B_Name, b.IFSC_Code
             ORDER BY total_approved_loan_amount DESC, b.B_ID',
            ['approved']
        );
    }

    public function customersWithActiveLoans(): array
    {
        return DB::select(
            'SELECT
                c.C_ID,
                c.C_Name,
                c.C_Email,
                l.L_ID,
                l.L_Type,
                l.L_Amount AS loan_amount,
                COALESCE(l.remaining_amount, l.L_Amount) AS remaining_balance,
                l.status AS loan_status
             FROM loans l
             JOIN customers c ON c.C_ID = l.C_ID
             WHERE LOWER(l.status) IN (?, ?, ?, ?)
               AND COALESCE(l.remaining_amount, l.L_Amount) > 0
             ORDER BY remaining_balance DESC, l.L_ID',
            ['approved', 'active', 'ongoing', 'in_progress']
        );
    }

    public function processPendingLoanRequests(int $customerId, int $accountNumber): void
    {
        $pendingRequests = DB::select(
            'SELECT LR_ID, B_ID, requested_amount
             FROM loan_requests
             WHERE C_ID = ?
               AND status = ?
               AND created_at <= ?
             ORDER BY created_at ASC',
            [
                $customerId,
                'processing',
                now()->subSeconds(self::PROCESSING_DELAY_SECONDS),
            ]
        );

        foreach ($pendingRequests as $loanRequest) {
            $this->runSerializableTransaction(function () use ($loanRequest, $customerId, $accountNumber): void {
                $lockedRequest = DB::selectOne(
                    'SELECT LR_ID, B_ID, requested_amount, status
                     FROM loan_requests
                     WHERE LR_ID = ?
                     FOR UPDATE',
                    [(int) $loanRequest->LR_ID]
                );

                if (!$lockedRequest || strtolower((string) $lockedRequest->status) !== 'processing') {
                    return;
                }

                $lockedCustomer = DB::selectOne(
                    'SELECT C_ID FROM customers WHERE C_ID = ? FOR UPDATE',
                    [$customerId]
                );

                if (!$lockedCustomer || $this->hasOutstandingLoan($customerId, true)) {
                    DB::update(
                        'UPDATE loan_requests
                         SET status = ?, decision_note = ?, processed_at = NOW(), updated_at = NOW()
                         WHERE LR_ID = ?',
                        ['rejected', 'Rejected because there is an existing unpaid loan.', (int) $lockedRequest->LR_ID]
                    );
                    return;
                }

                $lockedAccount = DB::selectOne(
                    'SELECT A_Number FROM accounts WHERE A_Number = ? AND C_ID = ? FOR UPDATE',
                    [$accountNumber, $customerId]
                );

                if (!$lockedAccount) {
                    DB::update(
                        'UPDATE loan_requests
                         SET status = ?, decision_note = ?, processed_at = NOW(), updated_at = NOW()
                         WHERE LR_ID = ?',
                        ['rejected', 'Rejected because no valid account exists for this customer.', (int) $lockedRequest->LR_ID]
                    );
                    return;
                }

                DB::insert(
                    'INSERT INTO loans
                        (C_ID, B_ID, L_Type, L_Amount, remaining_amount, Interest_Rate, status, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
                    [
                        $customerId,
                        (int) $lockedRequest->B_ID,
                        'Instant Tk ' . number_format((float) $lockedRequest->requested_amount, 2) . ' Loan',
                        (float) $lockedRequest->requested_amount,
                        (float) $lockedRequest->requested_amount,
                        0,
                        'active',
                    ]
                );

                $newLoanId = (int) DB::getPdo()->lastInsertId();

                DB::insert(
                    'INSERT INTO transactions (A_Number, C_ID, T_Type, T_Amount, T_Date, created_at, updated_at)
                     VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())',
                    [$accountNumber, $customerId, 'loan_disbursement', (float) $lockedRequest->requested_amount]
                );

                DB::update(
                    'UPDATE loan_requests
                     SET status = ?, approved_loan_id = ?, decision_note = ?, processed_at = NOW(), updated_at = NOW()
                     WHERE LR_ID = ?',
                    ['accepted', $newLoanId, 'Accepted and disbursed to account.', (int) $lockedRequest->LR_ID]
                );
            });
        }
    }

    public function resolveBranchIdForLoan(string $customerEmail): ?int
    {
        $existingBranch = DB::selectOne(
            'SELECT l.B_ID
             FROM loans l
             JOIN customers c ON c.C_ID = l.C_ID
             WHERE c.C_Email = ?
             ORDER BY l.created_at DESC
             LIMIT 1',
            [$customerEmail]
        );

        if ($existingBranch) {
            return (int) $existingBranch->B_ID;
        }

        $branch = DB::selectOne('SELECT B_ID FROM branches ORDER BY B_ID ASC LIMIT 1');
        return $branch ? (int) $branch->B_ID : null;
    }

    public function hasOutstandingLoan(int $customerId, bool $lock = false): bool
    {
        $lockClause = $lock && DB::transactionLevel() > 0 ? ' FOR UPDATE' : '';
        $row = DB::selectOne(
            'SELECT L_ID
             FROM loans
             WHERE C_ID = ?
               AND (
                    remaining_amount > 0
                    OR (remaining_amount IS NULL AND L_Amount > 0)
               )
             LIMIT 1' . $lockClause,
            [$customerId]
        );

        return $row !== null;
    }

    public function disburseInstantLoan(int $customerId, int $accountNumber, int $branchId, float $requestedAmount): void
    {
        $this->runSerializableTransaction(function () use ($customerId, $accountNumber, $branchId, $requestedAmount): void {
            DB::selectOne(
                'SELECT C_ID FROM customers WHERE C_ID = ? FOR UPDATE',
                [$customerId]
            );

            $lockedAccount = DB::selectOne(
                'SELECT A_Number FROM accounts WHERE A_Number = ? AND C_ID = ? FOR UPDATE',
                [$accountNumber, $customerId]
            );

            if (!$lockedAccount) {
                throw ValidationException::withMessages([
                    'loan' => 'Account not found for this customer.',
                ]);
            }

            if ($this->hasOutstandingLoan($customerId, true)) {
                throw ValidationException::withMessages([
                    'loan' => 'You already have an unpaid loan. Repay it before requesting a new loan.',
                ]);
            }

            DB::insert(
                'INSERT INTO loans
                    (C_ID, B_ID, L_Type, L_Amount, remaining_amount, Interest_Rate, status, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
                [
                    $customerId,
                    $branchId,
                    'Instant Tk ' . number_format($requestedAmount, 2) . ' Loan',
                    $requestedAmount,
                    $requestedAmount,
                    0,
                    'processing',
                ]
            );

            $loanId = (int) DB::getPdo()->lastInsertId();
            DB::update(
                'UPDATE loans SET status = ?, updated_at = NOW() WHERE L_ID = ?',
                ['approved', $loanId]
            );

            DB::insert(
                'INSERT INTO loan_requests
                    (C_ID, B_ID, requested_amount, status, decision_note, processed_at, approved_loan_id, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, NOW(), ?, NOW(), NOW())',
                [
                    $customerId,
                    $branchId,
                    $requestedAmount,
                    'accepted',
                    'Approved after password and OTP verification.',
                    $loanId,
                ]
            );
        });
    }

    public function repayLoan(int $customerId, int $accountNumber, int $loanId, float $repaymentAmount): array
    {
        return $this->runSerializableTransaction(function () use ($customerId, $accountNumber, $loanId, $repaymentAmount): array {
            $loan = DB::selectOne(
                'SELECT L_ID, L_Amount, remaining_amount, status
                 FROM loans
                 WHERE L_ID = ? AND C_ID = ?
                 FOR UPDATE',
                [$loanId, $customerId]
            );

            if (!$loan) {
                throw ValidationException::withMessages([
                    'loan' => 'Selected loan was not found for this account.',
                ]);
            }

            $this->applyMonthlyInterestToLoanId((int) $loan->L_ID);

            $freshLoan = DB::selectOne(
                'SELECT L_ID, L_Amount, remaining_amount FROM loans WHERE L_ID = ? FOR UPDATE',
                [$loanId]
            );

            $rawRemaining = (float) ($freshLoan->remaining_amount ?? $freshLoan->L_Amount);
            if ($rawRemaining <= 0) {
                throw ValidationException::withMessages([
                    'loan' => 'This loan is already fully paid.',
                ]);
            }

            $account = DB::selectOne(
                'SELECT A_Number, A_Balance FROM accounts WHERE A_Number = ? AND C_ID = ? FOR UPDATE',
                [$accountNumber, $customerId]
            );

            if (!$account) {
                throw ValidationException::withMessages([
                    'loan' => 'Customer profile or account is missing.',
                ]);
            }

            $appliedRepayment = min($repaymentAmount, $rawRemaining);
            if ((float) $account->A_Balance < $appliedRepayment) {
                throw ValidationException::withMessages([
                    'loan' => 'Insufficient account balance for this repayment.',
                ]);
            }

            $newRemaining = max($rawRemaining - $appliedRepayment, 0);
            DB::update(
                'UPDATE loans
                 SET remaining_amount = ?, status = ?, updated_at = NOW()
                 WHERE L_ID = ?',
                [$newRemaining, $newRemaining > 0 ? 'active' : 'closed', $loanId]
            );

            DB::insert(
                'INSERT INTO transactions (A_Number, C_ID, T_Type, T_Amount, T_Date, created_at, updated_at)
                 VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())',
                [$accountNumber, $customerId, 'loan_repayment', $appliedRepayment]
            );

            return [
                'requested_repayment' => $repaymentAmount,
                'applied_repayment' => $appliedRepayment,
            ];
        });
    }

    public function applyMonthlyInterestForLoans(array $loans): void
    {
        foreach ($loans as $loan) {
            $this->applyMonthlyInterestToLoanId((int) $loan->L_ID);
        }
    }

    public function buildLoanSummary(array $loans, float $accountBalance): array
    {
        $totalLoanTaken = 0.0;
        $remainingLoanBalance = 0.0;
        $activeLoans = [];

        foreach ($loans as $loan) {
            $loanAmount = (float) $loan->L_Amount;
            $remaining = (float) ($loan->remaining_amount ?? $loan->L_Amount);
            $status = strtolower((string) ($loan->status ?? 'active'));

            $totalLoanTaken += $loanAmount;
            $remainingLoanBalance += $remaining;

            if (in_array($status, ['active', 'ongoing', 'in_progress'], true) || $remaining > 0) {
                $activeLoans[] = $loan;
            }
        }

        $totalRepaid = max($totalLoanTaken - $remainingLoanBalance, 0);
        $availableMoney = $accountBalance - $remainingLoanBalance;

        return [
            $activeLoans,
            [
                'total_loan_taken' => $totalLoanTaken,
                'total_repaid' => $totalRepaid,
                'remaining_loan_balance' => $remainingLoanBalance,
                'available_money' => $availableMoney,
            ],
        ];
    }

    public function approveLoanRequest(int $loanRequestId): void
    {
        $this->runSerializableTransaction(function () use ($loanRequestId): void {
            $loanRequest = DB::selectOne(
                'SELECT LR_ID, C_ID, B_ID, requested_amount, status
                 FROM loan_requests
                 WHERE LR_ID = ?
                 FOR UPDATE',
                [$loanRequestId]
            );

            if (!$loanRequest || strtolower((string) $loanRequest->status) !== 'processing') {
                throw new \RuntimeException('Loan request is not eligible for approval.');
            }

            $account = DB::selectOne(
                'SELECT A_Number FROM accounts WHERE C_ID = ? FOR UPDATE',
                [(int) $loanRequest->C_ID]
            );

            if (!$account) {
                throw new \RuntimeException('No account found for this customer.');
            }

            DB::insert(
                'INSERT INTO loans
                    (C_ID, B_ID, L_Type, L_Amount, remaining_amount, Interest_Rate, status, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
                [
                    (int) $loanRequest->C_ID,
                    (int) $loanRequest->B_ID,
                    'standard',
                    (float) $loanRequest->requested_amount,
                    (float) $loanRequest->requested_amount,
                    9.00,
                    'processing',
                ]
            );

            $loanId = (int) DB::getPdo()->lastInsertId();
            DB::update(
                'UPDATE loans SET status = ?, updated_at = NOW() WHERE L_ID = ?',
                ['approved', $loanId]
            );

            DB::update(
                'UPDATE loan_requests
                 SET status = ?, processed_at = NOW(), approved_loan_id = ?, updated_at = NOW()
                 WHERE LR_ID = ?',
                ['approved', $loanId, $loanRequestId]
            );
        });
    }

    private function applyMonthlyInterestToLoanId(int $loanId): void
    {
        $this->runSerializableTransaction(function () use ($loanId): void {
            $loan = DB::selectOne(
                'SELECT L_ID, L_Amount, remaining_amount, status, created_at, last_interest_applied_at
                 FROM loans
                 WHERE L_ID = ?
                 FOR UPDATE',
                [$loanId]
            );

            if (!$loan) {
                return;
            }

            $originalAmount = (float) $loan->L_Amount;
            $remainingAmount = (float) ($loan->remaining_amount ?? $loan->L_Amount);
            $status = strtolower((string) ($loan->status ?? 'active'));

            if ($originalAmount > self::MONTHLY_INTEREST_ELIGIBLE_MAX_AMOUNT || $remainingAmount <= 0) {
                return;
            }

            if (!in_array($status, ['active', 'ongoing', 'in_progress'], true)) {
                return;
            }

            $appliedBase = $loan->last_interest_applied_at
                ? Carbon::parse($loan->last_interest_applied_at)
                : Carbon::parse($loan->created_at);

            $monthsElapsed = max(0, $appliedBase->diffInMonths(now()));
            if ($monthsElapsed < 1) {
                return;
            }

            $newRemaining = $remainingAmount;
            for ($i = 0; $i < $monthsElapsed; $i++) {
                $newRemaining = round($newRemaining * (1 + self::MONTHLY_INTEREST_RATE), 2);
            }

            DB::update(
                'UPDATE loans
                 SET remaining_amount = ?, Interest_Rate = ?, last_interest_applied_at = ?, updated_at = NOW()
                 WHERE L_ID = ?',
                [
                    $newRemaining,
                    9,
                    $appliedBase->copy()->addMonthsNoOverflow($monthsElapsed)->toDateTimeString(),
                    $loanId,
                ]
            );
        });
    }

    /**
     * Run money-sensitive work inside a SERIALIZABLE transaction with retry.
     * Falls back to the current transaction when already inside one to avoid nested isolation issues.
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
