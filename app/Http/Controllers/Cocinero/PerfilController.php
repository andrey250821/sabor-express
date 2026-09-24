<?php

namespace AppHttpControllersCocinero;

use AppHttpControllersController;
use IlluminateHttpRedirectResponse;
use IlluminateHttpRequest;
use IlluminateSupportFacadesStorage;
use IlluminateSupportFacadesRedirect;
use IlluminateSupportStr;
use IlluminateViewView;

class PerfilController extends Controller
{
    /**
     * Mostrar el perfil del cocinero autenticado.
     */
    public function edit(Request $request): View
    {
        return view('cocinero.perfil.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Actualizar los datos que el cocinero puede modificar.
     *
     * El rol, correo y estado se mantienen bajo control administrativo.
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

        return Redirect::route('cocinero.perfil.edit')
            ->with('profile_status', 'Perfil actualizado correctamente.');
    }
}
