<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Services\TransactionHistoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionInsightsController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
        private readonly TransactionHistoryService $transactionHistoryService
    ) {
    }

    public function history(Request $request)
    {
        $validated = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'type' => ['nullable', 'string', 'max:100'],
            'exact_type' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $context = $this->requireBankingContext();

        return response()->json([
            'data' => $this->transactionHistoryService->getHistory((int) $context->A_Number, $validated),
        ]);
    }

    public function monthlySummaries(Request $request)
    {
        $validated = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'type' => ['nullable', 'string', 'max:100'],
            'exact_type' => ['nullable', 'string', 'max:255'],
        ]);

        $context = $this->requireBankingContext();

        return response()->json([
            'data' => $this->transactionHistoryService->getMonthlySummaries((int) $context->A_Number, $validated),
        ]);
    }

    public function miniStatement(Request $request)
    {
        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $context = $this->requireBankingContext();

        return response()->json([
            'data' => [
                'account_number' => (int) $context->A_Number,
                'balance' => (float) $context->A_Balance,
                'transactions' => $this->transactionHistoryService->getMiniStatement(
                    (int) $context->A_Number,
                    (int) ($validated['limit'] ?? 5)
                ),
            ],
        ]);
    }

    public function downloadStatement(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'type' => ['nullable', 'string', 'max:100'],
            'exact_type' => ['nullable', 'string', 'max:255'],
        ]);

        $context = $this->requireBankingContext();
        $rows = $this->transactionHistoryService->getStatementRows((int) $context->A_Number, $validated);
        $filename = 'statement-' . $context->A_Number . '-' . now()->format('YmdHis') . '.csv';

        return response()->streamDownload(function () use ($rows, $context): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['account_number', 'balance_snapshot', 'transaction_id', 'type', 'amount', 'transaction_date']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $context->A_Number,
                    number_format((float) $context->A_Balance, 2, '.', ''),
                    $row->T_ID,
                    $row->T_Type,
                    number_format((float) $row->T_Amount, 2, '.', ''),
                    $row->T_Date?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function requireBankingContext(): object
    {
        $context = $this->accountService->getUserBankingContext((string) auth('api')->user()->email);

        abort_unless($context, 404, 'Customer profile or account is missing.');

        return $context;
    }
}
