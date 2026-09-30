<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PersonalActivoMiddleware
{
    /**
     * Solo permite el acceso operativo a Delivery y Cocinero
     * cuando su estado de trabajo es activo.
     *
     * Los usuarios inactivos conservan acceso a su perfil para
     * poder volver a activarse desde el botón de estado.
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
            $rutaPerfil = (int) $usuario->role_id === 3
                ? 'delivery.perfil.edit'
                : 'cocinero.perfil.edit';

            return redirect()
                ->route($rutaPerfil)
                ->with(
                    'error',
                    'Tu estado de trabajo está inactivo. Actívate para volver a recibir y gestionar pedidos.'
                );
        }

        return $next($request);
    }
}
