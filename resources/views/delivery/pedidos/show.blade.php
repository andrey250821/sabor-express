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


        {{-- VOLVER --}}

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

                            {{ $pedido->direccion_entrega }}

                        </strong>

                    </div>


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

                                    <td colspan="4" class="text-center py-4 text-muted">

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

                        Al tomarlo, el pedido quedará asignado a ti y aparecerá en "Mis pedidos".

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