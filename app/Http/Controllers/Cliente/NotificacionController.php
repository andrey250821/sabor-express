<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    /**
     * Mostrar únicamente las notificaciones del cliente autenticado.
     */
    public function index(): View
    {
        $notificaciones = Notificacion::where('user_id', Auth::id())
            ->where('tipo', 'cliente')
            ->with([
                'pedido',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $noLeidas = $notificaciones
            ->where('leido', false)
            ->count();

        $notificacionesAgrupadas = $notificaciones->groupBy(function ($notificacion) {
            if ($notificacion->created_at->isToday()) {
                return 'Hoy';
            }

            if ($notificacion->created_at->isYesterday()) {
                return 'Ayer';
            }

            return $notificacion->created_at->format('d/m/Y');
        });

        return view(
            'cliente.notificaciones.index',
            compact(
                'notificaciones',
                'noLeidas',
                'notificacionesAgrupadas'
            )
        );
    }

    /**
     * Marcar una notificación propia como leída.
     */
    public function marcarLeida(int $id): RedirectResponse
    {
        Notificacion::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('tipo', 'cliente')
            ->update([
                'leido' => true,
            ]);

        return back();
    }

    /**
     * Marcar todas las notificaciones propias como leídas.
     */
    public function marcarTodasLeidas(): RedirectResponse
    {
        Notificacion::where('user_id', Auth::id())
            ->where('tipo', 'cliente')
            ->where('leido', false)
            ->update([
                'leido' => true,
            ]);

        return back();
    }
}
