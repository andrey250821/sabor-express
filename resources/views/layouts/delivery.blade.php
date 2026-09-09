<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <meta
        name="theme-color"
        content="#111318">

    <title>
        @yield('title', 'Delivery')
        -
        {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}
    </title>

    {{-- Bootstrap 5.3.3 --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- CSS DELIVERY --}}
    <link
        rel="stylesheet"
        href="{{ asset('css/delivery.css') }}">

    @stack('styles')

</head>

<body>

    <div class="delivery-wrapper">

        {{-- =====================================================
            SIDEBAR
        ====================================================== --}}

        <aside
            class="delivery-sidebar"
            id="deliverySidebar">

            {{-- BOTÓN CERRAR EN MÓVIL --}}
            <button
                type="button"
                class="delivery-sidebar-close d-lg-none"
                id="deliverySidebarClose"
                aria-label="Cerrar menú">

                <i class="bi bi-x-lg"></i>

            </button>


            {{-- =================================================
                MARCA / LOGO
            ================================================== --}}

            <div class="delivery-brand">

                <a
                    href="{{ route('delivery.dashboard') }}"
                    class="delivery-brand-link">

                    <div class="delivery-brand-logo">

                        @if(!empty($configuracion?->logo))

                        <img
                            src="{{ asset('storage/' . $configuracion->logo) }}"
                            alt="{{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}">

                        @else

                        <i class="bi bi-bicycle"></i>

                        @endif

                    </div>


                    <div class="delivery-brand-text">

                        <strong>
                            {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}
                        </strong>

                        <span>
                            Panel Delivery
                        </span>

                    </div>

                </a>

            </div>


            {{-- =================================================
                ESTADO DEL DELIVERY
            ================================================== --}}

            <div class="delivery-status-box">

                <span class="delivery-status-indicator"></span>

                <div>

                    <strong>
                        En servicio
                    </strong>

                    <small>
                        Listo para entregar
                    </small>

                </div>

            </div>


            {{-- =================================================
                NAVEGACIÓN
            ================================================== --}}

            <nav class="delivery-nav">

                <div class="delivery-nav-title">
                    MENÚ
                </div>


                {{-- DASHBOARD --}}

                <a
                    href="{{ route('delivery.dashboard') }}"
                    class="delivery-nav-link
                    {{ request()->routeIs('delivery.dashboard') ? 'active' : '' }}">

                    <span class="delivery-nav-icon">
                        <i class="bi bi-grid-1x2-fill"></i>
                    </span>

                    <span class="delivery-nav-text">
                        Dashboard
                    </span>

                </a>


                {{-- PEDIDOS DISPONIBLES --}}

                <a
                    href="{{ route('delivery.pedidos.index') }}"
                    class="delivery-nav-link
                    {{ request()->routeIs('delivery.pedidos.index', 'delivery.pedidos.show') ? 'active' : '' }}">

                    <span class="delivery-nav-icon">
                        <i class="bi bi-box-seam-fill"></i>
                    </span>

                    <span class="delivery-nav-text">
                        Pedidos disponibles
                    </span>

                    <span class="delivery-nav-arrow">
                        <i class="bi bi-chevron-right"></i>
                    </span>

                </a>


                {{-- MIS PEDIDOS --}}

                <a
                    href="{{ route('delivery.pedidos.mis') }}"
                    class="delivery-nav-link
                    {{ request()->routeIs('delivery.pedidos.mis') ? 'active' : '' }}">

                    <span class="delivery-nav-icon">
                        <i class="bi bi-bicycle"></i>
                    </span>

                    <span class="delivery-nav-text">
                        Mis pedidos
                    </span>

                </a>

            </nav>


            {{-- =================================================
                ESPACIO
            ================================================== --}}

            <div class="delivery-sidebar-spacer"></div>


            {{-- =================================================
                INFORMACIÓN DEL USUARIO
            ================================================== --}}

            <div class="delivery-user-card">

                <div class="delivery-user-avatar">

                    <i class="bi bi-person-fill"></i>

                </div>


                <div class="delivery-user-info">

                    <strong>
                        {{ Auth::user()->name ?? 'Delivery' }}
                    </strong>

                    <span>
                        Repartidor
                    </span>

                </div>

            </div>


            {{-- =================================================
                CERRAR SESIÓN
            ================================================== --}}

            <form
                method="POST"
                action="{{ route('logout') }}"
                class="delivery-logout-form">

                @csrf

                <button
                    type="submit"
                    class="delivery-logout">

                    <i class="bi bi-box-arrow-left"></i>

                    <span>
                        Cerrar sesión
                    </span>

                </button>

            </form>

        </aside>


        {{-- OVERLAY PARA MÓVIL --}}

        <div
            class="delivery-overlay"
            id="deliveryOverlay">
        </div>


        {{-- =====================================================
            CONTENIDO PRINCIPAL
        ====================================================== --}}

        <main class="delivery-main">


            {{-- =================================================
                TOPBAR
            ================================================== --}}

            <header class="delivery-topbar">

                <div class="delivery-topbar-left">

                    {{-- BOTÓN MENÚ MÓVIL --}}

                    <button
                        type="button"
                        class="delivery-menu-toggle d-lg-none"
                        id="deliveryMenuToggle"
                        aria-label="Abrir menú">

                        <i class="bi bi-list"></i>

                    </button>


                    <div class="delivery-page-heading">

                        <span>
                            @yield('section', 'Panel de delivery')
                        </span>

                        <h1>
                            @yield(
                            'heading',
                            'Panel del Delivery'
                            )
                        </h1>

                    </div>

                </div>


                {{-- =================================================
                    USUARIO TOPBAR
                ================================================== --}}

                <div class="delivery-topbar-user">

                    <div class="delivery-topbar-user-icon">

                        <i class="bi bi-bicycle"></i>

                    </div>


                    <div class="delivery-topbar-user-info">

                        <strong>
                            {{ Auth::user()->name ?? 'Delivery' }}
                        </strong>

                        <span>
                            Repartidor
                        </span>

                    </div>

                </div>

            </header>


            {{-- =================================================
                CONTENIDO
            ================================================== --}}

            <section class="delivery-content">

                {{-- MENSAJES DE SESIÓN --}}

                @if(session('success'))

                <div
                    class="alert delivery-alert delivery-alert-success alert-dismissible fade show"
                    role="alert">

                    <i class="bi bi-check-circle-fill"></i>

                    <span>
                        {{ session('success') }}
                    </span>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                    </button>

                </div>

                @endif


                @if(session('error'))

                <div
                    class="alert delivery-alert delivery-alert-error alert-dismissible fade show"
                    role="alert">

                    <i class="bi bi-exclamation-triangle-fill"></i>

                    <span>
                        {{ session('error') }}
                    </span>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                    </button>

                </div>

                @endif


                @yield('content')

            </section>


            {{-- =================================================
                FOOTER
            ================================================== --}}

            <footer class="delivery-footer">

                <span>

                    <i class="bi bi-shield-check"></i>

                    Panel protegido

                </span>

                <span>

                    {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}

                </span>

            </footer>

        </main>

    </div>


    {{-- =====================================================
        BOOTSTRAP JS
    ====================================================== --}}

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>


    {{-- =====================================================
        MENÚ RESPONSIVE
    ====================================================== --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const sidebar =
                document.getElementById('deliverySidebar');

            const overlay =
                document.getElementById('deliveryOverlay');

            const menuToggle =
                document.getElementById('deliveryMenuToggle');

            const closeButton =
                document.getElementById('deliverySidebarClose');


            function openSidebar() {

                sidebar?.classList.add('show');

                overlay?.classList.add('show');

                document.body.classList.add('delivery-menu-open');

            }


            function closeSidebar() {

                sidebar?.classList.remove('show');

                overlay?.classList.remove('show');

                document.body.classList.remove('delivery-menu-open');

            }


            menuToggle?.addEventListener(
                'click',
                openSidebar
            );


            closeButton?.addEventListener(
                'click',
                closeSidebar
            );


            overlay?.addEventListener(
                'click',
                closeSidebar
            );


            document
                .querySelectorAll('.delivery-nav-link')
                .forEach(function(link) {

                    link.addEventListener(
                        'click',
                        function() {

                            if (window.innerWidth < 992) {
                                closeSidebar();
                            }

                        }
                    );

                });


            window.addEventListener(
                'resize',
                function() {

                    if (window.innerWidth >= 992) {
                        closeSidebar();
                    }

                }
            );

        });
    </script>
    @vite('resources/js/app.js')

    @stack('scripts')

</body>

</html>