<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_register_user()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'New User',
            'email' => 'newuser_' . uniqid() . '@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'User registered successfully',
            ])
            ->assertJsonStructure([
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $this->user->name,
            'email' => $this->user->email,
        ]);
    }

    public function test_can_login_user()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Login successful',
            ])
            ->assertJsonStructure([
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $this->user->name,
            'email' => $this->user->email,
        ]);
    }

    public function test_can_logout_user()
    {
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($this->user);
        $response = $this->postJson('/api/v1/auth/logout', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout successful',
            ])
            ->assertJsonStructure([
                'message',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $this->user->name,
            'email' => $this->user->email,
        ]);
    }
}
