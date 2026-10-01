<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\ComprobantePago;
use App\Models\AsignacionDelivery;
use App\Services\FechaFiltroService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(
        Request $request,
        FechaFiltroService $fechas
    ) {
        $fechaSeleccionada = $fechas->resolver($request);
        $hoy = $fechas->hoy();

        [$inicioUtc, $finUtc] = $fechas->rangoUtc($fechaSeleccionada);

        /*
        |--------------------------------------------------------------------------
        | INDICADORES GENERALES
        |--------------------------------------------------------------------------
        |
        | Estos indicadores representan el estado actual de la plataforma,
        | no una actividad histórica.
        |
        */
        $clientes = User::where('role_id', 2)->count();

        $deliverys = User::where('role_id', 3)
            ->where('estado', 'activo')
            ->count();

        $productos = Producto::count();


        /*
        |--------------------------------------------------------------------------
        | ACTIVIDAD DE LA FECHA SELECCIONADA
        |--------------------------------------------------------------------------
        */
        $pedidosDiaQuery = Pedido::query()
            ->whereBetween('created_at', [$inicioUtc, $finUtc]);

        $pedidos = (clone $pedidosDiaQuery)->count();

        $pedidosPagados = (clone $pedidosDiaQuery)
            ->where('estado', 'pagado')
            ->count();

        $pedidosPreparando = (clone $pedidosDiaQuery)
            ->where('estado', 'preparando')
            ->count();

        $pedidosListos = (clone $pedidosDiaQuery)
            ->where('estado', 'listo')
            ->count();

        $pedidosAsignados = (clone $pedidosDiaQuery)
            ->where('estado', 'asignado')
            ->count();

        $pedidosEnCamino = (clone $pedidosDiaQuery)
            ->where('estado', 'en_camino')
            ->count();

        $pedidosEntregados = (clone $pedidosDiaQuery)
            ->where('estado', 'entregado')
            ->count();

        $pedidosCancelados = (clone $pedidosDiaQuery)
            ->where('estado', 'cancelado')
            ->count();

        $estadosVenta = [
            'pagado',
            'preparando',
            'listo',
            'asignado',
            'en_camino',
            'entregado',
        ];

        $ventasDia = (clone $pedidosDiaQuery)
            ->whereIn('estado', $estadosVenta)
            ->sum('total');


        /*
        |--------------------------------------------------------------------------
        | VENTAS DEL MES
        |--------------------------------------------------------------------------
        |
        | Se conserva como referencia secundaria para el gráfico.
        |
        */
        $inicioMes = $hoy->copy()->startOfMonth();
        $finMes = $hoy->copy()->endOfMonth();

        [$inicioMesUtc, $finMesUtc] = [
            $inicioMes->copy()->startOfDay()->utc(),
            $finMes->copy()->endOfDay()->utc(),
        ];

        $ventasMes = Pedido::query()
            ->whereBetween('created_at', [$inicioMesUtc, $finMesUtc])
            ->whereIn('estado', $estadosVenta)
            ->sum('total');


        /*
        |--------------------------------------------------------------------------
        | PEDIDOS DE LA FECHA
        |--------------------------------------------------------------------------
        */
        $ultimosPedidos = Pedido::with([
            'user',
            'asignacionDelivery.delivery'
        ])
            ->whereBetween('created_at', [$inicioUtc, $finUtc])
            ->latest()
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | GRÁFICO DE 7 DÍAS TERMINANDO EN LA FECHA SELECCIONADA
        |--------------------------------------------------------------------------
        */
        $inicioGrafico = $fechaSeleccionada->copy()->subDays(6);

        [$inicioGraficoUtc, $finGraficoUtc] = [
            $inicioGrafico->copy()->startOfDay()->utc(),
            $fechaSeleccionada->copy()->endOfDay()->utc(),
        ];

        $pedidosGrafico = Pedido::query()
            ->select(['created_at', 'total'])
            ->whereBetween('created_at', [$inicioGraficoUtc, $finGraficoUtc])
            ->whereIn('estado', $estadosVenta)
            ->get();

        $ventasSemana = collect(range(6, 0))
            ->map(function (int $diasAtras) use (
                $fechaSeleccionada,
                $pedidosGrafico
            ) {
                $fecha = $fechaSeleccionada
                    ->copy()
                    ->subDays($diasAtras);

                $total = $pedidosGrafico
                    ->filter(function ($pedido) use ($fecha) {
                        return $pedido->created_at
                            && $pedido->created_at
                                ->copy()
                                ->timezone(FechaFiltroService::TIMEZONE)
                                ->isSameDay($fecha);
                    })
                    ->sum(fn ($pedido) => (float) $pedido->total);

                return [
                    'fecha' => $fecha->format('d/m'),
                    'total' => round($total, 2),
                ];
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | COMPROBANTES EN REVISIÓN
        |--------------------------------------------------------------------------
        |
        | Es un indicador operativo actual, por eso no se filtra por fecha.
        |--------------------------------------------------------------------------
        */
        $comprobantesEnRevision = ComprobantePago::where(
            'estado',
            'en_revision'
        )->count();


        /*
        |--------------------------------------------------------------------------
        | COLA ACTUAL DE DELIVERY
        |--------------------------------------------------------------------------
        */
        $pedidosEnColaDelivery = $fechaSeleccionada->isSameDay($hoy)
            ? Pedido::where('estado', 'listo')
                ->whereDoesntHave('asignacionDelivery')
                ->count()
            : 0;


        /*
        |--------------------------------------------------------------------------
        | RESUMEN ECONÓMICO DE DELIVERY DEL DÍA
        |--------------------------------------------------------------------------
        |
        | La fecha económica es la fecha en que se completó la entrega.
        | En el flujo actual se conserva en updated_at de la asignación,
        | actualizado justo al marcar el pedido como entregado.
        |--------------------------------------------------------------------------
        */
        $entregasFinalizadas = AsignacionDelivery::query()
            ->whereBetween('updated_at', [$inicioUtc, $finUtc])
            ->whereHas('pedido', function ($query) {
                $query->where('estado', 'entregado');
            })
            ->with([
                'pedido:id,tarifa_delivery,monto_delivery,monto_restaurante_delivery'
            ])
            ->get();

        $ingresosDelivery = round(
            $entregasFinalizadas->sum(
                fn ($asignacion) => (float) ($asignacion->pedido?->tarifa_delivery ?? 0)
            ),
            2
        );

        $comisionesDelivery = round(
            $entregasFinalizadas->sum(
                fn ($asignacion) => (float) ($asignacion->pedido?->monto_delivery ?? 0)
            ),
            2
        );

        $parteRestauranteDelivery = round(
            $entregasFinalizadas->sum(
                fn ($asignacion) => (float) ($asignacion->pedido?->monto_restaurante_delivery ?? 0)
            ),
            2
        );

        $entregasDeliveryDia = $entregasFinalizadas->count();


        return view(
            'admin.dashboard.index',
            compact(
                'pedidos',
                'clientes',
                'deliverys',
                'productos',
                'pedidosPagados',
                'pedidosPreparando',
                'pedidosListos',
                'pedidosAsignados',
                'pedidosEnCamino',
                'pedidosEntregados',
                'pedidosCancelados',
                'ventasDia',
                'ventasMes',
                'ultimosPedidos',
                'ventasSemana',
                'comprobantesEnRevision',
                'pedidosEnColaDelivery',
                'ingresosDelivery',
                'comisionesDelivery',
                'parteRestauranteDelivery',
                'entregasDeliveryDia',
                'fechaSeleccionada'
            )
        );
    }
}
