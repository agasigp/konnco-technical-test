<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(function () {
    Route::get('/user', ProfileController::class);
    Route::apiResource('transactions', PaymentController::class);
});

Route::post('login', [AuthController::class, 'login']);
