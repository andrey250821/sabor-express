<?php

namespace Tests\Feature\Delivery;

use App\Models\AsignacionDelivery;
use App\Models\Pedido;
use App\Models\Role;
use App\Models\User;
use App\Services\AsignarPedidoDeliveryService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidoTest extends TestCase
{
    use RefreshDatabase;

    public function test_asigna_la_cola_fifo_a_los_deliverys_libres(): void
    {
        $deliveryRole = Role::create([
            'nombre' => 'Delivery',
        ]);

        $clienteRole = Role::create([
            'nombre' => 'Cliente',
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
        ]);

        $clienteRole = Role::create([
            'nombre' => 'Cliente',
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

    public function test_balancea_por_distancia_acumulada_de_hoy_entre_deliverys_libres(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 10, 1, 12, 0, 0, 'America/La_Paz'));

        try {
            $deliveryRole = Role::create([
                'nombre' => 'Delivery',
            ]);

            $clienteRole = Role::create([
                'nombre' => 'Cliente',
            ]);

            $delivery1 = User::create([
                'role_id' => $deliveryRole->id,
                'name' => 'Delivery 1',
                'email' => 'balance1@test.com',
                'password' => 'password',
                'estado' => 'activo',
            ]);

            $delivery2 = User::create([
                'role_id' => $deliveryRole->id,
                'name' => 'Delivery 2',
                'email' => 'balance2@test.com',
                'password' => 'password',
                'estado' => 'activo',
            ]);

            $delivery3 = User::create([
                'role_id' => $deliveryRole->id,
                'name' => 'Delivery 3',
                'email' => 'balance3@test.com',
                'password' => 'password',
                'estado' => 'activo',
            ]);

            $cliente = User::create([
                'role_id' => $clienteRole->id,
                'name' => 'Cliente balance',
                'email' => 'cliente-balance@test.com',
                'password' => 'password',
                'estado' => 'activo',
            ]);

            /*
             * Carga acumulada de hoy:
             * D1 = 8 km
             * D2 = 3 km
             * D3 = 6 km
             *
             * Todos están libres porque sus pedidos anteriores ya fueron
             * entregados.
             */
            foreach ([
                [$delivery1, 8.00, '13:00'],
                [$delivery2, 3.00, '13:05'],
                [$delivery3, 6.00, '13:10'],
            ] as [$delivery, $distancia, $hora]) {
                $pedidoEntregado = Pedido::create([
                    'user_id' => $cliente->id,
                    'total' => 50,
                    'distancia_delivery_km' => $distancia,
                    'estado' => 'entregado',
                    'direccion_entrega' => 'Historial ' . $delivery->id,
                ]);

                $asignacion = AsignacionDelivery::create([
                    'pedido_id' => $pedidoEntregado->id,
                    'delivery_id' => $delivery->id,
                ]);

                $momento = Carbon::createFromFormat(
                    'Y-m-d H:i',
                    '2026-10-01 ' . $hora,
                    'America/La_Paz'
                );

                $asignacion->update([
                    'created_at' => $momento->copy()->utc(),
                    'updated_at' => $momento->copy()->utc(),
                ]);
            }

            // Los pedidos de la cola tienen distancias diferentes,
            // pero la distancia NO decide qué pedido sale primero.
            $pedido1 = Pedido::create([
                'user_id' => $cliente->id,
                'total' => 60,
                'distancia_delivery_km' => 20.00,
                'estado' => 'listo',
                'fecha_listo' => Carbon::createFromFormat(
                    'Y-m-d H:i',
                    '2026-10-01 14:00',
                    'America/La_Paz'
                ),
                'direccion_entrega' => 'Cola 1',
            ]);

            $pedido2 = Pedido::create([
                'user_id' => $cliente->id,
                'total' => 60,
                'distancia_delivery_km' => 1.00,
                'estado' => 'listo',
                'fecha_listo' => Carbon::createFromFormat(
                    'Y-m-d H:i',
                    '2026-10-01 14:02',
                    'America/La_Paz'
                ),
                'direccion_entrega' => 'Cola 2',
            ]);

            $pedido3 = Pedido::create([
                'user_id' => $cliente->id,
                'total' => 60,
                'distancia_delivery_km' => 5.00,
                'estado' => 'listo',
                'fecha_listo' => Carbon::createFromFormat(
                    'Y-m-d H:i',
                    '2026-10-01 14:04',
                    'America/La_Paz'
                ),
                'direccion_entrega' => 'Cola 3',
            ]);

            $asignados = app(AsignarPedidoDeliveryService::class)->procesarCola();

            $this->assertSame(3, $asignados);

            // FIFO: el pedido más antiguo de la cola sale primero,
            // aunque tenga una distancia mayor.
            $this->assertEquals(
                $delivery2->id,
                AsignacionDelivery::where('pedido_id', $pedido1->id)->value('delivery_id')
            );

            // Los otros dos Delivery libres continúan según el balance:
            // D3 tenía 6 km y D1 tenía 8 km.
            $this->assertEquals(
                $delivery3->id,
                AsignacionDelivery::where('pedido_id', $pedido2->id)->value('delivery_id')
            );

            $this->assertEquals(
                $delivery1->id,
                AsignacionDelivery::where('pedido_id', $pedido3->id)->value('delivery_id')
            );
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_delivery_ocupado_no_recibe_otro_pedido_activo(): void
    {
        $deliveryRole = Role::create([
            'nombre' => 'Delivery',
        ]);

        $clienteRole = Role::create([
            'nombre' => 'Cliente',
        ]);

        $delivery = User::create([
            'role_id' => $deliveryRole->id,
            'name' => 'Delivery ocupado',
            'email' => 'ocupado@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $cliente = User::create([
            'role_id' => $clienteRole->id,
            'name' => 'Cliente ocupado',
            'email' => 'cliente-ocupado@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $pedidoActivo = Pedido::create([
            'user_id' => $cliente->id,
            'total' => 40,
            'estado' => 'en_camino',
            'direccion_entrega' => 'Activo',
        ]);

        AsignacionDelivery::create([
            'pedido_id' => $pedidoActivo->id,
            'delivery_id' => $delivery->id,
        ]);

        $pedidoEnCola = Pedido::create([
            'user_id' => $cliente->id,
            'total' => 50,
            'estado' => 'listo',
            'direccion_entrega' => 'En cola',
        ]);

        $asignados = app(AsignarPedidoDeliveryService::class)->procesarCola();

        $this->assertSame(0, $asignados);
        $this->assertDatabaseMissing('asignaciones_delivery', [
            'pedido_id' => $pedidoEnCola->id,
        ]);
        $this->assertEquals('listo', $pedidoEnCola->fresh()->estado);
    }

    public function test_cola_solo_muestra_la_cantidad_y_no_los_detalles(): void
    {
        $deliveryRole = Role::create([
            'nombre' => 'Delivery',
        ]);

        $clienteRole = Role::create([
            'nombre' => 'Cliente',
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
        ]);

        $clienteRole = Role::create([
            'nombre' => 'Cliente',
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
            'direccion_entrega' => 'Dirección privada',
        ]);

        AsignacionDelivery::create([
            'pedido_id' => $pedido->id,
            'delivery_id' => $delivery2->id,
        ]);

        $this->actingAs($delivery1)
            ->get('/delivery/pedidos/' . $pedido->id)
            ->assertNotFound();

        $this->actingAs($delivery2)
            ->get('/delivery/pedidos/' . $pedido->id)
            ->assertOk();
    }
}
