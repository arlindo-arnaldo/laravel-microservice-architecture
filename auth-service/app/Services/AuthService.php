<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = JWTAuth::fromUser($user);

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    public function login(array $data): ?array
    {
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        if (!$token = JWTAuth::attempt($credentials)) {
            return null;
        }

        $user = auth()->user();

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    public function refresh(): ?array
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $user = User::find($payload['sub']);
            $newToken = JWTAuth::refresh();

            return [
                'token' => $newToken,
                'user' => $user,
            ];
        } catch (JWTException) {
            return null;
        }
    }
}
