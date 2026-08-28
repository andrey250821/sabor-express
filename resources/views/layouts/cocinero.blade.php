<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}
        - Panel del Cocinero
    </title>

    {{-- Bootstrap --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- CSS COCINERO --}}
    <link
        rel="stylesheet"
        href="{{ asset('css/cocinero.css') }}">

    @stack('styles')

</head>

<body>

    <div class="cocinero-wrapper">


        {{-- =====================================================
         SIDEBAR
    ====================================================== --}}

        <aside class="cocinero-sidebar">


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

                    Panel del Cocinero

                </div>

            </div>


            {{-- SEPARADOR --}}

            <div class="sidebar-divider"></div>


            {{-- =================================================
             MENU DEL COCINERO
        ================================================== --}}

            <nav class="sidebar-menu">


                {{-- Dashboard --}}

                <a
                    href="{{ route('cocinero.dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('cocinero.dashboard') ? 'active' : '' }}">

                    <span class="sidebar-icon">
                        <i class="bi bi-speedometer2"></i>
                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>


                {{-- Pedidos --}}

                <a
                    href="{{ route('cocinero.pedidos.index') }}"
                    class="cocinero-sidebar-link {{ request()->routeIs('cocinero.pedidos.*') ? 'active' : '' }}">

                    <span class="cocinero-sidebar-icon">
                        <i class="bi bi-bag-check"></i>
                    </span>

                    <span>
                        Pedidos
                    </span>

                </a>


                {{-- En preparación --}}

                <a
                    href="#"
                    class="sidebar-link">

                    <span class="sidebar-icon">
                        <i class="bi bi-fire"></i>
                    </span>

                    <span>
                        En preparación
                    </span>

                </a>


                {{-- Listos --}}

                <a
                    href="#"
                    class="sidebar-link">

                    <span class="sidebar-icon">
                        <i class="bi bi-check-circle"></i>
                    </span>

                    <span>
                        Pedidos listos
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
                        Cocinero
                    </span>

                </div>

            </div>


        </aside>


        {{-- =====================================================
         CONTENIDO PRINCIPAL
    ====================================================== --}}

        <main class="cocinero-main">


            {{-- =================================================
             HEADER
        ================================================== --}}

            <header class="cocinero-topbar">


                <div class="topbar-left">

                    <div>

                        <h1 class="topbar-title">

                            Panel del Cocinero

                        </h1>

                        <p class="topbar-subtitle">

                            Gestión y preparación de pedidos

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

                            Cocinero

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

            <section class="cocinero-content">

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