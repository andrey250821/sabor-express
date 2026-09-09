@extends('layouts.delivery')

@section('title', 'Detalle del pedido')

@section('section', 'Pedidos')

@section('heading', 'Detalle del pedido')

@section('content')

@php
/*
|--------------------------------------------------------------------------
| Datos principales
|--------------------------------------------------------------------------
*/

$asignacion = $pedido->asignacionDelivery;

$esMiAsignacion = $asignacion
&& (int) $asignacion->delivery_id === (int) auth()->id();

$estadoPedido = $pedido->estado ?? 'listo';

/*
|--------------------------------------------------------------------------
| Estados
|--------------------------------------------------------------------------
*/

$estados = [
'listo' => [
'texto' => 'Listo para entregar',
'icono' => 'bi-box-seam',
'clase' => 'listo',
],

'asignado' => [
'texto' => 'Pedido tomado',
'icono' => 'bi-person-check',
'clase' => 'asignado',
],

'en_camino' => [
'texto' => 'En camino',
'icono' => 'bi-bicycle',
'clase' => 'camino',
],

'entregado' => [
'texto' => 'Pedido entregado',
'icono' => 'bi-check-circle-fill',
'clase' => 'entregado',
],
];

$estadoActual = $estados[$estadoPedido] ?? [
'texto' => ucfirst(str_replace('_', ' ', $estadoPedido)),
'icono' => 'bi-info-circle',
'clase' => 'default',
];

$ordenEstados = [
'listo' => 1,
'asignado' => 2,
'en_camino' => 3,
'entregado' => 4,
];

$pasoActual = $ordenEstados[$estadoPedido] ?? 1;

/*
|--------------------------------------------------------------------------
| Fecha
|--------------------------------------------------------------------------
*/

$fechaPedido = $pedido->created_at
? $pedido->created_at->format('d/m/Y H:i')
: 'Sin fecha';

/*
|--------------------------------------------------------------------------
| Coordenadas
|--------------------------------------------------------------------------
*/

$latitud = $pedido->latitud;
$longitud = $pedido->longitud;

$tieneCoordenadas =
is_numeric($latitud) &&
is_numeric($longitud);

/*
|--------------------------------------------------------------------------
| Permisos de acción
|--------------------------------------------------------------------------
*/

$puedeTomar =
$estadoPedido === 'listo'
&& !$asignacion;

$puedeIniciar =
$estadoPedido === 'asignado'
&& $esMiAsignacion
&& $asignacion->estado === 'aceptado';

$puedeEntregar =
$estadoPedido === 'en_camino'
&& $esMiAsignacion
&& $asignacion->estado === 'en_camino';

$pedidoDeOtroDelivery =
$asignacion
&& !$esMiAsignacion
&& $estadoPedido !== 'entregado';
@endphp


