<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;

class PedidoController extends Controller
{
    /**
     * Mostrar todos los pedidos aprobados.
     */
    public function index()
    {
        $pedidos = Pedido::with([
            'user',
            'asignacionDelivery.delivery',
            'comprobantePago',
        ])
            ->whereHas('comprobantePago', function ($query) {
                $query->where('estado', 'aprobado');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view(
            'admin.pedidos.index',
            compact('pedidos')
        );
    }

    /**
     * Mostrar el detalle completo de un pedido.
     */
    public function show($id)
    {
        $pedido = Pedido::with([
            'user',
            'detallePedidos.producto',
            'comprobantePago',
            'asignacionDelivery.delivery',
        ])
            ->findOrFail($id);

        return view(
            'admin.pedidos.show',
            compact('pedido')
        );
    }
}
