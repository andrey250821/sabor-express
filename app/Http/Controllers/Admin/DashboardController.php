<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\ComprobantePago;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | CONTADORES GENERALES
        |--------------------------------------------------------------------------
        */

        $pedidos = Pedido::count();

        $clientes = User::where('role_id', 2)->count();

        $deliverys = User::where('role_id', 3)
            ->where('estado', 'activo')
            ->count();

        $productos = Producto::count();


        /*
        |--------------------------------------------------------------------------
        | PEDIDOS POR ESTADO
        |--------------------------------------------------------------------------
        */

        $pedidosPendientes = Pedido::where('estado', 'pendiente')->count();

        $pedidosPagados = Pedido::where('estado', 'pagado')->count();

        $pedidosPreparando = Pedido::where('estado', 'preparando')->count();

        $pedidosListos = Pedido::where('estado', 'listo')->count();

        $pedidosAsignados = Pedido::where('estado', 'asignado')->count();

        $pedidosEnCamino = Pedido::where('estado', 'en_camino')->count();

        $pedidosEntregados = Pedido::where('estado', 'entregado')->count();

        $pedidosCancelados = Pedido::where('estado', 'cancelado')->count();


        /*
        |--------------------------------------------------------------------------
        | VENTAS DEL MES
        |--------------------------------------------------------------------------
        |
        | Se consideran ventas los pedidos que ya fueron pagados
        | y que se encuentran dentro del flujo normal del pedido.
        |
        */

        $estadosVenta = [
            'pagado',
            'preparando',
            'listo',
            'asignado',
            'en_camino',
            'entregado',
        ];

        $ventasMes = Pedido::whereYear(
            'created_at',
            Carbon::now()->year
        )
            ->whereMonth(
                'created_at',
                Carbon::now()->month
            )
            ->whereIn('estado', $estadosVenta)
            ->sum('total');


        /*
        |--------------------------------------------------------------------------
        | ÚLTIMOS PEDIDOS
        |--------------------------------------------------------------------------
        */

        $ultimosPedidos = Pedido::with([
            'user',
            'asignacionDelivery.delivery'
        ])
            ->latest()
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | VENTAS ÚLTIMOS 7 DÍAS
        |--------------------------------------------------------------------------
        */

        $ventasSemana = Pedido::selectRaw(
            'DATE(created_at) as fecha, SUM(total) as total'
        )
            ->whereDate(
                'created_at',
                '>=',
                Carbon::now()->subDays(6)
            )
            ->whereIn('estado', $estadosVenta)
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | COMPROBANTES PENDIENTES
        |--------------------------------------------------------------------------
        */

        $comprobantesPendientes = ComprobantePago::where(
            'estado',
            'pendiente'
        )->count();


        /*
        |--------------------------------------------------------------------------
        | ENVIAR DATOS A LA VISTA
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.dashboard.index',
            compact(
                'pedidos',
                'clientes',
                'deliverys',
                'productos',

                'pedidosPendientes',
                'pedidosPagados',
                'pedidosPreparando',
                'pedidosListos',
                'pedidosAsignados',
                'pedidosEnCamino',
                'pedidosEntregados',
                'pedidosCancelados',

                'ventasMes',
                'ultimosPedidos',
                'ventasSemana',
                'comprobantesPendientes'
            )
        );
    }
}
