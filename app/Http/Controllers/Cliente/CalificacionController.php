<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalificacionController extends Controller
{
    /**
     * Mostrar las calificaciones de un producto.
     */
    public function index($productoId)
    {
        // Buscar el producto
        $producto = Producto::with('categoria')
            ->findOrFail($productoId);

        // Obtener todas las calificaciones del producto
        // junto con el usuario que realizó la calificación
        $calificaciones = Calificacion::where(
            'producto_id',
            $producto->id
        )
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Promedio de puntuación
        $promedio = $calificaciones->avg('puntuacion');

        // Total de calificaciones
        $totalCalificaciones = $calificaciones->count();

        // Cantidad de calificaciones por estrellas
        $cantidadEstrellas = [
            5 => $calificaciones->where('puntuacion', 5)->count(),
            4 => $calificaciones->where('puntuacion', 4)->count(),
            3 => $calificaciones->where('puntuacion', 3)->count(),
            2 => $calificaciones->where('puntuacion', 2)->count(),
            1 => $calificaciones->where('puntuacion', 1)->count(),
        ];

        return view(
            'cliente.calificaciones.index',
            compact(
                'producto',
                'calificaciones',
                'promedio',
                'totalCalificaciones',
                'cantidadEstrellas'
            )
        );
    }

    /**
     * Guardar una nueva calificación.
     */
    public function store(
        Request $request,
        $pedidoId,
        $productoId
    ) {
        // Validar datos
        $datos = $request->validate([
            'puntuacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
        ]);

        // Buscar el pedido y comprobar que pertenece
        // al usuario autenticado
        $pedido = Pedido::where('id', $pedidoId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // El pedido debe estar entregado
        if ($pedido->estado !== 'entregado') {

            return back()->with(
                'error',
                'Solo puedes calificar productos de pedidos que ya fueron entregados.'
            );
        }

        // Comprobar que el producto pertenece al pedido
        $productoPerteneceAlPedido = $pedido->detallePedidos()
            ->where('producto_id', $productoId)
            ->exists();

        if (!$productoPerteneceAlPedido) {

            return back()->with(
                'error',
                'No puedes calificar un producto que no pertenece a este pedido.'
            );
        }

        // Comprobar que el producto existe
        $producto = Producto::find($productoId);

        if (!$producto) {

            return back()->with(
                'error',
                'El producto no existe.'
            );
        }

        // Comprobar que el usuario no haya calificado
        // anteriormente este producto
        $yaCalifico = Calificacion::where(
            'user_id',
            Auth::id()
        )
            ->where(
                'producto_id',
                $productoId
            )
            ->exists();

        if ($yaCalifico) {

            return back()->with(
                'error',
                'Ya calificaste este producto anteriormente.'
            );
        }

        // Crear la calificación
        Calificacion::create([
            'pedido_id' => $pedido->id,
            'user_id' => Auth::id(),
            'producto_id' => $producto->id,
            'puntuacion' => $datos['puntuacion'],
            'comentario' => $datos['comentario'] ?? null,
        ]);

        return back()->with(
            'success',
            '¡Gracias! Tu calificación fue registrada correctamente.'
        );
    }
}
