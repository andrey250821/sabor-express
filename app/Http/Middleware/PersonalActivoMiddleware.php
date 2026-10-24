<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PersonalActivoMiddleware
{
    /**
     * Protege las funciones operativas de Cocina y Delivery.
     *
     * Delivery puede conservar una sesión limitada a su perfil para volver
     * a activarse. Un Cocinero inactivo no puede reactivarse por cuenta propia:
     * su estado debe ser cambiado por la administración.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $usuario = Auth::user();

        if (!$usuario || !in_array((int) $usuario->role_id, [3, 4], true)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        if ($usuario->estado !== 'activo') {
            if ((int) $usuario->role_id === 3) {
                return redirect()
                    ->route('delivery.perfil.edit')
                    ->with(
                        'error',
                        'Tu cuenta de Delivery está inactiva. Actívala desde tu perfil para volver a recibir entregas.'
                    );
            }

            // Un Cocinero inactivo no conserva acceso al panel ni al perfil.
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Tu cuenta de Cocinero está inactiva. Contacta al Administrador para reactivarla.',
                ]);
        }

        return $next($request);
    }
}
