@extends('layouts.cliente')

@section('title', 'Confirmar pedido')

@section('content')

<div class="cliente-pedido-page">

    {{-- =========================================================
         ENCABEZADO
    ========================================================== --}}

    <div class="cliente-pedido-header">

        <div class="cliente-pedido-header-contenido">

            <span class="cliente-pedido-label">
                SABOR EXPRESS
            </span>

            <h1>
                <i class="bi bi-bag-check-fill"></i>
                Confirmar pedido
            </h1>

            <p>
                Completa los datos de entrega, selecciona tu ubicación
                y envía tu comprobante de pago.
            </p>

        </div>

        <div class="cliente-pedido-header-icono">
            <i class="bi bi-receipt-cutoff"></i>
        </div>

    </div>


    {{-- =========================================================
         MENSAJES
    ========================================================== --}}

    @if(session('error'))

    <div class="cliente-pedido-alert cliente-pedido-alert-error">

        <i class="bi bi-exclamation-circle-fill"></i>

        <span>
            {{ session('error') }}
        </span>

    </div>

    @endif


    @if(session('success'))

    <div class="cliente-pedido-alert cliente-pedido-alert-success">

        <i class="bi bi-check-circle-fill"></i>

        <span>
            {{ session('success') }}
        </span>

    </div>

    @endif


    @if($errors->any())

    <div class="cliente-pedido-alert cliente-pedido-alert-error">

        <i class="bi bi-exclamation-triangle-fill"></i>

        <div>

            <strong>
                Revisa los siguientes campos:
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

                @endforeach

            </ul>

        </div>

    </div>

    @endif


    {{-- =========================================================
         FORMULARIO
    ========================================================== --}}

    <form
        action="{{ route('cliente.pedidos.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="form-pedido">

        @csrf


        <div class="row g-4">


            {{-- =================================================
                 COLUMNA PRINCIPAL
            ================================================== --}}

            <div class="col-12 col-lg-8">


                {{-- =================================================
                     DATOS DE ENTREGA
                ================================================== --}}

                <div class="cliente-pedido-card">

                    <div class="cliente-pedido-card-header">

                        <div class="cliente-pedido-card-icono">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>

                        <div>

                            <h2>
                                Datos de entrega
                            </h2>

                            <p>
                                Indica dónde quieres recibir tu pedido.
                            </p>

                        </div>

                    </div>


                    <div class="cliente-pedido-card-body">


                        {{-- DIRECCIÓN --}}

                        <div class="cliente-pedido-campo">

                            <label for="direccion_entrega">

                                <i class="bi bi-house-door-fill"></i>

                                Dirección de entrega

                                <span>*</span>

                            </label>

                            <textarea
                                name="direccion_entrega"
                                id="direccion_entrega"
                                class="form-control cliente-pedido-input"
                                rows="3"
                                placeholder="La dirección se completará automáticamente al seleccionar tu ubicación."
                                required>{{ old('direccion_entrega') }}</textarea>

                            <small>
                                Puedes corregir manualmente la dirección si es necesario.
                            </small>

                        </div>


                        {{-- REFERENCIA --}}

                        <div class="cliente-pedido-campo">

                            <label for="referencia_delivery">

                                <i class="bi bi-signpost-2-fill"></i>

                                Referencia para el delivery

                            </label>

                            <textarea
                                name="referencia_delivery"
                                id="referencia_delivery"
                                class="form-control cliente-pedido-input"
                                rows="3"
                                maxlength="1000"
                                placeholder="Ej.: puerta verde, frente a la farmacia, al lado de la plaza...">{{ old('referencia_delivery') }}</textarea>

                            <small>
                                Agrega detalles que ayuden al repartidor a encontrar tu domicilio.
                            </small>

                        </div>


                        {{-- OBSERVACIONES --}}

                        <div class="cliente-pedido-campo">

                            <label for="observacion_cliente">

                                <i class="bi bi-chat-left-text-fill"></i>

                                Observaciones del pedido

                            </label>

                            <textarea
                                name="observacion_cliente"
                                id="observacion_cliente"
                                class="form-control cliente-pedido-input"
                                rows="3"
                                maxlength="500"
                                placeholder="Ej.: poca salsa, sin cebolla, tocar el timbre...">{{ old('observacion_cliente') }}</textarea>

                            <small>
                                Instrucciones especiales sobre tu pedido.
                            </small>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     UBICACIÓN
                ================================================== --}}

                <div class="cliente-pedido-card">

                    <div class="cliente-pedido-card-header">

                        <div class="cliente-pedido-card-icono">
                            <i class="bi bi-map-fill"></i>
                        </div>

                        <div>

                            <h2>
                                Ubicación de entrega
                            </h2>

                            <p>
                                Selecciona exactamente dónde debe llegar tu pedido.
                            </p>

                        </div>

                    </div>


                    <div class="cliente-pedido-card-body">


                        {{-- =================================================
                             MAPA LEAFLET / OPENSTREETMAP
                        ================================================== --}}

                        {{-- BOTONES --}}

                        <div class="cliente-pedido-mapa-botones">

                            <button
                                type="button"
                                id="btn-mi-ubicacion"
                                class="cliente-pedido-btn-ubicacion">

                                <i class="bi bi-crosshair"></i>

                                Usar mi ubicación

                            </button>


                            <button
                                type="button"
                                id="btn-direccion-mapa"
                                class="cliente-pedido-btn-secundario">

                                <i class="bi bi-arrow-repeat"></i>

                                Actualizar dirección

                            </button>

                        </div>


                        {{-- MAPA --}}

                        <div
                            id="mapa-pedido"
                            class="cliente-pedido-mapa">

                            <div class="cliente-pedido-mapa-cargando">

                                <i class="bi bi-map"></i>

                                <span>
                                    Cargando mapa...
                                </span>

                            </div>

                        </div>


                        {{-- COORDENADAS --}}

                        <div class="row g-3 mt-3">

                            <div class="col-12 col-md-6">

                                <div class="cliente-pedido-coordenada">

                                    <label for="latitud">

                                        <i class="bi bi-compass"></i>

                                        Latitud

                                    </label>

                                    <input
                                        type="text"
                                        name="latitud"
                                        id="latitud"
                                        class="form-control"
                                        value="{{ old('latitud') }}"
                                        placeholder="-17.3935000"
                                        readonly>

                                </div>

                            </div>


                            <div class="col-12 col-md-6">

                                <div class="cliente-pedido-coordenada">

                                    <label for="longitud">

                                        <i class="bi bi-compass"></i>

                                        Longitud

                                    </label>

                                    <input
                                        type="text"
                                        name="longitud"
                                        id="longitud"
                                        class="form-control"
                                        value="{{ old('longitud') }}"
                                        placeholder="-66.1570000"
                                        readonly>

                                </div>

                            </div>

                        </div>


                        {{-- ESTADO --}}

                        <div
                            id="estado-ubicacion"
                            class="cliente-pedido-estado-ubicacion"
                            aria-live="polite">

                            <i class="bi bi-info-circle"></i>

                            Selecciona tu ubicación en el mapa.

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     INFORMACIÓN DE PAGO
                ================================================== --}}

                <div class="cliente-pedido-card">

                    <div class="cliente-pedido-card-header">

                        <div class="cliente-pedido-card-icono">
                            <i class="bi bi-credit-card-fill"></i>
                        </div>

                        <div>

                            <h2>
                                Información de pago
                            </h2>

                            <p>
                                Realiza el pago utilizando el código QR del restaurante.
                            </p>

                        </div>

                    </div>


                    <div class="cliente-pedido-card-body">

                        @if($configuracion && !empty($configuracion->qr_pago))

                        <div class="cliente-pago-contenedor">


                            <div class="cliente-pago-qr">

                                <div class="cliente-pago-qr-titulo">

                                    <i class="bi bi-qr-code"></i>

                                    <span>
                                        Escanea el QR para pagar
                                    </span>

                                </div>


                                <div class="cliente-pago-qr-imagen">

                                    <img
                                        src="{{ asset('storage/' . $configuracion->qr_pago) }}"
                                        alt="Código QR de pago">

                                </div>


                                <div class="cliente-pago-qr-ayuda">

                                    <i class="bi bi-info-circle"></i>

                                    <span>
                                        Realiza el pago y luego sube una imagen
                                        clara de tu comprobante.
                                    </span>

                                </div>

                            </div>


                            <div class="cliente-pago-restaurante">

                                <div class="cliente-pago-restaurante-icono">

                                    <i class="bi bi-shop"></i>

                                </div>

                                <div>

                                    <span>
                                        Realizar pago a
                                    </span>

                                    <strong>
                                        {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}
                                    </strong>

                                </div>

                            </div>

                        </div>

                        @elseif($configuracion)

                        <div class="cliente-pedido-alert cliente-pedido-alert-warning">

                            <i class="bi bi-exclamation-triangle-fill"></i>

                            <div>

                                <strong>
                                    QR de pago no configurado
                                </strong>

                                <p>
                                    El administrador todavía no ha configurado
                                    el código QR de pago.
                                </p>

                            </div>

                        </div>

                        @else

                        <div class="cliente-pedido-alert cliente-pedido-alert-warning">

                            <i class="bi bi-exclamation-triangle-fill"></i>

                            <div>

                                <strong>
                                    Información de pago no disponible
                                </strong>

                                <p>
                                    El restaurante todavía no ha configurado
                                    sus datos de pago.
                                </p>

                            </div>

                        </div>

                        @endif

                    </div>

                </div>


                {{-- =================================================
                     COMPROBANTE
                ================================================== --}}

                <div class="cliente-pedido-card">

                    <div class="cliente-pedido-card-header">

                        <div class="cliente-pedido-card-icono">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                        </div>

                        <div>

                            <h2>
                                Comprobante de pago
                            </h2>

                            <p>
                                Sube una imagen clara del comprobante.
                            </p>

                        </div>

                    </div>


                    <div class="cliente-pedido-card-body">


                        <label
                            for="comprobante"
                            class="cliente-comprobante-area"
                            id="area-comprobante">

                            <div class="cliente-comprobante-icono">

                                <i class="bi bi-image"></i>

                            </div>

                            <strong>
                                Seleccionar comprobante
                            </strong>

                            <span>
                                Haz clic aquí para seleccionar una imagen
                            </span>

                            <small>
                                JPG, JPEG o PNG · Máximo 2 MB
                            </small>

                        </label>


                        <input
                            type="file"
                            name="comprobante"
                            id="comprobante"
                            accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                            required
                            hidden>


                        {{-- PREVISUALIZACIÓN --}}

                        <div
                            id="comprobante-preview"
                            class="cliente-comprobante-preview">

                            <div class="cliente-comprobante-preview-header">

                                <span>
                                    Vista previa del comprobante
                                </span>

                                <button
                                    type="button"
                                    id="btn-quitar-comprobante"
                                    aria-label="Quitar comprobante">

                                    <i class="bi bi-x-lg"></i>

                                </button>

                            </div>


                            <div class="cliente-comprobante-preview-body">

                                <img
                                    id="comprobante-imagen-preview"
                                    src=""
                                    alt="Vista previa del comprobante">

                            </div>

                        </div>


                        <div class="cliente-comprobante-info">

                            <i class="bi bi-shield-check"></i>

                            <span>
                                Asegúrate de que el comprobante sea legible
                                y muestre claramente el pago realizado.
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 RESUMEN
            ================================================== --}}

            <div class="col-12 col-lg-4">

                <div class="cliente-pedido-resumen">

                    <div class="cliente-pedido-resumen-header">

                        <span>
                            SABOR EXPRESS
                        </span>

                        <h2>

                            <i class="bi bi-receipt"></i>

                            Resumen del pedido

                        </h2>

                    </div>


                    <div class="cliente-pedido-resumen-body">


                        {{-- PRODUCTOS --}}

                        <div class="cliente-pedido-resumen-linea">

                            <span>

                                <i class="bi bi-bag-fill"></i>

                                Productos

                            </span>

                            <strong>

                                {{ collect(session('carrito', []))->sum('cantidad') }}

                            </strong>

                        </div>


                        {{-- SUBTOTAL --}}

                        <div class="cliente-pedido-resumen-linea">

                            <span>

                                <i class="bi bi-calculator-fill"></i>

                                Subtotal

                            </span>

                            <strong>

                                Bs. {{ number_format($total, 2) }}

                            </strong>

                        </div>


                        {{-- DELIVERY --}}

                        <div class="cliente-pedido-resumen-linea">

                            <span>

                                <i class="bi bi-bicycle"></i>

                                Delivery

                            </span>

                            <strong class="cliente-resumen-pendiente">

                                Se calculará después

                            </strong>

                        </div>


                        <div class="cliente-pedido-resumen-separador"></div>


                        {{-- TOTAL --}}

                        <div class="cliente-pedido-resumen-total">

                            <span>
                                Total
                            </span>

                            <strong>
                                Bs. {{ number_format($total, 2) }}
                            </strong>

                        </div>


                        {{-- INFORMACIÓN --}}

                        <div class="cliente-pedido-resumen-info">

                            <i class="bi bi-shield-check"></i>

                            <span>
                                Tu pedido será enviado al restaurante
                                después de validar el comprobante.
                            </span>

                        </div>


                        {{-- CONFIRMAR --}}

                        <button
                            type="submit"
                            class="cliente-pedido-btn-confirmar"
                            id="btn-confirmar-pedido">

                            <i class="bi bi-check-circle-fill"></i>

                            Confirmar pedido

                        </button>


                        {{-- VOLVER --}}

                        <a
                            href="{{ route('cliente.carrito.index') }}"
                            class="cliente-pedido-btn-volver">

                            <i class="bi bi-arrow-left"></i>

                            Volver al carrito

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>

