<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use OpenApi\Attributes as OA;

class AdminController extends Controller
{
    use ApiResponse;

    #[OA\Get(
        path: '/api/users/count',
        summary: 'Total de clientes cadastrados (admin)',
        tags: ['Admin'],
        security: [['bearerAuth' => []]],
    )]
    #[OA\Response(
        response: 200,
        description: 'Total de clientes',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(property: 'total', type: 'integer', example: 5),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Token ausente ou inválido')]
    #[OA\Response(response: 403, description: 'Acesso não autorizado (não é admin)')]
    public function usersCount()
    {
        $total = User::count();

        return $this->success(['total' => $total]);
    }
}
