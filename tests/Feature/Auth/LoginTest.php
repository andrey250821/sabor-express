<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * CP-001: un usuario Cliente puede iniciar sesión con credenciales válidas.
     */
    public function test_cliente_puede_iniciar_sesion_con_credenciales_validas(): void
    {
        $role = Role::create([
            'nombre' => 'Cliente',
            'descripcion' => 'Rol utilizado para pruebas automatizadas.',
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'password' => 'password',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/cliente');
    }
}
