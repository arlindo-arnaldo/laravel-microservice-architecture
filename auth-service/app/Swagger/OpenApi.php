<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Auth Service API',
    description: 'Microserviço de autenticação com JWT. Gerencia cadastro, login, refresh, logout e consulta de total de clientes.',
)]
#[OA\Server(
    url: 'http://localhost:8001',
    description: 'Servidor local'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Token JWT obtido no login/register. Enviar no header Authorization: Bearer {token}'
)]
class OpenApi
{
}
