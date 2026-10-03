<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\AsignacionDelivery;
use App\Models\Notificacion;
use App\Models\Pedido;
use App\Services\AsignarPedidoDeliveryService;
use App\Services\FirebaseDeliveryLocationService;
use App\Services\FechaFiltroService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
     * COLA DE PEDIDOS
     *
     * El Delivery no puede elegir pedidos ni consultar sus datos.
     * La cola solo muestra la cantidad de pedidos que esperan asignación.
     */
    public function index(
        AsignarPedidoDeliveryService $asignador
    ): View|RedirectResponse {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof RedirectResponse) {
            return $delivery;
        }

        // Mantener la cola procesada cuando el Delivery entra al módulo.
        $asignador->procesarCola();

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
            ->count();

        return view(
            'delivery.pedidos.index',
            compact('pedidosEnCola')
        );
    }

    /**
     * DETALLE DE UN PEDIDO
     *
     * El detalle solamente se habilita cuando la entrega ya está
     * en camino o cuando fue completada.
     *
     * Mientras el pedido está únicamente ASIGNADO, el Delivery no
     * puede abrir sus detalles: primero debe iniciar la entrega.
     */
    public function show(int $id): View|RedirectResponse
    {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof RedirectResponse) {
            return $delivery;
        }

        $pedido = Pedido::query()
            ->whereHas('asignacionDelivery', function ($query) use ($delivery) {
                $query->where('delivery_id', $delivery->id);
            })
            ->with([
                'user',
                'detallePedidos.producto',
                'asignacionDelivery',
            ])
            ->findOrFail($id);

        if ($pedido->estado === 'asignado') {
            return redirect()
                ->route('delivery.pedidos.mis')
                ->with(
                    'error',
                    'Los detalles del pedido estarán disponibles después de iniciar la entrega.'
                );
        }

        return view(
            'delivery.pedidos.show',
            compact('pedido')
        );
    }

    /**
     * MIS PEDIDOS
     *
     * La única fecha utilizada por este módulo es la seleccionada
     * en el filtro superior.
     *
     * La búsqueda por Gmail se maneja también en el servidor para
     * mantener el funcionamiento aun si JavaScript estuviera desactivado.
     */
    public function misPedidos(
        Request $request,
        FechaFiltroService $fechas,
        AsignarPedidoDeliveryService $asignador
    ): View|RedirectResponse {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof RedirectResponse) {
            return $delivery;
        }

        // Reprocesar la cola puede asignar automáticamente el siguiente pedido.
        $asignador->procesarCola();

        $fechaSeleccionada = $fechas->resolver($request);

        [$inicioUtc, $finUtc] = $fechas->rangoUtc(
            $fechaSeleccionada
        );

        $busquedaGmail = trim((string) $request->input('buscar', ''));

        $asignaciones = AsignacionDelivery::query()
            ->with([
                'pedido.user',
                'pedido.detallePedidos.producto',
            ])
            ->where('delivery_id', $delivery->id)
            ->where(function ($query) {
                $query
                    ->whereHas('pedido', function ($pedidoQuery) {
                        $pedidoQuery->whereIn('estado', [
                            'asignado',
                            'en_camino',
                            'entregado',
                        ]);
                    });
            })
            ->where(function ($query) use ($inicioUtc, $finUtc) {
                $query
                    ->whereBetween('created_at', [$inicioUtc, $finUtc])
                    ->orWhereBetween('updated_at', [$inicioUtc, $finUtc]);
            })
            ->when($busquedaGmail !== '', function ($query) use ($busquedaGmail) {
                $query->whereHas('pedido.user', function ($userQuery) use ($busquedaGmail) {
                    $userQuery->where('email', 'like', '%' . $busquedaGmail . '%');
                });
            })
            ->orderByDesc('updated_at')
            ->get();

        return view(
            'delivery.pedidos.mis',
            compact(
                'asignaciones',
                'fechaSeleccionada',
                'busquedaGmail'
            )
        );
    }

    /**
     * INICIAR ENTREGA
     *
     * asignado -> en_camino
     *
     * Desde este momento los detalles del pedido quedan disponibles
     * y el GPS puede comenzar a funcionar.
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

            return back()->with(
                'success',
                'Entrega iniciada correctamente. Ahora puedes consultar todos los detalles del pedido.'
            );
        } catch (\Throwable $e) {
            return back()->with(
                'error',
                $e instanceof \RuntimeException
                    ? $e->getMessage()
                    : 'No fue posible iniciar la entrega.'
            );
        }
    }

    /**
     * CANCELAR ASIGNACIÓN
     *
     * Solo está permitido mientras el pedido permanece ASIGNADO.
     *
     * El pedido vuelve a LISTO y la asignación actual se elimina para
     * devolverlo a la cola. El Delivery que canceló queda excluido de
     * la reasignación inmediata y por un periodo de enfriamiento.
     *
     * No se puede cancelar un pedido EN CAMINO.
     */
    public function cancelar(
        int $id,
        AsignarPedidoDeliveryService $asignador
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
                        'Este pedido ya inició su entrega y no puede cancelarse.'
                    );
                }

                $pedido->update([
                    'estado' => 'listo',
                ]);

                $asignacion->delete();
            });

            /*
             * Bloqueo temporal fuera de la BD:
             * evita que el mismo Delivery reciba inmediatamente el pedido
             * que acaba de cancelar.
             */
            Cache::put(
                'delivery_cancelled:' . $delivery->id . ':' . $id,
                true,
                now()->addHours(24)
            );

            // Reasignar automáticamente a otro Delivery libre.
            $asignador->procesarCola($delivery->id);

            return back()->with(
                'success',
                'Pedido #' . $id . ' cancelado. Volvió a la cola y será asignado automáticamente a otro Delivery disponible.'
            );
        } catch (\Throwable $e) {
            return back()->with(
                'error',
                $e instanceof \RuntimeException
                    ? $e->getMessage()
                    : 'No fue posible cancelar la asignación.'
            );
        }
    }

    /**
     * MARCAR COMO ENTREGADO
     *
     * en_camino -> entregado
     *
     * Después de completar la entrega, el Delivery queda libre y
     * el sistema procesa la siguiente orden de la cola.
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

                // updated_at de la asignación representa la finalización.
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

            $firebaseLocation->eliminarPorPedido($id);

            $asignador->procesarCola();

            return back()->with(
                'success',
                'Pedido marcado como entregado correctamente. La cola fue procesada automáticamente.'
            );
        } catch (\Throwable $e) {
            return back()->with(
                'error',
                $e instanceof \RuntimeException
                    ? $e->getMessage()
                    : 'No fue posible completar la entrega.'
            );
        }
    }
}
