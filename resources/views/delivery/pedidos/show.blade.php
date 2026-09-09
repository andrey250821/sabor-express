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

    $tieneCoordenadas = is_numeric($latitud) && is_numeric($longitud);

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

            <a href="{{ route('delivery.pedidos.index') }}"
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

                    $pasoCompletado = $numeroPaso < $pasoActual;

                    $pasoActivo = $numeroPaso === $pasoActual;
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


            <div
                id="mapa-entrega"
                class="delivery-map">
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
     GOOGLE MAPS
================================================================ --}}

@push('scripts')

    @if($tieneCoordenadas)

        <script>

            function initDeliveryMap() {

                const lat = {{ (float) $latitud }};

                const lng = {{ (float) $longitud }};


                const position = {
                    lat: lat,
                    lng: lng
                };


                const mapElement = document.getElementById('mapa-entrega');


                if (!mapElement) {
                    console.error('No se encontró el elemento #mapa-entrega');
                    return;
                }


                const map = new google.maps.Map(
                    mapElement,
                    {
                        center: position,
                        zoom: 16,
                        mapTypeControl: false,
                        streetViewControl: false,
                        fullscreenControl: true,
                        zoomControl: true
                    }
                );


                const marker = new google.maps.Marker(
                    {
                        position: position,
                        map: map,
                        title: 'Ubicación de entrega',
                        animation: google.maps.Animation.DROP
                    }
                );


                const infoWindow = new google.maps.InfoWindow(
                    {
                        content: `
                            <div style="padding: 8px; max-width: 260px;">
                                <strong>
                                    Entrega #{{ $pedido->id }}
                                </strong>
                                <br>
                                {{ addslashes($pedido->direccion_entrega ?? 'Ubicación del pedido') }}
                            </div>
                        `
                    }
                );


                marker.addListener(
                    'click',
                    function() {
                        infoWindow.open(
                            {
                                map: map,
                                anchor: marker
                            }
                        );
                    }
                );

            }

        </script>


        @if(config('services.google_maps.key'))

            <script
                src="https://maps.googleapis.com/maps/api/js?key={{ urlencode(config('services.google_maps.key')) }}&callback=initDeliveryMap"
                async
                defer>
            </script>

        @else

            <script>

                document.addEventListener(
                    'DOMContentLoaded',
                    function() {

                        const mapa =
                            document.getElementById('mapa-entrega');


                        if (!mapa) {
                            return;
                        }


                        mapa.innerHTML = `
                            <div style="
                                width: 100%;
                                height: 100%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                flex-direction: column;
                                gap: 10px;
                                color: #777;
                                background: #f3f3f5;
                                text-align: center;
                                padding: 20px;
                            ">

                                <i
                                    class="bi bi-map"
                                    style="font-size: 42px;">
                                </i>

                                <strong>
                                    Mapa no disponible
                                </strong>

                                <span>
                                    Configura la clave de Google Maps para visualizar el mapa.
                                </span>

                            </div>
                        `;

                    }
                );

            </script>

        @endif

    @endif

@endpush