<div class="delivery-show">

    {{-- ============================================================
         ENCABEZADO
    ============================================================= --}}

    <div class="delivery-show-header">

        <div>

            <a
                href="{{ route('delivery.pedidos.index') }}"
                class="delivery-show-back">

                <i class="bi bi-arrow-left"></i>

                Volver a pedidos

            </a>


            <div class="delivery-show-title-row">

                <div class="delivery-show-order-icon">

                    <i class="bi bi-receipt-cutoff"></i>

                </div>


                <div>

                    <div class="delivery-show-eyebrow">
                        DETALLE DEL PEDIDO
                    </div>

                    <h1>
                        Pedido #{{ $pedido->id }}
                    </h1>

                    <p>
                        Revisa la información necesaria para realizar la entrega.
                    </p>

                </div>

            </div>

        </div>


        <div class="delivery-show-header-date">

            <i class="bi bi-calendar3"></i>

            <div>

                <span>Realizado</span>

                <strong>
                    {{ $fechaPedido }}
                </strong>

            </div>

        </div>

    </div>


    {{-- ============================================================
         ALERTAS
    ============================================================= --}}

    @if(session('success'))

    <div class="alert alert-success delivery-show-alert">

        <i class="bi bi-check-circle-fill"></i>

        <span>
            {{ session('success') }}
        </span>

    </div>

    @endif


    @if(session('error'))

    <div class="alert alert-danger delivery-show-alert">

        <i class="bi bi-exclamation-triangle-fill"></i>

        <span>
            {{ session('error') }}
        </span>

    </div>

    @endif


    {{-- ============================================================
         ESTADO DEL PEDIDO
    ============================================================= --}}

    <div class="delivery-show-card delivery-status-card">

        <div class="delivery-show-card-header">

            <div class="delivery-show-card-heading">

                <div class="delivery-show-card-icon status-icon">

                    <i class="bi {{ $estadoActual['icono'] }}"></i>

                </div>


                <div>

                    <span>Estado actual</span>

                    <h2>
                        {{ $estadoActual['texto'] }}
                    </h2>

                </div>

            </div>


            <span class="delivery-status-badge {{ $estadoActual['clase'] }}">

                <i class="bi {{ $estadoActual['icono'] }}"></i>

                {{ $estadoActual['texto'] }}

            </span>

        </div>


        <div class="delivery-progress">

            @php

            $pasos = [

            [
            'estado' => 'listo',
            'texto' => 'Listo',
            'icono' => 'bi-box-seam',
            ],

            [
            'estado' => 'asignado',
            'texto' => 'Asignado',
            'icono' => 'bi-person-check',
            ],

            [
            'estado' => 'en_camino',
            'texto' => 'En camino',
            'icono' => 'bi-bicycle',
            ],

            [
            'estado' => 'entregado',
            'texto' => 'Entregado',
            'icono' => 'bi-check-circle-fill',
            ],

            ];

            @endphp


            @foreach($pasos as $indice => $paso)

            @php

            $numeroPaso = $indice + 1;

            $pasoCompletado =
            $numeroPaso < $pasoActual;

                $pasoActivo=$numeroPaso===$pasoActual;

                @endphp


                <div class="delivery-progress-step
                    {{ $pasoCompletado ? 'completed' : '' }}
                    {{ $pasoActivo ? 'active' : '' }}">

                <div class="delivery-progress-circle">

                    @if($pasoCompletado)

                    <i class="bi bi-check"></i>

                    @else

                    <i class="bi {{ $paso['icono'] }}"></i>

                    @endif

                </div>


                <span>
                    {{ $paso['texto'] }}
                </span>

        </div>


        @if(!$loop->last)

        <div class="delivery-progress-line
                        {{ $numeroPaso < $pasoActual ? 'completed' : '' }}">
        </div>

        @endif

        @endforeach

    </div>

</div>


{{-- ============================================================
         INFORMACIÓN PRINCIPAL
    ============================================================= --}}

