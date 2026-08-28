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
                Pedidos disponibles para preparación
            </p>
        </div>

        <span class="badge bg-dark fs-6">
            {{ $pedidos->count() }} pedidos
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


    {{-- SIN PEDIDOS --}}

    @if($pedidos->isEmpty())

    <div class="card shadow-sm">

        <div class="card-body text-center py-5">

            <i class="bi bi-inbox fs-1 text-muted"></i>

            <h4 class="mt-3">
                No hay pedidos
            </h4>

            <p class="text-muted mb-0">
                Actualmente no existen pedidos pendientes de preparación.
            </p>

        </div>

    </div>

    @else


    {{-- PEDIDOS --}}

    <div class="row g-4">

        @foreach($pedidos as $pedido)

        <div class="col-12 col-lg-6 col-xl-4">

            <div class="card shadow-sm h-100">


                {{-- CABECERA --}}

                <div class="card-header bg-white">

                    <div class="d-flex justify-content-between align-items-center">

                        <strong>
                            Pedido #{{ $pedido->id }}
                        </strong>


                        @if($pedido->estado === 'pagado')

                        <span class="badge bg-warning text-dark">
                            Pagado
                        </span>

                        @elseif($pedido->estado === 'preparando')

                        <span class="badge bg-primary">
                            Preparando
                        </span>

                        @elseif($pedido->estado === 'listo')

                        <span class="badge bg-success">
                            Listo
                        </span>

                        @endif

                    </div>

                </div>


                {{-- CONTENIDO --}}

                <div class="card-body">


                    {{-- CLIENTE --}}

                    <div class="mb-3">

                        <small class="text-muted">
                            Cliente
                        </small>

                        <div class="fw-semibold">

                            <i class="bi bi-person"></i>

                            {{ $pedido->user->name ?? 'Cliente' }}

                        </div>

                    </div>


                    {{-- FECHA --}}

                    <div class="mb-3">

                        <small class="text-muted">
                            Fecha del pedido
                        </small>

                        <div>

                            <i class="bi bi-calendar3"></i>

                            {{ $pedido->created_at->format('d/m/Y H:i') }}

                        </div>

                    </div>


                    {{-- PRODUCTOS --}}

                    <div class="mb-3">

                        <small class="text-muted">
                            Productos
                        </small>

                        <ul class="list-group list-group-flush mt-2">

                            @foreach($pedido->detallePedidos as $detalle)

                            <li class="list-group-item px-0">

                                <div class="d-flex justify-content-between">

                                    <span>

                                        {{ $detalle->cantidad }} ×

                                        {{ $detalle->producto->nombre ?? 'Producto' }}

                                    </span>

                                    <strong>

                                        Bs.
                                        {{ number_format($detalle->subtotal, 2) }}

                                    </strong>

                                </div>

                            </li>

                            @endforeach

                        </ul>

                    </div>


                    {{-- TOTAL --}}

                    <div class="border-top pt-3">

                        <div class="d-flex justify-content-between">

                            <span class="fw-semibold">
                                Total
                            </span>

                            <strong class="fs-5">

                                Bs.
                                {{ number_format($pedido->total, 2) }}

                            </strong>

                        </div>

                    </div>


                </div>


                {{-- FOOTER --}}

                <div class="card-footer bg-white">

                    <a
                        href="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                        class="btn btn-dark w-100">

                        <i class="bi bi-eye"></i>

                        Ver pedido

                    </a>

                </div>


            </div>

        </div>

        @endforeach

    </div>

    @endif

</div>

@endsection