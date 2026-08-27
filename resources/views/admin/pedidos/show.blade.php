@extends('layouts.admin')

@section('content')

<div class="container-fluid px-0 pedido-detalle">

    {{-- ENCABEZADO --}}
    <div class="d-flex flex-column flex-md-row
                justify-content-between
                align-items-md-center
                gap-2 mb-4">

        <div>
            <h2 class="fw-bold text-white mb-1">
                <i class="bi bi-receipt text-danger"></i>
                Pedido #{{ $pedido->id }}
            </h2>

            <p class="text-secondary mb-0">
                Detalle completo del pedido
            </p>
        </div>

        <a href="{{ route('admin.pedidos.index') }}"
            class="btn btn-outline-light">

            <i class="bi bi-arrow-left"></i>
            Volver a pedidos

        </a>

    </div>


    {{-- INFORMACIÓN DEL PEDIDO --}}
    <div class="card pedido-card shadow mb-4">

        <div class="card-body p-3 p-md-4">

            <div class="row g-4">

                {{-- CLIENTE --}}
                <div class="col-12 col-md-6">

                    <div class="pedido-info">

                        <h5 class="fw-bold text-white mb-3">

                            <i class="bi bi-person-circle text-danger"></i>
                            Cliente

                        </h5>

                        <p class="text-white mb-2">

                            <strong>Nombre:</strong>

                            {{ $pedido->user->name }}

                        </p>

                        <p class="text-secondary mb-0">

                            <strong>Teléfono:</strong>

                            {{ $pedido->user->telefono ?? 'Sin teléfono' }}

                        </p>

                    </div>

                </div>


                {{-- ENTREGA --}}
                <div class="col-12 col-md-6">

                    <div class="pedido-info">

                        <h5 class="fw-bold text-white mb-3">

                            <i class="bi bi-geo-alt text-danger"></i>
                            Dirección de entrega

                        </h5>

                        <p class="text-white mb-2">

                            {{ $pedido->direccion_entrega }}

                        </p>


                        @if($pedido->referencia_delivery)

                        <p class="text-secondary mb-0">

                            <strong>Referencia:</strong>

                            {{ $pedido->referencia_delivery }}

                        </p>

                        @endif

                    </div>

                </div>

            </div>


            <hr class="pedido-divider my-4">


            {{-- PRODUCTOS --}}
            <div>

                <h5 class="fw-bold text-white mb-3">

                    <i class="bi bi-bag text-danger"></i>
                    Productos del pedido

                </h5>


                {{-- RESPONSIVE --}}
                <div class="table-responsive">

                    <table class="table pedido-table align-middle mb-0">

                        <thead>

                            <tr>

                                <th>Producto</th>

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

                            @foreach($pedido->detallePedidos as $detalle)

                            <tr>

                                <td>

                                    <div class="fw-semibold text-white">

                                        {{ $detalle->producto->nombre }}

                                    </div>

                                </td>


                                <td class="text-center text-white">

                                    {{ $detalle->cantidad }}

                                </td>


                                <td class="text-end text-white">

                                    Bs {{ number_format($detalle->precio, 2) }}

                                </td>


                                <td class="text-end fw-bold text-white">

                                    Bs {{ number_format($detalle->subtotal, 2) }}

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- TOTAL --}}
                <div class="d-flex justify-content-end mt-4">

                    <div class="pedido-total">

                        <span class="text-secondary">
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

</div>

@endsection