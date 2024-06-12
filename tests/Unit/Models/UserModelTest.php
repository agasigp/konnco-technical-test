<?php

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it has many transactions', function () {
    $user = User::factory()
        ->has(Transaction::factory()->count(2))
        ->create();
    $transaction = Transaction::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->assertTrue($user->transactions->contains($transaction));
    $this->assertEquals(3, $user->transactions->count());
    $this->assertInstanceOf(Collection::class, $user->transactions);
});
