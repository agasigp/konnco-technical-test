<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['bail', 'required'],
            'password' => ['bail', 'required', 'string'],
        ]);

        if (auth()->attempt($validated)) {
            $user = $request->user();
            $token = $user->createToken('Konnco personal access token')->accessToken;

            return response()->json([
                'status' => 'OK',
                'data' => [
                    'access_token' => $token,
                ],
        ]);
        }

        return response()->json([
            'status' => 'Unauthorized',
            'data' => [
                'message' => 'Email or password is invalid.'
            ],
        ], 401);
    }
}
