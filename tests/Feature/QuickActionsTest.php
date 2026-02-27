<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuickActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_transfer_to_internal_account(): void
    {
        [$senderUser, $senderCustomer, $senderAccount] = $this->createUserWithBankingProfile(1000);
        [$recipientUser, $recipientCustomer, $recipientAccount] = $this->createUserWithBankingProfile(300);

        $this->actingAs($senderUser)
            ->post(route('personal.quick-actions.transfer'), [
                'recipient_identifier' => (string) $recipientAccount->A_Number,
                'amount' => 250,
                'note' => 'Monthly support',
                'quick_action_password' => 'password',
            ])
            ->assertRedirect(route('personal.dashboard'))
            ->assertSessionHas('quick_action_success');

        $senderAccount->refresh();
        $recipientAccount->refresh();

        $this->assertSame(750.0, (float) $senderAccount->A_Balance);
        $this->assertSame(550.0, (float) $recipientAccount->A_Balance);

        $this->assertDatabaseHas('transactions', [
            'A_Number' => $senderAccount->A_Number,
            'C_ID' => $senderCustomer->C_ID,
            'T_Amount' => '250.00',
        ]);

        $this->assertDatabaseHas('transactions', [
            'A_Number' => $recipientAccount->A_Number,
            'C_ID' => $recipientCustomer->C_ID,
            'T_Type' => 'Fund Transfer Received from ' . $senderAccount->A_Number,
            'T_Amount' => '250.00',
        ]);

        $this->assertDatabaseHas('quick_actions', [
            'user_id' => $senderUser->id,
            'C_ID' => $senderCustomer->C_ID,
            'A_Number' => $senderAccount->A_Number,
            'action_type' => 'fund_transfer',
            'channel' => 'internal',
            'recipient_identifier' => (string) $recipientAccount->A_Number,
            'amount' => '250.00',
            'note' => 'Monthly support',
            'status' => 'success',
        ]);

        $this->assertNotNull($recipientUser->id);
    }

    public function test_external_transfer_fails_when_balance_is_insufficient(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(120);

        $this->actingAs($user)
            ->post(route('personal.quick-actions.transfer'), [
                'recipient_identifier' => 'WALLET-EXT-90',
                'amount' => 500,
                'quick_action_password' => 'password',
            ])
            ->assertRedirect(route('personal.dashboard'))
            ->assertSessionHas('quick_action_error', 'Insufficient balance for this transfer.');

        $account->refresh();
        $this->assertSame(120.0, (float) $account->A_Balance);

        $this->assertDatabaseMissing('transactions', [
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Amount' => '500.00',
        ]);
    }

    public function test_user_can_pay_bill(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(900);

        $this->actingAs($user)
            ->post(route('personal.quick-actions.pay-bill'), [
                'bill_type' => 'electricity',
                'bill_number' => 'DESCO-102938',
                'amount' => 400,
                'quick_action_password' => 'password',
            ])
            ->assertRedirect(route('personal.dashboard'))
            ->assertSessionHas('quick_action_success');

        $account->refresh();
        $this->assertSame(500.0, (float) $account->A_Balance);

        $this->assertDatabaseHas('transactions', [
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Bill Payment - Electricity (DESCO-102938)',
            'T_Amount' => '400.00',
        ]);

        $this->assertDatabaseHas('quick_actions', [
            'user_id' => $user->id,
            'C_ID' => $customer->C_ID,
            'A_Number' => $account->A_Number,
            'action_type' => 'bill_payment',
            'bill_type' => 'electricity',
            'bill_number' => 'DESCO-102938',
            'amount' => '400.00',
            'status' => 'success',
        ]);
    }

    public function test_recharge_cannot_exceed_fifty_thousand_taka(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(100000);

        $this->actingAs($user)
            ->post(route('personal.quick-actions.recharge'), [
                'recharge_app' => 'Bkash',
                'recipient' => '01700000000',
                'amount' => 50001,
                'quick_action_password' => 'password',
            ])
            ->assertSessionHasErrors(['amount']);

        $account->refresh();
        $this->assertSame(100000.0, (float) $account->A_Balance);

        $this->assertDatabaseMissing('transactions', [
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Recharge - Bkash (01700000000)',
            'T_Amount' => '50001.00',
        ]);
    }

    public function test_recharge_adds_money_to_account_balance(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(1200);

        $this->actingAs($user)
            ->post(route('personal.quick-actions.recharge'), [
                'recharge_app' => 'Rocket',
                'recipient' => '01711111111',
                'amount' => 800,
                'quick_action_password' => 'password',
            ])
            ->assertRedirect(route('personal.dashboard'))
            ->assertSessionHas('quick_action_success');

        $account->refresh();
        $this->assertSame(2000.0, (float) $account->A_Balance);

        $this->assertDatabaseHas('transactions', [
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Recharge Received - Rocket (01711111111)',
            'T_Amount' => '800.00',
        ]);

        $this->assertDatabaseHas('quick_actions', [
            'user_id' => $user->id,
            'C_ID' => $customer->C_ID,
            'A_Number' => $account->A_Number,
            'action_type' => 'recharge',
            'provider' => 'Rocket',
            'recipient_identifier' => '01711111111',
            'amount' => '800.00',
            'status' => 'success',
        ]);
    }

    public function test_quick_action_fails_when_password_is_incorrect(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(2000);

        $this->actingAs($user)
            ->post(route('personal.quick-actions.pay-bill'), [
                'bill_type' => 'water',
                'bill_number' => 'WASA-01',
                'amount' => 200,
                'quick_action_password' => 'wrong-password',
            ])
            ->assertRedirect(route('personal.dashboard'))
            ->assertSessionHas('quick_action_error', 'Security password is incorrect.');

        $account->refresh();
        $this->assertSame(2000.0, (float) $account->A_Balance);

        $this->assertDatabaseMissing('transactions', [
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Bill Payment - Water (WASA-01)',
            'T_Amount' => '200.00',
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
