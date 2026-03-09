<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Branch;
use App\Models\CardApplication;
use App\Models\Customer;
use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_log_in_from_admin_login_page(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => 'secret12345',
        ]);

        $this->post(route('admin.login.submit'), [
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

    public function test_admin_can_accept_loan_request(): void
    {
        $admin = User::factory()->admin()->create();
        [$customer, $branch] = $this->createCustomerWithAccount();

        $loanRequest = LoanRequest::create([
            'C_ID' => $customer->C_ID,
            'B_ID' => $branch->B_ID,
            'requested_amount' => 15000,
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
}
