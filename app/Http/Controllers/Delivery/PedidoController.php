<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\AsignacionDelivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    /**
     * Verificar que el usuario actual sea un delivery activo.
     */
    private function verificarDelivery()
    {
        $delivery = Auth::user();

        if (!$delivery) {
            return redirect()->route('login')
                ->with('error', 'Debe iniciar sesión.');
        }

        if (
            $delivery->role_id != 3 ||
            $delivery->estado !== 'activo'
        ) {
            abort(403, 'No tienes permisos para acceder como delivery.');
        }

        return $delivery;
    }


    /**
     * PEDIDOS DISPONIBLES
     *
     * Muestra los pedidos que:
     * - Están listos.
     * - No tienen delivery asignado.
     */
    public function index()
    {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof \Illuminate\Http\RedirectResponse) {
            return $delivery;
        }

        $pedidos = Pedido::with([
            'user',
            'detallePedidos.producto'
        ])
            ->where('estado', 'listo')
            ->whereDoesntHave('asignacionDelivery')
            ->orderBy('created_at', 'asc')
            ->get();

        return view(
            'delivery.pedidos.index',
            compact('pedidos')
        );
    }


    /**
     * DETALLE DE UN PEDIDO
     */
    public function show($id)
    {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof \Illuminate\Http\RedirectResponse) {
            return $delivery;
        }

        $pedido = Pedido::with([
            'user',
            'detallePedidos.producto',
            'comprobantePago',
            'asignacionDelivery.delivery'
        ])->findOrFail($id);

        return view(
            'delivery.pedidos.show',
            compact('pedido')
        );
    }


    /**
     * TOMAR PEDIDO
     *
     * El delivery toma personalmente el pedido.
     *
     * Reglas:
     * - Debe ser delivery activo.
     * - El pedido debe estar LISTO.
     * - No debe tener otro delivery asignado.
     */
    public function tomar($id)
    {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof \Illuminate\Http\RedirectResponse) {
            return $delivery;
        }

        DB::beginTransaction();

        try {

            $pedido = Pedido::lockForUpdate()
                ->findOrFail($id);

            // El pedido debe estar listo
            if ($pedido->estado !== 'listo') {

                DB::rollBack();

                return back()->with(
                    'error',
                    'Este pedido todavía no está listo para entregar.'
                );
            }

            // Verificar si ya tiene delivery
            $asignacionExistente = AsignacionDelivery::where(
                'pedido_id',
                $pedido->id
            )->first();

            if ($asignacionExistente) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'Este pedido ya fue tomado por otro repartidor.'
                );
            }

            // Crear asignación
            AsignacionDelivery::create([

                'pedido_id' => $pedido->id,

                'delivery_id' => $delivery->id,

                'estado' => 'aceptado',

                'fecha_asignacion' => now(),

                'fecha_respuesta' => now(),

            ]);

            // Cambiar estado del pedido
            $pedido->estado = 'asignado';

            $pedido->save();

            DB::commit();

            return redirect()
                ->route('delivery.pedidos.mis')
                ->with(
                    'success',
                    'Pedido tomado correctamente.'
                );
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'No se pudo tomar el pedido.'
            );
        }
    }


    /**
     * MIS PEDIDOS
     *
     * Pedidos que pertenecen al delivery actualmente logueado.
     */
    public function misPedidos()
    {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof \Illuminate\Http\RedirectResponse) {
            return $delivery;
        }

        $asignaciones = AsignacionDelivery::with([
            'pedido.user',
            'pedido.detallePedidos.producto'
        ])
            ->where(
                'delivery_id',
                $delivery->id
            )
            ->whereIn('estado', [
                'aceptado',
                'en_camino',
                'entregado'
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
     * aceptado -> en_camino
     */
    public function iniciar($id)
    {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof \Illuminate\Http\RedirectResponse) {
            return $delivery;
        }

        $asignacion = AsignacionDelivery::where(
            'pedido_id',
            $id
        )
            ->where(
                'delivery_id',
                $delivery->id
            )
            ->firstOrFail();

        // Solo se puede iniciar si está aceptado
        if ($asignacion->estado !== 'aceptado') {

            return back()->with(
                'error',
                'El pedido no puede iniciar la entrega en este momento.'
            );
        }

        $asignacion->estado = 'en_camino';

        $asignacion->save();

        // Actualizar pedido
        $pedido = Pedido::findOrFail($id);

        $pedido->estado = 'en_camino';

        $pedido->save();

        return back()->with(
            'success',
            'Entrega iniciada correctamente.'
        );
    }


    /**
     * MARCAR COMO ENTREGADO
     *
     * en_camino -> entregado
     */
    public function entregar($id)
    {
        $delivery = $this->verificarDelivery();

        if ($delivery instanceof \Illuminate\Http\RedirectResponse) {
            return $delivery;
        }

        $asignacion = AsignacionDelivery::where(
            'pedido_id',
            $id
        )
            ->where(
                'delivery_id',
                $delivery->id
            )
            ->firstOrFail();

        // Solo se puede entregar si está en camino
        if ($asignacion->estado !== 'en_camino') {

            return back()->with(
                'error',
                'El pedido todavía no está en camino.'
            );
        }

        $asignacion->estado = 'entregado';

        $asignacion->save();

        // Actualizar pedido
        $pedido = Pedido::findOrFail($id);

        $pedido->estado = 'entregado';

        $pedido->save();

        return back()->with(
            'success',
            'Pedido marcado como entregado correctamente.'
        );
    }
}
