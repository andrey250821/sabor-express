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

        /*
         * Una cuenta inactiva no puede iniciar sesión.
         * Esto mantiene coherente el estado administrado desde el panel.
         */
        if ($user->estado !== 'activo') {
            Auth::logout();

            return redirect('/login')
                ->withErrors([
                    'email' => 'Tu cuenta está inactiva. Contacta con el administrador.',
                ]);
        }

        $user->loadMissing('role');

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
