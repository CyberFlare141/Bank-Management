<?php

namespace App\Http\Controllers;

use App\Services\AccountService;
use App\Services\LoanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoanController extends Controller
{
    private const INSTANT_LOAN_MIN_AMOUNT = 5000;
    private const INSTANT_LOAN_MAX_AMOUNT = 30000;
    private const OTP_EXPIRY_MINUTES = 5;
    private const OTP_MAX_ATTEMPTS = 3;

    public function __construct(
        private readonly LoanService $loanService,
        private readonly AccountService $accountService
    ) {
    }

    public function approvedLoanTotalsByBranch(): JsonResponse
    {
        return response()->json([
            'data' => $this->loanService->approvedLoanTotalsByBranch(),
        ]);
    }

    public function customersWithActiveLoans(): JsonResponse
    {
        return response()->json([
            'data' => $this->loanService->customersWithActiveLoans(),
        ]);
    }

    public function approveLoanRequest(int $loanRequestId): JsonResponse
    {
        $this->loanService->approveLoanRequest($loanRequestId);
        return response()->json(['message' => 'Loan approved successfully.']);
    }

    public function index(): View
    {
        $user = auth()->user();
        $context = $this->accountService->getUserBankingContext((string) $user->email);

        $loans = [];
        $loanRequests = [];
        $activeLoans = [];
        $loanSummary = [
            'total_loan_taken' => 0,
            'total_repaid' => 0,
            'remaining_loan_balance' => 0,
            'available_money' => 0,
        ];

        if ($context && $context->A_Number) {
            $this->loanService->processPendingLoanRequests((int) $context->C_ID, (int) $context->A_Number);
            $loans = $this->accountService->getCustomerLoans((int) $context->C_ID);
            $this->loanService->applyMonthlyInterestForLoans($loans);
            $loans = $this->accountService->getCustomerLoans((int) $context->C_ID);
            $loanRequests = $this->accountService->getCustomerLoanRequests((int) $context->C_ID);
            [$activeLoans, $loanSummary] = $this->loanService->buildLoanSummary(
                $loans,
                (float) ($context->A_Balance ?? 0)
            );
        }

        return view('personal.loan', [
            'account' => $context,
            'loans' => collect($loans),
            'activeLoans' => collect($activeLoans),
            'loanSummary' => $loanSummary,
            'loanRequests' => collect($loanRequests),
            'canRequestLoan' => (bool) $context,
            'hasOtpEmail' => $this->resolveOtpEmail((string) $user->email) !== '',
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
        $context = $this->accountService->getUserBankingContext((string) $user->email);
        if (!$context || !$context->A_Number) {
            return response()->json([
                'message' => 'Customer profile or account is missing. Please complete your profile and create an account first.',
            ], 422);
        }

        if (!Hash::check((string) $validated['password'], (string) $user->password)) {
            return response()->json(['message' => 'Incorrect account password.'], 422);
        }

        $otpEmail = $this->resolveOtpEmail((string) $user->email);
        if ($otpEmail === '') {
            return response()->json([
                'message' => 'No registered email found for this user. Please update your profile email first.',
            ], 422);
        }

        $requestedAmount = round((float) $validated['requested_amount'], 2);
        $branchId = $this->loanService->resolveBranchIdForLoan((string) $user->email);

        if (!$branchId) {
            return response()->json(['message' => 'No branch is available to issue a loan.'], 422);
        }

        if ($this->loanService->hasOutstandingLoan((int) $context->C_ID)) {
            return response()->json([
                'message' => 'You already have an unpaid loan. Repay it before requesting a new loan.',
            ], 422);
        }

        $otp = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(self::OTP_EXPIRY_MINUTES);
        Cache::put($this->otpCacheKey((int) $user->id), [
            'customer_id' => (int) $context->C_ID,
            'account_number' => (int) $context->A_Number,
            'branch_id' => $branchId,
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
            return response()->json(['message' => 'Failed to send OTP. Please try again.'], 500);
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
            return response()->json(['message' => 'OTP session expired. Please start again.'], 422);
        }

        if (now()->timestamp > (int) $payload['expires_at']) {
            Cache::forget($cacheKey);
            return response()->json(['message' => 'OTP expired. Please request a new OTP.'], 422);
        }

        $attemptsLeft = (int) ($payload['attempts_left'] ?? 0);
        if ($attemptsLeft <= 0) {
            Cache::forget($cacheKey);
            return response()->json(['message' => 'Maximum OTP attempts reached. Please start again.'], 429);
        }

        if (!Hash::check((string) $validated['otp'], (string) $payload['otp_hash'])) {
            $payload['attempts_left'] = $attemptsLeft - 1;
            $remaining = (int) $payload['attempts_left'];

            if ($remaining <= 0) {
                Cache::forget($cacheKey);
                return response()->json(['message' => 'Invalid OTP. Maximum attempts reached.'], 429);
            }

            Cache::put($cacheKey, $payload, now()->setTimestamp((int) $payload['expires_at']));
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
            $this->loanService->disburseInstantLoan(
                (int) $payload['customer_id'],
                (int) $payload['account_number'],
                (int) $payload['branch_id'],
                (float) $payload['requested_amount']
            );

            Cache::forget($cacheKey);
            return response()->json(['message' => 'Loan approved and disbursed successfully.']);
        } catch (ValidationException $e) {
            Cache::forget($cacheKey);
            return response()->json([
                'message' => $e->validator->errors()->first() ?: 'Unable to complete loan disbursement.',
            ], 422);
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
        $context = $this->accountService->getUserBankingContext((string) $user->email);
        if (!$context || !$context->A_Number) {
            return back()->with('loan_error', 'Customer profile or account is missing. Please complete your profile and create an account first.');
        }

        try {
            $result = $this->loanService->repayLoan(
                (int) $context->C_ID,
                (int) $context->A_Number,
                (int) $validated['loan_id'],
                (float) $validated['repayment_amount']
            );
        } catch (ValidationException $e) {
            return back()->with('loan_error', $e->validator->errors()->first() ?: 'Repayment failed.');
        } catch (\Throwable $e) {
            Log::error('Loan repayment failed.', [
                'user_id' => (int) $user->id,
                'error' => $e->getMessage(),
            ]);
            return back()->with('loan_error', 'Repayment failed. Please try again.');
        }

        $message = $result['requested_repayment'] > $result['applied_repayment']
            ? 'Repayment processed. Extra amount was not charged because the loan is now fully paid.'
            : 'Repayment processed successfully.';

        return back()->with('loan_success', $message);
    }

    private function otpCacheKey(int $userId): string
    {
        return 'loan-otp-user-' . $userId;
    }

    private function resolveOtpEmail(string $email): string
    {
        return trim($email);
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
        Mail::raw('Your loan OTP is ' . $otp . '. It expires in 5 minutes.', function ($message) use ($email): void {
            $message->to($email)->subject('Loan OTP Verification');
        });

        Log::info('Loan OTP email dispatched.', [
            'user_id' => $userId,
            'email' => $this->maskEmail($email),
        ]);
    }
}
