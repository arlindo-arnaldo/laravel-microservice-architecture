<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function list(int $userId): Collection
    {
        return Product::where('user_id', $userId)->get();
    }

    public function create(int $userId, array $data): Product
    {
        return Product::create([
            'user_id' => $userId,
            ...$data,
        ]);
    }

    public function find(int $userId, string $id): Product
    {
        return Product::where('user_id', $userId)->findOrFail($id);
    }

    public function update(int $userId, string $id, array $data): Product
    {
        $product = $this->find($userId, $id);

        $product->update($data);

        return $product;
    }

    public function delete(int $userId, string $id): void
    {
        $product = $this->find($userId, $id);

        $product->delete();
    }
}
