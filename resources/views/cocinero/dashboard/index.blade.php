@extends('layouts.cocinero')

@section('title', 'Pedidos')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="mb-1">
                Pedidos
            </h1>

            <p class="text-muted mb-0">
                Pedidos disponibles para preparación
            </p>
        </div>

    </div>


    @if($pedidos->isEmpty())

    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        No hay pedidos pendientes de preparación.
    </div>

    @else

    <div class="row g-4">

        @foreach($pedidos as $pedido)

        <div class="col-md-6 col-xl-4">

            <div class="card shadow-sm h-100">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <strong>
                        Pedido #{{ $pedido->id }}
                    </strong>

                    <span class="badge
                                @if($pedido->estado === 'pagado')
                                    bg-primary
                                @elseif($pedido->estado === 'preparando')
                                    bg-warning text-dark
                                @elseif($pedido->estado === 'listo')
                                    bg-success
                                @endif
                            ">
                        {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                    </span>

                </div>


                <div class="card-body">

                    <p class="mb-2">
                        <strong>Cliente:</strong>
                        {{ $pedido->user->name ?? 'Sin cliente' }}
                    </p>


                    <p class="mb-2">
                        <strong>Fecha:</strong>
                        {{ $pedido->created_at->format('d/m/Y H:i') }}
                    </p>


                    <hr>


                    <h6>
                        Productos
                    </h6>

                    <ul class="mb-3">

                        @foreach($pedido->detallePedidos as $detalle)

                        <li>
                            {{ $detalle->cantidad }}
                            x
                            {{ $detalle->producto->nombre ?? 'Producto eliminado' }}
                        </li>

                        @endforeach

                    </ul>


                    <p class="mb-0">
                        <strong>Total:</strong>
                        Bs. {{ number_format($pedido->total, 2) }}
                    </p>

                </div>


                <div class="card-footer bg-white">

                    <a
                        href="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                        class="btn btn-primary w-100">
                        <i class="bi bi-eye"></i>
                        Ver pedido
                    </a>

                </div>

            </div>

        </div>

        @endforeach

    </div>

    @endif

</div>

@endsection