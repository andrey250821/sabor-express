<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AsignacionDelivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\FechaFiltroService;
use App\Services\AsignarPedidoDeliveryService;

class DeliveryController extends Controller
{
    /**
     * Lista de Delivery
     */
    public function index(Request $request)
    {
        $buscar = trim((string) $request->input('buscar', ''));

        $deliverys = User::where('role_id', 3)
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where('email', 'like', '%' . $buscar . '%');
            })
            ->withCount('asignacionesDelivery')
            ->orderBy('id', 'desc')
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view(
                    'admin.deliverys._tabla',
                    compact('deliverys')
                )->render(),
                'count' => $deliverys->count(),
            ]);
        }

        return view(
            'admin.deliverys.index',
            compact('deliverys')
        );
    }

    /**
     * Mostrar el detalle y las estadísticas económicas de un Delivery.
     */
    public function show(
        Request $request,
        int $id,
        FechaFiltroService $fechas
    ) {
        $fechaSeleccionada = $fechas->resolver($request);

        [$inicioUtc, $finUtc] = $fechas->rangoUtc(
            $fechaSeleccionada
        );

        $delivery = User::query()
            ->where('role_id', 3)
            ->findOrFail($id);

        /*
         * Un pedido activo puede haber sido asignado antes del día seleccionado
         * y seguir activo. Por ello se considera actividad de la fecha cuando
         * la asignación fue creada o actualizada ese día.
         *
         * Para los entregados, updated_at corresponde al momento de finalizar
         * la entrega porque el controlador de Delivery hace touch() al entregar.
         */
        $asignaciones = AsignacionDelivery::query()
            ->where('delivery_id', $delivery->id)
            ->where(function ($query) use ($inicioUtc, $finUtc) {
                $query
                    ->whereBetween('created_at', [$inicioUtc, $finUtc])
                    ->orWhereBetween('updated_at', [$inicioUtc, $finUtc]);
            })
            ->with('pedido')
            ->orderByDesc('updated_at')
            ->get();

        $pedidosActivos = $asignaciones
            ->filter(function ($asignacion) {
                return in_array(
                    $asignacion->pedido?->estado,
                    ['asignado', 'en_camino'],
                    true
                );
            })
            ->count();

        $pedidosEntregados = $asignaciones
            ->filter(function ($asignacion) {
                return $asignacion->pedido?->estado === 'entregado';
            })
            ->values();

        $totalDeliveryGenerado = round(
            $pedidosEntregados->sum(
                fn ($asignacion) => (float) ($asignacion->pedido?->tarifa_delivery ?? 0)
            ),
            2
        );

        $comisionDelivery = round(
            $pedidosEntregados->sum(
                fn ($asignacion) => (float) ($asignacion->pedido?->monto_delivery ?? 0)
            ),
            2
        );

        $parteRestaurante = round(
            $pedidosEntregados->sum(
                fn ($asignacion) => (float) ($asignacion->pedido?->monto_restaurante_delivery ?? 0)
            ),
            2
        );

        return view(
            'admin.deliverys.show',
            compact(
                'delivery',
                'asignaciones',
                'pedidosActivos',
                'pedidosEntregados',
                'totalDeliveryGenerado',
                'comisionDelivery',
                'parteRestaurante',
                'fechaSeleccionada'
            )
        );
    }

    public function create()
    {
        return view('admin.deliverys.create');
    }

    /**
     * Guardar nuevo delivery
     */
    public function store(Request $request)
    {
        $request->validate([

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20'
            ],
'password' => [
                'required',
                'string',
                'min:6',
                'confirmed'
            ],

            'estado' => [
                'required',
                Rule::in(['activo', 'inactivo'])
            ]

        ]);

        User::create([

            'role_id' => 3,

            'name' => $request->name,

            'email' => $request->email,

            'telefono' => $request->telefono,
'password' => Hash::make($request->password),

            'estado' => $request->estado

        ]);

        return redirect()
            ->route('admin.deliverys.index')
            ->with(
                'success',
                'Delivery creado correctamente.'
            );
    }

    /**
     * Mostrar formulario de edición del Delivery
     */
    public function edit($id)
    {
        $delivery = User::where('role_id', 3)
            ->findOrFail($id);

        return view(
            'admin.deliverys.edit',
            compact('delivery')
        );
    }

    /**
     * Actualizar delivery
     */
    public function update(Request $request, $id)
    {
        $delivery = User::where('role_id', 3)
            ->findOrFail($id);

        $request->validate([

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $delivery->id
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20'
            ],
'password' => [
                'nullable',
                'string',
                'min:6',
                'confirmed'
            ],

            'estado' => [
                'required',
                Rule::in(['activo', 'inactivo'])
            ]

        ]);

        if (
            $request->estado === 'inactivo' &&
            $delivery->estado === 'activo'
        ) {
            $tienePedidoActivo = AsignacionDelivery::query()
                ->where('delivery_id', $delivery->id)
                ->whereHas('pedido', function ($query) {
                    $query->whereIn('estado', ['asignado', 'en_camino']);
                })
                ->exists();

            if ($tienePedidoActivo) {
                return back()
                    ->withInput()
                    ->with('error', 'No se puede desactivar este Delivery porque tiene un pedido asignado o en camino. Debe finalizar la entrega primero.');
            }
        }

        $delivery->name = $request->name;

        $delivery->email = $request->email;

        $delivery->telefono = $request->telefono;
        $delivery->estado = $request->estado;

        /**
         * Solo cambiar contraseña si se escribió una nueva
         */
        if ($request->filled('password')) {

            $delivery->password = Hash::make(
                $request->password
            );
        }

        $delivery->save();

        return redirect()
            ->route('admin.deliverys.index')
            ->with(
                'success',
                'Delivery actualizado correctamente.'
            );
    }

    /**
     * Desactivar Delivery
     */
    public function destroy($id)
    {
        $delivery = User::where('role_id', 3)
            ->findOrFail($id);

        $tienePedidoActivo = AsignacionDelivery::query()
            ->where('delivery_id', $delivery->id)
            ->whereHas('pedido', function ($query) {
                $query->whereIn('estado', ['asignado', 'en_camino']);
            })
            ->exists();

        if ($tienePedidoActivo) {
            return back()->with(
                'error',
                'No se puede desactivar este Delivery porque tiene un pedido asignado o en camino. Debe finalizar la entrega primero.'
            );
        }

        $delivery->estado = 'inactivo';
        $delivery->save();

        return back()->with(
            'success',
            'Delivery desactivado correctamente.'
        );
    }

    /**
     * Activar nuevamente el Delivery
     */
    public function activar(
        $id,
        AsignarPedidoDeliveryService $asignador
    )
    {
        $delivery = User::where('role_id', 3)
            ->findOrFail($id);

        $delivery->estado = 'activo';

        $delivery->save();

        // Si había pedidos listos esperando, la activación de este Delivery
        // debe liberar inmediatamente un cupo de la cola automática.
        $asignados = $asignador->procesarCola();

        $mensaje = $asignados > 0
            ? 'Delivery activado correctamente. Se procesó automáticamente la cola de pedidos.'
            : 'Delivery activado correctamente. No había pedidos esperando en la cola.';

        return back()->with('success', $mensaje);
    }
}
