<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use App\Traits\ApiResponse;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ProductService $productService,
    ) {}

    #[OA\Get(
        path: '/api/products',
        summary: 'Listar produtos do usuário autenticado',
        tags: ['Produtos'],
        security: [['bearerAuth' => []]],
    )]
    #[OA\Response(
        response: 200,
        description: 'Lista de produtos',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Notebook'),
                            new OA\Property(property: 'price', type: 'string', example: '4999.90'),
                            new OA\Property(property: 'quantity', type: 'integer', example: 10),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                        ]
                    )
                ),
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Token inválido ou ausente')]
    public function index()
    {
        $userId = request()->input('auth_user.id');

        return $this->success($this->productService->list($userId));
    }

    #[OA\Post(
        path: '/api/products',
        summary: 'Criar novo produto',
        tags: ['Produtos'],
        security: [['bearerAuth' => []]],
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'price', 'quantity'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Notebook'),
                new OA\Property(property: 'price', type: 'number', format: 'float', example: 4999.90),
                new OA\Property(property: 'quantity', type: 'integer', example: 10),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Produto criado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Notebook'),
                        new OA\Property(property: 'price', type: 'string', example: '4999.90'),
                        new OA\Property(property: 'quantity', type: 'integer', example: 10),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(response: 422, description: 'Dados inválidos (validação)')]
    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create(
            $request->input('auth_user.id'),
            $request->validated(),
        );

        return $this->success($product, 201);
    }

    #[OA\Get(
        path: '/api/products/{id}',
        summary: 'Exibir detalhes de um produto',
        tags: ['Produtos'],
        security: [['bearerAuth' => []]],
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: 1,
    )]
    #[OA\Response(
        response: 200,
        description: 'Dados do produto',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Notebook'),
                        new OA\Property(property: 'price', type: 'string', example: '4999.90'),
                        new OA\Property(property: 'quantity', type: 'integer', example: 10),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Produto não encontrado')]
    public function show(string $id)
    {
        $userId = request()->input('auth_user.id');

        return $this->success($this->productService->find($userId, $id));
    }

    #[OA\Put(
        path: '/api/products/{id}',
        summary: 'Atualizar um produto',
        tags: ['Produtos'],
        security: [['bearerAuth' => []]],
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: 1,
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Notebook Atualizado'),
                new OA\Property(property: 'price', type: 'number', format: 'float', example: 5499.90),
                new OA\Property(property: 'quantity', type: 'integer', example: 15),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Produto atualizado com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Notebook Atualizado'),
                        new OA\Property(property: 'price', type: 'string', example: '5499.90'),
                        new OA\Property(property: 'quantity', type: 'integer', example: 15),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Produto não encontrado')]
    #[OA\Response(response: 422, description: 'Dados inválidos (validação)')]
    public function update(UpdateProductRequest $request, string $id)
    {
        $product = $this->productService->update(
            $request->input('auth_user.id'),
            $id,
            $request->validated(),
        );

        return $this->success($product);
    }

    #[OA\Delete(
        path: '/api/products/{id}',
        summary: 'Remover um produto',
        tags: ['Produtos'],
        security: [['bearerAuth' => []]],
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: 1,
    )]
    #[OA\Response(
        response: 200,
        description: 'Produto removido com sucesso',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Produto removido com sucesso'),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Produto não encontrado')]
    public function destroy(string $id)
    {
        $this->productService->delete(request()->input('auth_user.id'), $id);

        return $this->success(['message' => 'Produto removido com sucesso']);
    }
}
