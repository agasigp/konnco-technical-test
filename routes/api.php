<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(function () {
    Route::get('/user', ProfileController::class)->name('user.profile');
    Route::apiResource('transactions', PaymentController::class)
        ->only(['index', 'store', 'update']);
    Route::get('transactions/summary', [PaymentController::class, 'summary'])
        ->name('transaction.summary');
});

Route::post('login', [AuthController::class, 'login'])->name('user.login');
