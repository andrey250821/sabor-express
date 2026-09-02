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

                <i class="bi bi-bicycle me-2"></i>

                Mis pedidos

            </h2>

            <p class="text-muted mb-0">

                Pedidos que tienes asignados para realizar la entrega.

            </p>

        </div>


        <div>

            <span class="badge text-bg-primary fs-6 px-3 py-2">

                <i class="bi bi-bag-check me-1"></i>

                {{ $asignaciones->count() }} pedidos

            </span>

        </div>

    </div>



    {{-- ==========================================
        MENSAJES
    =========================================== --}}

    @if(session('success'))

    <div class="alert alert-success d-flex align-items-center gap-2">

        <i class="bi bi-check-circle-fill"></i>

        <span>

            {{ session('success') }}

        </span>

    </div>

    @endif


    @if(session('error'))

    <div class="alert alert-danger d-flex align-items-center gap-2">

        <i class="bi bi-exclamation-triangle-fill"></i>

        <span>

            {{ session('error') }}

        </span>

    </div>

    @endif



    {{-- ==========================================
        LISTA DE PEDIDOS
    =========================================== --}}

    <div class="card shadow-sm border-0">

        {{-- CABECERA --}}

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-1">

                <i class="bi bi-list-check me-2"></i>

                Pedidos asignados

            </h5>

            <small class="text-muted">

                Aquí puedes consultar y gestionar tus entregas.

            </small>

        </div>



        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

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
                                Dirección
                            </th>

                            <th class="text-center">
                                Estado
                            </th>

                            <th>
                                Fecha
                            </th>

                            <th class="text-center">
                                Acción
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($asignaciones as $asignacion)

                        @php

                        $pedido = $asignacion->pedido;

                        @endphp


                        <tr>


                            {{-- ID --}}

                            <td class="text-center">

                                <span class="fw-bold">

                                    #{{ $pedido->id }}

                                </span>

                            </td>



                            {{-- CLIENTE --}}

                            <td>

                                <div class="d-flex align-items-center gap-2">

                                    <div class="text-primary fs-4">

                                        <i class="bi bi-person-circle"></i>

                                    </div>


                                    <div>

                                        <strong class="d-block">

                                            {{ $pedido->user->name ?? 'Cliente eliminado' }}

                                        </strong>


                                        <small class="text-muted">

                                            <i class="bi bi-telephone me-1"></i>

                                            {{ $pedido->user->telefono ?? 'Sin teléfono' }}

                                        </small>

                                    </div>

                                </div>

                            </td>



                            {{-- TOTAL --}}

                            <td>

                                <strong class="text-success">

                                    Bs.
                                    {{ number_format($pedido->total, 2) }}

                                </strong>

                            </td>



                            {{-- DIRECCIÓN --}}

                            <td>

                                <div class="d-flex align-items-start gap-2">

                                    <i class="bi bi-geo-alt-fill text-danger mt-1"></i>

                                    <span>

                                        {{ $pedido->direccion_entrega }}

                                    </span>

                                </div>

                            </td>



                            {{-- ESTADO --}}

                            <td class="text-center">

                                @if($asignacion->estado === 'aceptado')

                                <span class="badge bg-primary">

                                    <i class="bi bi-bicycle me-1"></i>

                                    Aceptado

                                </span>

                                @elseif($asignacion->estado === 'en_camino')

                                <span class="badge bg-warning text-dark">

                                    <i class="bi bi-truck me-1"></i>

                                    En camino

                                </span>

                                @elseif($asignacion->estado === 'entregado')

                                <span class="badge bg-success">

                                    <i class="bi bi-check-circle me-1"></i>

                                    Entregado

                                </span>

                                @else

                                <span class="badge bg-secondary">

                                    {{ ucfirst($asignacion->estado) }}

                                </span>

                                @endif

                            </td>



                            {{-- FECHA --}}

                            <td>

                                <strong class="d-block">

                                    {{ $pedido->created_at->format('d/m/Y') }}

                                </strong>

                                <small class="text-muted">

                                    {{ $pedido->created_at->format('H:i') }}

                                </small>

                            </td>



                            {{-- ACCIONES --}}

                            <td class="text-center">


                                {{-- VER DETALLES --}}

                                <a
                                    href="{{ route('delivery.pedidos.show', $pedido->id) }}"
                                    class="btn btn-primary btn-sm mb-2">

                                    <i class="bi bi-eye me-1"></i>

                                    Ver detalles

                                </a>



                                {{-- INICIAR ENTREGA --}}

                                @if($asignacion->estado === 'aceptado')

                                <form
                                    action="{{ route('delivery.pedidos.iniciar', $pedido->id) }}"
                                    method="POST">

                                    @csrf

                                    @method('PUT')

                                    <button
                                        type="submit"
                                        class="btn btn-warning btn-sm">

                                        <i class="bi bi-truck me-1"></i>

                                        Iniciar entrega

                                    </button>

                                </form>

                                @endif



                                {{-- MARCAR COMO ENTREGADO --}}

                                @if($asignacion->estado === 'en_camino')

                                <form
                                    method="POST"
                                    action="{{ route('delivery.pedidos.entregar', $pedido->id) }}">

                                    @csrf

                                    @method('PUT')

                                    <button
                                        type="submit"
                                        class="btn btn-success w-100">

                                        <i class="bi bi-check-circle"></i>

                                        Marcar como entregado

                                    </button>

                                </form>

                                @endif


                            </td>

                        </tr>


                        @empty

                        {{-- ==========================================
                            SIN PEDIDOS
                        =========================================== --}}

                        <tr>

                            <td colspan="7" class="py-5">

                                <div class="text-center text-muted">

                                    <i class="bi bi-bicycle display-4 d-block mb-3"></i>


                                    <h5 class="fw-bold">

                                        No tienes pedidos asignados

                                    </h5>


                                    <p class="mb-3">

                                        Cuando tomes un pedido, aparecerá aquí.

                                    </p>


                                    <a
                                        href="{{ route('delivery.pedidos.index') }}"
                                        class="btn btn-primary">

                                        <i class="bi bi-box-seam me-1"></i>

                                        Ver pedidos disponibles

                                    </a>

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