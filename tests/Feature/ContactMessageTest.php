<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_requires_authentication(): void
    {
        $response = $this->get(route('contact.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_contact_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('contact.create'));

        $response->assertOk();
        $response->assertSee('Contact Us');
        $response->assertSee($user->name);
        $response->assertSee($user->email);
    }

    public function test_subject_and_message_are_required(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('contact.create'))
            ->post(route('contact.store'), [
                'recipient' => 'all',
                'subject' => '',
                'message' => '',
            ]);

        $response->assertRedirect(route('contact.create'));
        $response->assertSessionHasErrors(['subject', 'message']);
        Mail::assertNothingSent();
    }

    public function test_contact_message_is_sent_to_all_recipients(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('contact.store'), [
                'recipient' => 'all',
                'subject' => 'Need help with transfer issue',
                'message' => 'Transfer failed and amount was deducted. Please check.',
            ]);

        $response->assertRedirect(route('contact.create'));
        $response->assertSessionHas('status');

        Mail::assertSent(ContactMessageMail::class, function (ContactMessageMail $mail) {
            return $mail->hasTo('sazid.cse.20230104140@aust.edu')
                && $mail->hasCc('samiul.cse.20230104142@aust.edu')
                && $mail->hasCc('masrafi.cse.20230104141@aust.edu');
        });
    }

    public function test_contact_message_can_be_sent_to_single_selected_recipient(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post(route('contact.store'), [
                'recipient' => 'masrafi',
                'subject' => 'UI suggestion',
                'message' => 'Please add transaction filters in dashboard.',
            ]);

        Mail::assertSent(ContactMessageMail::class, function (ContactMessageMail $mail) {
            return $mail->hasTo('masrafi.cse.20230104141@aust.edu')
                && ! $mail->hasCc('sazid.cse.20230104140@aust.edu')
                && ! $mail->hasCc('samiul.cse.20230104142@aust.edu');
        });
    }
}
