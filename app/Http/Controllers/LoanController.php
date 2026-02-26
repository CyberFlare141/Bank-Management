<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Loan;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $user->load([
            'account',
            'loans' => fn ($query) => $query->latest('created_at'),
        ]);

        [$activeLoans, $loanSummary] = $this->buildLoanSummary($user->loans, (float) ($user->account->A_Balance ?? 0));

        return view('personal.loan', [
            'account' => $user->account,
            'loans' => $user->loans,
            'activeLoans' => $activeLoans,
            'loanSummary' => $loanSummary,
        ]);
    }

    public function take(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'loan_type' => ['nullable', 'string', 'max:255'],
            'loan_amount' => ['required', 'numeric', 'min:0.01'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $user = auth()->user();
        $customer = $user->customer;
        $account = $user->account;

        if (!$customer || !$account) {
            return back()->with('loan_error', 'Customer profile or account is missing for this user.');
        }

        $branchId = $user->loans()->value('B_ID') ?? Branch::query()->value('B_ID');

        if (!$branchId) {
            return back()->with('loan_error', 'No branch is available to issue a loan.');
        }

        DB::transaction(function () use ($validated, $customer, $account, $branchId): void {
            $loanAmount = (float) $validated['loan_amount'];

            Loan::create([
                'C_ID' => $customer->C_ID,
                'B_ID' => $branchId,
                'L_Type' => $validated['loan_type'] ?? 'Personal Loan',
                'L_Amount' => $loanAmount,
                'remaining_amount' => $loanAmount,
                'Interest_Rate' => (float) ($validated['interest_rate'] ?? 0),
                'status' => 'active',
            ]);

            $account->increment('A_Balance', $loanAmount);

            Transaction::create([
                'A_Number' => $account->A_Number,
                'C_ID' => $customer->C_ID,
                'T_Type' => 'Loan Disbursement',
                'T_Amount' => $loanAmount,
                'T_Date' => now(),
            ]);
        });

        return back()->with('loan_success', 'Loan has been approved and added to your account.');
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
            return back()->with('loan_error', 'Customer profile or account is missing for this user.');
        }

        $loan = $user->loans()->where('L_ID', $validated['loan_id'])->first();

        if (!$loan) {
            return back()->with('loan_error', 'Selected loan was not found for this account.');
        }

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
}
