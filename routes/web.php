<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

// ---------- Login, register and logout ----------

// "guest" = only for people who are NOT logged in
Route::get('login', [LoginController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('login', [LoginController::class, 'store'])
    ->middleware(['guest', 'throttle:5,1']); // max 5 attempts per minute

Route::get('register', [RegisterController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('register', [RegisterController::class, 'store'])
    ->middleware(['guest', 'throttle:5,1'])
    ->name('register.store');

Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ---------- Pages that need a logged-in librarian ----------

Route::middleware('auth')->group(function () {

    // Home page: the dashboard
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('books', BookController::class)->except('show');
    Route::resource('members', MemberController::class)->except('show');

    // Only the 3 loan routes we need: list, borrow form, save
    Route::resource('loans', LoanController::class)->only(['index', 'create', 'store']);

    // A custom route for returning a book (PATCH changes part of a record)
    Route::patch('loans/{loan}/return', [LoanController::class, 'returnBook'])
        ->name('loans.return');
});
