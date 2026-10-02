@extends('layouts.cocinero')

@section('title', 'Pedido #' . $pedido->id)
@section('section', 'Gestión de cocina')
@section('heading', 'Pedido #' . $pedido->id)

@section('content')

@php
    $estado = strtolower($pedido->estado ?? '');

    $estadoTexto = match ($estado) {
        'pagado' => 'En cola',
        'preparando' => 'Preparando',
        'listo' => 'Listo',
        'asignado' => 'Asignado',
        'en_camino' => 'En camino',
        'entregado' => 'Entregado',
        'cancelado' => 'Cancelado',
        default => ucfirst(str_replace('_', ' ', $estado ?: 'Sin estado')),
    };

    $estadoClase = match ($estado) {
        'pagado' => 'pendiente',
        'preparando' => 'preparando',
        'listo' => 'listo',
        default => 'otro',
    };

    $estadoIcono = match ($estado) {
        'pagado' => 'bi-clock-fill',
        'preparando' => 'bi-fire',
        'listo' => 'bi-check-circle-fill',
        'asignado' => 'bi-bicycle',
        'en_camino' => 'bi-truck',
        'entregado' => 'bi-check2-all',
        'cancelado' => 'bi-x-circle-fill',
        default => 'bi-info-circle-fill',
    };

    $cliente = $pedido->user->name ?? 'Cliente';
    $detalles = $pedido->detallePedidos ?? collect();
    $cantidadProductos = $detalles->sum('cantidad');
@endphp

