<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Mostrar productos disponibles al cliente.
     */
    public function index(Request $request)
    {
        // Obtener todas las categorías
        $categorias = Categoria::orderBy('nombre')->get();

        // Consulta de productos
        $query = Producto::with('categoria')
            ->where('estado', 'disponible')
            ->where('stock', '>', 0);

        // FILTRO POR CATEGORÍA
        if ($request->filled('categoria_id')) {
            $query->where(
                'categoria_id',
                $request->categoria_id
            );
        }

        // BÚSQUEDA POR NOMBRE
        if ($request->filled('buscar')) {
            $query->where(
                'nombre',
                'like',
                '%' . $request->buscar . '%'
            );
        }

        // Obtener productos
        $productos = $query
            ->orderBy('nombre')
            ->get();

        return view(
            'cliente.productos.index',
            compact(
                'productos',
                'categorias'
            )
        );
    }
}
