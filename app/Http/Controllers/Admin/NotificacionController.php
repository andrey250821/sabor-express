<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use App\Services\FechaFiltroService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    /**
     * Mostrar las notificaciones del administrador de la fecha seleccionada.
     */
    public function index(
        Request $request,
        FechaFiltroService $fechas
    ): View {
        $fechaSeleccionada = $fechas->resolver($request);

        [$inicioUtc, $finUtc] = $fechas->rangoUtc(
            $fechaSeleccionada
        );

        $notificaciones = Notificacion::where('user_id', Auth::id())
            ->vigentes()
            ->whereBetween('created_at', [$inicioUtc, $finUtc])
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
        | EVITAR DUPLICAR VISUALMENTE UNA MISMA CALIFICACIÓN
        |--------------------------------------------------------------------------
        */
        $idsCalificacionesMostradas = [];

        $notificaciones = $notificaciones
            ->filter(function ($notificacion) use (&$idsCalificacionesMostradas) {

                if ($notificacion->evento !== 'nueva_calificacion') {
                    return true;
                }

                $calificacion = $notificacion->pedido?->calificaciones
                    ?->filter(function ($calificacion) use ($notificacion) {
                        return $calificacion->created_at <= $notificacion->created_at;
                    })
                    ->sortByDesc('created_at')
                    ->first();

                if (!$calificacion) {
                    return true;
                }

                if (in_array(
                    $calificacion->id,
                    $idsCalificacionesMostradas,
                    true
                )) {
                    return false;
                }

                $idsCalificacionesMostradas[] = $calificacion->id;
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

        $notificacionesAgrupadas = $notificaciones
            ->groupBy(function ($notificacion) {
                return $notificacion->created_at
                    ->copy()
                    ->timezone(FechaFiltroService::TIMEZONE)
                    ->format('d/m/Y');
            });

        return view(
            'admin.notificaciones.index',
            compact(
                'notificaciones',
                'noLeidas',
                'comprobantes',
                'calificaciones',
                'notificacionesAgrupadas',
                'fechaSeleccionada'
            )
        );
    }

    /**
     * Marcar una notificación como leída.
     */
    public function marcarLeida(int $id): JsonResponse|RedirectResponse
    {
        $notificacion = Notificacion::where('id', $id)
            ->vigentes()
            ->where('user_id', Auth::id())
            ->whereIn('evento', [
                'comprobante_en_revision',
                'nueva_calificacion',
            ])
            ->firstOrFail();

        $notificacion->update([
            'leido' => true,
        ]);

        if (request()->expectsJson()) {
            $noLeidas = Notificacion::where('user_id', Auth::id())
                ->vigentes()
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
            ->vigentes()
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
