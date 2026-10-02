@extends('layouts.cocinero')

@section('title', 'Dashboard - Cocinero')
@section('section', 'Panel de cocina')
@section('heading', 'Centro de trabajo')

@section('content')

<div class="container-fluid cocinero-dashboard">

    {{-- BIENVENIDA --}}
    <section class="cocinero-hero mb-4">

        <div class="cocinero-hero-glow"></div>

        <div class="row align-items-center g-4 position-relative">

            <div class="col-12 col-lg-8">

                <div class="cocinero-kicker">
                    <span class="cocinero-kicker-dot"></span>
                    <i class="bi bi-fire"></i>
                    Cocina · {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}
                </div>

                <h1 class="cocinero-hero-title">
                    ¡Hola, {{ auth()->user()->name }}! 👨‍🍳
                </h1>

                <p class="cocinero-hero-text">
                    Organiza la preparación de los pedidos y mantén el flujo de cocina
                    claro, rápido y ordenado.
                </p>

                <div class="cocinero-hero-actions">
                    <a href="{{ route('cocinero.pedidos.index', ['seccion' => 'pendientes']) }}"
                        class="cocinero-btn-primary cocinero-ripple-target">
                        <i class="bi bi-bag-check"></i>
                        Ver cola de preparación
                    </a>

                    <a href="{{ route('cocinero.pedidos.index', ['seccion' => 'preparando']) }}"
                        class="cocinero-btn-secondary cocinero-ripple-target">
                        <i class="bi bi-fire"></i>
                        Mis pedidos en preparación
                    </a>
                </div>

                <div class="cocinero-hero-date">
                    <i class="bi bi-calendar3"></i>
                    {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                </div>

            </div>

            <div class="col-12 col-lg-4">

                <div class="cocinero-hero-visual">

                    <div class="cocinero-hero-circle circle-one"></div>
                    <div class="cocinero-hero-circle circle-two"></div>

                    <div class="cocinero-chef-icon">
                        <i class="bi bi-egg-fried"></i>
                    </div>

                    <div class="cocinero-floating-card floating-top">
                        <i class="bi bi-check-circle"></i>
                        <div>
                            <strong>{{ $pedidosListos->count() }}</strong>
                            <span>Listos</span>
                        </div>
                    </div>

                    <div class="cocinero-floating-card floating-bottom">
                        <i class="bi bi-fire"></i>
                        <div>
                            <strong>{{ $pedidosPreparando->count() }}</strong>
                            <span>Preparando</span>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- ESTADO DE LA COCINA --}}
    <section class="cocinero-kitchen-status mb-4">

        <div class="cocinero-status-left">

            @if($pedidosPendientes->count() > 0)

            <div class="cocinero-status-pulse warning"></div>

            <div>
                <strong>
                    {{ $pedidosPendientes->count() }}
                    {{ $pedidosPendientes->count() === 1 ? 'pedido requiere' : 'pedidos requieren' }}
                    atención
                </strong>

                <small>
                    Hay pedidos pagados esperando que comience su preparación.
                </small>
            </div>

            @else

            <div class="cocinero-status-pulse success"></div>

            <div>
                <strong>Cocina al día</strong>

                <small>
                    No hay pedidos pagados esperando preparación.
                </small>
            </div>

            @endif

        </div>

        <div class="cocinero-status-badge">
            <i class="bi bi-activity"></i>
            Flujo activo
        </div>

    </section>


    {{-- INDICADORES --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cocinero.pedidos.index', ['seccion' => 'pendientes']) }}"
                class="cocinero-stat-link">
                <div class="cocinero-stat-card warning cocinero-ripple-target">
                    <div class="cocinero-stat-header">
                        <div>
                            <span class="cocinero-stat-label">En cola</span>
                            <strong class="cocinero-stat-number">{{ $pedidosPendientes->count() }}</strong>
                        </div>
                        <div class="cocinero-stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                    <div class="cocinero-stat-bottom">
                        <span>Esperando preparación</span>
                        <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cocinero.pedidos.index', ['seccion' => 'preparando']) }}"
                class="cocinero-stat-link">
                <div class="cocinero-stat-card primary cocinero-ripple-target">
                    <div class="cocinero-stat-header">
                        <div>
                            <span class="cocinero-stat-label">Preparando</span>
                            <strong class="cocinero-stat-number">{{ $pedidosPreparando->count() }}</strong>
                        </div>
                        <div class="cocinero-stat-icon">
                            <i class="bi bi-fire"></i>
                        </div>
                    </div>
                    <div class="cocinero-stat-bottom">
                        <span>Trabajo actual</span>
                        <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cocinero.pedidos.index', ['seccion' => 'listos']) }}"
                class="cocinero-stat-link">
                <div class="cocinero-stat-card success cocinero-ripple-target">
                    <div class="cocinero-stat-header">
                        <div>
                            <span class="cocinero-stat-label">Listos</span>
                            <strong class="cocinero-stat-number">{{ $pedidosListos->count() }}</strong>
                        </div>
                        <div class="cocinero-stat-icon">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                    </div>
                    <div class="cocinero-stat-bottom">
                        <span>Entregados al siguiente paso</span>
                        <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cocinero-stat-card pink">
                <div class="cocinero-stat-header">
                    <div>
                        <span class="cocinero-stat-label">Activos</span>
                        <strong class="cocinero-stat-number">{{ $totalPedidos }}</strong>
                    </div>
                    <div class="cocinero-stat-icon">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                </div>
                <div class="cocinero-stat-bottom">
                    <span>Movimiento de cocina</span>
                    <i class="bi bi-activity"></i>
                </div>
            </div>
        </div>

    </div>


    {{-- PEDIDOS RECIENTES --}}
    <section class="cocinero-panel mb-4">

        <div class="cocinero-panel-header">

            <div>
                <span class="cocinero-panel-kicker">ACTIVIDAD</span>
                <h2>
                    <i class="bi bi-clock-history"></i>
                    Pedidos recientes
                </h2>
            </div>

            <span class="cocinero-panel-tag">
                {{ $pedidos->count() }} ACTIVOS
            </span>

        </div>

        @if($pedidos->count() > 0)

        <div class="cocinero-orders-list">

            @foreach($pedidos->take(5) as $pedido)

            @php
                $estadoReciente = strtolower($pedido->estado ?? '');
            @endphp

            <a href="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                class="cocinero-order">

                <div class="cocinero-order-id">
                    #{{ $pedido->id }}
                </div>

                <div class="cocinero-order-info">
                    <div class="cocinero-order-title">
                        <strong>{{ $pedido->user->name ?? 'Cliente' }}</strong>

                        <span class="cocinero-order-elapsed"
                            data-order-time="{{ $pedido->created_at?->timestamp ?? '' }}">
                            {{ $pedido->created_at?->format('d/m/Y H:i') }}
                        </span>
                    </div>

                    <span class="mt-1">
                        <i class="bi bi-basket me-1"></i>
                        {{ $pedido->detallePedidos->sum('cantidad') }} productos
                    </span>
                </div>

                @if($estadoReciente === 'pagado')
                    <span class="cocinero-order-status pending">
                        <i class="bi bi-hourglass-split"></i>
                        En cola
                    </span>
                @elseif($estadoReciente === 'preparando')
                    <span class="cocinero-order-status preparing">
                        <i class="bi bi-fire"></i>
                        Preparando
                    </span>
                @elseif($estadoReciente === 'asignado')
                    <span class="cocinero-order-status ready">
                        <i class="bi bi-bicycle"></i>
                        Asignado
                    </span>
                @else
                    <span class="cocinero-order-status ready">
                        <i class="bi bi-check-circle"></i>
                        Listo
                    </span>
                @endif

                <i class="bi bi-chevron-right cocinero-order-arrow"></i>

            </a>

            @endforeach

        </div>

        @else

        <div class="cocinero-empty">
            <div class="cocinero-empty-icon">
                <i class="bi bi-check2-circle"></i>
            </div>
            <strong>Cocina al día</strong>
            <span>No existen pedidos activos en este momento.</span>
        </div>

        @endif

    </section>


    {{-- ACCIONES RÁPIDAS --}}
    <section class="cocinero-panel mb-4">

        <div class="cocinero-panel-header">

            <div>
                <span class="cocinero-panel-kicker">ACCESOS</span>
                <h2>
                    <i class="bi bi-lightning-charge"></i>
                    Acciones rápidas
                </h2>
            </div>

            <span class="cocinero-panel-tag">COCINA</span>

        </div>

        <div class="row g-3 p-3">

            <div class="col-12 col-md-4">
                <a href="{{ route('cocinero.pedidos.index', ['seccion' => 'pendientes']) }}"
                    class="cocinero-action cocinero-ripple-target">
                    <div class="cocinero-action-icon pink">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="cocinero-action-content">
                        <strong>Pedidos en cola</strong>
                        <span>Consulta el siguiente pedido que puedes preparar.</span>
                    </div>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="col-12 col-md-4">
                <a href="{{ route('cocinero.pedidos.index', ['seccion' => 'preparando']) }}"
                    class="cocinero-action cocinero-ripple-target">
                    <div class="cocinero-action-icon blue">
                        <i class="bi bi-fire"></i>
                    </div>
                    <div class="cocinero-action-content">
                        <strong>En preparación</strong>
                        <span>Continúa trabajando en tus pedidos actuales.</span>
                    </div>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="col-12 col-md-4">
                <a href="{{ route('cocinero.pedidos.index', ['seccion' => 'listos']) }}"
                    class="cocinero-action cocinero-ripple-target">
                    <div class="cocinero-action-icon green">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="cocinero-action-content">
                        <strong>Pedidos listos</strong>
                        <span>Consulta los pedidos terminados por cocina.</span>
                    </div>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

        </div>

    </section>


    <div class="cocinero-dashboard-footer">
        <i class="bi bi-shield-check"></i>
        Cocina organizada y flujo controlado por Sabor Express
    </div>

</div>

@endsection
