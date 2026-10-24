@extends('layouts.admin')

@push('styles')
<style>
    /*
     * Acciones de revisión: estilos explícitos para que los botones
     * destaquen y permanezcan visibles dentro de cada comprobante pendiente.
     */
    .admin-content .comprobantes-page .comprobante-acciones {
        display: flex !important;
        align-items: stretch;
        gap: 10px;
        width: 100%;
        margin: 14px 0 18px;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .admin-content .comprobantes-page .comprobante-acciones form {
        display: flex !important;
        flex: 1 1 0;
        min-width: 0;
        margin: 0;
    }

    .admin-content .comprobantes-page .comprobante-acciones .btn-aprobar-comprobante,
    .admin-content .comprobantes-page .comprobante-acciones .btn-rechazar-comprobante {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100% !important;
        min-height: 46px;
        padding: 11px 14px;
        border: 1px solid transparent;
        border-radius: 10px;
        color: #fff !important;
        font-size: .9rem;
        font-weight: 750;
        line-height: 1.2;
        text-align: center;
        white-space: normal;
        visibility: visible !important;
        opacity: 1 !important;
        box-shadow: 0 5px 14px rgba(0, 0, 0, .22);
        transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
    }

    .admin-content .comprobantes-page .comprobante-acciones .btn-aprobar-comprobante {
        background: linear-gradient(135deg, #147d46, #22a45d) !important;
        border-color: #31bf73 !important;
    }

    .admin-content .comprobantes-page .comprobante-acciones .btn-aprobar-comprobante:hover {
        background: linear-gradient(135deg, #168b4f, #29b86b) !important;
        border-color: #55d98e !important;
        color: #fff !important;
        filter: brightness(1.04);
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(22, 139, 79, .25);
    }

    .admin-content .comprobantes-page .comprobante-acciones .btn-rechazar-comprobante {
        background: linear-gradient(135deg, #b42338, #df354b) !important;
        border-color: #f05b6e !important;
    }

    .admin-content .comprobantes-page .comprobante-acciones .btn-rechazar-comprobante:hover {
        background: linear-gradient(135deg, #c82b41, #f04458) !important;
        border-color: #ff8794 !important;
        color: #fff !important;
        filter: brightness(1.04);
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(196, 35, 56, .25);
    }

    .admin-content .comprobantes-page .comprobante-acciones .btn:focus-visible {
        outline: 3px solid rgba(255, 255, 255, .9);
        outline-offset: 3px;
    }

    @media (max-width: 575.98px) {
        .admin-content .comprobantes-page .comprobante-acciones {
            flex-direction: column !important;
        }

        .admin-content .comprobantes-page .comprobante-acciones form {
            width: 100%;
        }
    }
</style>
@endpush

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

    @include('shared.date-filter', [
        'fechaSeleccionada' => $fechaSeleccionada,
        'tituloFecha' => 'Comprobantes registrados'
    ])

    {{-- ESTADOS --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-md-4">
            <a href="{{ route('admin.comprobantes.index', [
                'estado' => 'en_revision',
                'fecha' => $fechaSeleccionada->toDateString()
            ]) }}" class="text-decoration-none">
                <div class="comprobante-status-card status-revision {{ $estado === 'en_revision' ? 'active' : '' }}">

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
            <a href="{{ route('admin.comprobantes.index', [
                'estado' => 'aprobado',
                'fecha' => $fechaSeleccionada->toDateString()
            ]) }}" class="text-decoration-none">
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
            <a href="{{ route('admin.comprobantes.index', [
                'estado' => 'rechazado',
                'fecha' => $fechaSeleccionada->toDateString()
            ]) }}" class="text-decoration-none">
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
                            <span class="estado-badge revision">En revisión</span>
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
                            <strong>{{ $comprobante->pedido?->user?->name ?? 'Cliente eliminado' }}</strong>
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

                    <div class="comprobante-info">
                        <div class="info-icon"><i class="bi bi-pin-map"></i></div>
                        <div>
                            <small>Referencia Delivery</small>
                            <strong>
                                {{ $comprobante->pedido->referencia_delivery ?: 'Sin referencia registrada' }}
                            </strong>
                        </div>
                    </div>

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
                        <a
                            href="{{ route('admin.comprobantes.imagen', $comprobante->id) }}"
                            target="_blank"
                            rel="noopener noreferrer">
                            <img
                                src="{{ route('admin.comprobantes.imagen', $comprobante->id) }}"
                                class="img-fluid rounded-3 comprobante-img mb-3"
                                alt="Comprobante de pago">
                        </a>
                    </div>

                    <a
                        href="{{ route('admin.comprobantes.imagen', $comprobante->id) }}"
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

                    <div class="comprobante-acciones" role="group" aria-label="Acciones de revisión del comprobante">

                        <form
                            action="{{ route('admin.comprobantes.aprobar', $comprobante->id) }}"
                            method="POST"
                            class="form-aprobar-comprobante"
                            onsubmit="return confirm('¿Confirmas que deseas aprobar el comprobante del pedido #{{ $comprobante->pedido->id }}?');">
                            @csrf
                            @method('PUT')

                            <button
                                type="submit"
                                class="btn btn-aprobar-comprobante"
                                aria-label="Aprobar comprobante del pedido {{ $comprobante->pedido->id }}">
                                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                                <span>Aprobar comprobante</span>
                            </button>
                        </form>

                        <form
                            action="{{ route('admin.comprobantes.rechazar', $comprobante->id) }}"
                            method="POST"
                            class="form-rechazar-comprobante"
                            onsubmit="return confirm('¿Confirmas que deseas rechazar el comprobante del pedido #{{ $comprobante->pedido->id }}? El pedido se cancelará y se devolverá el stock.');">
                            @csrf
                            @method('PUT')

                            <button
                                type="submit"
                                class="btn btn-rechazar-comprobante"
                                aria-label="Rechazar comprobante del pedido {{ $comprobante->pedido->id }}">
                                <i class="bi bi-x-circle-fill" aria-hidden="true"></i>
                                <span>Rechazar comprobante</span>
                            </button>
                        </form>

                    </div>

                    @if($comprobante->motivo_revision)
                    <div class="mb-3">
                        <small class="text-secondary d-block mb-1">Motivo detectado</small>
                        <div class="alert alert-dark border border-warning-subtle mb-0">
                            {{ $comprobante->motivo_revision }}
                        </div>
                    </div>
                    @endif

                    @if($comprobante->datos_ocr)
                    <div class="mb-3">
                        <small class="text-secondary d-block mb-2">Datos extraídos por OCR</small>
                        <div class="small text-white">
                            <div class="d-flex justify-content-between border-bottom border-secondary-subtle py-1">
                                <span>Pedido</span>
                                <strong>{{ $comprobante->datos_ocr['pedido'] ?? 'No detectado' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between border-bottom border-secondary-subtle py-1">
                                <span>Monto</span>
                                <strong>
                                    {{ isset($comprobante->datos_ocr['monto']) && $comprobante->datos_ocr['monto'] !== null
                                        ? 'Bs ' . number_format((float) $comprobante->datos_ocr['monto'], 2)
                                        : 'No detectado' }}
                                </strong>
                            </div>
                            <div class="d-flex justify-content-between border-bottom border-secondary-subtle py-1">
                                <span>Referencia</span>
                                <strong>{{ $comprobante->datos_ocr['referencia'] ?? 'No detectada' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between border-bottom border-secondary-subtle py-1">
                                <span>Fecha</span>
                                <strong>{{ $comprobante->datos_ocr['fecha'] ?? 'No detectada' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span>Banco</span>
                                <strong>{{ $comprobante->datos_ocr['banco'] ?? 'No detectado' }}</strong>
                            </div>
                        </div>
                    </div>
                    @endif

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