<div class="delivery-show-grid">


    {{-- ========================================================
             CLIENTE
        ========================================================= --}}

    <div class="delivery-show-card">

        <div class="delivery-show-card-header">

            <div class="delivery-show-card-heading">

                <div class="delivery-show-card-icon">

                    <i class="bi bi-person"></i>

                </div>


                <div>

                    <span>Cliente</span>

                    <h2>
                        Información del cliente
                    </h2>

                </div>

            </div>

        </div>


        @if($pedido->user)

        <div class="delivery-client">

            <div class="delivery-client-avatar">

                {{ strtoupper(substr($pedido->user->name ?? 'C', 0, 1)) }}

            </div>


            <div class="delivery-client-main">

                <strong>
                    {{ $pedido->user->name }}
                </strong>

                <span>
                    Cliente
                </span>

            </div>

        </div>


        <div class="delivery-info-list">

            @if(!empty($pedido->user->telefono))

            <div class="delivery-info-item">

                <div class="delivery-info-item-icon">

                    <i class="bi bi-telephone"></i>

                </div>


                <div>

                    <span>Teléfono</span>

                    <strong>
                        {{ $pedido->user->telefono }}
                    </strong>

                </div>

            </div>

            @endif


            @if(!empty($pedido->user->email))

            <div class="delivery-info-item">

                <div class="delivery-info-item-icon">

                    <i class="bi bi-envelope"></i>

                </div>


                <div>

                    <span>Correo electrónico</span>

                    <strong>
                        {{ $pedido->user->email }}
                    </strong>

                </div>

            </div>

            @endif

        </div>

        @else

        <div class="delivery-empty-info">

            <i class="bi bi-person-x"></i>

            <span>
                No hay información del cliente.
            </span>

        </div>

        @endif

    </div>


    {{-- ========================================================
             ENTREGA
        ========================================================= --}}

    <div class="delivery-show-card">

        <div class="delivery-show-card-header">

            <div class="delivery-show-card-heading">

                <div class="delivery-show-card-icon">

                    <i class="bi bi-geo-alt"></i>

                </div>


                <div>

                    <span>Entrega</span>

                    <h2>
                        Destino del pedido
                    </h2>

                </div>

            </div>

        </div>


        <div class="delivery-location-main">

            <div class="delivery-location-icon">

                <i class="bi bi-geo-alt-fill"></i>

            </div>


            <div>

                <span>Dirección</span>

                <strong>
                    {{ $pedido->direccion_entrega ?? 'Sin dirección registrada' }}
                </strong>

            </div>

        </div>


        @if(!empty($pedido->referencia_delivery))

        <div class="delivery-reference">

            <div class="delivery-reference-icon">

                <i class="bi bi-signpost-2"></i>

            </div>


            <div>

                <span>Referencia</span>

                <p>
                    {{ $pedido->referencia_delivery }}
                </p>

            </div>

        </div>

        @endif


        @if($tieneCoordenadas)

        <div class="delivery-coordinates">

            <div>

                <span>Latitud</span>

                <strong>
                    {{ $latitud }}
                </strong>

            </div>


            <div>

                <span>Longitud</span>

                <strong>
                    {{ $longitud }}
                </strong>

            </div>

        </div>

        @endif

    </div>

</div>


{{-- ============================================================
         MAPA
    ============================================================= --}}

@if($tieneCoordenadas)

<div class="delivery-show-card delivery-map-card">

    <div class="delivery-show-card-header">

        <div class="delivery-show-card-heading">

            <div class="delivery-show-card-icon">

                <i class="bi bi-map"></i>

            </div>


            <div>

                <span>Ubicación</span>

                <h2>
                    Mapa de entrega
                </h2>

            </div>

        </div>


        <a
            href="https://www.google.com/maps?q={{ $latitud }},{{ $longitud }}"
            target="_blank"
            rel="noopener noreferrer"
            class="delivery-map-external">

            <i class="bi bi-box-arrow-up-right"></i>

            Abrir en Google Maps

        </a>

    </div>


    {{-- ====================================================
                 CONTENEDOR DEL MAPA
            ===================================================== --}}

    <div
        id="mapa-entrega"
        class="delivery-map">
    </div>

    <div
        id="delivery-gps"
        data-pedido-id="{{ $pedido->id }}"
        data-delivery-id="{{ auth()->id() }}"
        data-gps-activo="{{ $estadoPedido === 'en_camino' && $esMiAsignacion ? '1' : '0' }}"
        class="delivery-gps-status-wrapper">

        <i class="bi bi-geo-alt-fill"></i>

        <span id="delivery-gps-status">
            {{ $estadoPedido === 'en_camino' && $esMiAsignacion
            ? 'Preparando GPS...'
            : 'GPS detenido' }}
        </span>

    </div>

</div>

@else

<div class="delivery-show-card delivery-no-map">

    <i class="bi bi-geo-alt"></i>

    <div>

        <strong>
            No hay coordenadas disponibles
        </strong>

        <span>
            Utiliza la dirección y referencia proporcionadas para llegar al domicilio.
        </span>

    </div>

