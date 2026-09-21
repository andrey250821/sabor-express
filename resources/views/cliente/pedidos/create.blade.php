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
                                class="form-control cliente-pedido-input cliente-pedido-direccion-bloqueada"
                                rows="3"
                                placeholder="La dirección se obtendrá automáticamente al seleccionar tu ubicación."
                                readonly
                                aria-readonly="true"
                                tabindex="-1"
                                required>{{ old('direccion_entrega') }}</textarea>

                            <small>
                                Esta dirección se obtiene automáticamente a partir de la ubicación seleccionada en el mapa y no puede editarse manualmente.
                            </small>

                        </div>


                        {{-- REFERENCIA --}}

                        <div class="cliente-pedido-campo">

                            <label for="referencia_delivery">

                                <i class="bi bi-signpost-2-fill"></i>

                                Referencia para Delivery

                            </label>

                            <textarea
                                name="referencia_delivery"
                                id="referencia_delivery"
                                class="form-control cliente-pedido-input"
                                rows="3"
                                maxlength="1000"
                                placeholder="Ej.: puerta verde, frente a la farmacia, al lado de la plaza...">{{ old('referencia_delivery') }}</textarea>

                            <small>
                                Indica puntos de referencia que ayuden al Delivery a encontrar tu domicilio.
                            </small>

                        </div>


                        {{-- OBSERVACIONES --}}

                        <div class="cliente-pedido-campo">

                            <label for="observacion_cliente">

                                <i class="bi bi-chat-left-text-fill"></i>

                                Observaciones para cocina

                            </label>

                            <textarea
                                name="observacion_cliente"
                                id="observacion_cliente"
                                class="form-control cliente-pedido-input"
                                rows="3"
                                maxlength="500"
                                placeholder="Ej.: poca salsa, sin cebolla, tocar el timbre...">{{ old('observacion_cliente') }}</textarea>

                            <small>
                                Instrucciones para la preparación, por ejemplo: sin cebolla, poca salsa, etc.
                            </small>

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

                            <div
                                class="cliente-comprobante-preview"
                                id="comprobante-preview-contenedor"
                                hidden>

                                <img
                                    id="comprobante-preview"
                                    src=""
                                    alt="Vista previa del comprobante">

                            </div>

                            <div
                                class="cliente-comprobante-icono"
                                id="comprobante-icono">

                                <i class="bi bi-image"></i>

                            </div>

                            <strong id="comprobante-titulo">
                                Seleccionar comprobante
                            </strong>

                            <span id="comprobante-nombre">
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


                        {{-- HERRAMIENTA DE PRUEBA OCR --}}
                        <div class="cliente-ocr-herramienta">

                            <div class="cliente-ocr-herramienta-cabecera">
                                <i class="bi bi-cpu-fill"></i>

                                <div>
                                    <strong>Prueba OCR</strong>
                                    <span>
                                        Genera comprobantes de prueba con los datos de este pedido
                                        y usa cualquiera directamente en el comprobante seleccionado.
                                    </span>
                                </div>
                            </div>

                            <div class="cliente-ocr-pruebas-info">

                                <div>
                                    <span>Cliente</span>
                                    <strong>{{ auth()->user()->name }}</strong>
                                </div>

                                <div>
                                    <span>Próximo pedido</span>
                                    <strong>#{{ $proximoPedidoId }}</strong>
                                </div>

                                <div>
                                    <span>Total</span>
                                    <strong>Bs {{ number_format($total, 2) }}</strong>
                                </div>

                            </div>

                            <button
                                type="button"
                                id="btn-generar-comprobantes-ocr"
                                class="cliente-ocr-pruebas-btn">

                                <i class="bi bi-images"></i>

                                Generar imágenes de prueba OCR

                            </button>

                            <div
                                id="estado-generacion-ocr"
                                class="cliente-ocr-pruebas-estado"
                                aria-live="polite">
                            </div>

                            <div
                                id="resultados-comprobantes-ocr"
                                class="cliente-ocr-pruebas-resultados">
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


