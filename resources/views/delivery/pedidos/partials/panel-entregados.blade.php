{{-- =====================================================
        PANEL ENTREGADOS — SOLO FECHA SELECCIONADA
    ====================================================== --}}

    <section
        id="panel-entregados"
        class="delivery-my-tab-panel">

        <div class="delivery-my-section delivery-my-section-delivered">

            <div class="delivery-my-section-header">

                <div>
                    <span class="delivery-my-section-label delivered">
                        FECHA SELECCIONADA
                    </span>

                    <h2>
                        <i class="bi bi-check2-circle"></i>
                        Pedidos entregados
                    </h2>

                    <p>
                        Solo se muestran las entregas finalizadas en la fecha elegida arriba.
                    </p>
                </div>

                <div class="delivery-my-section-count delivered">
                    {{ $cantidadEntregados }}
                </div>

            </div>


            @if($pedidosEntregados->isEmpty())

            <div
                class="delivery-my-empty delivery-my-empty-delivered">

                <div class="delivery-my-empty-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>

                <h3>
                    No hay entregas para esta fecha
                </h3>

                <p>
                    Selecciona otra fecha en el filtro superior para consultar
                    tus entregas finalizadas.
                </p>

            </div>

            @else

            <div class="row g-3">

                @foreach($pedidosEntregados as $asignacion)

                @php
                    $pedido = $asignacion->pedido;
                    $cliente = $pedido?->user;
                @endphp

                <div
                    class="col-12 col-md-6 col-xl-4">

                    <article
                        class="delivery-delivered-card delivery-delivered-card-static"
                        data-delivery-animate>

                        <div class="delivery-delivered-top">

                            <div class="delivery-delivered-number">

                                <div>
                                    <i class="bi bi-check-lg"></i>
                                </div>

                                <span>
                                    Pedido #{{ $pedido->id }}
                                </span>

                            </div>

                            <span class="delivery-delivered-badge">
                                Entregado
                            </span>

                        </div>


                        <div class="delivery-delivered-client">

                            <div class="delivery-delivered-avatar">

                                @if($cliente?->foto_perfil_url)

                                    <img
                                        src="{{ $cliente->foto_perfil_url }}"
                                        alt="Foto de {{ $cliente->name }}">

                                @else

                                    {{ strtoupper(substr($cliente->name ?? 'C', 0, 1)) }}

                                @endif

                            </div>

                            <div>

                                <span>Cliente</span>

                                <strong>
                                    {{ $cliente->name ?? 'Cliente eliminado' }}
                                </strong>

                            </div>

                        </div>


                        <div class="delivery-delivered-address">

                            <i class="bi bi-geo-alt-fill"></i>

                            <span>
                                {{ $pedido->direccion_entrega ?? 'Sin dirección' }}
                            </span>

                        </div>


                        <div class="delivery-delivered-bottom">

                            <div>

                                <strong>
                                    Ruta:
                                    Bs {{ number_format((float) ($pedido->tarifa_delivery ?? 0), 2) }}
                                </strong>

                                <small class="delivery-delivered-financial">

                                    Mi comisión:
                                    Bs {{ number_format((float) ($pedido->monto_delivery ?? 0), 2) }}

                                    · Restaurante:
                                    Bs {{ number_format((float) ($pedido->monto_restaurante_delivery ?? 0), 2) }}

                                </small>

                            </div>

                            <a
                                href="{{ route('delivery.pedidos.show', $pedido->id) }}"
                                class="delivery-delivered-view-link"
                                data-delivery-interactive>

                                <i class="bi bi-eye-fill"></i>
                                Ver detalles

                            </a>

                        </div>

                    </article>

                </div>

                @endforeach

            </div>

            @endif

        </div>

    </section>
