@extends('layouts.cliente')

@push('styles')
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css"
    onerror="this.onerror=null;this.href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';">
@endpush

@section('title', 'Inicio - Sabor Express')

@section('content')
@php
    $tieneUbicacionRestaurante =
        is_numeric($configuracion?->latitud_restaurante) &&
        is_numeric($configuracion?->longitud_restaurante);

    $tieneUbicacionCliente = $ultimaUbicacion &&
        is_numeric($ultimaUbicacion->latitud) &&
        is_numeric($ultimaUbicacion->longitud);

    $nombreRestaurante = $configuracion?->nombre_restaurante ?: 'Sabor Express';
@endphp

<div class="sx-client-home">

    @if(session('success'))
        <div class="sx-dashboard-flash sx-dashboard-flash-success" role="status">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="sx-dashboard-flash sx-dashboard-flash-error" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- PRESENTACIÓN PRINCIPAL --}}
    <section class="sx-client-hero">

        <div class="sx-client-hero-copy">
            <span class="sx-client-eyebrow">
                <i class="bi bi-stars"></i>
                SABOR EXPRESS
            </span>

            <h1>
                @if($esCliente)
                    ¡Hola, {{ Auth::user()->name }}!
                @else
                    ¡Tu próximo antojo empieza aquí!
                @endif
            </h1>

            <p>
                Descubre tus favoritos, elige algo delicioso y disfruta
                de tus pedidos sin complicaciones.
            </p>

            <div class="sx-client-hero-actions">
                <a href="#sx-dashboard-catalogo" class="sx-client-primary-action">
                    <i class="bi bi-grid-fill"></i>
                    Explorar el menú
                </a>

                <a href="{{ route('cliente.carrito.index') }}" class="sx-client-secondary-action">
                    <i class="bi bi-bag"></i>
                    Mi carrito
                </a>
            </div>


        </div>

        <div class="sx-client-hero-art" aria-hidden="true">
            <div class="sx-client-hero-glow"></div>
            <div class="sx-client-hero-logo">
                @if(!empty($configuracion?->logo))
                    <img
                        src="{{ asset('storage/' . $configuracion->logo) }}"
                        alt="">
                @else
                    <i class="bi bi-shop-window"></i>
                @endif
            </div>
            <span class="sx-client-hero-sticker sx-client-sticker-one">
                <i class="bi bi-fire"></i>
                ¡A disfrutar!
            </span>
            <span class="sx-client-hero-sticker sx-client-sticker-two">
                <i class="bi bi-bag-heart-fill"></i>
                Hecho para ti
            </span>
        </div>

    </section>

    {{-- CATÁLOGO DESPLEGABLE CON TODOS LOS PRODUCTOS DISPONIBLES --}}
    <section id="sx-dashboard-catalogo" class="sx-dashboard-catalog-section">

        <details class="sx-dashboard-catalog" open>

            <summary class="sx-dashboard-catalog-summary">
                <span class="sx-dashboard-catalog-heading">
                    <span class="sx-dashboard-catalog-icon">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </span>
                    <span>
                        <small>DESCUBRE LO QUE PREPARAMOS</small>
                        <strong>Todos nuestros productos</strong>
                        <em>Una vista rápida del menú. Desliza horizontalmente para descubrirlos.</em>
                    </span>
                </span>

                <span class="sx-dashboard-catalog-toggle">
                    <span>{{ $productos->count() }} productos</span>
                    <i class="bi bi-chevron-up"></i>
                </span>
            </summary>

            <div class="sx-dashboard-product-rail" aria-label="Vista rápida de todos los productos disponibles">

                @forelse($productos as $producto)

                    <a
                        href="{{ route('cliente.productos.index', ['buscar' => $producto->nombre]) }}"
                        class="sx-dashboard-product-chip"
                        title="Ver {{ $producto->nombre }} en el menú">

                        <span class="sx-dashboard-product-chip-image">
                            @if($producto->imagen)
                                <img
                                    src="{{ asset('storage/' . $producto->imagen) }}"
                                    alt="{{ $producto->nombre }}"
                                    loading="lazy">
                            @else
                                <i class="bi bi-image"></i>
                            @endif
                        </span>

                        <span class="sx-dashboard-product-chip-info">
                            <small>{{ $producto->categoria?->nombre ?? 'Sabor Express' }}</small>
                            <strong>{{ $producto->nombre }}</strong>
                            <span>Bs. {{ number_format((float) $producto->precio, 2) }}</span>
                        </span>

                        <i class="bi bi-arrow-up-right sx-dashboard-product-chip-arrow"></i>
                    </a>

                @empty

                    <div class="sx-dashboard-empty-products">
                        <i class="bi bi-basket"></i>
                        <strong>Pronto tendremos productos disponibles</strong>
                        <span>Vuelve a visitarnos para descubrir nuestro menú.</span>
                    </div>

                @endforelse

            </div>

            @if($productos->count() > 1)
                <div class="sx-dashboard-product-rail-hint">
                    <i class="bi bi-arrow-left-right"></i>
                    Desliza para ver todos los productos
                </div>
            @endif

        </details>

    </section>

    {{-- UBICACIÓN, CONTACTO Y RUTA HACIA EL RESTAURANTE --}}
    <section class="sx-dashboard-location-section">

        <div class="sx-dashboard-section-title">
            <span class="sx-dashboard-section-title-icon">
                <i class="bi bi-geo-alt-fill"></i>
            </span>
            <div>
                <small>ESTAMOS CERCA DE TI</small>
                <h2>Encuentra {{ $nombreRestaurante }}</h2>
                <p>Consulta nuestra ubicación y calcula cómo llegar desde donde te encuentres.</p>
            </div>
        </div>

        <div class="sx-dashboard-location-grid">

            {{-- MAPA INTERACTIVO --}}
            <div class="sx-dashboard-map-card">

                <div class="sx-dashboard-card-heading">
                    <span class="sx-dashboard-card-heading-icon">
                        <i class="bi bi-map-fill"></i>
                    </span>
                    <div>
                        <h3>Mapa y cómo llegar</h3>
                        <p>Ubicación del restaurante y tu último punto registrado.</p>
                    </div>
                </div>

                <div
                    id="cliente-dashboard-map"
                    class="sx-dashboard-map"
                    data-restaurant-name="{{ $nombreRestaurante }}"
                    data-restaurant-has-location="{{ $tieneUbicacionRestaurante ? '1' : '0' }}"
                    data-restaurant-lat="{{ $tieneUbicacionRestaurante ? $configuracion->latitud_restaurante : '' }}"
                    data-restaurant-lng="{{ $tieneUbicacionRestaurante ? $configuracion->longitud_restaurante : '' }}"
                    data-client-has-location="{{ $tieneUbicacionCliente ? '1' : '0' }}"
                    data-client-lat="{{ $tieneUbicacionCliente ? $ultimaUbicacion->latitud : '' }}"
                    data-client-lng="{{ $tieneUbicacionCliente ? $ultimaUbicacion->longitud : '' }}"
                    data-client-address="{{ $tieneUbicacionCliente ? $ultimaUbicacion->direccion_entrega : '' }}"
                    data-osrm-url="{{ rtrim((string) config('services.osrm.url', 'https://router.project-osrm.org'), '/') }}">
                </div>

                <div class="sx-dashboard-map-legend">
                    <span><i class="sx-legend-dot sx-legend-restaurant"></i> Restaurante</span>
                    @if($tieneUbicacionCliente)
                        <span><i class="sx-legend-dot sx-legend-client"></i> Última ubicación guardada</span>
                    @endif
                    <span><i class="sx-legend-line"></i> Ruta calculada</span>
                </div>

                <div class="sx-dashboard-route-actions">
                    <button
                        type="button"
                        id="cliente-dashboard-como-llegar"
                        class="sx-dashboard-directions-button"
                        {{ $tieneUbicacionRestaurante ? '' : 'disabled' }}>
                        <i class="bi bi-sign-turn-right-fill"></i>
                        <span>Cómo llegar al restaurante</span>
                    </button>

                    @if($tieneUbicacionRestaurante)
                        <a
                            class="sx-dashboard-open-map"
                            href="https://www.openstreetmap.org/?mlat={{ $configuracion->latitud_restaurante }}&mlon={{ $configuracion->longitud_restaurante }}#map=17/{{ $configuracion->latitud_restaurante }}/{{ $configuracion->longitud_restaurante }}"
                            target="_blank"
                            rel="noopener noreferrer">
                            <i class="bi bi-box-arrow-up-right"></i>
                            Abrir mapa
                        </a>
                    @endif
                </div>

                <p
                    id="cliente-dashboard-route-status"
                    class="sx-dashboard-route-status"
                    role="status"
                    aria-live="polite"></p>

            </div>

            {{-- CONTACTO Y ÚLTIMA UBICACIÓN DEL CLIENTE --}}
            <aside class="sx-dashboard-contact-column">

                <div class="sx-dashboard-contact-card">

                    <div class="sx-dashboard-card-heading">
                        <span class="sx-dashboard-card-heading-icon">
                            <i class="bi bi-shop"></i>
                        </span>
                        <div>
                            <h3>Visítanos</h3>
                            <p>Estamos listos para atenderte.</p>
                        </div>
                    </div>

                    <div class="sx-dashboard-contact-item">
                        <span class="sx-dashboard-contact-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </span>
                        <div>
                            <small>Dirección del restaurante</small>
                            <strong>
                                {{ $configuracion?->direccion ?: 'Dirección aún no configurada' }}
                            </strong>
                        </div>
                    </div>

                    @if($configuracion?->telefono)
                        <div class="sx-dashboard-contact-item">
                            <span class="sx-dashboard-contact-icon sx-contact-whatsapp">
                                <i class="bi bi-whatsapp"></i>
                            </span>
                            <div class="sx-dashboard-phone-content">
                                <small>Teléfono / WhatsApp</small>

                                @if($enlaceWhatsApp)
                                    <a
                                        href="{{ $enlaceWhatsApp }}"
                                        class="sx-dashboard-whatsapp-link"
                                        target="_blank"
                                        rel="noopener noreferrer">
                                        <i class="bi bi-whatsapp"></i>
                                        <strong>{{ $configuracion->telefono }}</strong>
                                        <span>Escríbenos</span>
                                    </a>
                                @else
                                    <a href="tel:{{ $configuracion->telefono }}" class="sx-dashboard-whatsapp-link">
                                        <i class="bi bi-telephone-fill"></i>
                                        <strong>{{ $configuracion->telefono }}</strong>
                                        <span>Llamar</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="sx-dashboard-contact-item">
                            <span class="sx-dashboard-contact-icon">
                                <i class="bi bi-telephone-x-fill"></i>
                            </span>
                            <div>
                                <small>Teléfono</small>
                                <strong>Teléfono aún no configurado</strong>
                            </div>
                        </div>
                    @endif

                    @if($tieneUbicacionRestaurante)
                        <a
                            href="https://www.google.com/maps/dir/?api=1&destination={{ $configuracion->latitud_restaurante }},{{ $configuracion->longitud_restaurante }}"
                            class="sx-dashboard-contact-directions"
                            target="_blank"
                            rel="noopener noreferrer">
                            <i class="bi bi-sign-turn-right"></i>
                            Cómo llegar al restaurante
                        </a>
                    @endif

                </div>



            </aside>

        </div>

    </section>

</div>
@endsection

@section('scripts')
<script
    src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"
    onerror="this.onerror=null;this.src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';"
    defer></script>
<script src="{{ asset('js/cliente-dashboard.js') }}" defer></script>
@endsection
