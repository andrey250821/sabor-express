@extends('layouts.delivery')

@section('title', 'Dashboard')

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

            <h1 class="mb-1">
                Dashboard
            </h1>

            <p class="text-muted mb-0">
                Bienvenido, {{ $delivery->name }}
            </p>

        </div>

    </div>


    {{-- =====================================================
        TARJETAS PRINCIPALES
    ====================================================== --}}

    <div class="row g-4">


        {{-- PEDIDOS DISPONIBLES --}}

        <div class="col-12 col-md-4">

            <a href="{{ route('delivery.pedidos.index') }}"
                class="text-decoration-none">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex
                                    justify-content-between
                                    align-items-center">

                            <div>

                                <h6 class="text-muted">
                                    Pedidos disponibles
                                </h6>

                                <h2 class="fw-bold mb-0">
                                    {{ $pedidosDisponibles }}
                                </h2>

                            </div>

                            <div class="fs-1 text-primary">

                                <i class="bi bi-box-seam"></i>

                            </div>

                        </div>


                        <hr>


                        <small class="text-muted">

                            Pedidos listos para recoger

                        </small>

                    </div>

                </div>

            </a>

        </div>



        {{-- MIS PEDIDOS --}}

        <div class="col-12 col-md-4">

            <a href="{{ route('delivery.pedidos.mis') }}"
                class="text-decoration-none">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex
                                    justify-content-between
                                    align-items-center">

                            <div>

                                <h6 class="text-muted">
                                    Mis pedidos
                                </h6>

                                <h2 class="fw-bold mb-0">
                                    {{ $misPedidos }}
                                </h2>

                            </div>


                            <div class="fs-1 text-warning">

                                <i class="bi bi-bicycle"></i>

                            </div>

                        </div>


                        <hr>


                        <small class="text-muted">

                            Pedidos que estás entregando

                        </small>

                    </div>

                </div>

            </a>

        </div>



        {{-- ENTREGADOS --}}

        <div class="col-12 col-md-4">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <div>

                            <h6 class="text-muted">
                                Pedidos entregados
                            </h6>

                            <h2 class="fw-bold mb-0">
                                {{ $pedidosEntregados }}
                            </h2>

                        </div>


                        <div class="fs-1 text-success">

                            <i class="bi bi-check-circle"></i>

                        </div>

                    </div>


                    <hr>


                    <small class="text-muted">

                        Total de pedidos entregados

                    </small>

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


                        {{-- VER PEDIDOS DISPONIBLES --}}

                        <div class="col-12 col-md-6">

                            <a
                                href="{{ route('delivery.pedidos.index') }}"
                                class="btn btn-primary w-100 py-3">

                                <i class="bi bi-box-seam"></i>

                                Ver pedidos disponibles

                            </a>

                        </div>



                        {{-- MIS ENTREGAS --}}

                        <div class="col-12 col-md-6">

                            <a
                                href="{{ route('delivery.pedidos.mis') }}"
                                class="btn btn-outline-primary w-100 py-3">

                                <i class="bi bi-bicycle"></i>

                                Ver mis entregas

                            </a>

                        </div>


                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- =====================================================
        INFORMACIÓN DEL REPARTIDOR
    ====================================================== --}}

    <div class="row mt-4">

        <div class="col-12 col-lg-6">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h5 class="fw-bold mb-3">

                        <i class="bi bi-person-circle"></i>

                        Mi información

                    </h5>


                    <div class="mb-3">

                        <strong>
                            Nombre:
                        </strong>

                        {{ $delivery->name }}

                    </div>


                    <div class="mb-3">

                        <strong>
                            Correo:
                        </strong>

                        {{ $delivery->email }}

                    </div>


                    <div class="mb-3">

                        <strong>
                            Teléfono:
                        </strong>

                        {{ $delivery->telefono ?? 'No registrado' }}

                    </div>


                    <div>

                        <strong>
                            Estado:
                        </strong>


                        @if($delivery->estado === 'activo')

                        <span class="badge bg-success">

                            Activo

                        </span>

                        @else

                        <span class="badge bg-danger">

                            Inactivo

                        </span>

                        @endif

                    </div>

                </div>

            </div>

        </div>



        {{-- INFORMACIÓN DEL FLUJO --}}

        <div class="col-12 col-lg-6">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <h5 class="fw-bold mb-3">

                        <i class="bi bi-info-circle"></i>

                        Flujo de entrega

                    </h5>


                    <div class="mb-3">

                        <span class="badge bg-primary">
                            1
                        </span>

                        Selecciona un pedido disponible.

                    </div>


                    <div class="mb-3">

                        <span class="badge bg-primary">
                            2
                        </span>

                        Toma el pedido.

                    </div>


                    <div class="mb-3">

                        <span class="badge bg-primary">
                            3
                        </span>

                        Inicia la entrega.

                    </div>


                    <div class="mb-3">

                        <span class="badge bg-primary">
                            4
                        </span>

                        Dirígete a la dirección del cliente.

                    </div>


                    <div>

                        <span class="badge bg-success">
                            5
                        </span>

                        Marca el pedido como entregado.

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection