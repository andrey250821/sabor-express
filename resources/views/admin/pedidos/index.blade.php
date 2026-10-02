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
                Consulta y supervisa los pedidos, estados y entregas.
            </p>

        </div>


        {{-- TOTAL PEDIDOS --}}

        <div class="pedidos-counter">

            <i class="bi bi-bag-fill"></i>

            <div>

                <small>
                    Pedidos de la fecha
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
                        Consulta y supervisa los pedidos registrados.
                    </small>

                </div>

            </div>



            @include('shared.date-filter', [
        'fechaSeleccionada' => $fechaSeleccionada,
        'tituloFecha' => 'Pedidos registrados'
    ])

    {{-- FILTRO POR ESTADO --}}
    <div class="pedidos-filtros-panel">
        <div class="pedidos-filtros-heading">
            <div class="pedidos-filtros-icon">
                <i class="bi bi-funnel-fill"></i>
            </div>
            <div>
                <span>Filtros de pedidos</span>
                <small>Filtra los pedidos por su estado actual</small>
            </div>
        </div>

        <form
            action="{{ route('admin.pedidos.index') }}"
            method="GET"
            class="pedidos-filtros-form">

            <input
                type="hidden"
                name="fecha"
                value="{{ $fechaSeleccionada->toDateString() }}">

            <div class="pedidos-filtro-field">
                <label for="estado-pedido-filtro">
                    <i class="bi bi-activity"></i>
                    Estado
                </label>

                <select
                    id="estado-pedido-filtro"
                    name="estado"
                    class="pedidos-filtro-select">
                    <option value="">Todos los estados</option>
                    <option value="pagado" {{ $estadoSeleccionado === 'pagado' ? 'selected' : '' }}>Pagado</option>
                    <option value="preparando" {{ $estadoSeleccionado === 'preparando' ? 'selected' : '' }}>Preparando</option>
                    <option value="listo" {{ $estadoSeleccionado === 'listo' ? 'selected' : '' }}>Listo</option>
                    <option value="asignado" {{ $estadoSeleccionado === 'asignado' ? 'selected' : '' }}>Asignado</option>
                    <option value="en_camino" {{ $estadoSeleccionado === 'en_camino' ? 'selected' : '' }}>En camino</option>
                    <option value="entregado" {{ $estadoSeleccionado === 'entregado' ? 'selected' : '' }}>Entregado</option>
                    <option value="cancelado" {{ $estadoSeleccionado === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>

            <button type="submit" class="btn pedidos-filtro-submit">
                <i class="bi bi-funnel-fill"></i>
                Aplicar filtro
            </button>

            <a
                href="{{ route('admin.pedidos.index', ['fecha' => $fechaSeleccionada->toDateString()]) }}"
                class="btn pedidos-filtro-reset">
                <i class="bi bi-arrow-counterclockwise"></i>
                Ver todos
            </a>
        </form>
    </div>

    @if($estadoSeleccionado)
    <div class="pedidos-filtro-activo">
        <div class="pedidos-filtro-activo-info">
            <span class="pedidos-filtro-activo-icon">
                <i class="bi bi-funnel-fill"></i>
            </span>
            <div>
                <small>Filtro activo</small>
                <strong>{{ $estadoNombre }} · {{ $fechaSeleccionada->format('d/m/Y') }}</strong>
            </div>
        </div>

        <a
            href="{{ route('admin.pedidos.index', ['fecha' => $fechaSeleccionada->toDateString()]) }}"
            class="pedidos-filtro-activo-clear">
            <i class="bi bi-x-circle"></i>
            Quitar filtro
        </a>
    </div>
    @endif

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

                                        @if($pedido->user?->foto_perfil_url)
                                            <img
                                                src="{{ $pedido->user->foto_perfil_url }}"
                                                alt="Foto de {{ $pedido->user->name }}">
                                        @else
                                            <i class="bi bi-person-fill"></i>
                                        @endif

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

                                'pagado' => 'pagado',

                                'preparando' => 'preparando',

                                'listo' => 'listo',

                                'asignado' => 'asignado',

                                'en_camino' => 'en_camino',

                                'entregado' => 'entregado',

                                'cancelado' => 'cancelado',

                                default => 'default',

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

                                    {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}

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
                                        {{ $pedido->created_at->copy()->timezone(\App\Services\FechaFiltroService::TIMEZONE)->format('d/m/Y') }}
                                    </strong>

                                    <small>
                                        {{ $pedido->created_at->copy()->timezone(\App\Services\FechaFiltroService::TIMEZONE)->format('H:i') }}
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

                                    @if($pedido->asignacionDelivery && $pedido->asignacionDelivery->delivery)

                                    <div class="small text-success">

                                        <i class="bi bi-bicycle"></i>

                                        <strong>Delivery:</strong>

                                        {{ $pedido->asignacionDelivery->delivery->name }}

                                    </div>

                                    @else

                                    <div class="small text-warning">

                                        <i class="bi bi-exclamation-circle"></i>

                                        <strong>Sin delivery asignado</strong>

                                    </div>

                                    @endif

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