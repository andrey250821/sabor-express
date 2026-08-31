@extends('layouts.cliente')

@section('title', 'Realizar Pedido')

@section('content')

<div class="cliente-pedido-page">
    <div class="cliente-pedido-header">
        <h1><i class="bi bi-bag-check"></i> Realizar Pedido</h1>
        <p>Completa los datos para confirmar tu pedido.</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('cliente.pedidos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="cliente-pedido-section">
            <h2><i class="bi bi-geo-alt"></i> Datos de entrega</h2>

            <div class="mb-3">
                <label for="direccion_entrega" class="form-label">Dirección de entrega</label>
                <textarea name="direccion_entrega" id="direccion_entrega" class="form-control" rows="2" placeholder="La dirección se obtendrá automáticamente desde el mapa. También puedes corregirla manualmente." required>{{ old('direccion_entrega') }}</textarea>
                <small class="text-muted">Mueve el marcador para ajustar el punto exacto de entrega.</small>
            </div>

            <div class="mb-3">
                <label for="referencia_delivery" class="form-label">Referencia para el delivery</label>
                <textarea name="referencia_delivery" id="referencia_delivery" class="form-control" rows="2" placeholder="Ej.: puerta verde, frente a la farmacia, al lado de la plaza...">{{ old('referencia_delivery') }}</textarea>
                <small class="text-muted">Indica referencias que ayuden al repartidor a encontrar tu domicilio.</small>
            </div>

            <div class="mb-3">
                <label for="observacion_cliente" class="form-label">Observaciones del pedido</label>
                <textarea name="observacion_cliente" id="observacion_cliente" class="form-control" rows="2" maxlength="500" placeholder="Ej.: 1 hamburguesa sin cebolla, agregar poca salsa...">{{ old('observacion_cliente') }}</textarea>
                <small class="text-muted">Indica modificaciones o instrucciones sobre la comida.</small>
            </div>
        </div>

        <div class="cliente-pedido-section">
            <h2><i class="bi bi-map"></i> Ubicación de entrega</h2>
            <p class="text-muted">Selecciona tu ubicación en el mapa. Puedes mover el marcador si la ubicación automática no es exacta.</p>

            @if(empty(config('services.google_maps.key')))
                <div class="alert alert-warning">
                    <strong>Mapa no configurado.</strong>
                    Agrega <code>GOOGLE_MAPS_API_KEY</code> en tu archivo <code>.env</code> para activar Google Maps.
                </div>
            @endif

            <div class="d-flex flex-wrap gap-2 mb-3">
                <button type="button" id="btn-mi-ubicacion" class="btn btn-primary"><i class="bi bi-crosshair"></i> Usar mi ubicación actual</button>
                <button type="button" id="btn-direccion-mapa" class="btn btn-outline-secondary"><i class="bi bi-arrow-repeat"></i> Actualizar dirección</button>
            </div>

            <div id="mapa-pedido" style="width:100%; height:380px; border-radius:12px; overflow:hidden; border:1px solid #ddd; background:#f3f3f3;"></div>

            <div class="row mt-3">
                <div class="col-md-6 mb-3">
                    <label for="latitud" class="form-label">Latitud</label>
                    <input type="text" name="latitud" id="latitud" class="form-control" value="{{ old('latitud') }}" placeholder="-17.3895" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="longitud" class="form-label">Longitud</label>
                    <input type="text" name="longitud" id="longitud" class="form-control" value="{{ old('longitud') }}" placeholder="-66.1568" readonly>
                </div>
            </div>
            <div id="estado-ubicacion" class="small text-muted" aria-live="polite"></div>
        </div>

        <div class="cliente-pedido-section">
            <h2><i class="bi bi-credit-card"></i> Pago</h2>
            @if($configuracion)
                <p>Realiza el pago utilizando los datos proporcionados por el restaurante.</p>
                @if(!empty($configuracion->qr_pago))
                    <div class="mb-3"><p><strong>QR de pago:</strong></p><img src="{{ asset('storage/' . $configuracion->qr_pago) }}" alt="QR de pago" style="max-width:300px;"></div>
                @endif
                @if(!empty($configuracion->numero_cuenta))<p><strong>Número de cuenta:</strong> {{ $configuracion->numero_cuenta }}</p>@endif
            @else
                <div class="alert alert-warning">Los datos de pago todavía no han sido configurados.</div>
            @endif
        </div>

        <div class="cliente-pedido-section">
            <h2><i class="bi bi-image"></i> Comprobante de pago</h2>
            <div class="mb-3">
                <label for="comprobante" class="form-label">Selecciona una imagen del comprobante</label>
                <input type="file" name="comprobante" id="comprobante" class="form-control" accept=".jpg,.jpeg,.png" required>
                <small>Formatos permitidos: JPG, JPEG y PNG. Máximo 2 MB.</small>
            </div>
        </div>

        <div class="cliente-pedido-section">
            <h2><i class="bi bi-receipt"></i> Resumen del pedido</h2>
            <div class="cliente-pedido-total"><span>Total a pagar:</span><strong>Bs {{ number_format($total, 2) }}</strong></div>
        </div>

        <div class="cliente-pedido-actions">
            <a href="{{ route('cliente.carrito.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver al carrito</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Confirmar pedido</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    let mapaPedido = null;
    let marcadorPedido = null;
    let ultimaConsultaDireccion = 0;
    let consultaDireccionEnCurso = false;
    const cochabamba = { lat: -17.3935, lng: -66.1570 };

    function mostrarEstado(mensaje, error = false) {
        const estado = document.getElementById('estado-ubicacion');
        estado.textContent = mensaje;
        estado.classList.toggle('text-danger', error);
        estado.classList.toggle('text-muted', !error);
    }

    function actualizarCoordenadas(posicion, obtenerDireccion = true) {
        document.getElementById('latitud').value = posicion.lat().toFixed(7);
        document.getElementById('longitud').value = posicion.lng().toFixed(7);

        if (obtenerDireccion) {
            obtenerDireccionPedido(posicion);
        }
    }

    async function obtenerDireccionPedido(posicion) {
        const latitud = Number(posicion.lat());
        const longitud = Number(posicion.lng());

        if (!Number.isFinite(latitud) || !Number.isFinite(longitud)) {
            mostrarEstado('Coordenadas no válidas.', true);
            return;
        }

        const ahora = Date.now();
        const espera = Math.max(0, 1100 - (ahora - ultimaConsultaDireccion));

        if (consultaDireccionEnCurso) {
            return;
        }

        if (espera > 0) {
            mostrarEstado('Esperando para actualizar la dirección...');
            setTimeout(() => obtenerDireccionPedido(posicion), espera);
            return;
        }

        ultimaConsultaDireccion = Date.now();
        consultaDireccionEnCurso = true;
        mostrarEstado('Obteniendo dirección desde OpenStreetMap...');

        try {
            // La consulta se hace contra Laravel. Laravel consulta Nominatim en el servidor,
            // evitando problemas de CORS y permitiendo enviar un User-Agent identificativo.
            const url = new URL('{{ route('cliente.pedidos.direccion') }}', window.location.origin);
            url.searchParams.set('latitud', latitud.toString());
            url.searchParams.set('longitud', longitud.toString());

            const respuesta = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const datos = await respuesta.json();

            if (!respuesta.ok || !datos.ok || !datos.direccion) {
                throw new Error(datos.message || 'No se pudo obtener la dirección.');
            }

            document.getElementById('direccion_entrega').value = datos.direccion;
            mostrarEstado('Ubicación y dirección actualizadas correctamente.');
        } catch (error) {
            console.error('Error al obtener dirección:', error);
            mostrarEstado('No se pudo obtener la dirección automáticamente. Puedes escribirla manualmente.', true);
        } finally {
            consultaDireccionEnCurso = false;
        }
    }

    function colocarMarcador(posicion, centrar = true, obtenerDireccion = true) {
        if (!marcadorPedido) {
            marcadorPedido = new google.maps.Marker({
                position: posicion,
                map: mapaPedido,
                draggable: true,
                title: 'Ubicación de entrega'
            });

            marcadorPedido.addListener('dragend', function(evento) {
                actualizarCoordenadas(evento.latLng, true);
            });
        } else {
            marcadorPedido.setPosition(posicion);
        }

        if (centrar) {
            mapaPedido.setCenter(posicion);
        }

        actualizarCoordenadas(marcadorPedido.getPosition(), obtenerDireccion);
    }

    function inicializarMapaPedido() {
        const latGuardada = parseFloat(document.getElementById('latitud').value);
        const lngGuardada = parseFloat(document.getElementById('longitud').value);
        const centroInicial = Number.isFinite(latGuardada) && Number.isFinite(lngGuardada)
            ? { lat: latGuardada, lng: lngGuardada }
            : cochabamba;

        mapaPedido = new google.maps.Map(document.getElementById('mapa-pedido'), {
            center: centroInicial,
            zoom: 16,
            mapTypeControl: true,
            streetViewControl: false,
            fullscreenControl: true
        });

        // Google Maps se mantiene solamente para el mapa y el marcador.
        // La conversión coordenadas -> dirección se realiza con OpenStreetMap/Nominatim.
        colocarMarcador(centroInicial, false, true);

        document.getElementById('btn-mi-ubicacion').addEventListener('click', function() {
            if (!navigator.geolocation) {
                mostrarEstado('Tu navegador no permite obtener la ubicación.', true);
                return;
            }

            mostrarEstado('Obteniendo tu ubicación...');

            navigator.geolocation.getCurrentPosition(function(posicion) {
                colocarMarcador({
                    lat: posicion.coords.latitude,
                    lng: posicion.coords.longitude
                }, true, true);
            }, function() {
                mostrarEstado('No se pudo obtener tu ubicación. Revisa los permisos del navegador.', true);
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            });
        });

        document.getElementById('btn-direccion-mapa').addEventListener('click', function() {
            if (marcadorPedido) {
                obtenerDireccionPedido(marcadorPedido.getPosition());
            }
        });
    }

    function mapaNoDisponible() {
        const mapa = document.getElementById('mapa-pedido');
        mapa.innerHTML = '<div class="d-flex h-100 align-items-center justify-content-center text-muted p-4 text-center">Google Maps no está disponible. Configura GOOGLE_MAPS_API_KEY en .env o ingresa la dirección manualmente.</div>';
        document.getElementById('btn-mi-ubicacion').disabled = true;
        document.getElementById('btn-direccion-mapa').disabled = true;
        mostrarEstado('Mapa no disponible.', true);
    }
</script>

@if(!empty(config('services.google_maps.key')))
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ urlencode(config('services.google_maps.key')) }}&callback=inicializarMapaPedido"></script>
@else
<script>document.addEventListener('DOMContentLoaded', mapaNoDisponible);</script>
@endif
@endsection
