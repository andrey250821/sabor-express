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

    private function crearUsuarioConRol(string $nombreRol, array $overrides = []): User
    {
        $role = Role::firstOrCreate([
            'nombre' => $nombreRol,
        ]);

        return User::create(array_merge([
            'role_id' => $role->id,
            'name' => 'Usuario de prueba',
            'email' => strtolower($nombreRol) . '.test@saborexpress.com',
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

    public function test_delivery_inactivo_puede_iniciar_sesion_para_activarse_desde_su_perfil(): void
    {
        $delivery = $this->crearUsuarioConRol('Delivery', [
            'email' => 'delivery.inactivo@saborexpress.com',
            'estado' => 'inactivo',
        ]);

        $response = $this->post('/login', [
            'email' => $delivery->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(
            route('delivery.perfil.edit', absolute: false)
        );

        $this->assertAuthenticatedAs($delivery);

        $this->get(route('delivery.perfil.edit', absolute: false))
            ->assertOk()
            ->assertSee('Activar')
            ->assertSee('delivery-status-toggle');
    }

    public function test_cocinero_inactivo_no_puede_iniciar_sesion(): void
    {
        $cocinero = $this->crearUsuarioConRol('Cocinero', [
            'email' => 'cocinero.inactivo@saborexpress.com',
            'estado' => 'inactivo',
        ]);

        $response = $this->post('/login', [
            'email' => $cocinero->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
