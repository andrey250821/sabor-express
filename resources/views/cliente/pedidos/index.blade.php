@extends('layouts.cliente')

@section('content')

<div class="container mt-4">

    <h2 class="mb-4">
        <i class="bi bi-bag-check"></i>
        Mis pedidos
    </h2>

    @if($pedidos->count() > 0)

    <div class="table-responsive">

        <table class="table table-bordered align-middle">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Comprobante</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                @foreach($pedidos as $pedido)

                <tr>

                    <td>
                        #{{ $pedido->id }}
                    </td>

                    <td>
                        {{ $pedido->created_at->format('d/m/Y H:i') }}
                    </td>

                    <td>
                        Bs. {{ number_format($pedido->total, 2) }}
                    </td>

                    <td>
                        {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                    </td>

                    <td>

                        @if($pedido->comprobantePago)

                        @if($pedido->comprobantePago->estado === 'aprobado')

                        <span class="badge bg-success">
                            Aprobado
                        </span>

                        @elseif($pedido->comprobantePago->estado === 'rechazado')

                        <span class="badge bg-danger">
                            Rechazado
                        </span>

                        @else

                        <span class="badge bg-warning text-dark">
                            Pendiente
                        </span>

                        @endif

                        @else

                        <span class="badge bg-secondary">
                            Sin comprobante
                        </span>

                        @endif

                    </td>

                    <td>

                        <a
                            href="{{ route('cliente.pedidos.show', $pedido->id) }}"
                            class="btn btn-primary btn-sm">

                            <i class="bi bi-eye"></i>
                            Ver detalle

                        </a>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

    @else

    <div class="alert alert-info">

        <i class="bi bi-info-circle"></i>

        Todavía no tienes pedidos registrados.

    </div>

    <a
        href="{{ route('cliente.productos.index') }}"
        class="btn btn-primary">

        <i class="bi bi-shop"></i>
        Ver productos

    </a>

    @endif

</div>

@endsection