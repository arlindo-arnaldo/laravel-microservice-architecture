<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'arlindo',
            'email' => 'arlindo@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'arlindo@gmail.com',
            'name' => 'arlindo',
        ]);
    }

    public function test_registration_requires_valid_data(): void
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_registration_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'arlindo@gmail.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'arlindo',
            'email' => 'arlindo@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'arlindo@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'arlindo@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email'],
                ],
            ]);
    }

    public function test_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'arlindo@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'arlindo@gmail.com',
            'password' => 'password_errada',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure(['success', 'error']);
    }

    public function test_login_requires_valid_data(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_user_can_refresh_token(): void
    {
        $registerResponse = $this->postJson('/api/register', [
            'name' => 'arlindo',
            'email' => 'arlindo@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $token = $registerResponse->json('data.token');

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->postJson('/api/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email'],
                ],
            ]);

        $this->assertNotEquals($token, $response->json('data.token'));
    }

    public function test_refresh_without_token_returns_error(): void
    {
        $response = $this->postJson('/api/refresh');

        $response->assertStatus(401)
            ->assertJsonStructure(['success', 'error']);
    }

    public function test_user_can_logout(): void
    {
        $registerResponse = $this->postJson('/api/register', [
            'name' => 'arlindo',
            'email' => 'arlindo@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $token = $registerResponse->json('data.token');

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_logout_without_token_returns_error(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401)
            ->assertJsonStructure(['success', 'error']);
    }

    public function test_admin_can_get_users_count(): void
    {
        User::factory()->create(['is_admin' => true]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => User::first()->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.token');

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->getJson('/api/users/count');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['total'],
            ])
            ->assertJson([
                'data' => ['total' => 1],
            ]);
    }

    public function test_non_admin_cannot_get_users_count(): void
    {
        User::factory()->create(['is_admin' => false]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => User::first()->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.token');

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->getJson('/api/users/count');

        $response->assertStatus(403)
            ->assertJsonStructure(['success', 'error']);
    }

    public function test_users_count_without_token_returns_error(): void
    {
        $response = $this->getJson('/api/users/count');

        $response->assertStatus(401)
            ->assertJsonStructure(['success', 'error']);
    }
}
