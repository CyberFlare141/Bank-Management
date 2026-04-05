<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Branch;
use App\Models\CardApplication;
use App\Models\Customer;
use App\Models\LoanRequest;
use App\Models\UserDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_log_in_from_admin_login_page(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => 'secret12345',
        ]);
        [, $account] = $this->createBankingProfileForUser($admin);

        $this->post(route('admin.login.submit'), [
            'account_number' => (string) $account->A_Number,
            'email' => $admin->email,
            'password' => 'secret12345',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_matching_env_email_without_admin_flag_cannot_log_in_from_admin_page(): void
    {
        putenv('ADMIN_EMAIL=legacy-admin@example.com');
        $_ENV['ADMIN_EMAIL'] = 'legacy-admin@example.com';
        $_SERVER['ADMIN_EMAIL'] = 'legacy-admin@example.com';

        $user = User::factory()->create([
            'email' => 'legacy-admin@example.com',
            'password' => 'secret12345',
            'is_admin' => false,
        ]);
        [, $account] = $this->createBankingProfileForUser($user);

        $this->post(route('admin.login.submit'), [
            'account_number' => (string) $account->A_Number,
            'email' => $user->email,
            'password' => 'secret12345',
        ])->assertSessionHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_admin_can_accept_loan_request(): void
    {
        $admin = User::factory()->admin()->create();
        [$customer, $branch] = $this->createCustomerWithAccount();

        $loanRequest = LoanRequest::create([
            'C_ID' => $customer->C_ID,
            'B_ID' => $branch->B_ID,
            'requested_amount' => 15000,
            'request_type' => 'loan_request',
            'status' => 'processing',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->post(route('admin.loans.accept', $loanRequest))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('admin_success', 'Loan request accepted successfully.');

        $loanRequest->refresh();

        $this->assertSame('accepted', $loanRequest->status);
        $this->assertNotNull($loanRequest->approved_loan_id);
        $this->assertDatabaseHas('loans', [
            'L_ID' => $loanRequest->approved_loan_id,
            'C_ID' => $customer->C_ID,
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_accept_repayment_request(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        [$customer, $branch, $account] = $this->createCustomerWithAccount($user->email);

        $loan = \App\Models\Loan::create([
            'C_ID' => $customer->C_ID,
            'B_ID' => $branch->B_ID,
            'L_Type' => 'Personal Loan',
            'L_Amount' => 1200,
            'remaining_amount' => 800,
            'Interest_Rate' => 3,
            'status' => 'active',
        ]);

        $account->update(['A_Balance' => 2000]);

        $repaymentRequest = LoanRequest::create([
            'C_ID' => $customer->C_ID,
            'B_ID' => $branch->B_ID,
            'requested_amount' => 300,
            'request_type' => 'repayment_request',
            'target_loan_id' => $loan->L_ID,
            'status' => 'processing',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->post(route('admin.repayments.accept', $repaymentRequest))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('admin_success', 'Repayment request approved successfully.');

        $loan->refresh();
        $repaymentRequest->refresh();
        $account->refresh();

        $this->assertSame('accepted', $repaymentRequest->status);
        $this->assertSame(500.0, (float) $loan->remaining_amount);
        $this->assertSame(1700.0, (float) $account->A_Balance);
        $this->assertDatabaseHas('transactions', [
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Loan Repayment',
            'T_Amount' => '300.00',
        ]);
    }

    public function test_admin_can_accept_card_application(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        [$customer, $branch, $account] = $this->createCustomerWithAccount($user->email);

        $application = CardApplication::create([
            'C_ID' => $customer->C_ID,
            'B_ID' => $branch->B_ID,
            'application_id' => 'CARD-20260309-ABCDEFGH',
            'card_category' => 'debit',
            'card_network' => 'Visa',
            'card_design' => 'Classic Blue',
            'delivery_method' => 'home_delivery',
            'full_name' => 'Test User',
            'date_of_birth' => '1995-01-15',
            'national_id_passport' => 'NID-123456',
            'contact_number' => '01700000000',
            'email_address' => 'test@example.com',
            'residential_address' => 'Dhaka',
            'existing_account_number' => $account->A_Number,
            'account_type' => 'Personal',
            'branch_name' => $branch->B_Name,
            'status' => 'pending_review',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->post(route('admin.cards.accept', $application))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('admin_success', 'Card application accepted successfully.');

        $application->refresh();
        $this->assertSame('accepted', $application->status);
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'type' => \App\Notifications\ApplicationStatusNotification::class,
        ]);
    }

    public function test_admin_can_view_uploaded_registration_documents_from_loan_review(): void
    {
        Storage::fake('local');

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        [$customer, $branch] = $this->createCustomerWithAccount($user->email);

        Storage::disk('local')->put('documents/' . $user->id . '/photo.jpg', 'photo');

        $documents = UserDocument::create([
            'user_id' => $user->id,
            'account_type' => 'student',
            'nid_or_birth_certificate' => 'documents/' . $user->id . '/nid.pdf',
            'photo' => 'documents/' . $user->id . '/photo.jpg',
            'job_id' => null,
            'student_id' => 'documents/' . $user->id . '/student-id.pdf',
            'electric_bill' => 'documents/' . $user->id . '/electric-bill.pdf',
        ]);

        LoanRequest::create([
            'C_ID' => $customer->C_ID,
            'B_ID' => $branch->B_ID,
            'requested_amount' => 15000,
            'request_type' => 'loan_request',
            'status' => 'processing',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Loan Applications');

        $this->actingAs($admin)
            ->get(route('admin.user-documents.show', [$documents, 'photo']))
            ->assertOk();
    }

    public function test_admin_can_browse_registration_documents_page(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        UserDocument::create([
            'user_id' => $user->id,
            'account_type' => 'normal',
            'nid_or_birth_certificate' => 'documents/' . $user->id . '/nid.pdf',
            'photo' => 'documents/' . $user->id . '/photo.jpg',
            'job_id' => 'documents/' . $user->id . '/job-id.pdf',
            'student_id' => null,
            'electric_bill' => 'documents/' . $user->id . '/electric-bill.pdf',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.documents'))
            ->assertOk()
            ->assertSee('Registration Documents')
            ->assertSee($user->email);
    }

    private function createCustomerWithAccount(?string $email = null): array
    {
        $customer = Customer::create([
            'C_Name' => 'Test Customer',
            'C_Email' => $email ?? 'customer' . random_int(1000, 9999) . '@example.com',
            'C_Address' => 'Test Address',
            'C_PhoneNumber' => '1234567890',
        ]);

        $account = Account::create([
            'C_ID' => $customer->C_ID,
            'account_type' => 'Personal',
            'A_Balance' => 2000,
            'Operating_Date' => now()->toDateString(),
        ]);

        $branch = Branch::create([
            'B_Name' => 'Main Branch',
            'B_Location' => 'Dhaka',
            'IFSC_Code' => 'IFSC' . random_int(10000, 99999),
        ]);

        return [$customer, $branch, $account];
    }

    private function createBankingProfileForUser(User $user): array
    {
        $customer = Customer::create([
            'C_Name' => $user->name,
            'C_Email' => $user->email,
            'C_Address' => 'Admin Address',
            'C_PhoneNumber' => '017' . random_int(10000000, 99999999),
        ]);

        $account = Account::create([
            'A_Number' => random_int(10000000000, 99999999999),
            'C_ID' => $customer->C_ID,
            'account_type' => 'Personal',
            'A_Balance' => 0,
            'Operating_Date' => now()->toDateString(),
        ]);

        return [$customer, $account];
    }
}
