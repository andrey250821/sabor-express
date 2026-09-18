@extends('layouts.admin')

@section('content')

<div class="container-fluid px-0 comprobantes-page">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

        <div>
            <h2 class="fw-bold text-white mb-1">
                <i class="bi bi-receipt text-danger"></i>
                Gestión de Comprobantes
            </h2>

            <p class="text-secondary mb-0">
                El administrador revisa únicamente los comprobantes que presentan problemas en la validación automática.
            </p>
        </div>

        <div class="comprobante-total">
            <div class="comprobante-total-icon">
                <i class="bi bi-shield-exclamation"></i>
            </div>

            <div>
                <small class="text-secondary d-block">Estado actual</small>
                <strong>{{ $comprobantes->count() }}</strong>
            </div>
        </div>

    </div>

    {{-- ESTADOS --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-md-4">
            <a href="{{ route('admin.comprobantes.index', 'en_revision') }}" class="text-decoration-none">
                <div class="comprobante-status-card status-pendiente {{ $estado === 'en_revision' ? 'active' : '' }}">

                    <div class="status-icon">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>

                    <div class="status-info">
                        <span>En revisión</span>
                        <strong>{{ $enRevision }}</strong>
                    </div>

                    <i class="bi bi-chevron-right status-arrow"></i>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-4">
            <a href="{{ route('admin.comprobantes.index', 'aprobado') }}" class="text-decoration-none">
                <div class="comprobante-status-card status-aprobado {{ $estado === 'aprobado' ? 'active' : '' }}">

                    <div class="status-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <div class="status-info">
                        <span>Aprobados</span>
                        <strong>{{ $aprobados }}</strong>
                    </div>

                    <i class="bi bi-chevron-right status-arrow"></i>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-4">
            <a href="{{ route('admin.comprobantes.index', 'rechazado') }}" class="text-decoration-none">
                <div class="comprobante-status-card status-rechazado {{ $estado === 'rechazado' ? 'active' : '' }}">

                    <div class="status-icon">
                        <i class="bi bi-x-circle"></i>
                    </div>

                    <div class="status-info">
                        <span>Rechazados</span>
                        <strong>{{ $rechazados }}</strong>
                    </div>

                    <i class="bi bi-chevron-right status-arrow"></i>
                </div>
            </a>
        </div>

    </div>

    <div class="comprobantes-section-header mb-3">
        <div>
            <h4 class="fw-bold text-white mb-1">
                Comprobantes {{ $estado === 'en_revision' ? 'en revisión' : ucfirst($estado) }}
            </h4>

            <small class="text-secondary">
                Los comprobantes aprobados automáticamente no requieren intervención del administrador.
            </small>
        </div>
    </div>

    <div class="row g-4">

        @forelse($comprobantes as $comprobante)

        <div class="col-12 col-md-6 col-xl-4">

            <div class="comprobante-card">

                <div class="comprobante-card-header">

                    <div class="d-flex justify-content-between align-items-center">

                        <span class="pedido-numero">
                            <i class="bi bi-bag"></i>
                            Pedido #{{ $comprobante->pedido->id }}
                        </span>

                        @if($comprobante->estado === 'en_revision')
                            <span class="estado-badge pendiente">En revisión</span>
                        @elseif($comprobante->estado === 'aprobado')
                            <span class="estado-badge aprobado">Aprobado</span>
                        @else
                            <span class="estado-badge rechazado">Rechazado</span>
                        @endif

                    </div>

                </div>

                <div class="comprobante-card-body">

                    <div class="comprobante-info">
                        <div class="info-icon"><i class="bi bi-person"></i></div>
                        <div>
                            <small>Cliente</small>
                            <strong>{{ $comprobante->pedido->user->name ?? 'Cliente eliminado' }}</strong>
                        </div>
                    </div>

                    <div class="comprobante-info">
                        <div class="info-icon"><i class="bi bi-telephone"></i></div>
                        <div>
                            <small>Teléfono</small>
                            <strong>{{ $comprobante->pedido->user->telefono ?? 'Sin teléfono' }}</strong>
                        </div>
                    </div>

                    <div class="comprobante-info">
                        <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <small>Dirección</small>
                            <strong>{{ $comprobante->pedido->direccion_entrega }}</strong>
                        </div>
                    </div>

                    @if($comprobante->pedido->referencia_delivery)
                    <div class="comprobante-info">
                        <div class="info-icon"><i class="bi bi-pin-map"></i></div>
                        <div>
                            <small>Referencia Delivery</small>
                            <strong>{{ $comprobante->pedido->referencia_delivery }}</strong>
                        </div>
                    </div>
                    @endif

                    <div class="comprobante-monto">
                        <div>
                            <small>Total del pedido</small>
                            <strong>Bs {{ number_format($comprobante->pedido->total, 2) }}</strong>
                        </div>
                        <i class="bi bi-cash-stack"></i>
                    </div>

                    @if($comprobante->referencia_bancaria)
                    <div class="comprobante-info">
                        <div class="info-icon"><i class="bi bi-upc-scan"></i></div>
                        <div>
                            <small>Referencia registrada</small>
                            <strong>{{ $comprobante->referencia_bancaria }}</strong>
                        </div>
                    </div>
                    @endif

                    <div class="comprobante-imagen-container">
                        <img
                            src="{{ asset('storage/' . $comprobante->imagen) }}"
                            class="img-fluid rounded-3 comprobante-img mb-3"
                            alt="Comprobante de pago">
                    </div>

                    <a
                        href="{{ asset('storage/' . $comprobante->imagen) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-outline-light w-100 mb-3">
                        <i class="bi bi-eye me-1"></i>
                        Ver comprobante
                    </a>

                    @if($comprobante->estado === 'en_revision')

                    <div class="alert alert-warning py-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Este comprobante requiere revisión manual.
                    </div>

                    <div class="comprobante-acciones mt-3">

                        <form action="{{ route('admin.comprobantes.aprobar', $comprobante->id) }}" method="POST" class="flex-fill">
                            @csrf
                            @method('PUT')

                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-lg"></i>
                                Aprobar
                            </button>
                        </form>

                        <form action="{{ route('admin.comprobantes.rechazar', $comprobante->id) }}" method="POST" class="flex-fill">
                            @csrf
                            @method('PUT')

                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-x-lg"></i>
                                Rechazar
                            </button>
                        </form>

                    </div>

                    @elseif($comprobante->estado === 'aprobado')

                    <div class="estado-final">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Comprobante aprobado</span>
                    </div>

                    @else

                    <div class="estado-final">
                        <i class="bi bi-x-circle-fill"></i>
                        <span>Comprobante rechazado</span>
                    </div>

                    @endif

                </div>
            </div>
        </div>

        @empty

        <div class="col-12">

            <div class="sin-comprobantes">

                <div class="sin-comprobantes-icon">
                    <i class="bi bi-shield-check"></i>
                </div>

                <h5 class="text-white fw-bold">
                    No existen comprobantes en este estado
                </h5>

                <p class="text-secondary mb-0">
                    {{ $estado === 'en_revision'
                        ? 'No hay excepciones que requieran revisión administrativa.'
                        : 'No hay comprobantes para mostrar.' }}
                </p>

            </div>

        </div>

        @endforelse

    </div>
</div>

@endsection