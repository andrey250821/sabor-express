@extends('layouts.cliente')

@section('title', 'Mis pedidos')

@section('content')

<div class="cliente-mis-pedidos-page">

    <div class="container-fluid">

        {{-- =====================================================
             ENCABEZADO
             ===================================================== --}}

        <div class="cliente-mis-pedidos-header">

            <div class="cliente-mis-pedidos-header-contenido">

                <span class="cliente-mis-pedidos-label">
                    SABOR EXPRESS
                </span>

                <h1>
                    <i class="bi bi-bag-check-fill"></i>
                    Mis pedidos
                </h1>

                <p>
                    Consulta tus pedidos, revisa su estado
                    y mira todos sus detalles.
                </p>

            </div>


            {{-- CONTADOR --}}

            <div class="cliente-mis-pedidos-contador">

                <div class="cliente-mis-pedidos-contador-icono">
                    <i class="bi bi-receipt"></i>
                </div>

                <div>

                    <span>
                        PEDIDOS REALIZADOS
                    </span>

                    <strong>
                        {{ $pedidos->count() }}
                    </strong>

                </div>

            </div>

        </div>


        {{-- =====================================================
             MENSAJES
             ===================================================== --}}

        @if(session('success'))

        <div class="cliente-mis-pedidos-alert cliente-mis-pedidos-alert-success">

            <i class="bi bi-check-circle-fill"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

        @endif


        @if(session('error'))

        <div class="cliente-mis-pedidos-alert cliente-mis-pedidos-alert-error">

            <i class="bi bi-exclamation-circle-fill"></i>

            <span>
                {{ session('error') }}
            </span>

        </div>

        @endif


        {{-- =====================================================
             PEDIDOS
             ===================================================== --}}

        @if($pedidos->count() > 0)

        <div class="cliente-mis-pedidos-lista">

            @foreach($pedidos as $pedido)

            @php

            /*
            |--------------------------------------------------------------------------
            | ESTADOS DEL PEDIDO
            |--------------------------------------------------------------------------
            */

            $estados = [

            'comprobante_enviado' => [
            'texto' => 'Comprobante enviado',
            'icono' => 'bi-receipt',
            'clase' => 'enviado'
            ],

            'pendiente' => [
            'texto' => 'Pendiente',
            'icono' => 'bi-hourglass-split',
            'clase' => 'pendiente'
            ],

            'confirmado' => [
            'texto' => 'Pedido confirmado',
            'icono' => 'bi-check-circle',
            'clase' => 'confirmado'
            ],

            'preparando' => [
            'texto' => 'Preparando pedido',
            'icono' => 'bi-fire',
            'clase' => 'preparando'
            ],

            'en_preparacion' => [
            'texto' => 'En preparación',
            'icono' => 'bi-fire',
            'clase' => 'preparando'
            ],

            'listo' => [
            'texto' => 'Pedido listo',
            'icono' => 'bi-check2-circle',
            'clase' => 'listo'
            ],

            'asignado' => [
            'texto' => 'Delivery asignado',
            'icono' => 'bi-person-check',
            'clase' => 'asignado'
            ],

            'en_camino' => [
            'texto' => 'En camino',
            'icono' => 'bi-bicycle',
            'clase' => 'camino'
            ],

            'entregado' => [
            'texto' => 'Pedido entregado',
            'icono' => 'bi-check-circle-fill',
            'clase' => 'entregado'
            ],

            'cancelado' => [
            'texto' => 'Pedido cancelado',
            'icono' => 'bi-x-circle-fill',
            'clase' => 'cancelado'
            ],

            ];


            $estadoActual =
            $estados[$pedido->estado]
            ?? [

            'texto' => ucfirst(
            str_replace(
            '_',
            ' ',
            $pedido->estado
            )
            ),

            'icono' => 'bi-info-circle',

            'clase' => 'pendiente'

            ];


            /*
            |--------------------------------------------------------------------------
            | ESTADO DEL COMPROBANTE
            |--------------------------------------------------------------------------
            */

            if ($pedido->comprobantePago) {

            $estadoComprobante = [

            'pendiente' => [
            'texto' => 'Pendiente',
            'clase' => 'pendiente',
            'icono' => 'bi-clock'
            ],

            'aprobado' => [
            'texto' => 'Aprobado',
            'clase' => 'aprobado',
            'icono' => 'bi-check-circle-fill'
            ],

            'rechazado' => [
            'texto' => 'Rechazado',
            'clase' => 'rechazado',
            'icono' => 'bi-x-circle-fill'
            ],

            ];

            $comprobanteActual =
            $estadoComprobante[
            $pedido->comprobantePago->estado
            ]
            ?? [
            'texto' => ucfirst(
            $pedido->comprobantePago->estado
            ),
            'clase' => 'pendiente',
            'icono' => 'bi-info-circle'
            ];

            }

            @endphp


            {{-- =================================================
                         TARJETA DEL PEDIDO
                         ================================================= --}}

            <div class="cliente-mis-pedidos-card">


                {{-- =================================================
                             PARTE SUPERIOR
                             ================================================= --}}

                <div class="cliente-mis-pedidos-card-header">


                    <div class="cliente-mis-pedidos-card-identidad">

                        <div class="cliente-mis-pedidos-card-icono">

                            <i class="bi bi-receipt-cutoff"></i>

                        </div>


                        <div>

                            <span>
                                PEDIDO
                            </span>

                            <h2>
                                #{{ $pedido->id }}
                            </h2>

                        </div>

                    </div>


                    {{-- ESTADO --}}

                    <div
                        class="cliente-mis-pedidos-estado {{ $estadoActual['clase'] }}">

                        <i class="bi {{ $estadoActual['icono'] }}"></i>

                        <span>
                            {{ $estadoActual['texto'] }}
                        </span>

                    </div>

                </div>


                {{-- =================================================
                             INFORMACIÓN PRINCIPAL
                             ================================================= --}}

                <div class="cliente-mis-pedidos-card-info">


                    {{-- FECHA --}}

                    <div class="cliente-mis-pedidos-info-item">

                        <div class="cliente-mis-pedidos-info-icono">

                            <i class="bi bi-calendar3"></i>

                        </div>

                        <div>

                            <span>
                                Fecha del pedido
                            </span>

                            <strong>
                                {{ $pedido->created_at->format('d/m/Y') }}
                            </strong>

                        </div>

                    </div>


                    {{-- HORA --}}

                    <div class="cliente-mis-pedidos-info-item">

                        <div class="cliente-mis-pedidos-info-icono">

                            <i class="bi bi-clock"></i>

                        </div>

                        <div>

                            <span>
                                Hora
                            </span>

                            <strong>
                                {{ $pedido->created_at->format('H:i') }}
                            </strong>

                        </div>

                    </div>


                    {{-- PRODUCTOS --}}

                    <div class="cliente-mis-pedidos-info-item">

                        <div class="cliente-mis-pedidos-info-icono">

                            <i class="bi bi-bag-fill"></i>

                        </div>

                        <div>

                            <span>
                                Productos
                            </span>

                            <strong>
                                {{ $pedido->detallePedidos->sum('cantidad') }}
                            </strong>

                        </div>

                    </div>


                    {{-- TOTAL --}}

                    <div class="cliente-mis-pedidos-info-item cliente-mis-pedidos-info-total">

                        <div class="cliente-mis-pedidos-info-icono">

                            <i class="bi bi-cash-stack"></i>

                        </div>

                        <div>

                            <span>
                                Total
                            </span>

                            <strong>
                                Bs. {{ number_format($pedido->total, 2) }}
                            </strong>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                             PRODUCTOS RESUMIDOS
                             ================================================= --}}

                <div class="cliente-mis-pedidos-productos">


                    <div class="cliente-mis-pedidos-productos-header">

                        <span>
                            <i class="bi bi-box-seam"></i>
                            Productos del pedido
                        </span>

                        <small>
                            {{ $pedido->detallePedidos->count() }}
                            producto(s) diferente(s)
                        </small>

                    </div>


                    <div class="cliente-mis-pedidos-productos-lista">

                        @foreach($pedido->detallePedidos->take(3) as $detalle)

                        <div class="cliente-mis-pedidos-producto">


                            {{-- IMAGEN --}}

                            <div class="cliente-mis-pedidos-producto-imagen">

                                @if(
                                $detalle->producto &&
                                $detalle->producto->imagen
                                )

                                <img
                                    src="{{ asset('storage/' . $detalle->producto->imagen) }}"
                                    alt="{{ $detalle->producto->nombre }}">

                                @else

                                <div class="cliente-mis-pedidos-producto-sin-imagen">

                                    <i class="bi bi-image"></i>

                                </div>

                                @endif

                            </div>


                            {{-- NOMBRE --}}

                            <div class="cliente-mis-pedidos-producto-nombre">

                                <strong>
                                    {{ $detalle->producto->nombre ?? 'Producto eliminado' }}
                                </strong>

                                <span>
                                    {{ $detalle->cantidad }} ×
                                    Bs. {{ number_format($detalle->precio, 2) }}
                                </span>

                            </div>


                            {{-- SUBTOTAL --}}

                            <strong class="cliente-mis-pedidos-producto-subtotal">

                                Bs. {{ number_format($detalle->subtotal, 2) }}

                            </strong>

                        </div>

                        @endforeach


                        {{-- SI HAY MÁS DE 3 PRODUCTOS DIFERENTES --}}

                        @if($pedido->detallePedidos->count() > 3)

                        <div class="cliente-mis-pedidos-mas-productos">

                            <i class="bi bi-three-dots"></i>

                            <span>
                                {{ $pedido->detallePedidos->count() - 3 }}
                                producto(s) más
                            </span>

                        </div>

                        @endif

                    </div>

                </div>


                {{-- =================================================
                             PARTE INFERIOR
                             ================================================= --}}

                <div class="cliente-mis-pedidos-card-footer">


                    {{-- COMPROBANTE --}}

                    <div class="cliente-mis-pedidos-comprobante">

                        @if($pedido->comprobantePago)

                        <i class="bi {{ $comprobanteActual['icono'] }}"></i>

                        <div>

                            <span>
                                Comprobante
                            </span>

                            <strong class="{{ $comprobanteActual['clase'] }}">
                                {{ $comprobanteActual['texto'] }}
                            </strong>

                        </div>

                        @else

                        <i class="bi bi-receipt"></i>

                        <div>

                            <span>
                                Comprobante
                            </span>

                            <strong class="sin-comprobante">
                                Sin comprobante
                            </strong>

                        </div>

                        @endif

                    </div>


                    {{-- BOTÓN --}}

                    <a
                        href="{{ route('cliente.pedidos.show', $pedido->id) }}"
                        class="cliente-mis-pedidos-btn-detalle">

                        <span>
                            Ver detalle
                        </span>

                        <i class="bi bi-arrow-right"></i>

                    </a>

                </div>

            </div>

            @endforeach

        </div>


        @else


        {{-- =====================================================
                 SIN PEDIDOS
                 ===================================================== --}}

        <div class="cliente-mis-pedidos-vacio">

            <div class="cliente-mis-pedidos-vacio-icono">

                <i class="bi bi-bag-x"></i>

            </div>


            <span>
                AÚN NO TIENES PEDIDOS
            </span>


            <h2>
                Tu historial está vacío
            </h2>


            <p>
                Cuando realices tu primer pedido,
                aparecerá aquí para que puedas consultar
                su estado y todos sus detalles.
            </p>


            <a
                href="{{ route('cliente.productos.index') }}"
                class="cliente-mis-pedidos-btn-productos">

                <i class="bi bi-shop"></i>

                Ver productos

            </a>

        </div>

        @endif


    </div>

</div>

@endsection