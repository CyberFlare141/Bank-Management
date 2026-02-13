<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController; // Re-added
use App\Http\Controllers\AccountController;

Route::get('/customers', [CustomerController::class, 'index']);

Route::get('/accounts', [AccountController::class, 'index']);
Route::post('/accounts', [AccountController::class, 'store']);


Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return "Bank System Working!";
});


