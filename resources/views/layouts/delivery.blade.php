<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}
        - Panel del Delivery
    </title>

    {{-- Bootstrap --}}
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

        <aside class="delivery-sidebar">


            {{-- LOGO / IDENTIDAD --}}

            <div class="sidebar-brand">

                <div class="sidebar-logo">

                    @if(!empty($configuracion?->logo))

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

                    Panel del Delivery

                </div>

            </div>


            {{-- SEPARADOR --}}

            <div class="sidebar-divider"></div>


            {{-- =================================================
                MENU DEL DELIVERY
            ================================================== --}}

            <nav class="sidebar-menu">


                {{-- Dashboard --}}

                <a
                    href="{{ route('delivery.dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('delivery.dashboard') ? 'active' : '' }}">

                    <span class="sidebar-icon">

                        <i class="bi bi-speedometer2"></i>

                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>


                {{-- PEDIDOS DISPONIBLES --}}

                <a
                    href="{{ route('delivery.pedidos.index') }}"
                    class="sidebar-link {{ request()->routeIs('delivery.pedidos.index') ? 'active' : '' }}">

                    <span class="sidebar-icon">

                        <i class="bi bi-box-seam"></i>

                    </span>

                    <span>
                        Pedidos disponibles
                    </span>

                </a>


                {{-- MIS PEDIDOS --}}

                <a
                    href="{{ route('delivery.pedidos.mis') }}"
                    class="sidebar-link {{ request()->routeIs('delivery.pedidos.mis') ? 'active' : '' }}">

                    <span class="sidebar-icon">

                        <i class="bi bi-bicycle"></i>

                    </span>

                    <span>
                        Mis pedidos
                    </span>

                </a>


            </nav>


            {{-- =================================================
                INFORMACIÓN DEL USUARIO
            ================================================== --}}

            <div class="sidebar-user">

                <div class="sidebar-user-icon">

                    <i class="bi bi-person-circle"></i>

                </div>

                <div class="sidebar-user-info">

                    <strong>
                        {{ Auth::user()->name }}
                    </strong>

                    <span>
                        Delivery
                    </span>

                </div>

            </div>


        </aside>


        {{-- =====================================================
            CONTENIDO PRINCIPAL
        ====================================================== --}}

        <main class="delivery-main">


            {{-- =================================================
                HEADER
            ================================================== --}}

            <header class="delivery-topbar">


                <div class="topbar-left">

                    <div>

                        <h1 class="topbar-title">

                            Panel del Delivery

                        </h1>

                        <p class="topbar-subtitle">

                            Gestión y entrega de pedidos

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

                            Delivery

                        </span>

                    </div>


                    {{-- CERRAR SESIÓN --}}

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

            <section class="delivery-content">

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