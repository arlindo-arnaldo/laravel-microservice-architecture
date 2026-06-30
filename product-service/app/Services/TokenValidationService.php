<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TokenValidationService
{
    public function validate(string $token): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->post(config('services.auth.url') . '/api/validate-token');

        if (!$response->successful()) {
            return null;
        }

        return $response->json('data');
    }
}
