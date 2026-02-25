<?php

namespace App\Http\Controllers;

use App\Models\Loan;
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
        ]);

        $recentTransactions = $user
            ->transactions()
            ->latest('T_Date')
            ->latest('created_at')
            ->limit(10)
            ->get();

        $activeLoans = $user
            ->loans
            ->filter(function (Loan $loan): bool {
                $status = strtolower((string) ($loan->status ?? 'active'));

                return $status === 'active' || $status === 'ongoing' || $status === 'in_progress';
            })
            ->values();

        return view('personal.dashboard', [
            'user' => $user,
            'account' => $user->account,
            'creditCard' => $user->creditCard,
            'activeLoans' => $activeLoans,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
