@extends('layouts.admin')

@section('content')

<div class="container-fluid px-0 configuracion-page">

    {{-- ENCABEZADO --}}
    <div class="configuracion-header">
        <div class="configuracion-title-wrapper">
            <div class="configuracion-icon">
                <i class="bi bi-gear-fill"></i>
            </div>

            <div>
                <h2 class="configuracion-title">
                    Configuración
                </h2>

                <p class="configuracion-subtitle">
                    Administra la información del restaurante, la ubicación de origen
                    y los valores económicos de Delivery.
                </p>
            </div>
        </div>
    </div>

    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="alert configuracion-alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert configuracion-alert-error alert-dismissible fade show">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ session('error') }}</span>

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert configuracion-alert-error">
            <i class="bi bi-exclamation-circle-fill"></i>

            <div>
                <strong>Revisa los datos ingresados.</strong>

                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif


    {{-- =========================================================
         INFORMACIÓN GENERAL
    ========================================================== --}}
    <form action="{{ route('admin.configuracion.update') }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="configuracion-card mb-4">

            <div class="configuracion-card-header">
                <div class="configuracion-section-icon">
                    <i class="bi bi-shop"></i>
                </div>

                <div>
                    <h5>Información del restaurante</h5>

                    <p>
                        Datos generales de Sabor Express.
                    </p>
                </div>
            </div>

            <div class="configuracion-card-body">

                <div class="row g-4">

                    <div class="col-12 col-lg-6">
                        <label class="configuracion-label">
                            <i class="bi bi-shop"></i>
                            Nombre del restaurante
                        </label>

                        <input
                            type="text"
                            class="form-control configuracion-input"
                            name="nombre_restaurante"
                            value="{{ old('nombre_restaurante', $configuracion->nombre_restaurante ?? 'Sabor Express') }}"
                            placeholder="Nombre del restaurante"
                            required>
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="configuracion-label">
                            <i class="bi bi-telephone-fill"></i>
                            Teléfono
                        </label>

                        <input
                            type="text"
                            class="form-control configuracion-input"
                            name="telefono"
                            value="{{ old('telefono', $configuracion->telefono ?? '') }}"
                            placeholder="Ej. 72222222">
                    </div>

                </div>
            </div>
        </div>


        {{-- =========================================================
             ARCHIVOS
        ========================================================== --}}
        <div class="row g-4 mb-4">

            {{-- LOGO --}}
            <div class="col-12 col-xl-6">

                <div class="configuracion-card configuracion-file-card h-100">

                    <div class="configuracion-card-header">

                        <div class="configuracion-section-icon">
                            <i class="bi bi-image"></i>
                        </div>

                        <div>
                            <h5>Logo del restaurante</h5>

                            <p>
                                Imagen utilizada como identidad visual.
                            </p>
                        </div>

                    </div>

                    <div class="configuracion-card-body">

                        <label class="configuracion-label">
                            <i class="bi bi-upload"></i>
                            Seleccionar logo
                        </label>

                        <input
                            type="file"
                            class="form-control configuracion-input"
                            name="logo"
                            accept="image/*"
                            onchange="previewImage(event, 'logoPreview')">

                        <small class="configuracion-help">
                            Formatos permitidos: JPG, PNG o WEBP. Máximo 2 MB.
                        </small>

                        <div class="configuracion-preview-container">

                            <span class="configuracion-preview-title">
                                Vista previa
                            </span>

                            <div class="configuracion-logo-preview">

                                <img
                                    id="logoPreview"
                                    @if(!empty($configuracion?->logo))
                                        src="{{ asset('storage/'.$configuracion->logo) }}"
                                    @endif
                                    alt="Vista previa del logo">

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- QR --}}
            <div class="col-12 col-xl-6">

                <div class="configuracion-card configuracion-file-card h-100">

                    <div class="configuracion-card-header">

                        <div class="configuracion-section-icon">
                            <i class="bi bi-qr-code"></i>
                        </div>

                        <div>
                            <h5>QR de pago</h5>

                            <p>
                                Código QR utilizado para recibir pagos.
                            </p>
                        </div>

                    </div>

                    <div class="configuracion-card-body">

                        <label class="configuracion-label">
                            <i class="bi bi-upload"></i>
                            Seleccionar QR
                        </label>

                        <input
                            type="file"
                            class="form-control configuracion-input"
                            name="qr_pago"
                            accept="image/*"
                            onchange="previewImage(event, 'qrPreview')">

                        <small class="configuracion-help">
                            Utiliza una imagen clara y de buena resolución. Máximo 2 MB.
                        </small>

                        <div class="configuracion-preview-container">

                            <span class="configuracion-preview-title">
                                Vista previa
                            </span>

                            <div class="configuracion-qr-preview">

                                <img
                                    id="qrPreview"
                                    @if(!empty($configuracion?->qr_pago))
                                        src="{{ asset('storage/'.$configuracion->qr_pago) }}"
                                    @endif
                                    alt="Vista previa del QR">

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="configuracion-actions mb-5">

            <button type="submit"
                class="btn configuracion-btn-save">

                <i class="bi bi-check-circle-fill"></i>

                Guardar información

            </button>

        </div>

    </form>


    {{-- =========================================================
         UBICACIÓN DEL RESTAURANTE
    ========================================================== --}}
    <div class="configuracion-card configuracion-location-card mb-5">

        <div class="configuracion-card-header">

            <div class="configuracion-section-icon configuracion-location-icon">
                <i class="bi bi-geo-alt-fill"></i>
            </div>

            <div>
                <h5>Dirección del restaurante</h5>

                <p>
                    Esta ubicación será el <strong>origen fijo</strong> utilizado
                    para calcular la distancia por carretera hacia los clientes.
                </p>
            </div>

        </div>

        <form
            action="{{ route('admin.configuracion.ubicacion.update') }}"
            method="POST">

            @csrf
            @method('PUT')

            <div class="configuracion-card-body">

                <div class="row g-4">

                    <div class="col-12">

                        <label
                            for="direccion-restaurante"
                            class="configuracion-label">

                            <i class="bi bi-signpost-2-fill"></i>

                            Dirección del restaurante

                        </label>

                        <textarea
                            id="direccion-restaurante"
                            name="direccion"
                            class="form-control configuracion-input configuracion-textarea"
                            rows="3"
                            maxlength="1000"
                            required
                            placeholder="Ej. Av. principal, esquina...">{{ old('direccion', $configuracion->direccion ?? '') }}</textarea>

                        <small class="configuracion-help">
                            Al mover el marcador, la dirección se actualizará automáticamente.
                        </small>

                        <small
                            id="estado-direccion-restaurante"
                            class="configuracion-help d-block mt-1"
                            aria-live="polite">
                        </small>

                    </div>


                    <div class="col-12">

                        <div class="configuracion-map-header">

                            <div>
                                <strong>
                                    <i class="bi bi-map me-1"></i>
                                    Ubicación en el mapa
                                </strong>

                                <span>
                                    Haz clic en el mapa o mueve el marcador para definir el punto exacto.
                                </span>
                            </div>

                            <button
                                type="button"
                                id="btn-ubicacion-actual-restaurante"
                                class="btn btn-outline-primary btn-sm">

                                <i class="bi bi-crosshair me-1"></i>
                                Usar mi ubicación

                            </button>

                        </div>

                        <div
                            id="mapa-restaurante"
                            class="configuracion-restaurante-mapa">

                            <div class="configuracion-mapa-cargando">
                                <i class="bi bi-map"></i>
                                Cargando mapa...
                            </div>

                        </div>

                    </div>


                    <div class="col-12 col-lg-6">

                        <label
                            for="latitud-restaurante"
                            class="configuracion-label">

                            <i class="bi bi-compass"></i>
                            Latitud

                        </label>

                        <input
                            id="latitud-restaurante"
                            type="text"
                            name="latitud_restaurante"
                            class="form-control configuracion-input"
                            value="{{ old('latitud_restaurante', $configuracion->latitud_restaurante ?? '') }}"
                            readonly
                            required>

                    </div>


                    <div class="col-12 col-lg-6">

                        <label
                            for="longitud-restaurante"
                            class="configuracion-label">

                            <i class="bi bi-compass"></i>
                            Longitud

                        </label>

                        <input
                            id="longitud-restaurante"
                            type="text"
                            name="longitud_restaurante"
                            class="form-control configuracion-input"
                            value="{{ old('longitud_restaurante', $configuracion->longitud_restaurante ?? '') }}"
                            readonly
                            required>

                    </div>


                    <div class="col-12">

                        @if($configuracion?->latitud_restaurante !== null && $configuracion?->longitud_restaurante !== null)

                            <div class="configuracion-location-status configuracion-location-status-ok">
                                <i class="bi bi-check-circle-fill"></i>

                                <div>
                                    <strong>Origen configurado</strong>

                                    <span>
                                        Los pedidos nuevos utilizarán esta ubicación
                                        como punto de partida del recorrido.
                                    </span>
                                </div>
                            </div>

                        @else

                            <div class="configuracion-location-status configuracion-location-status-warning">
                                <i class="bi bi-exclamation-triangle-fill"></i>

                                <div>
                                    <strong>La ubicación todavía no está configurada</strong>

                                    <span>
                                        Guarda una dirección y un punto del mapa antes de aceptar pedidos nuevos.
                                    </span>
                                </div>
                            </div>

                        @endif

                    </div>

                </div>

            </div>


            <div class="configuracion-actions configuracion-location-actions">

                <button
                    type="submit"
                    class="btn configuracion-btn-save">

                    <i class="bi bi-geo-alt-fill"></i>

                    Guardar ubicación

                </button>

            </div>

        </form>

    </div>


    {{-- =========================================================
         CONFIGURACIÓN ECONÓMICA DE DELIVERY
    ========================================================== --}}
    <div class="configuracion-card configuracion-delivery-settings-card mb-5">

        <div class="configuracion-card-header">

            <div class="configuracion-section-icon configuracion-delivery-icon">
                <i class="bi bi-bicycle"></i>
            </div>

            <div>
                <h5>Configuración de Delivery</h5>

                <p>
                    Estos valores se utilizarán para calcular los pedidos nuevos.
                    El pedido guardará una copia de los valores utilizados.
                </p>
            </div>

        </div>

        <form
            action="{{ route('admin.configuracion.delivery.update') }}"
            method="POST">

            @csrf
            @method('PUT')

            <div class="configuracion-card-body">

                <div class="row g-4">

                    <div class="col-12 col-md-6">

                        <label
                            for="tarifa-minima-delivery"
                            class="configuracion-label">

                            <i class="bi bi-cash-coin"></i>
                            Tarifa mínima de entrega

                        </label>

                        <div class="configuracion-money-input">
                            <span>Bs</span>

                            <input
                                id="tarifa-minima-delivery"
                                type="number"
                                name="tarifa_minima_delivery"
                                class="form-control configuracion-input"
                                min="0"
                                max="999999.99"
                                step="0.01"
                                value="{{ old('tarifa_minima_delivery', $configuracion->tarifa_minima_delivery ?? 5.00) }}"
                                required>
                        </div>

                        <small class="configuracion-help">
                            Monto mínimo que se cobrará por una entrega.
                        </small>

                    </div>


                    <div class="col-12 col-md-6">

                        <label
                            for="precio-km-delivery"
                            class="configuracion-label">

                            <i class="bi bi-signpost-split-fill"></i>
                            Precio por kilómetro

                        </label>

                        <div class="configuracion-money-input">
                            <span>Bs</span>

                            <input
                                id="precio-km-delivery"
                                type="number"
                                name="precio_km_delivery"
                                class="form-control configuracion-input"
                                min="0"
                                max="999999.99"
                                step="0.01"
                                value="{{ old('precio_km_delivery', $configuracion->precio_km_delivery ?? 3.00) }}"
                                required>
                        </div>

                        <small class="configuracion-help">
                            Se multiplica por la distancia real de carretera.
                        </small>

                    </div>


                    <div class="col-12 col-md-6">

                        <label
                            for="porcentaje-delivery"
                            class="configuracion-label">

                            <i class="bi bi-person-badge-fill"></i>
                            Porcentaje para Delivery

                        </label>

                        <div class="configuracion-percent-input">
                            <input
                                id="porcentaje-delivery"
                                type="number"
                                name="porcentaje_delivery"
                                class="form-control configuracion-input"
                                min="0"
                                max="100"
                                step="0.01"
                                value="{{ old('porcentaje_delivery', $configuracion->porcentaje_delivery ?? 80.00) }}"
                                required>

                            <span>%</span>
                        </div>

                    </div>


                    <div class="col-12 col-md-6">

                        <label
                            for="porcentaje-restaurante-delivery"
                            class="configuracion-label">

                            <i class="bi bi-shop-window"></i>
                            Porcentaje para restaurante

                        </label>

                        <div class="configuracion-percent-input">
                            <input
                                id="porcentaje-restaurante-delivery"
                                type="number"
                                name="porcentaje_restaurante_delivery"
                                class="form-control configuracion-input"
                                min="0"
                                max="100"
                                step="0.01"
                                value="{{ old('porcentaje_restaurante_delivery', $configuracion->porcentaje_restaurante_delivery ?? 20.00) }}"
                                required>

                            <span>%</span>
                        </div>

                    </div>


                    <div class="col-12">

                        <div class="configuracion-delivery-formula">

                            <i class="bi bi-calculator-fill"></i>

                            <div>

                                <strong>Fórmula utilizada</strong>

                                <span>
                                    Tarifa = máximo(tarifa mínima, distancia en km × precio por km)
                                </span>

                                <small>
                                    Los porcentajes deben sumar exactamente 100%.
                                </small>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <div class="configuracion-actions">

                <button
                    type="submit"
                    class="btn configuracion-btn-save">

                    <i class="bi bi-check-circle-fill"></i>

                    Guardar configuración de Delivery

                </button>

            </div>

        </form>

    </div>

