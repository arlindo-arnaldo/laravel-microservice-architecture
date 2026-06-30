<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private array $authUser = ['id' => 1, 'name' => 'João', 'email' => 'joao@email.com'];
    private string $token = 'token-falso-valido';

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            config('services.auth.url') . '/api/validate-token' => function () {
                return Http::response([
                    'success' => true,
                    'data' => $this->authUser,
                ], 200);
            },
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => 'Bearer ' . $this->token];
    }

    public function test_user_can_list_products(): void
    {
        Product::factory()->create(['user_id' => 1, 'name' => 'Produto A']);
        Product::factory()->create(['user_id' => 1, 'name' => 'Produto B']);
        Product::factory()->create(['user_id' => 2, 'name' => 'Outro user']);

        $response = $this->getJson('/api/products', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data'])
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_create_product(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Produto Novo',
            'price' => 29.90,
            'quantity' => 10,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'name', 'price', 'quantity'],
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Produto Novo',
                    'price' => '29.90',
                    'quantity' => 10,
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Produto Novo',
            'user_id' => 1,
        ]);
    }

    public function test_create_product_validates_data(): void
    {
        $response = $this->postJson('/api/products', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'quantity']);
    }

    public function test_user_can_see_product(): void
    {
        $product = Product::factory()->create(['user_id' => 1]);

        $response = $this->getJson('/api/products/' . $product->id, $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'name', 'price', 'quantity'],
            ]);
    }

    public function test_user_cannot_see_other_users_product(): void
    {
        $product = Product::factory()->create(['user_id' => 2]);

        $response = $this->getJson('/api/products/' . $product->id, $this->authHeaders());

        $response->assertStatus(404)
            ->assertJsonStructure(['success', 'error']);
    }

    public function test_user_can_update_product(): void
    {
        $product = Product::factory()->create([
            'user_id' => 1,
            'name' => 'Nome Antigo',
        ]);

        $response = $this->putJson('/api/products/' . $product->id, [
            'name' => 'Nome Atualizado',
            'price' => 99.90,
            'quantity' => 20,
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Nome Atualizado',
                    'price' => '99.90',
                    'quantity' => 20,
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Nome Atualizado',
        ]);
    }

    public function test_user_cannot_update_other_users_product(): void
    {
        $product = Product::factory()->create([
            'user_id' => 2,
            'name' => 'De outro',
        ]);

        $response = $this->putJson('/api/products/' . $product->id, [
            'name' => 'Tentativa',
        ], $this->authHeaders());

        $response->assertStatus(404);
    }

    public function test_user_can_delete_product(): void
    {
        $product = Product::factory()->create(['user_id' => 1]);

        $response = $this->deleteJson('/api/products/' . $product->id, [], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_endpoints_require_token(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(401)
            ->assertJsonStructure(['success', 'error']);
    }
}
