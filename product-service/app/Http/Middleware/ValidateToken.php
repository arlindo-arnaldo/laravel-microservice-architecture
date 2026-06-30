<?php

namespace App\Http\Middleware;

use App\Services\TokenValidationService;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateToken
{
    use ApiResponse;

    public function __construct(
        private readonly TokenValidationService $tokenValidationService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return $this->error('Token ausente', 401);
        }

        $user = $this->tokenValidationService->validate($token);

        if (!$user) {
            return $this->error('Token inválido', 401);
        }

        $request->merge(['auth_user' => $user]);

        return $next($request);
    }
}
