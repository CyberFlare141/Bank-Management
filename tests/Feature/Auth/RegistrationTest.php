<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Storage::fake('local');

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'account_type' => 'normal',
            'nid_or_birth_certificate' => UploadedFile::fake()->create('nid.pdf', 200, 'application/pdf'),
            'photo' => UploadedFile::fake()->image('photo.jpg'),
            'job_id' => UploadedFile::fake()->create('job-id.pdf', 200, 'application/pdf'),
            'electric_bill' => UploadedFile::fake()->create('bill.pdf', 200, 'application/pdf'),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'test@example.com')->firstOrFail();

        $this->assertDatabaseHas('user_documents', [
            'user_id' => $user->id,
            'account_type' => 'normal',
            'student_id' => null,
        ]);

        $documentRow = \App\Models\UserDocument::where('user_id', $user->id)->firstOrFail();
        Storage::disk('local')->assertExists($documentRow->nid_or_birth_certificate);
        Storage::disk('local')->assertExists($documentRow->photo);
        Storage::disk('local')->assertExists($documentRow->job_id);
        Storage::disk('local')->assertExists($documentRow->electric_bill);
    }
}
