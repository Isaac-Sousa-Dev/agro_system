<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_user()
    {
        $userData = [
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at'
                    ],
                    'token'
                ]);

        $this->assertDatabaseHas('users', [
            'name' => 'João Silva',
            'email' => 'joao@email.com'
        ]);
    }

    public function test_cannot_register_user_with_invalid_data()
    {
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_cannot_register_user_with_existing_email()
    {
        User::create([
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'password' => Hash::make('password123')
        ]);

        $userData = [
            'name' => 'Maria Santos',
            'email' => 'joao@email.com', // Email já existe
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_can_login_user()
    {
        $user = User::create([
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'password' => Hash::make('password123')
        ]);

        $loginData = [
            'email' => 'joao@email.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at'
                    ],
                    'token',
                    'message'
                ]);

        $this->assertEquals('joao@email.com', $response->json('user.email'));
    }

    public function test_cannot_login_with_invalid_credentials()
    {
        $user = User::create([
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'password' => Hash::make('password123')
        ]);

        $loginData = [
            'email' => 'joao@email.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Invalid credentials'
                ]);
    }

    public function test_can_get_authenticated_user()
    {
        $user = User::create([
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'password' => Hash::make('password123')
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/auth/user');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                    'message'
                ]);

        $this->assertEquals('joao@email.com', $response->json('email'));
    }

    public function test_cannot_get_user_without_token()
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(401);
    }

    public function test_can_logout_user()
    {
        $user = User::create([
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'password' => Hash::make('password123')
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Logout successful'
                ]);

        // Verificar se o token foi revogado
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'auth_token'
        ]);
    }

    public function test_cannot_logout_without_token()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    public function test_can_get_user_via_me_endpoint()
    {
        $user = User::create([
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'password' => Hash::make('password123')
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/auth/me');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                    'message'
                ]);

        $this->assertEquals('joao@email.com', $response->json('email'));
    }
}
