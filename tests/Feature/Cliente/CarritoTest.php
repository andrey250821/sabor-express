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
        );

        $user = User::where('email', 'testcliente@saborexpress.com')->firstOrFail();

        $producto = Producto::where('estado', 'disponible')
            ->where('stock', '>', 0)
            ->firstOrFail();

        $response = $this->actingAs($user)
            ->postJson('/carrito/agregar/' . $producto->id);

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

    public function test_formulario_normal_redirige_y_muestra_mensaje_en_vez_de_json(): void
    {
        $user = User::where('email', 'testcliente@saborexpress.com')->firstOrFail();

        $producto = Producto::where('estado', 'disponible')
            ->where('stock', '>', 0)
            ->firstOrFail();

        $response = $this->actingAs($user)
            ->post('/carrito/agregar/' . $producto->id);

        $response->assertRedirect();
        $response->assertSessionHas(
            'success',
            'Producto agregado al carrito.'
        );
    }

}
