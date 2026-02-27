<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Branch;
use App\Models\CardApplication;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_cards_management_page(): void
    {
        [$user] = $this->createUserWithBankingProfile();

        $this->actingAs($user)
            ->get(route('personal.cards'))
            ->assertOk()
            ->assertSee('Cards Management');
    }

    public function test_credit_card_application_requires_employment_fields(): void
    {
        [$user, , $account, $branch] = $this->createUserWithBankingProfile();

        $this->actingAs($user)
            ->from(route('personal.cards.create', ['cardType' => 'credit']))
            ->post(route('personal.cards.store', ['cardType' => 'credit']), [
                'full_name' => 'Test User',
                'date_of_birth' => '1996-05-10',
                'national_id_passport' => 'NID-123456',
                'contact_number' => '1234567890',
                'email_address' => $user->email,
                'residential_address' => 'Address',
                'existing_account_number' => $account->A_Number,
                'account_type' => 'Personal',
                'branch_id' => $branch->B_ID,
                'card_network' => 'Visa',
                'card_design' => 'Classic Blue',
                'delivery_method' => 'home_delivery',
            ])
            ->assertRedirect(route('personal.cards.create', ['cardType' => 'credit']))
            ->assertSessionHasErrors(['occupation', 'employer_name', 'monthly_income', 'source_of_income']);
    }

    public function test_user_can_submit_debit_card_application_and_status_is_pending_review(): void
    {
        [$user, $customer, $account, $branch] = $this->createUserWithBankingProfile();

        $response = $this->actingAs($user)
            ->post(route('personal.cards.store', ['cardType' => 'debit']), [
                'full_name' => 'Test User',
                'date_of_birth' => '1996-05-10',
                'national_id_passport' => 'NID-123456',
                'contact_number' => '1234567890',
                'email_address' => $user->email,
                'residential_address' => 'Address',
                'existing_account_number' => $account->A_Number,
                'account_type' => 'Personal',
                'branch_id' => $branch->B_ID,
                'card_network' => 'MasterCard',
                'card_design' => 'Midnight Black',
                'delivery_method' => 'branch_pickup',
            ]);

        $response
            ->assertRedirect(route('personal.cards'))
            ->assertSessionHas('card_success');

        $this->assertDatabaseHas('card_applications', [
            'C_ID' => $customer->C_ID,
            'card_category' => 'debit',
            'card_network' => 'MasterCard',
            'status' => 'pending_review',
            'B_ID' => $branch->B_ID,
            'existing_account_number' => $account->A_Number,
        ]);

        $application = CardApplication::query()->firstOrFail();
        $this->assertMatchesRegularExpression('/^CARD-\d{8}-[A-Z0-9]{8}$/', $application->application_id);
    }

    public function test_card_application_is_visible_in_personal_dashboard_tracking_section(): void
    {
        [$user, $customer, $account, $branch] = $this->createUserWithBankingProfile();

        $application = CardApplication::create([
            'C_ID' => $customer->C_ID,
            'B_ID' => $branch->B_ID,
            'application_id' => 'CARD-20260227-ABCDEFGH',
            'card_category' => 'debit',
            'card_network' => 'Visa',
            'card_design' => 'Classic Blue',
            'delivery_method' => 'home_delivery',
            'full_name' => 'Test User',
            'date_of_birth' => '1998-01-10',
            'national_id_passport' => 'NID-998877',
            'contact_number' => '1234567890',
            'email_address' => $user->email,
            'residential_address' => 'Address',
            'existing_account_number' => $account->A_Number,
            'account_type' => 'Personal',
            'branch_name' => $branch->B_Name,
            'status' => 'pending_review',
        ]);

        $this->actingAs($user)
            ->get(route('personal.dashboard'))
            ->assertOk()
            ->assertSee($application->application_id)
            ->assertSee('Card Applications');
    }

    private function createUserWithBankingProfile(): array
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
            'A_Balance' => 5000,
            'Operating_Date' => now()->toDateString(),
        ]);

        $branch = Branch::create([
            'B_Name' => 'Main Branch',
            'B_Location' => 'Test City',
            'IFSC_Code' => 'IFSC' . random_int(10000, 99999),
        ]);

        return [$user, $customer, $account, $branch];
    }
}
