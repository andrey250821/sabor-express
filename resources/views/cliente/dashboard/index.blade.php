@extends('layouts.cliente')

@section('title', 'Inicio - Sabor Express')

@section('content')

<div class="cliente-dashboard container-fluid py-4">

    {{-- ENCABEZADO --}}
    <div class="cliente-dashboard-header">

        <div class="cliente-dashboard-header-content">

            <span class="cliente-dashboard-welcome">
                Bienvenido
            </span>

            <h1 class="cliente-dashboard-title">
                ¡Bienvenido a {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}!
            </h1>

            <p class="cliente-dashboard-subtitle">
                Disfruta nuestros productos y realiza tu pedido
                de forma rápida y sencilla.
            </p>

        </div>


        <div class="cliente-dashboard-header-logo">

            @if(!empty($configuracion?->logo))

            <img
                src="{{ asset('storage/' . $configuracion->logo) }}"
                alt="Logo del restaurante">

            @else

            <i class="bi bi-shop"></i>

            @endif

        </div>

    </div>


    {{-- TARJETAS PRINCIPALES --}}
    <div class="row g-4 mb-4">

        {{-- PRODUCTOS --}}
        <div class="col-12 col-md-6 col-xl-4">

            <a href="{{ route('cliente.productos.index') }}"
                class="cliente-dashboard-card">

                <div class="cliente-card-icon cliente-icon-pink">
                    <i class="bi bi-shop"></i>
                </div>

                <div class="cliente-card-content">

                    <h3>
                        Ver productos
                    </h3>

                    <p>
                        Explora nuestro menú y descubre
                        todos los productos disponibles.
                    </p>

                    <span class="cliente-card-link">
                        Explorar menú
                        <i class="bi bi-arrow-right"></i>
                    </span>

                </div>

            </a>

        </div>


        {{-- CARRITO --}}
        <div class="col-12 col-md-6 col-xl-4">

            <a href="{{ route('cliente.carrito.index') }}"
                class="cliente-dashboard-card">

                <div class="cliente-card-icon cliente-icon-blue">
                    <i class="bi bi-cart3"></i>
                </div>

                <div class="cliente-card-content">

                    <h3>
                        Mi carrito
                    </h3>

                    <p>
                        Revisa tus productos seleccionados
                        antes de realizar tu pedido.
                    </p>

                    <span class="cliente-card-link">
                        Ver carrito
                        <i class="bi bi-arrow-right"></i>
                    </span>

                </div>

            </a>

        </div>


        {{-- PEDIDOS --}}
        <div class="col-12 col-md-6 col-xl-4">

            <a href="{{ route('cliente.pedidos.index') }}"
                class="cliente-dashboard-card">

                <div class="cliente-card-icon cliente-icon-green">
                    <i class="bi bi-receipt"></i>
                </div>

                <div class="cliente-card-content">

                    <h3>
                        Mis pedidos
                    </h3>

                    <p>
                        Consulta tus pedidos anteriores
                        y revisa su estado.
                    </p>

                    <span class="cliente-card-link">
                        Ver pedidos
                        <i class="bi bi-arrow-right"></i>
                    </span>

                </div>

            </a>

        </div>

    </div>


    {{-- SECCIÓN INFORMATIVA --}}
    <div class="row g-4">

        {{-- PROMOCIÓN --}}
        <div class="col-12 col-lg-8">

            <div class="cliente-welcome-panel">

                <div class="cliente-welcome-icon">
                    <i class="bi bi-stars"></i>
                </div>

                <div>

                    <h2>
                        Todo lo que necesitas, en un solo lugar
                    </h2>

                    <p>
                        Elige tus productos favoritos, agrégalos
                        al carrito y realiza tu pedido de manera
                        sencilla y rápida.
                    </p>

                    <a href="{{ route('cliente.productos.index') }}"
                        class="cliente-secondary-btn">

                        <i class="bi bi-arrow-right-circle"></i>

                        Comenzar a comprar

                    </a>

                </div>

            </div>

        </div>


        {{-- INFORMACIÓN --}}
        <div class="col-12 col-lg-4">

            <div class="cliente-info-panel">

                <div class="cliente-info-header">

                    <div class="cliente-info-icon">
                        <i class="bi bi-info-circle"></i>
                    </div>

                    <h3>
                        ¿Cómo comprar?
                    </h3>

                </div>


                <div class="cliente-step">

                    <span>1</span>

                    <p>
                        Explora nuestros productos.
                    </p>

                </div>


                <div class="cliente-step">

                    <span>2</span>

                    <p>
                        Agrega tus productos al carrito.
                    </p>

                </div>


                <div class="cliente-step">

                    <span>3</span>

                    <p>
                        Confirma tu pedido y realiza el pago.
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection