<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use App\Mail\LoanOtpMail;

class LoanController extends Controller
{
    private const INSTANT_LOAN_MIN_AMOUNT = 5000;
    private const INSTANT_LOAN_MAX_AMOUNT = 30000;
    private const MONTHLY_INTEREST_ELIGIBLE_MAX_AMOUNT = 30000;
    private const MONTHLY_INTEREST_RATE = 0.09;
    private const PROCESSING_DELAY_SECONDS = 10;
    private const OTP_EXPIRY_MINUTES = 5;
    private const OTP_MAX_ATTEMPTS = 3;

    public function index(): View
    {
        $user = auth()->user();
        $customer = $user->customer;
        $account = $user->account;

        if ($customer && $account) {
            $this->processPendingLoanRequests($customer->C_ID, $account->A_Number);
        }

        $user->load([
            'account',
            'loans' => fn ($query) => $query->latest('created_at'),
        ]);

        $this->applyMonthlyInterestForEligibleLoans($user->loans);
        $user->load('loans');

        $loanRequests = $customer
            ? LoanRequest::query()
                ->where('C_ID', $customer->C_ID)
                ->latest('created_at')
                ->get()
            : collect();

        [$activeLoans, $loanSummary] = $this->buildLoanSummary($user->loans, (float) ($user->account->A_Balance ?? 0));

        return view('personal.loan', [
            'account' => $user->account,
            'loans' => $user->loans,
            'activeLoans' => $activeLoans,
            'loanSummary' => $loanSummary,
            'loanRequests' => $loanRequests,
            'canRequestLoan' => (bool) ($customer && $account),
            'hasOtpEmail' => $this->resolveOtpEmail($user) !== '',
            'instantLoanMinAmount' => self::INSTANT_LOAN_MIN_AMOUNT,
            'instantLoanMaxAmount' => self::INSTANT_LOAN_MAX_AMOUNT,
        ]);
    }

