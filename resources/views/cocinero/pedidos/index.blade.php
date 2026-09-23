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

    {{-- =====================================================
         CABECERA
    ====================================================== --}}
    <div class="row align-items-center mb-4">
        <div class="col-12 col-lg">
            <div class="cocinero-section-intro">
                <div class="cocinero-section-icon">
                    <i class="bi bi-bag-check-fill"></i>
                </div>

                <div>
                    <h2>Pedidos de cocina</h2>
                    <p>
                        Los pedidos pagados aparecen en orden de llegada.
                        Al pulsar <strong>Preparar</strong>, el pedido pasa a tu sección
                        de preparación y deja de estar disponible para los demás cocineros.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
            <a href="{{ route('cocinero.dashboard') }}"
                class="btn cocinero-btn-secondary">
                <i class="bi bi-grid-1x2-fill me-1"></i>
                Dashboard
            </a>
        </div>
    </div>


    {{-- =====================================================
         RESUMEN DE ESTADOS
    ====================================================== --}}
    <div class="row g-3 mb-4">

        {{-- PENDIENTES EN COLA --}}
        <div class="col-12 col-sm-6 col-xl-3" id="pendientes">
            <a href="{{ route('cocinero.pedidos.index', ['seccion' => 'pendientes']) }}" class="text-decoration-none">
                <div class="pedido-stat-card pendiente">
                    <div class="pedido-stat-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>

                    <div class="pedido-stat-content">
                        <span>Pendientes</span>
                        <strong>{{ $cantidadPendientes }}</strong>
                        <small>Esperando preparación</small>
                    </div>
                </div>
            </a>
        </div>


        {{-- PREPARANDO --}}
        <div class="col-12 col-sm-6 col-xl-3" id="preparando">
            <a href="{{ route('cocinero.pedidos.index', ['seccion' => 'preparando']) }}" class="text-decoration-none">
                <div class="pedido-stat-card preparando">
                    <div class="pedido-stat-icon">
                        <i class="bi bi-fire"></i>
                    </div>

                    <div class="pedido-stat-content">
                        <span>Preparando</span>
                        <strong>{{ $cantidadPreparando }}</strong>
                        <small>Solo tus pedidos</small>
                    </div>
                </div>
            </a>
        </div>


        {{-- LISTOS --}}
        <div class="col-12 col-sm-6 col-xl-3" id="listos">
            <a href="{{ route('cocinero.pedidos.index', ['seccion' => 'listos']) }}" class="text-decoration-none">
                <div class="pedido-stat-card listo">
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


        {{-- TOTAL --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="pedido-stat-card total">
                <div class="pedido-stat-icon">
                    <i class="bi bi-collection-fill"></i>
                </div>

                <div class="pedido-stat-content">
                    <span>Resumen</span>
                    <strong>{{ $totalActivos }}</strong>
                    <small>Resumen general</small>
                </div>
            </div>
        </div>

    </div>


    @if($seccion === 'pendientes')

    {{-- =====================================================
         COLA DE PEDIDOS PENDIENTES
    ====================================================== --}}
    <section class="cocinero-orders-card mb-4" id="cola-pendientes">

        <div class="cocinero-orders-header">
            <div>
                <h3>
                    <i class="bi bi-hourglass-split me-2"></i>
                    Cola de pedidos pendientes
                </h3>

                <span>
                    Los pedidos aparecen en orden de llegada.
                    Cuando un cocinero pulsa Preparar, desaparece de esta cola para todos.
                </span>
            </div>

            <span class="cocinero-orders-count">
                {{ $cantidadPendientes }}
                {{ $cantidadPendientes == 1 ? 'pedido' : 'pedidos' }}
            </span>
        </div>

        @if($cantidadPendientes > 0)
            <div class="cocinero-orders-list">

                @foreach($pendientes as $pedido)
                    <article class="cocinero-order-item">

                        <div class="cocinero-order-number">
                            <span>#</span>
                            <strong>{{ $pedido->id }}</strong>
                        </div>

                        <div class="cocinero-order-info">
                            <div class="cocinero-order-title">
                                <h4>Pedido #{{ $pedido->id }}</h4>

                                <span class="pedido-status pendiente">
                                    <i class="bi bi-clock-fill"></i>
                                    Pendiente
                                </span>
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
                                    {{ $pedido->detallePedidos->count() == 1 ? 'producto' : 'productos' }}
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

                            <a href="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                                class="btn cocinero-btn-view">
                                <i class="bi bi-eye-fill"></i>
                                <span>Ver</span>
                            </a>

                            <form
                                method="POST"
                                action="{{ route('cocinero.pedidos.preparar', $pedido->id) }}">
                                @csrf
                                @method('PUT')

                                <button
                                    type="submit"
                                    class="btn cocinero-btn-preparar"
                                    onclick="return confirm('¿Comenzar a preparar el pedido #{{ $pedido->id }}? Al iniciar, dejará de estar disponible para los demás cocineros.')">
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


    @elseif($seccion === 'preparando')

    {{-- =====================================================
         MIS PEDIDOS EN PREPARACIÓN
    ====================================================== --}}
    <section class="cocinero-orders-card mb-4" id="mis-preparando">

        <div class="cocinero-orders-header">
            <div>
                <h3>
                    <i class="bi bi-fire me-2"></i>
                    Mis pedidos en preparación
                </h3>

                <span>
                    Solo aparecen los pedidos que tú comenzaste a preparar.
                </span>
            </div>

            <span class="cocinero-orders-count">
                {{ $cantidadPreparando }}
                {{ $cantidadPreparando == 1 ? 'pedido' : 'pedidos' }}
            </span>
        </div>

        @if($cantidadPreparando > 0)

            <div class="cocinero-orders-list">

                @foreach($preparando as $pedido)
                    <article class="cocinero-order-item">

                        <div class="cocinero-order-number">
                            <span>#</span>
                            <strong>{{ $pedido->id }}</strong>
                        </div>

                        <div class="cocinero-order-info">
                            <div class="cocinero-order-title">
                                <h4>Pedido #{{ $pedido->id }}</h4>

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

                                @if($pedido->created_at)
                                    <span>
                                        <i class="bi bi-clock"></i>
                                        {{ $pedido->created_at->format('d/m/Y H:i') }}
                                    </span>
                                @endif

                                <span class="text-success">
                                    <i class="bi bi-person-check-fill"></i>
                                    Pedido asignado a ti
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

                            <a href="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                                class="btn cocinero-btn-view">
                                <i class="bi bi-eye-fill"></i>
                                <span>Ver</span>
                            </a>

                            <form
                                method="POST"
                                action="{{ route('cocinero.pedidos.listo', $pedido->id) }}">
                                @csrf
                                @method('PUT')

                                <button
                                    type="submit"
                                    class="btn cocinero-btn-listo"
                                    onclick="return confirm('¿Marcar el pedido #{{ $pedido->id }} como listo?')">
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

                <h3>Ningún pedido en preparación</h3>

                <p>
                    Cuando comiences a preparar un pedido,
                    aparecerá aquí.
                </p>
            </div>
        @endif

    </section>


    @elseif($seccion === 'listos')

    {{-- =====================================================
         PEDIDOS LISTOS
    ====================================================== --}}
    <section class="cocinero-orders-card" id="pedidos-listos">

        <div class="cocinero-orders-header">
            <div>
                <h3>
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Pedidos listos
                </h3>

                <span>
                    Pedidos terminados por cocina y disponibles para el siguiente paso del flujo.
                </span>
            </div>

            <span class="cocinero-orders-count">
                {{ $cantidadListos }}
                {{ $cantidadListos == 1 ? 'pedido' : 'pedidos' }}
            </span>
        </div>

        @if($cantidadListos > 0)

            <div class="cocinero-orders-list">

                @foreach($listos as $pedido)
                    <article class="cocinero-order-item">

                        <div class="cocinero-order-number">
                            <span>#</span>
                            <strong>{{ $pedido->id }}</strong>
                        </div>

                        <div class="cocinero-order-info">
                            <div class="cocinero-order-title">
                                <h4>Pedido #{{ $pedido->id }}</h4>

                                <span class="pedido-status listo">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Listo
                                </span>
                            </div>

                            <div class="cocinero-order-meta">
                                <span>
                                    <i class="bi bi-person-fill"></i>
                                    {{ $pedido->user->name ?? 'Cliente' }}
                                </span>

                                <span>
                                    <i class="bi bi-basket-fill"></i>
                                    {{ $pedido->detallePedidos->count() }}
                                    {{ $pedido->detallePedidos->count() == 1 ? 'producto' : 'productos' }}
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

                            <a href="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                                class="btn cocinero-btn-view">
                                <i class="bi bi-eye-fill"></i>
                                <span>Ver</span>
                            </a>

                            <span class="cocinero-ready-label">
                                <i class="bi bi-check-circle-fill"></i>
                                Listo para delivery
                            </span>

                        </div>

                    </article>
                @endforeach

            </div>

        @else
            <div class="cocinero-empty-state">
                <div class="cocinero-empty-icon">
                    <i class="bi bi-check2-circle"></i>
                </div>

                <h3>Aún no hay pedidos listos</h3>

                <p>
                    Cuando termines una preparación, el pedido aparecerá aquí.
                </p>
            </div>
        @endif

    </section>

    @endif

</div>

@endsection
