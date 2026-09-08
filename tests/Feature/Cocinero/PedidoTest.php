<?php

namespace Tests\Feature\Cocinero;

use App\Models\Pedido;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class PedidoTest extends TestCase
{
    public function test_cocinero_puede_cambiar_el_estado_del_pedido(): void
    {
        $role = Role::firstOrCreate(
            ['nombre' => 'Cocinero'],
            ['descripcion' => 'Personal de cocina']
        );

        $cocinero = User::where('email', 'testcocinero@saborexpress.com')->first();

        if (!$cocinero) {
            $cocinero = User::create([
                'role_id' => $role->id,
                'name' => 'Cocinero de Prueba',
                'email' => 'testcocinero@saborexpress.com',
                'password' => 'password',
                'estado' => 1,
            ]);
        }

        $pedido = Pedido::where('estado', 'pagado')->firstOrFail();

        $response = $this->actingAs($cocinero)
            ->put('/cocinero/pedidos/' . $pedido->id . '/preparar');

        $response->assertRedirect();

        $pedido->refresh();

        $this->assertEquals('preparando', $pedido->estado);

        $response = $this->actingAs($cocinero)
            ->put('/cocinero/pedidos/' . $pedido->id . '/listo');

        $response->assertRedirect();

        $pedido->refresh();

        $this->assertEquals('listo', $pedido->estado);
    }
}