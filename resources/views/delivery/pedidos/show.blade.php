@extends('layouts.delivery')

@section('content')

<div class="container-fluid px-0">

    {{-- ==========================================
        ENCABEZADO
    =========================================== --}}

    <div class="d-flex flex-column flex-md-row
                justify-content-between
                align-items-md-center
                gap-3
                mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-receipt me-2"></i>
                Detalle del pedido #{{ $pedido->id }}
            </h2>

            <p class="text-muted mb-0">
                Consulta la información del pedido antes de tomarlo.
            </p>
        </div>

        <a
            href="{{ route('delivery.pedidos.index') }}"
            class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Volver a pedidos

        </a>
    </div>


    {{-- ==========================================
        MENSAJES
    =========================================== --}}

    @if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif


    <div class="row g-4">

        {{-- ==========================================
            INFORMACIÓN DEL CLIENTE
        =========================================== --}}

        <div class="col-lg-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-header bg-white py-3">

                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-person-circle me-2"></i>
                        Información del cliente
                    </h5>

                </div>

                <div class="card-body">

                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Nombre
                        </small>

                        <strong>
                            {{ $pedido->user->name ?? 'Cliente eliminado' }}
                        </strong>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Teléfono
                        </small>

                        <strong>
                            {{ $pedido->user->telefono ?? 'Sin teléfono' }}
                        </strong>

                    </div>


                    <div>

                        <small class="text-muted d-block">
                            Correo electrónico
                        </small>

                        <strong>
                            {{ $pedido->user->email ?? 'Sin correo' }}
                        </strong>

                    </div>

                </div>

            </div>

        </div>


        {{-- ==========================================
            INFORMACIÓN DE ENTREGA
        =========================================== --}}

        <div class="col-lg-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-header bg-white py-3">

                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-geo-alt-fill me-2"></i>
                        Información de entrega
                    </h5>

                </div>


                <div class="card-body">

                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Dirección
                        </small>

                        <strong>
                            {{ $pedido->direccion_entrega ?? 'Sin dirección registrada' }}
                        </strong>

                    </div>


                    {{-- REFERENCIA PARA EL DELIVERY --}}

                    <div class="mb-3">

                        <small class="text-muted d-block">

                            <i class="bi bi-signpost-2 me-1"></i>
                            Referencia para el delivery

                        </small>

                        @if(!empty($pedido->referencia_delivery))

                        <div class="alert alert-info mb-0 mt-1">

                            <i class="bi bi-info-circle me-1"></i>

                            {{ $pedido->referencia_delivery }}

                        </div>

                        @else

                        <span class="text-muted">
                            Sin referencia proporcionada.
                        </span>

                        @endif

                    </div>


                    {{-- OBSERVACIONES DEL CLIENTE --}}

                    @if(!empty($pedido->observacion_cliente))

                    <div class="mb-3">

                        <small class="text-muted d-block">

                            <i class="bi bi-chat-left-text me-1"></i>
                            Observaciones del pedido

                        </small>

                        <div class="alert alert-warning mb-0 mt-1">

                            <i class="bi bi-info-circle me-1"></i>

                            {{ $pedido->observacion_cliente }}

                        </div>

                    </div>

                    @endif


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Fecha del pedido
                        </small>

                        <strong>
                            {{ $pedido->created_at->format('d/m/Y H:i') }}
                        </strong>

                    </div>


                    <div>

                        <small class="text-muted d-block">
                            Estado
                        </small>

                        <span class="badge bg-success">

                            <i class="bi bi-check-circle me-1"></i>

                            Listo para entregar

                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- ==========================================
            UBICACIÓN DEL CLIENTE
        =========================================== --}}

        <div class="col-12">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">

                    <h5 class="fw-bold mb-1">

                        <i class="bi bi-map me-2"></i>
                        Ubicación de entrega

                    </h5>

                    <small class="text-muted">
                        Ubicación seleccionada por el cliente al realizar el pedido.
                    </small>

                </div>


                <div class="card-body">

                    @if(
                    !is_null($pedido->latitud) &&
                    !is_null($pedido->longitud)
                    )

                    <div
                        id="mapa-entrega"
                        style="
                                width: 100%;
                                height: 450px;
                                border-radius: 12px;
                                overflow: hidden;
                                border: 1px solid #ddd;
                                background: #f3f3f3;
                            ">
                    </div>


                    <div class="mt-3">

                        <div class="row">

                            <div class="col-md-6">

                                <small class="text-muted d-block">
                                    Latitud
                                </small>

                                <strong>
                                    {{ $pedido->latitud }}
                                </strong>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted d-block">
                                    Longitud
                                </small>

                                <strong>
                                    {{ $pedido->longitud }}
                                </strong>

                            </div>

                        </div>

                    </div>

                    @else

                    <div class="alert alert-warning mb-0">

                        <i class="bi bi-exclamation-triangle me-1"></i>

                        Este pedido no tiene una ubicación GPS registrada.

                    </div>

                    @endif

                </div>

            </div>

        </div>


        {{-- ==========================================
            PRODUCTOS
        =========================================== --}}

        <div class="col-12">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">

                    <h5 class="fw-bold mb-0">

                        <i class="bi bi-basket me-2"></i>
                        Productos del pedido

                    </h5>

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th>
                                        Producto
                                    </th>

                                    <th class="text-center">
                                        Cantidad
                                    </th>

                                    <th class="text-end">
                                        Precio
                                    </th>

                                    <th class="text-end">
                                        Subtotal
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($pedido->detallePedidos as $detalle)

                                <tr>

                                    <td>

                                        <strong>
                                            {{ $detalle->producto->nombre ?? 'Producto eliminado' }}
                                        </strong>

                                    </td>


                                    <td class="text-center">

                                        {{ $detalle->cantidad }}

                                    </td>


                                    <td class="text-end">

                                        Bs.
                                        {{ number_format($detalle->precio_unitario, 2) }}

                                    </td>


                                    <td class="text-end">

                                        <strong>

                                            Bs.
                                            {{ number_format(
                                                    $detalle->cantidad * $detalle->precio_unitario,
                                                    2
                                                ) }}

                                        </strong>

                                    </td>

                                </tr>

                                @empty

                                <tr>

                                    <td
                                        colspan="4"
                                        class="text-center py-4 text-muted">

                                        No hay productos registrados en este pedido.

                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>


                {{-- TOTAL --}}

                <div class="card-footer bg-white">

                    <div class="d-flex justify-content-end align-items-center gap-3">

                        <span class="fw-bold">
                            Total del pedido:
                        </span>

                        <span class="fs-4 fw-bold text-success">

                            Bs.
                            {{ number_format($pedido->total, 2) }}

                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- ==========================================
            COMPROBANTE DE PAGO
        =========================================== --}}

        @if($pedido->comprobantePago)

        <div class="col-lg-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-header bg-white py-3">

                    <h5 class="fw-bold mb-0">

                        <i class="bi bi-file-earmark-image me-2"></i>
                        Comprobante de pago

                    </h5>

                </div>


                <div class="card-body">

                    <p class="text-muted">
                        Comprobante registrado para este pedido.
                    </p>


                    @if(!empty($pedido->comprobantePago->imagen))

                    <a
                        href="{{ asset('storage/' . $pedido->comprobantePago->imagen) }}"
                        target="_blank"
                        class="btn btn-outline-primary">

                        <i class="bi bi-image me-1"></i>
                        Ver comprobante

                    </a>

                    @else

                    <span class="text-muted">
                        No hay imagen disponible.
                    </span>

                    @endif

                </div>

            </div>

        </div>

        @endif


        {{-- ==========================================
            TOMAR PEDIDO
        =========================================== --}}

        <div class="col-12">

            <div class="card shadow-sm border-0">

                <div class="card-body text-center py-4">

                    <h5 class="fw-bold mb-2">
                        ¿Deseas tomar este pedido?
                    </h5>

                    <p class="text-muted mb-4">

                        Al tomarlo, el pedido quedará asignado a ti
                        y aparecerá en "Mis pedidos".

                    </p>


                    <form
                        action="{{ route('delivery.pedidos.tomar', $pedido->id) }}"
                        method="POST">

                        @csrf

                        <button
                            type="submit"
                            class="btn btn-success btn-lg px-5">

                            <i class="bi bi-bicycle me-2"></i>
                            Tomar pedido

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection


