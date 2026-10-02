<div class="col-12 col-lg-6 col-xl-4">

    <article class="cocinero-detail-card h-100">

        <div class="cocinero-detail-card-header">

            <div>
                <div class="cocinero-detail-card-icon">
                    @if($pedido->estado === 'pagado')
                        <i class="bi bi-clock-history"></i>
                    @elseif($pedido->estado === 'preparando')
                        <i class="bi bi-fire"></i>
                    @else
                        <i class="bi bi-check-circle"></i>
                    @endif
                </div>

                <div>
                    <h3>Pedido #{{ $pedido->id }}</h3>
                    <span>{{ $pedido->created_at?->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            @if($pedido->estado === 'pagado')
                <span class="cocinero-order-status pending">En cola</span>
            @elseif($pedido->estado === 'preparando')
                <span class="cocinero-order-status preparing">Preparando</span>
            @elseif($pedido->estado === 'listo')
                <span class="cocinero-order-status ready">Listo</span>
            @endif

        </div>


        <div class="p-3">

            <div class="cocinero-client-box p-0 mb-3">
                <div class="cocinero-client-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>

                <div class="cocinero-client-info">
                    <strong>{{ $pedido->user->name ?? 'Cliente' }}</strong>
                    <span>
                        <i class="bi bi-basket-fill"></i>
                        {{ $pedido->detallePedidos->sum('cantidad') }} productos
                    </span>
                </div>
            </div>


            <div class="cocinero-products-list p-0">

                @foreach($pedido->detallePedidos as $detalle)

                    <div class="cocinero-product-item">

                        <div class="cocinero-product-image">
                            @if($detalle->producto?->imagen)
                                <img
                                    src="{{ asset('storage/' . $detalle->producto->imagen) }}"
                                    alt="{{ $detalle->producto->nombre }}">
                            @else
                                <i class="bi bi-image"></i>
                            @endif
                        </div>

                        <div class="cocinero-product-info">
                            <h4>{{ $detalle->producto->nombre ?? 'Producto' }}</h4>
                            <span>
                                {{ $detalle->cantidad }} ×
                                <strong>
                                    Bs {{ number_format($detalle->precio ?? 0, 2) }}
                                </strong>
                            </span>
                        </div>

                        <div class="cocinero-product-price">
                            <strong>
                                Bs {{ number_format($detalle->subtotal ?? 0, 2) }}
                            </strong>
                        </div>

                    </div>

                @endforeach

            </div>


            <div class="cocinero-detail-total mt-3">
                <span>Total</span>
                <strong>Bs {{ number_format($pedido->total ?? 0, 2) }}</strong>
            </div>

        </div>


        <div class="p-3 pt-0 mt-auto">

            <a
                href="{{ route('cocinero.pedidos.show', $pedido->id) }}"
                class="btn cocinero-btn-secondary w-100 mb-2">
                <i class="bi bi-eye"></i>
                Ver pedido
            </a>


            @if($tipo === 'pendiente' && $pedido->estado === 'pagado')

                <form
                    action="{{ route('cocinero.pedidos.preparar', $pedido->id) }}"
                    method="POST"
                    data-confirm="¿Comenzar a preparar el pedido #{{ $pedido->id }}?"
                    data-loading>
                    @csrf
                    @method('PUT')

                    <button type="submit" class="btn cocinero-btn-preparar w-100">
                        <i class="bi bi-fire"></i>
                        Comenzar a preparar
                    </button>
                </form>

            @elseif($tipo === 'preparando' && $pedido->estado === 'preparando')

                <form
                    action="{{ route('cocinero.pedidos.listo', $pedido->id) }}"
                    method="POST"
                    data-confirm="¿Marcar el pedido #{{ $pedido->id }} como listo?"
                    data-loading>
                    @csrf
                    @method('PUT')

                    <button type="submit" class="btn cocinero-btn-listo w-100">
                        <i class="bi bi-check-lg"></i>
                        Marcar como listo
                    </button>
                </form>

            @elseif($tipo === 'listo' && $pedido->estado === 'listo')

                <div class="cocinero-ready-label w-100">
                    <i class="bi bi-check-circle-fill"></i>
                    Pedido listo para el siguiente paso
                </div>

            @endif

        </div>

    </article>

</div>
