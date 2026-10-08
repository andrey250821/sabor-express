<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function crearCliente(array $overrides = []): User
    {
        $role = Role::firstOrCreate([
            'nombre' => 'Cliente',
        ]);

        return User::create(array_merge([
            'role_id' => $role->id,
            'name' => 'Cliente de Prueba',
            'email' => 'testcliente@saborexpress.com',
            'password' => Hash::make('password'),
            'estado' => 'activo',
        ], $overrides));
    }

    public function test_cliente_puede_iniciar_sesion_con_credenciales_validas(): void
    {
        $user = $this->crearCliente();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(
            route('cliente.dashboard.index', absolute: false)
        );

        $this->assertAuthenticatedAs($user);
    }

    public function test_cliente_no_puede_iniciar_sesion_con_contrasena_incorrecta(): void
    {
        $user = $this->crearCliente();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'contrasena_incorrecta',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_usuario_inactivo_no_puede_iniciar_sesion(): void
    {
        $user = $this->crearCliente([
            'email' => 'cliente.inactivo@saborexpress.com',
            'estado' => 'inactivo',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
