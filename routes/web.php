<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PersonalDashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\CardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return redirect()->route('personal.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/personal', [PersonalDashboardController::class, 'index'])->name('personal.dashboard');
    Route::get('/personal/cards', [CardController::class, 'index'])->name('personal.cards');
    Route::get('/personal/cards/apply/{cardType}', [CardController::class, 'create'])->name('personal.cards.create');
    Route::post('/personal/cards/apply/{cardType}', [CardController::class, 'store'])->name('personal.cards.store');
    Route::get('/personal/loan', [LoanController::class, 'index'])->name('personal.loan');
    Route::post('/personal/loan/request-password', [LoanController::class, 'requestPasswordVerification'])->name('personal.loan.request-password');
    Route::post('/personal/loan/verify-otp', [LoanController::class, 'verifyOtpAndDisburse'])->name('personal.loan.verify-otp');
    Route::post('/personal/loan/take', [LoanController::class, 'take'])->name('personal.loan.take');
    Route::post('/personal/loan/repay', [LoanController::class, 'repay'])->name('personal.loan.repay');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
