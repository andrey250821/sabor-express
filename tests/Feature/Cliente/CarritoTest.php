<?php

namespace Tests\Feature\Cliente;

use App\Models\Producto;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class CarritoTest extends TestCase
{
    public function test_cliente_puede_agregar_un_producto_al_carrito(): void
    {
        $role = Role::firstOrCreate(
            ['nombre' => 'Cliente'],
            ['descripcion' => 'Cliente del sistema']
        );

        $user = User::where('email', 'testcliente@saborexpress.com')->firstOrFail();

        $producto = Producto::where('estado', 'disponible')
            ->where('stock', '>', 0)
            ->firstOrFail();

        $response = $this->actingAs($user)
            ->post('/carrito/agregar/' . $producto->id);

        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'message' => 'Producto agregado al carrito.',
        ]);

        $this->assertEquals(
            1,
            session('carrito.' . $producto->id . '.cantidad')
        );

        $this->assertEquals(
            $producto->id,
            session('carrito.' . $producto->id . '.id')
        );
    }
}
