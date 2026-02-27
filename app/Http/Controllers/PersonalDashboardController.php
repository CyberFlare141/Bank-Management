<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PersonalDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $user->load([
            'account',
            'creditCard',
            'loans' => fn ($query) => $query->latest('created_at'),
            'cardApplications' => fn ($query) => $query->latest('card_applications.created_at')->limit(5),
        ]);

        $recentTransactions = $user
            ->transactions()
            ->latest('T_Date')
            ->latest('created_at')
            ->limit(10)
            ->get();

        [$activeLoans, $loanSummary] = $this->buildLoanSummary(
            $user->loans,
            (float) ($user->account->A_Balance ?? 0)
        );

        return view('personal.dashboard', [
            'user' => $user,
            'account' => $user->account,
            'creditCard' => $user->creditCard,
            'activeLoans' => $activeLoans,
            'recentTransactions' => $recentTransactions,
            'loanSummary' => $loanSummary,
            'recentCardApplications' => $user->cardApplications,
        ]);
    }

    private function buildLoanSummary(Collection $loans, float $accountBalance): array
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