</div>

@endif


{{-- ============================================================
         PRODUCTOS
    ============================================================= --}}

<div class="delivery-show-card delivery-products-card">

    <div class="delivery-show-card-header">

        <div class="delivery-show-card-heading">

            <div class="delivery-show-card-icon">

                <i class="bi bi-bag-check"></i>

            </div>


            <div>

                <span>Pedido</span>

                <h2>
                    Productos
                </h2>

            </div>

        </div>


        <span class="delivery-products-count">

            {{ $pedido->detallePedidos->count() }}

            {{ $pedido->detallePedidos->count() === 1 ? 'producto' : 'productos' }}

        </span>

    </div>


    <div class="delivery-products-list">

        @forelse($pedido->detallePedidos as $detalle)

        @php
        $producto = $detalle->producto;
        @endphp


        <div class="delivery-product">

            <div class="delivery-product-image">

                @if($producto && !empty($producto->imagen))

                <img
                    src="{{ asset('storage/' . $producto->imagen) }}"
                    alt="{{ $producto->nombre }}">

                @else

                <div class="delivery-product-placeholder">

                    <i class="bi bi-image"></i>

                </div>

                @endif

            </div>


            <div class="delivery-product-info">

                <strong>
                    {{ $producto->nombre ?? 'Producto' }}
                </strong>

                <span>

                    {{ $detalle->cantidad }}

                    ×

                    Bs {{ number_format((float) $detalle->precio, 2) }}

                </span>

            </div>


            <div class="delivery-product-subtotal">

                Bs {{ number_format((float) $detalle->subtotal, 2) }}

            </div>

        </div>

        @empty

        <div class="delivery-empty-info">

            <i class="bi bi-bag-x"></i>

            <span>
                No hay productos registrados en este pedido.
            </span>

        </div>

        @endforelse

    </div>


    <div class="delivery-total">

        <div>

            <span>
                Total del pedido
            </span>

            <small>
                Importe total
            </small>

        </div>


        <strong>

            Bs {{ number_format((float) $pedido->total, 2) }}

        </strong>

    </div>

</div>


{{-- ============================================================
         ACCIONES
    ============================================================= --}}

