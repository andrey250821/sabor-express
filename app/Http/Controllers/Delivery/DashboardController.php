<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\AsignacionDelivery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AsignarPedidoDeliveryService;

class DashboardController extends Controller
{
    public function index(
        Request $request,
        AsignarPedidoDeliveryService $asignador
    )
    {
        // Verificar que haya un usuario logueado.
        $deliveryId = Auth::id();

        if (!$deliveryId) {
            return redirect()->route('login')
                ->with('error', 'Debe iniciar sesión.');
        }

        // Verificar que el usuario sea un Delivery activo.
        $delivery = Auth::user();

        if (
            !$delivery ||
            (int) $delivery->role_id !== 3 ||
            $delivery->estado !== 'activo'
        ) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        // Procesar cualquier pedido que ya esté en cola y tenga un Delivery libre.
        $asignador->procesarCola();

        // Pedidos listos que todavía NO tienen Delivery asignado.
        $pedidosEnCola = Pedido::where('estado', 'listo')
            ->whereDoesntHave('asignacionDelivery')
            ->count();

        // Pedidos que este Delivery tiene actualmente.
        $misPedidos = AsignacionDelivery::where('delivery_id', $deliveryId)
            ->whereIn('estado', [
                'aceptado',
                'en_camino',
            ])
            ->count();

        /*
         * Historial económico de los últimos 7 días.
         *
         * La fecha de referencia es fecha_entrega, registrada cuando
         * el Delivery completa realmente el pedido.
         */
        $hoy = now()->startOfDay();
        $inicioHistorial = $hoy->copy()->subDays(6);
        $finHistorial = $hoy->copy()->endOfDay();

        $entregasUltimos7Dias = AsignacionDelivery::query()
            ->where('delivery_id', $deliveryId)
            ->where('estado', 'entregado')
            ->whereBetween('fecha_entrega', [
                $inicioHistorial,
                $finHistorial,
            ])
            ->with([
                'pedido:id,distancia_delivery_km,tarifa_delivery,porcentaje_delivery,monto_delivery,porcentaje_restaurante_delivery,monto_restaurante_delivery',
            ])
            ->orderByDesc('fecha_entrega')
            ->get();

        $comisionHoy = round(
            $entregasUltimos7Dias
                ->filter(function ($asignacion) use ($hoy) {
                    return $asignacion->fecha_entrega
                        && $asignacion->fecha_entrega->isSameDay($hoy);
                })
                ->sum(fn ($asignacion) => (float) ($asignacion->pedido?->monto_delivery ?? 0)),
            2
        );

        $parteRestauranteHoy = round(
            $entregasUltimos7Dias
                ->filter(function ($asignacion) use ($hoy) {
                    return $asignacion->fecha_entrega
                        && $asignacion->fecha_entrega->isSameDay($hoy);
                })
                ->sum(fn ($asignacion) => (float) ($asignacion->pedido?->monto_restaurante_delivery ?? 0)),
            2
        );

        $entregasHoy = $entregasUltimos7Dias
            ->filter(function ($asignacion) use ($hoy) {
                return $asignacion->fecha_entrega
                    && $asignacion->fecha_entrega->isSameDay($hoy);
            })
            ->count();

        /*
         * Resumen de cada uno de los 7 días.
         * Incluye hoy y los seis días anteriores.
         */
        $historialDias = collect(range(0, 6))
            ->map(function (int $diasAtras) use ($hoy, $entregasUltimos7Dias) {
                $fecha = $hoy->copy()->subDays($diasAtras);

                $entregas = $entregasUltimos7Dias->filter(function ($asignacion) use ($fecha) {
                    return $asignacion->fecha_entrega
                        && $asignacion->fecha_entrega->isSameDay($fecha);
                });

                return [
                    'fecha' => $fecha->toDateString(),
                    'etiqueta' => $fecha->isToday()
                        ? 'Hoy'
                        : ($fecha->isYesterday() ? 'Ayer' : $fecha->translatedFormat('l d/m')),
                    'entregas' => $entregas->count(),
                    'comision' => round(
                        $entregas->sum(fn ($asignacion) => (float) ($asignacion->pedido?->monto_delivery ?? 0)),
                        2
                    ),
                    'restaurante' => round(
                        $entregas->sum(fn ($asignacion) => (float) ($asignacion->pedido?->monto_restaurante_delivery ?? 0)),
                        2
                    ),
                ];
            });

        /*
         * Día que se quiere consultar desde el historial.
         * Solo se permiten hoy y los seis días anteriores.
         */
        $fechaHistorial = null;

        if ($request->filled('fecha')) {
            try {
                $fechaSolicitada = Carbon::createFromFormat(
                    'Y-m-d',
                    (string) $request->input('fecha')
                )->startOfDay();

                if (
                    $fechaSolicitada->greaterThanOrEqualTo($inicioHistorial) &&
                    $fechaSolicitada->lessThanOrEqualTo($hoy)
                ) {
                    $fechaHistorial = $fechaSolicitada;
                }
            } catch (\Throwable) {
                $fechaHistorial = null;
            }
        }

        if (!$fechaHistorial) {
            $fechaHistorial = $hoy->copy();
        }

        $entregasDiaSeleccionado = $entregasUltimos7Dias
            ->filter(function ($asignacion) use ($fechaHistorial) {
                return $asignacion->fecha_entrega
                    && $asignacion->fecha_entrega->isSameDay($fechaHistorial);
            })
            ->values();

        $comisionDiaSeleccionado = round(
            $entregasDiaSeleccionado
                ->sum(fn ($asignacion) => (float) ($asignacion->pedido?->monto_delivery ?? 0)),
            2
        );

        $parteRestauranteDiaSeleccionado = round(
            $entregasDiaSeleccionado
                ->sum(fn ($asignacion) => (float) ($asignacion->pedido?->monto_restaurante_delivery ?? 0)),
            2
        );

        $entregasFinalizadas = AsignacionDelivery::query()
            ->where('delivery_id', $deliveryId)
            ->where('estado', 'entregado')
            ->with([
                'pedido:id,distancia_delivery_km,tarifa_delivery,monto_delivery,monto_restaurante_delivery,porcentaje_delivery,porcentaje_restaurante_delivery',
            ])
            ->get();

        $pedidosEntregados = $entregasFinalizadas->count();

        return view(
            'delivery.dashboard.index',
            compact(
                'pedidosEnCola',
                'misPedidos',
                'pedidosEntregados',
                'delivery',
                'comisionHoy',
                'parteRestauranteHoy',
                'entregasHoy',
                'historialDias',
                'fechaHistorial',
                'entregasDiaSeleccionado',
                'comisionDiaSeleccionado',
                'parteRestauranteDiaSeleccionado',
                'entregasFinalizadas'
            )
        );
    }
}
