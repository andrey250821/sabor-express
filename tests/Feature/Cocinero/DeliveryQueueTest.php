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

    public function test_marcar_pedido_listo_procesa_la_cola_fifo_usando_created_at(): void
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
            'direccion_entrega' => 'Cola 1',
        ]);

        $pedidoCola2 = Pedido::create([
            'user_id' => $cliente->id,
            'total' => 60,
            'estado' => 'listo',
            'direccion_entrega' => 'Cola 2',
        ]);

        /*
         * Este es el pedido que Cocina termina ahora.
         * Debe quedar detrás de los dos pedidos que ya estaban esperando
         * porque created_at es posterior a ellos.
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

    public function test_pedido_marcado_listo_y_asignado_sigue_apareciendo_en_listos_de_cocina(): void
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
            'name' => 'Cocinero de prueba 2',
            'email' => 'cocinero-queue-2@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $delivery = User::create([
            'role_id' => $deliveryRole->id,
            'name' => 'Delivery de prueba 2',
            'email' => 'delivery-queue-2@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $cliente = User::create([
            'role_id' => $clienteRole->id,
            'name' => 'Cliente de prueba 2',
            'email' => 'cliente-queue-2@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $pedido = Pedido::create([
            'user_id' => $cliente->id,
            'total' => 80,
            'estado' => 'preparando',
            'cocinero_id' => $cocinero->id,
            'direccion_entrega' => 'Pedido listo',
        ]);

        $response = $this->actingAs($cocinero)
            ->put('/cocinero/pedidos/' . $pedido->id . '/listo');

        $response->assertRedirect();

        $pedido->refresh();

        $this->assertEquals('asignado', $pedido->estado);

        $this->assertDatabaseHas('asignaciones_delivery', [
            'pedido_id' => $pedido->id,
            'delivery_id' => $delivery->id,
        ]);

        $this->assertEquals(
            1,
            Pedido::query()
                ->whereIn('estado', ['listo', 'asignado'])
                ->where('cocinero_id', $cocinero->id)
                ->count()
        );
    }
}