<div class="delivery-action-card">


    {{-- ========================================================
             PEDIDO DISPONIBLE
        ========================================================= --}}

    @if($puedeTomar)

    <div class="delivery-action-icon take">

        <i class="bi bi-hand-index-thumb"></i>

    </div>


    <div class="delivery-action-content">

        <span>
            Pedido disponible
        </span>

        <h2>
            ¿Deseas tomar este pedido?
        </h2>

        <p>
            Al tomarlo, el pedido quedará asignado a ti y podrás iniciar la entrega cuando estés listo.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route('delivery.pedidos.tomar', $pedido->id) }}"
        class="delivery-action-form">

        @csrf

        <button
            type="submit"
            class="delivery-action-button primary">

            <i class="bi bi-hand-index-thumb"></i>

            Tomar pedido

        </button>

    </form>


    {{-- ========================================================
             PEDIDO TOMADO POR ESTE DELIVERY
        ========================================================= --}}

    @elseif($puedeIniciar)

    <div class="delivery-action-icon start">

        <i class="bi bi-bicycle"></i>

    </div>


    <div class="delivery-action-content">

        <span>
            Pedido asignado
        </span>

        <h2>
            Listo para iniciar la entrega
        </h2>

        <p>
            Ya tienes este pedido asignado. Cuando salgas hacia el domicilio, inicia la entrega.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route('delivery.pedidos.iniciar', $pedido->id) }}"
        class="delivery-action-form">

        @csrf

        @method('PUT')

        <button
            type="submit"
            class="delivery-action-button primary">

            <i class="bi bi-bicycle"></i>

            Iniciar entrega

        </button>

    </form>


    {{-- ========================================================
             PEDIDO EN CAMINO
        ========================================================= --}}

    @elseif($puedeEntregar)

    <div class="delivery-action-icon finish">

        <i class="bi bi-check2-circle"></i>

    </div>


    <div class="delivery-action-content">

        <span>
            Entrega en curso
        </span>

        <h2>
            ¿Ya entregaste este pedido?
        </h2>

        <p>
            Marca el pedido como entregado solamente después de haberlo entregado al cliente.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route('delivery.pedidos.entregar', $pedido->id) }}"
        class="delivery-action-form">

        @csrf

        @method('PUT')

        <button
            type="submit"
            class="delivery-action-button success">

            <i class="bi bi-check2-circle"></i>

            Marcar como entregado

        </button>

    </form>


    {{-- ========================================================
             PEDIDO ENTREGADO
        ========================================================= --}}

    @elseif($estadoPedido === 'entregado')

    <div class="delivery-action-icon completed">

        <i class="bi bi-check-circle-fill"></i>

    </div>


    <div class="delivery-action-content">

        <span>
            Entrega completada
        </span>

        <h2>
            Pedido entregado correctamente
        </h2>

        <p>
            Este pedido ya fue marcado como entregado.
        </p>

    </div>


    <div class="delivery-action-complete-badge">

        <i class="bi bi-check-circle-fill"></i>

        Completado

    </div>


    {{-- ========================================================
             PEDIDO DE OTRO DELIVERY
        ========================================================= --}}

    @elseif($pedidoDeOtroDelivery)

    <div class="delivery-action-icon unavailable">

        <i class="bi bi-person-lock"></i>

    </div>


    <div class="delivery-action-content">

        <span>
            Pedido no disponible
        </span>

        <h2>
            Este pedido ya fue tomado
        </h2>

        <p>
            Otro repartidor ya tiene asignado este pedido.
        </p>

    </div>


    <div class="delivery-action-complete-badge unavailable">

        <i class="bi bi-person-check"></i>

        Ya asignado

    </div>


    {{-- ========================================================
             ESTADO NO DISPONIBLE
        ========================================================= --}}

    @else

    <div class="delivery-action-icon unavailable">

        <i class="bi bi-info-circle"></i>

    </div>


    <div class="delivery-action-content">

        <span>
            Información
        </span>

        <h2>
            Este pedido no tiene una acción disponible
        </h2>

        <p>
            El estado actual del pedido no permite realizar ninguna acción desde este momento.
        </p>

    </div>

    @endif

</div>

</div>

@endsection


{{-- ================================================================
     LEAFLET - CSS
================================================================ --}}

@push('styles')

@if($tieneCoordenadas)

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

@endif

@endpush


{{-- ================================================================
     LEAFLET - JAVASCRIPT
================================================================ --}}

@push('scripts')

@if($tieneCoordenadas)

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
</script>


