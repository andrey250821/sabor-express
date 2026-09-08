<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_cliente_puede_iniciar_sesion_con_credenciales_validas(): void
    {
        $role = Role::firstOrCreate(
            ['nombre' => 'Cliente'],
            ['descripcion' => 'Cliente del sistema']
        );

        $user = User::where('email', 'testcliente@saborexpress.com')->first();

        if (!$user) {
            $user = User::create([
                'role_id' => $role->id,
                'name' => 'Cliente de Prueba',
                'email' => 'testcliente@saborexpress.com',
                'password' => 'password',
                'estado' => 1,
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'testcliente@saborexpress.com',
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }
    public function test_cliente_no_puede_iniciar_sesion_con_contrasena_incorrecta(): void
    {
        $user = User::where('email', 'testcliente@saborexpress.com')->firstOrFail();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'contrasena_incorrecta',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
