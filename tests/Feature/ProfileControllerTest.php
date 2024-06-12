<?php

use App\Models\User;
use Laravel\Passport\Passport;

test('a user can show his profile data', function () {
    $user = User::factory()->create();
    Passport::actingAs(
        $user,
        []
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])
        ->get(
            '/api/user',
        );

    $response
        ->assertStatus(200)
        ->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at->toISOString(),
            'created_at' => $user->created_at->toISOString(),
            'updated_at' => $user->updated_at->toISOString(),
        ]);
});
