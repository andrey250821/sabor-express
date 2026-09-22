<?php

namespace App\Http\Controllers\Cocinero;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PedidoController extends Controller
{
    /**
     * Mostrar la cola de cocina.
     *
     * Pendientes:
     * - Solo pedidos pagados que todavía no fueron tomados por ningún cocinero.
     *
     * Preparando:
     * - Solo pedidos que el cocinero autenticado tomó y está preparando.
     *
     * Listos:
     * - Todos los pedidos ya terminados por cocina. En esta etapa ya no
     *   existen acciones de cocina, por lo que son seguros de visualizar.
     *
     * El pedido pagado que ya tomó el cocinero actual se muestra aparte
     * para no perderlo de vista.
     */
    public function index(): View
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

        $miPedidoPendiente = Pedido::with([
            'user',
            'detallePedidos.producto',
        ])
            ->where('estado', 'pagado')
            ->where('cocinero_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->first();

        $preparando = Pedido::with([
            'user',
            'detallePedidos.producto',
        ])
            ->where('estado', 'preparando')
            ->where('cocinero_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $listos = Pedido::with([
            'user',
            'detallePedidos.producto',
        ])
            ->where('estado', 'listo')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('cocinero.pedidos.index', compact(
            'pendientes',
            'miPedidoPendiente',
            'preparando',
            'listos'
        ));
    }

    /**
     * Mostrar el detalle de un pedido.
     *
     * Un cocinero no puede abrir un pedido pagado o en preparación
     * que ya pertenezca a otro cocinero.
     */
    public function show(int $id): View
    {
        $pedido = Pedido::with([
            'user',
            'cocinero',
            'detallePedidos.producto',
        ])->findOrFail($id);

        if (
            in_array($pedido->estado, ['pagado', 'preparando'], true)
            && $pedido->cocinero_id !== null
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
     * Tomar el siguiente pedido de la cola.
     *
     * La cola es FIFO: el primer pedido disponible es el primero
     * que puede tomar un cocinero. Así se evita que alguien salte
     * pedidos y se respeta el orden en que llegaron a cocina.
     */
    public function tomar(int $id): RedirectResponse
    {
        try {
            DB::transaction(function () use ($id): void {
                $cocineroId = (int) Auth::id();

                // Un cocinero trabaja con un único pedido activo a la vez.
                $yaTienePedido = Pedido::query()
                    ->where('cocinero_id', $cocineroId)
                    ->whereIn('estado', ['pagado', 'preparando'])
                    ->lockForUpdate()
                    ->exists();

                if ($yaTienePedido) {
                    throw new \RuntimeException(
                        'Ya tienes un pedido asignado. Termina ese pedido antes de tomar otro.'
                    );
                }

                // Bloqueamos el siguiente pedido libre de la cola.
                $siguiente = Pedido::query()
                    ->where('estado', 'pagado')
                    ->whereNull('cocinero_id')
                    ->orderBy('created_at', 'asc')
                    ->orderBy('id', 'asc')
                    ->lockForUpdate()
                    ->first();

                if (!$siguiente) {
                    throw new \RuntimeException(
                        'No hay pedidos pendientes disponibles en este momento.'
                    );
                }

                // No se permite saltar el orden de la cola.
                if ((int) $siguiente->id !== $id) {
                    throw new \RuntimeException(
                        'Para respetar la cola debes tomar primero el pedido #' . $siguiente->id . '.'
                    );
                }

                $siguiente->update([
                    'cocinero_id' => $cocineroId,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('cocinero.pedidos.show', $id)
            ->with('success', 'Pedido #' . $id . ' tomado correctamente. Ahora solo tú puedes modificarlo.');
    }

    /**
     * Cambiar pedido de PAGADO a PREPARANDO.
     */
    public function preparar(int $id): RedirectResponse
    {
        $pedido = Pedido::findOrFail($id);

        if ((int) $pedido->cocinero_id !== (int) Auth::id()) {
            abort(403, 'No puedes iniciar la preparación de un pedido tomado por otro cocinero.');
        }

        if ($pedido->estado !== 'pagado') {
            return back()->with(
                'error',
                'El pedido no puede comenzar a prepararse porque su estado ya cambió.'
            );
        }

        $pedido->update([
            'estado' => 'preparando',
        ]);

        return redirect()
            ->route('cocinero.pedidos.show', $pedido->id)
            ->with('success', 'El pedido comenzó a prepararse.');
    }

    /**
     * Cambiar pedido de PREPARANDO a LISTO.
     */
    public function listo(int $id): RedirectResponse
    {
        $pedido = Pedido::findOrFail($id);

        if ((int) $pedido->cocinero_id !== (int) Auth::id()) {
            abort(403, 'No puedes finalizar un pedido que pertenece a otro cocinero.');
        }

        if ($pedido->estado !== 'preparando') {
            return back()->with(
                'error',
                'El pedido no puede marcarse como listo porque no está en preparación.'
            );
        }

        $pedido->update([
            'estado' => 'listo',
        ]);

        return redirect()
            ->route('cocinero.pedidos.index')
            ->with(
                'success',
                'Pedido #' . $pedido->id . ' listo. Ya puedes tomar el siguiente pedido de la cola.'
            );
    }
}
