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

            <div class="sx-client-hero-benefits">
                <span><i class="bi bi-check-circle-fill"></i> Pedido sencillo</span>
                <span><i class="bi bi-geo-alt-fill"></i> Seguimiento de entrega</span>
                <span><i class="bi bi-heart-fill"></i> Tus favoritos</span>
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

    {{-- ACCESOS RÁPIDOS --}}
    <section class="sx-dashboard-shortcuts" aria-label="Accesos rápidos">

        <a href="{{ route('cliente.productos.index') }}" class="sx-dashboard-shortcut">
            <span class="sx-dashboard-shortcut-icon sx-shortcut-pink">
                <i class="bi bi-egg-fried"></i>
            </span>
            <span>
                <strong>Productos</strong>
                <small>Explora el catálogo</small>
            </span>
            <i class="bi bi-arrow-up-right"></i>
        </a>

        <a href="{{ route('cliente.carrito.index') }}" class="sx-dashboard-shortcut">
            <span class="sx-dashboard-shortcut-icon sx-shortcut-orange">
                <i class="bi bi-cart3"></i>
            </span>
            <span>
                <strong>Mi carrito</strong>
                <small>Revisa tu selección</small>
            </span>
            <i class="bi bi-arrow-up-right"></i>
        </a>

        <a href="{{ route('cliente.pedidos.index') }}" class="sx-dashboard-shortcut">
            <span class="sx-dashboard-shortcut-icon sx-shortcut-blue">
                <i class="bi bi-receipt"></i>
            </span>
            <span>
                <strong>Mis pedidos</strong>
                <small>Consulta tus compras</small>
            </span>
            <i class="bi bi-arrow-up-right"></i>
        </a>

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
                        <small>ELIGE ALGO DELICIOSO</small>
                        <strong>Menú de {{ $nombreRestaurante }}</strong>
                        <em>Todos los productos disponibles en un solo lugar</em>
                    </span>
                </span>

                <span class="sx-dashboard-catalog-toggle">
                    <span>{{ $productos->count() }} productos</span>
                    <i class="bi bi-chevron-up"></i>
                </span>
            </summary>

            <div class="sx-dashboard-product-grid">

                @forelse($productos as $producto)

                    <article class="sx-dashboard-product-card">

                        <div class="sx-dashboard-product-image">
                            @if($producto->imagen)
                                <img
                                    src="{{ asset('storage/' . $producto->imagen) }}"
                                    alt="{{ $producto->nombre }}"
                                    loading="lazy">
                            @else
                                <div class="sx-dashboard-product-no-image">
                                    <i class="bi bi-image"></i>
                                    <span>Imagen no disponible</span>
                                </div>
                            @endif

                            <span class="sx-dashboard-product-category">
                                <i class="bi bi-tag-fill"></i>
                                {{ $producto->categoria?->nombre ?? 'Sin categoría' }}
                            </span>
                        </div>

                        <div class="sx-dashboard-product-body">
                            <h3>{{ $producto->nombre }}</h3>

                            <p>
                                {{ $producto->descripcion ?: 'Disfruta este producto preparado para ti.' }}
                            </p>

                            <div class="sx-dashboard-product-footer">
                                <div class="sx-dashboard-product-price">
                                    <small>Precio</small>
                                    <strong>Bs. {{ number_format((float) $producto->precio, 2) }}</strong>
                                </div>

                                <span class="sx-dashboard-stock">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Disponible
                                </span>
                            </div>

                            <form
                                action="{{ route('cliente.carrito.agregar', $producto->id) }}"
                                method="POST">
                                @csrf

                                <button type="submit" class="sx-dashboard-add-button">
                                    <i class="bi bi-cart-plus-fill"></i>
                                    Agregar al carrito
                                </button>
                            </form>
                        </div>

                    </article>

                @empty

                    <div class="sx-dashboard-empty-products">
                        <i class="bi bi-basket"></i>
                        <h3>Estamos preparando nuestro menú</h3>
                        <p>Por el momento no hay productos disponibles. Vuelve a visitarnos pronto.</p>
                    </div>

                @endforelse

            </div>

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
                    aria-live="polite">
                    @if(!$tieneUbicacionRestaurante)
                        La ubicación del restaurante todavía no está configurada.
                    @elseif($tieneUbicacionCliente)
                        Se muestra tu última ubicación de entrega guardada. Pulsa «Cómo llegar» para usar tu GPS actual.
                    @else
                        Pulsa «Cómo llegar» para permitir que el navegador obtenga tu ubicación actual y trace la ruta.
                    @endif
                </p>

                <div id="cliente-dashboard-route-summary" class="sx-dashboard-route-summary" hidden></div>

                <div id="cliente-dashboard-route-details" class="sx-dashboard-route-details" hidden>
                    <h4><i class="bi bi-signpost-split-fill"></i> Indicaciones del recorrido</h4>
                    <ol id="cliente-dashboard-route-steps"></ol>
                    <a
                        id="cliente-dashboard-external-navigation"
                        class="sx-dashboard-external-navigation"
                        href="#"
                        target="_blank"
                        rel="noopener noreferrer"
                        hidden>
                        <i class="bi bi-navigation-fill"></i>
                        Abrir navegación paso a paso
                    </a>
                </div>

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

                <div class="sx-dashboard-saved-location-card">

                    <div class="sx-dashboard-card-heading">
                        <span class="sx-dashboard-card-heading-icon sx-saved-location-icon">
                            <i class="bi bi-person-pin-map-fill"></i>
                        </span>
                        <div>
                            <h3>Tu ubicación guardada</h3>
                            <p>Última dirección registrada en tus pedidos.</p>
                        </div>
                    </div>

                    @if($tieneUbicacionCliente)
                        <div class="sx-dashboard-saved-address">
                            <i class="bi bi-geo-alt-fill"></i>
                            <p>{{ $ultimaUbicacion->direccion_entrega }}</p>
                        </div>

                        <small class="sx-dashboard-saved-date">
                            Pedido #{{ $ultimaUbicacion->id }}
                            · {{ $ultimaUbicacion->created_at?->format('d/m/Y H:i') }}
                        </small>
                    @elseif($esCliente)
                        <div class="sx-dashboard-no-saved-location">
                            <i class="bi bi-info-circle"></i>
                            <p>
                                Todavía no tienes una ubicación guardada en tus pedidos.
                                Al confirmar tu primer pedido, el sistema registrará tu dirección y coordenadas.
                            </p>
                        </div>
                    @else
                        <div class="sx-dashboard-no-saved-location">
                            <i class="bi bi-person-lock"></i>
                            <p>
                                Inicia sesión como Cliente y realiza un pedido para consultar aquí tu última dirección guardada.
                            </p>
                            <a href="{{ route('login') }}">Iniciar sesión</a>
                        </div>
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
