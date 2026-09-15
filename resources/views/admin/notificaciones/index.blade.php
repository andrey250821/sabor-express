@extends('layouts.admin')

@section('title', 'Notificaciones')

@section('content')

<div class="container-fluid py-4">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-bell me-2"></i>
                Notificaciones
            </h2>

            <p class="text-muted mb-0">
                Avisos importantes del sistema.
            </p>
        </div>

        @if($noLeidas > 0)
        <form
            action="{{ route('admin.notificaciones.leer.todas') }}"
            method="POST">
            @csrf
            @method('PATCH')

            <button type="submit" class="btn btn-outline-secondary">
                <i class="bi bi-check2-all me-1"></i>
                Marcar todas como leídas
            </button>
        </form>
        @endif
    </div>


    {{-- Resumen --}}
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">

                    <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-bell-fill text-danger fs-4"></i>
                    </div>

                    <div>
                        <h6 class="text-muted mb-1">
                            No leídas
                        </h6>

                        <h3 class="fw-bold mb-0">
                            {{ $noLeidas }}
                        </h3>
                    </div>

                </div>
            </div>
        </div>

    </div>


    {{-- Lista de notificaciones --}}
    @forelse($notificaciones as $notificacion)

    @php
    $esComprobante = $notificacion->evento === 'comprobante_enviado';
    $esCalificacion = $notificacion->evento === 'nueva_calificacion';
    @endphp

    <div
        class="card border-0 shadow-sm mb-3
            {{ !$notificacion->leido ? 'border-start border-4 border-danger' : '' }}">

        <div class="card-body">

            <div class="d-flex align-items-start">

                {{-- Icono --}}
                <div
                    class="rounded-circle p-3 me-3
                        {{ !$notificacion->leido ? 'bg-danger bg-opacity-10' : 'bg-light' }}">

                    @if($esComprobante)
                    <i class="bi bi-receipt fs-4
                                {{ !$notificacion->leido ? 'text-danger' : 'text-secondary' }}">
                    </i>

                    @elseif($esCalificacion)
                    <i class="bi bi-star-fill fs-4
                                {{ !$notificacion->leido ? 'text-warning' : 'text-secondary' }}">
                    </i>

                    @else
                    <i class="bi bi-bell fs-4 text-secondary"></i>
                    @endif

                </div>


                {{-- Contenido --}}
                <div class="flex-grow-1">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <h5 class="mb-1 fw-bold">

                                @if($esComprobante)
                                Comprobante enviado

                                @elseif($esCalificacion)
                                Nueva calificación

                                @else
                                Notificación
                                @endif

                                @if(!$notificacion->leido)
                                <span class="badge bg-danger ms-2">
                                    Nueva
                                </span>
                                @endif

                            </h5>

                            <p class="mb-2 text-dark">
                                {{ $notificacion->mensaje }}
                            </p>

                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                {{ $notificacion->created_at->format('d/m/Y H:i') }}
                            </small>

                        </div>

                    </div>


                    {{-- Acciones --}}
                    <div class="mt-3 d-flex gap-2 flex-wrap">

                        @if(!$notificacion->leido)

                        <form
                            action="{{ route('admin.notificaciones.leer', $notificacion->id) }}"
                            method="POST">
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-check2 me-1"></i>
                                Marcar como leída
                            </button>
                        </form>

                        @endif


                        @if($notificacion->pedido_id)

                        <a
                            href="{{ route('admin.pedidos.show', $notificacion->pedido_id) }}"
                            class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-eye me-1"></i>
                            Ver pedido
                        </a>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>

    @empty

    {{-- Sin notificaciones --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body text-center py-5">

            <i class="bi bi-bell-slash text-muted display-4"></i>

            <h4 class="mt-3">
                No tienes notificaciones
            </h4>

            <p class="text-muted mb-0">
                Aquí aparecerán los comprobantes enviados
                y las nuevas calificaciones.
            </p>

        </div>

    </div>

    @endforelse

</div>

@endsection