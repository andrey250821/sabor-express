@extends('layouts.delivery')

@section('title', 'Dashboard - Delivery')
@section('section', 'Panel de Delivery')
@section('heading', 'Centro de entregas')

@section('content')

<div class="container-fluid delivery-dashboard">

    {{-- =========================================================
         HERO
    ========================================================== --}}
    <section class="delivery-dashboard-hero mb-4">

        <div class="delivery-dashboard-hero-glow"></div>

        <div class="row align-items-center g-4 position-relative">

            <div class="col-12 col-lg-8">

                <div class="delivery-dashboard-kicker">

                    <span class="delivery-dashboard-kicker-dot"></span>

                    <i class="bi bi-bicycle"></i>

                    Delivery ·
                    {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}

                </div>

                <h1 class="delivery-dashboard-title">

                    ¡Hola, {{ $delivery->name ?? auth()->user()->name }}!

                    <span>🏍️</span>

                </h1>

                <p class="delivery-dashboard-text">

                    Bienvenido a tu centro de entregas.
                    Consulta la cantidad de pedidos en cola. Las entregas se asignan automáticamente y mantén actualizado el estado de tus pedidos.

                </p>

                <div class="delivery-dashboard-actions">

                    <a href="{{ route('delivery.pedidos.index') }}"
                        class="delivery-dashboard-btn-primary">

                        <i class="bi bi-box-seam"></i>

                        Ver cola de pedidos

                    </a>

                    <a href="{{ route('delivery.pedidos.mis') }}"
                        class="delivery-dashboard-btn-secondary">

                        <i class="bi bi-bicycle"></i>

                        Mis pedidos

                    </a>

                </div>

                <div class="delivery-dashboard-date">

                    <i class="bi bi-calendar3"></i>

                    {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}

                </div>

            </div>


            {{-- =================================================
                 HERO VISUAL
            ================================================== --}}
            <div class="col-12 col-lg-4">

                <div class="delivery-dashboard-hero-visual">

                    <div class="delivery-hero-circle circle-one"></div>

                    <div class="delivery-hero-circle circle-two"></div>

                    <div class="delivery-hero-circle circle-three"></div>


                    <div class="delivery-bike-icon">

                        <i class="bi bi-bicycle"></i>

                    </div>


                    {{-- PEDIDOS EN COLA --}}
                    <div class="delivery-floating-card floating-top">

                        <div class="delivery-floating-icon available">

                            <i class="bi bi-box-seam"></i>

                        </div>

                        <div>

                            <strong>
                                {{ $pedidosEnCola }}
                            </strong>

                            <span>
                                En cola
                            </span>

                        </div>

                    </div>


                    {{-- PEDIDOS ACTIVOS --}}
                    <div class="delivery-floating-card floating-bottom">

                        <div class="delivery-floating-icon active">

                            <i class="bi bi-truck"></i>

                        </div>

                        <div>

                            <strong>
                                {{ $misPedidos }}
                            </strong>

                            <span>
                                En reparto
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- =========================================================
         ESTADO DEL DELIVERY
    ========================================================== --}}
    <section class="delivery-dashboard-status mb-4">

        <div class="delivery-dashboard-status-left">

            @if($pedidosEnCola > 0)

            <div class="delivery-status-pulse warning">
                <span></span>
            </div>

            <div>

                <strong>

                    {{ $pedidosEnCola }}

                    {{ $pedidosEnCola === 1
                            ? 'pedido en cola'
                            : 'pedidos en cola'
                        }}

                </strong>

                <small>

                    Hay pedidos listos en cocina esperando asignación automática.

                </small>

            </div>

            @else

            <div class="delivery-status-pulse success">
                <span></span>
            </div>

            <div>

                <strong>
                    No hay pedidos en cola
                </strong>

                <small>
                    Actualmente no hay pedidos esperando asignación.
                </small>

            </div>

            @endif

        </div>


        <div class="delivery-dashboard-status-badge">

            <i class="bi bi-activity"></i>

            Delivery activo

        </div>

    </section>


    {{-- =========================================================
         ESTADÍSTICAS
    ========================================================== --}}
    <div class="row g-3 mb-4">


        {{-- PEDIDOS EN COLA --}}
        <div class="col-12 col-sm-6 col-xl-4">

            <a href="{{ route('delivery.pedidos.index') }}"
                class="delivery-stat-link">

                <div class="delivery-stat-card warning">

                    <div class="delivery-stat-header">

                        <div>

                            <span class="delivery-stat-label">
                                En cola
                            </span>

                            <strong class="delivery-stat-number">
                                {{ $pedidosEnCola }}
                            </strong>

                        </div>

                        <div class="delivery-stat-icon">

                            <i class="bi bi-box-seam"></i>

                        </div>

                    </div>

                    <div class="delivery-stat-bottom">

                        <span>
                            Pedidos listos esperando asignación
                        </span>

                        <i class="bi bi-arrow-up-right"></i>

                    </div>

                </div>

            </a>

        </div>


        {{-- MIS PEDIDOS --}}
        <div class="col-12 col-sm-6 col-xl-4">

            <a href="{{ route('delivery.pedidos.mis') }}"
                class="delivery-stat-link">

                <div class="delivery-stat-card primary">

                    <div class="delivery-stat-header">

                        <div>

                            <span class="delivery-stat-label">
                                Mis pedidos
                            </span>

                            <strong class="delivery-stat-number">
                                {{ $misPedidos }}
                            </strong>

                        </div>

                        <div class="delivery-stat-icon">

                            <i class="bi bi-bicycle"></i>

                        </div>

                    </div>

                    <div class="delivery-stat-bottom">

                        <span>
                            Entregas en proceso
                        </span>

                        <i class="bi bi-arrow-up-right"></i>

                    </div>

                </div>

            </a>

        </div>


        {{-- ENTREGADOS --}}
        <div class="col-12 col-sm-6 col-xl-4">

            <div class="delivery-stat-card success">

                <div class="delivery-stat-header">

                    <div>

                        <span class="delivery-stat-label">
                            Entregados
                        </span>

                        <strong class="delivery-stat-number">
                            {{ $pedidosEntregados }}
                        </strong>

                    </div>

                    <div class="delivery-stat-icon">

                        <i class="bi bi-check2-circle"></i>

                    </div>

                </div>

                <div class="delivery-stat-bottom">

                    <span>
                        Entregas completadas
                    </span>

                    <i class="bi bi-check-circle"></i>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         FLUJO + JORNADA
    ========================================================== --}}
    <div class="row g-3 mb-4">


        {{-- FLUJO DE DELIVERY --}}
        <div class="col-12 col-xl-7">

            <section class="delivery-dashboard-panel h-100">

                <div class="delivery-panel-header">

                    <div>

                        <span class="delivery-panel-kicker">
                            OPERACIÓN
                        </span>

                        <h2>

                            <i class="bi bi-arrow-repeat"></i>

                            Flujo de entrega

                        </h2>

                    </div>

                    <span class="delivery-panel-tag">
                        4 ETAPAS
                    </span>

                </div>


                <div class="delivery-flow">


                    {{-- 01 --}}
                    <div class="delivery-flow-step warning">

                        <div class="delivery-flow-number">
                            01
                        </div>

                        <div class="delivery-flow-icon">

                            <i class="bi bi-box-seam"></i>

                        </div>

                        <div class="delivery-flow-content">

                            <strong>
                                Pedido en cola
                            </strong>

                            <span>
                                Listo en cocina
                            </span>

                        </div>

                        <div class="delivery-flow-count">
                            {{ $pedidosEnCola }}
                        </div>

                    </div>


                    <div class="delivery-flow-line"></div>


                    {{-- 02 --}}
                    <div class="delivery-flow-step primary">

                        <div class="delivery-flow-number">
                            02
                        </div>

                        <div class="delivery-flow-icon">

                            <i class="bi bi-hand-index-thumb"></i>

                        </div>

                        <div class="delivery-flow-content">

                            <strong>
                                Pedido asignado
                            </strong>

                            <span>
                                Asignación automática
                            </span>

                        </div>

                        <div class="delivery-flow-count">
                            {{ $misPedidos }}
                        </div>

                    </div>


                    <div class="delivery-flow-line"></div>


                    {{-- 03 --}}
                    <div class="delivery-flow-step blue">

                        <div class="delivery-flow-number">
                            03
                        </div>

                        <div class="delivery-flow-icon">

                            <i class="bi bi-sign-turn-right"></i>

                        </div>

                        <div class="delivery-flow-content">

                            <strong>
                                En camino
                            </strong>

                            <span>
                                Dirigiéndote al cliente
                            </span>

                        </div>

                        <div class="delivery-flow-count">

                            <i class="bi bi-bicycle"></i>

                        </div>

                    </div>


                    <div class="delivery-flow-line"></div>


                    {{-- 04 --}}
                    <div class="delivery-flow-step success">

                        <div class="delivery-flow-number">
                            04
                        </div>

                        <div class="delivery-flow-icon">

                            <i class="bi bi-check2-circle"></i>

                        </div>

                        <div class="delivery-flow-content">

                            <strong>
                                Entregado
                            </strong>

                            <span>
                                Pedido completado
                            </span>

                        </div>

                        <div class="delivery-flow-count">
                            {{ $pedidosEntregados }}
                        </div>

                    </div>

                </div>

            </section>

        </div>


        {{-- MI JORNADA --}}
        <div class="col-12 col-xl-5">

            <section class="delivery-dashboard-panel h-100">

                <div class="delivery-panel-header">

                    <div>

                        <span class="delivery-panel-kicker">
                            MI ACTIVIDAD
                        </span>

                        <h2>

                            <i class="bi bi-person-badge"></i>

                            Mi jornada

                        </h2>

                    </div>

                    <span class="delivery-panel-tag">
                        ACTIVO
                    </span>

                </div>


                <div class="delivery-profile-card">

                    <div class="delivery-profile-avatar">

                        @if($delivery->foto_perfil_url)
                            <img
                                src="{{ $delivery->foto_perfil_url }}"
                                alt="Foto de {{ $delivery->name ?? auth()->user()->name }}">
                        @else
                            {{ strtoupper(substr($delivery->name ?? auth()->user()->name, 0, 1)) }}
                        @endif

                    </div>

                    <div class="delivery-profile-info">

                        <strong>
                            {{ $delivery->name ?? auth()->user()->name }}
                        </strong>

                        <span>
                            Delivery
                        </span>

                    </div>

                    <div class="delivery-profile-status">

                        <span></span>

                        En línea

                    </div>

                </div>


                <div class="delivery-mini-stats">


                    <div class="delivery-mini-stat">

                        <div class="delivery-mini-stat-icon available">

                            <i class="bi bi-box"></i>

                        </div>

                        <div>

                            <strong>
                                {{ $pedidosEnCola }}
                            </strong>

                            <span>
                                En cola
                            </span>

                        </div>

                    </div>


                    <div class="delivery-mini-stat">

                        <div class="delivery-mini-stat-icon active">

                            <i class="bi bi-truck"></i>

                        </div>

                        <div>

                            <strong>
                                {{ $misPedidos }}
                            </strong>

                            <span>
                                En proceso
                            </span>

                        </div>

                    </div>


                    <div class="delivery-mini-stat">

                        <div class="delivery-mini-stat-icon completed">

                            <i class="bi bi-check-lg"></i>

                        </div>

                        <div>

                            <strong>
                                {{ $pedidosEntregados }}
                            </strong>

                            <span>
                                Completados
                            </span>

                        </div>

                    </div>

                </div>


                <div class="delivery-profile-message">

                    <div class="delivery-profile-message-icon">

                        <i class="bi bi-lightning-charge-fill"></i>

                    </div>

                    <div>

                        <strong>
                            Mantente atento
                        </strong>

                        <span>
                            Los pedidos listos aparecen automáticamente
                            para que el sistema te asigne el siguiente pedido automáticamente.
                        </span>

                    </div>

                </div>

            </section>

        </div>

    </div>


    {{-- =========================================================
         ACCIONES RÁPIDAS
    ========================================================== --}}
    <section class="delivery-dashboard-panel mb-4">

        <div class="delivery-panel-header">

            <div>

                <span class="delivery-panel-kicker">
                    ACCESOS
                </span>

                <h2>

                    <i class="bi bi-lightning-charge"></i>

                    Acciones rápidas

                </h2>

            </div>

            <span class="delivery-panel-tag">
                DELIVERY
            </span>

        </div>


        <div class="row g-3">


            {{-- PEDIDOS EN COLA --}}
            <div class="col-12 col-md-4">

                <a href="{{ route('delivery.pedidos.index') }}"
                    class="delivery-action">

                    <div class="delivery-action-icon pink">

                        <i class="bi bi-box-seam"></i>

                    </div>

                    <div class="delivery-action-content">

                        <strong>
                            Buscar pedidos
                        </strong>

                        <span>
                            Ver cola de pedidos
                        </span>

                    </div>

                    <i class="bi bi-arrow-right"></i>

                </a>

            </div>


            {{-- MIS PEDIDOS --}}
            <div class="col-12 col-md-4">

                <a href="{{ route('delivery.pedidos.mis') }}"
                    class="delivery-action">

                    <div class="delivery-action-icon blue">

                        <i class="bi bi-bicycle"></i>

                    </div>

                    <div class="delivery-action-content">

                        <strong>
                            Mis pedidos
                        </strong>

                        <span>
                            Controlar tus entregas actuales
                        </span>

                    </div>

                    <i class="bi bi-arrow-right"></i>

                </a>

            </div>


            {{-- ENTREGA --}}
            <div class="col-12 col-md-4">

                <a href="{{ route('delivery.pedidos.mis') }}"
                    class="delivery-action">

                    <div class="delivery-action-icon green">

                        <i class="bi bi-geo-alt"></i>

                    </div>

                    <div class="delivery-action-content">

                        <strong>
                            Entregas en curso
                        </strong>

                        <span>
                            Revisar pedidos que debes entregar
                        </span>

                    </div>

                    <i class="bi bi-arrow-right"></i>

                </a>

            </div>

        </div>

    </section>


    {{-- =========================================================
         INFORMACIÓN FINAL
    ========================================================== --}}
    <section class="delivery-dashboard-footer-card">

        <div class="delivery-footer-card-icon">

            <i class="bi bi-shield-check"></i>

        </div>

        <div class="delivery-footer-card-content">

            <strong>
                Entregas seguras y eficientes
            </strong>

            <span>

                Cada pedido que tomas forma parte del flujo de
                {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}.
                Mantén actualizado el estado de tus entregas.

            </span>

        </div>

        <div class="delivery-footer-card-badge">

            <i class="bi bi-check-circle-fill"></i>

            Sistema activo

        </div>

    </section>

</div>

@endsection