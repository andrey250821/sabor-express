@extends('layouts.delivery')

@section('title', 'Mis pedidos')
@section('section', 'Mis entregas')
@section('heading', 'Mis pedidos')

@section('content')

@php
$asignaciones = $asignaciones ?? collect();

/*
|--------------------------------------------------------------------------
| FILTRO DE PEDIDOS
|--------------------------------------------------------------------------
*/

$pedidosTomados = $asignaciones
->filter(function ($asignacion) {
return in_array($asignacion->estado, [
'aceptado',
'en_camino'
]);
});

$pedidosEntregados = $asignaciones
->filter(function ($asignacion) {
return $asignacion->estado === 'entregado';
});

$cantidadTomados = $pedidosTomados->count();
$cantidadEntregados = $pedidosEntregados->count();
$cantidadTotal = $asignaciones->count();
@endphp


<div class="delivery-my-orders">


    {{-- =====================================================
        ENCABEZADO
    ====================================================== --}}

    <div class="delivery-my-orders-header">

        <div class="delivery-my-orders-title">

            <div class="delivery-my-orders-icon">
                <i class="bi bi-bicycle"></i>
            </div>

            <div>

                <span class="delivery-section-label">
                    CENTRO DE ENTREGAS
                </span>

                <h1>
                    Mis pedidos
                </h1>

                <p>
                    Administra los pedidos que has tomado y consulta
                    tus entregas completadas.
                </p>

            </div>

        </div>


        <a
            href="{{ route('delivery.pedidos.index') }}"
            class="delivery-my-orders-new">
            <i class="bi bi-plus-lg"></i>
            Tomar nuevo pedido
        </a>

    </div>



    {{-- =====================================================
        RESUMEN
    ====================================================== --}}

    <div class="row g-3 mb-4">

        {{-- TOMADOS --}}

        <div class="col-12 col-md-4">

            <div class="delivery-my-stat delivery-my-stat-active">

                <div class="delivery-my-stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>

                <div>

                    <span>
                        Pedidos tomados
                    </span>

                    <strong>
                        {{ $cantidadTomados }}
                    </strong>

                    <small>
                        En proceso de entrega
                    </small>

                </div>

            </div>

        </div>


        {{-- ENTREGADOS --}}

        <div class="col-12 col-md-4">

            <div class="delivery-my-stat delivery-my-stat-success">

                <div class="delivery-my-stat-icon">
                    <i class="bi bi-check2-circle"></i>
                </div>

                <div>

                    <span>
                        Entregados
                    </span>

                    <strong>
                        {{ $cantidadEntregados }}
                    </strong>

                    <small>
                        Entregas completadas
                    </small>

                </div>

            </div>

        </div>


        {{-- TOTAL --}}

        <div class="col-12 col-md-4">

            <div class="delivery-my-stat delivery-my-stat-total">

                <div class="delivery-my-stat-icon">
                    <i class="bi bi-bar-chart-line"></i>
                </div>

                <div>

                    <span>
                        Total de pedidos
                    </span>

                    <strong>
                        {{ $cantidadTotal }}
                    </strong>

                    <small>
                        Historial de entregas
                    </small>

                </div>

            </div>

        </div>

    </div>



    {{-- =====================================================
        MENSAJES
    ====================================================== --}}

    @if(session('success'))

    <div class="delivery-my-alert delivery-my-alert-success">

        <div class="delivery-my-alert-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>

        <div>
            <strong>Operación realizada</strong>

            <span>
                {{ session('success') }}
            </span>
        </div>

    </div>

    @endif


    @if(session('error'))

    <div class="delivery-my-alert delivery-my-alert-error">

        <div class="delivery-my-alert-icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>

        <div>
            <strong>Ocurrió un problema</strong>

            <span>
                {{ session('error') }}
            </span>
        </div>

    </div>

    @endif



    {{-- =====================================================
        PEDIDOS TOMADOS
    ====================================================== --}}

    <div class="delivery-my-section">

        <div class="delivery-my-section-header">

            <div>

                <span class="delivery-my-section-label">
                    EN PROCESO
                </span>

                <h2>
                    <i class="bi bi-bicycle"></i>
                    Pedidos tomados
                </h2>

                <p>
                    Pedidos que todavía debes entregar.
                </p>

            </div>


            <div class="delivery-my-section-count active">
                {{ $cantidadTomados }}
            </div>

        </div>



        @if($pedidosTomados->isEmpty())

        <div class="delivery-my-empty delivery-my-empty-active">

            <div class="delivery-my-empty-icon">
                <i class="bi bi-bicycle"></i>
            </div>

            <h3>
                No tienes pedidos pendientes
            </h3>

            <p>
                Actualmente no tienes pedidos tomados.
                Puedes revisar los pedidos disponibles y elegir una nueva entrega.
            </p>

            <a
                href="{{ route('delivery.pedidos.index') }}"
                class="delivery-my-empty-btn">
                <i class="bi bi-box-seam"></i>
                Ver pedidos disponibles
            </a>

        </div>

        @else

        <div class="row g-4">

            @foreach($pedidosTomados as $asignacion)

            @php
            $pedido = $asignacion->pedido;
            $cliente = $pedido->user;

            $cantidadProductos = $pedido->detallePedidos
            ->sum('cantidad');
            @endphp

            <div class="col-12 col-lg-6">

                <article class="delivery-my-order-card">

                    {{-- CABECERA --}}

                    <div class="delivery-my-order-header">

                        <div class="delivery-my-order-number">

                            <div class="delivery-my-order-number-icon">
                                <i class="bi bi-receipt"></i>
                            </div>

                            <div>

                                <span>
                                    Pedido
                                </span>

                                <strong>
                                    #{{ $pedido->id }}
                                </strong>

                            </div>

                        </div>


                        @if($asignacion->estado === 'aceptado')

                        <span class="delivery-my-status accepted">
                            <span></span>
                            Aceptado
                        </span>

                        @elseif($asignacion->estado === 'en_camino')

                        <span class="delivery-my-status route">
                            <span></span>
                            En camino
                        </span>

                        @endif

                    </div>



                    {{-- CLIENTE --}}

                    <div class="delivery-my-client">

                        <div class="delivery-my-avatar">
                            {{ strtoupper(substr($cliente->name ?? 'C', 0, 1)) }}
                        </div>

                        <div>

                            <span>
                                Cliente
                            </span>

                            <strong>
                                {{ $cliente->name ?? 'Cliente eliminado' }}
                            </strong>

                            @if(!empty($cliente->telefono))

                            <small>
                                <i class="bi bi-telephone"></i>
                                {{ $cliente->telefono }}
                            </small>

                            @endif

                        </div>

                    </div>



                    {{-- INFORMACIÓN DEL PEDIDO --}}

                    <div class="delivery-my-order-info">

                        <div class="delivery-my-info-item">

                            <i class="bi bi-basket3"></i>

                            <div>

                                <span>
                                    Productos
                                </span>

                                <strong>
                                    {{ $cantidadProductos }}
                                    {{ $cantidadProductos == 1 ? 'unidad' : 'unidades' }}
                                </strong>

                            </div>

                        </div>


                        <div class="delivery-my-info-item">

                            <i class="bi bi-cash-stack"></i>

                            <div>

                                <span>
                                    Total
                                </span>

                                <strong>
                                    Bs {{ number_format($pedido->total, 2) }}
                                </strong>

                            </div>

                        </div>

                    </div>



                    {{-- DIRECCIÓN --}}

                    <div class="delivery-my-address">

                        <div class="delivery-my-address-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>

                        <div>

                            <span>
                                Dirección de entrega
                            </span>

                            <strong>
                                {{ $pedido->direccion_entrega ?? 'Sin dirección registrada' }}
                            </strong>

                        </div>

                    </div>



                    {{-- REFERENCIA --}}

                    @if(!empty($pedido->referencia_delivery))

                    <div class="delivery-my-reference">

                        <i class="bi bi-signpost-2-fill"></i>

                        <div>

                            <strong>
                                Referencia
                            </strong>

                            <span>
                                {{ $pedido->referencia_delivery }}
                            </span>

                        </div>

                    </div>

                    @endif



                    {{-- OBSERVACIÓN --}}

                    @if(!empty($pedido->observacion_cliente))

                    <div class="delivery-my-observation">

                        <i class="bi bi-chat-left-text-fill"></i>

                        <div>

                            <strong>
                                Observación del cliente
                            </strong>

                            <span>
                                {{ $pedido->observacion_cliente }}
                            </span>

                        </div>

                    </div>

                    @endif



                    {{-- FECHA --}}

                    <div class="delivery-my-order-date">

                        <i class="bi bi-clock-history"></i>

                        Pedido realizado el

                        {{ $pedido->created_at->format('d/m/Y') }}

                        a las

                        {{ $pedido->created_at->format('H:i') }}

                    </div>



                    {{-- ACCIONES --}}

                    <div class="delivery-my-actions">

                        <a
                            href="{{ route('delivery.pedidos.show', $pedido->id) }}"
                            class="delivery-my-detail-btn">
                            <i class="bi bi-eye"></i>
                            Ver detalles
                        </a>


                        @if($asignacion->estado === 'aceptado')

                        <form
                            action="{{ route('delivery.pedidos.iniciar', $pedido->id) }}"
                            method="POST">

                            @csrf
                            @method('PUT')

                            <button
                                type="submit"
                                class="delivery-my-start-btn">
                                <i class="bi bi-truck"></i>
                                Iniciar entrega
                            </button>

                        </form>

                        @elseif($asignacion->estado === 'en_camino')

                        <form
                            action="{{ route('delivery.pedidos.entregar', $pedido->id) }}"
                            method="POST"
                            onsubmit="return confirm('¿Confirmas que el pedido #{{ $pedido->id }} ya fue entregado al cliente?')">

                            @csrf
                            @method('PUT')

                            <button
                                type="submit"
                                class="delivery-my-deliver-btn">
                                <i class="bi bi-check-circle"></i>
                                Marcar como entregado
                            </button>

                        </form>

                        @endif

                    </div>

                </article>

            </div>

            @endforeach

        </div>

        @endif

    </div>



    {{-- =====================================================
        PEDIDOS ENTREGADOS
    ====================================================== --}}

    <div class="delivery-my-section delivery-my-section-delivered">

        <div class="delivery-my-section-header">

            <div>

                <span class="delivery-my-section-label delivered">
                    HISTORIAL
                </span>

                <h2>
                    <i class="bi bi-check2-circle"></i>
                    Pedidos entregados
                </h2>

                <p>
                    Pedidos que ya fueron entregados correctamente.
                </p>

            </div>


            <div class="delivery-my-section-count delivered">
                {{ $cantidadEntregados }}
            </div>

        </div>



        @if($pedidosEntregados->isEmpty())

        <div class="delivery-my-empty delivery-my-empty-delivered">

            <div class="delivery-my-empty-icon">
                <i class="bi bi-check2-circle"></i>
            </div>

            <h3>
                Todavía no tienes entregas completadas
            </h3>

            <p>
                Los pedidos aparecerán aquí automáticamente
                después de marcarlos como entregados.
            </p>

        </div>

        @else

        <div class="row g-3">

            @foreach($pedidosEntregados as $asignacion)

            @php
            $pedido = $asignacion->pedido;
            $cliente = $pedido->user;
            @endphp

            <div class="col-12 col-md-6 col-xl-4">

                <article class="delivery-delivered-card">

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
                            {{ strtoupper(substr($cliente->name ?? 'C', 0, 1)) }}
                        </div>

                        <div>

                            <span>
                                Cliente
                            </span>

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

                        <strong>
                            Bs {{ number_format($pedido->total, 2) }}
                        </strong>

                        <a
                            href="{{ route('delivery.pedidos.show', $pedido->id) }}"
                            class="delivery-delivered-detail">
                            Ver detalle
                            <i class="bi bi-arrow-right"></i>
                        </a>

                    </div>

                </article>

            </div>

            @endforeach

        </div>

        @endif

    </div>

</div>

@endsection