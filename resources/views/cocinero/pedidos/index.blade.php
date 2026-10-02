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
    $totalActivos = $cantidadPendientes + $cantidadPreparando + $cantidadListos;
@endphp

<div class="container-fluid px-0 cocinero-pedidos">

    {{-- CABECERA --}}
    <div class="row align-items-center g-3 mb-4 cocinero-reveal">

        <div class="col-12 col-lg">

            <div class="d-flex align-items-start gap-3">

                <div class="cocinero-detail-card-icon flex-shrink-0">
                    <i class="bi bi-bag-check-fill"></i>
                </div>

                <div>
                    <span class="cocinero-kicker">
                        <i class="bi bi-fire"></i>
                        Gestión de cocina
                    </span>

                    <h2 class="h3 fw-bold text-white mb-2 mt-1">
                        Pedidos
                    </h2>

                    <p class="text-secondary mb-0 small">
                        Atiende los pedidos pagados respetando el orden de llegada.
                        Una vez que comienzas una preparación, el pedido queda asociado a tu cuenta.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-auto">

            <a
                href="{{ route('cocinero.dashboard') }}"
                class="btn cocinero-btn-secondary js-ripple w-100 w-lg-auto">

                <i class="bi bi-grid-1x2-fill me-1"></i>
                Dashboard
            </a>
        </div>
    </div>

    {{-- RESUMEN --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl-3 cocinero-reveal">

            <a
                href="{{ route('cocinero.pedidos.index', ['seccion' => 'pendientes']) }}"
                class="cocinero-stat-link">

                <div class="pedido-stat-card pendiente clickable">

                    <div class="d-flex align-items-center justify-content-between gap-3">

                        <div class="pedido-stat-content">
                            <span>Cola de cocina</span>
                            <strong>{{ $cantidadPendientes }}</strong>
                            <small>Pedidos pagados esperando preparación</small>
                        </div>

                        <div class="pedido-stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>

                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 cocinero-reveal">

            <a
                href="{{ route('cocinero.pedidos.index', ['seccion' => 'preparando']) }}"
                class="cocinero-stat-link">

                <div class="pedido-stat-card preparando clickable">

                    <div class="d-flex align-items-center justify-content-between gap-3">

                        <div class="pedido-stat-content">
                            <span>Preparando</span>
                            <strong>{{ $cantidadPreparando }}</strong>
                            <small>Pedidos que estás preparando</small>
                        </div>

                        <div class="pedido-stat-icon">
                            <i class="bi bi-fire"></i>
                        </div>
                    </div>

                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 cocinero-reveal">

            <a
                href="{{ route('cocinero.pedidos.index', ['seccion' => 'listos']) }}"
                class="cocinero-stat-link">

                <div class="pedido-stat-card listo clickable">

                    <div class="d-flex align-items-center justify-content-between gap-3">

                        <div class="pedido-stat-content">
                            <span>Listos</span>
                            <strong>{{ $cantidadListos }}</strong>
                            <small>Pedidos terminados por cocina</small>
                        </div>

                        <div class="pedido-stat-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                    </div>

                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 cocinero-reveal">

            <div class="pedido-stat-card total">

                <div class="d-flex align-items-center justify-content-between gap-3">

                    <div class="pedido-stat-content">
                        <span>Activos</span>
                        <strong>{{ $totalActivos }}</strong>
                        <small>Resumen del flujo actual</small>
                    </div>

                    <div class="pedido-stat-icon">
                        <i class="bi bi-collection-fill"></i>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- NAVEGACIÓN DE ESTADOS --}}
    <div class="cocinero-panel mb-4 cocinero-reveal">

        <div class="p-3">

            <div
                class="nav nav-pills nav-fill gap-2"
                aria-label="Secciones de pedidos">

                <a
                    href="{{ route('cocinero.pedidos.index', ['seccion' => 'pendientes']) }}"
                    class="nav-link {{ $seccion === 'pendientes' ? 'active' : '' }} rounded-3 text-start px-3 py-3">

                    <div class="fw-bold small">
                        <i class="bi bi-hourglass-split me-1"></i>
                        Cola
                    </div>

                    <small class="opacity-75">
                        {{ $cantidadPendientes }} pendientes
                    </small>
                </a>

                <a
                    href="{{ route('cocinero.pedidos.index', ['seccion' => 'preparando']) }}"
                    class="nav-link {{ $seccion === 'preparando' ? 'active' : '' }} rounded-3 text-start px-3 py-3">

                    <div class="fw-bold small">
                        <i class="bi bi-fire me-1"></i>
                        Preparando
                    </div>

                    <small class="opacity-75">
                        {{ $cantidadPreparando }} en cocina
                    </small>
                </a>

                <a
                    href="{{ route('cocinero.pedidos.index', ['seccion' => 'listos']) }}"
                    class="nav-link {{ $seccion === 'listos' ? 'active' : '' }} rounded-3 text-start px-3 py-3">

                    <div class="fw-bold small">
                        <i class="bi bi-check2-circle me-1"></i>
                        Listos
                    </div>

                    <small class="opacity-75">
                        {{ $cantidadListos }} en seguimiento
                    </small>
                </a>

            </div>
        </div>
    </div>

    {{-- COLA --}}
    @if($seccion === 'pendientes')

        <section class="cocinero-orders-card cocinero-reveal">

            <div class="cocinero-orders-header">

                <div>
                    <h3>
                        <i class="bi bi-hourglass-split me-2"></i>
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

                            <div class="cocinero-order-number">
                                <span>#</span>
                                <strong>{{ $pedido->id }}</strong>
                            </div>

                            <div class="cocinero-order-info">

                                <div class="cocinero-order-title">

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

                            <div class="cocinero-order-total">
                                <span>Total</span>
                                <strong>
                                    Bs {{ number_format($pedido->total ?? 0, 2) }}
                                </strong>
                            </div>

                            <div class="cocinero-order-actions">

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
                                        <span>Preparar</span>
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

            @else

                <div class="cocinero-empty-state">

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

                <div>
                    <h3>
                        <i class="bi bi-fire me-2"></i>
                        Mis preparaciones
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

                            <div class="cocinero-order-number">
                                <span>#</span>
                                <strong>{{ $pedido->id }}</strong>
                            </div>

                            <div class="cocinero-order-info">

                                <div class="cocinero-order-title">

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

                            <div class="cocinero-order-total">
                                <span>Total</span>
                                <strong>
                                    Bs {{ number_format($pedido->total ?? 0, 2) }}
                                </strong>
                            </div>

                            <div class="cocinero-order-actions">

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
                                        <span>Marcar listo</span>
                                    </button>
                                </form>
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

    {{-- LISTOS --}}
    @elseif($seccion === 'listos')

        <section class="cocinero-orders-card cocinero-reveal">

            <div class="cocinero-orders-header">

                <div>
                    <h3>
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Pedidos terminados
                    </h3>

                    <span>
                        Consulta los pedidos que finalizaste y su estado posterior.
                    </span>
                </div>

                <span class="cocinero-orders-count">
                    {{ $cantidadListos }}
                    {{ $cantidadListos === 1 ? 'pedido' : 'pedidos' }}
                </span>
            </div>

            <div class="cocinero-history-filter">

                <form
                    method="GET"
                    action="{{ route('cocinero.pedidos.index') }}"
                    class="row g-2 align-items-center">

                    <input
                        type="hidden"
                        name="seccion"
                        value="listos">

                    <div class="col-12 col-md-auto">

                        <label
                            for="periodo_listos"
                            class="form-label text-secondary small fw-semibold mb-0">

                            <i class="bi bi-calendar3 me-1"></i>
                            Historial
                        </label>
                    </div>

                    <div class="col-12 col-md-auto">

                        <select
                            id="periodo_listos"
                            name="periodo_listos"
                            class="form-select form-select-sm cocinero-select"
                            onchange="this.form.submit()">

                            <option value="hoy" {{ $periodoListos === 'hoy' ? 'selected' : '' }}>
                                Hoy
                            </option>

                            <option value="ayer" {{ $periodoListos === 'ayer' ? 'selected' : '' }}>
                                Ayer
                            </option>

                            <option value="anteayer" {{ $periodoListos === 'anteayer' ? 'selected' : '' }}>
                                Anteayer
                            </option>

                            <option value="semana" {{ $periodoListos === 'semana' ? 'selected' : '' }}>
                                Última semana
                            </option>
                        </select>
                    </div>
                </form>
            </div>

            @if($cantidadListos > 0)

                <div class="cocinero-orders-list">

                    @foreach($listos as $pedido)

                        <article
                            class="cocinero-order-item is-clickable"
                            data-order-url="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                            tabindex="0"
                            role="link">

                            <div class="cocinero-order-number">
                                <span>#</span>
                                <strong>{{ $pedido->id }}</strong>
                            </div>

                            <div class="cocinero-order-info">

                                <div class="cocinero-order-title">

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

                            <div class="cocinero-order-total">
                                <span>Total</span>
                                <strong>
                                    Bs {{ number_format($pedido->total ?? 0, 2) }}
                                </strong>
                            </div>

                            <div class="cocinero-order-actions">

                                @if(in_array($pedido->estado, ['listo', 'asignado'], true))

                                    <span class="cocinero-ready-label">
                                        <i class="bi bi-check-circle-fill"></i>
                                        {{ $pedido->estado === 'asignado'
                                            ? 'Asignado a Delivery'
                                            : 'Esperando siguiente paso' }}
                                    </span>

                                @else

                                    <span class="text-secondary small">
                                        <i class="bi bi-clock-history me-1"></i>
                                        Seguimiento
                                    </span>

                                @endif
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
                        Los pedidos que finalices aparecerán aquí para seguimiento.
                    </p>
                </div>

            @endif
        </section>

    @endif

</div>

@endsection
