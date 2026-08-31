<div class="col-12 col-lg-6 col-xl-4">

    <div class="card shadow-sm h-100">

        {{-- CABECERA --}}
        <div class="card-header bg-white">

            <div class="d-flex justify-content-between align-items-center">

                <strong>
                    Pedido #{{ $pedido->id }}
                </strong>

                @if($pedido->estado === 'pagado')

                <span class="badge bg-warning text-dark">
                    <i class="bi bi-credit-card"></i>
                    Pagado
                </span>

                @elseif($pedido->estado === 'preparando')

                <span class="badge bg-primary">
                    <i class="bi bi-fire"></i>
                    Preparando
                </span>

                @elseif($pedido->estado === 'listo')

                <span class="badge bg-success">
                    <i class="bi bi-check-circle"></i>
                    Listo
                </span>

                @endif

            </div>

        </div>


        {{-- CONTENIDO --}}
        <div class="card-body">

            {{-- CLIENTE --}}
            <div class="mb-3">

                <small class="text-muted">
                    Cliente
                </small>

                <div class="fw-semibold">

                    <i class="bi bi-person"></i>

                    {{ $pedido->user->name ?? 'Cliente' }}

                </div>

            </div>


            {{-- FECHA --}}
            <div class="mb-3">

                <small class="text-muted">
                    Fecha del pedido
                </small>

                <div>

                    <i class="bi bi-calendar3"></i>

                    {{ $pedido->created_at->format('d/m/Y H:i') }}

                </div>

            </div>


            {{-- PRODUCTOS --}}
            <div class="mb-3">

                <small class="text-muted">
                    Productos
                </small>

                <ul class="list-group list-group-flush mt-2">

                    @foreach($pedido->detallePedidos as $detalle)

                    <li class="list-group-item px-0">

                        <div class="d-flex justify-content-between gap-2">

                            <span>

                                <strong>
                                    {{ $detalle->cantidad }} ×
                                </strong>

                                {{ $detalle->producto->nombre ?? 'Producto' }}

                            </span>

                            <strong class="text-nowrap">

                                Bs.
                                {{ number_format($detalle->subtotal, 2) }}

                            </strong>

                        </div>

                    </li>

                    @endforeach

                </ul>

            </div>


            {{-- TOTAL --}}
            <div class="border-top pt-3">

                <div class="d-flex justify-content-between">

                    <span class="fw-semibold">
                        Total
                    </span>

                    <strong class="fs-5">

                        Bs.
                        {{ number_format($pedido->total, 2) }}

                    </strong>

                </div>

            </div>

        </div>


        {{-- FOOTER / ACCIONES --}}
        <div class="card-footer bg-white">

            {{-- VER PEDIDO --}}
            <a
                href="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                class="btn btn-dark w-100 mb-2">

                <i class="bi bi-eye"></i>

                Ver pedido

            </a>


            {{-- PEDIDO PAGADO --}}
            @if($tipo === 'pendiente' && $pedido->estado === 'pagado')

            <form
                action="{{ route('cocinero.pedidos.preparar', $pedido->id) }}"
                method="POST">

                @csrf

                @method('PUT')

                <button
                    type="submit"
                    class="btn btn-warning w-100">

                    <i class="bi bi-fire"></i>

                    Comenzar a preparar

                </button>

            </form>


            {{-- PEDIDO EN PREPARACIÓN --}}
            @elseif($tipo === 'preparando' && $pedido->estado === 'preparando')

            <form
                action="{{ route('cocinero.pedidos.listo', $pedido->id) }}"
                method="POST">

                @csrf

                @method('PUT')

                <button
                    type="submit"
                    class="btn btn-success w-100">

                    <i class="bi bi-check-circle"></i>

                    Marcar como listo

                </button>

            </form>


            {{-- PEDIDO LISTO --}}
            @elseif($tipo === 'listo' && $pedido->estado === 'listo')

            <div class="alert alert-success mb-0 text-center">

                <i class="bi bi-check-circle-fill"></i>

                <strong>
                    Pedido listo
                </strong>

                <div class="small mt-1">
                    Disponible para delivery
                </div>

            </div>

            @endif

        </div>

    </div>

</div>