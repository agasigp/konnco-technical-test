<?php

use App\Models\User;
use App\Jobs\PaymentUpdate;
use App\Models\Transaction;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use App\Http\Requests\PaymentStoreRequest;
use App\Http\Controllers\PaymentController;
use App\Http\Requests\PaymentUpdateRequest;
use JMac\Testing\Traits\AdditionalAssertions;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(AdditionalAssertions::class, RefreshDatabase::class);

test('it shows list of transactions', function () {
    $user = User::factory()
        ->has(Transaction::factory()->count(2))
        ->create();
    $transactions = $user->transactions()->paginate(10);
    $transactions->withPath(url()->current() . '/api/transactions');
    $transactionsJson = json_encode($transactions);

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
            'data' => json_decode($transactionsJson, true),
        ]);
});

test('it can stores list of transactions in cache', function () {
    Cache::spy();
    $user = User::factory()
        ->has(Transaction::factory()->count(2))
        ->create();
    $transactions = $user->transactions()->paginate(10);
    $transactions->withPath(url()->current() . '/api/transactions');

    Passport::actingAs(
        $user,
        []
    );

    $this->withHeaders([
        'Accept' => 'application/json',
    ])
        ->get(
            '/api/transactions',
        );

    Cache::shouldHaveReceived('remember')
        ->once()
        ->with('transactions', 60, Closure::class);
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

test('it can not edit transaction of other user', function () {
    $user = User::factory()->create();
    $user2 = User::factory()
        ->has(Transaction::factory()->count(1))
        ->create();

    Passport::actingAs(
        $user,
        []
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->put(
        "/api/transactions/{$user2->transactions[0]->id}",
        [
            'amount' => 1000000,
            'status' => 'completed'
        ]
    );

    $response->dump();

    $response->assertStatus(403)
        ->assertJson([
            'message' => 'This action is unauthorized.',
        ]);
});

test('it shows summary of transactions', function () {
    $user = User::factory()
        ->has(
            Transaction::factory()
                ->sequence(
                    ['status' => 'pending', 'amount' => 500000],
                    ['status' => 'pending', 'amount' => 1000000],
                    ['status' => 'completed', 'amount' => 1500000],
                    ['status' => 'completed', 'amount' => 2000000],
                    ['status' => 'failed', 'amount' => 1000000],
                )
                ->count(5)
        )
        ->create();

    $lowestTransaction = json_decode($user->transactions[0]->toJson(), true);
    $highestTransaction = json_decode($user->transactions[3]->toJson(), true);

    Passport::actingAs(
        $user,
        []
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])
        ->get(
            '/api/transactions/summary',
        );

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'OK',
            'data' => [
                "total_transactions" => 5,
                "average_amount" => 1200000,
                "highest_transaction" => $highestTransaction,
                "lowest_transaction" => $lowestTransaction,
                "status_distribution" => [
                    "pending" => 2,
                    "completed" => 2,
                    "failed" => 1,
                ],
            ],
        ]);
});
