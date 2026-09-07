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
                    {{-- =====================================================
     CALIFICACIÓN DEL PRODUCTO
====================================================== --}}

                    @if($detalle->producto)

                    @php
                    $calificacion = $calificaciones->get($detalle->producto_id);
                    @endphp

                    <div
                        class="cliente-pedido-show-calificacion"
                        data-calificacion-container
                        data-producto-id="{{ $detalle->producto_id }}"
                        data-pedido-id="{{ $pedido->id }}">

                        @if($pedido->estado === 'entregado')

                        @if($calificacion)

                        {{-- CALIFICACIÓN EXISTENTE --}}
                        <div class="cliente-pedido-show-calificacion-realizada">

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                                <div>
                                    <strong>
                                        <i class="bi bi-star-fill me-1"></i>
                                        Tu calificación
                                    </strong>
                                </div>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-warning"
                                    data-editar-calificacion>
                                    <i class="bi bi-pencil me-1"></i>
                                    Editar
                                </button>

                            </div>

                            <div
                                class="cliente-pedido-show-calificacion-estrellas mt-2"
                                data-calificacion-estrellas>
                                @for($i = 1; $i <= 5; $i++)

                                    @if($i <=$calificacion->puntuacion)
                                    <i class="bi bi-star-fill"></i>
                                    @else
                                    <i class="bi bi-star"></i>
                                    @endif

                                    @endfor
                            </div>

                            <div
                                class="cliente-pedido-show-calificacion-puntuacion"
                                data-calificacion-puntuacion>
                                {{ $calificacion->puntuacion }}/5
                            </div>

                            @if($calificacion->comentario)

                            <p
                                class="cliente-pedido-show-calificacion-comentario"
                                data-calificacion-comentario>
                                "{{ $calificacion->comentario }}"
                            </p>

                            @else

                            <p
                                class="cliente-pedido-show-calificacion-comentario text-muted"
                                data-calificacion-comentario>
                                Sin comentario.
                            </p>

                            @endif

                        </div>


                        {{-- FORMULARIO PARA EDITAR --}}
                        <div
                            class="cliente-pedido-show-calificacion-formulario d-none"
                            data-calificacion-formulario>

                            <form
                                data-calificacion-form
                                data-method="PUT"
                                action="{{ route('cliente.calificaciones.update', [
                            'pedidoId' => $pedido->id,
                            'productoId' => $detalle->producto_id
                        ]) }}">

                                @csrf

                                @method('PUT')

                                <div class="mb-3">

                                    <label class="form-label fw-semibold">
                                        Cambia tu calificación
                                    </label>

                                    <div
                                        class="cliente-rating-selector"
                                        data-rating-selector>

                                        @for($i = 5; $i >= 1; $i--)

                                        <input
                                            type="radio"
                                            id="editar-rating-{{ $pedido->id }}-{{ $detalle->producto_id }}-{{ $i }}"
                                            name="puntuacion"
                                            value="{{ $i }}"
                                            @checked($calificacion->puntuacion == $i)
                                        >

                                        <label
                                            for="editar-rating-{{ $pedido->id }}-{{ $detalle->producto_id }}-{{ $i }}"
                                            title="{{ $i }} estrellas">
                                            <i class="bi bi-star-fill"></i>
                                        </label>

                                        @endfor

                                    </div>

                                </div>


                                <div class="mb-3">

                                    <label
                                        for="editar-comentario-{{ $pedido->id }}-{{ $detalle->producto_id }}"
                                        class="form-label fw-semibold">
                                        Comentario
                                    </label>

                                    <textarea
                                        id="editar-comentario-{{ $pedido->id }}-{{ $detalle->producto_id }}"
                                        name="comentario"
                                        class="form-control"
                                        rows="3"
                                        maxlength="1000"
                                        placeholder="Cuéntanos qué te pareció...">{{ $calificacion->comentario }}</textarea>

                                </div>


                                <div class="d-flex gap-2 flex-wrap">

                                    <button
                                        type="submit"
                                        class="btn btn-warning"
                                        data-calificacion-submit>
                                        <i class="bi bi-check-lg me-1"></i>
                                        Guardar cambios
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        data-cancelar-edicion>
                                        Cancelar
                                    </button>

                                </div>

                            </form>

                        </div>


                        @else

                        {{-- NUEVA CALIFICACIÓN --}}
                        <div class="cliente-pedido-show-calificacion-formulario">

                            <form
                                data-calificacion-form
                                data-method="POST"
                                action="{{ route('cliente.calificaciones.store', [
                            'pedidoId' => $pedido->id,
                            'productoId' => $detalle->producto_id
                        ]) }}">

                                @csrf

                                <div class="mb-3">

                                    <label class="form-label fw-semibold">
                                        ¿Qué te pareció este producto?
                                    </label>

                                    <div
                                        class="cliente-rating-selector"
                                        data-rating-selector>

                                        @for($i = 5; $i >= 1; $i--)

                                        <input
                                            type="radio"
                                            id="rating-{{ $pedido->id }}-{{ $detalle->producto_id }}-{{ $i }}"
                                            name="puntuacion"
                                            value="{{ $i }}">

                                        <label
                                            for="rating-{{ $pedido->id }}-{{ $detalle->producto_id }}-{{ $i }}"
                                            title="{{ $i }} estrellas">
                                            <i class="bi bi-star-fill"></i>
                                        </label>

                                        @endfor

                                    </div>

                                </div>


                                <div class="mb-3">

                                    <label
                                        for="comentario-{{ $pedido->id }}-{{ $detalle->producto_id }}"
                                        class="form-label fw-semibold">
                                        Comentario
                                        <span class="text-muted fw-normal">
                                            (opcional)
                                        </span>
                                    </label>

                                    <textarea
                                        id="comentario-{{ $pedido->id }}-{{ $detalle->producto_id }}"
                                        name="comentario"
                                        class="form-control"
                                        rows="3"
                                        maxlength="1000"
                                        placeholder="Cuéntanos qué te pareció..."></textarea>

                                </div>


                                <button
                                    type="submit"
                                    class="btn btn-warning"
                                    data-calificacion-submit>
                                    <i class="bi bi-star-fill me-1"></i>
                                    Enviar calificación
                                </button>

                            </form>

                        </div>

                        @endif

                        @endif

                    </div>

                    @endif
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
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const formularios = document.querySelectorAll(
            '[data-calificacion-form]'
        );

        formularios.forEach(function(formulario) {

            formulario.addEventListener('submit', async function(event) {

                event.preventDefault();

                const container = formulario.closest(
                    '[data-calificacion-container]'
                );

                if (!container) {
                    return;
                }

                const boton = formulario.querySelector(
                    '[data-calificacion-submit]'
                );

                const puntuacion = formulario.querySelector(
                    'input[name="puntuacion"]:checked'
                );

                const comentario = formulario.querySelector(
                    'textarea[name="comentario"]'
                );

                /*
                 * Comprobar que se haya seleccionado
                 * una puntuación.
                 */
                if (!puntuacion) {

                    alert('Selecciona una puntuación de 1 a 5 estrellas.');

                    return;
                }

                const url = formulario.action;

                const metodo = formulario.dataset.method || 'POST';

                const datos = new FormData();

                datos.append(
                    '_token',
                    formulario.querySelector(
                        'input[name="_token"]'
                    ).value
                );

                datos.append(
                    'puntuacion',
                    puntuacion.value
                );

                datos.append(
                    'comentario',
                    comentario ? comentario.value : ''
                );

                /*
                 * Para editar enviamos PUT.
                 */
                if (metodo === 'PUT') {
                    datos.append('_method', 'PUT');
                }

                /*
                 * Evitar doble clic.
                 */
                const textoOriginal = boton.innerHTML;

                boton.disabled = true;

                boton.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm me-1"
                    role="status"
                ></span>
                Guardando...
            `;

                try {

                    const respuesta = await fetch(url, {

                        method: 'POST',

                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },

                        body: datos

                    });

                    const resultado = await respuesta.json();

                    if (!respuesta.ok || !resultado.success) {

                        throw new Error(
                            resultado.message ||
                            'No fue posible guardar la calificación.'
                        );
                    }

                    /*
                     * Actualizar solamente este producto.
                     */
                    mostrarCalificacion(
                        container,
                        resultado.calificacion
                    );

                    /*
                     * Mostrar mensaje de éxito.
                     */
                    mostrarMensajeCalificacion(
                        container,
                        resultado.message,
                        'success'
                    );

                } catch (error) {

                    console.error(
                        'Error al guardar la calificación:',
                        error
                    );

                    mostrarMensajeCalificacion(
                        container,
                        error.message ||
                        'Ocurrió un error al guardar la calificación.',
                        'danger'
                    );

                    boton.disabled = false;

                    boton.innerHTML = textoOriginal;
                }

            });

        });


        /*
         * Botón "Editar".
         */
        document.addEventListener(
            'click',
            function(event) {

                const botonEditar = event.target.closest(
                    '[data-editar-calificacion]'
                );

                if (!botonEditar) {
                    return;
                }

                const container = botonEditar.closest(
                    '[data-calificacion-container]'
                );

                if (!container) {
                    return;
                }

                const realizada = container.querySelector(
                    '.cliente-pedido-show-calificacion-realizada'
                );

                const formulario = container.querySelector(
                    '[data-calificacion-formulario]'
                );

                if (realizada) {
                    realizada.classList.add('d-none');
                }

                if (formulario) {
                    formulario.classList.remove('d-none');
                }

            }
        );


        /*
         * Botón "Cancelar".
         */
        document.addEventListener(
            'click',
            function(event) {

                const botonCancelar = event.target.closest(
                    '[data-cancelar-edicion]'
                );

                if (!botonCancelar) {
                    return;
                }

                const container = botonCancelar.closest(
                    '[data-calificacion-container]'
                );

                if (!container) {
                    return;
                }

                const realizada = container.querySelector(
                    '.cliente-pedido-show-calificacion-realizada'
                );

                const formulario = container.querySelector(
                    '[data-calificacion-formulario]'
                );

                if (formulario) {
                    formulario.classList.add('d-none');
                }

                if (realizada) {
                    realizada.classList.remove('d-none');
                }

            }
        );


        /*
         * Mostrar la calificación recién guardada
         * sin recargar la página.
         */
        function mostrarCalificacion(
            container,
            calificacion
        ) {

            const formularioPrincipal = container.querySelector(
                '[data-calificacion-form]'
            );

            const formularioEdicion = container.querySelector(
                '[data-calificacion-formulario]'
            );

            /*
             * Crear nuevamente la sección visual
             * de calificación realizada.
             */
            let realizada = container.querySelector(
                '.cliente-pedido-show-calificacion-realizada'
            );

            if (!realizada) {

                realizada = document.createElement('div');

                realizada.className =
                    'cliente-pedido-show-calificacion-realizada';

                container.prepend(realizada);
            }

            let estrellas = '';

            for (let i = 1; i <= 5; i++) {

                if (i <= calificacion.puntuacion) {

                    estrellas +=
                        '<i class="bi bi-star-fill"></i>';

                } else {

                    estrellas +=
                        '<i class="bi bi-star"></i>';

                }

            }

            const comentario = calificacion.comentario ?
                `"${escapeHtml(calificacion.comentario)}"` :
                'Sin comentario.';

            realizada.innerHTML = `

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div>
                    <strong>
                        <i class="bi bi-star-fill me-1"></i>
                        Tu calificación
                    </strong>
                </div>

                <button
                    type="button"
                    class="btn btn-sm btn-outline-warning"
                    data-editar-calificacion
                >
                    <i class="bi bi-pencil me-1"></i>
                    Editar
                </button>

            </div>

            <div
                class="cliente-pedido-show-calificacion-estrellas mt-2"
                data-calificacion-estrellas
            >
                ${estrellas}
            </div>

            <div
                class="cliente-pedido-show-calificacion-puntuacion"
                data-calificacion-puntuacion
            >
                ${calificacion.puntuacion}/5
            </div>

            <p
                class="cliente-pedido-show-calificacion-comentario"
                data-calificacion-comentario
            >
                ${comentario}
            </p>

        `;

            realizada.classList.remove('d-none');

            /*
             * Ocultar formulario de nueva calificación.
             */
            if (formularioPrincipal) {

                const formularioContenedor =
                    formularioPrincipal.closest(
                        '.cliente-pedido-show-calificacion-formulario'
                    );

                if (formularioContenedor) {
                    formularioContenedor.remove();
                }
            }

            /*
             * Ocultar formulario de edición.
             */
            if (formularioEdicion) {

                formularioEdicion.classList.add('d-none');
            }

            /*
             * Convertir el formulario actual en formulario
             * de edición para futuras modificaciones.
             */
            crearFormularioEdicion(
                container,
                calificacion
            );
        }


        /*
         * Crear formulario de edición después de guardar
         * una calificación nueva.
         */
        function crearFormularioEdicion(
            container,
            calificacion
        ) {

            let formularioEdicion = container.querySelector(
                '[data-calificacion-formulario]'
            );

            if (formularioEdicion) {

                actualizarFormularioEdicion(
                    formularioEdicion,
                    calificacion
                );

                return;
            }

            /*
             * El formulario de edición ya viene desde Blade
             * cuando la calificación existía.
             */
            console.log(
                'Formulario de edición preparado.'
            );
        }


        /*
         * Actualizar los datos del formulario de edición.
         */
        function actualizarFormularioEdicion(
            formulario,
            calificacion
        ) {

            const radios = formulario.querySelectorAll(
                'input[name="puntuacion"]'
            );

            radios.forEach(function(radio) {

                radio.checked =
                    Number(radio.value) ===
                    Number(calificacion.puntuacion);

            });

            const comentario = formulario.querySelector(
                'textarea[name="comentario"]'
            );

            if (comentario) {

                comentario.value =
                    calificacion.comentario || '';
            }
        }


        /*
         * Mostrar mensajes.
         */
        function mostrarMensajeCalificacion(
            container,
            mensaje,
            tipo
        ) {

            const anterior = container.querySelector(
                '[data-calificacion-mensaje]'
            );

            if (anterior) {
                anterior.remove();
            }

            const alerta = document.createElement('div');

            alerta.className =
                `alert alert-${tipo} mt-3`;

            alerta.setAttribute(
                'data-calificacion-mensaje',
                ''
            );

            alerta.innerHTML = escapeHtml(mensaje);

            container.prepend(alerta);

            setTimeout(function() {

                alerta.remove();

            }, 4000);
        }


        /*
         * Evitar insertar HTML proveniente
         * directamente del usuario.
         */
        function escapeHtml(texto) {

            const div = document.createElement('div');

            div.textContent = texto ?? '';

            return div.innerHTML;
        }

    });
</script>
@endsection