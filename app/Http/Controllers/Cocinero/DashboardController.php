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
            'detallePedidos'
        ])
        ->whereIn('estado', [
            'pagado',
            'preparando',
            'listo'
        ])
        ->orderBy('created_at', 'asc')
        ->get();

        return view('cocinero.dashboard.index', compact('pedidos'));
    }
}