<div class="container-fluid cocinero-pedido-show">

    {{-- CABECERA --}}
    <div class="cocinero-detail-header">

        <div>
            <a href="{{ route('cocinero.pedidos.index') }}" class="cocinero-back-link">
                <i class="bi bi-arrow-left"></i>
                Volver a pedidos
            </a>

            <div class="cocinero-detail-title">
                <div class="cocinero-detail-order-icon">
                    <i class="bi bi-bag-check-fill"></i>
                </div>

                <div>
                    <span>Detalle del pedido</span>
                    <h2>Pedido #{{ $pedido->id }}</h2>
                </div>
            </div>
        </div>

        <div class="pedido-detail-status {{ $estadoClase }}">
            <i class="bi {{ $estadoIcono }}"></i>
            <div>
                <small>Estado actual</small>
                <strong>{{ $estadoTexto }}</strong>
            </div>
        </div>

    </div>


    <div class="row g-4">

        {{-- COLUMNA PRINCIPAL --}}
        <div class="col-12 col-xl-8">

            {{-- CLIENTE --}}
            <section class="cocinero-detail-card mb-4">

                <div class="cocinero-detail-card-header">
                    <div>
                        <div class="cocinero-detail-card-icon">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <h3>Cliente</h3>
                            <span>Datos asociados al pedido</span>
                        </div>
                    </div>
                </div>

                <div class="cocinero-client-box">

                    <div class="cocinero-client-avatar">
                        @if($pedido->user?->foto_perfil_url)
                            <img
                                src="{{ $pedido->user->foto_perfil_url }}"
                                alt="Foto de {{ $pedido->user->name }}">
                        @else
                            <i class="bi bi-person-fill"></i>
                        @endif
                    </div>

                    <div class="cocinero-client-info">
                        <strong>{{ $cliente }}</strong>

                        @if($pedido->user?->email)
                            <span>
                                <i class="bi bi-envelope"></i>
                                {{ $pedido->user->email }}
                            </span>
                        @endif

                        @if($pedido->user?->telefono)
                            <span>
                                <i class="bi bi-telephone"></i>
                                {{ $pedido->user->telefono }}
                            </span>
                        @endif
                    </div>

                </div>

            </section>


            {{-- PRODUCTOS --}}
            <section class="cocinero-detail-card mb-4">

                <div class="cocinero-detail-card-header">

                    <div>
                        <div class="cocinero-detail-card-icon">
                            <i class="bi bi-basket-fill"></i>
                        </div>
                        <div>
                            <h3>Productos</h3>
                            <span>Detalle que debes preparar</span>
                        </div>
                    </div>

                    <span class="cocinero-detail-count">
                        {{ $cantidadProductos }}
                        {{ $cantidadProductos == 1 ? 'producto' : 'productos' }}
                    </span>

                </div>

                <div class="cocinero-products-list">

                    @forelse($detalles as $detalle)

                        @php($producto = $detalle->producto)

                        <div class="cocinero-product-item">

                            <div class="cocinero-product-image">
                                @if($producto?->imagen)
                                    <img
                                        src="{{ asset('storage/' . $producto->imagen) }}"
                                        alt="{{ $producto->nombre }}">
                                @else
                                    <i class="bi bi-image"></i>
                                @endif
                            </div>

                            <div class="cocinero-product-info">

                                <h4>{{ $producto->nombre ?? 'Producto' }}</h4>

                                @if($producto?->descripcion)
                                    <p>{{ $producto->descripcion }}</p>
                                @endif

                                <span>
                                    Cantidad:
                                    <strong>{{ $detalle->cantidad }}</strong>
                                </span>

                            </div>

                            <div class="cocinero-product-price">
                                <span>
                                    Bs {{ number_format($detalle->precio ?? 0, 2) }} c/u
                                </span>
                                <strong>
                                    Bs {{ number_format($detalle->subtotal ?? 0, 2) }}
                                </strong>
                            </div>

                        </div>

                    @empty

                        <div class="cocinero-detail-empty">
                            <i class="bi bi-basket"></i>
                            <span>No hay productos registrados para este pedido.</span>
                        </div>

                    @endforelse

                </div>

                <div class="cocinero-detail-total">
                    <span>Total del pedido</span>
                    <strong>Bs {{ number_format($pedido->total ?? 0, 2) }}</strong>
                </div>

            </section>


            {{-- OBSERVACIÓN --}}
            @if($pedido->observacion_cliente)

                <section class="cocinero-detail-card mb-4">

                    <div class="cocinero-detail-card-header">
                        <div>
                            <div class="cocinero-detail-card-icon warning">
                                <i class="bi bi-chat-left-text-fill"></i>
                            </div>
                            <div>
                                <h3>Indicaciones especiales</h3>
                                <span>Solicitud escrita por el cliente</span>
                            </div>
                        </div>
                    </div>

                    <div class="cocinero-observation">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <div>
                            <strong>Observación</strong>
                            <p>{{ $pedido->observacion_cliente }}</p>
                        </div>
                    </div>

                </section>

            @endif

        </div>


        {{-- COLUMNA LATERAL --}}
        <div class="col-12 col-xl-4">

            {{-- ACCIONES --}}
            <section class="cocinero-detail-card mb-4">

                <div class="cocinero-detail-card-header">
                    <div>
                        <div class="cocinero-detail-card-icon">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <div>
                            <h3>Acción de cocina</h3>
                            <span>Solo gestiona tu etapa de preparación</span>
                        </div>
                    </div>
                </div>

                <div class="cocinero-detail-actions">

                    @if($estado === 'pagado' && $pedido->cocinero_id === null)

                        <div class="cocinero-action-info pendiente">
                            <i class="bi bi-clock-history"></i>
                            <div>
                                <strong>Pedido disponible</strong>
                                <span>
                                    Está en la cola de preparación. Al iniciar,
                                    quedará bajo tu responsabilidad.
                                </span>
                            </div>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('cocinero.pedidos.preparar', $pedido->id) }}"
                            data-confirm="¿Comenzar a preparar el pedido #{{ $pedido->id }}? Se retirará de la cola para los demás cocineros."
                            data-loading>
                            @csrf
                            @method('PUT')

                            <button type="submit" class="btn cocinero-action-btn preparar">
                                <i class="bi bi-fire"></i>
                                Preparar pedido
                            </button>
                        </form>

                    @elseif($estado === 'preparando' && (int) $pedido->cocinero_id === (int) auth()->id())

                        <div class="cocinero-action-info preparando">
                            <i class="bi bi-fire"></i>
                            <div>
                                <strong>Pedido en preparación</strong>
                                <span>
                                    Continúa con la preparación y confirma cuando hayas terminado.
                                </span>
                            </div>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('cocinero.pedidos.listo', $pedido->id) }}"
                            data-confirm="¿Marcar el pedido #{{ $pedido->id }} como listo?"
                            data-loading>
                            @csrf
                            @method('PUT')

                            <button type="submit" class="btn cocinero-action-btn listo">
                                <i class="bi bi-check-lg"></i>
                                Marcar como listo
                            </button>
                        </form>

                    @elseif($estado === 'listo' && (int) $pedido->cocinero_id === (int) auth()->id())

                        <div class="cocinero-action-info listo">
                            <i class="bi bi-check-circle-fill"></i>
                            <div>
                                <strong>Pedido listo</strong>
                                <span>
                                    Cocina terminó este pedido. El siguiente paso
                                    del flujo corresponde a Delivery.
                                </span>
                            </div>
                        </div>

                    @else

                        <div class="cocinero-action-info">
                            <i class="bi bi-info-circle-fill"></i>
                            <div>
                                <strong>Sin acciones disponibles</strong>
                                <span>
                                    Este pedido ya no puede modificarse desde esta cuenta
                                    en su estado actual.
                                </span>
                            </div>
                        </div>

                    @endif

                </div>

            </section>


            {{-- ENTREGA --}}
            <section class="cocinero-detail-card mb-4">

                <div class="cocinero-detail-card-header">
                    <div>
                        <div class="cocinero-detail-card-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <h3>Información de entrega</h3>
                            <span>Datos útiles para el despacho</span>
                        </div>
                    </div>
                </div>

                <div class="cocinero-delivery-info">

                    @if($pedido->direccion_entrega)
                        <div class="cocinero-info-row">
                            <div class="cocinero-info-row-icon">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div>
                                <small>Dirección</small>
                                <strong>{{ $pedido->direccion_entrega }}</strong>
                            </div>
                        </div>
                    @endif

                    @if($pedido->referencia_delivery)
                        <div class="cocinero-info-row">
                            <div class="cocinero-info-row-icon">
                                <i class="bi bi-signpost-2-fill"></i>
                            </div>
                            <div>
                                <small>Referencia</small>
                                <strong>{{ $pedido->referencia_delivery }}</strong>
                            </div>
                        </div>
                    @endif

                    @if($pedido->latitud && $pedido->longitud)
                        <div class="cocinero-coordinates">
                            <i class="bi bi-pin-map-fill"></i>
                            <div>
                                <small>Ubicación registrada</small>
                                <span>
                                    {{ $pedido->latitud }}, {{ $pedido->longitud }}
                                </span>
                            </div>
                        </div>
                    @endif

                </div>

            </section>


            {{-- INFORMACIÓN --}}
            <section class="cocinero-detail-card">

                <div class="cocinero-detail-card-header">
                    <div>
                        <div class="cocinero-detail-card-icon">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>
                        <div>
                            <h3>Información</h3>
                            <span>Datos básicos del pedido</span>
                        </div>
                    </div>
                </div>

                <div class="cocinero-order-data">

                    <div>
                        <span>Número</span>
                        <strong>#{{ $pedido->id }}</strong>
                    </div>

                    <div>
                        <span>Fecha</span>
                        <strong>{{ $pedido->created_at?->format('d/m/Y') ?? 'No disponible' }}</strong>
                    </div>

                    <div>
                        <span>Hora</span>
                        <strong>{{ $pedido->created_at?->format('H:i') ?? 'No disponible' }}</strong>
                    </div>

                    <div>
                        <span>Estado</span>
                        <strong class="pedido-status {{ $estadoClase }}">
                            <i class="bi {{ $estadoIcono }}"></i>
                            {{ $estadoTexto }}
                        </strong>
                    </div>

                </div>

            </section>

        </div>

    </div>

</div>

@endsection
