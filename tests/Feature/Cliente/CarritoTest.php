<?php

namespace Tests\Feature\Cliente;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarritoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Cada prueba crea sus propios datos y no depende de la base importada
     * que se usa para trabajar normalmente en el sistema.
     *
     * @return array{0: User, 1: Producto}
     */
    private function crearDatosCarrito(): array
    {
        $role = Role::firstOrCreate(['nombre' => 'Cliente']);

        $user = User::create([
            'role_id' => $role->id,
            'name' => 'Cliente Carrito',
            'email' => 'cliente-carrito@example.com',
            'telefono' => '71234567',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $categoria = Categoria::create([
            'nombre' => 'Categoría de prueba',
            'estado' => 'activo',
        ]);

        $producto = Producto::create([
            'categoria_id' => $categoria->id,
            'nombre' => 'Hamburguesa de prueba',
            'descripcion' => 'Producto creado para la prueba automatizada.',
            'precio' => 25.00,
            'stock' => 5,
            'estado' => 'disponible',
        ]);

        return [$user, $producto];
    }

    public function test_cliente_puede_agregar_un_producto_al_carrito(): void
    {
        [$user, $producto] = $this->crearDatosCarrito();

        $response = $this->actingAs($user)
            ->postJson('/carrito/agregar/' . $producto->id);

        $response->assertOk();

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
        [$user, $producto] = $this->crearDatosCarrito();

        $response = $this->actingAs($user)
            ->post('/carrito/agregar/' . $producto->id);

        $response->assertRedirect();
        $response->assertSessionHas(
            'success',
            'Producto agregado al carrito.'
        );
    }
}
