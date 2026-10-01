<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\AsignacionDelivery;
use App\Models\Pedido;
use App\Services\AsignarPedidoDeliveryService;
use App\Services\FechaFiltroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(
        Request $request,
        AsignarPedidoDeliveryService $asignador,
        FechaFiltroService $fechas
    ) {
        $deliveryId = Auth::id();

        if (!$deliveryId) {
            return redirect()
                ->route('login')
                ->with('error', 'Debe iniciar sesión.');
        }

        $delivery = Auth::user();

        if (
            !$delivery ||
            (int) $delivery->role_id !== 3 ||
            $delivery->estado !== 'activo'
        ) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        /*
         * Si queda un Delivery libre, el sistema procesa la cola automáticamente.
         */
        $asignador->procesarCola();

        $hoy = $fechas->hoy();
        $fechaSeleccionada = $fechas->resolver($request);

        /*
         * Pedidos en cola y pedidos actualmente asignados son indicadores
         * operativos del momento actual.
         */
        $pedidosEnCola = Pedido::where('estado', 'listo')
            ->whereDoesntHave('asignacionDelivery')
            ->count();

        $misPedidos = AsignacionDelivery::where(
            'delivery_id',
            $deliveryId
        )
            ->whereHas('pedido', function ($query) {
                $query->whereIn('estado', [
                    'asignado',
                    'en_camino',
                ]);
            })
            ->count();

        /*
         * Historial económico de los últimos 14 días.
         *
         * Para una entrega finalizada, updated_at de la asignación representa
         * el momento en que el Delivery la marcó como entregada.
         */
        $inicioHistorial = $hoy->copy()->subDays(13);
        [$inicioHistorialUtc, $finHistorialUtc] = [
            $inicioHistorial->copy()->startOfDay()->utc(),
            $hoy->copy()->endOfDay()->utc(),
        ];

        $entregasHistorial = AsignacionDelivery::query()
            ->where('delivery_id', $deliveryId)
            ->whereHas('pedido', function ($query) {
                $query->where('estado', 'entregado');
            })
            ->whereBetween(
                'updated_at',
                [$inicioHistorialUtc, $finHistorialUtc]
            )
            ->with([
                'pedido:id,distancia_delivery_km,tarifa_delivery,porcentaje_delivery,monto_delivery,porcentaje_restaurante_delivery,monto_restaurante_delivery',
            ])
            ->orderByDesc('updated_at')
            ->get();

        $entregasDiaSeleccionado = $entregasHistorial
            ->filter(function ($asignacion) use ($fechaSeleccionada) {
                return $asignacion->updated_at
                    && $asignacion->updated_at
                        ->copy()
                        ->timezone(FechaFiltroService::TIMEZONE)
                        ->isSameDay($fechaSeleccionada);
            })
            ->values();

        $comisionDia = round(
            $entregasDiaSeleccionado->sum(
                fn ($asignacion) => (float) (
                    $asignacion->pedido?->monto_delivery ?? 0
                )
            ),
            2
        );

        $parteRestauranteDia = round(
            $entregasDiaSeleccionado->sum(
                fn ($asignacion) => (float) (
                    $asignacion->pedido?->monto_restaurante_delivery ?? 0
                )
            ),
            2
        );

        $tarifaDeliveryDia = round(
            $entregasDiaSeleccionado->sum(
                fn ($asignacion) => (float) (
                    $asignacion->pedido?->tarifa_delivery ?? 0
                )
            ),
            2
        );

        $entregasDia = $entregasDiaSeleccionado->count();

        /*
         * Resumen para la barra de fechas.
         */


        return view(
            'delivery.dashboard.index',
            compact(
                'pedidosEnCola',
                'misPedidos',
                'delivery',
                'comisionDia',
                'parteRestauranteDia',
                'tarifaDeliveryDia',
                'entregasDia',
                'fechaSeleccionada',
                'entregasDiaSeleccionado'
            )
        );
    }
}
