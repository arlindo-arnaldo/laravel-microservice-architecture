<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
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

    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());

        if (!$data) {
            return $this->error('Credenciais inválidas', 401);
        }

        return $this->success($data);
    }

    public function refresh()
    {
        $data = $this->authService->refresh();

        if (!$data) {
            return $this->error('Token inválido ou ausente', 401);
        }

        return $this->success($data);
    }
}
