<?php

namespace App\Services;

use App\Models\AsignacionDelivery;
use App\Models\Notificacion;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AsignarPedidoDeliveryService
{
    /**
     * Procesar automáticamente la cola de pedidos listos.
     *
     * Reglas principales:
     *
     * 1. La cola es FIFO usando created_at del pedido.
     * 2. Solo se consideran Delivery activos y libres.
     * 3. Un Delivery ocupado no recibe otro pedido activo.
     * 4. El Delivery no elige pedidos manualmente.
     * 5. La operación se protege con transacción y bloqueos.
     * 6. Si un Delivery cancela una asignación, puede excluirse de la
     *    reasignación inmediata y durante un periodo de enfriamiento.
     */
    public function procesarCola(?int $excluirDeliveryId = null): int
    {
        $asignados = 0;

        DB::transaction(function () use (&$asignados, $excluirDeliveryId): void {
            $deliverysLibres = User::query()
                ->where('role_id', 3)
                ->where('estado', 'activo')
                ->when(
                    $excluirDeliveryId !== null,
                    fn ($query) => $query->where('id', '!=', $excluirDeliveryId)
                )
                ->whereDoesntHave('asignacionesDelivery', function ($query) {
                    $query->whereHas('pedido', function ($pedidoQuery) {
                        $pedidoQuery->whereIn('estado', [
                            'asignado',
                            'en_camino',
                        ]);
                    });
                })
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($deliverysLibres->isEmpty()) {
                return;
            }

            // Balancear por los kilómetros entregados hoy en Bolivia.
            // La cola de pedidos se mantiene FIFO; solo se ordenan los
            // repartidores disponibles por su carga acumulada del día.
            $fechaFiltro = app(FechaFiltroService::class);
            [$inicioDia, $finDia] = $fechaFiltro->rangoUtc($fechaFiltro->hoy());

            $distanciasAcumuladas = AsignacionDelivery::query()
                ->join('pedidos', 'pedidos.id', '=', 'asignaciones_delivery.pedido_id')
                ->where('pedidos.estado', 'entregado')
                ->whereBetween('asignaciones_delivery.created_at', [$inicioDia, $finDia])
                ->selectRaw(
                    'asignaciones_delivery.delivery_id, COALESCE(SUM(pedidos.distancia_delivery_km), 0) AS distancia_acumulada'
                )
                ->groupBy('asignaciones_delivery.delivery_id')
                ->pluck('distancia_acumulada', 'asignaciones_delivery.delivery_id');

            $deliverysLibres = $deliverysLibres
                ->sort(function (User $a, User $b) use ($distanciasAcumuladas): int {
                    $distanciaA = (float) ($distanciasAcumuladas[$a->id] ?? 0);
                    $distanciaB = (float) ($distanciasAcumuladas[$b->id] ?? 0);

                    return ($distanciaA <=> $distanciaB)
                        ?: ($a->id <=> $b->id);
                })
                ->values();

            foreach ($deliverysLibres as $delivery) {
                /*
                 * Se obtiene la cola estrictamente por fecha de creación.
                 *
                 * created_at es la única referencia utilizada para mantener
                 * el orden FIFO de la cola.
                 */
                $pedidosEnCola = Pedido::query()
                    ->where('estado', 'listo')
                    ->whereDoesntHave('asignacionDelivery', function ($query) {
                        $query->whereHas('pedido', function ($pedidoQuery) {
                            $pedidoQuery->whereIn('estado', [
                                'asignado',
                                'en_camino',
                            ]);
                        });
                    })
                    ->orderBy('created_at', 'asc')
                    ->orderBy('id', 'asc')
                    ->lockForUpdate()
                    ->get();

                /*
                 * FIFO estricto:
                 * el candidato siempre intenta recibir el pedido más antiguo.
                 *
                 * Si ese pedido fue cancelado recientemente por este mismo
                 * Delivery, no se le asigna un pedido posterior. El pedido
                 * cancelado debe esperar hasta que otro Delivery pueda tomarlo.
                 *
                 * Esto no modifica la base de datos.
                 */
                $pedido = $pedidosEnCola->first();

                if (!$pedido) {
                    continue;
                }

                $bloqueadoPorCancelacion = Cache::has(
                    'delivery_cancelled:' . $delivery->id . ':' . $pedido->id
                );

                if ($bloqueadoPorCancelacion) {
                    continue;
                }

                $tienePedidoActivo = AsignacionDelivery::query()
                    ->where('delivery_id', $delivery->id)
                    ->whereHas('pedido', function ($query) {
                        $query->whereIn('estado', [
                            'asignado',
                            'en_camino',
                        ]);
                    })
                    ->exists();

                if ($tienePedidoActivo) {
                    continue;
                }

                AsignacionDelivery::create([
                    'pedido_id' => $pedido->id,
                    'delivery_id' => $delivery->id,
                ]);

                $pedido->update([
                    'estado' => 'asignado',
                ]);

                Notificacion::create([
                    'user_id' => $delivery->id,
                    'pedido_id' => $pedido->id,
                    'mensaje' => 'Se te asignó automáticamente el pedido #' . $pedido->id . '.',
                    'tipo' => 'delivery',
                    'evento' => 'pedido_asignado_delivery',
                    'leido' => false,
                ]);

                Notificacion::create([
                    'user_id' => $pedido->user_id,
                    'pedido_id' => $pedido->id,
                    'mensaje' => 'Tu pedido #' . $pedido->id . ' fue asignado a un Delivery y está listo para salir.',
                    'tipo' => 'cliente',
                    'evento' => 'pedido_asignado_delivery',
                    'leido' => false,
                ]);

                $asignados++;
            }
        });

        return $asignados;
    }
}
