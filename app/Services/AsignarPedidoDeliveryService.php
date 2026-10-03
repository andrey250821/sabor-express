<?php

namespace App\Services;

use App\Models\AsignacionDelivery;
use App\Models\Notificacion;
use App\Models\Pedido;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AsignarPedidoDeliveryService
{
    /**
     * Procesar automáticamente la cola de pedidos listos.
     *
     * Reglas:
     *
     * 1. La cola de pedidos siempre es FIFO:
     *    se toma el pedido que pasó primero a "listo".
     *
     * 2. Solo se consideran Delivery activos y libres.
     *
     * 3. Entre los Delivery libres se busca equilibrar el trabajo.
     *    Para ello, primero se considera la distancia total entregada
     *    durante el día de negocio actual; quien tenga menor distancia
     *    acumulada recibe prioridad.
     *
     *    En caso de empate se utiliza:
     *    - menor cantidad de entregas realizadas hoy;
     *    - menor distancia de la última entrega;
     *    - quien lleva más tiempo sin actividad;
     *    - ID del Delivery como desempate final.
     *
     * 4. Un Delivery ocupado no puede recibir un segundo pedido activo.
     *
     * 5. La operación se ejecuta dentro de una transacción y bloquea
     *    los registros necesarios para reducir el riesgo de asignaciones
     *    duplicadas cuando coinciden varias peticiones.
     *
     * Mientras existan pedidos en cola y Deliverys libres, se asignará
     * exactamente un pedido a cada Delivery libre de esta ejecución.
     */
    public function procesarCola(): int
    {
        $asignados = 0;

        DB::transaction(function () use (&$asignados): void {
            // 1. Bloquear primero a los Delivery activos y libres.
            $deliverysLibres = User::query()
                ->where('role_id', 3)
                ->where('estado', 'activo')
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

            // 2. Determinar el día de negocio actual en Bolivia.
            $fechaFiltro = app(FechaFiltroService::class);
            $hoy = $fechaFiltro->hoy();

            [$inicioUtc, $finUtc] = $fechaFiltro->rangoUtc($hoy);

            /*
             * 3. Cargar las entregas completadas hoy de los Delivery libres.
             *
             * Para una asignación entregada, updated_at representa el momento
             * en que finalizó la entrega porque Delivery/PedidoController
             * hace touch() al completar el pedido.
             */
            $asignacionesEntregadasHoy = AsignacionDelivery::query()
                ->whereIn('delivery_id', $deliverysLibres->pluck('id'))
                ->whereBetween('updated_at', [$inicioUtc, $finUtc])
                ->whereHas('pedido', function ($query) {
                    $query->where('estado', 'entregado');
                })
                ->with([
                    'pedido:id,distancia_delivery_km',
                ])
                ->orderByDesc('updated_at')
                ->get()
                ->groupBy('delivery_id');

            /*
             * 4. Calcular la carga de cada Delivery libre.
             */
            $candidatos = $deliverysLibres
                ->map(function (User $delivery) use ($asignacionesEntregadasHoy) {
                    $entregas = $asignacionesEntregadasHoy->get(
                        $delivery->id,
                        collect()
                    );

                    $distanciaAcumulada = round(
                        $entregas->sum(
                            fn ($asignacion) => (float) (
                                $asignacion->pedido?->distancia_delivery_km ?? 0
                            )
                        ),
                        2
                    );

                    $cantidadEntregas = $entregas->count();

                    $ultimaEntrega = $entregas->sortByDesc(
                        'updated_at'
                    )->first();

                    $ultimaDistancia = $ultimaEntrega
                        ? round(
                            (float) (
                                $ultimaEntrega->pedido?->distancia_delivery_km ?? 0
                            ),
                            2
                        )
                        : 0.0;

                    $ultimaActividad = $ultimaEntrega?->updated_at;

                    return [
                        'delivery' => $delivery,
                        'distancia_acumulada' => $distanciaAcumulada,
                        'cantidad_entregas' => $cantidadEntregas,
                        'ultima_distancia' => $ultimaDistancia,
                        'ultima_actividad' => $ultimaActividad,
                    ];
                })
                ->sort(function (array $a, array $b): int {
                    $comparacion = $a['distancia_acumulada']
                        <=> $b['distancia_acumulada'];

                    if ($comparacion !== 0) {
                        return $comparacion;
                    }

                    $comparacion = $a['cantidad_entregas']
                        <=> $b['cantidad_entregas'];

                    if ($comparacion !== 0) {
                        return $comparacion;
                    }

                    $comparacion = $a['ultima_distancia']
                        <=> $b['ultima_distancia'];

                    if ($comparacion !== 0) {
                        return $comparacion;
                    }

                    /*
                     * null significa que el Delivery no tuvo actividad hoy,
                     * por lo que debe tener prioridad frente a uno con actividad.
                     */
                    $actividadA = $a['ultima_actividad'];
                    $actividadB = $b['ultima_actividad'];

                    if ($actividadA === null && $actividadB !== null) {
                        return -1;
                    }

                    if ($actividadA !== null && $actividadB === null) {
                        return 1;
                    }

                    if ($actividadA !== null && $actividadB !== null) {
                        $comparacion = Carbon::parse($actividadA)
                            ->getTimestamp()
                            <=> Carbon::parse($actividadB)
                                ->getTimestamp();

                        if ($comparacion !== 0) {
                            return $comparacion;
                        }
                    }

                    return $a['delivery']->id <=> $b['delivery']->id;
                })
                ->values();

            /*
             * 5. Mantener FIFO estricto según el momento en que cocina
             * terminó cada pedido.
             *
             * fecha_listo representa cuándo el pedido pasó a LISTO.
             * Para registros históricos que aún no tengan esa fecha,
             * usamos created_at como respaldo.
             */
            foreach ($candidatos as $candidato) {
                $pedido = Pedido::query()
                    ->where('estado', 'listo')
                    ->whereDoesntHave('asignacionDelivery')
                    ->orderByRaw('COALESCE(fecha_listo, created_at) ASC')
                    ->orderBy('id', 'asc')
                    ->lockForUpdate()
                    ->first();

                if (!$pedido) {
                    break;
                }

                $delivery = $candidato['delivery'];

                // Defensa adicional dentro de la transacción.
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

                // Aviso al Delivery.
                Notificacion::create([
                    'user_id' => $delivery->id,
                    'pedido_id' => $pedido->id,
                    'mensaje' => 'Se te asignó automáticamente el pedido #' . $pedido->id . '.',
                    'tipo' => 'delivery',
                    'evento' => 'pedido_asignado_delivery',
                    'leido' => false,
                ]);

                // Aviso al cliente.
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
