<?php

namespace Tests\Feature\Cocinero;

use App\Models\AsignacionDelivery;
use App\Models\Pedido;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_marcar_pedido_listo_guarda_fecha_y_procesa_la_cola_fifo(): void
    {
        $cocineroRole = Role::create([
            'nombre' => 'Cocinero',
        ]);

        $deliveryRole = Role::create([
            'nombre' => 'Delivery',
        ]);

        $clienteRole = Role::create([
            'nombre' => 'Cliente',
        ]);

        $cocinero = User::create([
            'role_id' => $cocineroRole->id,
            'name' => 'Cocinero de prueba',
            'email' => 'cocinero-queue@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $delivery = User::create([
            'role_id' => $deliveryRole->id,
            'name' => 'Delivery de prueba',
            'email' => 'delivery-queue@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $cliente = User::create([
            'role_id' => $clienteRole->id,
            'name' => 'Cliente de prueba',
            'email' => 'cliente-queue@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        /*
         * Ya existen dos pedidos en la cola.
         * Son anteriores al pedido que el cocinero está terminando.
         */
        $pedidoCola1 = Pedido::create([
            'user_id' => $cliente->id,
            'total' => 50,
            'estado' => 'listo',
            'fecha_listo' => now()->subMinutes(10),
            'direccion_entrega' => 'Cola 1',
        ]);

        $pedidoCola2 = Pedido::create([
            'user_id' => $cliente->id,
            'total' => 60,
            'estado' => 'listo',
            'fecha_listo' => now()->subMinutes(5),
            'direccion_entrega' => 'Cola 2',
        ]);

        /*
         * Este es el pedido que Cocina termina ahora.
         * Debe guardar su propia fecha_listo, pero NO debe saltarse
         * los dos pedidos que ya estaban esperando.
         */
        $pedidoEnPreparacion = Pedido::create([
            'user_id' => $cliente->id,
            'total' => 70,
            'estado' => 'preparando',
            'cocinero_id' => $cocinero->id,
            'direccion_entrega' => 'Pedido recién terminado',
        ]);

        $response = $this->actingAs($cocinero)
            ->put('/cocinero/pedidos/' . $pedidoEnPreparacion->id . '/listo');

        $response->assertRedirect();

        $pedidoEnPreparacion->refresh();
        $pedidoCola1->refresh();

        $this->assertEquals('listo', $pedidoEnPreparacion->estado);
        $this->assertNotNull($pedidoEnPreparacion->fecha_listo);

        // El pedido que ya llevaba más tiempo en cola debe recibir
        // el Delivery disponible.
        $this->assertEquals(
            $delivery->id,
            AsignacionDelivery::where(
                'pedido_id',
                $pedidoCola1->id
            )->value('delivery_id')
        );

        $this->assertEquals(
            'asignado',
            $pedidoCola1->estado
        );

        // Los pedidos posteriores todavía no deben adelantarse.
        $this->assertEquals(
            'listo',
            $pedidoCola2->fresh()->estado
        );

        $this->assertEquals(
            'listo',
            $pedidoEnPreparacion->fresh()->estado
        );
    }
}
