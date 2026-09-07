@extends('layouts.cocinero')

@section('title', 'Dashboard - Cocinero')

@section('content')

<div class="container-fluid cocinero-dashboard">

    {{-- =====================================================
         BIENVENIDA
    ====================================================== --}}
    <div class="cocinero-dashboard-hero mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-9">
                <div class="cocinero-dashboard-kicker">
                    <i class="bi bi-fire"></i>
                    Cocina · Sabor Express
                </div>

                <h1 class="cocinero-dashboard-title">
                    ¡Hola, {{ auth()->user()->name }}! 👨‍🍳
                </h1>

                <p class="cocinero-dashboard-subtitle">
                    Este es tu centro de trabajo. Revisa los pedidos pendientes,
                    controla lo que está en preparación y entrega a Delivery los pedidos listos.
                </p>

                <div class="d-flex flex-column flex-sm-row gap-2 mt-3">
                    <a href="{{ route('cocinero.pedidos.index') }}" class="btn-cocinero-primary">
                        <i class="bi bi-bag-check"></i>
                        Gestionar pedidos
                    </a>

                    <a href="{{ route('cocinero.pedidos.index') }}" class="btn-cocinero-secondary">
                        <i class="bi bi-list-check"></i>
                        Ver pedidos
                    </a>
                </div>

                <div class="cocinero-dashboard-date">
                    <i class="bi bi-calendar3"></i>
                    {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                </div>
            </div>

            <div class="col-lg-3 d-flex justify-content-lg-end justify-content-center">
                <div class="cocinero-dashboard-hero-icon">
                    <i class="bi bi-egg-fried"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================
         ESTADÍSTICAS PRINCIPALES
    ====================================================== --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cocinero.pedidos.index') }}" class="d-block h-100">
                <div class="cocinero-stat-card is-warning">
                    <div class="cocinero-stat-top">
                        <div>
                            <div class="cocinero-stat-label">Pendientes</div>
                            <div class="cocinero-stat-number">{{ $pedidosPendientes->count() }}</div>
                        </div>
                        <div class="cocinero-stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>

                    <div class="cocinero-stat-divider"></div>
                    <p class="cocinero-stat-description">
                        Pedidos pagados esperando comenzar su preparación.
                    </p>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cocinero.pedidos.index') }}" class="d-block h-100">
                <div class="cocinero-stat-card is-primary">
                    <div class="cocinero-stat-top">
                        <div>
                            <div class="cocinero-stat-label">En preparación</div>
                            <div class="cocinero-stat-number">{{ $pedidosPreparando->count() }}</div>
                        </div>
                        <div class="cocinero-stat-icon">
                            <i class="bi bi-fire"></i>
                        </div>
                    </div>

                    <div class="cocinero-stat-divider"></div>
                    <p class="cocinero-stat-description">
                        Pedidos que actualmente se están preparando en cocina.
                    </p>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cocinero.pedidos.index') }}" class="d-block h-100">
                <div class="cocinero-stat-card is-success">
                    <div class="cocinero-stat-top">
                        <div>
                            <div class="cocinero-stat-label">Pedidos listos</div>
                            <div class="cocinero-stat-number">{{ $pedidosListos->count() }}</div>
                        </div>
                        <div class="cocinero-stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>

                    <div class="cocinero-stat-divider"></div>
                    <p class="cocinero-stat-description">
                        Pedidos terminados y disponibles para Delivery.
                    </p>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cocinero-stat-card is-pink">
                <div class="cocinero-stat-top">
                    <div>
                        <div class="cocinero-stat-label">Total activos</div>
                        <div class="cocinero-stat-number">{{ $totalPedidos }}</div>
                    </div>
                    <div class="cocinero-stat-icon">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                </div>

                <div class="cocinero-stat-divider"></div>
                <p class="cocinero-stat-description">
                    Pedidos que actualmente forman parte del flujo de cocina.
                </p>
            </div>
        </div>

    </div>

    {{-- =====================================================
         FLUJO + PEDIDOS RECIENTES
    ====================================================== --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-xl-7">
            <div class="cocinero-dashboard-section">
                <div class="cocinero-section-heading">
                    <h2>
                        <i class="bi bi-arrow-repeat"></i>
                        Flujo de preparación
                    </h2>
                    <span>ESTADO DE LA COCINA</span>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <div class="cocinero-flow-item warning">
                            <div class="cocinero-flow-number">01</div>
                            <div class="cocinero-flow-icon">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <h3 class="cocinero-flow-title">Pedido pagado</h3>
                            <p class="cocinero-flow-text">
                                Esperando que cocina comience la preparación.
                            </p>
                            <span class="cocinero-flow-count">
                                {{ $pedidosPendientes->count() }} pedidos
                            </span>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="cocinero-flow-item primary">
                            <div class="cocinero-flow-number">02</div>
                            <div class="cocinero-flow-icon">
                                <i class="bi bi-fire"></i>
                            </div>
                            <h3 class="cocinero-flow-title">Preparando</h3>
                            <p class="cocinero-flow-text">
                                El equipo está trabajando en el pedido.
                            </p>
                            <span class="cocinero-flow-count">
                                {{ $pedidosPreparando->count() }} pedidos
                            </span>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="cocinero-flow-item success">
                            <div class="cocinero-flow-number">03</div>
                            <div class="cocinero-flow-icon">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <h3 class="cocinero-flow-title">Listo</h3>
                            <p class="cocinero-flow-text">
                                Listo para ser recogido por Delivery.
                            </p>
                            <span class="cocinero-flow-count">
                                {{ $pedidosListos->count() }} pedidos
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="cocinero-dashboard-section">
                <div class="cocinero-section-heading">
                    <h2>
                        <i class="bi bi-clock-history"></i>
                        Pedidos recientes
                    </h2>
                    <span>{{ $pedidos->count() }} ACTIVOS</span>
                </div>

                @if($pedidos->count())
                    <div class="cocinero-order-list">
                        @foreach($pedidos->take(5) as $pedido)
                            <a href="{{ route('cocinero.pedidos.index') }}" class="cocinero-order-item">
                                <div class="cocinero-order-main">
                                    <div class="cocinero-order-number">
                                        Pedido #{{ $pedido->id }}
                                    </div>

                                    <div class="cocinero-order-client">
                                        {{ $pedido->user->name ?? 'Cliente' }}
                                    </div>

                                    <div class="cocinero-order-time">
                                        {{ $pedido->created_at?->format('d/m/Y H:i') }}
                                    </div>
                                </div>

                                @if($pedido->estado === 'pagado')
                                    <span class="cocinero-status cocinero-status-pagado">
                                        <i class="bi bi-hourglass-split"></i>
                                        Pendiente
                                    </span>
                                @elseif($pedido->estado === 'preparando')
                                    <span class="cocinero-status cocinero-status-preparando">
                                        <i class="bi bi-fire"></i>
                                        Preparando
                                    </span>
                                @else
                                    <span class="cocinero-status cocinero-status-listo">
                                        <i class="bi bi-check-circle"></i>
                                        Listo
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="cocinero-empty-state">
                        <i class="bi bi-check2-circle"></i>
                        <p>No hay pedidos activos en cocina.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- =====================================================
         ACCIONES RÁPIDAS
    ====================================================== --}}
    <div class="cocinero-dashboard-section">
        <div class="cocinero-section-heading">
            <h2>
                <i class="bi bi-lightning-charge"></i>
                Acciones rápidas
            </h2>
            <span>ACCESOS DE COCINA</span>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <a href="{{ route('cocinero.pedidos.index') }}" class="cocinero-quick-action">
                    <div class="cocinero-quick-icon">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <div>
                        <h3 class="cocinero-quick-title">Gestionar pedidos</h3>
                        <p class="cocinero-quick-text">Ver y actualizar el flujo de cocina.</p>
                    </div>
                    <i class="bi bi-chevron-right cocinero-quick-arrow"></i>
                </a>
            </div>

            <div class="col-12 col-md-4">
                <a href="{{ route('cocinero.pedidos.index') }}" class="cocinero-quick-action">
                    <div class="cocinero-quick-icon">
                        <i class="bi bi-fire"></i>
                    </div>
                    <div>
                        <h3 class="cocinero-quick-title">En preparación</h3>
                        <p class="cocinero-quick-text">Controlar los pedidos que estás preparando.</p>
                    </div>
                    <i class="bi bi-chevron-right cocinero-quick-arrow"></i>
                </a>
            </div>

            <div class="col-12 col-md-4">
                <a href="{{ route('cocinero.pedidos.index') }}" class="cocinero-quick-action">
                    <div class="cocinero-quick-icon">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div>
                        <h3 class="cocinero-quick-title">Pedidos listos</h3>
                        <p class="cocinero-quick-text">Revisar los pedidos disponibles para Delivery.</p>
                    </div>
                    <i class="bi bi-chevron-right cocinero-quick-arrow"></i>
                </a>
            </div>
        </div>
    </div>

</div>

@endsection