@push('styles')
<style>
    .cliente-comprobante-preview {
        width: min(100%, 360px);
        margin: 0 auto 14px;
        display: flex;
        justify-content: center;
    }

    .cliente-comprobante-preview[hidden] {
        display: none !important;
    }

    .cliente-comprobante-preview img {
        display: block;
        width: 100%;
        max-height: 420px;
        object-fit: contain;
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, .08);
        background: #fff;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
    }

    .cliente-comprobante-area.seleccionado .cliente-comprobante-icono {
        display: none;
    }

    .cliente-ocr-herramienta {
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid rgba(139, 30, 69, .16);
    }

    /* Dirección generada automáticamente desde la ubicación */
    .cliente-pedido-direccion-bloqueada {
        background: #17181d !important;
        border-color: #454750 !important;
        color: #e5e5e5 !important;
        cursor: not-allowed;
        resize: none;
    }

    .cliente-pedido-direccion-bloqueada:focus {
        border-color: #454750 !important;
        box-shadow: none !important;
    }

    .cliente-ocr-herramienta-cabecera {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px;
        color: #4d3d45;
    }

    .cliente-ocr-herramienta-cabecera > i {
        margin-top: 2px;
        font-size: 1.1rem;
        color: #8b1e45;
        flex: 0 0 auto;
    }

    .cliente-ocr-herramienta-cabecera strong {
        display: block;
        color: #2d1b22;
        font-size: .92rem;
    }

    .cliente-ocr-herramienta-cabecera span {
        display: block;
        margin-top: 3px;
        color: #75636b;
        font-size: .82rem;
        line-height: 1.45;
    }

    .cliente-ocr-pruebas-info {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 12px;
    }

    .cliente-ocr-pruebas-info > div {
        padding: 10px 12px;
        background: #fff;
        border: 1px solid rgba(0,0,0,.06);
        border-radius: 10px;
    }

    .cliente-ocr-pruebas-info span {
        display: block;
        margin-bottom: 3px;
        color: #86747c;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .cliente-ocr-pruebas-info strong {
        color: #2d1b22;
        word-break: break-word;
    }

    .cliente-ocr-pruebas-btn {
        width: 100%;
        border: 0;
        border-radius: 10px;
        padding: 12px 16px;
        background: #8b1e45;
        color: #fff;
        font-weight: 700;
    }

    .cliente-ocr-pruebas-btn:disabled {
        opacity: .65;
        cursor: wait;
    }

    .cliente-ocr-pruebas-estado {
        min-height: 22px;
        margin-top: 10px;
        color: #75636b;
        font-size: .86rem;
    }

    .cliente-ocr-pruebas-resultados {
        display: grid;
        gap: 8px;
        margin-top: 10px;
    }

    .cliente-ocr-prueba-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 12px;
        background: #fff;
        border: 1px solid rgba(0,0,0,.07);
        border-radius: 10px;
    }

    .cliente-ocr-prueba-nombre {
        min-width: 0;
        color: #4d3d45;
        font-size: .78rem;
        word-break: break-word;
    }

    .cliente-ocr-prueba-acciones {
        display: flex;
        gap: 6px;
        flex: 0 0 auto;
    }

    .cliente-ocr-prueba-acciones a,
    .cliente-ocr-prueba-acciones button {
        border: 0;
        border-radius: 8px;
        padding: 7px 9px;
        font-size: .75rem;
        text-decoration: none;
        cursor: pointer;
    }

    .cliente-ocr-ver {
        background: #f0ecef;
        color: #45363d;
    }

    .cliente-ocr-usar {
        background: #198754;
        color: #fff;
    }

    @media (max-width: 768px) {
        .cliente-ocr-pruebas-info {
            grid-template-columns: 1fr;
        }

        .cliente-ocr-prueba-item {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

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

        const areaComprobante =
            document.getElementById('area-comprobante');

        const comprobanteTitulo =
            document.getElementById('comprobante-titulo');

        const comprobanteNombre =
            document.getElementById('comprobante-nombre');

        const comprobantePreviewContenedor =
            document.getElementById('comprobante-preview-contenedor');

        const comprobantePreview =
            document.getElementById('comprobante-preview');

        let urlVistaPreviaComprobante = null;

        const formulario =
            document.getElementById('form-pedido');

        const botonConfirmar =
            document.getElementById('btn-confirmar-pedido');


        /*
        |--------------------------------------------------------------------------
        | PRUEBAS DEL OCR
        |--------------------------------------------------------------------------
        */

        const botonGenerarOcr =
            document.getElementById('btn-generar-comprobantes-ocr');

        const estadoGeneracionOcr =
            document.getElementById('estado-generacion-ocr');

        const resultadosOcr =
            document.getElementById('resultados-comprobantes-ocr');

        const rutaGenerarComprobantesOcr =
            @json(route('cliente.pedidos.generar.comprobantes.prueba'));

        const tokenCsrf =
            @json(csrf_token());


        function escaparHtmlOcr(valor) {

            const elemento =
                document.createElement('div');

            elemento.textContent =
                valor ?? '';

            return elemento.innerHTML;

        }


        function mostrarEstadoOcr(mensaje, error = false) {

            if (!estadoGeneracionOcr) {
                return;
            }

            estadoGeneracionOcr.innerHTML =
                mensaje;

            estadoGeneracionOcr.style.color =
                error ? '#b42318' : '#75636b';

        }


        async function usarImagenOcr(imagen) {

            if (!inputComprobante || !imagen?.url) {
                return;
            }

            try {

                mostrarEstadoOcr(
                    'Cargando el comprobante en el campo principal...'
                );

                const respuesta =
                    await fetch(imagen.url);

                if (!respuesta.ok) {
                    throw new Error(
                        'No se pudo abrir la imagen generada.'
                    );
                }

                const blob =
                    await respuesta.blob();

                const archivo =
                    new File(
                        [blob],
                        imagen.nombre,
                        {
                            type: blob.type || 'image/png'
                        }
                    );

                const transferencia =
                    new DataTransfer();

                transferencia.items.add(archivo);

                inputComprobante.files =
                    transferencia.files;

                inputComprobante.dispatchEvent(
                    new Event(
                        'change',
                        { bubbles: true }
                    )
                );

                mostrarEstadoOcr(
                    '✅ Se cargó <strong>'
                    + escaparHtmlOcr(imagen.nombre)
                    + '</strong> en el comprobante principal.'
                );

                areaComprobante?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

            } catch (error) {

                console.error(
                    'Error al usar imagen OCR:',
                    error
                );

                mostrarEstadoOcr(
                    '❌ No se pudo cargar automáticamente esa imagen. Puedes abrirla y seleccionarla manualmente.',
                    true
                );

            }

        }


        function limpiarVistaPreviaComprobante() {

            if (urlVistaPreviaComprobante) {
                URL.revokeObjectURL(urlVistaPreviaComprobante);
                urlVistaPreviaComprobante = null;
            }

            if (comprobantePreview) {
                comprobantePreview.removeAttribute('src');
            }

            if (comprobantePreviewContenedor) {
                comprobantePreviewContenedor.hidden = true;
            }

        }


        function mostrarVistaPreviaComprobante(archivo) {

            if (!archivo || !archivo.type.startsWith('image/')) {
                limpiarVistaPreviaComprobante();
                return;
            }

            limpiarVistaPreviaComprobante();

            urlVistaPreviaComprobante =
                URL.createObjectURL(archivo);

            if (comprobantePreview) {
                comprobantePreview.onload = function() {

                    if (comprobantePreviewContenedor) {
                        comprobantePreviewContenedor.hidden = false;
                    }

                };

                comprobantePreview.onerror = function() {

                    limpiarVistaPreviaComprobante();

                    if (comprobanteNombre) {
                        comprobanteNombre.textContent =
                            'No se pudo mostrar la vista previa de esta imagen.';
                    }

                };

                comprobantePreview.src =
                    urlVistaPreviaComprobante;
            }

            if (comprobantePreviewContenedor) {
                comprobantePreviewContenedor.hidden = false;
            }

            if (areaComprobante) {
                areaComprobante.classList.add('seleccionado');
            }

            if (comprobanteTitulo) {
                comprobanteTitulo.textContent =
                    'Comprobante seleccionado';
            }

            if (comprobanteNombre) {
                comprobanteNombre.textContent =
                    archivo.name || 'Imagen seleccionada';
            }

        }


        if (inputComprobante) {

            inputComprobante.addEventListener(
                'change',
                function() {

                    const archivo =
                        this.files && this.files.length > 0
                            ? this.files[0]
                            : null;

                    if (!archivo) {

                        limpiarVistaPreviaComprobante();

                        if (areaComprobante) {
                            areaComprobante.classList.remove('seleccionado');
                        }

                        if (comprobanteTitulo) {
                            comprobanteTitulo.textContent =
                                'Seleccionar comprobante';
                        }

                        if (comprobanteNombre) {
                            comprobanteNombre.textContent =
                                'Haz clic aquí para seleccionar una imagen';
                        }

                        return;

                    }

                    mostrarVistaPreviaComprobante(archivo);

                }
            );

        }


        function renderizarResultadosOcr(imagenes) {

            if (!resultadosOcr) {
                return;
            }

            resultadosOcr.innerHTML =
                imagenes.map(function(imagen) {

                    return `
                        <div class="cliente-ocr-prueba-item">

                            <div class="cliente-ocr-prueba-nombre">
                                ${escaparHtmlOcr(imagen.nombre)}
                            </div>

                            <div class="cliente-ocr-prueba-acciones">

                                <a
                                    href="${imagen.url}"
                                    target="_blank"
                                    rel="noopener"
                                    class="cliente-ocr-ver">
                                    <i class="bi bi-eye"></i>
                                    Ver
                                </a>

                                <button
                                    type="button"
                                    class="cliente-ocr-usar">
                                    <i class="bi bi-upload"></i>
                                    Usar
                                </button>

                            </div>

                        </div>
                    `;

                }).join('');


            resultadosOcr
                .querySelectorAll('.cliente-ocr-usar')
                .forEach(function(boton, indice) {

                    boton.addEventListener(
                        'click',
                        function() {

                            usarImagenOcr(
                                imagenes[indice]
                            );

                        }
                    );

                });

        }


        if (botonGenerarOcr) {

            botonGenerarOcr.addEventListener(
                'click',
                async function() {

                    botonGenerarOcr.disabled = true;

                    resultadosOcr.innerHTML = '';

                    mostrarEstadoOcr(
                        '<i class="bi bi-hourglass-split"></i> Generando las 4 imágenes de prueba...'
                    );

                    try {

                        const respuesta =
                            await fetch(
                                rutaGenerarComprobantesOcr,
                                {
                                    method: 'POST',

                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': tokenCsrf
                                    }
                                }
                            );

                        const datos =
                            await respuesta.json();

                        if (
                            !respuesta.ok ||
                            !datos.ok
                        ) {
                            throw new Error(
                                datos.message
                                || 'No se pudieron generar las imágenes.'
                            );
                        }

                        renderizarResultadosOcr(
                            datos.imagenes
                        );

                        mostrarEstadoOcr(
                            '✅ Generados los 4 comprobantes para <strong>pedido #'
                            + datos.pedido
                            + '</strong>. Puedes pulsar <strong>Usar</strong> para cargar cualquiera de ellos en el comprobante principal.'
                        );

                    } catch (error) {

                        console.error(
                            'Error al generar comprobantes OCR:',
                            error
                        );

                        mostrarEstadoOcr(
                            '❌ '
                            + escaparHtmlOcr(error.message),
                            true
                        );

                    } finally {

                        botonGenerarOcr.disabled = false;

                    }

                }
            );

        }


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
                <i class="bi ${error
                    ? 'bi-exclamation-circle-fill'
                    : 'bi-info-circle-fill'}"></i>
                <span>${mensaje}</span>
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
        | Leaflet utiliza objetos con .lat y .lng.
        | Esta función también admite el formato lat()/lng()
        | por compatibilidad con cualquier código anterior.
        |--------------------------------------------------------------------------
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


                            const procesarUbicacion = function(posicion) {

                                const ubicacion = {
                                    lat: posicion.coords.latitude,
                                    lng: posicion.coords.longitude
                                };

                                colocarMarcador(
                                    ubicacion,
                                    true,
                                    true
                                );

                                mostrarEstado(
                                    '✅ Ubicación encontrada. La dirección se está obteniendo automáticamente.'
                                );

                                btnMiUbicacion.disabled = false;
                            };

                            const manejarErrorUbicacion = function(error) {

                                console.error(
                                    'Geolocation:',
                                    error
                                );

                                if (error.code === error.PERMISSION_DENIED) {
                                    mostrarEstado(
                                        'Permiso de ubicación denegado. Activa la ubicación para este sitio en Chrome y vuelve a intentarlo.',
                                        true
                                    );

                                    btnMiUbicacion.disabled = false;
                                    return;
                                }

                                if (error.code === error.TIMEOUT) {
                                    mostrarEstado(
                                        'La ubicación está tardando demasiado. Intentando con una ubicación aproximada...',
                                        false
                                    );

                                    navigator.geolocation.getCurrentPosition(
                                        procesarUbicacion,
                                        function(errorSegundoIntento) {
                                            console.error(
                                                'Segundo intento de geolocalización:',
                                                errorSegundoIntento
                                            );

                                            mostrarEstado(
                                                'No se pudo obtener tu ubicación. También puedes hacer clic directamente sobre el mapa.',
                                                true
                                            );

                                            btnMiUbicacion.disabled = false;
                                        },
                                        {
                                            enableHighAccuracy: false,
                                            timeout: 20000,
                                            maximumAge: 120000
                                        }
                                    );

                                    return;
                                }

                                mostrarEstado(
                                    'La ubicación no está disponible. Puedes hacer clic directamente sobre el mapa.',
                                    true
                                );

                                btnMiUbicacion.disabled = false;
                            };

                            navigator.geolocation.getCurrentPosition(
                                procesarUbicacion,
                                manejarErrorUbicacion,
                                {
                                    enableHighAccuracy: false,
                                    timeout: 12000,
                                    maximumAge: 120000
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
                        limpiarVistaPreviaComprobante();
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

                        limpiarVistaPreviaComprobante();

                        if (comprobanteTitulo) {
                            comprobanteTitulo.textContent =
                                'Seleccionar comprobante';
                        }

                        if (comprobanteNombre) {
                            comprobanteNombre.textContent =
                                'Haz clic aquí para seleccionar una imagen';
                        }

                        areaComprobante?.classList.remove(
                            'seleccionado'
                        );


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

                        limpiarVistaPreviaComprobante();

                        if (comprobanteTitulo) {
                            comprobanteTitulo.textContent =
                                'Seleccionar comprobante';
                        }

                        if (comprobanteNombre) {
                            comprobanteNombre.textContent =
                                'Haz clic aquí para seleccionar una imagen';
                        }

                        areaComprobante?.classList.remove(
                            'seleccionado'
                        );


                        return;

                    }


                    mostrarVistaPreviaComprobante(archivo);

                    areaComprobante?.classList.add(
                        'seleccionado'
                    );

                    if (comprobanteTitulo) {
                        comprobanteTitulo.textContent =
                            'Comprobante seleccionado';
                    }

                    if (comprobanteNombre) {
                        comprobanteNombre.textContent =
                            archivo.name;
                    }

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
                    | La ubicación es obligatoria para completar
                    | correctamente la dirección de entrega.
                    |
                    */

                    if (
                        !latitudInput?.value ||
                        !longitudInput?.value ||
                        !direccionInput?.value.trim()
                    ) {

                        evento.preventDefault();

                        mostrarEstado(
                            'Selecciona tu ubicación en el mapa para obtener automáticamente la dirección.',
                            true
                        );

                        document.getElementById('mapa-pedido')?.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        return;
                    }


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