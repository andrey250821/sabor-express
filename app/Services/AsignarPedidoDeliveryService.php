<?php

namespace App\Services;

use App\Models\AsignacionDelivery;
use App\Models\Notificacion;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AsignarPedidoDeliveryService
{
    /**
     * Procesar la cola FIFO de pedidos listos.
     *
     * Mientras existan pedidos en cola y Deliverys libres:
     * - toma siempre el pedido más antiguo según fecha_listo;
     * - toma un Delivery activo sin pedido activo;
     * - crea la asignación automáticamente;
     * - marca el pedido como asignado.
     *
     * La transacción y los bloqueos evitan que dos procesos asignen
     * simultáneamente el mismo pedido o el mismo Delivery.
     */
    public function procesarCola(): int
    {
        $asignados = 0;

        DB::transaction(function () use (&$asignados): void {
            $deliverysLibres = User::query()
                ->where('role_id', 3)
                ->where('estado', 'activo')
                ->whereDoesntHave('asignacionesDelivery', function ($query) {
                    $query->whereIn('estado', ['aceptado', 'en_camino']);
                })
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($deliverysLibres as $delivery) {
                $pedido = Pedido::query()
                    ->where('estado', 'listo')
                    ->whereDoesntHave('asignacionDelivery')
                    ->orderByRaw('fecha_listo IS NULL')
                    ->orderBy('fecha_listo', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->orderBy('id', 'asc')
                    ->lockForUpdate()
                    ->first();

                if (!$pedido) {
                    break;
                }

                AsignacionDelivery::create([
                    'pedido_id' => $pedido->id,
                    'delivery_id' => $delivery->id,
                    'estado' => 'aceptado',
                    'fecha_asignacion' => now(),
                    // La asignación es automática, por lo tanto la
                    // respuesta se registra inmediatamente.
                    'fecha_respuesta' => now(),
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
