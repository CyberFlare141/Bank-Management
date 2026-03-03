<?php

namespace App\Http\Controllers;

use App\Services\AccountService;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    public function __construct(private readonly AccountService $accountService)
    {
    }

    public function profile(): JsonResponse
    {
        $user = auth()->user();
        $context = $this->accountService->getUserBankingContext((string) $user->email);

        if (!$context) {
            return response()->json([
                'message' => 'Customer profile or account is missing.',
            ], 404);
        }

        return response()->json([
            'data' => $context,
        ]);
    }
}
