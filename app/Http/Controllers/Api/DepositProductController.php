<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DepositProduct;
use App\Services\FixedDepositService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepositProductController extends Controller
{
    public function __construct(private readonly FixedDepositService $fixedDepositService)
    {
    }

    public function index(Request $request)
    {
        $activeOnly = filter_var($request->query('active_only', 'true'), FILTER_VALIDATE_BOOL);

        return response()->json([
            'data' => $this->fixedDepositService->listProducts($activeOnly),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate($this->rules());
        $product = $this->fixedDepositService->createProduct((int) auth('api')->id(), $validated);

        return response()->json([
            'message' => 'Deposit product created successfully.',
            'data' => $product,
        ], 201);
    }

    public function update(Request $request, DepositProduct $depositProduct)
    {
        $this->ensureAdmin();

        $validated = $request->validate($this->rules($depositProduct->id, true));
        $product = $this->fixedDepositService->updateProduct($depositProduct, $validated);

        return response()->json([
            'message' => 'Deposit product updated successfully.',
            'data' => $product,
        ]);
    }

    private function rules(?int $productId = null, bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        return [
            'name' => [$required, 'string', 'max:255'],
            'product_code' => [$required, 'string', 'max:50', Rule::unique('deposit_products', 'product_code')->ignore($productId)],
            'product_type' => [$required, Rule::in(['fixed_deposit', 'savings'])],
            'minimum_amount' => [$required, 'numeric', 'min:0.01'],
            'maximum_amount' => ['nullable', 'numeric', 'gte:minimum_amount'],
            'term_months' => [$required, 'integer', 'min:1'],
            'annual_interest_rate' => [$required, 'numeric', 'min:0'],
            'allow_early_break' => [$required, 'boolean'],
            'early_break_penalty_rate' => [$required, 'numeric', 'min:0'],
            'status' => [$required, Rule::in(['active', 'inactive'])],
            'description' => ['nullable', 'string'],
        ];
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth('api')->user()?->isAdminUser(), 403, 'Admin access is required.');
    }
}
