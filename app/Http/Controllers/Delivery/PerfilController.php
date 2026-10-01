<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\AsignacionDelivery;

class PerfilController extends Controller
{
    /**
     * Mostrar el perfil del Delivery autenticado.
     */
    public function edit(Request $request): View
    {
        return view('delivery.perfil.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Activar o desactivar el estado de trabajo del Delivery.
     *
     * Un Delivery con pedidos activos no puede pasar a inactivo
     * para evitar dejar una entrega en curso sin acceso.
     */
    public function alternarEstado(Request $request): RedirectResponse
    {
        $user = $request->user();

        $tienePedidoActivo = AsignacionDelivery::query()
            ->where('delivery_id', $user->id)
            ->whereHas('pedido', function ($query) {
                $query->whereIn('estado', ['asignado', 'en_camino']);
            })
            ->exists();

        if ($user->estado === 'activo' && $tienePedidoActivo) {
            return back()->with(
                'error',
                'No puedes ponerte inactivo mientras tengas un pedido asignado o en camino. Finaliza primero la entrega.'
            );
        }

        $nuevoEstado = $user->estado === 'activo'
            ? 'inactivo'
            : 'activo';

        $user->update([
            'estado' => $nuevoEstado,
        ]);

        return back()->with(
            'success',
            $nuevoEstado === 'activo'
                ? 'Ahora estás activo y puedes recibir pedidos.'
                : 'Ahora estás inactivo y no recibirás nuevos pedidos.'
        );
    }

    /**
     * Actualizar los datos personales y la foto del Delivery.
     *
     * El correo, rol y estado permanecen bajo control administrativo.
     */
    public function update(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],
            'foto_perfil' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $user = $request->user();

        $user->update([
            'name' => $datos['name'],
            'telefono' => $datos['telefono'] ?? null,
        ]);

        if ($request->hasFile('foto_perfil')) {
            $fotoAnterior = $user->foto_perfil;

            $nuevaFoto = $request->file('foto_perfil')
                ->store('perfiles', 'public');

            if (!$nuevaFoto) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'foto_perfil' => 'No se pudo guardar la foto de perfil. Verifica que el directorio de almacenamiento tenga permisos de escritura.',
                    ]);
            }

            $user->update([
                'foto_perfil' => $nuevaFoto,
            ]);

            if (
                $fotoAnterior &&
                !Str::startsWith($fotoAnterior, ['http://', 'https://']) &&
                Storage::disk('public')->exists($fotoAnterior)
            ) {
                Storage::disk('public')->delete($fotoAnterior);
            }
        }

        return Redirect::route('delivery.perfil.edit')
            ->with('profile_status', 'Perfil actualizado correctamente.');
    }
}
