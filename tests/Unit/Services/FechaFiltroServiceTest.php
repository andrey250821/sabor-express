<?php

namespace Tests\Unit\Services;

use App\Services\FechaFiltroService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tests\TestCase;

class FechaFiltroServiceTest extends TestCase
{
    public function test_sin_fecha_seleccionada_devuelve_hoy(): void
    {
        $service = new FechaFiltroService();

        $fecha = $service->resolver(
            Request::create('/test', 'GET')
        );

        $this->assertSame(
            Carbon::now(FechaFiltroService::TIMEZONE)
                ->startOfDay()
                ->toDateString(),
            $fecha->toDateString()
        );
    }

    public function test_ayer_es_una_fecha_valida(): void
    {
        $service = new FechaFiltroService();

        $ayer = Carbon::now(FechaFiltroService::TIMEZONE)
            ->subDay()
            ->toDateString();

        $fecha = $service->resolver(
            Request::create(
                '/test?fecha=' . $ayer,
                'GET'
            )
        );

        $this->assertSame(
            $ayer,
            $fecha->toDateString()
        );
    }

    public function test_una_fecha_futura_vuelve_a_hoy(): void
    {
        $service = new FechaFiltroService();

        $futuro = Carbon::now(FechaFiltroService::TIMEZONE)
            ->addDay()
            ->toDateString();

        $fecha = $service->resolver(
            Request::create(
                '/test?fecha=' . $futuro,
                'GET'
            )
        );

        $this->assertSame(
            Carbon::now(FechaFiltroService::TIMEZONE)
                ->startOfDay()
                ->toDateString(),
            $fecha->toDateString()
        );
    }
}
