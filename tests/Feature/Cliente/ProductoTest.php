<?php

namespace Tests\Feature\Cliente;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoTest extends TestCase
{
    use RefreshDatabase;

    private function crearCategoria(string $nombre): Categoria
    {
        return Categoria::create([
            'nombre' => $nombre,
            'estado' => 'activo',
        ]);
    }

    private function crearProducto(
        string $nombre,
        Categoria $categoria,
        string $estado = 'disponible',
        int $stock = 5
    ): Producto {
        return Producto::create([
            'categoria_id' => $categoria->id,
            'nombre' => $nombre,
            'descripcion' => 'Producto creado para una prueba automatizada.',
            'precio' => 25.00,
            'stock' => $stock,
            'estado' => $estado,
        ]);
    }

    public function test_catalogo_muestra_solo_productos_disponibles_con_stock(): void
    {
        $categoria = $this->crearCategoria('Hamburguesas');

        $disponible = $this->crearProducto(
            'Hamburguesa disponible',
            $categoria
        );

        $agotado = $this->crearProducto(
            'Hamburguesa agotada',
            $categoria,
            'agotado',
            4
        );

        $sinStock = $this->crearProducto(
            'Hamburguesa sin stock',
            $categoria,
            'disponible',
            0
        );

        $this->get(route('cliente.productos.index'))
            ->assertOk()
            ->assertViewHas('productos', function ($productos) use (
                $disponible,
                $agotado,
                $sinStock
            ): bool {
                $ids = $productos->modelKeys();

                return in_array($disponible->id, $ids, true)
                    && !in_array($agotado->id, $ids, true)
                    && !in_array($sinStock->id, $ids, true);
            });
    }

    public function test_catalogo_permite_buscar_productos_por_nombre(): void
    {
        $categoria = $this->crearCategoria('Comidas');

        $hamburguesa = $this->crearProducto(
            'Hamburguesa Andina',
            $categoria
        );

        $salchipapa = $this->crearProducto(
            'Salchipapa Especial',
            $categoria
        );

        $this->get(route('cliente.productos.index', [
            'buscar' => 'Andina',
        ]))
            ->assertOk()
            ->assertViewHas('productos', function ($productos) use (
                $hamburguesa,
                $salchipapa
            ): bool {
                $ids = $productos->modelKeys();

                return in_array($hamburguesa->id, $ids, true)
                    && !in_array($salchipapa->id, $ids, true);
            });
    }

    public function test_catalogo_permite_filtrar_productos_por_categoria(): void
    {
        $categoriaHamburguesas = $this->crearCategoria('Hamburguesas');
        $categoriaBebidas = $this->crearCategoria('Bebidas');

        $hamburguesa = $this->crearProducto(
            'Hamburguesa Clásica',
            $categoriaHamburguesas
        );

        $bebida = $this->crearProducto(
            'Limonada',
            $categoriaBebidas
        );

        $this->get(route('cliente.productos.index', [
            'categoria_id' => $categoriaHamburguesas->id,
        ]))
            ->assertOk()
            ->assertViewHas('productos', function ($productos) use (
                $hamburguesa,
                $bebida
            ): bool {
                $ids = $productos->modelKeys();

                return in_array($hamburguesa->id, $ids, true)
                    && !in_array($bebida->id, $ids, true);
            });
    }
}
