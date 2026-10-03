<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\AsignacionDelivery;
use App\Models\Notificacion;
use App\Models\Pedido;
use App\Services\AsignarPedidoDeliveryService;
use App\Services\FechaFiltroService;
use App\Services\FirebaseDeliveryLocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PedidoController extends Controller
{
    /**
     * Verificar que el usuario actual sea un Delivery activo.
     */
    private function verificarDelivery()
    {
        $delivery = Auth::user();

        if (!$delivery) {
            return redirect()
                ->route('login')
                ->with('error', 'Debe iniciar sesión.');
        }

        if (
            (int) $delivery->role_id !== 3 ||
            $delivery->estado !== 'activo'
        ) {
            abort(403, 'No tienes permisos para acceder como Delivery.');
        }

        return $delivery;
    }

    /**
     * Mostrar únicamente la cantidad de pedidos que permanecen en cola.
     *
     * Los datos del pedido no se exponen mientras no exista una asignación.
     */
    public function index(): View|RedirectResponse
    {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof RedirectResponse) {
            return $delivery;
        }

        $pedidosEnCola = Pedido::query()
            ->where('estado', 'listo')
            ->whereDoesntHave('asignacionDelivery')
            ->count();

        return view(
            'delivery.pedidos.index',
            compact('pedidosEnCola')
        );
    }

    /**
     * Mostrar un pedido asignado.
     *
     * Seguridad de flujo:
     * - ASIGNADO: no se muestran detalles del cliente, dirección,
     *   productos, distancia ni importes.
     * - EN_CAMINO / ENTREGADO: recién aquí se cargan los detalles completos.
     */
    public function show(int $id): View|RedirectResponse
    {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof RedirectResponse) {
            return $delivery;
        }

        $asignacion = AsignacionDelivery::query()
            ->where('pedido_id', $id)
            ->where('delivery_id', $delivery->id)
            ->with([
                'pedido:id,user_id,total,estado,fecha_listo',
            ])
            ->firstOrFail();

        $pedido = $asignacion->pedido;

        if (!$pedido) {
            abort(404, 'El pedido ya no existe.');
        }

        if ($pedido->estado === 'asignado') {
            return view(
                'delivery.pedidos.asignado',
                compact('pedido', 'asignacion')
            );
        }

        $pedido->load([
            'user',
            'detallePedidos.producto',
        ]);

        return view(
            'delivery.pedidos.show',
            compact('pedido')
        );
    }

    /**
     * MIS PEDIDOS
     *
     * El filtro superior de fecha es la única referencia temporal de esta vista.
     * No se vuelve a agrupar ni filtrar manualmente por fechas dentro de la vista.
     */
    public function misPedidos(
        Request $request,
        FechaFiltroService $fechas
    ): View|RedirectResponse {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof RedirectResponse) {
            return $delivery;
        }

        $fechaSeleccionada = $fechas->resolver($request);

        [$inicioUtc, $finUtc] = $fechas->rangoUtc($fechaSeleccionada);

        $asignaciones = AsignacionDelivery::query()
            ->with([
                'pedido:id,user_id,total,estado,fecha_listo,direccion_entrega,referencia_delivery,observacion_cliente,tarifa_delivery,distancia_delivery_km,porcentaje_delivery,monto_delivery,porcentaje_restaurante_delivery,monto_restaurante_delivery,created_at',
            ])
            ->where('delivery_id', $delivery->id)
            ->where(function ($query) use ($inicioUtc, $finUtc) {
                $query
                    ->whereBetween('created_at', [$inicioUtc, $finUtc])
                    ->orWhereBetween('updated_at', [$inicioUtc, $finUtc]);
            })
            ->orderByDesc('updated_at')
            ->get();

        // Cargar información sensible solo cuando ya puede consultarse.
        $asignaciones->each(function (AsignacionDelivery $asignacion): void {
            if (
                $asignacion->pedido &&
                in_array($asignacion->pedido->estado, [
                    'en_camino',
                    'entregado',
                ], true)
            ) {
                $asignacion->pedido->load([
                    'user',
                    'detallePedidos.producto',
                ]);
            }
        });

        return view(
            'delivery.pedidos.mis',
            compact(
                'asignaciones',
                'fechaSeleccionada'
            )
        );
    }

    /**
     * INICIAR ENTREGA
     *
     * ASIGNADO -> EN_CAMINO
     *
     * Desde este momento el Delivery puede consultar los detalles
     * completos del pedido y se activa el GPS del seguimiento.
     */
    public function iniciar(int $id): RedirectResponse
    {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof RedirectResponse) {
            return $delivery;
        }

        try {
            DB::transaction(function () use ($id, $delivery): void {
                $asignacion = AsignacionDelivery::query()
                    ->where('pedido_id', $id)
                    ->where('delivery_id', $delivery->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $pedido = Pedido::query()
                    ->where('id', $id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($pedido->estado !== 'asignado') {
                    throw new \RuntimeException(
                        'El pedido ya no se encuentra asignado y no puede iniciarse.'
                    );
                }

                $pedido->update([
                    'estado' => 'en_camino',
                ]);

                $asignacion->touch();

                Notificacion::create([
                    'user_id' => $pedido->user_id,
                    'pedido_id' => $pedido->id,
                    'mensaje' => 'Tu pedido #' . $pedido->id . ' ya está en camino con nuestro Delivery.',
                    'tipo' => 'cliente',
                    'evento' => 'pedido_en_camino',
                    'leido' => false,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('delivery.pedidos.show', $id)
            ->with(
                'success',
                'Entrega iniciada. Ahora puedes consultar los detalles del pedido y realizar el recorrido.'
            );
    }

    /**
     * CANCELAR ASIGNACIÓN
     *
     * Solo está permitido mientras el pedido permanezca ASIGNADO.
     *
     * Al cancelar:
     * - se elimina la asignación actual;
     * - el pedido vuelve a LISTO;
     * - conserva su fecha_listo para mantener su posición FIFO;
     * - el sistema intenta asignarlo automáticamente a otro Delivery libre;
     * - el Delivery que canceló queda excluido de esa ejecución para
     *   evitar que reciba inmediatamente el mismo pedido.
     */
    public function cancelar(
        int $id,
        AsignarPedidoDeliveryService $asignador,
        FirebaseDeliveryLocationService $firebaseLocation
    ): RedirectResponse {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof RedirectResponse) {
            return $delivery;
        }

        try {
            DB::transaction(function () use ($id, $delivery): void {
                $asignacion = AsignacionDelivery::query()
                    ->where('pedido_id', $id)
                    ->where('delivery_id', $delivery->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $pedido = Pedido::query()
                    ->where('id', $id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($pedido->estado !== 'asignado') {
                    throw new \RuntimeException(
                        'Solo puedes cancelar un pedido mientras está asignado. Una entrega iniciada ya no puede cancelarse.'
                    );
                }

                $pedido->update([
                    'estado' => 'listo',
                ]);

                $asignacion->delete();
            });

            // Limpieza preventiva por si existiera una ubicación anterior.
            $firebaseLocation->eliminarPorPedido($id);

            $asignador->procesarCola($delivery->id);

            return redirect()
                ->route('delivery.pedidos.mis')
                ->with(
                    'success',
                    'Pedido #' . $id . ' cancelado. Volvió a la cola y se buscará automáticamente otro Delivery disponible.'
                );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * MARCAR COMO ENTREGADO
     *
     * EN_CAMINO -> ENTREGADO
     *
     * Después de la entrega, el Delivery queda libre y el sistema
     * procesa automáticamente el siguiente pedido de la cola.
     */
    public function entregar(
        int $id,
        AsignarPedidoDeliveryService $asignador,
        FirebaseDeliveryLocationService $firebaseLocation
    ): RedirectResponse {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof RedirectResponse) {
            return $delivery;
        }

        try {
            DB::transaction(function () use ($id, $delivery): void {
                $asignacion = AsignacionDelivery::query()
                    ->where('pedido_id', $id)
                    ->where('delivery_id', $delivery->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $pedido = Pedido::query()
                    ->where('id', $id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($pedido->estado !== 'en_camino') {
                    throw new \RuntimeException(
                        'El pedido ya no se encuentra en camino.'
                    );
                }

                $pedido->update([
                    'estado' => 'entregado',
                ]);

                $asignacion->touch();

                Notificacion::create([
                    'user_id' => $pedido->user_id,
                    'pedido_id' => $pedido->id,
                    'mensaje' => 'Tu pedido #' . $pedido->id . ' fue entregado correctamente.',
                    'tipo' => 'cliente',
                    'evento' => 'pedido_entregado',
                    'leido' => false,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $firebaseLocation->eliminarPorPedido($id);

        $asignados = $asignador->procesarCola();

        if ($asignados > 0) {
            return redirect()
                ->route('delivery.pedidos.mis')
                ->with(
                    'success',
                    'Pedido #' . $id . ' marcado como entregado. Se procesó automáticamente la siguiente entrega de la cola.'
                );
        }

        return redirect()
            ->route('delivery.pedidos.mis')
            ->with(
                'success',
                'Pedido #' . $id . ' marcado como entregado correctamente. No hay más pedidos esperando en la cola.'
            );
    }
}
