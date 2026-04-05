<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LoanManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_instant_loan_request_and_it_remains_pending_until_admin_approval(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(500);

        $response = $this
            ->actingAs($user)
            ->from(route('personal.loan'))
            ->post(route('personal.loan.take'));

        $response
            ->assertRedirect(route('personal.loan'))
            ->assertSessionHas('loan_success', 'Loan request submitted successfully and is awaiting admin approval.');

        $account->refresh();
        $this->assertSame(500.0, (float) $account->A_Balance);

        $this->assertDatabaseHas('loan_requests', [
            'C_ID' => $customer->C_ID,
            'requested_amount' => '30000.00',
            'request_type' => 'loan_request',
            'status' => 'processing',
        ]);

        $loanRequest = LoanRequest::first();
        $this->assertSame('processing', $loanRequest->status);
        $this->assertDatabaseCount('loans', 0);
        $this->assertDatabaseMissing('transactions', [
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Loan Disbursement',
        ]);
    }

    public function test_user_repayment_request_stays_pending_until_admin_approval(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(900);
        $loan = $this->createLoanForCustomer($customer->C_ID, 700, 700);

        $response = $this
            ->actingAs($user)
            ->from(route('personal.loan'))
            ->post(route('personal.loan.repay'), [
                'loan_id' => $loan->L_ID,
                'repayment_amount' => 700,
            ]);

        $response
            ->assertRedirect(route('personal.loan'))
            ->assertSessionHas('loan_success', 'Repayment request submitted successfully and is awaiting admin approval.');

        $loan->refresh();
        $account->refresh();

        $this->assertSame(700.0, (float) $loan->remaining_amount);
        $this->assertSame('active', $loan->status);
        $this->assertSame(900.0, (float) $account->A_Balance);
        $this->assertDatabaseHas('loan_requests', [
            'C_ID' => $customer->C_ID,
            'target_loan_id' => $loan->L_ID,
            'request_type' => 'repayment_request',
            'requested_amount' => '700.00',
        ]);
    }

    public function test_repayment_request_is_capped_only_when_admin_processes_it(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(700);
        $loan = $this->createLoanForCustomer($customer->C_ID, 600, 250);

        $response = $this
            ->actingAs($user)
            ->from(route('personal.loan'))
            ->post(route('personal.loan.repay'), [
                'loan_id' => $loan->L_ID,
                'repayment_amount' => 400,
            ]);

        $response
            ->assertRedirect(route('personal.loan'))
            ->assertSessionHas('loan_success', 'Repayment request submitted successfully and is awaiting admin approval.');

        $loan->refresh();
        $account->refresh();

        $this->assertSame(250.0, (float) $loan->remaining_amount);
        $this->assertSame('active', $loan->status);
        $this->assertSame(700.0, (float) $account->A_Balance);
        $this->assertDatabaseHas('loan_requests', [
            'C_ID' => $customer->C_ID,
            'target_loan_id' => $loan->L_ID,
            'request_type' => 'repayment_request',
            'requested_amount' => '400.00',
        ]);
    }

    public function test_new_loan_request_is_rejected_when_user_has_existing_unpaid_loan(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(700);
        $this->createLoanForCustomer($customer->C_ID, 500, 300);

        $this->actingAs($user)
            ->from(route('personal.loan'))
            ->post(route('personal.loan.take'))
            ->assertRedirect(route('personal.loan'))
            ->assertSessionHas('loan_error', 'You already have an unpaid loan. Repay it before requesting a new loan.');

        $account->refresh();
        $this->assertSame(700.0, (float) $account->A_Balance);
        $this->assertDatabaseCount('loan_requests', 0);
        $this->assertDatabaseCount('loans', 1);
    }

    public function test_repayment_request_can_be_created_even_if_balance_may_change_before_admin_approval(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(100);
        $loan = $this->createLoanForCustomer($customer->C_ID, 500, 500);

        $response = $this
            ->actingAs($user)
            ->from(route('personal.loan'))
            ->post(route('personal.loan.repay'), [
                'loan_id' => $loan->L_ID,
                'repayment_amount' => 300,
            ]);

        $response
            ->assertRedirect(route('personal.loan'))
            ->assertSessionHas('loan_success', 'Repayment request submitted successfully and is awaiting admin approval.');

        $loan->refresh();
        $account->refresh();

        $this->assertSame(500.0, (float) $loan->remaining_amount);
        $this->assertSame('active', $loan->status);
        $this->assertSame(100.0, (float) $account->A_Balance);

        $this->assertDatabaseHas('loan_requests', [
            'C_ID' => $customer->C_ID,
            'target_loan_id' => $loan->L_ID,
            'request_type' => 'repayment_request',
            'requested_amount' => '300.00',
        ]);
    }

    private function createUserWithBankingProfile(float $openingBalance): array
    {
        $user = User::factory()->create();

        $customer = Customer::create([
            'C_Name' => $user->name,
            'C_Email' => $user->email,
            'C_Address' => 'Test Address',
            'C_PhoneNumber' => '1234567890',
        ]);

        $account = Account::create([
            'C_ID' => $customer->C_ID,
            'account_type' => 'Personal',
            'A_Balance' => $openingBalance,
            'Operating_Date' => now()->toDateString(),
        ]);

        DB::table('branches')->insert([
            'B_Name' => 'Main Branch',
            'B_Location' => 'Test City',
            'IFSC_Code' => 'IFSC' . random_int(10000, 99999),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$user, $customer, $account];
    }

    private function createLoanForCustomer(int $customerId, float $totalAmount, float $remainingAmount): Loan
    {
        $branchId = (int) DB::table('branches')->value('B_ID');

        return Loan::create([
            'C_ID' => $customerId,
            'B_ID' => $branchId,
            'L_Type' => 'Personal Loan',
            'L_Amount' => $totalAmount,
            'remaining_amount' => $remainingAmount,
            'Interest_Rate' => 3,
            'status' => $remainingAmount > 0 ? 'active' : 'closed',
        ]);
    }
}
