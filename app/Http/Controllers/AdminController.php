<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CardApplication;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Models\User;
use App\Notifications\ApplicationStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function showLogin(): View
    {
        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Invalid admin credentials.',
            ])->onlyInput('email');
        }

        if (!$request->user() || !$request->user()->isAdminUser()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'You do not have admin access.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'pendingLoanRequests' => LoanRequest::query()
                ->where('status', 'processing')
                ->orderByDesc('created_at')
                ->get(),
            'pendingCardApplications' => CardApplication::query()
                ->where('status', 'pending_review')
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    public function acceptLoanRequest(LoanRequest $loanRequest): RedirectResponse
    {
        if ($loanRequest->status !== 'processing') {
            return back()->with('admin_error', 'This loan request is already processed.');
        }

        DB::transaction(function () use ($loanRequest): void {
            $lockedRequest = LoanRequest::query()
                ->whereKey($loanRequest->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedRequest->status !== 'processing') {
                return;
            }

            $account = Account::query()
                ->where('C_ID', $lockedRequest->C_ID)
                ->orderBy('A_Number')
                ->lockForUpdate()
                ->first();

            if (!$account) {
                throw new \RuntimeException('No account found for this customer.');
            }

            $loan = Loan::create([
                'C_ID' => (int) $lockedRequest->C_ID,
                'B_ID' => (int) $lockedRequest->B_ID,
                'L_Type' => 'Admin Approved Loan',
                'L_Amount' => (float) $lockedRequest->requested_amount,
                'remaining_amount' => (float) $lockedRequest->requested_amount,
                'Interest_Rate' => 0,
                'status' => 'processing',
            ]);

            $loan->update(['status' => 'approved']);

            $lockedRequest->update([
                'status' => 'accepted',
                'decision_note' => 'Accepted by admin and disbursed.',
                'processed_at' => now(),
                'approved_loan_id' => $loan->L_ID,
            ]);
        });

        $this->notifyCustomer(
            (int) $loanRequest->C_ID,
            'Loan Request Accepted',
            'Your loan request #' . $loanRequest->LR_ID . ' has been accepted and disbursed.',
            'personal.loan'
        );

        return back()->with('admin_success', 'Loan request accepted successfully.');
    }

    public function rejectLoanRequest(LoanRequest $loanRequest): RedirectResponse
    {
        if ($loanRequest->status !== 'processing') {
            return back()->with('admin_error', 'This loan request is already processed.');
        }

        $loanRequest->update([
            'status' => 'rejected',
            'decision_note' => 'Rejected by admin.',
            'processed_at' => now(),
        ]);

        $this->notifyCustomer(
            (int) $loanRequest->C_ID,
            'Loan Request Rejected',
            'Your loan request #' . $loanRequest->LR_ID . ' has been rejected by admin.',
            'personal.loan'
        );

        return back()->with('admin_success', 'Loan request rejected successfully.');
    }

    public function acceptCardApplication(CardApplication $cardApplication): RedirectResponse
    {
        if ($cardApplication->status !== 'pending_review') {
            return back()->with('admin_error', 'This card application is already processed.');
        }

        $cardApplication->update(['status' => 'accepted']);

        $this->notifyCustomer(
            (int) $cardApplication->C_ID,
            'Card Application Accepted',
            'Your card application ' . $cardApplication->application_id . ' has been accepted.',
            'personal.cards'
        );

        return back()->with('admin_success', 'Card application accepted successfully.');
    }

    public function rejectCardApplication(CardApplication $cardApplication): RedirectResponse
    {
        if ($cardApplication->status !== 'pending_review') {
            return back()->with('admin_error', 'This card application is already processed.');
        }

        $cardApplication->update(['status' => 'rejected']);

        $this->notifyCustomer(
            (int) $cardApplication->C_ID,
            'Card Application Rejected',
            'Your card application ' . $cardApplication->application_id . ' has been rejected.',
            'personal.cards'
        );

        return back()->with('admin_success', 'Card application rejected successfully.');
    }

    private function notifyCustomer(int $customerId, string $title, string $message, string $targetRoute): void
    {
        $customer = Customer::query()->find($customerId);
        if (!$customer || empty($customer->C_Email)) {
            return;
        }

        $user = User::query()
            ->where('email', (string) $customer->C_Email)
            ->first();

        if (!$user) {
            return;
        }

        $user->notify(new ApplicationStatusNotification($title, $message, $targetRoute));
    }
}
