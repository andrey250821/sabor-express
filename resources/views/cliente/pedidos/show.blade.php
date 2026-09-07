@extends('layouts.cliente')

@section('title', 'Detalle del pedido')

@section('content')

<div class="cliente-pedido-show-page">

    {{-- =========================================================
         ENCABEZADO
    ========================================================== --}}

    <div class="cliente-pedido-show-header">

        <div class="cliente-pedido-show-header-contenido">

            <span class="cliente-pedido-show-label">
                SABOR EXPRESS
            </span>

            <div class="cliente-pedido-show-titulo">

                <div class="cliente-pedido-show-icono">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>

                <div>

                    <h1>
                        Pedido #{{ $pedido->id }}
                    </h1>

                    <p>
                        Realizado el
                        {{ $pedido->created_at->format('d/m/Y') }}
                        a las
                        {{ $pedido->created_at->format('H:i') }}
                    </p>

                </div>

            </div>

        </div>


        <a
            href="{{ route('cliente.pedidos.index') }}"
            class="cliente-pedido-show-btn-volver">

            <i class="bi bi-arrow-left"></i>

            <span>
                Mis pedidos
            </span>

        </a>

    </div>


    {{-- =========================================================
         MENSAJES
    ========================================================== --}}

    @if(session('success'))

    <div class="cliente-pedido-show-alert cliente-pedido-show-alert-success">

        <i class="bi bi-check-circle-fill"></i>

        <span>
            {{ session('success') }}
        </span>

    </div>

    @endif


    @if(session('error'))

    <div class="cliente-pedido-show-alert cliente-pedido-show-alert-error">

        <i class="bi bi-exclamation-circle-fill"></i>

        <span>
            {{ session('error') }}
        </span>

    </div>

    @endif


    {{-- =========================================================
         CONTENIDO PRINCIPAL
    ========================================================== --}}

    <div class="row g-4">


        {{-- =====================================================
             COLUMNA PRINCIPAL
        ====================================================== --}}

        <div class="col-12 col-lg-8">


            {{-- =================================================
                 ESTADO DEL PEDIDO
            ================================================== --}}

            @php

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

            $estadoActual = $estados[$pedido->estado] ?? [

            'texto' => ucfirst(
            str_replace('_', ' ', $pedido->estado)
            ),

            'icono' => 'bi-info-circle',

            'clase' => 'pendiente'

            ];

            @endphp


            <div class="cliente-pedido-show-estado-card">

                <div class="cliente-pedido-show-card-header">

                    <div class="cliente-pedido-show-card-icono">
                        <i class="bi {{ $estadoActual['icono'] }}"></i>
                    </div>

                    <div>

                        <span>
                            ESTADO ACTUAL
                        </span>

                        <h2>
                            Estado del pedido
                        </h2>

                    </div>

                </div>


                <div class="cliente-pedido-show-estado-body">

                    <div class="cliente-pedido-show-estado-principal {{ $estadoActual['clase'] }}">

                        <div class="cliente-pedido-show-estado-icono">

                            <i class="bi {{ $estadoActual['icono'] }}"></i>

                        </div>

                        <div>

                            <strong>
                                {{ $estadoActual['texto'] }}
                            </strong>

                            <p>
                                Te mostraremos aquí el avance de tu pedido.
                            </p>

                        </div>

                    </div>


                    {{-- =================================================
                         PROGRESO
                    ================================================== --}}

                    @if($pedido->estado !== 'cancelado')

                    @php

                    $pasos = [

                    [
                    'estados' => ['comprobante_enviado'],
                    'icono' => 'bi-receipt',
                    'texto' => 'Comprobante'
                    ],

                    [
                    'estados' => ['confirmado'],
                    'icono' => 'bi-check-circle',
                    'texto' => 'Confirmado'
                    ],

                    [
                    'estados' => [
                    'preparando',
                    'en_preparacion'
                    ],
                    'icono' => 'bi-fire',
                    'texto' => 'Preparando'
                    ],

                    [
                    'estados' => ['listo'],
                    'icono' => 'bi-box-seam',
                    'texto' => 'Listo'
                    ],

                    [
                    'estados' => [
                    'asignado',
                    'en_camino'
                    ],
                    'icono' => 'bi-bicycle',
                    'texto' => 'Delivery'
                    ],

                    [
                    'estados' => ['entregado'],
                    'icono' => 'bi-house-check',
                    'texto' => 'Entregado'
                    ],

                    ];

                    $ordenEstados = [

                    'comprobante_enviado' => 1,
                    'pendiente' => 1,
                    'confirmado' => 2,
                    'preparando' => 3,
                    'en_preparacion' => 3,
                    'listo' => 4,
                    'asignado' => 5,
                    'en_camino' => 5,
                    'entregado' => 6,

                    ];

                    $progresoActual =
                    $ordenEstados[$pedido->estado] ?? 1;

                    @endphp


                    <div class="cliente-pedido-show-progreso">

                        @foreach($pasos as $indice => $paso)

                        @php

                        $numeroPaso = $indice + 1;

                        $pasoCompletado =
                        $numeroPaso < $progresoActual;

                            $pasoActual=$numeroPaso==$progresoActual;

                            @endphp


                            <div class="cliente-pedido-show-paso
                                    {{ $pasoCompletado ? 'completado' : '' }}
                                    {{ $pasoActual ? 'actual' : '' }}">

                            <div class="cliente-pedido-show-paso-icono">

                                <i class="bi {{ $paso['icono'] }}"></i>

                            </div>

                            <span>
                                {{ $paso['texto'] }}
                            </span>

                    </div>


                    @if(!$loop->last)

                    <div class="cliente-pedido-show-paso-linea
                                        {{ $pasoCompletado ? 'completado' : '' }}">
                    </div>

                    @endif

                    @endforeach

                </div>

                @else

                <div class="cliente-pedido-show-cancelado">

                    <i class="bi bi-x-circle-fill"></i>

                    <div>

                        <strong>
                            Este pedido fue cancelado
                        </strong>

                        <p>
                            Si necesitas más información,
                            comunícate con el restaurante.
                        </p>

                    </div>

                </div>

                @endif

            </div>

        </div>


        {{-- =================================================
                 PRODUCTOS
            ================================================== --}}

        <div class="cliente-pedido-show-card">

            <div class="cliente-pedido-show-card-header">

                <div class="cliente-pedido-show-card-icono">
                    <i class="bi bi-bag-check-fill"></i>
                </div>

                <div>

                    <span>
                        DETALLE
                    </span>

                    <h2>
                        Productos del pedido
                    </h2>

                </div>

            </div>


            <div class="cliente-pedido-show-productos">

                @forelse($pedido->detallePedidos as $detalle)

                <div class="cliente-pedido-show-producto">

                    {{-- IMAGEN --}}

                    <div class="cliente-pedido-show-producto-imagen">

                        @if(
                        $detalle->producto &&
                        $detalle->producto->imagen
                        )

                        <img
                            src="{{ asset('storage/' . $detalle->producto->imagen) }}"
                            alt="{{ $detalle->producto->nombre }}">

                        @else

                        <div class="cliente-pedido-show-producto-sin-imagen">

                            <i class="bi bi-image"></i>

                        </div>

                        @endif

                    </div>


                    {{-- INFORMACIÓN --}}

                    <div class="cliente-pedido-show-producto-info">

                        <h3>

                            {{ $detalle->producto->nombre ?? 'Producto eliminado' }}

                        </h3>

                        <div class="cliente-pedido-show-producto-detalles">

                            <span>
                                <i class="bi bi-box"></i>
                                Cantidad:
                                <strong>
                                    {{ $detalle->cantidad }}
                                </strong>
                            </span>

                            <span>
                                <i class="bi bi-tag"></i>
                                Unitario:
                                <strong>
                                    Bs. {{ number_format($detalle->precio, 2) }}
                                </strong>
                            </span>

                        </div>

                    </div>


                    {{-- SUBTOTAL --}}

                    <div class="cliente-pedido-show-producto-subtotal">

                        <span>
                            Subtotal
                        </span>

                        <strong>
                            Bs. {{ number_format($detalle->subtotal, 2) }}
                        </strong>

                    </div>

                </div>

                @empty

                <div class="cliente-pedido-show-vacio">

                    <i class="bi bi-bag-x"></i>

                    <h3>
                        No hay productos
                    </h3>

                    <p>
                        No se encontraron productos asociados a este pedido.
                    </p>

                </div>

                @endforelse

            </div>


            {{-- TOTAL --}}

            <div class="cliente-pedido-show-total">

                <div>

                    <span>
                        Total del pedido
                    </span>

                    <small>
                        {{ $pedido->detallePedidos->sum('cantidad') }}
                        producto(s)
                    </small>

                </div>

                <strong>
                    Bs. {{ number_format($pedido->total, 2) }}
                </strong>

            </div>

        </div>


        {{-- =================================================
                 ENTREGA
            ================================================== --}}

        <div class="cliente-pedido-show-card">

            <div class="cliente-pedido-show-card-header">

                <div class="cliente-pedido-show-card-icono">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>

                <div>

                    <span>
                        DELIVERY
                    </span>

                    <h2>
                        Información de entrega
                    </h2>

                </div>

            </div>


            <div class="cliente-pedido-show-entrega">

                {{-- DIRECCIÓN --}}

                <div class="cliente-pedido-show-dato">

                    <div class="cliente-pedido-show-dato-icono">
                        <i class="bi bi-house-door-fill"></i>
                    </div>

                    <div>

                        <span>
                            Dirección de entrega
                        </span>

                        <p>
                            {{ $pedido->direccion_entrega ?? 'No registrada' }}
                        </p>

                    </div>

                </div>


                {{-- REFERENCIA --}}

                @if($pedido->referencia_delivery)

                <div class="cliente-pedido-show-dato">

                    <div class="cliente-pedido-show-dato-icono">
                        <i class="bi bi-signpost-2-fill"></i>
                    </div>

                    <div>

                        <span>
                            Referencia para el delivery
                        </span>

                        <p>
                            {{ $pedido->referencia_delivery }}
                        </p>

                    </div>

                </div>

                @endif


                {{-- OBSERVACIÓN --}}

                @if($pedido->observacion_cliente)

                <div class="cliente-pedido-show-dato">

                    <div class="cliente-pedido-show-dato-icono">
                        <i class="bi bi-chat-left-text-fill"></i>
                    </div>

                    <div>

                        <span>
                            Observaciones del pedido
                        </span>

                        <p>
                            {{ $pedido->observacion_cliente }}
                        </p>

                    </div>

                </div>

                @endif


                {{-- COORDENADAS --}}

                @if($pedido->latitud && $pedido->longitud)

                <div class="cliente-pedido-show-ubicacion">

                    <div class="cliente-pedido-show-ubicacion-header">

                        <div>

                            <i class="bi bi-pin-map-fill"></i>

                            <strong>
                                Ubicación registrada
                            </strong>

                        </div>

                        <a
                            href="https://www.google.com/maps/search/?api=1&query={{ $pedido->latitud }},{{ $pedido->longitud }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="cliente-pedido-show-btn-mapa">

                            <i class="bi bi-map"></i>

                            Ver en mapa

                        </a>

                    </div>


                    <div class="cliente-pedido-show-coordenadas">

                        <div>

                            <span>
                                Latitud
                            </span>

                            <strong>
                                {{ number_format((float) $pedido->latitud, 7) }}
                            </strong>

                        </div>

                        <div>

                            <span>
                                Longitud
                            </span>

                            <strong>
                                {{ number_format((float) $pedido->longitud, 7) }}
                            </strong>

                        </div>

                    </div>

                </div>

                @endif

            </div>

        </div>

    </div>


    {{-- =====================================================
             COLUMNA DERECHA
        ====================================================== --}}

    {{-- =========================================================
     COLUMNA DERECHA
     ========================================================= --}}

    <div class="col-12 col-lg-4">

        {{-- =====================================================
         RESUMEN STICKY
         ===================================================== --}}

        <div class="cliente-show-sidebar">

            <div class="cliente-pedido-show-resumen">

                <div class="cliente-pedido-show-resumen-header">

                    <span>
                        SABOR EXPRESS
                    </span>

                    <h2>
                        <i class="bi bi-receipt"></i>
                        Resumen
                    </h2>

                </div>


                <div class="cliente-pedido-show-resumen-body">

                    {{-- PRODUCTOS --}}

                    <div class="cliente-pedido-show-resumen-linea">

                        <span>
                            <i class="bi bi-bag-fill"></i>
                            Productos
                        </span>

                        <strong>
                            {{ $pedido->detallePedidos->sum('cantidad') }}
                        </strong>

                    </div>


                    {{-- SUBTOTAL --}}

                    <div class="cliente-pedido-show-resumen-linea">

                        <span>
                            <i class="bi bi-calculator-fill"></i>
                            Subtotal
                        </span>

                        <strong>
                            Bs. {{ number_format($pedido->total, 2) }}
                        </strong>

                    </div>


                    {{-- SEPARADOR --}}

                    <div class="cliente-pedido-show-resumen-separador"></div>


                    {{-- TOTAL --}}

                    <div class="cliente-pedido-show-resumen-total">

                        <span>
                            Total
                        </span>

                        <strong>
                            Bs. {{ number_format($pedido->total, 2) }}
                        </strong>

                    </div>


                    {{-- ESTADO --}}

                    <div class="cliente-pedido-show-resumen-estado {{ $estadoActual['clase'] }}">

                        <i class="bi {{ $estadoActual['icono'] }}"></i>

                        <div>

                            <span>
                                Estado
                            </span>

                            <strong>
                                {{ $estadoActual['texto'] }}
                            </strong>

                        </div>

                    </div>


                    {{-- BOTÓN --}}

                    <a
                        href="{{ route('cliente.pedidos.index') }}"
                        class="cliente-pedido-show-btn-pedidos">

                        <i class="bi bi-list-ul"></i>

                        Ver mis pedidos

                    </a>

                </div>

            </div>

        </div>


        {{-- =====================================================
         COMPROBANTE
         ===================================================== --}}

        <div class="cliente-pedido-show-comprobante">

            <div class="cliente-pedido-show-comprobante-header">

                <div class="cliente-pedido-show-comprobante-icono">

                    <i class="bi bi-receipt-cutoff"></i>

                </div>

                <div>

                    <span>
                        PAGO
                    </span>

                    <h2>
                        Comprobante
                    </h2>

                </div>

            </div>


            <div class="cliente-pedido-show-comprobante-body">

                @if($pedido->comprobantePago)

                @php

                $estadoComprobante = [

                'pendiente' => [
                'texto' => 'Pendiente de revisión',
                'clase' => 'pendiente',
                'icono' => 'bi-clock'
                ],

                'aprobado' => [
                'texto' => 'Comprobante aprobado',
                'clase' => 'aprobado',
                'icono' => 'bi-check-circle-fill'
                ],

                'rechazado' => [
                'texto' => 'Comprobante rechazado',
                'clase' => 'rechazado',
                'icono' => 'bi-x-circle-fill'
                ],

                ];


                $comprobanteEstado =
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

                @endphp


                {{-- ESTADO DEL COMPROBANTE --}}

                <div class="cliente-pedido-show-comprobante-estado {{ $comprobanteEstado['clase'] }}">

                    <i class="bi {{ $comprobanteEstado['icono'] }}"></i>

                    <span>
                        {{ $comprobanteEstado['texto'] }}
                    </span>

                </div>


                {{-- IMAGEN --}}

                @if($pedido->comprobantePago->imagen)

                <div class="cliente-pedido-show-comprobante-imagen">

                    <img
                        src="{{ asset('storage/' . $pedido->comprobantePago->imagen) }}"
                        alt="Comprobante de pago">

                </div>

                @else

                <div class="cliente-pedido-show-comprobante-sin-imagen">

                    <i class="bi bi-image"></i>

                    <span>
                        Imagen no disponible
                    </span>

                </div>

                @endif


                {{-- FECHA DE REVISIÓN --}}

                @if($pedido->comprobantePago->fecha_revision)

                <div class="cliente-pedido-show-comprobante-fecha">

                    <i class="bi bi-calendar-check"></i>

                    Revisado el

                    {{ \Carbon\Carbon::parse(
                            $pedido->comprobantePago->fecha_revision
                        )->format('d/m/Y H:i') }}

                </div>

                @endif

                @else

                {{-- SIN COMPROBANTE --}}

                <div class="cliente-pedido-show-comprobante-sin-registro">

                    <i class="bi bi-receipt"></i>

                    <strong>
                        Sin comprobante
                    </strong>

                    <span>
                        No se encontró un comprobante registrado.
                    </span>

                </div>

                @endif

            </div>

        </div>


        {{-- =====================================================
         AYUDA
         ===================================================== --}}

        <div class="cliente-pedido-show-ayuda">

            <i class="bi bi-shield-check"></i>

            <div>

                <strong>
                    Información segura
                </strong>

                <p>
                    Los datos de tu pedido son visibles únicamente
                    para ti y el personal autorizado del restaurante.
                </p>

            </div>

        </div>

    </div>
</div>

</div>

@endsection