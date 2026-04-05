<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Customer;
use App\Models\DepositProduct;
use App\Models\FixedDeposit;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiBankingEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_history_filters_summaries_mini_statement_and_download_work(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(5000);

        Transaction::create([
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Bill Payment - Utility (DESCO-001)',
            'T_Amount' => 200,
            'T_Date' => '2026-03-04 10:00:00',
        ]);

        Transaction::create([
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Fund Transfer Received from 999001',
            'T_Amount' => 500,
            'T_Date' => '2026-03-15 09:30:00',
        ]);

        Transaction::create([
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Fixed Deposit Booking',
            'T_Amount' => 300,
            'T_Date' => '2026-04-01 12:00:00',
        ]);

        $this->actingAs($user, 'api')
            ->getJson('/api/transactions/history?from_date=2026-03-10&type=credit')
            ->assertOk()
            ->assertJsonPath('data.data.0.T_Type', 'Fund Transfer Received from 999001')
            ->assertJsonCount(1, 'data.data');

        $this->actingAs($user, 'api')
            ->getJson('/api/transactions/monthly-summaries')
            ->assertOk()
            ->assertJsonFragment([
                'month' => '2026-04',
                'transaction_count' => 1,
                'total_credits' => 0,
                'total_debits' => 300,
                'net_amount' => -300,
            ])
            ->assertJsonFragment([
                'month' => '2026-03',
                'transaction_count' => 2,
                'total_credits' => 500,
                'total_debits' => 200,
                'net_amount' => 300,
            ]);

        $this->actingAs($user, 'api')
            ->getJson('/api/accounts/mini-statement?limit=2')
            ->assertOk()
            ->assertJsonPath('data.transactions.0.T_Type', 'Fixed Deposit Booking')
            ->assertJsonCount(2, 'data.transactions');

        $response = $this->actingAs($user, 'api')
            ->get('/api/transactions/statement/download?exact_type=Fixed%20Deposit%20Booking');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('Fixed Deposit Booking', $response->streamedContent());
    }

    public function test_admin_can_manage_deposit_products(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();

        $this->actingAs($user, 'api')
            ->postJson('/api/deposit-products', [
                'name' => 'Premium 12 Month FD',
                'product_code' => 'fd-12-premium',
                'product_type' => 'fixed_deposit',
                'minimum_amount' => 1000,
                'maximum_amount' => 100000,
                'term_months' => 12,
                'annual_interest_rate' => 8.5,
                'allow_early_break' => true,
                'early_break_penalty_rate' => 15,
                'status' => 'active',
            ])
            ->assertForbidden();

        $response = $this->actingAs($admin, 'api')
            ->postJson('/api/deposit-products', [
                'name' => 'Premium 12 Month FD',
                'product_code' => 'fd-12-premium',
                'product_type' => 'fixed_deposit',
                'minimum_amount' => 1000,
                'maximum_amount' => 100000,
                'term_months' => 12,
                'annual_interest_rate' => 8.5,
                'allow_early_break' => true,
                'early_break_penalty_rate' => 15,
                'status' => 'active',
            ])
            ->assertCreated()
            ->assertJsonPath('data.product_code', 'FD-12-PREMIUM');

        $productId = $response->json('data.id');

        $this->actingAs($admin, 'api')
            ->putJson('/api/deposit-products/' . $productId, [
                'status' => 'inactive',
                'annual_interest_rate' => 9.25,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'inactive')
            ->assertJsonPath('data.annual_interest_rate', '9.2500');
    }

    public function test_customer_can_create_and_break_fixed_deposit_with_penalty_rules(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(1000);

        $product = DepositProduct::create([
            'name' => 'Standard 12 Month FD',
            'product_code' => 'FD-STD-12',
            'product_type' => 'fixed_deposit',
            'minimum_amount' => 500,
            'maximum_amount' => 5000,
            'term_months' => 12,
            'annual_interest_rate' => 12,
            'allow_early_break' => true,
            'early_break_penalty_rate' => 25,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/fixed-deposits', [
                'product_id' => $product->id,
                'amount' => 600,
            ])
            ->assertCreated()
            ->assertJsonPath('data.principal_amount', '600.00')
            ->assertJsonPath('data.maturity_amount', '672.00');

        $depositId = $response->json('data.id');

        $account->refresh();
        $this->assertSame(400.0, (float) $account->A_Balance);

        $deposit = FixedDeposit::findOrFail($depositId);
        $deposit->update([
            'started_at' => now()->subMonths(6)->toDateString(),
            'maturity_date' => now()->addMonths(6)->toDateString(),
            'projected_interest' => 72,
            'maturity_amount' => 672,
        ]);

        $this->actingAs($user, 'api')
            ->getJson('/api/fixed-deposits')
            ->assertOk()
            ->assertJsonPath('data.0.product.name', 'Standard 12 Month FD');

        $this->actingAs($user, 'api')
            ->postJson('/api/fixed-deposits/' . $depositId . '/break')
            ->assertOk()
            ->assertJsonPath('data.settlement.principal_amount', 600)
            ->assertJsonPath('data.settlement.gross_interest', 36)
            ->assertJsonPath('data.settlement.penalty_amount', 9)
            ->assertJsonPath('data.settlement.net_interest', 27)
            ->assertJsonPath('data.settlement.payout_amount', 627)
            ->assertJsonPath('data.settlement.status', 'broken');

        $account->refresh();
        $this->assertSame(1027.0, (float) $account->A_Balance);

        $this->assertDatabaseHas('transactions', [
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Fixed Deposit Booking',
            'T_Amount' => '600.00',
        ]);

        $this->assertDatabaseHas('transactions', [
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Fixed Deposit Early Break Payout',
            'T_Amount' => '627.00',
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

        return [$user, $customer, $account];
    }
}
