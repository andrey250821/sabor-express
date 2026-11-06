<?php

namespace Tests\Feature\Cliente;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Role;
use App\Models\User;
use App\Services\ReservaCarritoService;
use Illuminate\Support\Facades\DB;
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


    public function test_stock_disponible_se_reserva_al_agregar_y_no_permite_superar_el_stock(): void
    {
        [$user, $producto] = $this->crearDatosCarrito();

        for ($i = 1; $i <= 5; $i++) {
            $response = $this->actingAs($user)
                ->postJson('/carrito/agregar/' . $producto->id);

            $response->assertOk()
                ->assertJson([
                    'success' => true,
                    'stockDisponible' => 5 - $i,
                    'cantidad' => $i,
                ]);
        }

        $sinStock = $this->actingAs($user)
            ->postJson('/carrito/agregar/' . $producto->id);

        $sinStock->assertStatus(422)
            ->assertJson([
                'success' => false,
                'stockDisponible' => 0,
            ]);

        // El stock físico se descuenta al confirmar el pedido, no al reservar.
        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'stock' => 5,
        ]);

        $this->assertDatabaseHas('reservas_carrito', [
            'producto_id' => $producto->id,
            'cantidad' => 5,
        ]);

        $this->assertSame(
            5,
            (int) session('carrito.' . $producto->id . '.cantidad')
        );
    }

    public function test_disminuir_y_eliminar_del_carrito_liberan_stock_en_tiempo_real(): void
    {
        [$user, $producto] = $this->crearDatosCarrito();

        $this->actingAs($user)
            ->postJson('/carrito/agregar/' . $producto->id)
            ->assertOk();

        $this->actingAs($user)
            ->postJson('/carrito/agregar/' . $producto->id)
            ->assertOk();

        $this->actingAs($user)
            ->putJson('/carrito/disminuir/' . $producto->id)
            ->assertOk()
            ->assertJson([
                'success' => true,
                'cantidad' => 1,
                'stockDisponible' => 4,
            ]);

        $this->actingAs($user)
            ->deleteJson('/carrito/eliminar/' . $producto->id)
            ->assertOk()
            ->assertJson([
                'success' => true,
                'stockDisponible' => 5,
                'carritoVacio' => true,
            ]);

        $this->assertDatabaseMissing('reservas_carrito', [
            'producto_id' => $producto->id,
        ]);
    }


    public function test_consumir_la_reserva_descuenta_el_stock_fisico_una_sola_vez(): void
    {
        [$user, $producto] = $this->crearDatosCarrito();

        $this->actingAs($user)
            ->postJson('/carrito/agregar/' . $producto->id)
            ->assertOk();

        $this->actingAs($user)
            ->postJson('/carrito/agregar/' . $producto->id)
            ->assertOk();

        DB::transaction(function () use ($producto) {
            app(ReservaCarritoService::class)
                ->consumirProducto($producto->id, 2);
        });

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'stock' => 3,
            'estado' => 'disponible',
        ]);

        $this->assertDatabaseMissing('reservas_carrito', [
            'producto_id' => $producto->id,
        ]);
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
