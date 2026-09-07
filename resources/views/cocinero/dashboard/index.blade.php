@extends('layouts.cocinero')

@section('title', 'Dashboard - Cocinero')

@section('content')

<div class="container-fluid">

    {{-- =====================================================
         ENCABEZADO
    ====================================================== --}}

    <div class="d-flex flex-column flex-md-row
                justify-content-between
                align-items-md-center
                mb-4">

        <div>

            <h1 class="fw-bold mb-1">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </h1>

            <p class="text-muted mb-0">
                Bienvenido, {{ auth()->user()->name }}
            </p>

        </div>

        <div>

            <a
                href="{{ route('cocinero.pedidos.index') }}"
                class="btn btn-primary">

                <i class="bi bi-bag-check"></i>

                Ver pedidos

            </a>

        </div>

    </div>


    {{-- =====================================================
         TARJETAS PRINCIPALES
    ====================================================== --}}

    <div class="row g-4">


        {{-- PEDIDOS PENDIENTES --}}

        <div class="col-12 col-md-6 col-xl-3">

            <a
                href="{{ route('cocinero.pedidos.index') }}"
                class="text-decoration-none">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex
                                    justify-content-between
                                    align-items-center">

                            <div>

                                <h6 class="text-muted">
                                    Pendientes
                                </h6>

                                <h2 class="fw-bold mb-0">

                                    {{ $pedidosPendientes->count() }}

                                </h2>

                            </div>

                            <div class="fs-1 text-warning">

                                <i class="bi bi-hourglass-split"></i>

                            </div>

                        </div>

                        <hr>

                        <small class="text-muted">

                            Pedidos pagados esperando preparación

                        </small>

                    </div>

                </div>

            </a>

        </div>


        {{-- EN PREPARACIÓN --}}

        <div class="col-12 col-md-6 col-xl-3">

            <a
                href="{{ route('cocinero.pedidos.index') }}"
                class="text-decoration-none">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex
                                    justify-content-between
                                    align-items-center">

                            <div>

                                <h6 class="text-muted">
                                    En preparación
                                </h6>

                                <h2 class="fw-bold mb-0">

                                    {{ $pedidosPreparando->count() }}

                                </h2>

                            </div>

                            <div class="fs-1 text-primary">

                                <i class="bi bi-fire"></i>

                            </div>

                        </div>

                        <hr>

                        <small class="text-muted">

                            Pedidos que se están preparando

                        </small>

                    </div>

                </div>

            </a>

        </div>


        {{-- LISTOS --}}

        <div class="col-12 col-md-6 col-xl-3">

            <a
                href="{{ route('cocinero.pedidos.index') }}"
                class="text-decoration-none">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex
                                    justify-content-between
                                    align-items-center">

                            <div>

                                <h6 class="text-muted">
                                    Pedidos listos
                                </h6>

                                <h2 class="fw-bold mb-0">

                                    {{ $pedidosListos->count() }}

                                </h2>

                            </div>

                            <div class="fs-1 text-success">

                                <i class="bi bi-check-circle"></i>

                            </div>

                        </div>

                        <hr>

                        <small class="text-muted">

                            Listos para ser recogidos por Delivery

                        </small>

                    </div>

                </div>

            </a>

        </div>


        {{-- TOTAL --}}

        <div class="col-12 col-md-6 col-xl-3">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <div>

                            <h6 class="text-muted">
                                Total activos
                            </h6>

                            <h2 class="fw-bold mb-0">

                                {{ $totalPedidos }}

                            </h2>

                        </div>

                        <div class="fs-1 text-dark">

                            <i class="bi bi-clipboard-check"></i>

                        </div>

                    </div>

                    <hr>

                    <small class="text-muted">

                        Pedidos actualmente en cocina

                    </small>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         ESTADO DEL FLUJO
    ====================================================== --}}

    <div class="row mt-4">

        <div class="col-12">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h5 class="fw-bold mb-4">

                        <i class="bi bi-arrow-repeat"></i>

                        Flujo de preparación

                    </h5>


                    <div class="row g-4">


                        {{-- PASO 1 --}}

                        <div class="col-12 col-md-4">

                            <div class="text-center">

                                <div class="fs-1 text-warning">

                                    <i class="bi bi-hourglass-split"></i>

                                </div>

                                <h6 class="fw-bold mt-2">

                                    1. Pedido pagado

                                </h6>

                                <p class="text-muted small mb-0">

                                    El pedido espera ser tomado por cocina.

                                </p>

                                <span class="badge bg-warning text-dark mt-2">

                                    {{ $pedidosPendientes->count() }}

                                </span>

                            </div>

                        </div>


                        {{-- PASO 2 --}}

                        <div class="col-12 col-md-4">

                            <div class="text-center">

                                <div class="fs-1 text-primary">

                                    <i class="bi bi-fire"></i>

                                </div>

                                <h6 class="fw-bold mt-2">

                                    2. Preparando

                                </h6>

                                <p class="text-muted small mb-0">

                                    El pedido está siendo preparado.

                                </p>

                                <span class="badge bg-primary mt-2">

                                    {{ $pedidosPreparando->count() }}

                                </span>

                            </div>

                        </div>


                        {{-- PASO 3 --}}

                        <div class="col-12 col-md-4">

                            <div class="text-center">

                                <div class="fs-1 text-success">

                                    <i class="bi bi-check-circle"></i>

                                </div>

                                <h6 class="fw-bold mt-2">

                                    3. Listo

                                </h6>

                                <p class="text-muted small mb-0">

                                    El pedido está listo para Delivery.

                                </p>

                                <span class="badge bg-success mt-2">

                                    {{ $pedidosListos->count() }}

                                </span>

                            </div>

                        </div>


                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         ACCIONES RÁPIDAS
    ====================================================== --}}

    <div class="row mt-4">

        <div class="col-12">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h5 class="fw-bold mb-3">

                        <i class="bi bi-lightning-charge"></i>

                        Acciones rápidas

                    </h5>

                    <div class="row g-3">

                        <div class="col-12 col-md-6">

                            <a
                                href="{{ route('cocinero.pedidos.index') }}"
                                class="btn btn-primary w-100 py-3">

                                <i class="bi bi-bag-check"></i>

                                Gestionar pedidos

                            </a>

                        </div>

                        <div class="col-12 col-md-6">

                            <a
                                href="{{ route('cocinero.pedidos.index') }}"
                                class="btn btn-outline-primary w-100 py-3">

                                <i class="bi bi-list-check"></i>

                                Ver estado de pedidos

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection