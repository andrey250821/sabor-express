<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class CalificacionController extends Controller
{
    /**
     * Mostrar todas las calificaciones de un producto.
     */
    public function index($productoId)
    {
        $producto = Producto::with('categoria')
            ->findOrFail($productoId);

        $calificaciones = Calificacion::where(
            'producto_id',
            $producto->id
        )
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $promedio = $calificaciones->avg('puntuacion');

        $totalCalificaciones = $calificaciones->count();

        $cantidadEstrellas = [
            5 => $calificaciones->where('puntuacion', 5)->count(),
            4 => $calificaciones->where('puntuacion', 4)->count(),
            3 => $calificaciones->where('puntuacion', 3)->count(),
            2 => $calificaciones->where('puntuacion', 2)->count(),
            1 => $calificaciones->where('puntuacion', 1)->count(),
        ];

        /*
         * Calificación del usuario autenticado.
         *
         * Como un usuario solo puede tener una calificación
         * por producto, buscamos directamente por user_id + producto_id.
         */
        $miCalificacion = $calificaciones
            ->firstWhere('user_id', Auth::id());

        return view(
            'cliente.calificaciones.index',
            compact(
                'producto',
                'calificaciones',
                'promedio',
                'totalCalificaciones',
                'cantidadEstrellas',
                'miCalificacion'
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
        $datos = $request->validate([
            'puntuacion' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
            'comentario' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        /*
         * El pedido debe pertenecer al usuario autenticado.
         */
        $pedido = Pedido::where('id', $pedidoId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'El pedido no existe o no tienes permiso para calificarlo.',
            ], 404);
        }

        /*
         * Solo se pueden calificar pedidos entregados.
         */
        if ($pedido->estado !== 'entregado') {
            return response()->json([
                'success' => false,
                'message' => 'Solo puedes calificar productos de pedidos que ya fueron entregados.',
            ], 422);
        }

        /*
         * Comprobar que el producto realmente pertenece
         * al pedido entregado.
         */
        $productoPerteneceAlPedido = $pedido->detallePedidos()
            ->where('producto_id', $productoId)
            ->exists();

        if (!$productoPerteneceAlPedido) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes calificar un producto que no pertenece a este pedido.',
            ], 422);
        }

        /*
         * Comprobar que el producto existe.
         */
        $producto = Producto::find($productoId);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'El producto no existe.',
            ], 404);
        }

        /*
         * Regla principal:
         *
         * Un usuario solamente puede tener UNA calificación
         * para un producto, aunque lo compre varias veces.
         */
        $yaCalifico = Calificacion::where('user_id', Auth::id())
            ->where('producto_id', $productoId)
            ->exists();

        if ($yaCalifico) {
            return response()->json([
                'success' => false,
                'message' => 'Ya calificaste este producto anteriormente. Puedes editar tu calificación.',
                'already_rated' => true,
            ], 422);
        }

        try {

            $calificacion = Calificacion::create([
                'pedido_id' => $pedido->id,
                'user_id' => Auth::id(),
                'producto_id' => $producto->id,
                'puntuacion' => $datos['puntuacion'],
                'comentario' => $datos['comentario'] ?? null,
            ]);
        } catch (QueryException $e) {

            /*
             * Protección adicional por si dos peticiones
             * intentaran crear la misma calificación al mismo tiempo.
             */
            return response()->json([
                'success' => false,
                'message' => 'No fue posible registrar la calificación. Es posible que ya hayas calificado este producto.',
            ], 422);
        }

        /*
         * Calcular nuevamente los datos del producto
         * después de guardar la calificación.
         */
        $calificaciones = Calificacion::where(
            'producto_id',
            $producto->id
        )->get();

        $promedio = $calificaciones->avg('puntuacion');

        $totalCalificaciones = $calificaciones->count();

        return response()->json([
            'success' => true,
            'message' => '¡Gracias! Tu calificación fue registrada correctamente.',
            'calificacion' => [
                'id' => $calificacion->id,
                'puntuacion' => $calificacion->puntuacion,
                'comentario' => $calificacion->comentario,
                'usuario' => Auth::user()->name,
                'fecha' => $calificacion->created_at->format('d/m/Y'),
            ],
            'promedio' => round($promedio, 1),
            'totalCalificaciones' => $totalCalificaciones,
        ], 201);
    }

    /**
     * Editar una calificación existente.
     */
    public function update(
        Request $request,
        $pedidoId,
        $productoId
    ) {
        $datos = $request->validate([
            'puntuacion' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
            'comentario' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        /*
         * Verificar que el pedido pertenece al usuario.
         */
        $pedido = Pedido::where('id', $pedidoId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'El pedido no existe o no tienes permiso para modificar esta calificación.',
            ], 404);
        }

        /*
         * El pedido utilizado para editar también debe
         * estar entregado.
         */
        if ($pedido->estado !== 'entregado') {
            return response()->json([
                'success' => false,
                'message' => 'Solo puedes modificar calificaciones desde pedidos que ya fueron entregados.',
            ], 422);
        }

        /*
         * Comprobar que el producto pertenece al pedido.
         */
        $productoPerteneceAlPedido = $pedido->detallePedidos()
            ->where('producto_id', $productoId)
            ->exists();

        if (!$productoPerteneceAlPedido) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes modificar la calificación de un producto que no pertenece a este pedido.',
            ], 422);
        }

        /*
         * Buscar la única calificación que tiene el usuario
         * para este producto.
         */
        $calificacion = Calificacion::where(
            'user_id',
            Auth::id()
        )
            ->where(
                'producto_id',
                $productoId
            )
            ->first();

        if (!$calificacion) {
            return response()->json([
                'success' => false,
                'message' => 'Todavía no tienes una calificación para este producto.',
            ], 404);
        }

        /*
         * Actualizar solamente la puntuación y comentario.
         *
         * NO cambiamos pedido_id.
         */
        $calificacion->update([
            'puntuacion' => $datos['puntuacion'],
            'comentario' => $datos['comentario'] ?? null,
        ]);

        /*
         * Recalcular promedio.
         */
        $calificaciones = Calificacion::where(
            'producto_id',
            $productoId
        )->get();

        $promedio = $calificaciones->avg('puntuacion');

        $totalCalificaciones = $calificaciones->count();

        return response()->json([
            'success' => true,
            'message' => 'Tu calificación fue actualizada correctamente.',
            'calificacion' => [
                'id' => $calificacion->id,
                'puntuacion' => $calificacion->puntuacion,
                'comentario' => $calificacion->comentario,
                'usuario' => Auth::user()->name,
                'fecha' => $calificacion->created_at->format('d/m/Y'),
            ],
            'promedio' => round($promedio, 1),
            'totalCalificaciones' => $totalCalificaciones,
        ]);
    }
}
