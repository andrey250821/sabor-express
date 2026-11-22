<?php

namespace Tests\Feature\Cocinero;

use App\Models\Pedido;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidoTest extends TestCase
{
    use RefreshDatabase;

    public function test_cocinero_puede_cambiar_el_estado_del_pedido(): void
    {
        $cocineroRole = Role::firstOrCreate(['nombre' => 'Cocinero']);
        $clienteRole = Role::firstOrCreate(['nombre' => 'Cliente']);

        $cocinero = User::create([
            'role_id' => $cocineroRole->id,
            'name' => 'Cocinero de prueba',
            'email' => 'testcocinero@example.com',
            'telefono' => '71234568',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $cliente = User::create([
            'role_id' => $clienteRole->id,
            'name' => 'Cliente de prueba',
            'email' => 'cliente-cocinero@example.com',
            'telefono' => '71234569',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $pedido = Pedido::create([
            'user_id' => $cliente->id,
            'total' => 50.00,
            'estado' => 'pagado',
            'direccion_entrega' => 'Dirección de prueba',
        ]);

        $response = $this->actingAs($cocinero)
            ->put('/cocinero/pedidos/' . $pedido->id . '/preparar');

        $response->assertRedirect();
        $this->assertSame('preparando', $pedido->fresh()->estado);

        $response = $this->actingAs($cocinero)
            ->put('/cocinero/pedidos/' . $pedido->id . '/listo');

        $response->assertRedirect();
        $this->assertSame('listo', $pedido->fresh()->estado);
    }

    public function test_estado_en_vivo_cuenta_pedidos_en_cola_y_solo_preparaciones_propias(): void
    {
        $cocineroRole = Role::firstOrCreate(['nombre' => 'Cocinero']);
        $clienteRole = Role::firstOrCreate(['nombre' => 'Cliente']);

        $cocinero = User::create([
            'role_id' => $cocineroRole->id,
            'name' => 'Cocinero principal',
            'email' => 'cocinero-live@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $otroCocinero = User::create([
            'role_id' => $cocineroRole->id,
            'name' => 'Otro cocinero',
            'email' => 'otro-cocinero-live@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $cliente = User::create([
            'role_id' => $clienteRole->id,
            'name' => 'Cliente live',
            'email' => 'cliente-live@test.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        foreach (range(1, 3) as $numero) {
            Pedido::create([
                'user_id' => $cliente->id,
                'total' => 20 * $numero,
                'estado' => 'pagado',
                'direccion_entrega' => 'Cola ' . $numero,
            ]);
        }

        foreach (range(1, 2) as $numero) {
            Pedido::create([
                'user_id' => $cliente->id,
                'cocinero_id' => $cocinero->id,
                'total' => 30 * $numero,
                'estado' => 'preparando',
                'direccion_entrega' => 'Preparación propia ' . $numero,
            ]);
        }

        Pedido::create([
            'user_id' => $cliente->id,
            'cocinero_id' => $otroCocinero->id,
            'total' => 70,
            'estado' => 'preparando',
            'direccion_entrega' => 'Preparación ajena',
        ]);

        Pedido::create([
            'user_id' => $cliente->id,
            'cocinero_id' => $cocinero->id,
            'total' => 80,
            'estado' => 'listo',
            'direccion_entrega' => 'Pedido ya listo',
        ]);

        $this->actingAs($cocinero)
            ->getJson('/cocinero/estado-en-vivo')
            ->assertOk()
            ->assertExactJson([
                'pendientes' => 3,
                'preparando' => 2,
            ]);
    }


}
