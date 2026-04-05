<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatementPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_statement_page_is_displayed_with_filtered_transactions(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(2000);

        Transaction::create([
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Recharge Received - Rocket (01711111111)',
            'T_Amount' => 400,
            'T_Date' => '2026-04-01 10:00:00',
        ]);

        Transaction::create([
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Bill Payment - Electricity (DESCO-908)',
            'T_Amount' => 250,
            'T_Date' => '2026-04-02 12:00:00',
        ]);

        $this->actingAs($user)
            ->get(route('personal.statements', ['type' => 'recharge']))
            ->assertOk()
            ->assertSee('Transaction Statements')
            ->assertSee('Recharge Received - Rocket (01711111111)')
            ->assertDontSee('Bill Payment - Electricity (DESCO-908)');
    }

    public function test_statement_download_returns_csv(): void
    {
        [$user, $customer, $account] = $this->createUserWithBankingProfile(2000);

        Transaction::create([
            'A_Number' => $account->A_Number,
            'C_ID' => $customer->C_ID,
            'T_Type' => 'Fund Transfer Received from 90909090909',
            'T_Amount' => 700,
            'T_Date' => '2026-04-03 11:30:00',
        ]);

        $response = $this->actingAs($user)
            ->get(route('personal.statements.download', ['type' => 'credit']));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('Fund Transfer Received from 90909090909', $response->streamedContent());
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
