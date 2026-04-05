<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\AccountService;
use App\Services\TransactionHistoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StatementController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
        private readonly TransactionHistoryService $transactionHistoryService
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'type' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $user = auth()->user();
        $context = $this->accountService->getUserBankingContext((string) $user->email);
        abort_unless($context, 404, 'Customer profile or account is missing.');

        return view('personal.statements', [
            'user' => $user,
            'account' => $context,
            'filters' => [
                'from_date' => $filters['from_date'] ?? '',
                'to_date' => $filters['to_date'] ?? '',
                'type' => $filters['type'] ?? '',
                'per_page' => (int) ($filters['per_page'] ?? 10),
            ],
            'availableTypes' => [
                Transaction::TYPE_CREDIT => 'Credit',
                Transaction::TYPE_DEBIT => 'Debit',
                Transaction::TYPE_TRANSFER => 'Transfer',
                Transaction::TYPE_BILL_PAYMENT => 'Bill Payment',
                Transaction::TYPE_RECHARGE => 'Recharge',
                Transaction::TYPE_LOAN => 'Loan',
                Transaction::TYPE_FIXED_DEPOSIT => 'Fixed Deposit',
            ],
            'transactions' => $this->transactionHistoryService->getHistory((int) $context->A_Number, $filters),
            'monthlySummaries' => $this->transactionHistoryService->getMonthlySummaries((int) $context->A_Number, $filters),
            'miniStatement' => $this->transactionHistoryService->getMiniStatement((int) $context->A_Number, 5),
        ]);
    }

    public function download(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'type' => ['nullable', 'string', 'max:100'],
        ]);

        $context = $this->accountService->getUserBankingContext((string) auth()->user()->email);
        abort_unless($context, 404, 'Customer profile or account is missing.');

        $rows = $this->transactionHistoryService->getStatementRows((int) $context->A_Number, $filters);
        $filename = 'statement-' . $context->A_Number . '-' . now()->format('YmdHis') . '.csv';

        return response()->streamDownload(function () use ($rows, $context): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['transaction_id', 'account_number', 'type', 'amount', 'transaction_date']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->T_ID,
                    $context->A_Number,
                    $row->T_Type,
                    number_format((float) $row->T_Amount, 2, '.', ''),
                    $row->T_Date?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
