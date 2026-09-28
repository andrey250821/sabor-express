@extends('layouts.delivery')

@section('title', 'Cola de pedidos')
@section('section', 'Gestión de pedidos')
@section('heading', 'Cola de pedidos')

@section('content')

<div class="delivery-orders-page">

    <div class="delivery-orders-header">

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

        <div class="delivery-orders-counter">

            <div class="delivery-orders-counter-icon">
                <i class="bi bi-clock-history"></i>
            </div>

            <div>
                <span>Pedidos en cola</span>
                <strong>{{ $pedidosEnCola }}</strong>
            </div>

        </div>

    </div>


    <div class="delivery-orders-info">

        <div class="delivery-orders-info-icon">
            <i class="bi bi-lightning-charge-fill"></i>
        </div>

        <div class="delivery-orders-info-content">
            <strong>Asignación automática</strong>

            <span>
                El sistema respeta el orden de la cola.
                Cuando quedes libre, recibirás obligatoriamente el siguiente pedido disponible.
            </span>
        </div>

        <div class="delivery-orders-info-status">
            <span class="delivery-live-dot"></span>
            Automático
        </div>

    </div>


    <div class="delivery-orders-summary">

        <div>
            <span class="delivery-summary-label">
                ESTADO DE LA COLA
            </span>

            <h2>
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

            <article class="delivery-show-card text-center p-5">

                <div class="delivery-show-card-icon mx-auto mb-4">
                    <i class="bi bi-box-seam"></i>
                </div>

                <div class="display-4 fw-bold mb-2">
                    {{ $pedidosEnCola }}
                </div>

                <h2 class="mb-3">
                    {{ $pedidosEnCola === 1 ? 'pedido en cola' : 'pedidos en cola' }}
                </h2>

                <p class="text-muted mb-4">
                    Estos pedidos ya están listos en cocina y todavía no tienen un Delivery asignado.
                    Los detalles permanecen ocultos hasta que un pedido sea asignado a ti.
                </p>

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

            </article>

        </div>

    </div>

</div>

@endsection
