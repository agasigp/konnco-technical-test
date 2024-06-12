<?php

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it belongs to a user', function () {
    $user = User::factory()
        ->has(Transaction::factory()->count(2))
        ->create();

    $this->assertInstanceOf(Transaction::class, $user->transactions[0]);
});
