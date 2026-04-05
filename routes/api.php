<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Api\DepositProductController;
use App\Http\Controllers\Api\FixedDepositController;
use App\Http\Controllers\Api\TransactionInsightsController;

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Validator;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::get('account/profile', [AccountController::class, 'profile']);
    Route::get('transactions/history', [TransactionInsightsController::class, 'history']);
    Route::get('transactions/monthly-summaries', [TransactionInsightsController::class, 'monthlySummaries']);
    Route::get('accounts/mini-statement', [TransactionInsightsController::class, 'miniStatement']);
    Route::get('transactions/statement/download', [TransactionInsightsController::class, 'downloadStatement']);
    Route::get('deposit-products', [DepositProductController::class, 'index']);
    Route::post('deposit-products', [DepositProductController::class, 'store']);
    Route::put('deposit-products/{depositProduct}', [DepositProductController::class, 'update']);
    Route::get('fixed-deposits', [FixedDepositController::class, 'index']);
    Route::post('fixed-deposits', [FixedDepositController::class, 'store']);
    Route::get('fixed-deposits/{fixedDeposit}', [FixedDepositController::class, 'show']);
    Route::post('fixed-deposits/{fixedDeposit}/break', [FixedDepositController::class, 'break']);
});


Route::post('customers', [CustomerController::class, 'store']);

Route::get('test', function () {
    return "API WORKING";
});

Route::post('customers', [CustomerController::class, 'store'])->middleware('auth:api');

