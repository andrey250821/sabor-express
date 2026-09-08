<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Cocina') - Sabor Express
    </title>

    {{-- Bootstrap 5.3.3 --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- CSS general del cocinero --}}
    <link
        rel="stylesheet"
        href="{{ asset('css/cocinero.css') }}">

    @stack('styles')
</head>

<body class="cocinero-layout">

    {{-- =====================================================
         SIDEBAR
    ====================================================== --}}
    <aside class="cocinero-sidebar" id="cocineroSidebar">

        {{-- Logo / identidad del restaurante --}}
        <div class="cocinero-sidebar-header">

            <a href="{{ route('cocinero.dashboard') }}"
                class="cocinero-brand">

                <div class="cocinero-brand-icon">

                    @if(!empty($configuracion->logo))

                    <img
                        src="{{ asset('storage/' . $configuracion->logo) }}"
                        alt="{{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}">

                    @else

                    <i class="bi bi-fire"></i>

                    @endif

                </div>

                <div class="cocinero-brand-text">

                    <strong>
                        {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}
                    </strong>

                    <span>
                        Cocina
                    </span>

                </div>

            </a>

            <button
                type="button"
                class="cocinero-sidebar-close d-lg-none"
                id="cocineroSidebarClose"
                aria-label="Cerrar menú">

                <i class="bi bi-x-lg"></i>

            </button>

        </div>

        {{-- Botón cerrar en móvil --}}
        <button
            type="button"
            class="cocinero-sidebar-close d-lg-none"
            id="cocineroSidebarClose"
            aria-label="Cerrar menú">
            <i class="bi bi-x-lg"></i>
        </button>

        </div>


        {{-- =================================================
             INFORMACIÓN DEL COCINERO
        ================================================== --}}
        <div class="cocinero-profile">

            <div class="cocinero-avatar">
                <i class="bi bi-person-fill"></i>
            </div>

            <div class="cocinero-profile-info">

                <span class="cocinero-profile-name">
                    {{ Auth::user()->nombre ?? Auth::user()->name ?? 'Cocinero' }}
                </span>

                <span class="cocinero-profile-role">
                    <i class="bi bi-circle-fill"></i>
                    Cocina
                </span>

            </div>

        </div>


        {{-- =================================================
             NAVEGACIÓN
        ================================================== --}}
        <nav class="cocinero-nav">

            <div class="cocinero-nav-title">
                PRINCIPAL
            </div>

            {{-- Dashboard --}}
            <a href="{{ route('cocinero.dashboard') }}"
                class="cocinero-nav-link {{ request()->routeIs('cocinero.dashboard') ? 'active' : '' }}">

                <span class="cocinero-nav-icon">
                    <i class="bi bi-grid-1x2-fill"></i>
                </span>

                <span>Dashboard</span>

            </a>


            {{-- Pedidos --}}
            <a href="{{ route('cocinero.pedidos.index') }}"
                class="cocinero-nav-link {{ request()->routeIs('cocinero.pedidos.*') ? 'active' : '' }}">

                <span class="cocinero-nav-icon">
                    <i class="bi bi-bag-fill"></i>
                </span>

                <span>Pedidos</span>

            </a>


            <div class="cocinero-nav-title mt-4">
                CUENTA
            </div>


            {{-- Perfil --}}
            <a href="#"
                class="cocinero-nav-link">

                <span class="cocinero-nav-icon">
                    <i class="bi bi-person-circle"></i>
                </span>

                <span>Mi perfil</span>

            </a>

        </nav>


        {{-- =================================================
             PARTE INFERIOR
        ================================================== --}}
        <div class="cocinero-sidebar-footer">

            <div class="cocinero-status">

                <span class="cocinero-status-dot"></span>

                <div>
                    <strong>Sistema activo</strong>
                    <small>Listo para trabajar</small>
                </div>

            </div>


            {{-- Logout --}}
            <form
                method="POST"
                action="{{ route('logout') }}"
                class="cocinero-logout-form">
                @csrf

                <button type="submit" class="cocinero-logout">

                    <i class="bi bi-box-arrow-right"></i>

                    <span>Cerrar sesión</span>

                </button>

            </form>

        </div>

    </aside>


    {{-- Overlay para móvil --}}
    <div
        class="cocinero-overlay"
        id="cocineroOverlay"></div>


    {{-- =====================================================
         CONTENIDO PRINCIPAL
    ====================================================== --}}
    <div class="cocinero-main">

        {{-- =================================================
             TOPBAR
        ================================================== --}}
        <header class="cocinero-topbar">

            <div class="container-fluid">

                <div class="d-flex align-items-center justify-content-between">

                    {{-- Menú móvil --}}
                    <div class="d-flex align-items-center gap-3">

                        <button
                            type="button"
                            class="cocinero-menu-button d-lg-none"
                            id="cocineroSidebarOpen"
                            aria-label="Abrir menú">
                            <i class="bi bi-list"></i>
                        </button>


                        <div class="cocinero-page-heading">

                            <span>
                                @yield('section', 'Panel de cocina')
                            </span>

                            <h1>
                                @yield('heading', 'Sabor Express')
                            </h1>

                        </div>

                    </div>


                    {{-- Acciones superiores --}}
                    <div class="cocinero-topbar-actions">

                        <div class="cocinero-current-date d-none d-md-flex">

                            <i class="bi bi-calendar3"></i>

                            <span>
                                {{ now()->translatedFormat('d \d\e F, Y') }}
                            </span>

                        </div>


                        <div class="cocinero-topbar-divider d-none d-md-block"></div>


                        <div class="cocinero-topbar-user">

                            <div class="cocinero-topbar-avatar">
                                <i class="bi bi-person-fill"></i>
                            </div>

                            <div class="d-none d-md-block">

                                <strong>
                                    {{ Auth::user()->nombre ?? Auth::user()->name ?? 'Cocinero' }}
                                </strong>

                                <small>
                                    Cocinero
                                </small>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </header>


        {{-- =================================================
             CONTENIDO DE LA VISTA
        ================================================== --}}
        <main class="cocinero-content">

            @if(session('success'))
            <div class="container-fluid">
                <div class="alert alert-success alert-dismissible fade show cocinero-alert"
                    role="alert">

                    <i class="bi bi-check-circle-fill me-2"></i>

                    {{ session('success') }}

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Cerrar"></button>

                </div>
            </div>
            @endif


            @if(session('error'))
            <div class="container-fluid">
                <div class="alert alert-danger alert-dismissible fade show cocinero-alert"
                    role="alert">

                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    {{ session('error') }}

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Cerrar"></button>

                </div>
            </div>
            @endif


            @if($errors->any())
            <div class="container-fluid">

                <div class="alert alert-danger alert-dismissible fade show cocinero-alert"
                    role="alert">

                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    <strong>Hay algunos errores:</strong>

                    <ul class="mb-0 mt-2">

                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Cerrar"></button>

                </div>

            </div>
            @endif


            @yield('content')

        </main>

    </div>


    {{-- Bootstrap JS --}}
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>


    {{-- =====================================================
         SIDEBAR MOBILE
    ====================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const sidebar = document.getElementById('cocineroSidebar');
            const overlay = document.getElementById('cocineroOverlay');

            const openButton = document.getElementById('cocineroSidebarOpen');
            const closeButton = document.getElementById('cocineroSidebarClose');


            function openSidebar() {

                if (!sidebar) return;

                sidebar.classList.add('show');
                overlay?.classList.add('show');

                document.body.classList.add('sidebar-open');
            }


            function closeSidebar() {

                if (!sidebar) return;

                sidebar.classList.remove('show');
                overlay?.classList.remove('show');

                document.body.classList.remove('sidebar-open');
            }


            openButton?.addEventListener('click', openSidebar);

            closeButton?.addEventListener('click', closeSidebar);

            overlay?.addEventListener('click', closeSidebar);


            // Cerrar automáticamente al seleccionar una opción
            document.querySelectorAll('.cocinero-nav-link').forEach(function(link) {

                link.addEventListener('click', function() {

                    if (window.innerWidth < 992) {
                        closeSidebar();
                    }

                });

            });


            // Si cambia a escritorio, limpiamos el estado móvil
            window.addEventListener('resize', function() {

                if (window.innerWidth >= 992) {
                    closeSidebar();
                }

            });

        });
    </script>


    @stack('scripts')

</body>

</html>