</div>


{{-- PREVISUALIZACIÓN DE IMÁGENES --}}
<script>
    function previewImage(event, id) {
        const imagen = event.target.files[0];

        if (!imagen) {
            return;
        }

        const reader = new FileReader();

        reader.onload = function(e) {
            const preview = document.getElementById(id);

            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };

        reader.readAsDataURL(imagen);
    }
</script>


{{-- MAPA DEL RESTAURANTE --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const mapaContenedor = document.getElementById('mapa-restaurante');
        const latitudInput = document.getElementById('latitud-restaurante');
        const longitudInput = document.getElementById('longitud-restaurante');
        const direccionInput = document.getElementById('direccion-restaurante');
        const estadoDireccion = document.getElementById('estado-direccion-restaurante');
        const btnUbicacion = document.getElementById('btn-ubicacion-actual-restaurante');

        if (!mapaContenedor || typeof L === 'undefined') {
            return;
        }

        const latGuardada = Number(@json($configuracion?->latitud_restaurante));
        const lngGuardada = Number(@json($configuracion?->longitud_restaurante));

        const tieneUbicacion =
            Number.isFinite(latGuardada) &&
            Number.isFinite(lngGuardada);

        const centroInicial = tieneUbicacion
            ? [latGuardada, lngGuardada]
            : [-17.3935, -66.1570];

        mapaContenedor.innerHTML = '';

        const mapa = L.map('mapa-restaurante').setView(
            centroInicial,
            tieneUbicacion ? 17 : 13
        );

        L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: 19,
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
            }
        ).addTo(mapa);

        let marcador = null;
        let temporizadorDireccion = null;
        let solicitudDireccion = null;

        function actualizarCoordenadas(lat, lng) {

            if (latitudInput) {
                latitudInput.value = Number(lat).toFixed(7);
            }

            if (longitudInput) {
                longitudInput.value = Number(lng).toFixed(7);
            }
        }

        async function actualizarDireccion(lat, lng) {

            if (!direccionInput || !estadoDireccion) {
                return;
            }

            if (temporizadorDireccion) {
                clearTimeout(temporizadorDireccion);
            }

            if (solicitudDireccion) {
                solicitudDireccion.abort();
            }

            temporizadorDireccion = setTimeout(async function () {

                solicitudDireccion = new AbortController();

                estadoDireccion.textContent =
                    'Consultando dirección de la ubicación seleccionada...';

                estadoDireccion.classList.remove('text-danger', 'text-success');
                estadoDireccion.classList.add('text-muted');

                try {

                    const url =
                        '{{ route('admin.configuracion.ubicacion.direccion') }}' +
                        '?latitud=' + encodeURIComponent(Number(lat).toFixed(7)) +
                        '&longitud=' + encodeURIComponent(Number(lng).toFixed(7));

                    const respuesta = await fetch(
                        url,
                        {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            signal: solicitudDireccion.signal
                        }
                    );

                    const datos = await respuesta.json();

                    if (!respuesta.ok || !datos.ok) {
                        throw new Error(
                            datos.message ||
                            'No se pudo obtener la dirección.'
                        );
                    }

                    direccionInput.value = datos.direccion;

                    estadoDireccion.textContent =
                        'Dirección obtenida automáticamente desde el mapa.';

                    estadoDireccion.classList.remove('text-muted', 'text-danger');
                    estadoDireccion.classList.add('text-success');

                } catch (error) {

                    if (error.name === 'AbortError') {
                        return;
                    }

                    estadoDireccion.textContent =
                        'No se pudo obtener la dirección automáticamente. Puedes escribirla manualmente.';

                    estadoDireccion.classList.remove('text-muted', 'text-success');
                    estadoDireccion.classList.add('text-danger');

                }

            }, 350);
        }

        function colocarMarcador(lat, lng, centrar = true, obtenerDireccion = true) {

            actualizarCoordenadas(lat, lng);

            if (!marcador) {

                marcador = L.marker(
                    [lat, lng],
                    {
                        draggable: true,
                        title: 'Restaurante Sabor Express'
                    }
                ).addTo(mapa);

                marcador.bindPopup(
                    '<strong>Restaurante Sabor Express</strong><br>Arrastra este marcador para ajustar la ubicación.'
                );

                marcador.on('dragend', function (evento) {

                    const posicion =
                        evento.target.getLatLng();

                    actualizarCoordenadas(
                        posicion.lat,
                        posicion.lng
                    );

                    actualizarDireccion(
                        posicion.lat,
                        posicion.lng
                    );

                });

            } else {

                marcador.setLatLng([lat, lng]);

            }

            if (centrar) {
                mapa.setView([lat, lng], Math.max(mapa.getZoom(), 17));
            }

            if (obtenerDireccion) {
                actualizarDireccion(lat, lng);
            }
        }

        if (tieneUbicacion) {
            colocarMarcador(
                latGuardada,
                lngGuardada,
                false,
                false
            );
        }

        mapa.on('click', function (evento) {

            colocarMarcador(
                evento.latlng.lat,
                evento.latlng.lng,
                false,
                true
            );

        });

        if (btnUbicacion && navigator.geolocation) {

            btnUbicacion.addEventListener('click', function () {

                btnUbicacion.disabled = true;

                navigator.geolocation.getCurrentPosition(
                    function (posicion) {

                        colocarMarcador(
                            posicion.coords.latitude,
                            posicion.coords.longitude,
                            true,
                            true
                        );

                        btnUbicacion.disabled = false;

                    },
                    function () {

                        btnUbicacion.disabled = false;

                        alert(
                            'No se pudo obtener tu ubicación actual. También puedes seleccionar el punto directamente en el mapa.'
                        );

                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 12000,
                        maximumAge: 60000
                    }
                );

            });

        }

        setTimeout(function () {
            mapa.invalidateSize();
        }, 250);

    });
</script>

@endsection
