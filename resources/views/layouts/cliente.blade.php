<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Sabor Express')
    </title>

    {{-- Bootstrap --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- CSS CLIENTE --}}
    <link
        rel="stylesheet"
        href="{{ asset('css/cliente.css') }}">

</head>

<body>

    {{-- =====================================================
         NAVBAR CLIENTE
    ====================================================== --}}

    <nav class="cliente-navbar navbar navbar-expand-lg">

        <div class="container-fluid cliente-navbar-container">

            {{-- LOGO --}}
            <a
                href="{{ route('cliente.dashboard.index') }}"
                class="cliente-logo">

                @if(!empty($configuracion?->logo))

                <img
                    src="{{ asset('storage/' . $configuracion->logo) }}"
                    alt="{{ $configuracion->nombre_restaurante ?? 'Restaurante' }}"
                    class="cliente-logo-image">

                @else

                <i class="bi bi-shop"></i>

                @endif

                <span>
                    {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}
                </span>

            </a>


            {{-- BOTÓN RESPONSIVE --}}
            <button
                class="navbar-toggler cliente-navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#clienteNavbar"
                aria-controls="clienteNavbar"
                aria-expanded="false"
                aria-label="Abrir menú">

                <i class="bi bi-list"></i>

            </button>


            {{-- CONTENIDO NAVBAR --}}
            <div
                class="collapse navbar-collapse"
                id="clienteNavbar">

                {{-- ENLACES --}}
                <div class="cliente-navbar-links navbar-nav">

                    <a
                        href="{{ route('cliente.dashboard.index') }}"
                        class="cliente-nav-link nav-link">

                        <i class="bi bi-house"></i>

                        <span>Inicio</span>

                    </a>


                    <a
                        href="{{ route('cliente.productos.index') }}"
                        class="cliente-nav-link nav-link">

                        <i class="bi bi-grid"></i>

                        <span>Productos</span>

                    </a>


                    <a
                        href="{{ route('cliente.carrito.index') }}"
                        class="cliente-nav-link nav-link">

                        <i class="bi bi-cart3"></i>

                        <span>Carrito</span>

                    </a>


                    <a
                        href="{{ route('cliente.pedidos.index') }}"
                        class="cliente-nav-link nav-link">

                        <i class="bi bi-bag-check"></i>

                        <span>Mis pedidos</span>

                    </a>

                </div>


                {{-- USUARIO --}}
                <div class="cliente-navbar-user">

                    @auth

                    <div class="cliente-user-info">
                        <span class="cliente-user-name">
                            <i class="bi bi-person-circle"></i>
                            {{ Auth::user()->name }}
                        </span>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf

                            <button type="submit" class="cliente-btn-logout">
                                <i class="bi bi-box-arrow-right"></i>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>

                    @else

                    <a href="{{ route('login') }}" class="cliente-login-link">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Iniciar sesión
                    </a>

                    @endauth

                </div>

            </div>

        </div>

    </nav>


    {{-- =====================================================
         CONTENIDO
    ====================================================== --}}

    <main class="cliente-main">

        @yield('content')

    </main>


    {{-- =====================================================
         FOOTER
    ====================================================== --}}

    <footer class="cliente-footer">

        <div class="container">

            <p>

                © {{ date('Y') }} Sabor Express.
                Todos los derechos reservados.

            </p>

        </div>

    </footer>


    {{-- Bootstrap JS --}}

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    @yield('scripts')

</body>

</html>