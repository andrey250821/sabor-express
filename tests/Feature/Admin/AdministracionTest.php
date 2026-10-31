<?php

namespace Tests\Feature\Admin;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdministracionTest extends TestCase
{
    use RefreshDatabase;

    private function crearUsuario(string $nombreRol, string $correo): User
    {
        $rol = Role::where('nombre', $nombreRol)->firstOrFail();

        return User::create([
            'role_id' => $rol->id,
            'name' => 'Usuario de prueba',
            'email' => $correo,
            'password' => 'password',
            'estado' => 'activo',
        ]);
    }

    public function test_cliente_no_puede_acceder_a_la_configuracion_administrativa(): void
    {
        $cliente = $this->crearUsuario(
            'Cliente',
            'cliente-administracion-test@example.com'
        );

        $this->actingAs($cliente)
            ->get(route('admin.configuracion.index'))
            ->assertForbidden();
    }

    public function test_administrador_puede_registrar_producto_asociado_a_una_categoria(): void
    {
        $administrador = $this->crearUsuario(
            'Administrador',
            'admin-producto-test@example.com'
        );

        $categoria = Categoria::create([
            'nombre' => 'Categoría prueba administración',
            'estado' => 'activo',
        ]);

        $respuesta = $this->actingAs($administrador)
            ->post(route('admin.productos.store'), [
                'categoria_id' => $categoria->id,
                'nombre' => 'Hamburguesa de prueba administrativa',
                'descripcion' => 'Producto creado mediante PHPUnit.',
                'precio' => '32.50',
                'stock' => 4,
            ]);

        $respuesta->assertRedirect(route('admin.productos.index'));
        $respuesta->assertSessionHas(
            'success',
            'Producto creado correctamente.'
        );

        $this->assertDatabaseHas('productos', [
            'categoria_id' => $categoria->id,
            'nombre' => 'Hamburguesa de prueba administrativa',
            'precio' => '32.50',
            'stock' => 4,
            'estado' => 'disponible',
        ]);
    }

    public function test_administrador_no_puede_crear_categorias_con_nombres_duplicados(): void
    {
        $administrador = $this->crearUsuario(
            'Administrador',
            'admin-categoria-duplicada-test@example.com'
        );

        Categoria::create([
            'nombre' => 'Hamburguesas',
            'estado' => 'activo',
        ]);

        $respuesta = $this->actingAs($administrador)
            ->from(route('admin.categorias.create'))
            ->post(route('admin.categorias.store'), [
                'nombre' => '  hamburguesas  ',
            ]);

        $respuesta->assertRedirect(route('admin.categorias.create'));
        $respuesta->assertSessionHasErrors('nombre');

        $this->assertDatabaseCount('categorias', 1);
    }

    public function test_administrador_no_puede_crear_productos_con_nombres_duplicados_en_otra_categoria(): void
    {
        $administrador = $this->crearUsuario(
            'Administrador',
            'admin-producto-duplicado-test@example.com'
        );

        $categoriaOriginal = Categoria::create([
            'nombre' => 'Categoría original de prueba',
            'estado' => 'activo',
        ]);

        $segundaCategoria = Categoria::create([
            'nombre' => 'Segunda categoría de prueba',
            'estado' => 'activo',
        ]);

        Producto::create([
            'categoria_id' => $categoriaOriginal->id,
            'nombre' => 'Hamburguesa Clasica',
            'descripcion' => 'Producto existente para prueba.',
            'precio' => '25.00',
            'stock' => 10,
            'estado' => 'disponible',
        ]);

        $respuesta = $this->actingAs($administrador)
            ->from(route('admin.productos.create'))
            ->post(route('admin.productos.store'), [
                'categoria_id' => $segundaCategoria->id,
                'nombre' => '  hamburguesa clasica  ',
                'descripcion' => 'Intento de duplicado.',
                'precio' => '30.00',
                'stock' => 5,
            ]);

        $respuesta->assertRedirect(route('admin.productos.create'));
        $respuesta->assertSessionHasErrors('nombre');

        $this->assertDatabaseCount('productos', 1);
    }
}
