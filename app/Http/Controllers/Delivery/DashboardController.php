<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\AsignacionDelivery;
use Illuminate\Support\Facades\Auth;
use App\Services\AsignarPedidoDeliveryService;

class DashboardController extends Controller
{
    public function index(AsignarPedidoDeliveryService $asignador)
    {
        // Verificar que haya un usuario logueado
        $deliveryId = Auth::id();

        if (!$deliveryId) {
            return redirect()->route('login')
                ->with('error', 'Debe iniciar sesión.');
        }

        // Verificar que el usuario sea un delivery activo
        $delivery = Auth::user();

        if (
            !$delivery ||
            $delivery->role_id != 3 ||
            $delivery->estado !== 'activo'
        ) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        // Procesar cualquier pedido que ya esté en cola y tenga un Delivery libre.
        $asignador->procesarCola();

        // Pedidos listos que todavía NO tienen Delivery asignado
        $pedidosEnCola = Pedido::where('estado', 'listo')
            ->whereDoesntHave('asignacionDelivery')
            ->count();

        // Pedidos que este delivery tiene actualmente
        $misPedidos = AsignacionDelivery::where(
            'delivery_id',
            $deliveryId
        )
            ->whereIn('estado', [
                'aceptado',
                'en_camino'
            ])
            ->count();

        // Pedidos entregados por este delivery
        $pedidosEntregados = AsignacionDelivery::where(
            'delivery_id',
            $deliveryId
        )
            ->where('estado', 'entregado')
            ->count();

        return view(
            'delivery.dashboard.index',
            compact(
                'pedidosEnCola',
                'misPedidos',
                'pedidosEntregados',
                'delivery'
            )
        );
    }
}
