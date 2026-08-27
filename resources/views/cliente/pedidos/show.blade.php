@extends('layouts.cliente')

@section('title', 'Detalle del pedido')

@section('content')

<div class="container py-4">

    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <span class="text-uppercase text-muted small">
                Detalle del pedido
            </span>

            <h2 class="fw-bold mb-1">
                Pedido #{{ $pedido->id }}
            </h2>

            <p class="text-muted mb-0">
                Realizado el {{ $pedido->created_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <a href="{{ route('cliente.pedidos.index') }}"
            class="btn btn-outline-light">
            <i class="bi bi-arrow-left"></i>
            Volver
        </a>

    </div>


    <div class="row g-4">

        {{-- INFORMACIÓN DEL PEDIDO --}}
        <div class="col-12 col-lg-8">

            <div class="card shadow-sm">

                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-bag-check-fill"></i>
                        Productos del pedido
                    </h5>
                </div>

                <div class="card-body">

                    @forelse($pedido->detallePedidos as $detalle)

                    <div class="d-flex align-items-center gap-3 py-3 border-bottom">

                        {{-- IMAGEN --}}
                        <div style="width:80px;height:80px;flex-shrink:0;">

                            @if($detalle->producto && $detalle->producto->imagen)

                            <img
                                src="{{ asset('storage/' . $detalle->producto->imagen) }}"
                                alt="{{ $detalle->producto->nombre }}"
                                class="w-100 h-100 rounded object-fit-cover">

                            @else

                            <div class="w-100 h-100 rounded d-flex align-items-center justify-content-center bg-dark">
                                <i class="bi bi-image text-muted fs-3"></i>
                            </div>

                            @endif

                        </div>


                        {{-- INFORMACIÓN --}}
                        <div class="flex-grow-1">

                            <h6 class="fw-bold mb-1">

                                {{ $detalle->producto->nombre ?? 'Producto eliminado' }}

                            </h6>

                            <p class="text-muted mb-1">

                                Cantidad:
                                <strong>
                                    {{ $detalle->cantidad }}
                                </strong>

                            </p>

                            <small class="text-muted">

                                Precio unitario:
                                Bs {{ number_format($detalle->precio, 2) }}

                            </small>

                        </div>


                        {{-- SUBTOTAL --}}
                        <div class="text-end">

                            <span class="small text-muted">
                                Subtotal
                            </span>

                            <div class="fw-bold">

                                Bs {{ number_format($detalle->subtotal, 2) }}

                            </div>

                        </div>

                    </div>

                    @empty

                    <div class="text-center py-5">

                        <i class="bi bi-bag-x fs-1 text-muted"></i>

                        <p class="text-muted mt-3 mb-0">
                            No hay productos registrados en este pedido.
                        </p>

                    </div>

                    @endforelse


                    {{-- TOTAL --}}
                    <div class="d-flex justify-content-between align-items-center pt-4">

                        <span class="fs-5 fw-bold">
                            Total del pedido
                        </span>

                        <span class="fs-4 fw-bold text-guindo">

                            Bs {{ number_format($pedido->total, 2) }}

                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- INFORMACIÓN --}}
        <div class="col-12 col-lg-4">

            {{-- ESTADO --}}
            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <h5 class="mb-0">
                        <i class="bi bi-info-circle-fill"></i>
                        Estado del pedido
                    </h5>

                </div>

                <div class="card-body">

                    <p class="text-muted small mb-2">
                        Estado actual
                    </p>

                    @php
                    $estados = [
                    'comprobante_enviado' => 'Comprobante enviado',
                    'pendiente' => 'Pendiente',
                    'confirmado' => 'Confirmado',
                    'preparando' => 'Preparando',
                    'en_preparacion' => 'En preparación',
                    'en_camino' => 'En camino',
                    'entregado' => 'Entregado',
                    'cancelado' => 'Cancelado',
                    ];

                    $estadoTexto = $estados[$pedido->estado] ?? ucfirst(str_replace('_', ' ', $pedido->estado));
                    @endphp

                    <span class="badge bg-guindo px-3 py-2">

                        {{ $estadoTexto }}

                    </span>

                </div>

            </div>


            {{-- DIRECCIÓN --}}
            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <h5 class="mb-0">
                        <i class="bi bi-geo-alt-fill"></i>
                        Entrega
                    </h5>

                </div>

                <div class="card-body">

                    <p class="text-muted small mb-1">
                        Dirección
                    </p>

                    <p class="mb-3">

                        {{ $pedido->direccion_entrega ?? 'No registrada' }}

                    </p>


                    @if($pedido->referencia_delivery)

                    <p class="text-muted small mb-1">
                        Referencia
                    </p>

                    <p class="mb-3">

                        {{ $pedido->referencia_delivery }}

                    </p>

                    @endif


                    @if($pedido->observacion_cliente)

                    <p class="text-muted small mb-1">
                        Observación
                    </p>

                    <p class="mb-0">

                        {{ $pedido->observacion_cliente }}

                    </p>

                    @endif

                </div>

            </div>


            {{-- COMPROBANTE --}}
            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="mb-0">
                        <i class="bi bi-receipt"></i>
                        Comprobante de pago
                    </h5>

                </div>

                <div class="card-body text-center">

                    @if($pedido->comprobantePago)

                    <span class="badge bg-warning text-dark mb-3">

                        {{ ucfirst($pedido->comprobantePago->estado) }}

                    </span>

                    @if($pedido->comprobantePago->imagen)

                    <img
                        src="{{ asset('storage/' . $pedido->comprobantePago->imagen) }}"
                        alt="Comprobante de pago"
                        class="img-fluid rounded"
                        style="max-height:300px;object-fit:contain;">

                    @endif

                    @else

                    <i class="bi bi-receipt-cutoff fs-1 text-muted"></i>

                    <p class="text-muted mt-3 mb-0">
                        No hay comprobante registrado.
                    </p>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@endsection