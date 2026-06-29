<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(RegisterRequest $request)
    {
        $data = $this->authService->register($request->validated());

        return $this->success($data, 201);
    }
}
