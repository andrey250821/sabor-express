<?php

namespace Tests\Feature\Delivery;

use App\Models\AsignacionDelivery;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivery_puede_activar_y_desactivar_su_estado(): void
    {
        $role = Role::create([
            'nombre' => 'Delivery',
            'descripcion' => 'Personal de Delivery',
        ]);

        $delivery = User::create([
            'role_id' => $role->id,
            'name' => 'Delivery de prueba',
            'email' => 'delivery-estado@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $this->from('/delivery/perfil')
            ->actingAs($delivery)
            ->patch('/delivery/estado')
            ->assertRedirect('/delivery/perfil');

        $this->assertSame('inactivo', $delivery->fresh()->estado);

        $this->from('/delivery/perfil')
            ->actingAs($delivery->fresh())
            ->patch('/delivery/estado')
            ->assertRedirect('/delivery/perfil');

        $this->assertSame('activo', $delivery->fresh()->estado);
    }

    public function test_delivery_inactivo_no_puede_acceder_a_la_parte_operativa(): void
    {
        $role = Role::create([
            'nombre' => 'Delivery',
            'descripcion' => 'Personal de Delivery',
        ]);

        $delivery = User::create([
            'role_id' => $role->id,
            'name' => 'Delivery inactivo',
            'email' => 'delivery-inactivo@test.com',
            'password' => 'password',
            'estado' => 'inactivo',
        ]);

        $this->actingAs($delivery)
            ->get('/delivery/pedidos')
            ->assertRedirect('/delivery/perfil');
    }

    public function test_delivery_no_puede_desactivarse_con_un_pedido_activo(): void
    {
        $deliveryRole = Role::create([
            'nombre' => 'Delivery',
            'descripcion' => 'Personal de Delivery',
        ]);

        $clienteRole = Role::create([
            'nombre' => 'Cliente',
            'descripcion' => 'Cliente',
        ]);

        $delivery = User::create([
            'role_id' => $deliveryRole->id,
            'name' => 'Delivery ocupado',
            'email' => 'delivery-ocupado@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $cliente = User::create([
            'role_id' => $clienteRole->id,
            'name' => 'Cliente',
            'email' => 'cliente-delivery-estado@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $pedido = \App\Models\Pedido::create([
            'user_id' => $cliente->id,
            'total' => 50,
            'estado' => 'asignado',
        ]);

        AsignacionDelivery::create([
            'pedido_id' => $pedido->id,
            'delivery_id' => $delivery->id,
            'estado' => 'aceptado',
            'fecha_asignacion' => now(),
            'fecha_respuesta' => now(),
        ]);

        $this->from('/delivery/perfil')
            ->actingAs($delivery)
            ->patch('/delivery/estado')
            ->assertRedirect('/delivery/perfil');

        $this->assertSame('activo', $delivery->fresh()->estado);
    }
}
