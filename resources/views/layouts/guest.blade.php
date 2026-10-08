@php
    $configuracion = $configuracion ?? \App\Models\Configuracion::first();
    $nombreRestaurante = $configuracion?->nombre_restaurante ?: 'Sabor Express';
    $esRegistro = request()->routeIs('register') || request()->is('register');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ $configuracion->nombre_restaurante ?? config('app.name', 'Sabor Express') }}
        · {{ $esRegistro ? 'Registro' : 'Iniciar sesión' }}
    </title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body class="sx-auth-page">

    @php
        $logoRestaurante = $configuracion?->logo
            ? asset('storage/' . ltrim($configuracion->logo, '/'))
            : null;
        $tieneUbicacionRestaurante =
            is_numeric($configuracion?->latitud_restaurante) &&
            is_numeric($configuracion?->longitud_restaurante);
    @endphp

    <div class="sx-auth-background"></div>

    <main class="sx-auth-shell">

        <section class="sx-auth-main">

            <div class="sx-auth-brandbar">
                <a href="{{ url('/') }}" class="sx-auth-brand" aria-label="Ir al inicio">
                    <span class="sx-auth-brand-logo">
                        @if($logoRestaurante)
                            <img src="{{ $logoRestaurante }}" alt="Logo de {{ $nombreRestaurante }}">
                        @else
                            <i class="bi bi-shop-window"></i>
                        @endif
                    </span>

                    <span>
                        <strong>{{ $nombreRestaurante }}</strong>
                        <small>Pedidos rápidos · Sabores que disfrutas</small>
                    </span>
                </a>

                <span class="sx-auth-brand-badge">
                    <i class="bi bi-stars"></i>
                    Sabor local
                </span>
            </div>

            <div class="sx-auth-form-area">

                <div class="sx-auth-welcome">
                    <span class="sx-auth-eyebrow">
                        {{ $esRegistro ? 'NUEVO CLIENTE' : 'BIENVENIDO DE NUEVO' }}
                    </span>

                    <h1>
                        {{ $esRegistro
                            ? 'Regístrate en ' . $nombreRestaurante
                            : 'Inicia sesión en ' . $nombreRestaurante }}
                    </h1>

                    <p>
                        {{ $esRegistro
                            ? 'Crea tu cuenta para pedir en línea, guardar tus datos y seguir tus entregas.'
                            : 'Accede a tu cuenta para continuar con tus pedidos y consultar tus entregas.' }}
                    </p>
                </div>

                <div class="sx-auth-card">
                    {{ $slot }}
                </div>

            </div>

            <footer class="sx-auth-footer">
                <span>
                    © {{ date('Y') }} {{ $nombreRestaurante }}
                </span>

                @if($configuracion?->telefono)
                    <a href="tel:{{ $configuracion->telefono }}">
                        <i class="bi bi-telephone-fill"></i>
                        {{ $configuracion->telefono }}
                    </a>
                @endif
            </footer>

        </section>

        <aside class="sx-auth-showcase">

            <div class="sx-auth-showcase-content">

                <div class="sx-auth-showcase-heading">
                    <span class="sx-auth-showcase-icon">
                        <i class="bi bi-geo-alt-fill"></i>
                    </span>

                    <div>
                        <span>Encuéntranos</span>
                        <h2>{{ $nombreRestaurante }}</h2>
                    </div>
                </div>

                <div class="sx-auth-map-card">

                    <div
                        id="sx-auth-map"
                        class="sx-auth-map"
                        data-has-location="{{ $tieneUbicacionRestaurante ? '1' : '0' }}"
                        data-lat="{{ $tieneUbicacionRestaurante ? $configuracion->latitud_restaurante : '' }}"
                        data-lng="{{ $tieneUbicacionRestaurante ? $configuracion->longitud_restaurante : '' }}"
                        data-name="{{ $nombreRestaurante }}">
                    </div>

                    <div class="sx-auth-map-badge">
                        <span class="sx-auth-pulse"></span>
                        Ubicación del restaurante
                    </div>

                    @if($tieneUbicacionRestaurante)
                        <a
                            class="sx-auth-map-link"
                            href="https://www.openstreetmap.org/?mlat={{ $configuracion->latitud_restaurante }}&mlon={{ $configuracion->longitud_restaurante }}#map=18/{{ $configuracion->latitud_restaurante }}/{{ $configuracion->longitud_restaurante }}"
                            target="_blank"
                            rel="noopener noreferrer">
                            <i class="bi bi-map-fill"></i>
                            Abrir mapa
                        </a>
                    @endif

                </div>

                <div class="sx-auth-contact-card">

                    <div class="sx-auth-contact-item">
                        <span class="sx-auth-contact-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </span>

                        <div>
                            <small>Dirección</small>
                            <strong>
                                {{ $configuracion?->direccion ?: 'Ubicación aún no configurada' }}
                            </strong>
                        </div>
                    </div>

                    @if($configuracion?->telefono)
                        <div class="sx-auth-contact-item">
                            <span class="sx-auth-contact-icon">
                                <i class="bi bi-telephone-fill"></i>
                            </span>

                            <div>
                                <small>Teléfono</small>
                                <strong>{{ $configuracion->telefono }}</strong>
                            </div>
                        </div>
                    @endif

                </div>

                <div class="sx-auth-showcase-message">
                    <i class="bi bi-heart-fill"></i>
                    <p>
                        Elige tus favoritos, realiza tu pedido y disfruta
                        de una experiencia rápida con {{ $nombreRestaurante }}.
                    </p>
                </div>

            </div>

        </aside>

    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const mapElement = document.getElementById('sx-auth-map');

            if (!mapElement || typeof L === 'undefined') {
                return;
            }

            const hasLocation = mapElement.dataset.hasLocation === '1';

            const fallbackCenter = [-17.3935, -66.1570];

            const lat = hasLocation
                ? Number(mapElement.dataset.lat)
                : fallbackCenter[0];

            const lng = hasLocation
                ? Number(mapElement.dataset.lng)
                : fallbackCenter[1];

            const center = Number.isFinite(lat) && Number.isFinite(lng)
                ? [lat, lng]
                : fallbackCenter;

            const map = L.map(mapElement, {
                zoomControl: true,
                scrollWheelZoom: true,
                dragging: true
            }).setView(center, hasLocation ? 17 : 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            if (hasLocation) {

                const restaurantIcon = L.divIcon({
                    className: 'sx-auth-marker-wrap',
                    html: '<div class="sx-auth-marker"><i class="bi bi-shop-window"></i></div>',
                    iconSize: [46, 46],
                    iconAnchor: [23, 46],
                    popupAnchor: [0, -40]
                });

                L.marker(center, {
                    icon: restaurantIcon,
                    title: mapElement.dataset.name || 'Sabor Express'
                })
                .addTo(map)
                .bindPopup(
                    '<strong>' +
                    (mapElement.dataset.name || 'Sabor Express') +
                    '</strong><br>Ubicación del restaurante'
                )
                .openPopup();
            }

            setTimeout(function () {
                map.invalidateSize();
            }, 250);
        });

        document.querySelectorAll('.sx-auth-password-toggle').forEach(function (button) {

            button.addEventListener('click', function () {

                const inputId = button.dataset.target;
                const input = document.getElementById(inputId);

                if (!input) {
                    return;
                }

                const visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';

                button.innerHTML = visible
                    ? '<i class="bi bi-eye"></i>'
                    : '<i class="bi bi-eye-slash"></i>';

                button.setAttribute(
                    'aria-label',
                    visible ? 'Mostrar contraseña' : 'Ocultar contraseña'
                );
            });

        });

        const password = document.getElementById('password');
        const strength = document.getElementById('sx-password-strength');

        if (password && strength) {

            password.addEventListener('input', function () {

                const value = password.value;
                let score = 0;

                if (value.length >= 8) score++;
                if (/[A-Z]/.test(value)) score++;
                if (/[a-z]/.test(value)) score++;
                if (/\d/.test(value)) score++;
                if (/[^A-Za-z0-9]/.test(value)) score++;

                strength.className = 'sx-password-strength';

                if (!value) {
                    strength.dataset.level = '0';
                    strength.querySelector('span').textContent = '';
                } else if (score <= 2) {
                    strength.dataset.level = '1';
                    strength.querySelector('span').textContent = 'Contraseña débil';
                } else if (score <= 4) {
                    strength.dataset.level = '2';
                    strength.querySelector('span').textContent = 'Contraseña media';
                } else {
                    strength.dataset.level = '3';
                    strength.querySelector('span').textContent = 'Contraseña fuerte';
                }

            });

        }
    </script>

</body>

</html>
