<?php

namespace App\Http\Controllers\Cocinero;

use App\Http\Controllers\Controller;
use App\Models\Pedido;

class PedidoController extends Controller
{
    /**
     * Mostrar pedidos disponibles para el cocinero.
     */
    public function index()
    {
        $pedidosPendientes = Pedido::with([
            'user',
            'detallePedidos.producto'
        ])
            ->where('estado', 'pagado')
            ->orderBy('created_at', 'asc')
            ->get();

        $pedidosPreparando = Pedido::with([
            'user',
            'detallePedidos.producto'
        ])
            ->where('estado', 'preparando')
            ->orderBy('created_at', 'asc')
            ->get();

        $pedidosListos = Pedido::with([
            'user',
            'detallePedidos.producto'
        ])
            ->where('estado', 'listo')
            ->orderBy('created_at', 'asc')
            ->get();

        return view(
            'cocinero.pedidos.index',
            compact(
                'pedidosPendientes',
                'pedidosPreparando',
                'pedidosListos'
            )
        );
    }


    /**
     * Mostrar detalle de un pedido.
     */
    public function show($id)
    {
        $pedido = Pedido::with([
            'user',
            'detallePedidos.producto'
        ])->findOrFail($id);

        return view(
            'cocinero.pedidos.show',
            compact('pedido')
        );
    }


    /**
     * Cambiar pedido de PAGADO a PREPARANDO.
     */
    public function preparar($id)
    {
        $pedido = Pedido::findOrFail($id);

        // Solo se puede comenzar a preparar
        // un pedido que esté pagado.
        if ($pedido->estado !== 'pagado') {

            return redirect()
                ->back()
                ->with('error', 'El pedido no puede comenzar a prepararse.');
        }

        $pedido->update([
            'estado' => 'preparando'
        ]);

        return redirect()
            ->route('cocinero.pedidos.show', $pedido->id)
            ->with('success', 'El pedido comenzó a prepararse.');
    }


    /**
     * Cambiar pedido de PREPARANDO a LISTO.
     */
    public function listo($id)
    {
        $pedido = Pedido::findOrFail($id);

        // Solo se puede marcar como listo
        // un pedido que esté en preparación.
        if ($pedido->estado !== 'preparando') {

            return redirect()
                ->back()
                ->with('error', 'El pedido todavía no está en preparación.');
        }

        $pedido->update([
            'estado' => 'listo'
        ]);

        return redirect()
            ->route('cocinero.pedidos.show', $pedido->id)
            ->with('success', 'El pedido está listo.');
    }
}
