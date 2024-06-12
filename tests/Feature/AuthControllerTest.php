<?php

use App\Models\User;
use Laravel\Passport\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->oauth_clients = Client::factory()->create([
        'id' => '9c426f6c-f655-4a6c-9ab2-c101c3177b3d',
        'secret' => '0BiRQ0j9kfKJC2WsxWTB5xeDqKXOfoXIWD74Kmqe',
        'redirect' => 'http://localhost',
        'personal_access_client' => 1,
    ]);
});

test('a user can login successfully', function () {
    $response = $this->postJson(
        '/api/login',
        [
            'email' => $this->user->email,
            'password' => 'password',
        ]
    );

    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'access_token'
            ],
            'status'
        ])
        ->assertJson([
            'status' => 'OK',
        ]);
});

test('a user failed login with wrong username or password', function () {
    // login with wrong email
    $response = $this->postJson(
        '/api/login',
        [
            'email' => 'email@email.com',
            'password' => 'password',
        ]
    );

    $response->dump();

    $response
        ->assertStatus(401)
        ->assertJson([
            'status' => 'Unauthorized',
            'data' => [
                'message' => 'Email or password is invalid.'
            ]
        ]);

    // login with wrong password
    $response2 = $this->postJson(
        '/api/login',
        [
            'email' => $this->user->email,
            'password' => 'password123',
        ]
    );

    $response2
        ->assertStatus(401)
        ->assertJson([
            'status' => 'Unauthorized',
            'data' => [
                'message' => 'Email or password is invalid.'
            ]
        ]);
});
