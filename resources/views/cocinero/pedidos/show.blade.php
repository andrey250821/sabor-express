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
        default => ucfirst($estado ?: 'Sin estado'),
    };

    $estadoClase = match ($estado) {
        'pagado' => 'pendiente',
        'preparando' => 'preparando',
        'listo', 'asignado', 'entregado' => 'listo',
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

<div class="container-fluid px-0 cocinero-pedido-show">

    {{-- CABECERA --}}
    <div class="cocinero-detail-header cocinero-reveal">

        <div>

            <a
                href="{{ route('cocinero.pedidos.index') }}"
                class="cocinero-back-link">

                <i class="bi bi-arrow-left"></i>
                Volver a pedidos
            </a>

            <div class="cocinero-detail-title">

                <div class="cocinero-detail-order-icon">
                    <i class="bi bi-bag-check-fill"></i>
                </div>

                <div>
                    <span>Detalle del pedido</span>

                    <h2>
                        #{{ $pedido->id }}
                    </h2>
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
            <section class="cocinero-detail-card mb-4 cocinero-reveal">

                <div class="cocinero-detail-card-header">

                    <div>
                        <div class="cocinero-detail-card-icon">
                            <i class="bi bi-person-fill"></i>
                        </div>

                        <div>
                            <h3>Cliente</h3>
                            <span>Información del cliente</span>
                        </div>
                    </div>
                </div>

                <div class="cocinero-client-box">

                    <div class="cocinero-client-avatar">

                        @if($pedido->user?->foto_perfil_url)
                            <img
                                src="{{ $pedido->user->foto_perfil_url }}"
                                alt="Foto de {{ $cliente }}">
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
            <section class="cocinero-detail-card mb-4 cocinero-reveal">

                <div class="cocinero-detail-card-header">

                    <div>
                        <div class="cocinero-detail-card-icon">
                            <i class="bi bi-basket-fill"></i>
                        </div>

                        <div>
                            <h3>Productos</h3>
                            <span>Productos incluidos en el pedido</span>
                        </div>
                    </div>

                    <span class="cocinero-detail-count">
                        {{ $cantidadProductos }}
                        {{ $cantidadProductos === 1 ? 'unidad' : 'unidades' }}
                    </span>
                </div>

                <div class="cocinero-products-list">

                    @forelse($detalles as $detalle)

                        @php
                            $producto = $detalle->producto;
                        @endphp

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

                                <h4>
                                    {{ $producto->nombre ?? 'Producto' }}
                                </h4>

                                @if($producto?->descripcion)
                                    <p>
                                        {{ $producto->descripcion }}
                                    </p>
                                @endif

                                <span>
                                    Cantidad:
                                    <strong>{{ $detalle->cantidad }}</strong>
                                </span>
                            </div>

                            <div class="cocinero-product-price">

                                <span>
                                    Bs {{ number_format($detalle->precio ?? 0, 2) }}
                                </span>

                                <strong>
                                    Bs {{ number_format($detalle->subtotal ?? 0, 2) }}
                                </strong>
                            </div>
                        </div>

                    @empty

                        <div class="cocinero-detail-empty">
                            <i class="bi bi-basket"></i>
                            <span>No hay productos registrados.</span>
                        </div>

                    @endforelse

                </div>

                <div class="cocinero-detail-total">

                    <span>Total del pedido</span>

                    <strong>
                        Bs {{ number_format($pedido->total ?? 0, 2) }}
                    </strong>
                </div>
            </section>

        {{-- OBSERVACIONES PRIORITARIAS --}}
        <section class="cocinero-observation-card mb-4 cocinero-reveal">

            <div class="cocinero-observation-card-top">
                <div class="cocinero-observation-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>

                <div>
                    <span class="cocinero-observation-kicker">
                        <i class="bi bi-eye-fill"></i>
                        Prioridad de cocina
                    </span>

                    <h3>
                        Observaciones del cliente
                    </h3>

                    <p>
                        Revisa estas indicaciones antes de preparar el pedido.
                    </p>
                </div>

                <div class="cocinero-observation-badge">
                    <i class="bi bi-lightning-charge-fill"></i>
                    Revisar
                </div>
            </div>

            <div class="cocinero-observation-content">
                <i class="bi bi-chat-left-quote-fill"></i>

                <div>
                    <strong>
                        {{ $pedido->observacion_cliente ? 'Indicaciones especiales' : 'Sin indicaciones especiales' }}
                    </strong>

                    <p>
                        {{ $pedido->observacion_cliente ?: 'El cliente no registró observaciones para la preparación.' }}
                    </p>
                </div>
            </div>
        </section>

        </div>

        {{-- COLUMNA LATERAL --}}
        <div class="col-12 col-xl-4">

            {{-- ACCIÓN DE COCINA --}}
            <section class="cocinero-detail-card mb-4 cocinero-reveal">

                <div class="cocinero-detail-card-header">

                    <div>
                        <div class="cocinero-detail-card-icon">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>

                        <div>
                            <h3>Acción de cocina</h3>
                            <span>Solo cambia el estado de preparación</span>
                        </div>
                    </div>
                </div>

                <div class="cocinero-detail-actions">

                    @if($estado === 'pagado' && $pedido->cocinero_id === null)

                        <div class="cocinero-action-info pendiente">

                            <i class="bi bi-hourglass-split"></i>

                            <div>
                                <strong>Pedido disponible</strong>

                                <span>
                                    Está en la cola de preparación y respeta el orden de llegada.
                                </span>
                            </div>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('cocinero.pedidos.preparar', $pedido->id) }}"
                            data-confirm="¿Comenzar a preparar el pedido #{{ $pedido->id }}?"
                            data-loading-text="Preparando...">

                            @csrf
                            @method('PUT')

                            <button
                                type="submit"
                                class="btn cocinero-action-btn preparar js-ripple">

                                <i class="bi bi-fire me-1"></i>
                                Preparar pedido
                            </button>
                        </form>

                    @elseif($estado === 'preparando' &&
                        (int) $pedido->cocinero_id === (int) auth()->id())

                        <div class="cocinero-action-info preparando">

                            <i class="bi bi-fire"></i>

                            <div>
                                <strong>Pedido en preparación</strong>

                                <span>
                                    Este pedido está siendo preparado por ti.
                                    Cuando termines, márcalo como listo.
                                </span>
                            </div>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('cocinero.pedidos.listo', $pedido->id) }}"
                            data-confirm="¿Marcar el pedido #{{ $pedido->id }} como listo?"
                            data-loading-text="Finalizando...">

                            @csrf
                            @method('PUT')

                            <button
                                type="submit"
                                class="btn cocinero-action-btn listo js-ripple">

                                <i class="bi bi-check-lg me-1"></i>
                                Marcar como listo
                            </button>
                        </form>

                    @elseif($estado === 'listo' &&
                        (int) $pedido->cocinero_id === (int) auth()->id())

                        <div class="cocinero-action-info listo">

                            <i class="bi bi-check-circle-fill"></i>

                            <div>
                                <strong>Pedido terminado</strong>

                                <span>
                                    La cocina terminó este pedido.
                                    El sistema continúa con el siguiente paso automáticamente.
                                </span>
                            </div>
                        </div>

                    @else

                        <div class="cocinero-action-info">

                            <i class="bi bi-info-circle-fill"></i>

                            <div>
                                <strong>Sin acciones disponibles</strong>

                                <span>
                                    Este pedido ya está en una etapa posterior
                                    o pertenece a otro cocinero.
                                </span>
                            </div>
                        </div>

                    @endif

                </div>
            </section>

            {{-- INFORMACIÓN --}}
            <section class="cocinero-detail-card cocinero-reveal">

                <div class="cocinero-detail-card-header">

                    <div>
                        <div class="cocinero-detail-card-icon">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>

                        <div>
                            <h3>Información</h3>
                            <span>Datos del pedido</span>
                        </div>
                    </div>
                </div>

                <div class="cocinero-order-data">

                    <div>
                        <span>Número de pedido</span>
                        <strong>#{{ $pedido->id }}</strong>
                    </div>

                    <div>
                        <span>Fecha</span>
                        <strong>{{ $pedido->created_at?->format('d/m/Y') }}</strong>
                    </div>

                    <div>
                        <span>Hora</span>
                        <strong>{{ $pedido->created_at?->format('H:i') }}</strong>
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