    public function requestPasswordVerification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'requested_amount' => [
                'required',
                'numeric',
                'min:' . self::INSTANT_LOAN_MIN_AMOUNT,
                'max:' . self::INSTANT_LOAN_MAX_AMOUNT,
            ],
            'password' => ['required', 'string'],
        ]);

        $user = auth()->user();
        $customer = $user->customer;
        $account = $user->account;

        if (!$customer || !$account) {
            return response()->json([
                'message' => 'Customer profile or account is missing. Please complete your profile and create an account first.',
            ], 422);
        }

        if (!Hash::check((string) $validated['password'], (string) $user->password)) {
            return response()->json([
                'message' => 'Incorrect account password.',
            ], 422);
        }

        $otpEmail = $this->resolveOtpEmail($user);
        if ($otpEmail === '') {
            return response()->json([
                'message' => 'No registered email found for this user. Please update your profile email first.',
            ], 422);
        }

        $requestedAmount = round((float) $validated['requested_amount'], 2);
        $branchId = $this->resolveBranchIdForLoan($user);

        if (!$branchId) {
            return response()->json([
                'message' => 'No branch is available to issue a loan.',
            ], 422);
        }

        if ($this->hasOutstandingLoan((int) $customer->C_ID)) {
            return response()->json([
                'message' => 'You already have an unpaid loan. Repay it before requesting a new loan.',
            ], 422);
        }

        $otp = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(self::OTP_EXPIRY_MINUTES);
        Cache::put($this->otpCacheKey((int) $user->id), [
            'customer_id' => (int) $customer->C_ID,
            'account_number' => (int) $account->A_Number,
            'branch_id' => (int) $branchId,
            'requested_amount' => $requestedAmount,
            'otp_hash' => Hash::make($otp),
            'attempts_left' => self::OTP_MAX_ATTEMPTS,
            'expires_at' => $expiresAt->timestamp,
        ], $expiresAt);

        try {
            $this->sendLoanOtpToEmail($otpEmail, $otp, (int) $user->id);
        } catch (\Throwable $e) {
            Log::error('Failed to send loan OTP.', [
                'user_id' => (int) $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to send OTP. Please try again.',
            ], 500);
        }

        return response()->json([
            'message' => 'OTP sent to your registered email.',
            'masked_email' => $this->maskEmail($otpEmail),
            'expires_in_seconds' => self::OTP_EXPIRY_MINUTES * 60,
        ]);
    }

    public function verifyOtpAndDisburse(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $user = auth()->user();
        $cacheKey = $this->otpCacheKey((int) $user->id);
        $payload = Cache::get($cacheKey);

        if (!$payload) {
            return response()->json([
                'message' => 'OTP session expired. Please start again.',
            ], 422);
        }

        if (now()->timestamp > (int) $payload['expires_at']) {
            Cache::forget($cacheKey);

            return response()->json([
                'message' => 'OTP expired. Please request a new OTP.',
            ], 422);
        }

        $attemptsLeft = (int) ($payload['attempts_left'] ?? 0);
        if ($attemptsLeft <= 0) {
            Cache::forget($cacheKey);

            return response()->json([
                'message' => 'Maximum OTP attempts reached. Please start again.',
            ], 429);
        }

        if (!Hash::check((string) $validated['otp'], (string) $payload['otp_hash'])) {
            $payload['attempts_left'] = $attemptsLeft - 1;
            $remaining = (int) $payload['attempts_left'];

            if ($remaining <= 0) {
                Cache::forget($cacheKey);

                return response()->json([
                    'message' => 'Invalid OTP. Maximum attempts reached.',
                ], 429);
            }

            Cache::put($cacheKey, $payload, now()->timestamp >= (int) $payload['expires_at']
                ? now()
                : now()->setTimestamp((int) $payload['expires_at']));

            return response()->json([
                'message' => 'Invalid OTP.',
                'attempts_remaining' => $remaining,
            ], 422);
        }

        $lock = Cache::lock('loan-disbursement-user-' . (int) $user->id, 10);

        if (!$lock->get()) {
            return response()->json([
                'message' => 'A loan request is already being processed. Please wait.',
            ], 429);
        }

        try {
            $customer = $user->customer;
            $account = $user->account;

            if (!$customer || !$account) {
                return response()->json([
                    'message' => 'Customer profile or account is missing. Please complete your profile and create an account first.',
                ], 422);
            }

            $requestedAmount = round((float) ($payload['requested_amount'] ?? 0), 2);
            if ($requestedAmount < self::INSTANT_LOAN_MIN_AMOUNT || $requestedAmount > self::INSTANT_LOAN_MAX_AMOUNT) {
                Cache::forget($cacheKey);

                return response()->json([
                    'message' => 'Invalid loan amount.',
                ], 422);
            }

            if ($this->hasOutstandingLoan((int) $customer->C_ID)) {
                Cache::forget($cacheKey);

                return response()->json([
                    'message' => 'You already have an unpaid loan. Repay it before requesting a new loan.',
                ], 422);
            }

            DB::transaction(function () use ($customer, $account, $payload, $requestedAmount): void {
                $approvedLoan = Loan::create([
                    'C_ID' => (int) $customer->C_ID,
                    'B_ID' => (int) $payload['branch_id'],
                    'L_Type' => 'Instant Tk ' . number_format($requestedAmount, 2) . ' Loan',
                    'L_Amount' => $requestedAmount,
                    'remaining_amount' => $requestedAmount,
                    'Interest_Rate' => 0,
                    'status' => 'active',
                ]);

                DB::table('accounts')
                    ->where('A_Number', (int) $account->A_Number)
                    ->increment('A_Balance', $requestedAmount);

                Transaction::create([
                    'A_Number' => (int) $account->A_Number,
                    'C_ID' => (int) $customer->C_ID,
                    'T_Type' => 'Loan Disbursement',
                    'T_Amount' => $requestedAmount,
                    'T_Date' => now(),
                ]);

                LoanRequest::create([
                    'C_ID' => (int) $customer->C_ID,
                    'B_ID' => (int) $payload['branch_id'],
                    'requested_amount' => $requestedAmount,
                    'status' => 'accepted',
                    'decision_note' => 'Approved after password and OTP verification.',
                    'processed_at' => now(),
                    'approved_loan_id' => (int) $approvedLoan->L_ID,
                ]);
            });

            Cache::forget($cacheKey);

            return response()->json([
                'message' => 'Loan approved and disbursed successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Loan disbursement failed after OTP verification.', [
                'user_id' => (int) $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to complete loan disbursement. Please try again.',
            ], 500);
        } finally {
            $lock->release();
        }
    }

    public function take(Request $request): RedirectResponse
    {
        return back()->with('loan_error', 'Please use the secured password and OTP flow to request a loan.');
    }

    public function repay(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'loan_id' => ['required', 'integer'],
            'repayment_amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $user = auth()->user();
        $customer = $user->customer;
        $account = $user->account;

        if (!$customer || !$account) {
            return back()->with('loan_error', 'Customer profile or account is missing. Please complete your profile and create an account first.');
        }

        $loan = $user->loans()->where('L_ID', $validated['loan_id'])->first();

        if (!$loan) {
            return back()->with('loan_error', 'Selected loan was not found for this account.');
        }

        $this->applyMonthlyInterestToLoan($loan);
        $loan->refresh();

        $rawRemaining = (float) ($loan->remaining_amount ?? $loan->L_Amount);
        if ($rawRemaining <= 0) {
            return back()->with('loan_error', 'This loan is already fully paid.');
        }

        $requestedRepayment = (float) $validated['repayment_amount'];
        $appliedRepayment = min($requestedRepayment, $rawRemaining);

        if ((float) $account->A_Balance < $appliedRepayment) {
            return back()->with('loan_error', 'Insufficient account balance for this repayment.');
        }

        DB::transaction(function () use ($loan, $account, $customer, $appliedRepayment, $rawRemaining): void {
            $newRemaining = max($rawRemaining - $appliedRepayment, 0);

            $loan->remaining_amount = $newRemaining;
            $loan->status = $newRemaining > 0 ? 'active' : 'closed';
            $loan->save();

            $account->decrement('A_Balance', $appliedRepayment);

            Transaction::create([
                'A_Number' => $account->A_Number,
                'C_ID' => $customer->C_ID,
                'T_Type' => 'Loan Repayment',
                'T_Amount' => $appliedRepayment,
                'T_Date' => now(),
            ]);
        });

        $message = $requestedRepayment > $appliedRepayment
            ? 'Repayment processed. Extra amount was not charged because the loan is now fully paid.'
            : 'Repayment processed successfully.';

        return back()->with('loan_success', $message);
    }

    public function buildLoanSummary(Collection $loans, float $accountBalance): array
    {
        $totalLoanTaken = (float) $loans->sum(fn (Loan $loan) => (float) $loan->L_Amount);
        $remainingLoanBalance = (float) $loans->sum(fn (Loan $loan) => (float) ($loan->remaining_amount ?? $loan->L_Amount));
        $totalRepaid = max($totalLoanTaken - $remainingLoanBalance, 0);
        $availableMoney = $accountBalance - $remainingLoanBalance;

        $activeLoans = $loans
            ->filter(function (Loan $loan): bool {
                $status = strtolower((string) ($loan->status ?? 'active'));

                if (in_array($status, ['active', 'ongoing', 'in_progress'], true)) {
                    return true;
                }

                return (float) ($loan->remaining_amount ?? $loan->L_Amount) > 0;
            })
            ->values();

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

    private function processPendingLoanRequests(int $customerId, int $accountNumber): void
    {
        $pendingRequests = LoanRequest::query()
            ->where('C_ID', $customerId)
            ->where('status', 'processing')
            ->where('created_at', '<=', now()->subSeconds(self::PROCESSING_DELAY_SECONDS))
            ->orderBy('created_at')
            ->get();

        foreach ($pendingRequests as $loanRequest) {
            DB::transaction(function () use ($loanRequest, $customerId, $accountNumber): void {
                $hasOutstandingLoan = Loan::query()
                    ->where('C_ID', $customerId)
                    ->where(function ($query) {
                        $query->where('remaining_amount', '>', 0)
                            ->orWhere(function ($subQuery) {
                                $subQuery->whereNull('remaining_amount')
                                    ->where('L_Amount', '>', 0);
                            });
                    })
                    ->exists();

                if ($hasOutstandingLoan) {
                    $loanRequest->status = 'rejected';
                    $loanRequest->decision_note = 'Rejected because there is an existing unpaid loan.';
                    $loanRequest->processed_at = now();
                    $loanRequest->save();

                    return;
                }

                $approvedLoan = Loan::create([
                    'C_ID' => $customerId,
                    'B_ID' => $loanRequest->B_ID,
                    'L_Type' => 'Instant Tk ' . number_format((float) $loanRequest->requested_amount, 2) . ' Loan',
                    'L_Amount' => (float) $loanRequest->requested_amount,
                    'remaining_amount' => (float) $loanRequest->requested_amount,
                    'Interest_Rate' => 0,
                    'status' => 'active',
                ]);

                DB::table('accounts')
                    ->where('A_Number', $accountNumber)
                    ->increment('A_Balance', (float) $loanRequest->requested_amount);

                Transaction::create([
                    'A_Number' => $accountNumber,
                    'C_ID' => $customerId,
                    'T_Type' => 'Loan Disbursement',
                    'T_Amount' => (float) $loanRequest->requested_amount,
                    'T_Date' => now(),
                ]);

                $loanRequest->status = 'accepted';
                $loanRequest->approved_loan_id = $approvedLoan->L_ID;
                $loanRequest->decision_note = 'Accepted and disbursed to account.';
                $loanRequest->processed_at = now();
                $loanRequest->save();
            });
        }
    }

    private function hasOutstandingLoan(int $customerId): bool
    {
        return Loan::query()
            ->where('C_ID', $customerId)
            ->where(function ($query) {
                $query->where('remaining_amount', '>', 0)
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNull('remaining_amount')
                            ->where('L_Amount', '>', 0);
                    });
            })
            ->exists();
    }

    private function resolveBranchIdForLoan($user): ?int
    {
        $existingBranchId = $user->loans()->value('B_ID');

        if ($existingBranchId) {
            return (int) $existingBranchId;
        }

        $branchId = Branch::query()->value('B_ID');
        if (!$branchId) {
            $this->ensureDefaultDhakaBranches();
            $branchId = Branch::query()->value('B_ID');
        }

        return $branchId ? (int) $branchId : null;
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

    private function otpCacheKey(int $userId): string
    {
        return 'loan-otp-user-' . $userId;
    }

    private function resolveOtpEmail($user): string
    {
        $userEmail = trim((string) ($user->email ?? ''));
        return $userEmail;
    }

    private function maskEmail(string $email): string
    {
        if (!str_contains($email, '@')) {
            return '***';
        }

        [$local, $domain] = explode('@', $email, 2);
        $visible = substr($local, 0, 2);
        $maskedLocal = $visible . str_repeat('*', max(strlen($local) - 2, 1));

        return $maskedLocal . '@' . $domain;
    }

    private function sendLoanOtpToEmail(string $email, string $otp, int $userId): void
    {
        if ($email === '') {
            throw new \RuntimeException('No recipient email available for OTP delivery.');
        }

        Mail::raw('Your loan OTP is ' . $otp . '. It expires in 5 minutes.', function ($message) use ($email): void {
            $message->to($email)->subject('Loan OTP Verification');
        });

        Log::info('Loan OTP email dispatched.', [
            'user_id' => $userId,
            'email' => $this->maskEmail($email),
        ]);
    }

    private function applyMonthlyInterestForEligibleLoans(Collection $loans): void
    {
        $loans->each(function (Loan $loan): void {
            $this->applyMonthlyInterestToLoan($loan);
        });
    }

    private function applyMonthlyInterestToLoan(Loan $loan): void
    {
        $originalAmount = (float) $loan->L_Amount;
        $remainingAmount = (float) ($loan->remaining_amount ?? $loan->L_Amount);
        $status = strtolower((string) ($loan->status ?? 'active'));

        if ($originalAmount > self::MONTHLY_INTEREST_ELIGIBLE_MAX_AMOUNT || $remainingAmount <= 0) {
            return;
        }

        if (!in_array($status, ['active', 'ongoing', 'in_progress'], true)) {
            return;
        }

        DB::transaction(function () use ($loan): void {
            $freshLoan = Loan::query()
                ->where('L_ID', $loan->L_ID)
                ->lockForUpdate()
                ->first();

            if (!$freshLoan) {
                return;
            }

            $freshRemaining = (float) ($freshLoan->remaining_amount ?? $freshLoan->L_Amount);
            if ($freshRemaining <= 0) {
                return;
            }

            $appliedBase = $freshLoan->last_interest_applied_at
                ? Carbon::parse($freshLoan->last_interest_applied_at)
                : Carbon::parse($freshLoan->created_at);

            $freshMonthsElapsed = max(0, $appliedBase->diffInMonths(now()));
            if ($freshMonthsElapsed < 1) {
                return;
            }

            $freshNewRemaining = $freshRemaining;
            for ($i = 0; $i < $freshMonthsElapsed; $i++) {
                $freshNewRemaining = round($freshNewRemaining * (1 + self::MONTHLY_INTEREST_RATE), 2);
            }

            $freshLoan->remaining_amount = $freshNewRemaining;
            $freshLoan->Interest_Rate = 9;
            $freshLoan->last_interest_applied_at = $appliedBase->copy()->addMonthsNoOverflow($freshMonthsElapsed);
            $freshLoan->save();
        });
    }
}
