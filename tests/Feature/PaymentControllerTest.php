<?php

use App\Models\User;
use App\Jobs\PaymentUpdate;
use App\Models\Transaction;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Queue;
use App\Http\Requests\PaymentStoreRequest;
use App\Http\Controllers\PaymentController;
use App\Http\Requests\PaymentUpdateRequest;
use JMac\Testing\Traits\AdditionalAssertions;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(AdditionalAssertions::class, RefreshDatabase::class);

test('it shows list of transactions', function () {
    $user = User::factory()
        ->has(Transaction::factory()->count(11))
        ->create();
    $transactions = $user->transactions()->paginate(10);
    $transactions->withPath(url()->current() . '/api/transactions');
    $transactions = json_encode($transactions);

    Passport::actingAs(
        $user,
        []
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])
        ->get(
            '/api/transactions',
        );

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'OK',
            'data' => json_decode($transactions, true),
        ]);
});

test('it can create new transsaction', function () {
    $user = User::factory()->create();

    Passport::actingAs(
        $user,
        []
    );

    $response = $this->postJson(
        '/api/transactions',
        [
            'amount' => 1000000,
        ]
    );

    $response->assertStatus(201)
        ->assertJson([
            'status' => 'OK',
            'data' => [],
        ]);

    $this->assertDatabaseHas('transactions', [
        'amount' => 1000000,
        'status' => 'pending',
        'user_id' => $user->id,
    ]);

    $this->assertActionUsesFormRequest(
        PaymentController::class,
        'store',
        PaymentStoreRequest::class
    );
});

test('it can update transaction with status completed/failed', function () {
    $user = User::factory()
        ->has(Transaction::factory()->count(1))
        ->create();
    $transaction = $user->transactions[0];

    Passport::actingAs(
        $user,
        []
    );

    Queue::fake();

    $response = $this->put(
        "/api/transactions/{$transaction->id}",
        [
            'amount' => 1000000,
            'status' => 'completed'
        ]
    );

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'OK',
            'data' => [],
        ]);

    Queue::assertPushed(function (PaymentUpdate $job) use ($transaction) {
        return $job->transaction->id === $transaction->id;
    });

    $response = $this->put(
        "/api/transactions/{$transaction->id}",
        [
            'amount' => 500000,
            'status' => 'failed'
        ]
    );

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'OK',
            'data' => [],
        ]);

    Queue::assertPushed(function (PaymentUpdate $job) use ($transaction) {
        return $job->transaction->id === $transaction->id;
    });

    $this->assertActionUsesFormRequest(
        PaymentController::class,
        'update',
        PaymentUpdateRequest::class
    );
});
