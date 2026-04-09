<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AdminBootstrapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBootstrapperTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_the_default_admin_from_environment_configuration(): void
    {
        config()->set('app.env', 'local');

        putenv('ADMIN_EMAIL=bootstrap-admin@example.com');
        putenv('ADMIN_NAME=Bootstrap Admin');
        putenv('ADMIN_PASSWORD=secret12345');
        putenv('ADMIN_ACCOUNT_NUMBER=10000000001');

        $_ENV['ADMIN_EMAIL'] = 'bootstrap-admin@example.com';
        $_ENV['ADMIN_NAME'] = 'Bootstrap Admin';
        $_ENV['ADMIN_PASSWORD'] = 'secret12345';
        $_ENV['ADMIN_ACCOUNT_NUMBER'] = '10000000001';
        $_SERVER['ADMIN_EMAIL'] = 'bootstrap-admin@example.com';
        $_SERVER['ADMIN_NAME'] = 'Bootstrap Admin';
        $_SERVER['ADMIN_PASSWORD'] = 'secret12345';
        $_SERVER['ADMIN_ACCOUNT_NUMBER'] = '10000000001';

        app(AdminBootstrapper::class)->ensureDefaultAdmin();

        $admin = User::query()->where('email', 'bootstrap-admin@example.com')->first();

        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdminUser());
        $this->assertTrue(password_verify('secret12345', $admin->password));
        $this->assertNotNull($admin->account);
        $this->assertSame('10000000001', (string) $admin->account->A_Number);
    }
}
