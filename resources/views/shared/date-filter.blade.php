@php
    $zonaHoraria = 'America/La_Paz';

    $fechaBase = $fechaSeleccionada
        ?? ($fechaHistorial ?? null);

    if ($fechaBase instanceof \Carbon\Carbon) {
        $fechaBase = $fechaBase
            ->copy()
            ->timezone($zonaHoraria)
            ->startOfDay();
    } elseif ($fechaBase) {
        try {
            $fechaBase = \Carbon\Carbon::parse(
                $fechaBase,
                $zonaHoraria
            )->startOfDay();
        } catch (\Throwable) {
            $fechaBase = \Carbon\Carbon::today($zonaHoraria);
        }
    } else {
        $fechaBase = \Carbon\Carbon::today($zonaHoraria);
    }

    $hoyFiltro = \Carbon\Carbon::today($zonaHoraria);

    $diasFiltro = collect(range(0, 13))
        ->map(function (int $diasAtras) use ($hoyFiltro) {
            $fecha = $hoyFiltro->copy()->subDays($diasAtras);

            return [
                'fecha' => $fecha->toDateString(),
                'carbon' => $fecha,
                'etiqueta' => $fecha->isToday()
                    ? 'Hoy'
                    : ($fecha->isYesterday()
                        ? 'Ayer'
                        : ($diasAtras === 2
                            ? 'Anteayer'
                            : $fecha->format('d/m'))),
            ];
        });
@endphp

<div class="sabor-date-filter mb-4">
    <div class="sabor-date-filter-header">
        <div>
            <span class="sabor-date-filter-kicker">
                FILTRO POR FECHA
            </span>

            <strong>
                {{ $tituloFecha ?? 'Actividad por día' }}
            </strong>
        </div>

        <span class="sabor-date-filter-selected">
            {{ $fechaBase->format('d/m/Y') }}
        </span>
    </div>

    <div
        class="sabor-date-filter-track"
        aria-label="Seleccionar fecha">

        @foreach($diasFiltro as $dia)

            <a
                href="{{ request()->fullUrlWithQuery(['fecha' => $dia['fecha']]) }}"
                class="sabor-date-filter-day {{ $fechaBase->toDateString() === $dia['fecha'] ? 'active' : '' }}"
                aria-current="{{ $fechaBase->toDateString() === $dia['fecha'] ? 'date' : 'false' }}">

                <span>
                    {{ $dia['etiqueta'] }}
                </span>

                <strong>
                    {{ $dia['carbon']->format('d/m') }}
                </strong>

            </a>

        @endforeach

    </div>

    <small class="sabor-date-filter-help">
        Desliza horizontalmente para consultar ayer, anteayer y días anteriores.
        Por defecto se muestra la actividad de hoy.
    </small>
</div>

<style>
    .sabor-date-filter {
        background: rgba(255,255,255,.035);
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 18px;
        padding: 16px;
    }

    .sabor-date-filter-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .sabor-date-filter-header > div {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .sabor-date-filter-kicker {
        font-size: .72rem;
        letter-spacing: .12em;
        color: #9aa0ab;
        font-weight: 700;
    }

    .sabor-date-filter-selected {
        flex: 0 0 auto;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 999px;
        padding: 7px 12px;
        font-size: .82rem;
        font-weight: 700;
        color: #fff;
    }

    .sabor-date-filter-track {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding: 2px 2px 10px;
        scrollbar-width: thin;
        scroll-snap-type: x proximity;
    }

    .sabor-date-filter-track::-webkit-scrollbar {
        height: 7px;
    }

    .sabor-date-filter-day {
        flex: 0 0 auto;
        min-width: 92px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
        padding: 10px 12px;
        border-radius: 14px;
        border: 1px solid rgba(255,255,255,.09);
        background: rgba(255,255,255,.025);
        color: #d5d9e0;
        text-decoration: none;
        scroll-snap-align: start;
        transition: transform .15s ease, border-color .15s ease, background .15s ease;
    }

    .sabor-date-filter-day span {
        font-size: .76rem;
        font-weight: 700;
        color: #9aa0ab;
    }

    .sabor-date-filter-day strong {
        font-size: .96rem;
        color: #fff;
    }

    .sabor-date-filter-day:hover {
        transform: translateY(-1px);
        color: #fff;
        border-color: rgba(255,255,255,.2);
        background: rgba(255,255,255,.06);
    }

    .sabor-date-filter-day.active {
        border-color: #8b1e45;
        background: linear-gradient(145deg, rgba(139,30,69,.42), rgba(139,30,69,.18));
        box-shadow: 0 8px 20px rgba(139,30,69,.16);
    }

    .sabor-date-filter-day.active span {
        color: #f4b4cb;
    }

    .sabor-date-filter-help {
        display: block;
        color: #8c929e;
        margin-top: 2px;
    }

    @media (max-width: 576px) {
        .sabor-date-filter-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .sabor-date-filter-day {
            min-width: 84px;
        }
    }
</style>
