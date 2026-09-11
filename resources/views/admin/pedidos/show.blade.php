@extends('layouts.admin')
@section('content')
<div class="container-fluid px-0 pedido-detalle">

    {{-- ENCABEZADO --}}
    <div class="pedido-detalle-header">

        <div>
            <h2>
                <i class="bi bi-receipt text-danger"></i>
                Pedido #{{ $pedido->id }}
            </h2>

            <p>
                Detalle completo del pedido
            </p>
        </div>

        <a href="{{ route('admin.pedidos.index') }}"
            class="btn btn-outline-light">

            <i class="bi bi-arrow-left"></i>
            Volver a pedidos

        </a>

    </div>


    {{-- INFORMACIÓN GENERAL --}}
    <div class="card pedido-card shadow">

        <div class="card-body">

            <div class="row pedido-row">

                {{-- CLIENTE --}}
                <div class="col-12 col-md-6">

                    <div class="pedido-info">

                        <h5>
                            <i class="bi bi-person-circle text-danger"></i>
                            Cliente
                        </h5>

                        <div class="pedido-dato">
                            <span>Nombre</span>
                            <strong>
                                {{ $pedido->user->name ?? 'Cliente eliminado' }}
                            </strong>
                        </div>

                        <div class="pedido-dato">
                            <span>Teléfono</span>
                            <strong>
                                {{ $pedido->user->telefono ?? 'Sin teléfono' }}
                            </strong>
                        </div>

                    </div>

                </div>


                {{-- ESTADO --}}
                <div class="col-12 col-md-6">

                    <div class="pedido-info">

                        <h5>
                            <i class="bi bi-clipboard-check text-danger"></i>
                            Estado del pedido
                        </h5>

                        @php
                        $estadoClase = match($pedido->estado) {
                        'pagado' => 'estado-pagado',
                        'preparando' => 'estado-preparando',
                        'listo' => 'estado-listo',
                        'asignado' => 'estado-asignado',
                        'en_camino' => 'estado-camino',
                        'entregado' => 'estado-entregado',
                        'cancelado' => 'estado-cancelado',
                        default => 'estado-default',
                        };
                        @endphp

                        <div class="pedido-estado-wrapper">

                            <span class="pedido-estado {{ $estadoClase }}">

                                @switch($pedido->estado)

                                @case('pagado')
                                <i class="bi bi-credit-card"></i>
                                Pagado
                                @break

                                @case('preparando')
                                <i class="bi bi-fire"></i>
                                Preparando
                                @break

                                @case('listo')
                                <i class="bi bi-check-circle"></i>
                                Listo
                                @break

                                @case('asignado')
                                <i class="bi bi-bicycle"></i>
                                Asignado
                                @break

                                @case('en_camino')
                                <i class="bi bi-truck"></i>
                                En camino
                                @break

                                @case('entregado')
                                <i class="bi bi-check2-all"></i>
                                Entregado
                                @break

                                @case('cancelado')
                                <i class="bi bi-x-circle"></i>
                                Cancelado
                                @break

                                @default
                                {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}

                                @endswitch

                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <div class="pedido-divider"></div>


            {{-- ENTREGA --}}
            <div class="row pedido-row">

                {{-- DIRECCIÓN --}}
                <div class="col-12 col-md-6">

                    <div class="pedido-info">

                        <h5>
                            <i class="bi bi-geo-alt text-danger"></i>
                            Dirección de entrega
                        </h5>

                        <div class="pedido-dato">
                            <span>Dirección</span>
                            <strong>
                                {{ $pedido->direccion_entrega ?? 'Sin dirección registrada' }}
                            </strong>
                        </div>

                        <div class="pedido-dato">
                            <span>Referencia</span>
                            <strong>
                                {{ $pedido->referencia_delivery ?? 'Sin referencia' }}
                            </strong>
                        </div>

                    </div>

                </div>


                {{-- DELIVERY --}}
                <div class="col-12 col-md-6">

                    <div class="pedido-info">

                        <h5>
                            <i class="bi bi-bicycle text-danger"></i>
                            Delivery
                        </h5>

                        @if($pedido->asignacionDelivery &&
                        $pedido->asignacionDelivery->delivery)

                        <div class="pedido-dato">
                            <span>Repartidor</span>
                            <strong>
                                {{ $pedido->asignacionDelivery->delivery->name }}
                            </strong>
                        </div>

                        <div class="pedido-dato">
                            <span>Estado</span>
                            <strong>
                                {{ ucfirst(str_replace(
                                    '_',
                                    ' ',
                                    $pedido->asignacionDelivery->estado
                                )) }}
                            </strong>
                        </div>

                        @else

                        <div class="pedido-sin-dato">
                            <i class="bi bi-hourglass-split"></i>
                            Sin delivery asignado
                        </div>

                        @endif

                    </div>

                </div>

            </div>


            <div class="pedido-divider"></div>


            {{-- PAGO Y FECHA --}}
            <div class="row pedido-row">

                {{-- PAGO --}}
                <div class="col-12 col-md-6">

                    <div class="pedido-info">

                        <h5>
                            <i class="bi bi-credit-card text-danger"></i>
                            Comprobante de pago
                        </h5>

                        @if($pedido->comprobantePago)

                        @if($pedido->comprobantePago->estado === 'aprobado')

                        <span class="pedido-badge pedido-badge-success">
                            <i class="bi bi-check-circle"></i>
                            Pago aprobado
                        </span>

                        @elseif($pedido->comprobantePago->estado === 'pendiente')

                        <span class="pedido-badge pedido-badge-warning">
                            <i class="bi bi-clock"></i>
                            Pago pendiente
                        </span>

                        @elseif($pedido->comprobantePago->estado === 'rechazado')

                        <span class="pedido-badge pedido-badge-danger">
                            <i class="bi bi-x-circle"></i>
                            Pago rechazado
                        </span>

                        @else

                        <span class="pedido-badge pedido-badge-secondary">
                            {{ ucfirst($pedido->comprobantePago->estado) }}
                        </span>

                        @endif

                        @else

                        <span class="pedido-badge pedido-badge-secondary">
                            Sin comprobante
                        </span>

                        @endif

                    </div>

                </div>


                {{-- FECHA --}}
                <div class="col-12 col-md-6">

                    <div class="pedido-info">

                        <h5>
                            <i class="bi bi-calendar-event text-danger"></i>
                            Registro
                        </h5>

                        <div class="pedido-dato">
                            <span>Fecha</span>
                            <strong>
                                {{ $pedido->created_at->format('d/m/Y') }}
                            </strong>
                        </div>

                        <div class="pedido-dato">
                            <span>Hora</span>
                            <strong>
                                {{ $pedido->created_at->format('H:i') }}
                            </strong>
                        </div>

                    </div>

                </div>

            </div>


            {{-- UBICACIÓN + OBSERVACIÓN --}}
            <div class="pedido-divider"></div>

            <div class="row pedido-row">

                {{-- UBICACIÓN --}}
                @if($pedido->latitud && $pedido->longitud)

                <div class="col-12 col-md-6">

                    <div class="pedido-info">

                        <h5>
                            <i class="bi bi-pin-map text-danger"></i>
                            Ubicación de entrega
                        </h5>

                        <div class="pedido-coordenadas">

                            <div>
                                <span>Latitud</span>
                                <strong>{{ $pedido->latitud }}</strong>
                            </div>

                            <div>
                                <span>Longitud</span>
                                <strong>{{ $pedido->longitud }}</strong>
                            </div>

                        </div>

                    </div>

                </div>

                @endif


                {{-- OBSERVACIÓN --}}
                <div class="{{ ($pedido->latitud && $pedido->longitud) ? 'col-12 col-md-6' : 'col-12' }}">

                    <div class="pedido-info">

                        <h5>
                            <i class="bi bi-chat-left-text text-danger"></i>
                            Observación del cliente
                        </h5>

                        <div class="pedido-observacion">

                            @if($pedido->observacion_cliente)

                            {{ $pedido->observacion_cliente }}

                            @else

                            <span>
                                Sin observaciones.
                            </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>


            {{-- PRODUCTOS --}}
            <div class="card pedido-card shadow">

                <div class="card-body">

                    <div class="pedido-productos-header">

                        <h5>
                            <i class="bi bi-bag text-danger"></i>
                            Productos del pedido
                        </h5>

                    </div>


                    <div class="table-responsive">

                        <table class="table pedido-table align-middle">

                            <thead>

                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>

                            </thead>

                            <tbody>

                                @forelse($pedido->detallePedidos as $detalle)

                                <tr>

                                    <td>
                                        <div class="pedido-producto">
                                            {{ $detalle->producto->nombre ?? 'Producto eliminado' }}
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        {{ $detalle->cantidad }}
                                    </td>

                                    <td class="text-end">
                                        Bs {{ number_format($detalle->precio, 2) }}
                                    </td>

                                    <td class="text-end pedido-subtotal">
                                        Bs {{ number_format($detalle->subtotal, 2) }}
                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="4" class="pedido-empty">
                                        No existen productos registrados en este pedido.
                                    </td>
                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>


                    <div class="pedido-total-wrapper">

                        <div class="pedido-total">

                            <span>
                                Total del pedido
                            </span>

                            <strong>
                                Bs {{ number_format($pedido->total, 2) }}
                            </strong>

                        </div>

                    </div>

                </div>

            </div>
        </div>

        @endsection