<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\AsignacionDelivery;
use App\Models\Notificacion;
use App\Models\Pedido;
use App\Services\AsignarPedidoDeliveryService;
use App\Services\FirebaseDeliveryLocationService;
use Illuminate\Http\RedirectResponse;
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
     * COLA DE PEDIDOS
     *
     * El Delivery puede consultar únicamente cuántos pedidos están
     * esperando asignación. No se muestran clientes, productos,
     * direcciones, montos ni ningún otro detalle.
     *
     * La selección del pedido no la realiza el Delivery:
     * el sistema asigna automáticamente el siguiente pedido de la cola.
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
     * DETALLE DE UN PEDIDO
     *
     * Solo puede abrirse un pedido que haya sido asignado al
     * Delivery autenticado. Esto evita que pueda consultar
     * manualmente un pedido ajeno escribiendo su ID en la URL.
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
            ])
            ->findOrFail($id);

        return view(
            'delivery.pedidos.show',
            compact('pedido')
        );
    }

    /**
     * MIS PEDIDOS
     *
     * Muestra únicamente asignaciones pertenecientes al Delivery
     * autenticado. Los pedidos históricos también pueden consultarse.
     */
    public function misPedidos(): View|RedirectResponse
    {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof RedirectResponse) {
            return $delivery;
        }

        $asignaciones = AsignacionDelivery::query()
            ->with([
                'pedido.user',
                'pedido.detallePedidos.producto',
            ])
            ->where('delivery_id', $delivery->id)
            ->whereIn('estado', [
                'aceptado',
                'en_camino',
                'entregado',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view(
            'delivery.pedidos.mis',
            compact('asignaciones')
        );
    }

    /**
     * INICIAR ENTREGA
     *
     * La asignación ya fue aceptada automáticamente por el sistema.
     * El Delivery solamente indica cuándo comienza el recorrido.
     */
    public function iniciar(int $id): RedirectResponse
    {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof RedirectResponse) {
            return $delivery;
        }

        DB::transaction(function () use ($id, $delivery): void {
            $asignacion = AsignacionDelivery::query()
                ->where('pedido_id', $id)
                ->where('delivery_id', $delivery->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($asignacion->estado !== 'aceptado') {
                throw new \RuntimeException(
                    'El pedido no puede iniciar la entrega en este momento.'
                );
            }

            $pedido = Pedido::query()
                ->where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pedido->estado !== 'asignado') {
                throw new \RuntimeException(
                    'El pedido ya no se encuentra disponible para iniciar la entrega.'
                );
            }

            $asignacion->update([
                'estado' => 'en_camino',
            ]);

            $pedido->update([
                'estado' => 'en_camino',
            ]);

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
            'Entrega iniciada correctamente.'
        );
    }

    /**
     * MARCAR COMO ENTREGADO
     *
     * en_camino -> entregado
     *
     * Después de completar la entrega, el mismo Delivery queda libre
     * y el sistema procesa inmediatamente la cola para asignarle
     * el siguiente pedido, sin permitir que salte pedidos.
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

        DB::transaction(function () use ($id, $delivery): void {
            $asignacion = AsignacionDelivery::query()
                ->where('pedido_id', $id)
                ->where('delivery_id', $delivery->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($asignacion->estado !== 'en_camino') {
                throw new \RuntimeException(
                    'El pedido todavía no está en camino.'
                );
            }

            $pedido = Pedido::query()
                ->where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pedido->estado !== 'en_camino') {
                throw new \RuntimeException(
                    'El pedido ya no se encuentra en camino.'
                );
            }

            $asignacion->update([
                'estado' => 'entregado',
            ]);

            $pedido->update([
                'estado' => 'entregado',
            ]);

            Notificacion::create([
                'user_id' => $pedido->user_id,
                'pedido_id' => $pedido->id,
                'mensaje' => 'Tu pedido #' . $pedido->id . ' fue entregado correctamente.',
                'tipo' => 'cliente',
                'evento' => 'pedido_entregado',
                'leido' => false,
            ]);
        });

        /*
         * Firebase es independiente de MySQL. La ubicación del pedido
         * se elimina explícitamente al finalizar la entrega.
         * No dependemos de que el navegador vuelva a cargar la página.
         */
        $firebaseLocation->eliminarPorPedido($id);

        // Al liberar este Delivery, se entrega automáticamente
        // el siguiente pedido más antiguo que esté esperando.
        $asignados = $asignador->procesarCola();

        if ($asignados > 0) {
            return back()->with(
                'success',
                'Pedido marcado como entregado. Se te asignó automáticamente el siguiente pedido de la cola.'
            );
        }

        return back()->with(
            'success',
            'Pedido marcado como entregado correctamente. No hay más pedidos esperando en la cola.'
        );
    }
}
