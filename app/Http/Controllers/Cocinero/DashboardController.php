<?php

namespace App\Http\Controllers\Cocinero;

use App\Http\Controllers\Controller;
use App\Models\Pedido;

class DashboardController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with([
            'user',
            'detallePedidos.producto'
        ])
            ->whereIn('estado', [
                'pagado',
                'preparando',
                'listo'
            ])
            ->orderBy('created_at', 'asc')
            ->get();

        $pedidosPendientes = $pedidos
            ->where('estado', 'pagado');

        $pedidosPreparando = $pedidos
            ->where('estado', 'preparando');

        $pedidosListos = $pedidos
            ->where('estado', 'listo');

        $totalPedidos = $pedidos->count();

        return view(
            'cocinero.dashboard.index',
            compact(
                'pedidos',
                'pedidosPendientes',
                'pedidosPreparando',
                'pedidosListos',
                'totalPedidos'
            )
        );
    }
}
