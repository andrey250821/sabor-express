<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PerfilController extends Controller
{
    /**
     * Mostrar la configuración del perfil del cliente autenticado.
     */
    public function edit(): View
    {
        return view('cliente.configuracion.index', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Actualizar nombre y teléfono.
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
        ]);

        $user = $request->user();

        $user->update([
            'name' => $datos['name'],
            'telefono' => $datos['telefono'] ?? null,
        ]);

        return Redirect::route('cliente.configuracion.edit')
            ->with('profile_status', 'Información de perfil actualizada correctamente.');
    }

    /**
     * Actualizar contraseña del cliente.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validateWithBag('passwordUpdate', [
            'current_password' => [
                'required',
                'current_password',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return Redirect::route('cliente.configuracion.edit')
            ->with('password_status', 'Contraseña actualizada correctamente.');
    }
}
