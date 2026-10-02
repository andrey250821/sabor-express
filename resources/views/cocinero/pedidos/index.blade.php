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

<div class="container-fluid cocinero-pedidos">

    {{-- CABECERA --}}
    <div class="row align-items-center g-3 mb-4">

        <div class="col-12 col-lg">
            <div class="cocinero-section-intro">
                <div class="cocinero-section-icon">
                    <i class="bi bi-bag-check-fill"></i>
                </div>

                <div>
                    <h2>Pedidos de cocina</h2>
                    <p>
                        Los pedidos pagados se organizan en orden de llegada.
                        Al comenzar uno, pasa a tu preparación y deja de estar disponible
                        para los demás cocineros.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-auto">
            <a href="{{ route('cocinero.dashboard') }}"
                class="btn cocinero-btn-secondary w-100">
                <i class="bi bi-grid-1x2-fill"></i>
                Dashboard
            </a>
        </div>

    </div>


    {{-- RESUMEN --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cocinero.pedidos.index', ['seccion' => 'pendientes']) }}"
                class="text-decoration-none">
                <div class="pedido-stat-card pendiente cocinero-ripple-target">
                    <div class="pedido-stat-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="pedido-stat-content">
                        <span>En cola</span>
                        <strong>{{ $cantidadPendientes }}</strong>
                        <small>Esperando preparación</small>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cocinero.pedidos.index', ['seccion' => 'preparando']) }}"
                class="text-decoration-none">
                <div class="pedido-stat-card preparando cocinero-ripple-target">
                    <div class="pedido-stat-icon">
                        <i class="bi bi-fire"></i>
                    </div>
                    <div class="pedido-stat-content">
                        <span>Preparando</span>
                        <strong>{{ $cantidadPreparando }}</strong>
                        <small>Pedidos asignados a ti</small>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cocinero.pedidos.index', ['seccion' => 'listos']) }}"
                class="text-decoration-none">
                <div class="pedido-stat-card listo cocinero-ripple-target">
                    <div class="pedido-stat-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="pedido-stat-content">
                        <span>Listos</span>
                        <strong>{{ $cantidadListos }}</strong>
                        <small>Terminados por cocina</small>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="pedido-stat-card total">
                <div class="pedido-stat-icon">
                    <i class="bi bi-collection-fill"></i>
                </div>
                <div class="pedido-stat-content">
                    <span>Activos</span>
                    <strong>{{ $totalActivos }}</strong>
                    <small>Movimiento actual</small>
                </div>
            </div>
        </div>

    </div>


    {{-- SECCIONES --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a
            href="{{ route('cocinero.pedidos.index', ['seccion' => 'pendientes']) }}"
            class="btn {{ $seccion === 'pendientes' ? 'cocinero-btn-primary' : 'cocinero-btn-secondary' }}">
            <i class="bi bi-hourglass-split"></i>
            En cola
            <span class="badge rounded-pill text-bg-dark">{{ $cantidadPendientes }}</span>
        </a>

        <a
            href="{{ route('cocinero.pedidos.index', ['seccion' => 'preparando']) }}"
            class="btn {{ $seccion === 'preparando' ? 'cocinero-btn-primary' : 'cocinero-btn-secondary' }}">
            <i class="bi bi-fire"></i>
            Preparando
            <span class="badge rounded-pill text-bg-dark">{{ $cantidadPreparando }}</span>
        </a>

        <a
            href="{{ route('cocinero.pedidos.index', ['seccion' => 'listos']) }}"
            class="btn {{ $seccion === 'listos' ? 'cocinero-btn-primary' : 'cocinero-btn-secondary' }}">
            <i class="bi bi-check-circle"></i>
            Listos
            <span class="badge rounded-pill text-bg-dark">{{ $cantidadListos }}</span>
        </a>
    </div>


    @if($seccion === 'pendientes')

        <section class="cocinero-orders-card mb-4">

            <div class="cocinero-orders-header">
                <div>
                    <h3>
                        <i class="bi bi-hourglass-split me-2"></i>
                        Cola de preparación
                    </h3>
                    <span>
                        Solo aparece aquí el pedido pagado que todavía no ha comenzado a prepararse.
                    </span>
                </div>

                <span class="cocinero-orders-count">
                    {{ $cantidadPendientes }}
                    {{ $cantidadPendientes == 1 ? 'pedido' : 'pedidos' }}
                </span>
            </div>

            @if($cantidadPendientes > 0)

                @foreach($pendientes as $pedido)

                    <div
                        class="cocinero-order-item pedido-card-clickable cocinero-ripple-target"
                        data-pedido-url="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                        tabindex="0"
                        role="link"
                        aria-label="Ver pedido #{{ $pedido->id }}">

                        <div class="cocinero-order-number">
                            #{{ $pedido->id }}
                        </div>

                        <div class="cocinero-order-info">
                            <div class="cocinero-order-title">
                                <strong>{{ $pedido->user->name ?? 'Cliente' }}</strong>

                                <span class="badge text-bg-warning">
                                    <i class="bi bi-clock-fill me-1"></i>
                                    En cola
                                </span>
                            </div>

                            <div class="cocinero-order-meta">
                                <span>
                                    <i class="bi bi-calendar3"></i>
                                    {{ $pedido->created_at?->format('d/m/Y H:i') }}
                                </span>
                                <span data-order-time="{{ $pedido->created_at?->timestamp ?? '' }}">
                                    <i class="bi bi-stopwatch"></i>
                                    Calculando...
                                </span>
                                <span>
                                    <i class="bi bi-basket-fill"></i>
                                    {{ $pedido->detallePedidos->sum('cantidad') }} productos
                                </span>
                            </div>
                        </div>

                        <div class="cocinero-order-total">
                            <small>Total</small>
                            <strong>Bs {{ number_format($pedido->total ?? 0, 2) }}</strong>
                        </div>

                        <div class="cocinero-order-actions">
                            <form
                                method="POST"
                                action="{{ route('cocinero.pedidos.preparar', $pedido->id) }}"
                                data-confirm="¿Comenzar a preparar el pedido #{{ $pedido->id }}? Se retirará de la cola para los demás cocineros."
                                data-loading>
                                @csrf
                                @method('PUT')

                                <button type="submit" class="btn cocinero-btn-preparar">
                                    <i class="bi bi-fire"></i>
                                    Preparar
                                </button>
                            </form>
                        </div>

                    </div>

                @endforeach

            @else

                <div class="cocinero-empty-state">
                    <div class="cocinero-empty-icon">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <strong>Cocina al día</strong>
                    <span>No hay pedidos pagados esperando preparación.</span>
                </div>

            @endif

        </section>

    @elseif($seccion === 'preparando')

        <section class="cocinero-orders-card mb-4">

            <div class="cocinero-orders-header">
                <div>
                    <h3>
                        <i class="bi bi-fire me-2"></i>
                        Pedidos en preparación
                    </h3>
                    <span>
                        Estos pedidos ya están bajo tu responsabilidad.
                    </span>
                </div>

                <span class="cocinero-orders-count">
                    {{ $cantidadPreparando }}
                    {{ $cantidadPreparando == 1 ? 'pedido' : 'pedidos' }}
                </span>
            </div>

            @if($cantidadPreparando > 0)

                @foreach($preparando as $pedido)

                    <div
                        class="cocinero-order-item pedido-card-clickable cocinero-ripple-target"
                        data-pedido-url="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                        tabindex="0"
                        role="link"
                        aria-label="Ver pedido #{{ $pedido->id }}">

                        <div class="cocinero-order-number">
                            #{{ $pedido->id }}
                        </div>

                        <div class="cocinero-order-info">
                            <div class="cocinero-order-title">
                                <strong>{{ $pedido->user->name ?? 'Cliente' }}</strong>

                                <span class="badge text-bg-danger">
                                    <i class="bi bi-fire me-1"></i>
                                    Preparando
                                </span>
                            </div>

                            <div class="cocinero-order-meta">
                                <span>
                                    <i class="bi bi-calendar3"></i>
                                    {{ $pedido->created_at?->format('d/m/Y H:i') }}
                                </span>
                                <span data-order-time="{{ $pedido->created_at?->timestamp ?? '' }}">
                                    <i class="bi bi-stopwatch"></i>
                                    Calculando...
                                </span>
                                <span>
                                    <i class="bi bi-basket-fill"></i>
                                    {{ $pedido->detallePedidos->sum('cantidad') }} productos
                                </span>
                            </div>
                        </div>

                        <div class="cocinero-order-total">
                            <small>Total</small>
                            <strong>Bs {{ number_format($pedido->total ?? 0, 2) }}</strong>
                        </div>

                        <div class="cocinero-order-actions">
                            <form
                                method="POST"
                                action="{{ route('cocinero.pedidos.listo', $pedido->id) }}"
                                data-confirm="¿Marcar el pedido #{{ $pedido->id }} como listo?"
                                data-loading>
                                @csrf
                                @method('PUT')

                                <button type="submit" class="btn cocinero-btn-listo">
                                    <i class="bi bi-check-lg"></i>
                                    Marcar listo
                                </button>
                            </form>
                        </div>

                    </div>

                @endforeach

            @else

                <div class="cocinero-empty-state">
                    <div class="cocinero-empty-icon">
                        <i class="bi bi-fire"></i>
                    </div>
                    <strong>No tienes pedidos en preparación</strong>
                    <span>Cuando tomes un pedido aparecerá aquí.</span>
                </div>

            @endif

        </section>

    @elseif($seccion === 'listos')

        <section class="cocinero-orders-card mb-4">

            <div class="cocinero-orders-header">

                <div>
                    <h3>
                        <i class="bi bi-check-circle me-2"></i>
                        Pedidos terminados
                    </h3>
                    <span>
                        Pedidos finalizados por cocina. Puedes consultar el historial por fecha.
                    </span>
                </div>

                <form
                    method="GET"
                    action="{{ route('cocinero.pedidos.index') }}"
                    class="d-flex flex-wrap align-items-center gap-2">

                    <input type="hidden" name="seccion" value="listos">

                    <label for="periodo_listos" class="visually-hidden">
                        Filtrar periodo
                    </label>

                    <select
                        id="periodo_listos"
                        name="periodo_listos"
                        class="form-select form-select-sm"
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
                            Últimos 7 días
                        </option>
                    </select>

                </form>

            </div>

            @if($cantidadListos > 0)

                @foreach($listos as $pedido)

                    @php
                        $estadoHistorico = strtolower($pedido->estado ?? '');
                    @endphp

                    <div
                        class="cocinero-order-item pedido-card-clickable cocinero-ripple-target"
                        data-pedido-url="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                        tabindex="0"
                        role="link"
                        aria-label="Ver pedido #{{ $pedido->id }}">

                        <div class="cocinero-order-number">
                            #{{ $pedido->id }}
                        </div>

                        <div class="cocinero-order-info">

                            <div class="cocinero-order-title">
                                <strong>{{ $pedido->user->name ?? 'Cliente' }}</strong>

                                @switch($estadoHistorico)
                                    @case('listo')
                                        <span class="cocinero-order-status ready">
                                            <i class="bi bi-check-circle"></i>
                                            Listo
                                        </span>
                                        @break

                                    @case('asignado')
                                        <span class="cocinero-order-status ready">
                                            <i class="bi bi-bicycle"></i>
                                            Asignado
                                        </span>
                                        @break

                                    @case('en_camino')
                                        <span class="cocinero-order-status preparing">
                                            <i class="bi bi-truck"></i>
                                            En camino
                                        </span>
                                        @break

                                    @case('entregado')
                                        <span class="cocinero-order-status ready">
                                            <i class="bi bi-check2-all"></i>
                                            Entregado
                                        </span>
                                        @break

                                    @default
                                        <span class="cocinero-order-status pending">
                                            <i class="bi bi-info-circle"></i>
                                            {{ ucfirst(str_replace('_', ' ', $estadoHistorico)) }}
                                        </span>
                                @endswitch
                            </div>

                            <div class="cocinero-order-meta">
                                <span>
                                    <i class="bi bi-calendar3"></i>
                                    {{ $pedido->created_at?->format('d/m/Y H:i') }}
                                </span>
                                <span>
                                    <i class="bi bi-basket-fill"></i>
                                    {{ $pedido->detallePedidos->sum('cantidad') }} productos
                                </span>

                                @if(in_array($estadoHistorico, ['listo', 'asignado'], true))
                                    <span>
                                        <i class="bi bi-bicycle"></i>
                                        Listo para el siguiente paso
                                    </span>
                                @endif
                            </div>

                        </div>

                        <div class="cocinero-order-total">
                            <small>Total</small>
                            <strong>Bs {{ number_format($pedido->total ?? 0, 2) }}</strong>
                        </div>

                    </div>

                @endforeach

            @else

                <div class="cocinero-empty-state">
                    <div class="cocinero-empty-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <strong>No hay pedidos terminados en este periodo</strong>
                    <span>Prueba con otro periodo para consultar más historial.</span>
                </div>

            @endif

        </section>

    @endif

</div>

@endsection
