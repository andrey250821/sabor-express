@extends('layouts.delivery')

@section('title', 'Cola de pedidos')
@section('section', 'Gestión de pedidos')
@section('heading', 'Cola de pedidos')

@section('content')

<div class="delivery-orders-page" data-delivery-live-queue>

    <div class="delivery-orders-header" data-delivery-animate>

        <div class="delivery-orders-heading">

            <div class="delivery-orders-icon">
                <i class="bi bi-box-seam"></i>
            </div>

            <div>
                <span class="delivery-section-label">
                    CENTRO DE ENTREGAS
                </span>

                <h1>
                    Pedidos en cola
                </h1>

                <p>
                    Consulta cuántos pedidos están esperando asignación.
                    Los pedidos se asignan automáticamente y no puedes elegirlos.
                </p>
            </div>

        </div>

        <div class="delivery-orders-actions">

            <button
                type="button"
                class="delivery-queue-refresh"
                id="deliveryQueueRefresh"
                data-delivery-live-refresh
                data-delivery-interactive
                title="Actualizar la cola ahora">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Actualizar</span>
            </button>

            <div class="delivery-orders-counter">
                <i class="bi bi-clock-history"></i>
            </div>

            <div>
                <span>Pedidos en cola</span>
                <strong id="delivery-queue-header-count">{{ $pedidosEnCola }}</strong>
            </div>

        </div>

    </div>


    <div class="delivery-orders-info" data-delivery-animate>

        <div class="delivery-orders-info-icon">
            <i class="bi bi-lightning-charge-fill"></i>
        </div>

        <div class="delivery-orders-info-content">
            <strong>Asignación automática</strong>

            <span>
                El sistema respeta el orden de la cola.
                Cuando quedes libre, recibirás obligatoriamente el siguiente pedido de la cola.
            </span>
        </div>

        <div class="delivery-orders-info-status">
            <span class="delivery-live-dot"></span>
            Automático
        </div>

    </div>


    <div class="delivery-orders-summary" data-delivery-animate>

        <div>
            <span class="delivery-summary-label">
                ESTADO DE LA COLA
            </span>

            <h2 id="delivery-queue-summary">
                {{ $pedidosEnCola > 0 ? 'Hay pedidos esperando asignación' : 'La cola está vacía' }}
            </h2>
        </div>

        <div class="delivery-summary-right">
            <i class="bi bi-shuffle"></i>
            <span>
                Sin selección manual
            </span>
        </div>

    </div>


    <div class="row justify-content-center">

        <div class="col-12 col-lg-8">

            <article class="delivery-show-card text-center p-5 delivery-queue-card" data-delivery-animate>

                <div class="delivery-show-card-icon mx-auto mb-4">
                    <i class="bi bi-box-seam"></i>
                </div>

                <div class="display-4 fw-bold mb-2" id="delivery-queue-main-count">
                    {{ $pedidosEnCola }}
                </div>

                <h2 class="mb-3" id="delivery-queue-main-label">
                    {{ $pedidosEnCola === 1 ? 'pedido en cola' : 'pedidos en cola' }}
                </h2>

                <p class="delivery-queue-description mb-4">
                    Estos pedidos ya están listos en cocina y todavía no tienen un Delivery asignado.
                    Los detalles permanecen ocultos hasta que un pedido sea asignado a ti.
                </p>

                <div id="delivery-queue-state-message">
                    @if($pedidosEnCola > 0)
    
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Cuando termines tu entrega actual, el sistema te asignará automáticamente
                            el siguiente pedido de la cola.
                        </div>
    
                    @else
    
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            No hay pedidos esperando asignación en este momento.
                        </div>
    
                    @endif
                </div>

            </article>

        </div>

    </div>

</div>

@endsection
