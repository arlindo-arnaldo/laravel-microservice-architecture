<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Product Service API',
    description: 'Microserviço de gestão de produtos. CRUD isolado por cliente via JWT.',
)]
#[OA\Server(
    url: 'http://localhost:8002',
    description: 'Servidor local'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Token JWT obtido no auth-service. Enviar no header Authorization: Bearer {token}'
)]
class OpenApi
{
}
