@extends('layouts.delivery')

@section('title', 'Pedidos disponibles')
@section('section', 'Gestión de pedidos')
@section('heading', 'Pedidos disponibles')

@section('content')

@php
$pedidos = $pedidos ?? collect();

$totalPedidos = $pedidos->count();
@endphp

<div class="delivery-orders-page">

    {{-- ENCABEZADO --}}
    <div class="delivery-orders-header">

        <div class="delivery-orders-heading">

            <div class="delivery-orders-icon">
                <i class="bi bi-box-seam"></i>
            </div>

            <div>
                <span class="delivery-section-label">
                    CENTRO DE ENTREGAS
                </span>

                <h1>
                    Pedidos disponibles
                </h1>

                <p>
                    Revisa los pedidos listos y toma el que quieras entregar.
                </p>
            </div>

        </div>

        <div class="delivery-orders-counter">

            <div class="delivery-orders-counter-icon">
                <i class="bi bi-bicycle"></i>
            </div>

            <div>
                <span>Disponibles ahora</span>
                <strong>{{ $totalPedidos }}</strong>
            </div>

        </div>

    </div>


    {{-- BARRA INFORMATIVA --}}
    <div class="delivery-orders-info">

        <div class="delivery-orders-info-icon">
            <i class="bi bi-lightning-charge-fill"></i>
        </div>

        <div class="delivery-orders-info-content">
            <strong>Pedidos listos para entregar</strong>

            <span>
                Estos pedidos ya fueron preparados por cocina.
                Toma uno para comenzar tu entrega.
            </span>
        </div>

        <div class="delivery-orders-info-status">
            <span class="delivery-live-dot"></span>
            Disponible
        </div>

    </div>


    {{-- SIN PEDIDOS --}}
    @if($pedidos->isEmpty())

    <div class="delivery-empty-orders">

        <div class="delivery-empty-orders-animation">
            <div class="delivery-empty-circle">
                <i class="bi bi-bicycle"></i>
            </div>
        </div>

        <h2>
            No hay pedidos disponibles
        </h2>

        <p>
            En este momento no existen pedidos listos para entregar.
            Cuando cocina termine un pedido, aparecerá aquí automáticamente.
        </p>

        <a
            href="{{ route('delivery.dashboard') }}"
            class="btn delivery-empty-btn">
            <i class="bi bi-speedometer2 me-2"></i>
            Volver al dashboard
        </a>

    </div>

    @else

    {{-- RESUMEN --}}
    <div class="delivery-orders-summary">

        <div>
            <span class="delivery-summary-label">
                PEDIDOS EN ESPERA
            </span>

            <h2>
                Elige tu próxima entrega
            </h2>
        </div>

        <div class="delivery-summary-right">
            <i class="bi bi-clock-history"></i>
            <span>
                Ordenados desde el más antiguo
            </span>
        </div>

    </div>


    {{-- GRID DE PEDIDOS --}}
    <div class="row g-4">

        @foreach($pedidos as $pedido)

        @php
        $cliente = $pedido->user;

        $cantidadProductos = $pedido->detallePedidos->sum('cantidad');

        $minutos = $pedido->created_at
        ? $pedido->created_at->diffInMinutes(now())
        : 0;

        if ($minutos < 1) {
            $tiempoPedido='Hace unos segundos' ;
            } elseif ($minutos < 60) {
            $tiempoPedido='Hace ' . $minutos . ' min' ;
            } else {
            $horas=floor($minutos / 60);
            $tiempoPedido='Hace ' . $horas . ($horas==1 ? ' hora' : ' horas' );
            }
            @endphp

            <div class="col-12 col-md-6 col-xl-4">

            <article class="delivery-order-card">

                {{-- CABECERA --}}
                <div class="delivery-order-card-header">

                    <div class="delivery-order-number">

                        <div class="delivery-order-number-icon">
                            <i class="bi bi-receipt"></i>
                        </div>

                        <div>
                            <span>Pedido</span>
                            <strong>#{{ $pedido->id }}</strong>
                        </div>

                    </div>

                    <span class="delivery-ready-badge">
                        <span></span>
                        Listo
                    </span>

                </div>


                {{-- TIEMPO --}}
                <div class="delivery-order-time">
                    <i class="bi bi-clock"></i>
                    {{ $tiempoPedido }}
                </div>


                {{-- CLIENTE --}}
                <div class="delivery-order-client">

                    <div class="delivery-client-avatar">
                        {{ strtoupper(substr($cliente->name ?? 'C', 0, 1)) }}
                    </div>

                    <div class="delivery-client-data">

                        <span>Cliente</span>

                        <strong>
                            {{ $cliente->name ?? 'Cliente' }}
                        </strong>

                    </div>

                </div>


                {{-- PRODUCTOS --}}
                <div class="delivery-order-products">

                    <div class="delivery-card-title">

                        <span>
                            <i class="bi bi-basket3"></i>
                            Productos
                        </span>

                        <small>
                            {{ $cantidadProductos }}
                            {{ $cantidadProductos == 1 ? 'unidad' : 'unidades' }}
                        </small>

                    </div>

                    <div class="delivery-products-list">

                        @foreach($pedido->detallePedidos->take(3) as $detalle)

                        <div class="delivery-product-row">

                            <div class="delivery-product-quantity">
                                {{ $detalle->cantidad }}x
                            </div>

                            <div class="delivery-product-name">
                                {{ $detalle->producto->nombre ?? 'Producto' }}
                            </div>

                            <div class="delivery-product-price">
                                Bs {{ number_format($detalle->subtotal, 2) }}
                            </div>

                        </div>

                        @endforeach

                        @if($pedido->detallePedidos->count() > 3)

                        <div class="delivery-more-products">
                            <i class="bi bi-three-dots"></i>

                            {{ $pedido->detallePedidos->count() - 3 }}
                            productos más
                        </div>

                        @endif

                    </div>

                </div>


                {{-- TOTAL --}}
                <div class="delivery-order-total">

                    <span>
                        Total del pedido
                    </span>

                    <strong>
                        Bs {{ number_format($pedido->total, 2) }}
                    </strong>

                </div>


                {{-- DIRECCIÓN --}}
                <div class="delivery-order-location">

                    <div class="delivery-location-icon">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>

                    <div class="delivery-location-content">

                        <span>
                            Dirección de entrega
                        </span>

                        <strong>
                            {{ $pedido->direccion_entrega ?? 'Sin dirección registrada' }}
                        </strong>

                    </div>

                </div>


                {{-- REFERENCIA --}}
                @if(!empty($pedido->referencia_delivery))

                <div class="delivery-order-reference">

                    <div>
                        <i class="bi bi-signpost-2-fill"></i>
                    </div>

                    <span>
                        <strong>Referencia:</strong>
                        {{ $pedido->referencia_delivery }}
                    </span>

                </div>

                @endif


                {{-- OBSERVACIÓN --}}
                @if(!empty($pedido->observacion_cliente))

                <div class="delivery-order-observation">

                    <i class="bi bi-chat-left-text-fill"></i>

                    <div>
                        <strong>Observación del cliente</strong>

                        <span>
                            {{ $pedido->observacion_cliente }}
                        </span>
                    </div>

                </div>

                @endif


                {{-- ACCIONES --}}
                <div class="delivery-order-actions">

                    <a
                        href="{{ route('delivery.pedidos.show', $pedido->id) }}"
                        class="delivery-order-detail-btn">
                        <i class="bi bi-eye"></i>
                        Ver detalle
                    </a>


                    <form
                        action="{{ route('delivery.pedidos.tomar', $pedido->id) }}"
                        method="POST"
                        class="delivery-take-form">
                        @csrf

                        <button
                            type="submit"
                            class="delivery-take-btn"
                            onclick="return confirm('¿Deseas tomar el pedido #{{ $pedido->id }}?')">
                            <i class="bi bi-bicycle"></i>
                            Tomar pedido
                        </button>

                    </form>

                </div>

            </article>

    </div>

    @endforeach

</div>

@endif

</div>

@endsection