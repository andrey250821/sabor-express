<?php

namespace App\Http\Controllers\Cocinero;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $pendientes = Pedido::with([
            'user',
            'detallePedidos.producto',
        ])
            ->where('estado', 'pagado')
            ->whereNull('cocinero_id')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $miPedidoPendiente = Pedido::with([
            'user',
            'detallePedidos.producto',
        ])
            ->where('estado', 'pagado')
            ->where('cocinero_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->first();

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
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Solo mostramos en "Pedidos recientes" pedidos que no puedan
        // confundirse con el trabajo activo de otro cocinero.
        $pedidos = $pendientes
            ->concat($miPedidoPendiente ? collect([$miPedidoPendiente]) : collect())
            ->concat($pedidosPreparando)
            ->concat($pedidosListos)
            ->sortBy([
                ['created_at', 'asc'],
                ['id', 'asc'],
            ])
            ->values();

        $totalPedidos =
            $pendientes->count()
            + ($miPedidoPendiente ? 1 : 0)
            + $pedidosPreparando->count()
            + $pedidosListos->count();

        return view(
            'cocinero.dashboard.index',
            compact(
                'pedidos',
                'pedidosPendientes',
                'pedidosPreparando',
                'pedidosListos',
                'totalPedidos',
                'miPedidoPendiente'
            )
        );
    }
}
