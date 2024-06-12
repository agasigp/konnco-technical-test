<?php

use App\Models\User;
use App\Jobs\PaymentUpdate;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can update amount & status of transaction', function () {
    $transaction = Transaction::factory()
        ->pending()
        ->for(User::factory()->create())
        ->create(['status' => 'pending']);
    $paymentUpdateJob = new PaymentUpdate($transaction, 500000, 'completed');

    $paymentUpdateJob->handle();
    $this->assertDatabaseHas('transactions', [
        'amount' => 500000,
        'status' => 'completed'
    ]);

    $transaction2 = Transaction::factory()->pending()
        ->for(User::factory()->create())
        ->create(['status' => 'pending']);
    $paymentUpdateJob2 = new PaymentUpdate($transaction2, 1000000, 'failed');

    $paymentUpdateJob2->handle();
    $this->assertDatabaseHas('transactions', [
        'amount' => 1000000,
        'status' => 'failed'
    ]);
});
