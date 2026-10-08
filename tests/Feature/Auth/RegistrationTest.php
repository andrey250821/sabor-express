<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_as_cliente(): void
    {
        Role::create([
            'id' => 2,
            'nombre' => 'Cliente',
        ]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'telefono' => '71234567',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();

        $response->assertRedirect(
            route('cliente.dashboard.index', absolute: false)
        );

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'telefono' => '71234567',
            'email' => 'test@example.com',
            'role_id' => 2,
            'estado' => 'activo',
        ]);
    }

    public function test_new_user_must_provide_phone(): void
    {
        Role::create([
            'id' => 2,
            'nombre' => 'Cliente',
        ]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('telefono');
        $this->assertGuest();
    }
}
