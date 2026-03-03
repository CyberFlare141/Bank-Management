<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class AccountService
{
    public function getUserBankingContext(string $userEmail): ?object
    {
        return DB::selectOne(
            'SELECT
                c.C_ID,
                c.C_Name,
                c.C_Email,
                a.A_Number,
                a.A_Balance
             FROM customers c
             LEFT JOIN accounts a ON a.C_ID = c.C_ID
             WHERE c.C_Email = ?
             LIMIT 1',
            [$userEmail]
        );
    }

    public function getCustomerLoans(int $customerId): array
    {
        return DB::select(
            'SELECT *
             FROM loans
             WHERE C_ID = ?
             ORDER BY created_at DESC',
            [$customerId]
        );
    }

    public function getCustomerLoanRequests(int $customerId): array
    {
        return DB::select(
            'SELECT *
             FROM loan_requests
             WHERE C_ID = ?
             ORDER BY created_at DESC',
            [$customerId]
        );
    }

    public function getCardApplications(int $customerId): array
    {
        return DB::select(
            'SELECT *
             FROM card_applications
             WHERE C_ID = ?
             ORDER BY created_at DESC',
            [$customerId]
        );
    }

    public function getBranchesWithBootstrap(): array
    {
        $this->ensureDefaultDhakaBranches();

        return DB::select(
            'SELECT B_ID, B_Name, B_Location, IFSC_Code
             FROM branches
             ORDER BY B_Name ASC'
        );
    }

    public function getBranchById(int $branchId): ?object
    {
        return DB::selectOne(
            'SELECT B_ID, B_Name, B_Location, IFSC_Code FROM branches WHERE B_ID = ? LIMIT 1',
            [$branchId]
        );
    }

    public function createCardApplication(int $customerId, array $payload): string
    {
        $applicationId = $this->generateApplicationId();
        $branch = $this->getBranchById((int) $payload['branch_id']);

        if (!$branch) {
            throw new RuntimeException('Selected branch not found.');
        }

        DB::insert(
            'INSERT INTO card_applications
                (C_ID, B_ID, application_id, card_category, card_network, card_design, delivery_method, full_name, date_of_birth,
                 national_id_passport, contact_number, email_address, residential_address, existing_account_number, account_type,
                 branch_name, occupation, employer_name, monthly_income, source_of_income, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
            [
                $customerId,
                (int) $branch->B_ID,
                $applicationId,
                $payload['card_category'],
                $payload['card_network'],
                $payload['card_design'],
                $payload['delivery_method'],
                $payload['full_name'],
                $payload['date_of_birth'],
                $payload['national_id_passport'],
                $payload['contact_number'],
                $payload['email_address'],
                $payload['residential_address'],
                $payload['existing_account_number'],
                $payload['account_type'],
                $branch->B_Name,
                $payload['occupation'],
                $payload['employer_name'],
                $payload['monthly_income'],
                $payload['source_of_income'],
                'pending_review',
            ]
        );

        return $applicationId;
    }

    public function hasApplicationId(string $applicationId): bool
    {
        $row = DB::selectOne(
            'SELECT App_ID FROM card_applications WHERE application_id = ? LIMIT 1',
            [$applicationId]
        );

        return $row !== null;
    }

    public function generateApplicationId(): string
    {
        do {
            $reference = 'CARD-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        } while ($this->hasApplicationId($reference));

        return $reference;
    }

    private function ensureDefaultDhakaBranches(): void
    {
        $existing = DB::selectOne('SELECT B_ID FROM branches LIMIT 1');
        if ($existing) {
            return;
        }

        $branches = [
            ['Dhanmondi Branch', 'Dhanmondi, Dhaka', 'DHKMARS001'],
            ['Gulshan Branch', 'Gulshan, Dhaka', 'DHKMARS002'],
            ['Uttara Branch', 'Uttara, Dhaka', 'DHKMARS003'],
            ['Mirpur Branch', 'Mirpur, Dhaka', 'DHKMARS004'],
            ['Motijheel Branch', 'Motijheel, Dhaka', 'DHKMARS005'],
        ];

        foreach ($branches as $branch) {
            DB::insert(
                'INSERT INTO branches (B_Name, B_Location, IFSC_Code, created_at, updated_at)
                 SELECT ?, ?, ?, NOW(), NOW()
                 FROM DUAL
                 WHERE NOT EXISTS (
                    SELECT 1 FROM branches WHERE IFSC_Code = ?
                 )',
                [$branch[0], $branch[1], $branch[2], $branch[2]]
            );
        }
    }
}
