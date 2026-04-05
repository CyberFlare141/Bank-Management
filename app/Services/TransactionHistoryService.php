<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TransactionHistoryService
{
    public function getHistory(int $accountNumber, array $filters = []): LengthAwarePaginator
    {
        $perPage = min(max((int) ($filters['per_page'] ?? 15), 1), 100);

        return $this->buildBaseQuery($accountNumber, $filters)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getStatementRows(int $accountNumber, array $filters = []): Collection
    {
        return $this->buildBaseQuery($accountNumber, $filters)->get();
    }

    public function getMiniStatement(int $accountNumber, int $limit = 5): Collection
    {
        $limit = min(max($limit, 1), 20);

        return Transaction::query()
            ->where('A_Number', $accountNumber)
            ->latest('T_Date')
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    public function getMonthlySummaries(int $accountNumber, array $filters = []): Collection
    {
        $rows = $this->buildBaseQuery($accountNumber, $filters)->get();

        return $rows
            ->groupBy(fn (Transaction $transaction) => $transaction->T_Date->format('Y-m'))
            ->map(function (Collection $transactions, string $month): array {
                $credits = 0.0;
                $debits = 0.0;

                foreach ($transactions as $transaction) {
                    $amount = (float) $transaction->T_Amount;

                    if ($this->isCreditType((string) $transaction->T_Type)) {
                        $credits += $amount;
                    } else {
                        $debits += $amount;
                    }
                }

                return [
                    'month' => $month,
                    'transaction_count' => $transactions->count(),
                    'total_credits' => round($credits, 2),
                    'total_debits' => round($debits, 2),
                    'net_amount' => round($credits - $debits, 2),
                ];
            })
            ->sortKeysDesc()
            ->values();
    }

    private function buildBaseQuery(int $accountNumber, array $filters = []): Builder
    {
        return Transaction::query()
            ->where('A_Number', $accountNumber)
            ->when($filters['from_date'] ?? null, function (Builder $query, string $fromDate) {
                $query->where('T_Date', '>=', Carbon::parse($fromDate)->startOfDay());
            })
            ->when($filters['to_date'] ?? null, function (Builder $query, string $toDate) {
                $query->where('T_Date', '<=', Carbon::parse($toDate)->endOfDay());
            })
            ->forFilterType($filters['type'] ?? null)
            ->when($filters['exact_type'] ?? null, function (Builder $query, string $exactType) {
                $query->where('T_Type', $exactType);
            })
            ->latest('T_Date')
            ->latest('created_at');
    }

    private function isCreditType(string $type): bool
    {
        return str_starts_with($type, 'Fund Transfer Received')
            || str_starts_with($type, 'Recharge Received')
            || $type === 'Loan Disbursement'
            || $type === 'Fixed Deposit Maturity Payout'
            || $type === 'Fixed Deposit Early Break Payout';
    }
}
