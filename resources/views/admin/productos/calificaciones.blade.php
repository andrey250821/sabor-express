@extends('layouts.admin')

@section('title', 'Calificaciones del producto')

@section('content')

<div class="container-fluid admin-calificaciones-container">

    {{-- ENCABEZADO --}}
    <div class="admin-calificaciones-header">

        <div>

            <span class="admin-calificaciones-label">
                SABOR EXPRESS
            </span>

            <h1 class="admin-calificaciones-title">
                Opiniones y calificaciones
            </h1>

            <p class="admin-calificaciones-subtitle">
                Revisa todas las opiniones de los clientes sobre este producto.
            </p>

        </div>

        <a
            href="{{ route('admin.productos.index') }}"
            class="btn btn-outline-light">
            <i class="bi bi-arrow-left me-1"></i>
            Volver a productos
        </a>

    </div>


    {{-- PRODUCTO + RESUMEN --}}
    <div class="row g-4 mb-4">

        {{-- PRODUCTO --}}
        <div class="col-12 col-lg-7">

            <div class="admin-calificaciones-product-card">

                <div class="row g-0 h-100">

                    {{-- IMAGEN --}}
                    <div class="col-12 col-md-5">

                        <div class="admin-calificaciones-product-image">

                            @if($producto->imagen)

                            <img
                                src="{{ asset(
                                        'storage/' . $producto->imagen
                                    ) }}"
                                alt="{{ $producto->nombre }}">

                            @else

                            <div class="admin-calificaciones-no-image">

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

                        <div class="admin-calificaciones-product-info">

                            <span class="admin-calificaciones-category">
                                {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                            </span>


                            <h2 class="admin-calificaciones-product-name">
                                {{ $producto->nombre }}
                            </h2>


                            @if($producto->descripcion)

                            <p class="admin-calificaciones-product-description">
                                {{ $producto->descripcion }}
                            </p>

                            @endif


                            <div class="admin-calificaciones-product-price">

                                Bs.
                                {{ number_format($producto->precio, 2) }}

                            </div>


                            {{-- PROMEDIO --}}
                            <div class="admin-calificaciones-average">

                                <div class="admin-calificaciones-average-number">

                                    {{ $promedio !== null
                                        ? number_format($promedio, 1)
                                        : '0.0' }}

                                </div>

                                <div>

                                    <div class="admin-calificaciones-stars admin-calificaciones-stars-large">

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

                                    <div class="admin-calificaciones-total">

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


        {{-- DISTRIBUCIÓN --}}
        <div class="col-12 col-lg-5">

            <div class="admin-calificaciones-summary-card">

                <div class="admin-calificaciones-summary-body">

                    <div class="admin-calificaciones-section-title">

                        <div>

                            <h2>
                                Resumen
                            </h2>

                            <p>
                                Distribución de las calificaciones
                            </p>

                        </div>

                        <div class="admin-calificaciones-summary-icon">

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

                    <div class="admin-rating-row">

                        <div class="admin-rating-star-number">

                            {{ $estrella }}

                            <i class="bi bi-star-fill"></i>

                        </div>


                        <div class="admin-rating-progress">

                            <div
                                class="admin-rating-progress-bar"
                                style="width: {{ $porcentaje }}%;"></div>

                        </div>


                        <div class="admin-rating-count">

                            {{ $cantidad }}

                        </div>

                    </div>

                    @endfor

                    @else

                    <div class="admin-calificaciones-empty-summary">

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


    {{-- ENCABEZADO OPINIONES --}}
    <div class="admin-calificaciones-reviews-heading">

        <div>

            <span class="admin-calificaciones-label">
                EXPERIENCIAS
            </span>

            <h2>
                Opiniones de nuestros clientes
            </h2>

            <p>
                Comentarios realizados por clientes que probaron este producto.
            </p>

        </div>


        <div class="admin-calificaciones-review-count">

            <i class="bi bi-chat-square-text"></i>

            {{ $totalCalificaciones }}

            {{ $totalCalificaciones == 1
                ? 'opinión'
                : 'opiniones' }}

        </div>

    </div>


    {{-- LISTA DE OPINIONES --}}
    <div class="row">

        <div class="col-12 col-xl-9">

            @forelse($calificaciones as $calificacion)

            <div class="admin-calificaciones-review-card">

                <div class="admin-calificaciones-review-body">

                    {{-- CABECERA --}}
                    <div class="admin-calificaciones-review-header">

                        <div class="admin-calificaciones-user">

                            <div class="admin-calificaciones-avatar">

                                <i class="bi bi-person-fill"></i>

                            </div>

                            <div>

                                <div class="admin-calificaciones-user-name">

                                    {{ $calificacion->user->name ?? 'Cliente' }}

                                </div>

                                <div class="admin-calificaciones-date">

                                    <i class="bi bi-calendar3"></i>

                                    {{ $calificacion->created_at
                                            ? $calificacion->created_at->format('d/m/Y')
                                            : '' }}

                                </div>

                            </div>

                        </div>


                        {{-- ESTRELLAS --}}
                        <div class="admin-calificaciones-stars">

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

                    <div class="admin-calificaciones-comment">

                        <i class="bi bi-quote"></i>

                        <p>
                            {{ $calificacion->comentario }}
                        </p>

                    </div>

                    @else

                    <div class="admin-calificaciones-no-comment">

                        <i class="bi bi-chat-square"></i>

                        <span>
                            Este cliente no dejó un comentario.
                        </span>

                    </div>

                    @endif

                </div>

            </div>

            @empty

            <div class="admin-calificaciones-empty-card">

                <div class="admin-calificaciones-empty-icon">

                    <i class="bi bi-star"></i>

                </div>

                <h3>
                    Este producto aún no tiene calificaciones
                </h3>

                <p>
                    Cuando los clientes califiquen este producto,
                    sus opiniones aparecerán aquí.
                </p>

            </div>

            @endforelse

        </div>

    </div>

</div>

@endsection