<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    /**
     * Mostrar las notificaciones del administrador autenticado.
     */
    public function index(): View
    {
        $notificaciones = Notificacion::where('user_id', Auth::id())
            ->whereIn('evento', [
                'comprobante_enviado',
                'nueva_calificacion',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $noLeidas = $notificaciones
            ->where('leido', false)
            ->count();

        return view(
            'admin.notificaciones.index',
            compact('notificaciones', 'noLeidas')
        );
    }

    /**
     * Marcar una notificación como leída.
     */
    public function marcarLeida(int $id): RedirectResponse
    {
        $notificacion = Notificacion::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notificacion->update([
            'leido' => true,
        ]);

        return back();
    }

    /**
     * Marcar todas las notificaciones como leídas.
     */
    public function marcarTodasLeidas(): RedirectResponse
    {
        Notificacion::where('user_id', Auth::id())
            ->whereIn('evento', [
                'comprobante_enviado',
                'nueva_calificacion',
            ])
            ->where('leido', false)
            ->update([
                'leido' => true,
            ]);

        return back();
    }
}
