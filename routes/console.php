<?php

use App\Services\UserBankingProfileService;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:create {email : The admin email address} {--name= : Admin display name} {--password= : Admin password} {--phone= : Banking profile phone number} {--account-number= : Preferred 11-digit account number}', function (UserBankingProfileService $userBankingProfileService): int {
    $email = trim((string) $this->argument('email'));
    $name = trim((string) ($this->option('name') ?: 'Admin User'));
    $password = (string) $this->option('password');
    $phone = ($this->option('phone') !== null && trim((string) $this->option('phone')) !== '') ? trim((string) $this->option('phone')) : null;
    $accountNumberOption = trim((string) ($this->option('account-number') ?? ''));
    $accountNumber = $accountNumberOption !== '' ? (int) $accountNumberOption : null;

    if ($email === '') {
        $this->error('Email is required.');

        return 1;
    }

    $user = User::query()->where('email', $email)->first();

    if (!$user && $password === '') {
        $this->error('A password is required when creating a brand-new admin user.');

        return 1;
    }

    if ($accountNumberOption !== '' && !preg_match('/^\d{11}$/', $accountNumberOption)) {
        $this->error('Account number must be exactly 11 digits.');

        return 1;
    }

    if ($user) {
        $user->fill([
            'name' => $name !== '' ? $name : $user->name,
            'is_admin' => true,
        ]);

        if ($password !== '') {
            $user->password = $password;
        }

        $user->email_verified_at ??= now();
        $user->save();

        $account = $userBankingProfileService->ensureForUser($user, $phone, $accountNumber);

        $this->info('Existing user promoted to admin successfully.');
        $this->line('Admin account number: ' . $account->A_Number);

        return 0;
    }

    $user = User::query()->create([
        'name' => $name !== '' ? $name : 'Admin User',
        'email' => $email,
        'password' => $password,
        'is_admin' => true,
        'email_verified_at' => now(),
    ]);

    $account = $userBankingProfileService->ensureForUser($user, $phone, $accountNumber);

    $this->info('Admin user created successfully.');
    $this->line('Admin account number: ' . $account->A_Number);

    return 0;
})->purpose('Create or promote an admin user');

Artisan::command('admin:revoke {email : The admin email address}', function (): int {
    $email = trim((string) $this->argument('email'));

    $user = User::query()->where('email', $email)->first();

    if (!$user) {
        $this->error('User not found.');

        return 1;
    }

    $user->forceFill([
        'is_admin' => false,
    ])->save();

    $this->info('Admin access revoked successfully.');

    return 0;
})->purpose('Remove admin access from a user');