<script>
    (function() {

        const latitud =
            Number(@json((float) $latitud));

        const longitud =
            Number(@json((float) $longitud));


        function mostrarErrorMapa(mensaje) {

            const contenedor =
                document.getElementById('mapa-entrega');

            if (!contenedor) {
                return;
            }

            contenedor.innerHTML = `

                        <div class="delivery-map-error">

                            <i class="bi bi-exclamation-triangle-fill"></i>

                            <strong>
                                No se pudo cargar el mapa
                            </strong>

                            <span>
                                ${mensaje}
                            </span>

                        </div>

                    `;

        }


        function iniciarMapaEntrega() {

            const contenedor =
                document.getElementById('mapa-entrega');


            /*
            |--------------------------------------------------------------------------
            | Verificar contenedor
            |--------------------------------------------------------------------------
            */

            if (!contenedor) {

                console.error(
                    'Sabor Express: no existe #mapa-entrega'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Verificar coordenadas
            |--------------------------------------------------------------------------
            */

            if (
                !Number.isFinite(latitud) ||
                !Number.isFinite(longitud)
            ) {

                console.error(
                    'Sabor Express: coordenadas inválidas', {
                        latitud: latitud,
                        longitud: longitud
                    }
                );

                mostrarErrorMapa(
                    'Las coordenadas del pedido no son válidas.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Verificar Leaflet
            |--------------------------------------------------------------------------
            */

            if (typeof L === 'undefined') {

                console.error(
                    'Sabor Express: Leaflet no se cargó.'
                );

                mostrarErrorMapa(
                    'Leaflet no pudo cargarse. Verifica tu conexión a Internet.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Evitar inicializar dos veces
            |--------------------------------------------------------------------------
            */

            if (contenedor._leaflet_id) {

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Crear mapa
            |--------------------------------------------------------------------------
            */

            const mapa =
                L.map(
                    contenedor, {
                        center: [
                            latitud,
                            longitud
                        ],

                        zoom: 17,

                        zoomControl: true,

                        scrollWheelZoom: true,

                        dragging: true,

                        doubleClickZoom: true
                    }
                );


            /*
            |--------------------------------------------------------------------------
            | OpenStreetMap
            |--------------------------------------------------------------------------
            */

            const capaOpenStreetMap =
                L.tileLayer(
                    'https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,

                        attribution: '&copy; OpenStreetMap contributors'
                    }
                );


            capaOpenStreetMap.addTo(mapa);


            /*
            |--------------------------------------------------------------------------
            | Detectar errores de las imágenes del mapa
            |--------------------------------------------------------------------------
            */

            capaOpenStreetMap.on(
                'tileerror',
                function(evento) {

                    console.error(
                        'Sabor Express: error cargando los mapas de OpenStreetMap.',
                        evento
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Marcador
            |--------------------------------------------------------------------------
            */

            const marcador =
                L.marker([
                    latitud,
                    longitud
                ]).addTo(mapa);


            /*
            |--------------------------------------------------------------------------
            | Popup
            |--------------------------------------------------------------------------
            */

            marcador.bindPopup(`

                        <div class="delivery-leaflet-popup">

                            <strong>
                                Entrega #{{ $pedido->id }}
                            </strong>

                            <span>
                                {{ $pedido->direccion_entrega ?? 'Ubicación del pedido' }}
                            </span>

                        </div>

                    `);


            /*
            |--------------------------------------------------------------------------
            | Mostrar popup
            |--------------------------------------------------------------------------
            */

            marcador.openPopup();


            /*
            |--------------------------------------------------------------------------
            | Corregir tamaño del mapa
            |--------------------------------------------------------------------------
            */

            setTimeout(
                function() {

                    mapa.invalidateSize(true);

                },
                100
            );


            setTimeout(
                function() {

                    mapa.invalidateSize(true);

                },
                500
            );


            setTimeout(
                function() {

                    mapa.invalidateSize(true);

                },
                1000
            );


            /*
            |--------------------------------------------------------------------------
            | Corregir tamaño al redimensionar
            |--------------------------------------------------------------------------
            */

            window.addEventListener(
                'resize',
                function() {

                    mapa.invalidateSize(true);

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Guardar referencia global
            |--------------------------------------------------------------------------
            */

            window.mapaEntregaDelivery =
                mapa;


            /*
            |--------------------------------------------------------------------------
            | Mensaje de diagnóstico
            |--------------------------------------------------------------------------
            */

            console.log(
                'Sabor Express: mapa de entrega cargado correctamente.', {
                    latitud: latitud,
                    longitud: longitud
                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Ejecutar cuando el DOM esté listo
        |--------------------------------------------------------------------------
        */

        if (
            document.readyState === 'loading'
        ) {

            document.addEventListener(
                'DOMContentLoaded',
                iniciarMapaEntrega
            );

        } else {

            iniciarMapaEntrega();

        }

    })();
</script>

@endif

@endpush