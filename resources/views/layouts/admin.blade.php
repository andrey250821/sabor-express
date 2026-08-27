<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

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

    <aside class="admin-sidebar">


        {{-- LOGO / IDENTIDAD --}}
        <div class="sidebar-brand">


            <div class="sidebar-logo">


                @if(!empty($configuracion->logo))

                    <img
                        src="{{ asset('storage/' . $configuracion->logo) }}"
                        alt="{{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}">

                @else

                    <span>
                        🍔
                    </span>

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



        {{-- MENU --}}
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



            {{-- Calificaciones --}}
            <a
                href="#"
                class="sidebar-link">

                <span class="sidebar-icon">
                    <i class="bi bi-star"></i>
                </span>

                <span>
                    Calificaciones
                </span>

            </a>



            {{-- Notificaciones --}}
            <a
                href="#"
                class="sidebar-link">

                <span class="sidebar-icon">
                    <i class="bi bi-bell"></i>
                </span>

                <span>
                    Notificaciones
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



    {{-- =====================================================
        CONTENIDO PRINCIPAL
    ====================================================== --}}

    <main class="admin-main">


        {{-- =================================================
            HEADER
        ================================================== --}}

        <header class="admin-topbar">


            <div class="topbar-left">


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
            CONTENIDO
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


@stack('scripts')


</body>

</html>