<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CardApplication;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Models\UserDocument;
use App\Models\User;
use App\Notifications\ApplicationStatusNotification;
use App\Services\LoanService;
use App\Services\UserBankingProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function __construct(
        private readonly UserBankingProfileService $userBankingProfileService,
        private readonly LoanService $loanService
    ) {
    }

    public function showLogin(): View
    {
        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'account_number' => ['required', 'digits:11'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $adminUser = User::query()
            ->where('email', $credentials['email'])
            ->where('is_admin', true)
            ->first();

        if (!$adminUser) {
            return back()->withErrors([
                'email' => 'Invalid admin credentials.',
            ])->onlyInput('email', 'account_number');
        }

        $adminAccount = $this->userBankingProfileService->ensureForUser($adminUser);

        if ((string) $adminAccount->A_Number !== (string) $credentials['account_number']) {
            return back()->withErrors([
                'account_number' => 'Invalid admin credentials.',
            ])->onlyInput('email', 'account_number');
        }

        if (!Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'is_admin' => true,
        ], $request->boolean('remember'))) {
            return back()->withErrors([
                'account_number' => 'Invalid admin credentials.',
            ])->onlyInput('email', 'account_number');
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
                ->with(['customer.user.userDocument'])
                ->where('status', 'processing')
                ->where('request_type', 'loan_request')
                ->orderByDesc('created_at')
                ->get(),
            'pendingRepaymentRequests' => LoanRequest::query()
                ->with(['customer.user.userDocument', 'targetLoan'])
                ->where('status', 'processing')
                ->where('request_type', 'repayment_request')
                ->orderByDesc('created_at')
                ->get(),
            'pendingCardApplications' => CardApplication::query()
                ->where('status', 'pending_review')
                ->orderByDesc('created_at')
                ->get(),
            'documentCount' => UserDocument::query()->count(),
        ]);
    }

    public function acceptLoanRequest(LoanRequest $loanRequest): RedirectResponse
    {
        if ($loanRequest->status !== 'processing' || $loanRequest->request_type !== 'loan_request') {
            return back()->with('admin_error', 'This loan request is already processed.');
        }

        try {
            $this->loanService->approveLoanRequest((int) $loanRequest->LR_ID);
        } catch (ValidationException $e) {
            return back()->with('admin_error', $e->validator->errors()->first() ?: 'Unable to approve this loan request.');
        } catch (\Throwable $e) {
            return back()->with('admin_error', 'Unable to approve this loan request.');
        }

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
        if ($loanRequest->status !== 'processing' || $loanRequest->request_type !== 'loan_request') {
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

    public function acceptRepaymentRequest(LoanRequest $loanRequest): RedirectResponse
    {
        if ($loanRequest->status !== 'processing' || $loanRequest->request_type !== 'repayment_request') {
            return back()->with('admin_error', 'This repayment request is already processed.');
        }

        try {
            $result = $this->loanService->approveRepaymentRequest((int) $loanRequest->LR_ID);
        } catch (ValidationException $e) {
            return back()->with('admin_error', $e->validator->errors()->first() ?: 'Unable to approve this repayment request.');
        } catch (\Throwable $e) {
            return back()->with('admin_error', 'Unable to approve this repayment request.');
        }

        $message = ((float) $result['requested_repayment']) > ((float) $result['applied_repayment'])
            ? 'Your repayment request #' . $loanRequest->LR_ID . ' was approved. Extra requested amount was not charged.'
            : 'Your repayment request #' . $loanRequest->LR_ID . ' was approved and applied.';

        $this->notifyCustomer(
            (int) $loanRequest->C_ID,
            'Loan Repayment Approved',
            $message,
            'personal.loan'
        );

        return back()->with('admin_success', 'Repayment request approved successfully.');
    }

    public function rejectRepaymentRequest(LoanRequest $loanRequest): RedirectResponse
    {
        if ($loanRequest->status !== 'processing' || $loanRequest->request_type !== 'repayment_request') {
            return back()->with('admin_error', 'This repayment request is already processed.');
        }

        $loanRequest->update([
            'status' => 'rejected',
            'decision_note' => 'Repayment rejected by admin.',
            'processed_at' => now(),
        ]);

        $this->notifyCustomer(
            (int) $loanRequest->C_ID,
            'Loan Repayment Rejected',
            'Your repayment request #' . $loanRequest->LR_ID . ' has been rejected by admin.',
            'personal.loan'
        );

        return back()->with('admin_success', 'Repayment request rejected successfully.');
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

    public function showUserDocument(UserDocument $userDocument, string $documentType): StreamedResponse
    {
        abort_unless(auth()->user()?->isAdminUser(), 403);

        $allowedTypes = [
            'nid_or_birth_certificate',
            'photo',
            'job_id',
            'student_id',
            'electric_bill',
        ];

        abort_unless(in_array($documentType, $allowedTypes, true), 404);

        $path = $userDocument->{$documentType};
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }

    public function documents(): View
    {
        abort_unless(auth()->user()?->isAdminUser(), 403);

        return view('admin.documents', [
            'documents' => UserDocument::query()
                ->with('user')
                ->latest()
                ->get(),
        ]);
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
