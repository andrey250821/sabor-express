<?php

namespace Tests\Feature\Delivery;

use App\Models\AsignacionDelivery;
use App\Models\Pedido;
use App\Models\Role;
use App\Models\User;
use App\Services\AsignarPedidoDeliveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidoTest extends TestCase
{
    use RefreshDatabase;

    public function test_asigna_la_cola_fifo_a_los_deliverys_libres(): void
    {
        $deliveryRole = Role::create([
            'nombre' => 'Delivery',
            'descripcion' => 'Delivery del sistema',
        ]);

        $clienteRole = Role::create([
            'nombre' => 'Cliente',
            'descripcion' => 'Cliente del sistema',
        ]);

        $delivery1 = User::create([
            'role_id' => $deliveryRole->id,
            'name' => 'Delivery 1',
            'email' => 'delivery1@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $delivery2 = User::create([
            'role_id' => $deliveryRole->id,
            'name' => 'Delivery 2',
            'email' => 'delivery2@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $cliente = User::create([
            'role_id' => $clienteRole->id,
            'name' => 'Cliente de prueba',
            'email' => 'cliente@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $pedidos = collect();

        foreach (range(1, 5) as $numero) {
            $pedidos->push(Pedido::create([
                'user_id' => $cliente->id,
                'total' => 10 * $numero,
                'estado' => 'listo',
                'fecha_listo' => now()->subMinutes(10 - $numero),
                'direccion_entrega' => 'Dirección de prueba ' . $numero,
            ]));
        }

        $asignador = app(AsignarPedidoDeliveryService::class);

        $asignados = $asignador->procesarCola();

        $this->assertSame(2, $asignados);

        $this->assertEquals(
            $delivery1->id,
            AsignacionDelivery::where('pedido_id', $pedidos[0]->id)->value('delivery_id')
        );

        $this->assertEquals(
            $delivery2->id,
            AsignacionDelivery::where('pedido_id', $pedidos[1]->id)->value('delivery_id')
        );

        $this->assertEquals(
            'listo',
            $pedidos[2]->fresh()->estado
        );

        $this->assertEquals(
            'listo',
            $pedidos[3]->fresh()->estado
        );

        $this->assertEquals(
            'listo',
            $pedidos[4]->fresh()->estado
        );
    }

    public function test_delivery_recibe_obligatoriamente_el_siguiente_pedido_al_entregar(): void
    {
        $deliveryRole = Role::create([
            'nombre' => 'Delivery',
            'descripcion' => 'Delivery del sistema',
        ]);

        $clienteRole = Role::create([
            'nombre' => 'Cliente',
            'descripcion' => 'Cliente del sistema',
        ]);

        $delivery1 = User::create([
            'role_id' => $deliveryRole->id,
            'name' => 'Delivery 1',
            'email' => 'delivery1@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $delivery2 = User::create([
            'role_id' => $deliveryRole->id,
            'name' => 'Delivery 2',
            'email' => 'delivery2@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $cliente = User::create([
            'role_id' => $clienteRole->id,
            'name' => 'Cliente de prueba',
            'email' => 'cliente@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $pedidos = collect();

        foreach (range(1, 5) as $numero) {
            $pedidos->push(Pedido::create([
                'user_id' => $cliente->id,
                'total' => 10 * $numero,
                'estado' => 'listo',
                'fecha_listo' => now()->subMinutes(10 - $numero),
                'direccion_entrega' => 'Dirección de prueba ' . $numero,
            ]));
        }

        app(AsignarPedidoDeliveryService::class)->procesarCola();

        $pedido2 = $pedidos[1];

        $this->actingAs($delivery2)
            ->put('/delivery/pedidos/' . $pedido2->id . '/iniciar')
            ->assertRedirect();

        $this->actingAs($delivery2)
            ->put('/delivery/pedidos/' . $pedido2->id . '/entregar')
            ->assertRedirect();

        $this->assertEquals(
            'entregado',
            $pedido2->fresh()->estado
        );

        $pedido3 = $pedidos[2]->fresh();

        $this->assertEquals(
            'asignado',
            $pedido3->estado
        );

        $this->assertEquals(
            $delivery2->id,
            AsignacionDelivery::where('pedido_id', $pedido3->id)->value('delivery_id')
        );

        // El pedido 4 no debe adelantarse al pedido 3.
        $this->assertDatabaseMissing('asignaciones_delivery', [
            'pedido_id' => $pedidos[3]->id,
        ]);
    }

    public function test_cola_solo_muestra_la_cantidad_y_no_los_detalles(): void
    {
        $deliveryRole = Role::create([
            'nombre' => 'Delivery',
            'descripcion' => 'Delivery del sistema',
        ]);

        $clienteRole = Role::create([
            'nombre' => 'Cliente',
            'descripcion' => 'Cliente del sistema',
        ]);

        $delivery = User::create([
            'role_id' => $deliveryRole->id,
            'name' => 'Delivery 1',
            'email' => 'delivery1@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $cliente = User::create([
            'role_id' => $clienteRole->id,
            'name' => 'Cliente privado',
            'email' => 'cliente-privado@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        Pedido::create([
            'user_id' => $cliente->id,
            'total' => 85,
            'estado' => 'listo',
            'fecha_listo' => now(),
            'direccion_entrega' => 'Dirección privada de prueba',
        ]);

        $response = $this->actingAs($delivery)
            ->get('/delivery/pedidos');

        $response->assertOk();
        $response->assertSee('Pedidos en cola');
        $response->assertSee('1');
        $response->assertDontSee('Cliente privado');
        $response->assertDontSee('cliente-privado@test.com');
        $response->assertDontSee('Dirección privada de prueba');
    }

    public function test_delivery_no_puede_ver_detalles_de_un_pedido_ajeno(): void
    {
        $deliveryRole = Role::create([
            'nombre' => 'Delivery',
            'descripcion' => 'Delivery del sistema',
        ]);

        $clienteRole = Role::create([
            'nombre' => 'Cliente',
            'descripcion' => 'Cliente del sistema',
        ]);

        $delivery1 = User::create([
            'role_id' => $deliveryRole->id,
            'name' => 'Delivery 1',
            'email' => 'delivery1@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $delivery2 = User::create([
            'role_id' => $deliveryRole->id,
            'name' => 'Delivery 2',
            'email' => 'delivery2@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $cliente = User::create([
            'role_id' => $clienteRole->id,
            'name' => 'Cliente de prueba',
            'email' => 'cliente@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $pedido = Pedido::create([
            'user_id' => $cliente->id,
            'total' => 50,
            'estado' => 'listo',
            'fecha_listo' => now(),
            'direccion_entrega' => 'Dirección privada',
        ]);

        AsignacionDelivery::create([
            'pedido_id' => $pedido->id,
            'delivery_id' => $delivery2->id,
            'estado' => 'aceptado',
            'fecha_asignacion' => now(),
            'fecha_respuesta' => now(),
        ]);

        $this->actingAs($delivery1)
            ->get('/delivery/pedidos/' . $pedido->id)
            ->assertNotFound();

        $this->actingAs($delivery2)
            ->get('/delivery/pedidos/' . $pedido->id)
            ->assertOk();
    }
}
