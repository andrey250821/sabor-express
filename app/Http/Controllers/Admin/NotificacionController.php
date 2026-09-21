<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\JsonResponse;
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
                'comprobante_en_revision',
                'nueva_calificacion',
            ])
            ->with([
                'pedido.user',
                'pedido.comprobantePago',
                'pedido.calificaciones.user',
                'pedido.calificaciones.producto',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Evitar mostrar dos veces la misma calificación
    |--------------------------------------------------------------------------
    |
    | La tabla notificaciones actualmente guarda pedido_id,
    | pero no guarda calificacion_id.
    |
    | Por eso buscamos la calificación correspondiente a cada
    | notificación y usamos su ID como referencia para evitar
    | duplicados visuales.
    |
    */

        $idsCalificacionesMostradas = [];

        $notificaciones = $notificaciones->filter(function ($notificacion) use (&$idsCalificacionesMostradas) {

            if ($notificacion->evento !== 'nueva_calificacion') {
                return true;
            }

            $calificacion = $notificacion->pedido?->calificaciones
                ?->filter(function ($calificacion) use ($notificacion) {

                    return $calificacion->created_at <= $notificacion->created_at;
                })
                ->sortByDesc('created_at')
                ->first();

            /*
        | Si no encontramos una calificación relacionada,
        | dejamos la notificación visible.
        */
            if (!$calificacion) {
                return true;
            }

            /*
        | Si esta calificación ya fue representada por otra
        | notificación, ocultamos esta duplicada.
        */
            if (in_array($calificacion->id, $idsCalificacionesMostradas)) {
                return false;
            }

            $idsCalificacionesMostradas[] = $calificacion->id;

            /*
        | Guardamos la calificación encontrada temporalmente
        | para utilizarla directamente en la vista.
        */
            $notificacion->calificacionRelacionada = $calificacion;

            return true;
        })
            ->values();

        $noLeidas = $notificaciones
            ->where('leido', false)
            ->count();

        $comprobantes = $notificaciones
            ->where('evento', 'comprobante_en_revision')
            ->count();

        $calificaciones = $notificaciones
            ->where('evento', 'nueva_calificacion')
            ->count();

        /*
    |--------------------------------------------------------------------------
    | Agrupar por fecha
    |--------------------------------------------------------------------------
    */

        $notificacionesAgrupadas = $notificaciones
            ->groupBy(function ($notificacion) {

                if ($notificacion->created_at->isToday()) {
                    return 'Hoy';
                }

                if ($notificacion->created_at->isYesterday()) {
                    return 'Ayer';
                }

                return $notificacion->created_at->format('d/m/Y');
            });

        return view(
            'admin.notificaciones.index',
            compact(
                'notificaciones',
                'noLeidas',
                'comprobantes',
                'calificaciones',
                'notificacionesAgrupadas'
            )
        );
    }

    /**
     * Marcar una notificación como leída.
     */
    public function marcarLeida(int $id): JsonResponse|RedirectResponse
    {
        $notificacion = Notificacion::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notificacion->update([
            'leido' => true,
        ]);

        if (request()->expectsJson()) {
            $noLeidas = Notificacion::where('user_id', Auth::id())
                ->whereIn('evento', [
                    'comprobante_en_revision',
                    'nueva_calificacion',
                ])
                ->where('leido', false)
                ->count();

            return response()->json([
                'success' => true,
                'id' => $notificacion->id,
                'noLeidas' => $noLeidas,
            ]);
        }

        return back();
    }

    /**
     * Marcar todas las notificaciones como leídas.
     */
    public function marcarTodasLeidas(): JsonResponse|RedirectResponse
    {
        Notificacion::where('user_id', Auth::id())
            ->whereIn('evento', [
                'comprobante_en_revision',
                'nueva_calificacion',
            ])
            ->where('leido', false)
            ->update([
                'leido' => true,
            ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'noLeidas' => 0,
            ]);
        }

        return back();
    }
}
