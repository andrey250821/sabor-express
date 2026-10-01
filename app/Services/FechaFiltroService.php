<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;

class FechaFiltroService
{
    /**
     * Zona horaria utilizada para determinar el día de negocio.
     */
    public const TIMEZONE = 'America/La_Paz';

    /**
     * Cantidad de días visibles en la barra de fechas.
     */
    public const DEFAULT_DAYS = 14;

    /**
     * Obtener el inicio del día actual en Bolivia.
     */
    public function hoy(): Carbon
    {
        return Carbon::now(self::TIMEZONE)->startOfDay();
    }

    /**
     * Resolver la fecha solicitada.
     *
     * Por defecto siempre devuelve HOY.
     * Solo permite fechas entre hoy y los días recientes configurados.
     */
    public function resolver(
        Request $request,
        int $dias = self::DEFAULT_DAYS
    ): Carbon {
        $hoy = $this->hoy();
        $minimo = $hoy->copy()->subDays(max(1, $dias) - 1);

        if (!$request->filled('fecha')) {
            return $hoy;
        }

        $valor = trim((string) $request->input('fecha'));

        try {
            $fecha = Carbon::createFromFormat(
                'Y-m-d',
                $valor,
                self::TIMEZONE
            )->startOfDay();
        } catch (\Throwable) {
            return $hoy;
        }

        if (
            $fecha->format('Y-m-d') !== $valor ||
            $fecha->greaterThan($hoy) ||
            $fecha->lessThan($minimo)
        ) {
            return $hoy;
        }

        return $fecha;
    }

    /**
     * Convertir un día local de Bolivia a sus límites UTC para consultar la BD.
     */
    public function rangoUtc(Carbon $fecha): array
    {
        $fechaLocal = $fecha->copy()
            ->timezone(self::TIMEZONE)
            ->startOfDay();

        return [
            $fechaLocal->copy()->startOfDay()->utc(),
            $fechaLocal->copy()->endOfDay()->utc(),
        ];
    }
}
