@extends('layouts.cocinero')

@section('title', 'Dashboard - Cocinero')

@section('content')

<div class="container-fluid cocinero-dashboard">

    {{-- =========================================================
         HERO / BIENVENIDA
    ========================================================== --}}
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
                    ¡Hola, {{ auth()->user()->name }}!
                    <span>👨‍🍳</span>
                </h1>

                <p class="cocinero-hero-text">
                    Bienvenido a tu centro de trabajo. Controla los pedidos,
                    organiza la preparación y mantén la cocina en movimiento.
                </p>

                <div class="cocinero-hero-actions">

                    <a href="{{ route('cocinero.pedidos.index') }}"
                        class="cocinero-btn-primary">
                        <i class="bi bi-bag-check"></i>
                        Gestionar pedidos
                    </a>

                    <a href="{{ route('cocinero.pedidos.index') }}"
                        class="cocinero-btn-secondary">
                        <i class="bi bi-list-check"></i>
                        Ver pedidos
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


    {{-- =========================================================
         INDICADOR DE ESTADO DE COCINA
    ========================================================== --}}
    <section class="cocinero-kitchen-status mb-4">

        <div class="cocinero-status-left">

            @if($pedidosPendientes->count() > 0)

            <div class="cocinero-status-pulse warning">
                <span></span>
            </div>

            <div>
                <strong>
                    {{ $pedidosPendientes->count() }}
                    {{ $pedidosPendientes->count() === 1 ? 'pedido requiere' : 'pedidos requieren' }}
                    atención
                </strong>

                <small>
                    Hay pedidos pagados esperando comenzar la preparación.
                </small>
            </div>

            @else

            <div class="cocinero-status-pulse success">
                <span></span>
            </div>

            <div>
                <strong>Cocina al día</strong>

                <small>
                    No hay pedidos pendientes de preparación.
                </small>
            </div>

            @endif

        </div>

        <div class="cocinero-status-badge">
            <i class="bi bi-activity"></i>
            Cocina activa
        </div>

    </section>


    {{-- =========================================================
         ESTADÍSTICAS
    ========================================================== --}}
    <div class="row g-3 mb-4">

        {{-- PENDIENTES --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <a href="{{ route('cocinero.pedidos.index') }}"
                class="cocinero-stat-link">

                <div class="cocinero-stat-card warning">

                    <div class="cocinero-stat-header">

                        <div>
                            <span class="cocinero-stat-label">
                                Pendientes
                            </span>

                            <strong class="cocinero-stat-number">
                                {{ $pedidosPendientes->count() }}
                            </strong>
                        </div>

                        <div class="cocinero-stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>

                    </div>

                    <div class="cocinero-stat-bottom">

                        <span>
                            Esperando preparación
                        </span>

                        <i class="bi bi-arrow-up-right"></i>

                    </div>

                </div>

            </a>

        </div>


        {{-- PREPARANDO --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <a href="{{ route('cocinero.pedidos.index') }}"
                class="cocinero-stat-link">

                <div class="cocinero-stat-card primary">

                    <div class="cocinero-stat-header">

                        <div>
                            <span class="cocinero-stat-label">
                                Preparando
                            </span>

                            <strong class="cocinero-stat-number">
                                {{ $pedidosPreparando->count() }}
                            </strong>
                        </div>

                        <div class="cocinero-stat-icon">
                            <i class="bi bi-fire"></i>
                        </div>

                    </div>

                    <div class="cocinero-stat-bottom">

                        <span>
                            En cocina ahora
                        </span>

                        <i class="bi bi-arrow-up-right"></i>

                    </div>

                </div>

            </a>

        </div>


        {{-- LISTOS --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <a href="{{ route('cocinero.pedidos.index') }}"
                class="cocinero-stat-link">

                <div class="cocinero-stat-card success">

                    <div class="cocinero-stat-header">

                        <div>
                            <span class="cocinero-stat-label">
                                Listos
                            </span>

                            <strong class="cocinero-stat-number">
                                {{ $pedidosListos->count() }}
                            </strong>
                        </div>

                        <div class="cocinero-stat-icon">
                            <i class="bi bi-check2-circle"></i>
                        </div>

                    </div>

                    <div class="cocinero-stat-bottom">

                        <span>
                            Para Delivery
                        </span>

                        <i class="bi bi-arrow-up-right"></i>

                    </div>

                </div>

            </a>

        </div>


        {{-- TOTAL --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="cocinero-stat-card pink">

                <div class="cocinero-stat-header">

                    <div>
                        <span class="cocinero-stat-label">
                            Total activos
                        </span>

                        <strong class="cocinero-stat-number">
                            {{ $totalPedidos }}
                        </strong>
                    </div>

                    <div class="cocinero-stat-icon">
                        <i class="bi bi-clipboard-check"></i>
                    </div>

                </div>

                <div class="cocinero-stat-bottom">

                    <span>
                        Flujo de cocina
                    </span>

                    <i class="bi bi-activity"></i>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         FLUJO DE COCINA
    ========================================================== --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-xl-7">

            <section class="cocinero-panel h-100">

                <div class="cocinero-panel-header">

                    <div>

                        <span class="cocinero-panel-kicker">
                            OPERACIÓN
                        </span>

                        <h2>
                            <i class="bi bi-arrow-repeat"></i>
                            Flujo de cocina
                        </h2>

                    </div>

                    <span class="cocinero-panel-tag">
                        3 ETAPAS
                    </span>

                </div>


                <div class="cocinero-flow">

                    {{-- PASO 1 --}}
                    <div class="cocinero-flow-step warning">

                        <div class="cocinero-flow-step-number">
                            01
                        </div>

                        <div class="cocinero-flow-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>

                        <div class="cocinero-flow-content">

                            <strong>
                                Pedido pagado
                            </strong>

                            <span>
                                Esperando preparación
                            </span>

                        </div>

                        <div class="cocinero-flow-count">
                            {{ $pedidosPendientes->count() }}
                        </div>

                    </div>


                    <div class="cocinero-flow-line"></div>


                    {{-- PASO 2 --}}
                    <div class="cocinero-flow-step primary">

                        <div class="cocinero-flow-step-number">
                            02
                        </div>

                        <div class="cocinero-flow-icon">
                            <i class="bi bi-fire"></i>
                        </div>

                        <div class="cocinero-flow-content">

                            <strong>
                                Preparando
                            </strong>

                            <span>
                                Trabajando en cocina
                            </span>

                        </div>

                        <div class="cocinero-flow-count">
                            {{ $pedidosPreparando->count() }}
                        </div>

                    </div>


                    <div class="cocinero-flow-line"></div>


                    {{-- PASO 3 --}}
                    <div class="cocinero-flow-step success">

                        <div class="cocinero-flow-step-number">
                            03
                        </div>

                        <div class="cocinero-flow-icon">
                            <i class="bi bi-check2-circle"></i>
                        </div>

                        <div class="cocinero-flow-content">

                            <strong>
                                Pedido listo
                            </strong>

                            <span>
                                Disponible para Delivery
                            </span>

                        </div>

                        <div class="cocinero-flow-count">
                            {{ $pedidosListos->count() }}
                        </div>

                    </div>

                </div>

            </section>

        </div>


        {{-- =====================================================
             PEDIDOS RECIENTES
        ====================================================== --}}
        <div class="col-12 col-xl-5">

            <section class="cocinero-panel h-100">

                <div class="cocinero-panel-header">

                    <div>

                        <span class="cocinero-panel-kicker">
                            ACTIVIDAD
                        </span>

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

                    <a href="{{ route('cocinero.pedidos.index') }}"
                        class="cocinero-order">

                        <div class="cocinero-order-id">
                            #{{ $pedido->id }}
                        </div>

                        <div class="cocinero-order-info">

                            <strong>
                                {{ $pedido->user->name ?? 'Cliente' }}
                            </strong>

                            <span>
                                {{ $pedido->created_at?->format('d/m/Y H:i') }}
                            </span>

                        </div>


                        @if($pedido->estado === 'pagado')

                        <span class="cocinero-order-status pending">
                            <i class="bi bi-hourglass-split"></i>
                            Pendiente
                        </span>

                        @elseif($pedido->estado === 'preparando')

                        <span class="cocinero-order-status preparing">
                            <i class="bi bi-fire"></i>
                            Preparando
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

                    <strong>
                        Cocina al día
                    </strong>

                    <span>
                        No existen pedidos activos en este momento.
                    </span>

                </div>

                @endif

            </section>

        </div>

    </div>


    {{-- =========================================================
         ACCIONES RÁPIDAS
    ========================================================== --}}
    <section class="cocinero-panel mb-4">

        <div class="cocinero-panel-header">

            <div>

                <span class="cocinero-panel-kicker">
                    ACCESOS
                </span>

                <h2>
                    <i class="bi bi-lightning-charge"></i>
                    Acciones rápidas
                </h2>

            </div>

            <span class="cocinero-panel-tag">
                COCINA
            </span>

        </div>


        <div class="row g-3">

            <div class="col-12 col-md-4">

                <a href="{{ route('cocinero.pedidos.index') }}"
                    class="cocinero-action">

                    <div class="cocinero-action-icon pink">
                        <i class="bi bi-bag-check"></i>
                    </div>

                    <div class="cocinero-action-content">

                        <strong>
                            Gestionar pedidos
                        </strong>

                        <span>
                            Ver todos los pedidos de cocina
                        </span>

                    </div>

                    <i class="bi bi-arrow-right"></i>

                </a>

            </div>


            <div class="col-12 col-md-4">

                <a href="{{ route('cocinero.pedidos.index') }}"
                    class="cocinero-action">

                    <div class="cocinero-action-icon blue">
                        <i class="bi bi-fire"></i>
                    </div>

                    <div class="cocinero-action-content">

                        <strong>
                            En preparación
                        </strong>

                        <span>
                            Revisar pedidos en proceso
                        </span>

                    </div>

                    <i class="bi bi-arrow-right"></i>

                </a>

            </div>


            <div class="col-12 col-md-4">

                <a href="{{ route('cocinero.pedidos.index') }}"
                    class="cocinero-action">

                    <div class="cocinero-action-icon green">
                        <i class="bi bi-check2-circle"></i>
                    </div>

                    <div class="cocinero-action-content">

                        <strong>
                            Pedidos listos
                        </strong>

                        <span>
                            Ver pedidos para Delivery
                        </span>

                    </div>

                    <i class="bi bi-arrow-right"></i>

                </a>

            </div>

        </div>

    </section>


    {{-- =========================================================
         PIE DEL DASHBOARD
    ========================================================== --}}
    <div class="cocinero-dashboard-footer">

        <div>
            <i class="bi bi-shield-check"></i>
            Panel protegido · {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}
        </div>

        <span>
            Flujo: Pagado → Preparando → Listo
        </span>

    </div>

</div>

@endsection