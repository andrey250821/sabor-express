<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}">

    <title>
        {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}
        - Panel Administrativo
    </title>


    {{-- Bootstrap --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    {{-- Bootstrap Icons --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">


    {{-- Leaflet CSS --}}
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">


    {{-- CSS principal --}}
    <link
        rel="stylesheet"
        href="{{ asset('css/admin.css') }}">


    @stack('styles')

</head>


<body>


    <div class="admin-wrapper">


        {{-- =====================================================
        SIDEBAR
        ====================================================== --}}

        <aside class="admin-sidebar" id="admin-sidebar" aria-label="Menú principal del administrador">


            {{-- LOGO / IDENTIDAD --}}
            <div class="sidebar-brand">


                <div class="sidebar-logo">

                    @if(!empty($configuracion->logo))

                    <img
                        src="{{ asset('storage/' . $configuracion->logo) }}"
                        alt="{{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}">

                    @else

                    <span>🍔</span>

                    @endif

                </div>


                <div class="sidebar-brand-name">

                    {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}

                </div>


                <div class="sidebar-brand-subtitle">

                    Panel Administrativo

                </div>


            </div>


            {{-- SEPARADOR --}}
            <div class="sidebar-divider"></div>


            {{-- =================================================
            MENU PRINCIPAL
            ================================================== --}}

            <nav class="sidebar-menu">


                {{-- Dashboard --}}
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">

                    <span class="sidebar-icon">
                        <i class="bi bi-speedometer2"></i>
                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>


                {{-- Pedidos --}}
                <a
                    href="{{ route('admin.pedidos.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}">

                    <span class="sidebar-icon">
                        <i class="bi bi-bag"></i>
                    </span>

                    <span>
                        Pedidos
                    </span>

                </a>


                {{-- Comprobantes --}}
                <a
                    href="{{ route('admin.comprobantes.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.comprobantes.*') ? 'active' : '' }}">

                    <span class="sidebar-icon">
                        <i class="bi bi-receipt"></i>
                    </span>

                    <span>
                        Comprobantes
                    </span>

                </a>


                {{-- Productos --}}
                <a
                    href="{{ route('admin.productos.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.productos.*') ? 'active' : '' }}">

                    <span class="sidebar-icon">
                        <i class="bi bi-box-seam"></i>
                    </span>

                    <span>
                        Productos
                    </span>

                </a>


                {{-- Categorías --}}
                <a
                    href="{{ route('admin.categorias.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.categorias.*') ? 'active' : '' }}">

                    <span class="sidebar-icon">
                        <i class="bi bi-tags"></i>
                    </span>

                    <span>
                        Categorías
                    </span>

                </a>


                {{-- Deliverys --}}
                <a
                    href="{{ route('admin.deliverys.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.deliverys.*') ? 'active' : '' }}">

                    <span class="sidebar-icon">
                        <i class="bi bi-bicycle"></i>
                    </span>

                    <span>
                        Deliverys
                    </span>

                </a>


                {{-- Clientes --}}
                <a
                    href="{{ route('admin.clientes.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.clientes.*') ? 'active' : '' }}">

                    <span class="sidebar-icon">
                        <i class="bi bi-people"></i>
                    </span>

                    <span>
                        Clientes
                    </span>

                </a>


                {{-- Cocineros --}}
                <a
                    href="{{ route('admin.cocineros.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.cocineros.*') ? 'active' : '' }}">

                    <span class="sidebar-icon">
                        <i class="bi bi-person-badge-fill"></i>
                    </span>

                    <span>
                        Cocineros
                    </span>

                </a>


                {{-- =================================================
                NOTIFICACIONES
                ================================================== --}}

                @php

                $notificacionesNoLeidas = auth()->user()
                ->notificaciones()
                ->vigentes()
                ->whereIn('evento', [
                'comprobante_en_revision',
                'nueva_calificacion',
                ])
                ->where('leido', false)
                ->count();

                @endphp


                <a
                    href="{{ route('admin.notificaciones.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.notificaciones.*') ? 'active' : '' }}">

                    <span class="sidebar-icon">
                        <i class="bi bi-bell"></i>
                    </span>

                    <span class="flex-grow-1">
                        Notificaciones
                    </span>

                    <span
                        id="badge-notificaciones"
                        class="badge bg-danger rounded-pill {{ $notificacionesNoLeidas > 0 ? '' : 'd-none' }}">
                        {{ $notificacionesNoLeidas }}
                    </span>

                </a>


                {{-- Configuración --}}
                <a
                    href="{{ route('admin.configuracion.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.configuracion.*') ? 'active' : '' }}">

                    <span class="sidebar-icon">
                        <i class="bi bi-gear"></i>
                    </span>

                    <span>
                        Configuración
                    </span>

                </a>


            </nav>


        </aside>

        {{-- FONDO PARA CERRAR EL MENÚ EN CELULAR --}}
        <button
            type="button"
            class="admin-sidebar-overlay"
            id="admin-sidebar-overlay"
            aria-label="Cerrar menú"
            aria-hidden="true">
        </button>


        {{-- =====================================================
        CONTENIDO PRINCIPAL
        ====================================================== --}}

        <main class="admin-main">


            {{-- =================================================
            HEADER
            ================================================== --}}

            <header class="admin-topbar">


                <div class="topbar-left d-flex align-items-center gap-2">

                    {{-- BOTÓN MENÚ MÓVIL --}}
                    <button
                        type="button"
                        class="admin-mobile-toggle"
                        id="admin-mobile-toggle"
                        aria-label="Abrir menú"
                        aria-controls="admin-sidebar"
                        aria-expanded="false">
                        <i class="bi bi-list"></i>
                    </button>

                    <div>

                        <h1 class="topbar-title">

                            Panel Administrativo

                        </h1>


                        <p class="topbar-subtitle">

                            Gestión de
                            {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}

                        </p>

                    </div>

                </div>


                {{-- USUARIO --}}
                <div class="topbar-user">


                    <div class="topbar-user-info">

                        <strong>

                            {{ Auth::user()->name }}

                        </strong>

                        <span>

                            Administrador

                        </span>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('logout') }}">

                        @csrf

                        <button
                            type="submit"
                            class="topbar-logout">

                            <i class="bi bi-box-arrow-right"></i>

                            <span>
                                Salir
                            </span>

                        </button>

                    </form>


                </div>


            </header>


            {{-- =================================================
            CONTENIDO DE CADA VISTA
            ================================================== --}}

            <section class="admin-content">

                @yield('content')

            </section>


        </main>


    </div>


    {{-- Bootstrap JS --}}
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>


    {{-- Leaflet JS --}}
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
    </script>


    @stack('scripts')


    {{-- NAVEGACIÓN RESPONSIVA DEL PANEL ADMINISTRATIVO --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('admin-sidebar');
            const toggle = document.getElementById('admin-mobile-toggle');
            const overlay = document.getElementById('admin-sidebar-overlay');

            if (!sidebar || !toggle || !overlay) {
                return;
            }

            const mobileMedia = window.matchMedia('(max-width: 991.98px)');

            const cerrarMenu = () => {
                sidebar.classList.remove('is-open');
                overlay.classList.remove('is-visible');
                toggle.setAttribute('aria-expanded', 'false');
                overlay.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('admin-menu-open');
            };

            const abrirMenu = () => {
                if (!mobileMedia.matches) {
                    return;
                }

                sidebar.classList.add('is-open');
                overlay.classList.add('is-visible');
                toggle.setAttribute('aria-expanded', 'true');
                overlay.setAttribute('aria-hidden', 'false');
                document.body.classList.add('admin-menu-open');
            };

            toggle.addEventListener('click', function () {
                sidebar.classList.contains('is-open')
                    ? cerrarMenu()
                    : abrirMenu();
            });

            overlay.addEventListener('click', cerrarMenu);

            sidebar.querySelectorAll('.sidebar-link').forEach((link) => {
                link.addEventListener('click', () => {
                    if (mobileMedia.matches) {
                        cerrarMenu();
                    }
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    cerrarMenu();
                }
            });

            const handleBreakpoint = (event) => {
                if (!event.matches) {
                    cerrarMenu();
                }
            };

            if (typeof mobileMedia.addEventListener === 'function') {
                mobileMedia.addEventListener('change', handleBreakpoint);
            } else {
                mobileMedia.addListener(handleBreakpoint);
            }
        });
    </script>


    {{-- Vite / JavaScript de la aplicación --}}
    @vite('resources/js/app.js')


</body>

</html>