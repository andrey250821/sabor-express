<?php

namespace Tests\Feature\Delivery;

use App\Models\AsignacionDelivery;
use App\Models\Pedido;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class PedidoTest extends TestCase
{
    public function test_delivery_puede_completar_el_flujo_de_entrega(): void
    {
        // Buscar o crear el rol Delivery
        $role = Role::firstOrCreate(
            ['nombre' => 'Delivery'],
            ['descripcion' => 'Repartidor del sistema']
        );

        // Buscar o crear usuario Delivery de prueba
        $delivery = User::where(
            'email',
            'testdelivery@saborexpress.com'
        )->first();

        if (!$delivery) {
            $delivery = User::create([
                'role_id' => $role->id,
                'name' => 'Delivery de Prueba',
                'email' => 'testdelivery@saborexpress.com',
                'password' => 'password',
                'estado' => 'activo',
            ]);
        }

        // Buscar un pedido que esté listo
        $pedido = Pedido::where('estado', 'listo')->firstOrFail();

        // Eliminar una asignación previa del pedido si existiera
        AsignacionDelivery::where('pedido_id', $pedido->id)->delete();

        /*
         * 1. DELIVERY TOMA EL PEDIDO
         * listo -> asignado
         */
        $response = $this->actingAs($delivery)
            ->post('/delivery/pedidos/' . $pedido->id . '/tomar');

        $response->assertRedirect();

        $pedido->refresh();

        $this->assertEquals(
            'asignado',
            $pedido->estado
        );

        $asignacion = AsignacionDelivery::where(
            'pedido_id',
            $pedido->id
        )
            ->where(
                'delivery_id',
                $delivery->id
            )
            ->firstOrFail();

        $this->assertEquals(
            'aceptado',
            $asignacion->estado
        );

        /*
         * 2. INICIAR ENTREGA
         * aceptado -> en_camino
         */
        $response = $this->actingAs($delivery)
            ->put('/delivery/pedidos/' . $pedido->id . '/iniciar');

        $response->assertRedirect();

        $pedido->refresh();
        $asignacion->refresh();

        $this->assertEquals(
            'en_camino',
            $pedido->estado
        );

        $this->assertEquals(
            'en_camino',
            $asignacion->estado
        );

        /*
         * 3. MARCAR COMO ENTREGADO
         * en_camino -> entregado
         */
        $response = $this->actingAs($delivery)
            ->put('/delivery/pedidos/' . $pedido->id . '/entregar');

        $response->assertRedirect();

        $pedido->refresh();
        $asignacion->refresh();

        $this->assertEquals(
            'entregado',
            $pedido->estado
        );

        $this->assertEquals(
            'entregado',
            $asignacion->estado
        );
    }
} 