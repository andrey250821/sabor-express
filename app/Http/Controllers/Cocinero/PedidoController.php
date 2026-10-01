<?php

namespace App\Http\Controllers\Cocinero;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AsignarPedidoDeliveryService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class PedidoController extends Controller
{
    /**
     * Mostrar los pedidos de cocina.
     *
     * Pendientes:
     * - Todos los pedidos pagados que todavía no han comenzado a prepararse.
     *
     * Preparando:
     * - Solo los pedidos que el cocinero autenticado comenzó a preparar.
     *
     * Listos:
     * - Pedidos que este cocinero llevó hasta LISTO o que posteriormente
     *   avanzaron en el flujo de Delivery.
     * - Se pueden consultar por fecha mediante ?periodo_listos=hoy|ayer|anteayer|semana.
     *
     * La tarjeta seleccionada se controla mediante ?seccion=pendientes|preparando|listos.
     */
    public function index(Request $request): View
    {
        $pendientes = Pedido::with([
            'user',
            'detallePedidos.producto',
        ])
            ->where('estado', 'pagado')
            ->whereNull('cocinero_id')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $preparando = Pedido::with([
            'user',
            'detallePedidos.producto',
        ])
            ->where('estado', 'preparando')
            ->where('cocinero_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        /*
         * LISTOS / HISTORIAL
         *
         * Se muestran los pedidos que este cocinero llevó hasta LISTO
         * o que posteriormente avanzaron en el flujo (asignado, en camino,
         * entregado o cancelado).
         *
         * El filtro de fecha usa created_at y no agrega columnas nuevas
         * ni modifica la estructura existente de pedidos.
         */
        $periodosListosValidos = [
            'hoy',
            'ayer',
            'anteayer',
            'semana',
        ];

        $periodoListos = $request->query('periodo_listos', 'hoy');

        if (!in_array($periodoListos, $periodosListosValidos, true)) {
            $periodoListos = 'hoy';
        }

        $hoy = Carbon::today();

        [$inicioListos, $finListos] = match ($periodoListos) {
            'ayer' => [
                $hoy->copy()->subDay()->startOfDay(),
                $hoy->copy()->subDay()->endOfDay(),
            ],
            'anteayer' => [
                $hoy->copy()->subDays(2)->startOfDay(),
                $hoy->copy()->subDays(2)->endOfDay(),
            ],
            'semana' => [
                $hoy->copy()->subDays(6)->startOfDay(),
                $hoy->copy()->endOfDay(),
            ],
            default => [
                $hoy->copy()->startOfDay(),
                $hoy->copy()->endOfDay(),
            ],
        };

        $listos = Pedido::with([
            'user',
            'detallePedidos.producto',
        ])
            ->whereIn('estado', [
                'listo',
                'asignado',
                'en_camino',
                'entregado',
                'cancelado',
            ])
            ->where('cocinero_id', Auth::id())
            ->whereBetween('created_at', [$inicioListos, $finListos])
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $seccionesValidas = ['pendientes', 'preparando', 'listos'];

        $seccion = $request->query('seccion', 'pendientes');

        if (!in_array($seccion, $seccionesValidas, true)) {
            $seccion = 'pendientes';
        }

        return view('cocinero.pedidos.index', compact(
            'pendientes',
            'preparando',
            'listos',
            'seccion',
            'periodoListos'
        ));
    }

    /**
     * Mostrar el detalle de un pedido.
     *
     * Los pedidos en preparación o listos solo pueden ser consultados
     * por el cocinero que inició su preparación.
     */
    public function show(int $id): View
    {
        $pedido = Pedido::with([
            'user',
            'cocinero',
            'detallePedidos.producto',
        ])->findOrFail($id);

        if (
            in_array($pedido->estado, ['preparando', 'listo', 'asignado'], true)
            && (int) $pedido->cocinero_id !== (int) Auth::id()
        ) {
            abort(403, 'Este pedido pertenece a otro cocinero.');
        }

        return view(
            'cocinero.pedidos.show',
            compact('pedido')
        );
    }

    /**
     * Iniciar la preparación de un pedido.
     *
     * Este es el único momento en que un pedido pasa a pertenecer
     * a un cocinero. No existe una etapa intermedia de "reservado".
     *
     * La operación se ejecuta dentro de una transacción y con bloqueo
     * para evitar que dos cocineros reclamen el mismo pedido.
     */
    public function preparar(int $id): RedirectResponse
    {
        try {
            DB::transaction(function () use ($id): void {
                $pedido = Pedido::query()
                    ->where('id', $id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($pedido->estado !== 'pagado') {
                    throw new \RuntimeException(
                        'El pedido no puede comenzar a prepararse porque su estado ya cambió.'
                    );
                }

                if ($pedido->cocinero_id !== null) {
                    throw new \RuntimeException(
                        'Este pedido ya fue tomado por otro cocinero.'
                    );
                }

                // FIFO: solo puede comenzar el pedido pagado más antiguo
                // que todavía está libre en la cola.
                $siguiente = Pedido::query()
                    ->where('estado', 'pagado')
                    ->whereNull('cocinero_id')
                    ->orderBy('created_at', 'asc')
                    ->orderBy('id', 'asc')
                    ->lockForUpdate()
                    ->first();

                if (!$siguiente || (int) $siguiente->id !== $id) {
                    $numero = $siguiente?->id;

                    throw new \RuntimeException(
                        $numero
                            ? 'Para respetar el orden de llegada debes preparar primero el pedido #' . $numero . '.'
                            : 'No hay pedidos pendientes disponibles en este momento.'
                    );
                }

                $pedido->update([
                    'cocinero_id' => Auth::id(),
                    'estado' => 'preparando',
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('cocinero.pedidos.index', ['seccion' => 'preparando'])
            ->with(
                'success',
                'Pedido #' . $id . ' comenzó a prepararse y ya no está disponible para los demás cocineros.'
            );
    }

    /**
     * Cambiar pedido de PREPARANDO a LISTO.
     */
    public function listo(int $id, AsignarPedidoDeliveryService $asignador): RedirectResponse
    {
        try {
            DB::transaction(function () use ($id): void {
                $pedido = Pedido::query()
                    ->where('id', $id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ((int) $pedido->cocinero_id !== (int) Auth::id()) {
                    abort(403, 'No puedes finalizar un pedido que pertenece a otro cocinero.');
                }

                if ($pedido->estado !== 'preparando') {
                    throw new \RuntimeException(
                        'El pedido no puede marcarse como listo porque no está en preparación.'
                    );
                }

                $pedido->update([
                    'estado' => 'listo',
                ]);
            });

            $asignador->procesarCola();
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('cocinero.pedidos.index', ['seccion' => 'listos'])
            ->with(
                'success',
                'Pedido #' . $id . ' listo. Se procesó automáticamente la cola de Delivery.'
            );
    }}
