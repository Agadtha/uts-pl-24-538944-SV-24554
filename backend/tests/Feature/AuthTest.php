<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_berhasil_mengembalikan_token(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'rahasia123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['message', 'token_type', 'token', 'user']);

        $this->assertNotEmpty($response->json('token'));
    }

    public function test_login_dengan_password_salah_mengembalikan_401(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'salah',
        ])->assertUnauthorized()
          ->assertJson(['message' => 'Email atau password salah.']);
    }

    public function test_login_dengan_data_tidak_valid_mengembalikan_422(): void
    {
        $this->postJson('/api/login', ['email' => 'bukan-email'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_logout_menghapus_token(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        $token = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'rahasia123',
        ])->json('token');

        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJson(['message' => 'Logout berhasil.']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
