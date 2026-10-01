<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FechaFiltroService;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Mostrar clientes registrados.
     *
     * Este listado es un directorio actual, por lo que no se filtra
     * por fecha. La búsqueda continúa funcionando en tiempo real.
     */
    public function index(Request $request)
    {
        $buscar = trim((string) $request->input('buscar', ''));

        $clientes = User::where('role_id', 2)
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where('email', 'like', '%' . $buscar . '%');
            })
            ->withCount('pedidos')
            ->orderBy('name')
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view(
                    'admin.clientes._tabla',
                    compact('clientes')
                )->render(),
                'count' => $clientes->count(),
            ]);
        }

        return view(
            'admin.clientes.index',
            compact('clientes')
        );
    }

    /**
     * Mostrar información e historial del cliente de la fecha seleccionada.
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

        $cliente = User::where('role_id', 2)
            ->with([
                'pedidos' => fn ($query) => $query
                    ->whereBetween('created_at', [$inicioUtc, $finUtc])
                    ->with([
                        'detallePedidos.producto',
                        'comprobantePago',
                    ])
                    ->orderByDesc('created_at'),
            ])
            ->withCount('pedidos')
            ->findOrFail($id);

        return view(
            'admin.clientes.show',
            compact(
                'cliente',
                'fechaSeleccionada'
            )
        );
    }

    public function activar($id)
    {
        $cliente = User::where('role_id', 2)
            ->findOrFail($id);

        $cliente->estado = 'activo';
        $cliente->save();

        return back()->with(
            'success',
            'Cliente activado correctamente.'
        );
    }

    public function desactivar($id)
    {
        $cliente = User::where('role_id', 2)
            ->findOrFail($id);

        $cliente->estado = 'inactivo';
        $cliente->save();

        return back()->with(
            'success',
            'Cliente desactivado correctamente.'
        );
    }

    public function destroy($id)
    {
        $cliente = User::where('role_id', 2)
            ->findOrFail($id);

        if ($cliente->pedidos()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar este cliente porque tiene pedidos registrados. Puedes desactivarlo.'
            );
        }

        $cliente->delete();

        return redirect()
            ->route('admin.clientes.index')
            ->with(
                'success',
                'Cliente eliminado correctamente.'
            );
    }
}
