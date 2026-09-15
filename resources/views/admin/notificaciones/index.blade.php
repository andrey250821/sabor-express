@extends('layouts.admin')

@section('title', 'Notificaciones')

@section('content')

<div class="container-fluid notificaciones-page">

    {{-- ENCABEZADO --}}
    <div class="notificaciones-header">

        <div class="d-flex flex-column flex-md-row
                    justify-content-between
                    align-items-md-center
                    gap-3">

            <div>
                <h2 class="notificaciones-title">
                    <i class="bi bi-bell-fill me-2"></i>
                    Notificaciones
                </h2>

                <p class="notificaciones-subtitle">
                    Comprobantes de pago y nuevas calificaciones
                    recibidas por Sabor Express.
                </p>
            </div>

            @if($noLeidas > 0)

            <form
                action="{{ route('admin.notificaciones.leer.todas') }}"
                method="POST">
                @csrf
                @method('PATCH')

                <button
                    type="submit"
                    class="btn btn-outline-light btn-marcar-todas">
                    <i class="bi bi-check2-all me-1"></i>
                    Marcar todas como leídas
                </button>
            </form>

            @endif

        </div>

    </div>


    {{-- RESUMEN --}}
    <div class="row g-3 mb-4">

        {{-- TOTAL --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="notificacion-resumen">

                <div class="resumen-icon total">
                    <i class="bi bi-bell-fill"></i>
                </div>

                <div>
                    <span class="resumen-label">
                        Total
                    </span>

                    <span class="resumen-numero">
                        {{ $notificaciones->count() }}
                    </span>
                </div>

            </div>

        </div>


        {{-- NO LEÍDAS --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="notificacion-resumen">

                <div class="resumen-icon nuevas">
                    <i class="bi bi-envelope-fill"></i>
                </div>

                <div>
                    <span class="resumen-label">
                        No leídas
                    </span>

                    <span class="resumen-numero">
                        {{ $noLeidas }}
                    </span>
                </div>

            </div>

        </div>


        {{-- COMPROBANTES --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="notificacion-resumen">

                <div class="resumen-icon comprobantes">
                    <i class="bi bi-receipt"></i>
                </div>

                <div>
                    <span class="resumen-label">
                        Comprobantes
                    </span>

                    <span class="resumen-numero">
                        {{ $comprobantes }}
                    </span>
                </div>

            </div>

        </div>


        {{-- CALIFICACIONES --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="notificacion-resumen">

                <div class="resumen-icon calificaciones">
                    <i class="bi bi-star-fill"></i>
                </div>

                <div>
                    <span class="resumen-label">
                        Calificaciones
                    </span>

                    <span class="resumen-numero">
                        {{ $calificaciones }}
                    </span>
                </div>

            </div>

        </div>

    </div>


    {{-- NOTIFICACIONES AGRUPADAS POR FECHA --}}
    @forelse($notificacionesAgrupadas as $fecha => $grupo)

    <div class="fecha-grupo">

        {{-- CABECERA DE FECHA --}}
        <div class="fecha-header">

            <div class="fecha-header-icon">
                <i class="bi bi-calendar3"></i>
            </div>

            <span class="fecha-header-title">
                {{ $fecha }}
            </span>

            <div class="fecha-header-line"></div>

        </div>


        {{-- NOTIFICACIONES DEL DÍA --}}
        @foreach($grupo as $notificacion)

        @php

        $esComprobante =
        $notificacion->evento === 'comprobante_enviado';

        $esCalificacion =
        $notificacion->evento === 'nueva_calificacion';

        $pedido = $notificacion->pedido;

        $cliente = $pedido?->user;

        $comprobante = $pedido?->comprobantePago;

        /*
        |--------------------------------------------------------------------------
        | CALIFICACIÓN
        |--------------------------------------------------------------------------
        | La notificación guarda pedido_id.
        | Se obtiene la calificación más reciente de ese pedido.
        */

        $calificacion = $notificacion->calificacionRelacionada;

        $producto = $calificacion?->producto;

        @endphp


        <div class="notificacion-card
                    {{ !$notificacion->leido ? 'no-leida' : 'leida' }}">

            <div class="notificacion-body">

                <div class="d-flex align-items-start gap-3">


                    {{-- ICONO --}}
                    <div class="notificacion-icon
                                {{ $esComprobante
                                    ? 'icon-comprobante'
                                    : 'icon-calificacion' }}">

                        @if($esComprobante)

                        <i class="bi bi-receipt"></i>

                        @elseif($esCalificacion)

                        <i class="bi bi-star-fill"></i>

                        @endif

                    </div>


                    {{-- CONTENIDO --}}
                    <div class="flex-grow-1">


                        {{-- TÍTULO --}}
                        <div class="d-flex
                                            flex-wrap
                                            align-items-center
                                            gap-2
                                            mb-1">

                            <h5 class="notificacion-titulo mb-0">

                                @if($esComprobante)

                                Comprobante de pago recibido

                                @elseif($esCalificacion)

                                Nueva calificación recibida

                                @endif

                            </h5>


                            @if(!$notificacion->leido)

                            <span class="badge-nueva">
                                Nueva
                            </span>

                            @endif


                            @if($esComprobante)

                            <span class="badge-tipo
                                            badge-tipo-comprobante">
                                Comprobante
                            </span>

                            @elseif($esCalificacion)

                            <span class="badge-tipo
                                            badge-tipo-calificacion">
                                Calificación
                            </span>

                            @endif

                        </div>


                        {{-- COMPROBANTE --}}
                        @if($esComprobante)

                        <p class="notificacion-descripcion">

                            <strong class="text-white">
                                {{ $cliente?->name ?? 'Cliente no disponible' }}
                            </strong>

                            envió un comprobante de pago
                            para el pedido

                            <strong class="text-white">
                                #{{ $pedido?->id ?? $notificacion->pedido_id }}
                            </strong>.

                        </p>


                        <div class="notificacion-info">

                            <div class="info-item">

                                <i class="bi bi-person-fill"></i>

                                Cliente:

                                <strong>
                                    {{ $cliente?->name ?? 'No disponible' }}
                                </strong>

                            </div>


                            <div class="info-item">

                                <i class="bi bi-bag-fill"></i>

                                Pedido:

                                <strong>
                                    #{{ $pedido?->id ?? $notificacion->pedido_id }}
                                </strong>

                            </div>


                            @if($comprobante)

                            <div class="info-item">

                                <i class="bi bi-file-earmark-check-fill"></i>

                                Estado:

                                <strong>
                                    {{ ucfirst($comprobante->estado) }}
                                </strong>

                            </div>

                            @endif

                        </div>


                        {{-- CALIFICACIÓN --}}
                        @elseif($esCalificacion)

                        <p class="notificacion-descripcion">

                            <strong class="text-white">
                                {{ $calificacion?->user?->name
                                                ?? $cliente?->name
                                                ?? 'Cliente no disponible' }}
                            </strong>

                            calificó el producto

                            <strong class="text-white">
                                {{ $producto?->nombre
                                                ?? 'Producto no disponible' }}
                            </strong>.

                        </p>


                        <div class="notificacion-info">

                            <div class="info-item">

                                <i class="bi bi-person-fill"></i>

                                Cliente:

                                <strong>
                                    {{ $calificacion?->user?->name
                                                    ?? $cliente?->name
                                                    ?? 'No disponible' }}
                                </strong>

                            </div>


                            @if($producto)

                            <div class="info-item">

                                <i class="bi bi-box-seam-fill"></i>

                                Producto:

                                <strong>
                                    {{ $producto->nombre }}
                                </strong>

                            </div>

                            @endif


                            @if($calificacion)

                            <div class="info-item">

                                <span class="estrellas">

                                    @for($i = 1; $i <= 5; $i++)

                                        @if($i <=$calificacion->puntuacion)
                                        ★
                                        @else
                                        ☆
                                        @endif

                                        @endfor

                                </span>

                                <span class="puntuacion-numero">
                                    {{ $calificacion->puntuacion }}/5
                                </span>

                            </div>

                            @endif

                        </div>


                        {{-- COMENTARIO --}}
                        @if($calificacion?->comentario)

                        <div class="comentario-box">

                            <i class="bi bi-chat-left-quote-fill"></i>

                            "{{ $calificacion->comentario }}"

                        </div>

                        @endif

                        @endif


                        {{-- FECHA Y HORA --}}
                        <div class="notificacion-fecha mt-3">

                            <i class="bi bi-clock"></i>

                            {{ $notificacion->created_at->format('H:i') }}

                            <span>•</span>

                            {{ $notificacion->created_at->format('d/m/Y') }}

                        </div>


                        {{-- ACCIONES --}}
                        <div class="notificacion-acciones">


                            {{-- MARCAR LEÍDA --}}
                            @if(!$notificacion->leido)

                            <form
                                action="{{ route(
                                                'admin.notificaciones.leer',
                                                $notificacion->id
                                            ) }}"
                                method="POST">

                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="btn btn-outline-secondary">
                                    <i class="bi bi-check2 me-1"></i>
                                    Marcar como leída
                                </button>

                            </form>

                            @endif


                            {{-- VER COMPROBANTE --}}
                            @if($esComprobante && $comprobante)

                            <a
                                href="{{ route(
            'admin.comprobantes.index',
            ['estado' => 'pendiente']
        ) }}"
                                class="btn btn-outline-primary">
                                <i class="bi bi-receipt me-1"></i>
                                Ver comprobante
                            </a>

                            @endif


                            {{-- VER PEDIDO --}}
                            @if(
                            $esComprobante &&
                            $pedido
                            )

                            <a
                                href="{{ route(
                                                'admin.pedidos.show',
                                                $pedido->id
                                            ) }}"
                                class="btn btn-outline-light">
                                <i class="bi bi-bag me-1"></i>
                                Ver pedido
                            </a>

                            @endif


                            {{-- VER PRODUCTO / CALIFICACIONES --}}
                            @if(
                            $esCalificacion &&
                            $producto
                            )

                            <a
                                href="{{ route(
                                                'admin.productos.calificaciones',
                                                $producto->id
                                            ) }}"
                                class="btn btn-outline-warning">
                                <i class="bi bi-star me-1"></i>
                                Ver producto y calificaciones
                            </a>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>

        @endforeach

    </div>

    @empty

    {{-- SIN NOTIFICACIONES --}}
    <div class="sin-notificaciones">

        <div class="sin-notificaciones-icon">
            <i class="bi bi-bell-slash"></i>
        </div>

        <h4>
            No tienes notificaciones
        </h4>

        <p class="mb-0">
            Aquí aparecerán los comprobantes enviados
            y las nuevas calificaciones.
        </p>

    </div>

    @endforelse

</div>

@endsection