<?php

namespace App\Support;

use App\Models\User;
use App\Services\UserBankingProfileService;
use RuntimeException;

class AdminBootstrapper
{
    public function __construct(
        private readonly UserBankingProfileService $userBankingProfileService
    ) {
    }

    public function ensureDefaultAdmin(): ?User
    {
        $adminEmail = trim((string) env('ADMIN_EMAIL', 'admin@example.com'));

        if ($adminEmail === '') {
            return null;
        }

        $adminName = trim((string) env('ADMIN_NAME', 'Admin User'));
        $adminPassword = (string) env('ADMIN_PASSWORD', 'admin12345');
        $adminAccountNumber = trim((string) env('ADMIN_ACCOUNT_NUMBER', ''));

        $adminUser = User::query()->updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName !== '' ? $adminName : 'Admin User',
                'password' => $adminPassword,
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $preferredAccountNumber = preg_match('/^\d{11}$/', $adminAccountNumber) ? (int) $adminAccountNumber : null;

        try {
            $this->userBankingProfileService->ensureForUser(
                $adminUser,
                preferredAccountNumber: $adminUser->account ? null : $preferredAccountNumber
            );
        } catch (RuntimeException) {
            // Fall back to any available account number so local bootstrapping never blocks startup.
            $this->userBankingProfileService->ensureForUser($adminUser);
        }

        return $adminUser;
    }
}
