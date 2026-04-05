<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Services\FixedDepositService;
use Illuminate\Http\Request;

class FixedDepositController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
        private readonly FixedDepositService $fixedDepositService
    ) {
    }

    public function index()
    {
        $context = $this->requireBankingContext();

        return response()->json([
            'data' => $this->fixedDepositService->listCustomerDeposits((int) $context->C_ID),
        ]);
    }

    public function show(int $fixedDeposit)
    {
        $context = $this->requireBankingContext();

        return response()->json([
            'data' => $this->fixedDepositService->getCustomerDeposit((int) $context->C_ID, $fixedDeposit),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:deposit_products,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $context = $this->requireBankingContext();
        $deposit = $this->fixedDepositService->createFixedDeposit((int) $context->C_ID, (int) $context->A_Number, $validated);

        return response()->json([
            'message' => 'Fixed deposit created successfully.',
            'data' => $deposit,
        ], 201);
    }

    public function break(int $fixedDeposit)
    {
        $context = $this->requireBankingContext();
        $result = $this->fixedDepositService->breakOrCloseDeposit((int) $context->C_ID, $fixedDeposit);

        return response()->json([
            'message' => 'Fixed deposit settled successfully.',
            'data' => $result,
        ]);
    }

    private function requireBankingContext(): object
    {
        $context = $this->accountService->getUserBankingContext((string) auth('api')->user()->email);

        abort_unless($context, 404, 'Customer profile or account is missing.');

        return $context;
    }
}
