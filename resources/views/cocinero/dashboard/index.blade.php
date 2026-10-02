@extends('layouts.cocinero')

@section('title', 'Dashboard - Cocinero')
@section('section', 'Panel de cocina')
@section('heading', 'Dashboard')

@section('content')

<div class="container-fluid px-0 cocinero-dashboard">

    {{-- HERO --}}
    <section class="cocinero-hero mb-4 cocinero-reveal">

        <div class="p-4 p-lg-5 position-relative">

            <div class="row align-items-center g-4 position-relative">

                <div class="col-12 col-lg-8">

                    <div class="cocinero-kicker">
                        <span class="cocinero-kicker-dot"></span>
                        <i class="bi bi-fire"></i>
                        {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}
                    </div>

                    <h2 class="cocinero-hero-title">
                        ¡Hola, {{ auth()->user()->name }}! 👨‍🍳
                    </h2>

                    <p class="cocinero-hero-text">
                        Organiza la cocina, prepara los pedidos en orden y mantén
                        el servicio avanzando de <strong>Pagado → Preparando → Listo</strong>.
                    </p>

                    <div class="cocinero-hero-actions">

                        <a
                            href="{{ route('cocinero.pedidos.index') }}"
                            class="cocinero-btn-primary js-ripple">

                            <i class="bi bi-bag-check-fill"></i>
                            Gestionar pedidos
                        </a>

                        <a
                            href="{{ route('cocinero.perfil.edit') }}"
                            class="cocinero-btn-secondary js-ripple">

                            <i class="bi bi-person-circle"></i>
                            Mi perfil
                        </a>

                    </div>

                    <div class="cocinero-hero-date">
                        <i class="bi bi-calendar3"></i>
                        {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                        <span class="mx-1">·</span>
                        <span id="cocineroClock"></span>
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
                            <i class="bi bi-check2-circle"></i>
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
        </div>
    </section>

    {{-- ESTADO DE COCINA --}}
    <section class="cocinero-kitchen-status mb-4 cocinero-reveal">

        <div class="cocinero-status-left">

            @if($pedidosPendientes->count() > 0)

                <div class="cocinero-status-pulse warning">
                    <span></span>
                </div>

                <div>
                    <strong>
                        {{ $pedidosPendientes->count() }}
                        {{ $pedidosPendientes->count() === 1 ? 'pedido espera' : 'pedidos esperan' }}
                        preparación
                    </strong>

                    <small>
                        La cola se atiende en orden de llegada.
                    </small>
                </div>

            @else

                <div class="cocinero-status-pulse success">
                    <span></span>
                </div>

                <div>
                    <strong>Cocina al día</strong>

                    <small>
                        No hay pedidos pagados esperando preparación.
                    </small>
                </div>

            @endif

        </div>

        <span class="cocinero-status-badge">
            <i class="bi bi-activity"></i>
            Cocina activa
        </span>

    </section>

    {{-- ESTADÍSTICAS --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl-3 cocinero-reveal">
            <a
                href="{{ route('cocinero.pedidos.index', ['seccion' => 'pendientes']) }}"
                class="cocinero-stat-link">

                <div class="cocinero-stat-card warning">

                    <div class="cocinero-stat-header">

                        <div>
                            <span class="cocinero-stat-label">Pendientes</span>
                            <strong class="cocinero-stat-number">
                                {{ $pedidosPendientes->count() }}
                            </strong>
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

        <div class="col-12 col-sm-6 col-xl-3 cocinero-reveal">
            <a
                href="{{ route('cocinero.pedidos.index', ['seccion' => 'preparando']) }}"
                class="cocinero-stat-link">

                <div class="cocinero-stat-card primary">

                    <div class="cocinero-stat-header">
                        <div>
                            <span class="cocinero-stat-label">Preparando</span>
                            <strong class="cocinero-stat-number">
                                {{ $pedidosPreparando->count() }}
                            </strong>
                        </div>

                        <div class="cocinero-stat-icon">
                            <i class="bi bi-fire"></i>
                        </div>
                    </div>

                    <div class="cocinero-stat-bottom">
                        <span>Pedidos que estás preparando</span>
                        <i class="bi bi-arrow-up-right"></i>
                    </div>

                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 cocinero-reveal">
            <a
                href="{{ route('cocinero.pedidos.index', ['seccion' => 'listos']) }}"
                class="cocinero-stat-link">

                <div class="cocinero-stat-card success">

                    <div class="cocinero-stat-header">
                        <div>
                            <span class="cocinero-stat-label">Listos</span>
                            <strong class="cocinero-stat-number">
                                {{ $pedidosListos->count() }}
                            </strong>
                        </div>

                        <div class="cocinero-stat-icon">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                    </div>

                    <div class="cocinero-stat-bottom">
                        <span>Listos para el siguiente paso</span>
                        <i class="bi bi-arrow-up-right"></i>
                    </div>

                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 cocinero-reveal">

            <div class="cocinero-stat-card pink">

                <div class="cocinero-stat-header">
                    <div>
                        <span class="cocinero-stat-label">Activos</span>
                        <strong class="cocinero-stat-number">
                            {{ $totalPedidos }}
                        </strong>
                    </div>

                    <div class="cocinero-stat-icon">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                </div>

                <div class="cocinero-stat-bottom">
                    <span>Flujo actual de cocina</span>
                    <i class="bi bi-activity"></i>
                </div>

            </div>
        </div>

    </div>

    {{-- PEDIDOS RECIENTES --}}
    <section class="cocinero-panel mb-4 cocinero-reveal">

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

                    <a
                        href="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                        class="cocinero-order">

                        <div class="cocinero-order-number">
                            <span>#</span>
                            <strong>{{ $pedido->id }}</strong>
                        </div>

                        <div class="cocinero-order-info">

                            <div class="cocinero-order-title">
                                <h4>
                                    {{ $pedido->user->name ?? 'Cliente' }}
                                </h4>

                                @if($pedido->estado === 'pagado')
                                    <span class="pedido-status pendiente">
                                        <i class="bi bi-clock-fill"></i>
                                        En cola
                                    </span>
                                @elseif($pedido->estado === 'preparando')
                                    <span class="pedido-status preparando">
                                        <i class="bi bi-fire"></i>
                                        Preparando
                                    </span>
                                @elseif($pedido->estado === 'asignado')
                                    <span class="pedido-status listo">
                                        <i class="bi bi-bicycle"></i>
                                        Asignado a Delivery
                                    </span>
                                @else
                                    <span class="pedido-status listo">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Listo
                                    </span>
                                @endif
                            </div>

                            <div class="cocinero-order-meta">

                                <span>
                                    <i class="bi bi-hash"></i>
                                    Pedido #{{ $pedido->id }}
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

                <span>
                    No existen pedidos activos en este momento.
                </span>
            </div>

        @endif

    </section>

    <div class="cocinero-dashboard-footer">

        <div>
            <i class="bi bi-shield-check me-1"></i>
            Panel protegido · {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}
        </div>

        <span>
            Flujo: Pagado → Preparando → Listo
        </span>
    </div>

</div>

@endsection
