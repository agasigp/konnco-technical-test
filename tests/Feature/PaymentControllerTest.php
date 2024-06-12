<?php

use App\Models\Transaction;
use App\Models\User;
use Laravel\Passport\Passport;

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
