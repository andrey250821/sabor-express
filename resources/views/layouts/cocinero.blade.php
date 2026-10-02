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

    {{-- Identidad visual del panel de Cocina --}}
    <link
        rel="stylesheet"
        href="{{ asset('css/cocinero.css') }}">

    @stack('styles')
</head>

<body class="cocinero-layout">

    <aside class="cocinero-sidebar" id="cocineroSidebar">

        <div class="cocinero-sidebar-header">
            <a
                href="{{ route('cocinero.dashboard') }}"
                class="cocinero-brand">

                <span class="cocinero-brand-icon">
                    @if(!empty($configuracion->logo))
                        <img
                            src="{{ asset('storage/' . $configuracion->logo) }}"
                            alt="{{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}">
                    @else
                        <i class="bi bi-fire"></i>
                    @endif
                </span>

                <span class="cocinero-brand-text">
                    <strong>
                        {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}
                    </strong>
                    <span>Cocina</span>
                </span>
            </a>

            <button
                type="button"
                class="cocinero-sidebar-close d-lg-none"
                id="cocineroSidebarClose"
                aria-label="Cerrar menú">

                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="cocinero-profile">

            <div class="cocinero-avatar">
                @if(Auth::user()->foto_perfil_url)
                    <img
                        src="{{ Auth::user()->foto_perfil_url }}"
                        alt="Foto de {{ Auth::user()->name }}">
                @else
                    <i class="bi bi-person-fill"></i>
                @endif
            </div>

            <div class="cocinero-profile-info">
                <span class="cocinero-profile-name">
                    {{ Auth::user()->name ?? 'Cocinero' }}
                </span>

                <span class="cocinero-profile-role">
                    <i class="bi bi-circle-fill"></i>
                    Cocina
                </span>
            </div>
        </div>

        <nav class="cocinero-nav">

            <div class="cocinero-nav-title">
                PRINCIPAL
            </div>

            <a
                href="{{ route('cocinero.dashboard') }}"
                class="cocinero-nav-link {{ request()->routeIs('cocinero.dashboard') ? 'active' : '' }}">

                <span class="cocinero-nav-icon">
                    <i class="bi bi-grid-1x2-fill"></i>
                </span>

                <span>Dashboard</span>
            </a>

            <a
                href="{{ route('cocinero.pedidos.index') }}"
                class="cocinero-nav-link {{ request()->routeIs('cocinero.pedidos.*') ? 'active' : '' }}">

                <span class="cocinero-nav-icon">
                    <i class="bi bi-bag-fill"></i>
                </span>

                <span>Pedidos</span>
            </a>

            <div class="cocinero-nav-title mt-4">
                CUENTA
            </div>

            <a
                href="{{ route('cocinero.perfil.edit') }}"
                class="cocinero-nav-link {{ request()->routeIs('cocinero.perfil.*') ? 'active' : '' }}">

                <span class="cocinero-nav-icon">
                    <i class="bi bi-person-circle"></i>
                </span>

                <span>Mi perfil</span>
            </a>
        </nav>

        <div class="cocinero-sidebar-footer">

            <div class="cocinero-status {{ Auth::user()->estado === 'activo' ? 'activo' : 'inactivo' }}">

                <div class="cocinero-status-info">

                    <span class="cocinero-status-dot"></span>

                    <div>
                        <strong>
                            {{ Auth::user()->estado === 'activo' ? 'Activo' : 'Inactivo' }}
                        </strong>

                        <small>
                            {{ Auth::user()->estado === 'activo'
                                ? 'Puedes recibir y preparar pedidos'
                                : 'No recibirás nuevos pedidos' }}
                        </small>
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route('cocinero.estado.alternar') }}"
                    class="cocinero-status-form"
                    data-confirm="{{ Auth::user()->estado === 'activo'
                        ? '¿Quieres ponerte inactivo?'
                        : '¿Quieres volver a estar activo?' }}"
                    data-loading-text="Actualizando...">

                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="cocinero-status-toggle {{ Auth::user()->estado === 'activo' ? 'desactivar' : 'activar' }}">

                        <i class="bi {{ Auth::user()->estado === 'activo'
                            ? 'bi-pause-circle-fill'
                            : 'bi-play-circle-fill' }}"></i>

                        {{ Auth::user()->estado === 'activo' ? 'Inactivo' : 'Activo' }}
                    </button>
                </form>
            </div>

            <form
                method="POST"
                action="{{ route('logout') }}"
                class="cocinero-logout-form"
                data-confirm="¿Cerrar sesión de Sabor Express?">

                @csrf

                <button
                    type="submit"
                    class="cocinero-logout">

                    <i class="bi bi-box-arrow-right"></i>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <div
        class="cocinero-overlay"
        id="cocineroOverlay"></div>

    <div class="cocinero-main">

        <header class="cocinero-topbar">

            <div class="container-fluid px-3 px-lg-4">

                <div class="d-flex align-items-center justify-content-between gap-3">

                    <div class="d-flex align-items-center gap-3 min-w-0">

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

                    <div class="cocinero-topbar-actions">

                        <div class="cocinero-current-date d-none d-md-flex">

                            <i class="bi bi-calendar3"></i>

                            <span>
                                {{ now()->locale('es')->translatedFormat('d \d\e F, Y') }}
                            </span>
                        </div>

                        <div class="cocinero-topbar-divider d-none d-md-block"></div>

                        <div class="cocinero-topbar-user">

                            <div class="cocinero-topbar-avatar">
                                @if(Auth::user()->foto_perfil_url)
                                    <img
                                        src="{{ Auth::user()->foto_perfil_url }}"
                                        alt="Foto de {{ Auth::user()->name }}">
                                @else
                                    <i class="bi bi-person-fill"></i>
                                @endif
                            </div>

                            <div class="cocinero-topbar-user-info">

                                <strong>
                                    {{ Auth::user()->name ?? 'Cocinero' }}
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

        <main class="cocinero-content">

            @if(session('success'))
                <div class="container-fluid px-0 mb-3">
                    <div
                        class="alert alert-success alert-dismissible fade show cocinero-alert"
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
                <div class="container-fluid px-0 mb-3">
                    <div
                        class="alert alert-danger alert-dismissible fade show cocinero-alert"
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
                <div class="container-fluid px-0 mb-3">
                    <div
                        class="alert alert-danger alert-dismissible fade show cocinero-alert"
                        role="alert">

                        <strong>
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Revisa los datos ingresados.
                        </strong>

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

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    <script
        src="{{ asset('js/cocinero.js') }}">
    </script>

    @stack('scripts')
</body>

</html>