@endsection


@section('scripts')

{{-- =========================================================
     LEAFLET
========================================================== --}}

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">


<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        let mapaPedido = null;
        let marcadorPedido = null;

        let ultimaConsultaDireccion = 0;
        let consultaDireccionEnCurso = false;
        let temporizadorDireccion = null;


        const cochabamba = {
            lat: -17.3935,
            lng: -66.1570
        };


        /*
        |--------------------------------------------------------------------------
        | ELEMENTOS
        |--------------------------------------------------------------------------
        */

        const latitudInput =
            document.getElementById('latitud');

        const longitudInput =
            document.getElementById('longitud');

        const direccionInput =
            document.getElementById('direccion_entrega');

        const estadoUbicacion =
            document.getElementById('estado-ubicacion');

        const btnMiUbicacion =
            document.getElementById('btn-mi-ubicacion');

        const btnDireccionMapa =
            document.getElementById('btn-direccion-mapa');

        const inputComprobante =
            document.getElementById('comprobante');

        const preview =
            document.getElementById('comprobante-preview');

        const imagenPreview =
            document.getElementById('comprobante-imagen-preview');

        const btnQuitarComprobante =
            document.getElementById('btn-quitar-comprobante');

        const areaComprobante =
            document.getElementById('area-comprobante');

        const formulario =
            document.getElementById('form-pedido');

        const botonConfirmar =
            document.getElementById('btn-confirmar-pedido');


        /*
        |--------------------------------------------------------------------------
        | ESTADO UBICACIÓN
        |--------------------------------------------------------------------------
        */

        function mostrarEstado(mensaje, error = false) {

            if (!estadoUbicacion) {
                return;
            }

            estadoUbicacion.innerHTML = `

                <i class="bi ${
                    error
                        ? 'bi-exclamation-circle-fill'
                        : 'bi-info-circle-fill'
                }"></i>

                <span>
                    ${mensaje}
                </span>

            `;

            estadoUbicacion.classList.toggle(
                'cliente-pedido-estado-error',
                error
            );

        }


        /*
        |--------------------------------------------------------------------------
        | NORMALIZAR COORDENADAS
        |--------------------------------------------------------------------------
        |
        | Google Maps utilizaba:
        |
        | posicion.lat()
        | posicion.lng()
        |
        | Leaflet utiliza:
        |
        | posicion.lat
        | posicion.lng
        |
        | Esta función permite que el resto de la lógica conserve
        | los mismos nombres y funcione correctamente con Leaflet.
        |
        */

        function normalizarPosicion(posicion) {

            if (!posicion) {
                return null;
            }

            let lat;
            let lng;


            if (typeof posicion.lat === 'function') {

                lat = Number(
                    posicion.lat()
                );

            } else {

                lat = Number(
                    posicion.lat
                );

            }


            if (typeof posicion.lng === 'function') {

                lng = Number(
                    posicion.lng()
                );

            } else {

                lng = Number(
                    posicion.lng
                );

            }


            if (
                !Number.isFinite(lat) ||
                !Number.isFinite(lng)
            ) {

                return null;

            }


            return {
                lat: lat,
                lng: lng
            };

        }


        /*
        |--------------------------------------------------------------------------
        | COORDENADAS
        |--------------------------------------------------------------------------
        */

        function guardarCoordenadas(posicion) {

            const coordenadas =
                normalizarPosicion(posicion);


            if (!coordenadas) {

                mostrarEstado(
                    'Las coordenadas no son válidas.',
                    true
                );

                return;

            }


            latitudInput.value =
                coordenadas.lat.toFixed(7);

            longitudInput.value =
                coordenadas.lng.toFixed(7);

        }


        /*
        |--------------------------------------------------------------------------
        | DIRECCIÓN
        |--------------------------------------------------------------------------
        */

        function solicitarDireccion(posicion) {

            if (!posicion) {
                return;
            }


            if (temporizadorDireccion) {

                clearTimeout(
                    temporizadorDireccion
                );

            }


            temporizadorDireccion =
                setTimeout(function() {

                    obtenerDireccionPedido(
                        posicion
                    );

                }, 300);

        }


        async function obtenerDireccionPedido(posicion) {

            const coordenadas =
                normalizarPosicion(posicion);


            if (!coordenadas) {

                mostrarEstado(
                    'Coordenadas no válidas.',
                    true
                );

                return;

            }


            const latitud =
                coordenadas.lat;

            const longitud =
                coordenadas.lng;


            const ahora =
                Date.now();


            const espera =
                Math.max(
                    0,
                    1100 - (
                        ahora -
                        ultimaConsultaDireccion
                    )
                );


            if (consultaDireccionEnCurso) {
                return;
            }


            if (espera > 0) {

                setTimeout(
                    function() {

                        obtenerDireccionPedido(
                            posicion
                        );

                    },
                    espera
                );

                return;

            }


            ultimaConsultaDireccion =
                Date.now();

            consultaDireccionEnCurso =
                true;


            mostrarEstado(
                'Obteniendo dirección automáticamente...'
            );


            try {

                const url =
                    new URL(
                        '{{ route("cliente.pedidos.direccion") }}',
                        window.location.origin
                    );


                url.searchParams.set(
                    'latitud',
                    latitud.toString()
                );

                url.searchParams.set(
                    'longitud',
                    longitud.toString()
                );


                const respuesta =
                    await fetch(
                        url.toString(), {
                            method: 'GET',

                            headers: {
                                'Accept': 'application/json',

                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );


                const datos =
                    await respuesta.json();


                if (
                    !respuesta.ok ||
                    !datos.ok ||
                    !datos.direccion
                ) {

                    throw new Error(
                        datos.message ||
                        'No se pudo obtener la dirección.'
                    );

                }


                direccionInput.value =
                    datos.direccion;


                mostrarEstado(
                    'Ubicación y dirección actualizadas correctamente.'
                );


            } catch (error) {

                console.error(
                    'Error al obtener dirección:',
                    error
                );


                mostrarEstado(
                    'No se pudo obtener la dirección automáticamente. Puedes escribirla manualmente.',
                    true
                );


            } finally {

                consultaDireccionEnCurso =
                    false;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | MARCADOR
        |--------------------------------------------------------------------------
        */

        function colocarMarcador(
            posicion,
            centrar = true,
            obtenerDireccion = true
        ) {

            if (!mapaPedido) {
                return;
            }


            const coordenadas =
                normalizarPosicion(posicion);


            if (!coordenadas) {

                mostrarEstado(
                    'La ubicación seleccionada no es válida.',
                    true
                );

                return;

            }


            const latLng = [
                coordenadas.lat,
                coordenadas.lng
            ];


            /*
            |--------------------------------------------------------------------------
            | Crear marcador Leaflet
            |--------------------------------------------------------------------------
            */

            if (!marcadorPedido) {

                marcadorPedido =
                    L.marker(
                        latLng, {
                            draggable: true,
                            title: 'Ubicación de entrega'
                        }
                    ).addTo(
                        mapaPedido
                    );


                marcadorPedido.bindPopup(
                    '<strong>Ubicación de entrega</strong><br>' +
                    'Puedes mover este marcador.'
                );


                /*
                |--------------------------------------------------------------------------
                | Mover marcador
                |--------------------------------------------------------------------------
                */

                marcadorPedido.on(
                    'dragend',
                    function(evento) {

                        const nuevaPosicion =
                            evento.target.getLatLng();


                        guardarCoordenadas(
                            nuevaPosicion
                        );


                        solicitarDireccion(
                            nuevaPosicion
                        );

                    }
                );


            } else {

                marcadorPedido.setLatLng(
                    latLng
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Centrar mapa
            |--------------------------------------------------------------------------
            */

            if (centrar) {

                mapaPedido.setView(
                    latLng,
                    Math.max(
                        mapaPedido.getZoom(),
                        16
                    )
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Guardar coordenadas
            |--------------------------------------------------------------------------
            */

            guardarCoordenadas(
                marcadorPedido.getLatLng()
            );


            /*
            |--------------------------------------------------------------------------
            | Obtener dirección
            |--------------------------------------------------------------------------
            */

            if (obtenerDireccion) {

                solicitarDireccion(
                    marcadorPedido.getLatLng()
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | INICIALIZAR MAPA
        |--------------------------------------------------------------------------
        */

        window.inicializarMapaPedido =
            function() {

                /*
                |--------------------------------------------------------------------------
                | Verificar Leaflet
                |--------------------------------------------------------------------------
                */

                if (
                    typeof L === 'undefined'
                ) {

                    window.mapaNoDisponible();

                    return;

                }


                const latGuardada =
                    parseFloat(
                        latitudInput.value
                    );

                const lngGuardada =
                    parseFloat(
                        longitudInput.value
                    );


                const tieneUbicacionGuardada =
                    Number.isFinite(latGuardada) &&
                    Number.isFinite(lngGuardada);


                /*
                |--------------------------------------------------------------------------
                | Centro inicial
                |--------------------------------------------------------------------------
                |
                | Cochabamba solamente se utiliza como centro visual.
                |
                | NO se guarda como ubicación del pedido.
                |
                */

                const centroInicial =
                    tieneUbicacionGuardada ?
                    [
                        latGuardada,
                        lngGuardada
                    ] :
                    [
                        cochabamba.lat,
                        cochabamba.lng
                    ];


                /*
                |--------------------------------------------------------------------------
                | Crear mapa Leaflet
                |--------------------------------------------------------------------------
                */

                mapaPedido =
                    L.map(
                        document.getElementById(
                            'mapa-pedido'
                        ), {
                            zoomControl: true
                        }
                    ).setView(
                        centroInicial,
                        16
                    );


                /*
                |--------------------------------------------------------------------------
                | OpenStreetMap
                |--------------------------------------------------------------------------
                */

                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {

                        maxZoom: 19,

                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">' +
                            'OpenStreetMap</a> contributors'

                    }
                ).addTo(
                    mapaPedido
                );


                /*
                |--------------------------------------------------------------------------
                | Icono del marcador
                |--------------------------------------------------------------------------
                */

                delete L.Icon.Default.prototype._getIconUrl;


                L.Icon.Default.mergeOptions({

                    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',

                    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',

                    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png'

                });


                /*
                |--------------------------------------------------------------------------
                | Si ya había coordenadas, las recuperamos
                |--------------------------------------------------------------------------
                */

                if (tieneUbicacionGuardada) {

                    colocarMarcador({
                            lat: latGuardada,
                            lng: lngGuardada
                        },
                        false,
                        false
                    );


                    mostrarEstado(
                        'Ubicación cargada. Puedes mover el marcador si deseas corregirla.'
                    );

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | Primera visita
                    |--------------------------------------------------------------------------
                    |
                    | NO colocamos un marcador automáticamente en Cochabamba.
                    | El usuario debe seleccionar su ubicación.
                    |
                    */

                    mostrarEstado(
                        'Selecciona tu ubicación en el mapa o utiliza "Usar mi ubicación".'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | HACER CLIC EN EL MAPA
                |--------------------------------------------------------------------------
                */

                mapaPedido.on(
                    'click',
                    function(evento) {

                        colocarMarcador(
                            evento.latlng,
                            true,
                            true
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | MI UBICACIÓN
                |--------------------------------------------------------------------------
                */

                if (btnMiUbicacion) {

                    btnMiUbicacion.addEventListener(
                        'click',
                        function() {

                            if (
                                !navigator.geolocation
                            ) {

                                mostrarEstado(
                                    'Tu navegador no permite obtener tu ubicación.',
                                    true
                                );

                                return;

                            }


                            mostrarEstado(
                                'Obteniendo tu ubicación...'
                            );


                            btnMiUbicacion.disabled =
                                true;


                            navigator.geolocation.getCurrentPosition(

                                function(posicion) {

                                    const ubicacion = {

                                        lat: posicion.coords.latitude,

                                        lng: posicion.coords.longitude

                                    };


                                    colocarMarcador(
                                        ubicacion,
                                        true,
                                        true
                                    );


                                    btnMiUbicacion.disabled =
                                        false;

                                },


                                function(error) {

                                    console.error(
                                        'Geolocation:',
                                        error
                                    );


                                    let mensaje =
                                        'No se pudo obtener tu ubicación. Revisa los permisos del navegador.';


                                    if (
                                        error.code ===
                                        error.PERMISSION_DENIED
                                    ) {

                                        mensaje =
                                            'Permiso de ubicación denegado. Puedes seleccionar manualmente tu ubicación en el mapa.';

                                    }


                                    if (
                                        error.code ===
                                        error.POSITION_UNAVAILABLE
                                    ) {

                                        mensaje =
                                            'La ubicación no está disponible. Puedes seleccionar manualmente tu ubicación en el mapa.';

                                    }


                                    if (
                                        error.code ===
                                        error.TIMEOUT
                                    ) {

                                        mensaje =
                                            'La solicitud de ubicación tardó demasiado. Intenta nuevamente.';

                                    }


                                    mostrarEstado(
                                        mensaje,
                                        true
                                    );


                                    btnMiUbicacion.disabled =
                                        false;

                                },


                                {

                                    enableHighAccuracy: true,

                                    timeout: 10000,

                                    maximumAge: 0

                                }

                            );

                        }
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | ACTUALIZAR DIRECCIÓN
                |--------------------------------------------------------------------------
                */

                if (btnDireccionMapa) {

                    btnDireccionMapa.addEventListener(
                        'click',
                        function() {

                            if (!marcadorPedido) {

                                mostrarEstado(
                                    'Primero selecciona una ubicación.',
                                    true
                                );

                                return;

                            }


                            obtenerDireccionPedido(
                                marcadorPedido.getLatLng()
                            );

                        }
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | CORREGIR TAMAÑO DEL MAPA
                |--------------------------------------------------------------------------
                */

                setTimeout(
                    function() {

                        if (mapaPedido) {

                            mapaPedido.invalidateSize();

                        }

                    },
                    300
                );

            };


        /*
        |--------------------------------------------------------------------------
        | MAPA NO DISPONIBLE
        |--------------------------------------------------------------------------
        */

        window.mapaNoDisponible =
            function() {

                const mapa =
                    document.getElementById(
                        'mapa-pedido'
                    );


                if (mapa) {

                    mapa.innerHTML = `

                        <div class="cliente-pedido-mapa-error">

                            <i class="bi bi-map"></i>

                            <strong>
                                Mapa no disponible
                            </strong>

                            <span>
                                Puedes escribir tu dirección manualmente.
                            </span>

                        </div>

                    `;

                }


                if (btnMiUbicacion) {

                    btnMiUbicacion.disabled =
                        true;

                }


                if (btnDireccionMapa) {

                    btnDireccionMapa.disabled =
                        true;

                }


                mostrarEstado(
                    'Mapa no disponible. Puedes escribir la dirección manualmente.',
                    true
                );

            };


        /*
        |--------------------------------------------------------------------------
        | INICIAR MAPA
        |--------------------------------------------------------------------------
        */

        inicializarMapaPedido();


        /*
        |--------------------------------------------------------------------------
        | COMPROBANTE
        |--------------------------------------------------------------------------
        */

        if (inputComprobante) {

            inputComprobante.addEventListener(
                'change',
                function() {

                    const archivo =
                        this.files[0];


                    if (!archivo) {
                        return;
                    }


                    const tiposPermitidos = [

                        'image/jpeg',

                        'image/png'

                    ];


                    if (
                        !tiposPermitidos.includes(
                            archivo.type
                        )
                    ) {

                        mostrarMensajeComprobante(
                            'Solo se permiten imágenes JPG, JPEG o PNG.',
                            'error'
                        );


                        this.value =
                            '';


                        return;

                    }


                    if (
                        archivo.size >
                        2 * 1024 * 1024
                    ) {

                        mostrarMensajeComprobante(
                            'El comprobante no puede superar los 2 MB.',
                            'error'
                        );


                        this.value =
                            '';


                        return;

                    }


                    const lector =
                        new FileReader();


                    lector.onload =
                        function(evento) {

                            imagenPreview.src =
                                evento.target.result;


                            preview.classList.add(
                                'activo'
                            );


                            areaComprobante.classList.add(
                                'seleccionado'
                            );

                        };


                    lector.readAsDataURL(
                        archivo
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | QUITAR COMPROBANTE
        |--------------------------------------------------------------------------
        */

        if (btnQuitarComprobante) {

            btnQuitarComprobante.addEventListener(
                'click',
                function() {

                    inputComprobante.value =
                        '';

                    imagenPreview.src =
                        '';

                    preview.classList.remove(
                        'activo'
                    );

                    areaComprobante.classList.remove(
                        'seleccionado'
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | MENSAJE COMPROBANTE
        |--------------------------------------------------------------------------
        */

        function mostrarMensajeComprobante(
            mensaje,
            tipo
        ) {

            const anterior =
                document.querySelector(
                    '.cliente-comprobante-mensaje'
                );


            if (anterior) {
                anterior.remove();
            }


            const alerta =
                document.createElement(
                    'div'
                );


            alerta.className =
                'cliente-comprobante-mensaje ' +
                (
                    tipo === 'error' ?
                    'error' :
                    'success'
                );


            alerta.innerHTML = `

                <i class="bi ${
                    tipo === 'error'
                        ? 'bi-exclamation-circle-fill'
                        : 'bi-check-circle-fill'
                }"></i>

                <span>
                    ${mensaje}
                </span>

            `;


            const cardBody =
                inputComprobante.closest(
                    '.cliente-pedido-card-body'
                );


            if (cardBody) {

                cardBody.appendChild(
                    alerta
                );

            }


            setTimeout(
                function() {

                    if (alerta) {

                        alerta.remove();

                    }

                },
                3500
            );

        }


        /*
        |--------------------------------------------------------------------------
        | CONFIRMAR PEDIDO
        |--------------------------------------------------------------------------
        */

        if (formulario) {

            formulario.addEventListener(
                'submit',
                function(evento) {

                    /*
                    |--------------------------------------------------------------------------
                    | Evita doble envío
                    |--------------------------------------------------------------------------
                    */

                    if (
                        botonConfirmar &&
                        botonConfirmar.disabled
                    ) {

                        evento.preventDefault();

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Comprobante
                    |--------------------------------------------------------------------------
                    */

                    if (
                        inputComprobante &&
                        inputComprobante.files.length === 0
                    ) {

                        evento.preventDefault();


                        mostrarMensajeComprobante(
                            'Debes seleccionar tu comprobante de pago.',
                            'error'
                        );


                        inputComprobante.focus();


                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Ubicación
                    |--------------------------------------------------------------------------
                    |
                    | NO la hacemos obligatoria porque el controller
                    | actual acepta nullable.
                    |
                    */

                    if (botonConfirmar) {

                        botonConfirmar.disabled =
                            true;


                        botonConfirmar.innerHTML = `

                            <span
                                class="spinner-border spinner-border-sm"
                                aria-hidden="true">
                            </span>

                            Enviando pedido...

                        `;

                    }

                }
            );

        }

    });
</script>

@endsection