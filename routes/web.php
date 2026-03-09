<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PersonalDashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/dashboard', function () {
    if (auth()->user()?->isAdminUser()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('personal.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
});

Route::middleware(['auth', 'non_admin'])->group(function () {
    Route::get('/personal', [PersonalDashboardController::class, 'index'])->name('personal.dashboard');
    Route::get('/personal/cards', [CardController::class, 'index'])->name('personal.cards');
    Route::get('/personal/cards/apply/{cardType}', [CardController::class, 'create'])->name('personal.cards.create');
    Route::post('/personal/cards/apply/{cardType}', [CardController::class, 'store'])->name('personal.cards.store');
    Route::get('/personal/loan', [LoanController::class, 'index'])->name('personal.loan');
    Route::post('/personal/loan/request-password', [LoanController::class, 'requestPasswordVerification'])->name('personal.loan.request-password');
    Route::post('/personal/loan/verify-otp', [LoanController::class, 'verifyOtpAndDisburse'])->name('personal.loan.verify-otp');
    Route::post('/personal/loan/take', [LoanController::class, 'take'])->name('personal.loan.take');
    Route::post('/personal/loan/repay', [LoanController::class, 'repay'])->name('personal.loan.repay');
    Route::post('/personal/quick-actions/transfer', [TransactionController::class, 'transfer'])->name('personal.quick-actions.transfer');
    Route::post('/personal/quick-actions/pay-bill', [TransactionController::class, 'payBill'])->name('personal.quick-actions.pay-bill');
    Route::post('/personal/quick-actions/recharge', [TransactionController::class, 'recharge'])->name('personal.quick-actions.recharge');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::post('/admin/loan-requests/{loanRequest}/accept', [AdminController::class, 'acceptLoanRequest'])->name('admin.loans.accept');
    Route::post('/admin/loan-requests/{loanRequest}/reject', [AdminController::class, 'rejectLoanRequest'])->name('admin.loans.reject');
    Route::post('/admin/card-applications/{cardApplication}/accept', [AdminController::class, 'acceptCardApplication'])->name('admin.cards.accept');
    Route::post('/admin/card-applications/{cardApplication}/reject', [AdminController::class, 'rejectCardApplication'])->name('admin.cards.reject');
});

require __DIR__.'/auth.php';
