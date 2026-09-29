<?php

namespace Tests\Feature\Cocinero;

use App\Models\Pedido;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_cocinero_puede_activar_y_desactivar_su_estado(): void
    {
        $role = Role::create([
            'nombre' => 'Cocinero',
            'descripcion' => 'Personal de cocina',
        ]);

        $cocinero = User::create([
            'role_id' => $role->id,
            'name' => 'Cocinero de prueba',
            'email' => 'cocinero-estado@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $this->from('/cocinero/perfil')
            ->actingAs($cocinero)
            ->patch('/cocinero/estado')
            ->assertRedirect('/cocinero/perfil');

        $this->assertSame('inactivo', $cocinero->fresh()->estado);

        $this->from('/cocinero/perfil')
            ->actingAs($cocinero->fresh())
            ->patch('/cocinero/estado')
            ->assertRedirect('/cocinero/perfil');

        $this->assertSame('activo', $cocinero->fresh()->estado);
    }

    public function test_cocinero_inactivo_no_puede_acceder_a_la_parte_operativa(): void
    {
        $role = Role::create([
            'nombre' => 'Cocinero',
            'descripcion' => 'Personal de cocina',
        ]);

        $cocinero = User::create([
            'role_id' => $role->id,
            'name' => 'Cocinero inactivo',
            'email' => 'cocinero-inactivo@test.com',
            'password' => 'password',
            'estado' => 'inactivo',
        ]);

        $this->actingAs($cocinero)
            ->get('/cocinero/pedidos')
            ->assertRedirect('/cocinero/perfil');
    }

    public function test_cocinero_no_puede_desactivarse_con_una_preparacion_activa(): void
    {
        $cocineroRole = Role::create([
            'nombre' => 'Cocinero',
            'descripcion' => 'Personal de cocina',
        ]);

        $clienteRole = Role::create([
            'nombre' => 'Cliente',
            'descripcion' => 'Cliente',
        ]);

        $cocinero = User::create([
            'role_id' => $cocineroRole->id,
            'name' => 'Cocinero ocupado',
            'email' => 'cocinero-ocupado@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $cliente = User::create([
            'role_id' => $clienteRole->id,
            'name' => 'Cliente',
            'email' => 'cliente-cocinero-estado@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        Pedido::create([
            'user_id' => $cliente->id,
            'total' => 40,
            'estado' => 'preparando',
            'cocinero_id' => $cocinero->id,
        ]);

        $this->from('/cocinero/perfil')
            ->actingAs($cocinero)
            ->patch('/cocinero/estado')
            ->assertRedirect('/cocinero/perfil');

        $this->assertSame('activo', $cocinero->fresh()->estado);
    }
}
