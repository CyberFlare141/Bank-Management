<?php

namespace Database\Seeders;

use App\Services\UserBankingProfileService;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $adminEmail = trim((string) env('ADMIN_EMAIL', 'admin@example.com'));
        $adminName = trim((string) env('ADMIN_NAME', 'Admin User'));
        $adminPassword = (string) env('ADMIN_PASSWORD', 'admin12345');
        $adminAccountNumber = trim((string) env('ADMIN_ACCOUNT_NUMBER', ''));

        User::query()->updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName !== '' ? $adminName : 'Admin User',
                'password' => $adminPassword,
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $adminUser = User::query()->where('email', $adminEmail)->first();

        if ($adminUser) {
            app(UserBankingProfileService::class)->ensureForUser(
                $adminUser,
                preferredAccountNumber: preg_match('/^\d{11}$/', $adminAccountNumber) ? (int) $adminAccountNumber : null
            );
        }
    }
}
