<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Mostrar clientes registrados.
     */
    public function index()
    {
        $clientes = User::where('role_id', 2)
            ->withCount('pedidos')
            ->orderBy('name')
            ->get();

        return view(
            'admin.clientes.index',
            compact('clientes')
        );
    }

    /**
     * Mostrar información e historial del cliente.
     */
    public function show($id)
    {
        $cliente = User::where('role_id', 2)
            ->with([
                'pedidos.detallePedidos.producto',
                'pedidos.comprobantePago'
            ])
            ->withCount('pedidos')
            ->findOrFail($id);

        return view(
            'admin.clientes.show',
            compact('cliente')
        );
    }

    /**
     * Activar cliente.
     */
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

    /**
     * Desactivar cliente.
     */
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

    /**
     * Eliminar cliente.
     *
     * Si tiene pedidos, no se elimina físicamente
     * para proteger el historial.
     */
    public function destroy($id)
    {
        $cliente = User::where('role_id', 2)
            ->findOrFail($id);

        // Verificar si tiene pedidos
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
