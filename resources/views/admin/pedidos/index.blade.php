@extends('layouts.admin')

@section('content')

<div class="container-fluid px-0 pedidos-page">

    {{-- ================================
        ENCABEZADO
    ================================= --}}

    <div class="d-flex flex-column flex-md-row
                justify-content-between
                align-items-md-center
                gap-3
                mb-4">

        <div>

            <h2 class="fw-bold text-white mb-1">
                <i class="bi bi-bag-check pedidos-title-icon"></i>
                Gestión de Pedidos
            </h2>

            <p class="text-muted mb-0">
                Administra los pedidos, estados y asignaciones de delivery.
            </p>

        </div>


        {{-- TOTAL PEDIDOS --}}

        <div class="pedidos-counter">

            <i class="bi bi-bag-fill"></i>

            <div>

                <small>
                    Total de pedidos
                </small>

                <strong>
                    {{ $pedidos->count() }}
                </strong>

            </div>

        </div>

    </div>



    {{-- ================================
        MENSAJE DE ÉXITO
    ================================= --}}

    @if(session('success'))

    <div class="alert alert-success pedidos-alert
                    d-flex align-items-center gap-2">

        <i class="bi bi-check-circle-fill"></i>

        <span>
            {{ session('success') }}
        </span>

    </div>

    @endif



    {{-- ================================
        TABLA
    ================================= --}}

    <div class="card pedidos-card shadow">

        <div class="card-body p-0">

            <div class="pedidos-card-header">

                <div>

                    <h5 class="mb-1 fw-bold">
                        <i class="bi bi-list-ul"></i>
                        Pedidos registrados
                    </h5>

                    <small>
                        Consulta y administra todos los pedidos.
                    </small>

                </div>

            </div>



            {{-- RESPONSIVE --}}

            <div class="table-responsive">

                <table class="table pedidos-table align-middle mb-0">

                    <thead>

                        <tr>

                            <th class="text-center">
                                ID
                            </th>

                            <th>
                                Cliente
                            </th>

                            <th>
                                Total
                            </th>

                            <th>
                                Estado
                            </th>

                            <th>
                                Dirección
                            </th>

                            <th>
                                Fecha
                            </th>

                            <th class="text-center pedidos-action-column">
                                Acción
                            </th>

                        </tr>

                    </thead>



                    <tbody>

                        @forelse($pedidos as $pedido)

                        <tr>

                            {{-- ID --}}

                            <td class="text-center">

                                <span class="pedido-id">
                                    #{{ $pedido->id }}
                                </span>

                            </td>



                            {{-- CLIENTE --}}

                            <td>

                                <div class="pedido-cliente">

                                    <div class="cliente-avatar">

                                        <i class="bi bi-person-fill"></i>

                                    </div>

                                    <div>

                                        <strong>
                                            {{ $pedido->user->name ?? 'Cliente eliminado' }}
                                        </strong>

                                        <small>

                                            <i class="bi bi-telephone"></i>

                                            {{ $pedido->user->telefono ?? 'Sin teléfono' }}

                                        </small>

                                    </div>

                                </div>

                            </td>



                            {{-- TOTAL --}}

                            <td>

                                <strong class="pedido-total">

                                    Bs.
                                    {{ number_format($pedido->total, 2) }}

                                </strong>

                            </td>



                            {{-- ESTADO --}}

                            <td>

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

                                    {{ ucfirst($pedido->estado) }}

                                    @endswitch

                                </span>

                            </td>



                            {{-- DIRECCIÓN --}}

                            <td>

                                <div class="pedido-direccion">

                                    <i class="bi bi-geo-alt-fill"></i>

                                    <span>
                                        {{ $pedido->direccion_entrega }}
                                    </span>

                                </div>

                            </td>



                            {{-- FECHA --}}

                            <td>

                                <div class="pedido-fecha">

                                    <strong>
                                        {{ $pedido->created_at->format('d/m/Y') }}
                                    </strong>

                                    <small>
                                        {{ $pedido->created_at->format('H:i') }}
                                    </small>

                                </div>

                            </td>



                            {{-- ACCIONES --}}

                            <td>

                                {{-- VER DETALLES --}}

                                <a href="{{ route('admin.pedidos.show', $pedido->id) }}"
                                    class="btn btn-primary btn-sm w-100 mb-2">

                                    <i class="bi bi-eye"></i>

                                    Ver detalles

                                </a>
                                

                                {{-- INFORMACIÓN DEL DELIVERY --}}

                                <div class="mt-3 pt-2 border-top">


                                    @if($pedido->asignacionDelivery)

                                    <div class="small text-success mb-3">

                                        <i class="bi bi-bicycle"></i>

                                        <strong>Delivery asignado:</strong>

                                        {{ $pedido->asignacionDelivery->delivery->name }}

                                    </div>

                                    @else

                                    <div class="small text-warning mb-3">

                                        <i class="bi bi-exclamation-circle"></i>

                                        <strong>Sin delivery asignado</strong>

                                    </div>

                                    @endif
                                    <div class="mt-2">
                                        <label class="form-label small fw-bold">
                                            <i class="bi bi-bicycle"></i>
                                            Delivery
                                        </label>

                                        @if($pedido->asignacionDelivery && $pedido->asignacionDelivery->delivery)
                                        <div class="alert alert-success py-2 mb-0">
                                            <i class="bi bi-person-check"></i>
                                            {{ $pedido->asignacionDelivery->delivery->name }}
                                        </div>
                                        @else
                                        <div class="alert alert-secondary py-2 mb-0">
                                            <i class="bi bi-hourglass-split"></i>
                                            Sin delivery asignado
                                        </div>
                                        @endif
                                    </div>

                                </div>


                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="7">

                                <div class="pedidos-empty">

                                    <i class="bi bi-bag-x"></i>

                                    <h5>
                                        No existen pedidos
                                    </h5>

                                    <p>
                                        Todavía no se han registrado pedidos.
                                    </p>

                                </div>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection