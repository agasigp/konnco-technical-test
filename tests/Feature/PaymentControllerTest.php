<?php

use App\Http\Controllers\PaymentController;
use App\Http\Requests\PaymentStoreRequest;
use App\Models\User;
use App\Models\Transaction;
use Laravel\Passport\Passport;
use JMac\Testing\Traits\AdditionalAssertions;

uses(AdditionalAssertions::class);

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
