<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UserBankingProfileService
{
    public function ensureForUser(
        User $user,
        ?string $phoneNumber = null,
        ?int $preferredAccountNumber = null,
        string $accountType = 'Personal'
    ): Account {
        return DB::transaction(function () use ($user, $phoneNumber, $preferredAccountNumber, $accountType): Account {
            $customer = Customer::query()
                ->where('C_Email', $user->email)
                ->lockForUpdate()
                ->first();

            if (!$customer) {
                $customer = Customer::query()->create([
                    'C_Name' => $user->name,
                    'C_Email' => $user->email,
                    'C_PhoneNumber' => $phoneNumber,
                ]);
            } else {
                $customer->forceFill([
                    'C_Name' => $customer->C_Name ?: $user->name,
                    'C_Email' => $user->email,
                    'C_PhoneNumber' => $customer->C_PhoneNumber ?: $phoneNumber,
                ])->save();
            }

            $account = Account::query()
                ->where('C_ID', $customer->C_ID)
                ->orderBy('A_Number')
                ->lockForUpdate()
                ->first();

            if ($account) {
                if (empty($account->account_type)) {
                    $account->forceFill([
                        'account_type' => $accountType,
                    ])->save();
                }

                return $account;
            }

            $accountNumber = $preferredAccountNumber ?: $this->generateUniqueAccountNumber();

            if ($preferredAccountNumber && Account::query()->whereKey($preferredAccountNumber)->exists()) {
                throw new RuntimeException('The selected account number is already in use.');
            }

            return Account::query()->create([
                'A_Number' => $accountNumber,
                'C_ID' => (int) $customer->C_ID,
                'account_type' => $accountType,
                'A_Balance' => 0,
                'Operating_Date' => now()->toDateString(),
            ]);
        });
    }

    private function generateUniqueAccountNumber(): int
    {
        do {
            $accountNumber = random_int(10000000000, 99999999999);
        } while (Account::query()->whereKey($accountNumber)->exists());

        return $accountNumber;
    }
}
