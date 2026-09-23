<?php

namespace App\Http\Controllers\Cocinero;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard del cocinero.
     *
     * Pendientes: todos los pedidos pagados que todavía no comenzaron
     * a prepararse.
     *
     * Preparando y Listos: únicamente los pedidos del cocinero
     * autenticado.
     */
    public function index()
    {
        $pedidosPendientes = Pedido::with([
            'user',
            'detallePedidos.producto',
        ])
            ->where('estado', 'pagado')
            ->whereNull('cocinero_id')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $pedidosPreparando = Pedido::with([
            'user',
            'detallePedidos.producto',
        ])
            ->where('estado', 'preparando')
            ->where('cocinero_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $pedidosListos = Pedido::with([
            'user',
            'detallePedidos.producto',
        ])
            ->where('estado', 'listo')
            ->where('cocinero_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $pedidos = $pedidosPendientes
            ->concat($pedidosPreparando)
            ->concat($pedidosListos)
            ->sortBy([
                ['created_at', 'asc'],
                ['id', 'asc'],
            ])
            ->values();

        $totalPedidos =
            $pedidosPendientes->count()
            + $pedidosPreparando->count()
            + $pedidosListos->count();

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
