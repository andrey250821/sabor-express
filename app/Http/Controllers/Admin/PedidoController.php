<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Pedido;


class PedidoController extends Controller
{


    public function index()
    {
        // Obtener todos los pedidos
        // incluyendo cliente y delivery asignado
        $pedidos = Pedido::with([
            'user',
            'asignacionDelivery.delivery',
            'comprobantePago'
        ])
            ->whereHas('comprobantePago', function ($query) {
                $query->where('estado', 'aprobado');
            })
            ->orderBy('created_at', 'desc')
            ->get();


        // Obtener los deliverys activos
        $deliverys = \App\Models\User::where('role_id', 3)
            ->where('estado', 'activo')
            ->get();


        return view(
            'admin.pedidos.index',
            compact(
                'pedidos',
                'deliverys'
            )
        );
    }

    public function show($id)
    {

        $pedido = Pedido::with([
            'user',
            'detallePedidos.producto',
            'comprobantePago'
        ])
            ->findOrFail($id);



        return view(
            'admin.pedidos.show',
            compact('pedido')
        );
    }
}
