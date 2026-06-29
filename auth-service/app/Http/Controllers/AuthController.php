<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AuthService $authService
    ) {}

    #[OA\Post(
        path: '/api/register',
        summary: 'Registrar novo usuário',
        tags: ['Autenticação'],
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'email', 'password', 'password_confirmation'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'João'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@email.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: '12345678'),
                new OA\Property(property: 'password_confirmation', type: 'string', example: '12345678'),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Usuário registrado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1Qi...'),
                        new OA\Property(
                            property: 'user',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'João'),
                                new OA\Property(property: 'email', type: 'string', example: 'joao@email.com'),
                            ]
                        ),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(response: 422, description: 'Dados inválidos (validação)')]
    public function register(RegisterRequest $request)
    {
        $data = $this->authService->register($request->validated());

        return $this->success($data, 201);
    }

    #[OA\Post(
        path: '/api/login',
        summary: 'Autenticar usuário',
        tags: ['Autenticação'],
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@email.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: '12345678'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Login realizado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1Qi...'),
                        new OA\Property(
                            property: 'user',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'João'),
                                new OA\Property(property: 'email', type: 'string', example: 'joao@email.com'),
                            ]
                        ),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Credenciais inválidas')]
    #[OA\Response(response: 422, description: 'Dados inválidos (validação)')]
    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());

        if (!$data) {
            return $this->error('Credenciais inválidas', 401);
        }

        return $this->success($data);
    }

    #[OA\Post(
        path: '/api/refresh',
        summary: 'Renovar token JWT',
        tags: ['Autenticação'],
        security: [['bearerAuth' => []]],
    )]
    #[OA\Response(
        response: 200,
        description: 'Token renovado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1Qi...'),
                        new OA\Property(
                            property: 'user',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'João'),
                                new OA\Property(property: 'email', type: 'string', example: 'joao@email.com'),
                            ]
                        ),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Token inválido ou ausente')]
    public function refresh()
    {
        $data = $this->authService->refresh();

        if (!$data) {
            return $this->error('Token inválido ou ausente', 401);
        }

        return $this->success($data);
    }

    #[OA\Post(
        path: '/api/logout',
        summary: 'Invalidar token JWT',
        tags: ['Autenticação'],
        security: [['bearerAuth' => []]],
    )]
    #[OA\Response(
        response: 200,
        description: 'Logout realizado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Logout realizado com sucesso'),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Token inválido ou ausente')]
    public function logout()
    {
        $loggedOut = $this->authService->logout();

        if (!$loggedOut) {
            return $this->error('Token inválido ou ausente', 401);
        }

        return $this->success(['message' => 'Logout realizado com sucesso']);
    }
}
