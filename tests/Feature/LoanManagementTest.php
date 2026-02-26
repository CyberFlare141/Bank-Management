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

    public function test_user_can_create_instant_loan_request_and_it_is_processed_after_delay(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(500);

        $response = $this
            ->actingAs($user)
            ->from(route('personal.loan'))
            ->post(route('personal.loan.take'));

        $response
            ->assertRedirect(route('personal.loan'))
            ->assertSessionHas('loan_success');

        $account->refresh();
        $this->assertSame(500.0, (float) $account->A_Balance);

        $this->assertDatabaseHas('loan_requests', [
            'C_ID' => $customer->C_ID,
            'requested_amount' => '30000.00',
            'status' => 'processing',
        ]);

        LoanRequest::query()->update(['created_at' => now()->subSeconds(30)]);

        $this->actingAs($user)->get(route('personal.loan'))->assertOk();

        $account->refresh();
        $loanRequest = LoanRequest::first();

        $this->assertSame(30500.0, (float) $account->A_Balance);
        $this->assertSame('accepted', $loanRequest->status);

        $this->assertDatabaseHas('transactions', [
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Loan Disbursement',
            'T_Amount' => '30000.00',
        ]);

        $this->assertDatabaseHas('loans', [
            'C_ID' => $customer->C_ID,
            'L_Amount' => '30000.00',
            'remaining_amount' => '30000.00',
            'status' => 'active',
        ]);
    }

    public function test_user_can_repay_loan_and_close_it_when_fully_paid(): void
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
            ->assertSessionHas('loan_success', 'Repayment processed successfully.');

        $loan->refresh();
        $account->refresh();

        $this->assertSame(0.0, (float) $loan->remaining_amount);
        $this->assertSame('closed', $loan->status);
        $this->assertSame(200.0, (float) $account->A_Balance);

        $this->assertDatabaseHas('transactions', [
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Loan Repayment',
            'T_Amount' => '700.00',
        ]);
    }

    public function test_overpayment_is_capped_to_remaining_balance(): void
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
            ->assertSessionHas('loan_success', 'Repayment processed. Extra amount was not charged because the loan is now fully paid.');

        $loan->refresh();
        $account->refresh();

        $this->assertSame(0.0, (float) $loan->remaining_amount);
        $this->assertSame('closed', $loan->status);
        $this->assertSame(450.0, (float) $account->A_Balance);

        $this->assertDatabaseHas('transactions', [
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Loan Repayment',
            'T_Amount' => '250.00',
        ]);
    }

    public function test_pending_request_gets_rejected_if_user_has_existing_unpaid_loan(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(700);
        $this->createLoanForCustomer($customer->C_ID, 500, 300);

        $this->actingAs($user)
            ->from(route('personal.loan'))
            ->post(route('personal.loan.take'))
            ->assertRedirect(route('personal.loan'));

        LoanRequest::query()->update(['created_at' => now()->subSeconds(30)]);

        $this->actingAs($user)->get(route('personal.loan'))->assertOk();

        $account->refresh();
        $loanRequest = LoanRequest::first();

        $this->assertSame('rejected', $loanRequest->status);
        $this->assertSame(700.0, (float) $account->A_Balance);
        $this->assertDatabaseCount('loans', 1);
    }

    public function test_repayment_fails_when_account_balance_is_insufficient(): void
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
            ->assertSessionHas('loan_error', 'Insufficient account balance for this repayment.');

        $loan->refresh();
        $account->refresh();

        $this->assertSame(500.0, (float) $loan->remaining_amount);
        $this->assertSame('active', $loan->status);
        $this->assertSame(100.0, (float) $account->A_Balance);

        $this->assertDatabaseMissing('transactions', [
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Loan Repayment',
            'T_Amount' => '300.00',
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
