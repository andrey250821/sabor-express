@extends('layouts.cliente')

@section('content')

<div class="cliente-calificaciones-container">

    {{-- =====================================================
         ENCABEZADO
    ====================================================== --}}
    <div class="cliente-calificaciones-header">

        <div class="cliente-calificaciones-header-info">

            <span class="cliente-calificaciones-label">
                SABOR EXPRESS
            </span>

            <h1 class="cliente-calificaciones-title">
                Opiniones y calificaciones
            </h1>

            <p class="cliente-calificaciones-subtitle">
                Conoce lo que nuestros clientes piensan de este producto.
            </p>

        </div>

        <a
            href="{{ route('cliente.productos.index') }}"
            class="cliente-calificaciones-btn-back">

            <i class="bi bi-arrow-left"></i>

            <span>
                Volver a productos
            </span>

        </a>

    </div>


    {{-- =====================================================
         PRODUCTO + RESUMEN
    ====================================================== --}}
    <div class="row g-4 mb-4">

        {{-- =================================================
             INFORMACIÓN DEL PRODUCTO
        ================================================== --}}
        <div class="col-12 col-lg-7">

            <div class="cliente-calificaciones-product-card">

                <div class="row g-0 h-100">

                    {{-- IMAGEN --}}
                    <div class="col-12 col-md-5">

                        <div class="cliente-calificaciones-product-image">

                            @if($producto->imagen)

                            <img
                                src="{{ asset('storage/' . $producto->imagen) }}"
                                alt="{{ $producto->nombre }}">

                            @else

                            <div class="cliente-calificaciones-no-image">

                                <i class="bi bi-image"></i>

                                <span>
                                    Sin imagen
                                </span>

                            </div>

                            @endif

                        </div>

                    </div>


                    {{-- INFORMACIÓN --}}
                    <div class="col-12 col-md-7">

                        <div class="cliente-calificaciones-product-info">

                            {{-- CATEGORÍA --}}
                            <span class="cliente-calificaciones-category">

                                {{ $producto->categoria->nombre ?? 'Sin categoría' }}

                            </span>


                            {{-- NOMBRE --}}
                            <h2 class="cliente-calificaciones-product-name">

                                {{ $producto->nombre }}

                            </h2>


                            {{-- DESCRIPCIÓN --}}
                            @if($producto->descripcion)

                            <p class="cliente-calificaciones-product-description">

                                {{ $producto->descripcion }}

                            </p>

                            @endif


                            {{-- PRECIO --}}
                            <div class="cliente-calificaciones-product-price">

                                Bs.
                                {{ number_format($producto->precio, 2) }}

                            </div>


                            {{-- PROMEDIO --}}
                            <div class="cliente-calificaciones-average">

                                <div class="cliente-calificaciones-average-number">

                                    {{ $promedio !== null
                                        ? number_format($promedio, 1)
                                        : '0.0' }}

                                </div>

                                <div>

                                    <div class="cliente-calificaciones-stars cliente-calificaciones-stars-large">

                                        @for($i = 1; $i <= 5; $i++)

                                            @if($promedio>= $i)

                                            <i class="bi bi-star-fill"></i>

                                            @elseif($promedio >= ($i - 0.5))

                                            <i class="bi bi-star-half"></i>

                                            @else

                                            <i class="bi bi-star"></i>

                                            @endif

                                            @endfor

                                    </div>

                                    <div class="cliente-calificaciones-total">

                                        {{ $totalCalificaciones }}

                                        {{ $totalCalificaciones == 1
                                            ? 'calificación'
                                            : 'calificaciones' }}

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =================================================
             DISTRIBUCIÓN
        ================================================== --}}
        <div class="col-12 col-lg-5">

            <div class="cliente-calificaciones-summary-card">

                <div class="cliente-calificaciones-summary-body">

                    <div class="cliente-calificaciones-section-title">

                        <div>

                            <h2>
                                Resumen
                            </h2>

                            <p>
                                Distribución de las calificaciones
                            </p>

                        </div>

                        <div class="cliente-calificaciones-summary-icon">

                            <i class="bi bi-bar-chart-fill"></i>

                        </div>

                    </div>


                    @if($totalCalificaciones > 0)

                    @for($estrella = 5; $estrella >= 1; $estrella--)

                    @php

                    $cantidad =
                    $cantidadEstrellas[$estrella];

                    $porcentaje =
                    ($cantidad / $totalCalificaciones) * 100;

                    @endphp

                    <div class="cliente-rating-row">

                        <div class="cliente-rating-star-number">

                            {{ $estrella }}

                            <i class="bi bi-star-fill"></i>

                        </div>


                        <div class="cliente-rating-progress">

                            <div
                                class="cliente-rating-progress-bar"
                                style="width: {{ $porcentaje }}%;">
                            </div>

                        </div>


                        <div class="cliente-rating-count">

                            {{ $cantidad }}

                        </div>

                    </div>

                    @endfor

                    @else

                    <div class="cliente-calificaciones-empty-summary">

                        <i class="bi bi-star"></i>

                        <p>
                            Todavía no existen calificaciones.
                        </p>

                    </div>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         ENCABEZADO OPINIONES
    ====================================================== --}}
    <div class="cliente-calificaciones-reviews-heading">

        <div>

            <span class="cliente-calificaciones-label">
                EXPERIENCIAS
            </span>

            <h2>
                Opiniones de nuestros clientes
            </h2>

            <p>
                Comentarios realizados por clientes que probaron este producto.
            </p>

        </div>

        <div class="cliente-calificaciones-review-count">

            <i class="bi bi-chat-square-text"></i>

            {{ $totalCalificaciones }}

            {{ $totalCalificaciones == 1
                ? 'opinión'
                : 'opiniones' }}

        </div>

    </div>


    {{-- =====================================================
         LISTA DE OPINIONES
    ====================================================== --}}
    <div class="row">

        <div class="col-12 col-xl-9">

            @forelse($calificaciones as $calificacion)

            <div class="cliente-calificaciones-review-card">

                <div class="cliente-calificaciones-review-body">

                    {{-- CABECERA --}}
                    <div class="cliente-calificaciones-review-header">

                        <div class="cliente-calificaciones-user">

                            <div class="cliente-calificaciones-avatar">

                                <i class="bi bi-person-fill"></i>

                            </div>

                            <div>

                                <div class="cliente-calificaciones-user-name">

                                    {{ $calificacion->user->name ?? 'Cliente' }}

                                </div>

                                <div class="cliente-calificaciones-date">

                                    <i class="bi bi-calendar3"></i>

                                    {{ $calificacion->created_at
                                            ? $calificacion->created_at->format('d/m/Y')
                                            : '' }}

                                </div>

                            </div>

                        </div>


                        {{-- ESTRELLAS --}}
                        <div class="cliente-calificaciones-stars">

                            @for($i = 1; $i <= 5; $i++)

                                @if($i <=$calificacion->puntuacion)

                                <i class="bi bi-star-fill"></i>

                                @else

                                <i class="bi bi-star"></i>

                                @endif

                                @endfor

                        </div>

                    </div>


                    {{-- COMENTARIO --}}
                    @if($calificacion->comentario)

                    <div class="cliente-calificaciones-comment">

                        <i class="bi bi-quote"></i>

                        <p>
                            {{ $calificacion->comentario }}
                        </p>

                    </div>

                    @else

                    <div class="cliente-calificaciones-no-comment">

                        <i class="bi bi-chat-square"></i>

                        <span>
                            Este cliente no dejó un comentario.
                        </span>

                    </div>

                    @endif

                </div>

            </div>

            @empty

            {{-- SIN CALIFICACIONES --}}
            <div class="cliente-calificaciones-empty-card">

                <div class="cliente-calificaciones-empty-icon">

                    <i class="bi bi-star"></i>

                </div>

                <h3>
                    Este producto aún no tiene calificaciones
                </h3>

                <p>
                    Sé el primero en compartir tu experiencia
                    con este producto.
                </p>

                <a
                    href="{{ route('cliente.productos.index') }}"
                    class="cliente-calificaciones-btn-primary">

                    <i class="bi bi-arrow-left"></i>

                    Explorar productos

                </a>

            </div>

            @endforelse

        </div>

    </div>

</div>

@endsection