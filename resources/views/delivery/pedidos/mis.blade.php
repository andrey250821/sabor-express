@extends('layouts.delivery')

@section('title', 'Mis pedidos')
@section('section', 'Mis entregas')
@section('heading', 'Mis pedidos')

@section('content')

@php
    $asignaciones = $asignaciones ?? collect();

    $pedidosAsignados = $asignaciones->filter(function ($asignacion) {
        return in_array($asignacion->pedido?->estado, [
            'asignado',
            'en_camino',
        ], true);
    });

    $pedidosEntregados = $asignaciones->filter(function ($asignacion) {
        return $asignacion->pedido?->estado === 'entregado';
    });

    $cantidadAsignados = $pedidosAsignados->count();
    $cantidadEntregados = $pedidosEntregados->count();
@endphp

<div
    class="delivery-my-orders"
    data-delivery-live-orders
    data-live-url="{{ route('delivery.pedidos.actualizar', request()->only('fecha')) }}">

    {{-- =====================================================
        ENCABEZADO
    ====================================================== --}}

    <header class="delivery-my-orders-header" data-delivery-animate>

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
                    Revisa únicamente los pedidos correspondientes a la fecha seleccionada.
                    Los pedidos asignados muestran solo la información necesaria hasta que inicies la entrega.
                </p>
            </div>

        </div>

        <a
            href="{{ route('delivery.pedidos.index') }}"
            class="delivery-my-orders-new"
            data-delivery-interactive>

            <i class="bi bi-box-seam"></i>
            Ver cola

        </a>

    </header>


    {{-- =====================================================
        FILTRO ÚNICO POR FECHA
    ====================================================== --}}

    @include('shared.date-filter', [
        'fechaSeleccionada' => $fechaSeleccionada,
        'tituloFecha' => 'Mis pedidos del día seleccionado'
    ])


    {{-- =====================================================
        MENSAJES
    ====================================================== --}}

    @if(session('success'))

    <div class="delivery-my-alert delivery-my-alert-success" data-delivery-animate>

        <div class="delivery-my-alert-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>

        <div>
            <strong>
                Operación realizada
            </strong>

            <span>
                {{ session('success') }}
            </span>
        </div>

    </div>

    @endif


    @if(session('error'))

    <div class="delivery-my-alert delivery-my-alert-error" data-delivery-animate>

        <div class="delivery-my-alert-icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>

        <div>
            <strong>
                Ocurrió un problema
            </strong>

            <span>
                {{ session('error') }}
            </span>
        </div>

    </div>

    @endif


    {{-- =====================================================
        PESTAÑAS
    ====================================================== --}}

    <div class="delivery-my-tabs-wrapper" data-delivery-animate>

        <div
            class="delivery-my-tabs"
            role="tablist"
            aria-label="Mis pedidos">

            <button
                type="button"
                class="delivery-my-tab active"
                id="tab-asignados"
                data-target="panel-asignados"
                role="tab"
                aria-selected="true"
                data-delivery-interactive>

                <i class="bi bi-bicycle"></i>

                <span>
                    Asignados
                </span>

                <strong id="delivery-live-assigned-tab-count">
                    {{ $cantidadAsignados }}
                </strong>

            </button>


            <button
                type="button"
                class="delivery-my-tab"
                id="tab-entregados"
                data-target="panel-entregados"
                role="tab"
                aria-selected="false"
                data-delivery-interactive>

                <i class="bi bi-check2-circle"></i>

                <span>
                    Entregados
                </span>

                <strong id="delivery-live-delivered-tab-count">
                    {{ $cantidadEntregados }}
                </strong>

            </button>

        </div>

    </div>


    {{-- Los paneles se actualizan en segundo plano sin recargar la página. --}}
    @include('delivery.pedidos.partials.panel-asignados')

    @include('delivery.pedidos.partials.panel-entregados')


</div>

@endsection
