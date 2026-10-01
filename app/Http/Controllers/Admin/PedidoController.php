<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Services\FechaFiltroService;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    /**
     * Mostrar los pedidos aprobados de la fecha seleccionada.
     */
    public function index(
        Request $request,
        FechaFiltroService $fechas
    ) {
        $fechaSeleccionada = $fechas->resolver($request);

        [$inicioUtc, $finUtc] = $fechas->rangoUtc(
            $fechaSeleccionada
        );

        $pedidos = Pedido::with([
            'user',
            'asignacionDelivery.delivery',
            'comprobantePago',
        ])
            ->whereBetween('created_at', [$inicioUtc, $finUtc])
            ->whereHas('comprobantePago', function ($query) {
                $query->where('estado', 'aprobado');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view(
            'admin.pedidos.index',
            compact('pedidos', 'fechaSeleccionada')
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
