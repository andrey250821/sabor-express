@extends('layouts.cocinero')

@section('title', 'Pedidos - Cocinero')

@section('content')

<div class="container-fluid">

    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="fw-bold mb-1">
                <i class="bi bi-bag-check"></i>
                Pedidos
            </h1>

            <p class="text-muted mb-0">
                Gestión y preparación de pedidos
            </p>
        </div>

        <span class="badge bg-dark fs-6">
            {{ $pedidosPendientes->count() + $pedidosPreparando->count() + $pedidosListos->count() }}
            pedidos
        </span>

    </div>


    {{-- MENSAJES --}}

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">

        <i class="bi bi-check-circle"></i>

        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>
    @endif


    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">

        <i class="bi bi-exclamation-triangle"></i>

        {{ session('error') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>
    @endif


    {{-- ==========================================================
         PEDIDOS PENDIENTES
         Estado: PAGADO
         ========================================================== --}}

    <div class="mb-5">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-hourglass-split text-warning"></i>
                    Pedidos pendientes
                </h3>

                <p class="text-muted mb-0">
                    Pedidos pagados que todavía no comenzaron a prepararse.
                </p>
            </div>

            <span class="badge bg-warning text-dark fs-6">
                {{ $pedidosPendientes->count() }}
            </span>

        </div>


        @if($pedidosPendientes->isEmpty())

        <div class="card shadow-sm">

            <div class="card-body text-center py-4">

                <i class="bi bi-check2-circle fs-1 text-success"></i>

                <h5 class="mt-3">
                    No hay pedidos pendientes
                </h5>

                <p class="text-muted mb-0">
                    Todos los pedidos pagados están siendo preparados o ya están listos.
                </p>

            </div>

        </div>

        @else

        @php
        $fechasPendientes = $pedidosPendientes->groupBy(
        fn($pedido) => $pedido->created_at->format('Y-m-d')
        );
        @endphp

        @foreach($fechasPendientes as $fecha => $pedidosFecha)

        <h6 class="fw-bold text-muted mt-4 mb-3">

            <i class="bi bi-calendar3"></i>

            {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}

        </h6>

        <div class="row g-4">

            @foreach($pedidosFecha as $pedido)

            @include('cocinero.pedidos.partials.card', [
            'pedido' => $pedido,
            'tipo' => 'pendiente'
            ])

            @endforeach

        </div>

        @endforeach

        @endif

    </div>



    {{-- ==========================================================
         PEDIDOS EN PREPARACIÓN
         Estado: PREPARANDO
         ========================================================== --}}

    <div class="mb-5">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-fire text-primary"></i>
                    Pedidos en preparación
                </h3>

                <p class="text-muted mb-0">
                    Pedidos que actualmente están siendo preparados.
                </p>
            </div>

            <span class="badge bg-primary fs-6">
                {{ $pedidosPreparando->count() }}
            </span>

        </div>


        @if($pedidosPreparando->isEmpty())

        <div class="card shadow-sm">

            <div class="card-body text-center py-4">

                <i class="bi bi-fire fs-1 text-muted"></i>

                <h5 class="mt-3">
                    No hay pedidos en preparación
                </h5>

                <p class="text-muted mb-0">
                    Actualmente no se está preparando ningún pedido.
                </p>

            </div>

        </div>

        @else

        @php
        $fechasPreparando = $pedidosPreparando->groupBy(
        fn($pedido) => $pedido->created_at->format('Y-m-d')
        );
        @endphp

        @foreach($fechasPreparando as $fecha => $pedidosFecha)

        <h6 class="fw-bold text-muted mt-4 mb-3">

            <i class="bi bi-calendar3"></i>

            {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}

        </h6>

        <div class="row g-4">

            @foreach($pedidosFecha as $pedido)

            @include('cocinero.pedidos.partials.card', [
            'pedido' => $pedido,
            'tipo' => 'preparando'
            ])

            @endforeach

        </div>

        @endforeach

        @endif

    </div>



    {{-- ==========================================================
         PEDIDOS LISTOS
         Estado: LISTO
         ========================================================== --}}

    <div class="mb-5">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-check-circle text-success"></i>
                    Pedidos listos
                </h3>

                <p class="text-muted mb-0">
                    Pedidos preparados y disponibles para delivery.
                </p>
            </div>

            <span class="badge bg-success fs-6">
                {{ $pedidosListos->count() }}
            </span>

        </div>


        @if($pedidosListos->isEmpty())

        <div class="card shadow-sm">

            <div class="card-body text-center py-4">

                <i class="bi bi-check-circle fs-1 text-muted"></i>

                <h5 class="mt-3">
                    No hay pedidos listos
                </h5>

                <p class="text-muted mb-0">
                    Los pedidos aparecerán aquí cuando la cocina los termine.
                </p>

            </div>

        </div>

        @else

        @php
        $fechasListos = $pedidosListos->groupBy(
        fn($pedido) => $pedido->created_at->format('Y-m-d')
        );
        @endphp

        @foreach($fechasListos as $fecha => $pedidosFecha)

        <h6 class="fw-bold text-muted mt-4 mb-3">

            <i class="bi bi-calendar3"></i>

            {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}

        </h6>

        <div class="row g-4">

            @foreach($pedidosFecha as $pedido)

            @include('cocinero.pedidos.partials.card', [
            'pedido' => $pedido,
            'tipo' => 'listo'
            ])

            @endforeach

        </div>

        @endforeach

        @endif

    </div>

</div>

@endsection