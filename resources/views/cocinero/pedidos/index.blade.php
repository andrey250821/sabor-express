@extends('layouts.cocinero')

@section('title', 'Pedidos')
@section('section', 'Gestión de cocina')
@section('heading', 'Pedidos')

@section('content')

@php
    $pendientes = $pendientes ?? collect();
    $preparando = $preparando ?? collect();
    $listos = $listos ?? collect();

    $cantidadPendientes = $pendientes->count();
    $cantidadPreparando = $preparando->count();
    $cantidadListos = $listos->count();
@endphp

<div class="container-fluid px-0 cocinero-pedidos">

    {{-- CABECERA --}}
    <section class="cocinero-page-intro cocinero-reveal mb-4">

        <div class="cocinero-page-intro-icon">
            <i class="bi bi-bag-check-fill"></i>
        </div>

        <div class="cocinero-page-intro-content">

            <span class="cocinero-kicker">
                <i class="bi bi-fire"></i>
                Gestión de cocina
            </span>

            <h2>
                Pedidos
            </h2>

            <p>
                Atiende los pedidos pagados respetando el orden de llegada.
                Una vez que comienzas una preparación, el pedido queda asociado a tu cuenta.
            </p>
        </div>

        <a
            href="{{ route('cocinero.dashboard') }}"
            class="cocinero-btn-secondary js-ripple cocinero-page-intro-button">

            <i class="bi bi-grid-1x2-fill"></i>
            Dashboard
        </a>
    </section>


    {{-- SELECTOR VERTICAL DE ESTADOS --}}
    <section class="cocinero-state-panel cocinero-reveal mb-4">

        <div class="cocinero-state-panel-heading">

            <div>
                <span class="cocinero-panel-kicker">
                    FLUJO DE COCINA
                </span>

                <h2>
                    ¿Qué quieres revisar?
                </h2>

                <p>
                    Selecciona una sección. Los pedidos aparecerán debajo.
                </p>
            </div>
        </div>

        <div class="cocinero-state-navigation">

            <a
                href="{{ route('cocinero.pedidos.index', ['seccion' => 'pendientes']) }}"
                class="cocinero-state-link {{ $seccion === 'pendientes' ? 'active pendiente' : '' }}">

                <span class="cocinero-state-link-icon">
                    <i class="bi bi-hourglass-split"></i>
                </span>

                <span class="cocinero-state-link-content">
                    <strong>Cola de preparación</strong>
                    <small>Pedidos pagados que esperan entrar a cocina</small>
                </span>

                <span class="cocinero-state-link-count">
                    {{ $cantidadPendientes }}
                </span>

                <i class="bi bi-chevron-right cocinero-state-link-arrow"></i>
            </a>


            <a
                href="{{ route('cocinero.pedidos.index', ['seccion' => 'preparando']) }}"
                class="cocinero-state-link {{ $seccion === 'preparando' ? 'active preparando' : '' }}">

                <span class="cocinero-state-link-icon">
                    <i class="bi bi-fire"></i>
                </span>

                <span class="cocinero-state-link-content">
                    <strong>Pedidos en preparación</strong>
                    <small>Pedidos que estás preparando en este momento</small>
                </span>

                <span class="cocinero-state-link-count">
                    {{ $cantidadPreparando }}
                </span>

                <i class="bi bi-chevron-right cocinero-state-link-arrow"></i>
            </a>


            <a
                href="{{ route('cocinero.pedidos.index', ['seccion' => 'listos']) }}"
                class="cocinero-state-link {{ $seccion === 'listos' ? 'active listos' : '' }}">

                <span class="cocinero-state-link-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </span>

                <span class="cocinero-state-link-content">
                    <strong>Listos e historial</strong>
                    <small>Pedidos terminados y seguimiento del siguiente paso</small>
                </span>

                <span class="cocinero-state-link-count">
                    {{ $cantidadListos }}
                </span>

                <i class="bi bi-chevron-right cocinero-state-link-arrow"></i>
            </a>

        </div>
    </section>


    {{-- COLA --}}
    @if($seccion === 'pendientes')

        <section class="cocinero-orders-card cocinero-reveal">

            <div class="cocinero-orders-header">

                <div class="cocinero-orders-header-main">

                    <span class="cocinero-section-eyebrow">
                        COLA ACTUAL
                    </span>

                    <h3>
                        <i class="bi bi-hourglass-split"></i>
                        Cola de preparación
                    </h3>

                    <span>
                        Los pedidos pagados se atienden de más antiguo a más reciente.
                    </span>
                </div>

                <span class="cocinero-orders-count">
                    {{ $cantidadPendientes }}
                    {{ $cantidadPendientes === 1 ? 'pedido' : 'pedidos' }}
                </span>
            </div>


            @if($cantidadPendientes > 0)

                <div class="cocinero-orders-list">

                    @foreach($pendientes as $pedido)

                        <article
                            class="cocinero-order-item is-clickable"
                            data-order-url="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                            tabindex="0"
                            role="link">

                            <div class="cocinero-order-topline">

                                <div class="cocinero-order-number">
                                    <span>#</span>
                                    <strong>{{ $pedido->id }}</strong>
                                </div>

                                <div class="cocinero-order-title">

                                    <div class="cocinero-order-title-main">
                                        <h4>
                                            Pedido #{{ $pedido->id }}
                                        </h4>

                                        <span class="pedido-status pendiente">
                                            <i class="bi bi-clock-fill"></i>
                                            En cola
                                        </span>
                                    </div>

                                    <div class="cocinero-order-meta">

                                        <span>
                                            <i class="bi bi-person-fill"></i>
                                            {{ $pedido->user->name ?? 'Cliente' }}
                                        </span>

                                        @if($pedido->user?->email)
                                            <span>
                                                <i class="bi bi-envelope"></i>
                                                {{ $pedido->user->email }}
                                            </span>
                                        @endif

                                        @if($pedido->created_at)
                                            <span>
                                                <i class="bi bi-clock"></i>
                                                {{ $pedido->created_at->format('d/m/Y H:i') }}
                                            </span>
                                        @endif

                                        <span>
                                            <i class="bi bi-basket-fill"></i>
                                            {{ $pedido->detallePedidos->count() }}
                                            {{ $pedido->detallePedidos->count() === 1 ? 'producto' : 'productos' }}
                                        </span>
                                    </div>
                                </div>
                            </div>


                            <div class="cocinero-order-footer">

                                <div class="cocinero-order-total">
                                    <span>Total</span>
                                    <strong>
                                        Bs {{ number_format($pedido->total ?? 0, 2) }}
                                    </strong>
                                </div>

                                <div class="cocinero-order-actions">

                                <a
                                    href="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                                    class="btn cocinero-btn-detalles js-ripple"
                                    onclick="event.stopPropagation()">
                                    <i class="bi bi-eye-fill"></i>
                                    <span>Ver detalles</span>
                                </a>

                                    <form
                                        method="POST"
                                        action="{{ route('cocinero.pedidos.preparar', $pedido->id) }}"
                                        data-confirm="¿Comenzar a preparar el pedido #{{ $pedido->id }}? Esta acción lo asignará a tu cocina."
                                        data-loading-text="Preparando...">

                                        @csrf
                                        @method('PUT')

                                        <button
                                            type="submit"
                                            class="btn cocinero-btn-preparar js-ripple">

                                            <i class="bi bi-fire"></i>
                                            <span>Preparar pedido</span>
                                        </button>
                                    </form>
                                </div>

                                <i class="bi bi-arrow-right cocinero-order-arrow"></i>
                            </div>

                        </article>

                    @endforeach

                    @endforeach

                </div>

            @elseinero-empty-state">

                    <div class="cocinero-empty-icon">
                        <i class="bi bi-check2-circle"></i>
                    </div>

                    <h3>Cola vacía</h3>

                    <p>
                        No hay pedidos pagados esperando preparación.
                    </p>
                </div>

            @endif

        </section>


    {{-- PREPARANDO --}}
    @elseif($seccion === 'preparando')

        <section class="cocinero-orders-card cocinero-reveal">

            <div class="cocinero-orders-header">

                <div class="cocinero-orders-header-main">

                    <span class="cocinero-section-eyebrow">
                        TRABAJO ACTUAL
                    </span>

                    <h3>
                        <i class="bi bi-fire"></i>
                        En preparación
                    </h3>

                    <span>
                        Aquí aparecen únicamente los pedidos que comenzaste a preparar.
                    </span>

                </div>

                <span class="cocinero-orders-count">
                    {{ $cantidadPreparando }}
                    {{ $cantidadPreparando === 1 ? 'pedido' : 'pedidos' }}
                </span>

            </div>


            @if($cantidadPreparando > 0)

                <div class="cocinero-orders-list">

                    @foreach($preparando as $pedido)

                        <article
                            class="cocinero-order-item is-clickable"
                            data-order-url="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                            tabindex="0"
                            role="link">

                            <div class="cocinero-order-topline">

                                <div class="cocinero-order-number">
                                    <span>#</span>
                                    <strong>{{ $pedido->id }}</strong>
                                </div>

                                <div class="cocinero-order-title">

                                    <div class="cocinero-order-title-main">

                                        <h4>
                                            Pedido #{{ $pedido->id }}
                                        </h4>

                                        <span class="pedido-status preparando">
                                            <i class="bi bi-fire"></i>
                                            Preparando
                                        </span>

                                    </div>

                                    <div class="cocinero-order-meta">

                                        <span>
                                            <i class="bi bi-person-fill"></i>
                                            {{ $pedido->user->name ?? 'Cliente' }}
                                        </span>

                                        @if($pedido->user?->email)
                                            <span>
                                                <i class="bi bi-envelope"></i>
                                                {{ $pedido->user->email }}
                                            </span>
                                        @endif

                                        @if($pedido->created_at)
                                            <span>
                                                <i class="bi bi-clock"></i>
                                                {{ $pedido->created_at->format('d/m/Y H:i') }}
                                            </span>
                                        @endif

                                        <span>
                                            <i class="bi bi-person-check-fill"></i>
                                            Asignado a ti
                                        </span>

                                    </div>
                                </div>
                            </div>


                            <div class="cocinero-order-footer">

                                <div class="cocinero-order-total">
                                    <span>Total</span>
                                    <strong>
                                        Bs {{ number_format($pedido->total ?? 0, 2) }}
                                    </strong>
                                </div>

                                <div class="cocinero-order-actions">

                                <a
                                    href="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                                    class="btn cocinero-btn-detalles js-ripple"
                                    onclick="event.stopPropagation()">
                                    <i class="bi bi-eye-fill"></i>
                                    <span>Ver detalles</span>
                                </a>

                                    <form
                                        method="POST"
                                        action="{{ route('cocinero.pedidos.listo', $pedido->id) }}"
                                        data-confirm="¿Marcar el pedido #{{ $pedido->id }} como listo?"
                                        data-loading-text="Finalizando...">

                                        @csrf
                                        @method('PUT')

                                        <button
                                            type="submit"
                                            class="btn cocinero-btn-listo js-ripple">

                                            <i class="bi bi-check-lg"></i>
                                            <span>Marcar como listo</span>
                                        </button>

                                    </form>

                                </div>

                                <i class="bi bi-arrow-right cocinero-order-arrow"></i>

                            </div>

                        </article>

                    @endforeach

                </div>

            @else

                <div class="cocinero-empty-state">

                    <div class="cocinero-empty-icon">
                        <i class="bi bi-fire"></i>
                    </div>

                    <h3>Ninguna preparación activa</h3>

                    <p>
                        Cuando tomes un pedido de la cola, aparecerá aquí.
                    </p>

                </div>

            @endif

        </section>


    {{-- SEGUIMIENTO --}}
    @elseif($seccion === 'listos')

        <section class="cocinero-orders-card cocinero-reveal">

            <div class="cocinero-orders-header">

                <div class="cocinero-orders-header-main">

                    <span class="cocinero-section-eyebrow">
                        SEGUIMIENTO
                    </span>

                    <h3>
                        <i class="bi bi-check-circle-fill"></i>
                        Pedidos terminados
                    </h3>

                    <span>
                        Consulta los pedidos que finalizaste y revisa en qué etapa se encuentran.
                    </span>

                </div>

                <div class="cocinero-orders-header-actions">

                    <span class="cocinero-orders-count">
                        {{ $cantidadListos }}
                        {{ $cantidadListos === 1 ? 'pedido' : 'pedidos' }}
                    </span>

                    <div class="cocinero-history-filters">

                        <span class="cocinero-history-label">
                            <i class="bi bi-clock-history"></i>
                            Historial:
                        </span>

                        @php
                            $filtrosHistorial = [
                                'hoy' => 'Hoy',
                                'ayer' => 'Ayer',
                                'anteayer' => 'Anteayer',
                                'semana' => 'Últimos 7 días',
                            ];
                        @endphp

                        @foreach($filtrosHistorial as $valor => $texto)

                            <a
                                href="{{ route('cocinero.pedidos.index', [
                                    'seccion' => 'listos',
                                    'periodo_listos' => $valor,
                                ]) }}"
                                class="btn btn-sm js-ripple {{ $periodoListos === $valor
                                    ? 'btn-dark'
                                    : 'btn-outline-secondary' }}">

                                {{ $texto }}

                            </a>

                        @endforeach

                    </div>

                </div>
            </div>


            {{-- PEDIDOS DE SEGUIMIENTO DEBAJO DEL ENCABEZADO --}}
            @if($cantidadListos > 0)

                @php
                    $listosAgrupados = $listosAgrupados ?? $listos->groupBy(function ($pedido) {
                        $fecha = $pedido->fecha_listo ?? $pedido->created_at;

                        return $fecha
                            ? $fecha->copy()->timezone('America/La_Paz')->format('Y-m-d')
                            : 'sin-fecha';
                    });
                @endphp

                <div class="cocinero-orders-list">

                    @foreach($listosAgrupados as $fechaClave => $pedidosDelDia)

                        @php
                            $fechaGrupo = $fechaClave !== 'sin-fecha'
                                ? \Carbon\Carbon::createFromFormat(
                                    'Y-m-d',
                                    $fechaClave,
                                    'America/La_Paz'
                                )
                                : null;

                            $hoyLocal = \Carbon\Carbon::now('America/La_Paz')->startOfDay();

                            $etiquetaFecha = match (true) {
                                $fechaGrupo?->isSameDay($hoyLocal) => 'Hoy',
                                $fechaGrupo?->isSameDay($hoyLocal->copy()->subDay()) => 'Ayer',
                                $fechaGrupo?->isSameDay($hoyLocal->copy()->subDays(2)) => 'Anteayer',
                                default => $fechaGrupo
                                    ? $fechaGrupo->format('d/m/Y')
                                    : 'Fecha no disponible',
                            };
                        @endphp

                        <div class="w-100 mb-3">

                            <div class="d-flex align-items-center gap-2 px-2 py-2 border-bottom mb-2">
                                <i class="bi bi-calendar3"></i>
                                <strong>{{ $etiquetaFecha }}</strong>

                                @if($fechaGrupo)
                                    <span class="text-muted">
                                        {{ $fechaGrupo->format('d/m/Y') }}
                                    </span>
                                @endif

                                <span class="badge bg-secondary ms-auto">
                                    {{ $pedidosDelDia->count() }}
                                    {{ $pedidosDelDia->count() === 1 ? 'pedido' : 'pedidos' }}
                                </span>
                            </div>

                        </div>

                        @foreach($pedidosDelDia as $pedido)

                        <article
                            class="cocinero-order-item is-clickable"
                            data-order-url="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                            tabindex="0"
                            role="link">

                            <div class="cocinero-order-topline">

                                <div class="cocinero-order-number">
                                    <span>#</span>
                                    <strong>{{ $pedido->id }}</strong>
                                </div>

                                <div class="cocinero-order-title">

                                    <div class="cocinero-order-title-main">

                                        <h4>
                                            Pedido #{{ $pedido->id }}
                                        </h4>

                                        @switch($pedido->estado)

                                            @case('asignado')
                                                <span class="pedido-status listo">
                                                    <i class="bi bi-bicycle"></i>
                                                    Asignado a Delivery
                                                </span>
                                                @break

                                            @case('en_camino')
                                                <span class="pedido-status preparando">
                                                    <i class="bi bi-truck"></i>
                                                    En camino
                                                </span>
                                                @break

                                            @case('entregado')
                                                <span class="pedido-status listo">
                                                    <i class="bi bi-check2-all"></i>
                                                    Entregado
                                                </span>
                                                @break

                                            @case('cancelado')
                                                <span class="pedido-status pendiente">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                    Cancelado
                                                </span>
                                                @break

                                            @default
                                                <span class="pedido-status listo">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    Listo
                                                </span>

                                        @endswitch

                                    </div>

                                    <div class="cocinero-order-meta">

                                        <span>
                                            <i class="bi bi-person-fill"></i>
                                            {{ $pedido->user->name ?? 'Cliente' }}
                                        </span>

                                        @if($pedido->user?->email)
                                            <span>
                                                <i class="bi bi-envelope"></i>
                                                {{ $pedido->user->email }}
                                            </span>
                                        @endif

                                        @if($pedido->created_at)
                                            <span>
                                                <i class="bi bi-clock"></i>
                                                {{ $pedido->created_at->format('d/m/Y H:i') }}
                                            </span>
                                        @endif

                                        <span>
                                            <i class="bi bi-basket-fill"></i>
                                            {{ $pedido->detallePedidos->count() }}
                                            {{ $pedido->detallePedidos->count() === 1 ? 'producto' : 'productos' }}
                                        </span>

                                    </div>
                                </div>
                            </div>


                            <div class="cocinero-order-footer">

                                <div class="cocinero-order-total">
                                    <span>Total</span>
                                    <strong>
                                        Bs {{ number_format($pedido->total ?? 0, 2) }}
                                    </strong>
                                </div>

                                <div class="cocinero-order-actions">

                                <a
                                    href="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                                    class="btn cocinero-btn-detalles js-ripple"
                                    onclick="event.stopPropagation()">
                                    <i class="bi bi-eye-fill"></i>
                                    <span>Ver detalles</span>
                                </a>

                                    @if(in_array($pedido->estado, ['listo', 'asignado'], true))

                                        <span class="cocinero-ready-label">
                                            <i class="bi bi-check-circle-fill"></i>
                                            {{ $pedido->estado === 'asignado'
                                                ? 'Asignado a Delivery'
                                                : 'Esperando siguiente paso' }}
                                        </span>

                                    @else

                                        <span class="cocinero-tracking-label">
                                            <i class="bi bi-clock-history"></i>
                                            Seguimiento
                                        </span>

                                    @endif

                                </div>

                                <i class="bi bi-arrow-right cocinero-order-arrow"></i>

                            </div>

                        </article>

                    @endforeach

                </div>

            @else

                <div class="cocinero-empty-state">

                    <div class="cocinero-empty-icon">
                        <i class="bi bi-check2-circle"></i>
                    </div>

                    <h3>No hay pedidos en este periodo</h3>

                    <p>
                        Cuando termines pedidos, aparecerán aquí para seguimiento e historial.
                    </p>

                </div>

            @endif

        </section>

    @endif

</div>

@endsection
