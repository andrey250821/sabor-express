<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Configuracion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Mostrar la pantalla de inicio de sesión.
     */
    public function create(): View
    {
        $configuracion = Configuracion::first();

        return view('auth.login', compact('configuracion'));
    }

    /**
     * Procesar las credenciales de acceso.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        $user->loadMissing('role');

        /*
         * Delivery es el único rol que puede iniciar sesión con estado
         * inactivo, únicamente para acceder al perfil y volver a activarse.
         * Los demás roles deben ser reactivados por la administración.
         */
        if ($user->estado !== 'activo') {
            if ($user->role && $user->role->nombre === 'Delivery') {
                return redirect()
                    ->route('delivery.perfil.edit')
                    ->with(
                        'error',
                        'Tu cuenta de Delivery está inactiva. Usa el botón Activar de tu perfil para volver a recibir entregas.'
                    );
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')
                ->withErrors([
                    'email' => 'Tu cuenta está inactiva. Contacta con el Administrador para reactivarla.',
                ]);
        }

        if ($user->role && $user->role->nombre === 'Administrador') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role && $user->role->nombre === 'Cliente') {
            return redirect()->intended(
                route('cliente.dashboard.index')
            );
        }

        if ($user->role && $user->role->nombre === 'Delivery') {
            return redirect()->route('delivery.dashboard');
        }

        if ($user->role && $user->role->nombre === 'Cocinero') {
            return redirect()->route('cocinero.dashboard');
        }

        Auth::logout();

        return redirect('/login')
            ->withErrors([
                'email' => 'El usuario no tiene un rol válido.',
            ]);
    }

    /**
     * Cerrar la sesión autenticada.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
