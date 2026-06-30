<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use App\Traits\ApiResponse;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ProductService $productService,
    ) {}

    public function index()
    {
        $userId = request()->input('auth_user.id');

        return $this->success($this->productService->list($userId));
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create(
            $request->input('auth_user.id'),
            $request->validated(),
        );

        return $this->success($product, 201);
    }

    public function show(string $id)
    {
        $userId = request()->input('auth_user.id');

        return $this->success($this->productService->find($userId, $id));
    }

    public function update(UpdateProductRequest $request, string $id)
    {
        $product = $this->productService->update(
            $request->input('auth_user.id'),
            $id,
            $request->validated(),
        );

        return $this->success($product);
    }

    public function destroy(string $id)
    {
        $this->productService->delete(request()->input('auth_user.id'), $id);

        return $this->success(['message' => 'Produto removido com sucesso']);
    }
}
