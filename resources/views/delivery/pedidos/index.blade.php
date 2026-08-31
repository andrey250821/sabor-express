@extends('layouts.delivery')

@section('content')

<div class="container-fluid px-0">

    {{-- ENCABEZADO --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-box-seam me-2"></i>
                Pedidos disponibles
            </h2>

            <p class="text-muted mb-0">
                Pedidos listos para ser tomados y entregados.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge text-bg-primary fs-6 px-3 py-2">
                <i class="bi bi-bag-check me-1"></i>
                {{ $pedidos->count() }} disponibles
            </span>
        </div>

    </div>


    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif


    {{-- PEDIDOS --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-1">
                <i class="bi bi-list-ul me-2"></i>
                Pedidos listos
            </h5>
            <small class="text-muted">
                Selecciona un pedido para consultar sus detalles antes de tomarlo.
            </small>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="text-center">ID</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Dirección</th>
                            <th>Fecha</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($pedidos as $pedido)

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
                                        Bs. {{ number_format($pedido->total, 2) }}
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


                                {{-- FECHA --}}
                                <td>
                                    <strong class="d-block">
                                        {{ $pedido->created_at->format('d/m/Y') }}
                                    </strong>
                                    <small class="text-muted">
                                        {{ $pedido->created_at->format('H:i') }}
                                    </small>
                                </td>


                                {{-- ACCIÓN --}}
                                <td class="text-center">
                                    <a
                                        href="{{ route('delivery.pedidos.show', $pedido->id) }}"
                                        class="btn btn-primary btn-sm">

                                        <i class="bi bi-eye me-1"></i>
                                        Ver detalles

                                    </a>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="py-5">

                                    <div class="text-center text-muted">

                                        <i class="bi bi-inbox display-4 d-block mb-3"></i>

                                        <h5 class="fw-bold">
                                            No hay pedidos disponibles
                                        </h5>

                                        <p class="mb-0">
                                            Actualmente no existen pedidos listos para entregar.
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
