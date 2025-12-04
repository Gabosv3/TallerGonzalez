<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_refresh_and_logout()
    {
        $user = User::factory()->create(['email' => 'test@example.com', 'password' => bcrypt('password')]);

        // Login
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)->assertJsonStructure(['user', 'token']);

        $token = $response->json('token');

        // Check auth
        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/check-auth')
            ->assertStatus(200)
            ->assertJson(['authenticated' => true]);

        // Refresh token
        $refresh = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/refresh-token');

        $refresh->assertStatus(200)->assertJsonStructure(['user', 'token']);

        $newToken = $refresh->json('token');

        // Logout with new token
        $this->withHeader('Authorization', 'Bearer '.$newToken)
            ->postJson('/api/logout')
            ->assertStatus(200)
            ->assertJson(['message' => 'Sesión cerrada correctamente']);
    }
}
