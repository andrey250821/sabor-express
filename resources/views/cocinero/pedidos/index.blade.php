@extends('layouts.cocinero')

@section('title', 'Pedidos')
@section('section', 'Gestión de cocina')
@section('heading', 'Pedidos')

@section('content')

@php
/*
|--------------------------------------------------------------------------
| Normalizar las colecciones recibidas del controlador
|--------------------------------------------------------------------------
*/

$pendientes = $pendientes ?? collect();
$preparando = $preparando ?? collect();
$listos = $listos ?? collect();

/*
|--------------------------------------------------------------------------
| Cantidades
|--------------------------------------------------------------------------
*/

$cantidadPendientes = $pendientes->count();
$cantidadPreparando = $preparando->count();
$cantidadListos = $listos->count();

$totalActivos =
$cantidadPendientes +
$cantidadPreparando +
$cantidadListos;

/*
|--------------------------------------------------------------------------
| Unificar todos los pedidos
|--------------------------------------------------------------------------
*/

$pedidos = $pendientes
->concat($preparando)
->concat($listos)
->sortBy('created_at')
->values();
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
                        Gestiona los pedidos y actualiza su estado
                        durante la preparación.
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

        {{-- Pendientes --}}

        <div class="col-12 col-sm-6 col-xl-3">

            <div class="pedido-stat-card pendiente">

                <div class="pedido-stat-icon">
                    <i class="bi bi-clock-history"></i>
                </div>

                <div class="pedido-stat-content">

                    <span>Pendientes</span>

                    <strong>
                        {{ $cantidadPendientes }}
                    </strong>

                    <small>
                        Esperando preparación
                    </small>

                </div>

            </div>

        </div>


        {{-- Preparando --}}

        <div class="col-12 col-sm-6 col-xl-3">

            <div class="pedido-stat-card preparando">

                <div class="pedido-stat-icon">
                    <i class="bi bi-fire"></i>
                </div>

                <div class="pedido-stat-content">

                    <span>Preparando</span>

                    <strong>
                        {{ $cantidadPreparando }}
                    </strong>

                    <small>
                        En proceso
                    </small>

                </div>

            </div>

        </div>


        {{-- Listos --}}

        <div class="col-12 col-sm-6 col-xl-3">

            <div class="pedido-stat-card listo">

                <div class="pedido-stat-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                <div class="pedido-stat-content">

                    <span>Listos</span>

                    <strong>
                        {{ $cantidadListos }}
                    </strong>

                    <small>
                        Esperando delivery
                    </small>

                </div>

            </div>

        </div>


        {{-- Total --}}

        <div class="col-12 col-sm-6 col-xl-3">

            <div class="pedido-stat-card total">

                <div class="pedido-stat-icon">
                    <i class="bi bi-collection-fill"></i>
                </div>

                <div class="pedido-stat-content">

                    <span>Total activos</span>

                    <strong>
                        {{ $totalActivos }}
                    </strong>

                    <small>
                        Pedidos en cocina
                    </small>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         ALERTA DE PEDIDOS PENDIENTES
    ====================================================== --}}

    @if($cantidadPendientes > 0)

    <div class="cocinero-kitchen-alert mb-4">

        <div class="cocinero-kitchen-alert-icon">
            <i class="bi bi-bell-fill"></i>
        </div>

        <div>

            <strong>
                Hay pedidos esperando preparación
            </strong>

            <span>
                Tienes {{ $cantidadPendientes }}
                {{ $cantidadPendientes == 1
                        ? 'pedido pendiente'
                        : 'pedidos pendientes' }}.
            </span>

        </div>

    </div>

    @endif


    {{-- =====================================================
         LISTADO DE PEDIDOS
    ====================================================== --}}

    <div class="cocinero-orders-card">

        <div class="cocinero-orders-header">

            <div>

                <h3>
                    <i class="bi bi-list-check me-2"></i>
                    Pedidos
                </h3>

                <span>
                    Pedidos que requieren atención en cocina
                </span>

            </div>

            <span class="cocinero-orders-count">

                {{ $pedidos->count() }}

                {{ $pedidos->count() == 1
                    ? 'pedido'
                    : 'pedidos' }}

            </span>

        </div>


        {{-- =================================================
             PEDIDOS
        ================================================== --}}

        @if($pedidos->count() > 0)

        <div class="cocinero-orders-list">

            @foreach($pedidos as $pedido)

            @php

            $estado = strtolower($pedido->estado ?? '');

            $estadoTexto = match($estado) {

            'pagado' => 'Pendiente',

            'preparando' => 'Preparando',

            'listo' => 'Listo',

            default => ucfirst(
            $estado ?: 'Sin estado'
            ),

            };

            $estadoClase = match($estado) {

            'pagado' => 'pendiente',

            'preparando' => 'preparando',

            'listo' => 'listo',

            default => 'otro',

            };

            @endphp


            <article class="cocinero-order-item">

                {{-- Número --}}

                <div class="cocinero-order-number">

                    <span>
                        #
                    </span>

                    <strong>
                        {{ $pedido->id }}
                    </strong>

                </div>


                {{-- Información --}}

                <div class="cocinero-order-info">

                    <div class="cocinero-order-title">

                        <h4>
                            Pedido #{{ $pedido->id }}
                        </h4>

                        <span class="pedido-status {{ $estadoClase }}">

                            @if($estado === 'pagado')

                            <i class="bi bi-clock-fill"></i>

                            @elseif($estado === 'preparando')

                            <i class="bi bi-fire"></i>

                            @elseif($estado === 'listo')

                            <i class="bi bi-check-circle-fill"></i>

                            @else

                            <i class="bi bi-info-circle-fill"></i>

                            @endif

                            {{ $estadoTexto }}

                        </span>

                    </div>


                    <div class="cocinero-order-meta">

                        <span>

                            <i class="bi bi-person-fill"></i>

                            {{ $pedido->user->name
                                        ?? 'Cliente' }}

                        </span>


                        @if($pedido->created_at)

                        <span>

                            <i class="bi bi-clock"></i>

                            {{ $pedido->created_at->format('d/m/Y H:i') }}

                        </span>

                        @endif


                        @if($pedido->detalle_pedidos)

                        <span>

                            <i class="bi bi-basket-fill"></i>

                            {{ $pedido->detalle_pedidos->count() }}

                            {{ $pedido->detalle_pedidos->count() == 1
                                            ? 'producto'
                                            : 'productos' }}

                        </span>

                        @endif

                    </div>

                </div>


                {{-- Total --}}

                <div class="cocinero-order-total">

                    <span>Total</span>

                    <strong>
                        Bs {{ number_format($pedido->total ?? 0, 2) }}
                    </strong>

                </div>


                {{-- Acciones --}}

                <div class="cocinero-order-actions">

                    <a href="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                        class="btn cocinero-btn-view">

                        <i class="bi bi-eye-fill"></i>

                        <span>Ver</span>

                    </a>


                    @if($estado === 'pagado')

                    <form
                        method="POST"
                        action="{{ route('cocinero.pedidos.preparar', $pedido->id) }}">

                        @csrf
                        @method('PUT')

                        <button
                            type="submit"
                            class="btn cocinero-btn-preparar">

                            <i class="bi bi-fire"></i>

                            <span>Preparar</span>

                        </button>

                    </form>


                    @elseif($estado === 'preparando')

                    <form
                        method="POST"
                        action="{{ route('cocinero.pedidos.listo', $pedido->id) }}">

                        @csrf
                        @method('PUT')

                        <button
                            type="submit"
                            class="btn cocinero-btn-listo">

                            <i class="bi bi-check-lg"></i>

                            <span>Marcar listo</span>

                        </button>

                    </form>


                    @elseif($estado === 'listo')

                    <span class="cocinero-ready-label">

                        <i class="bi bi-check-circle-fill"></i>

                        Listo para delivery

                    </span>

                    @endif

                </div>

            </article>

            @endforeach

        </div>


        @else

        {{-- =================================================
                 SIN PEDIDOS
            ================================================== --}}

        <div class="cocinero-empty-state">

            <div class="cocinero-empty-icon">
                <i class="bi bi-check2-circle"></i>
            </div>

            <h3>
                Cocina al día
            </h3>

            <p>
                No hay pedidos pendientes de atención
                en este momento.
            </p>

            <a href="{{ route('cocinero.dashboard') }}"
                class="btn cocinero-btn-primary">

                <i class="bi bi-grid-1x2-fill me-1"></i>

                Volver al dashboard

            </a>

        </div>

        @endif

    </div>

</div>

@endsection