{{-- ==========================================
    GOOGLE MAPS
=========================================== --}}

@section('scripts')

@if(
!is_null($pedido->latitud) &&
!is_null($pedido->longitud)
)

<script>
    let mapaEntrega = null;
    let marcadorEntrega = null;


    function inicializarMapaEntrega() {

        const posicionCliente = {
            lat: {
                {
                    (float) $pedido - > latitud
                }
            },
            lng: {
                {
                    (float) $pedido - > longitud
                }
            }
        };


        const elementoMapa = document.getElementById('mapa-entrega');


        if (!elementoMapa) {

            console.error(
                'No se encontró el elemento #mapa-entrega'
            );

            return;
        }


        mapaEntrega = new google.maps.Map(
            elementoMapa, {
                center: posicionCliente,
                zoom: 17,

                mapTypeControl: true,

                streetViewControl: false,

                fullscreenControl: true
            }
        );


        marcadorEntrega = new google.maps.Marker({

            position: posicionCliente,

            map: mapaEntrega,

            title: 'Ubicación de entrega del cliente'

        });

    }
</script>


@if(!empty(config('services.google_maps.key')))

<script
    async
    defer
    src="https://maps.googleapis.com/maps/api/js?key={{ urlencode(config('services.google_maps.key')) }}&callback=inicializarMapaEntrega">
</script>

@else

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const mapa = document.getElementById('mapa-entrega');


        if (mapa) {

            mapa.innerHTML = `
                <div class="d-flex h-100 align-items-center justify-content-center text-muted p-4 text-center">

                    <div>

                        <i class="bi bi-map display-5 d-block mb-3"></i>

                        <strong>
                            Google Maps no está configurado.
                        </strong>

                        <p class="mb-0 mt-2">
                            Configura GOOGLE_MAPS_API_KEY en el archivo .env.
                        </p>

                    </div>

                </div>
            `;

        }

    });
</script>

@endif

@endif

@endsection