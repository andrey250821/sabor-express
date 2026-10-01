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

        $estadosPermitidos = [
            'pagado',
            'preparando',
            'listo',
            'asignado',
            'en_camino',
            'entregado',
            'cancelado',
        ];

        $estadoSeleccionado = $request->input('estado');

        if (!in_array($estadoSeleccionado, $estadosPermitidos, true)) {
            $estadoSeleccionado = null;
        }

        $pedidos = Pedido::with([
            'user',
            'asignacionDelivery.delivery',
            'comprobantePago',
        ])
            ->whereBetween('created_at', [$inicioUtc, $finUtc])
            ->whereHas('comprobantePago', function ($query) {
                $query->where('estado', 'aprobado');
            })
            ->when(
                $estadoSeleccionado,
                fn ($query) => $query->where(
                    'estado',
                    $estadoSeleccionado
                )
            )
            ->orderBy('created_at', 'desc')
            ->get();

        $nombresEstados = [
            'pagado' => 'Pagados',
            'preparando' => 'Preparando',
            'listo' => 'Listos',
            'asignado' => 'Asignados',
            'en_camino' => 'En camino',
            'entregado' => 'Entregados',
            'cancelado' => 'Cancelados',
        ];

        $estadoNombre = $estadoSeleccionado
            ? ($nombresEstados[$estadoSeleccionado] ?? null)
            : null;

        return view(
            'admin.pedidos.index',
            compact(
                'pedidos',
                'fechaSeleccionada',
                'estadoSeleccionado',
                'estadoNombre'
            )
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
