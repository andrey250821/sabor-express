<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
        Role::firstOrCreate(['nombre' => 'Cliente']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'telefono' => '71234567',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(
            route('cliente.dashboard.index', absolute: false)
        );

        $this->assertAuthenticated();

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
        Role::firstOrCreate(['nombre' => 'Cliente']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('telefono');
        $this->assertGuest();
    }
    public function test_duplicate_email_validation_message_is_in_spanish(): void
    {
        app()->setLocale('es');

        Role::firstOrCreate(['nombre' => 'Cliente']);

        User::create([
            'role_id' => 2,
            'name' => 'Cliente Existente',
            'email' => 'existente@example.com',
            'telefono' => '71234567',
            'password' => Hash::make('Password123!'),
            'estado' => 'activo',
        ]);

        $response = $this->from('/register')->post('/register', [
            'name' => 'Otro Cliente',
            'telefono' => '76543210',
            'email' => 'existente@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'email' => 'Este correo electrónico ya está registrado. Inicia sesión si ya tienes una cuenta.',
        ]);
    }

}
