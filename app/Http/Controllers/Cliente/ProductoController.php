<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Services\ReservaCarritoService;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Mostrar productos disponibles y el stock libre de reservas.
     */
    public function index(
        Request $request,
        ReservaCarritoService $reservas
    ) {
        $sincronizacion = $reservas->sincronizarCarrito(
            session()->get('carrito', [])
        );

        if (!$sincronizacion['success']) {
            session()->flash('error', $sincronizacion['message']);
        }

        $reservas->renovar();

        $categorias = Categoria::orderBy('nombre')->get();

        $query = Producto::with('categoria')
            ->withAvg('calificaciones', 'puntuacion')
            ->withCount('calificaciones')
            ->withSum([
                'reservasCarrito as stock_reservado' => function ($consulta) {
                    $consulta->where('expires_at', '>', now());
                },
            ], 'cantidad')
            ->where('estado', 'disponible')
            ->where('stock', '>', 0);

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('buscar')) {
            $query->where(
                'nombre',
                'like',
                '%' . $request->buscar . '%'
            );
        }

        $productos = $query
            ->orderBy('nombre')
            ->get();

        return view(
            'cliente.productos.index',
            compact('productos', 'categorias')
        );
    }

    /**
     * Consulta ligera para actualizar el stock visible sin recargar la página.
     */
    public function stock(ReservaCarritoService $reservas)
    {
        $reservas->renovar();

        $productos = Producto::query()
            ->select('id', 'stock', 'estado')
            ->withSum([
                'reservasCarrito as stock_reservado' => function ($consulta) {
                    $consulta->where('expires_at', '>', now());
                },
            ], 'cantidad')
            ->get();

        $stocks = $productos->map(function ($producto) {
            $disponible = $producto->estado === 'disponible'
                ? max(
                    0,
                    (int) $producto->stock - (int) ($producto->stock_reservado ?? 0)
                )
                : 0;

            return [
                'id' => (int) $producto->id,
                'stockDisponible' => $disponible,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'stocks' => $stocks,
        ])->header('Cache-Control', 'no-store, private');
    }
}
