@extends('layouts.cocinero')

@section('title', 'Pedido #' . $pedido->id)

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">

    <i class="bi bi-check-circle-fill"></i>

    {{ session('success') }}

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert">
    </button>

</div>
@endif


@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">

    <i class="bi bi-exclamation-triangle-fill"></i>

    {{ session('error') }}

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert">
    </button>

</div>
@endif
<div class="container-fluid">

    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="mb-1">
                Pedido #{{ $pedido->id }}
            </h1>

            <p class="text-muted mb-0">
                Detalle y preparación del pedido
            </p>
        </div>

        <a
            href="{{ route('cocinero.pedidos.index') }}"
            class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Volver a pedidos
        </a>

    </div>


    <div class="row g-4">

        {{-- INFORMACIÓN DEL PEDIDO --}}
        <div class="col-lg-8">

            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="mb-0">
                        <i class="bi bi-bag"></i>
                        Productos del pedido
                    </h5>

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover mb-0">

                            <thead>

                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>

                            </thead>


                            <tbody>

                                @foreach($pedido->detallePedidos as $detalle)

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
                                        Bs. {{ number_format($detalle->precio, 2) }}
                                    </td>

                                    <td class="text-end">
                                        Bs. {{ number_format($detalle->subtotal, 2) }}
                                    </td>

                                </tr>

                                @endforeach

                            </tbody>


                            <tfoot>

                                <tr>

                                    <th colspan="3" class="text-end">
                                        TOTAL
                                    </th>

                                    <th class="text-end">
                                        Bs. {{ number_format($pedido->total, 2) }}
                                    </th>

                                </tr>

                            </tfoot>

                        </table>

                    </div>

                </div>

            </div>


            {{-- OBSERVACIÓN DEL CLIENTE --}}
            @if($pedido->observacion_cliente)

            <div class="card shadow-sm mt-4">

                <div class="card-header">

                    <h5 class="mb-0">
                        <i class="bi bi-chat-left-text"></i>
                        Observación del cliente
                    </h5>

                </div>

                <div class="card-body">

                    <p class="mb-0">
                        {{ $pedido->observacion_cliente }}
                    </p>

                </div>

            </div>

            @endif

        </div>


        {{-- INFORMACIÓN LATERAL --}}
        <div class="col-lg-4">

            {{-- ESTADO --}}
            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <h5 class="mb-0">
                        <i class="bi bi-clipboard-check"></i>
                        Estado
                    </h5>

                </div>

                <div class="card-body text-center">

                    @if($pedido->estado === 'pagado')

                    <span class="badge bg-primary fs-6">
                        Pagado
                    </span>

                    @elseif($pedido->estado === 'preparando')

                    <span class="badge bg-warning text-dark fs-6">
                        Preparando
                    </span>

                    @elseif($pedido->estado === 'listo')

                    <span class="badge bg-success fs-6">
                        Listo
                    </span>

                    @else

                    <span class="badge bg-secondary fs-6">
                        {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                    </span>

                    @endif

                </div>

            </div>


            {{-- CLIENTE --}}
            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <h5 class="mb-0">
                        <i class="bi bi-person"></i>
                        Cliente
                    </h5>

                </div>

                <div class="card-body">

                    <p class="mb-2">
                        <strong>Nombre:</strong>
                        {{ $pedido->user->name ?? 'Sin cliente' }}
                    </p>

                    <p class="mb-0">
                        <strong>Pedido realizado:</strong><br>
                        {{ $pedido->created_at->format('d/m/Y H:i') }}
                    </p>

                </div>

            </div>


            {{-- ACCIONES --}}
            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="mb-0">
                        <i class="bi bi-gear"></i>
                        Acciones
                    </h5>

                </div>

                <div class="card-body">

                    @if($pedido->estado === 'pagado')

                    <form
                        method="POST"
                        action="{{ route('cocinero.pedidos.preparar', $pedido->id) }}">

                        @csrf
                        @method('PUT')

                        <button
                            type="submit"
                            class="btn btn-warning w-100">
                            <i class="bi bi-fire"></i>
                            Comenzar preparación
                        </button>

                    </form>


                    @elseif($pedido->estado === 'preparando')

                    <form
                        method="POST"
                        action="{{ route('cocinero.pedidos.listo', $pedido->id) }}">

                        @csrf
                        @method('PUT')

                        <button
                            type="submit"
                            class="btn btn-success w-100">
                            <i class="bi bi-check-circle"></i>
                            Marcar como listo
                        </button>

                    </form>


                    @elseif($pedido->estado === 'listo')

                    <div class="alert alert-success mb-0">

                        <i class="bi bi-check-circle-fill"></i>

                        Este pedido ya está listo para ser entregado.

                    </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@endsection