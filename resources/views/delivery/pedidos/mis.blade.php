@extends('layouts.delivery')

@section('title', 'Mis pedidos')
@section('section', 'Mis entregas')
@section('heading', 'Mis pedidos')

@section('content')

@php
$asignaciones = $asignaciones ?? collect();

/*
|--------------------------------------------------------------------------
| FILTRO
|--------------------------------------------------------------------------
*/

// Pedidos que todavía están siendo gestionados
$pedidosAsignados = $asignaciones->filter(function ($asignacion) {
return in_array($asignacion->pedido?->estado, [
'asignado',
'en_camino'
], true);
});

// Pedidos completamente entregados
$pedidosEntregados = $asignaciones->filter(function ($asignacion) {
return $asignacion->pedido?->estado === 'entregado';
});

$cantidadAsignados = $pedidosAsignados->count();
$cantidadEntregados = $pedidosEntregados->count();
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
                    Administra tus pedidos asignados y consulta
                    las entregas que ya completaste.
                </p>

            </div>

        </div>

        <a
            href="{{ route('delivery.pedidos.index') }}"
            class="delivery-my-orders-new">

            <i class="bi bi-plus-lg"></i>

            Ver cola de pedidos

        </a>

    </div>


    @include('shared.date-filter', [
        'fechaSeleccionada' => $fechaSeleccionada,
        'tituloFecha' => 'Mis pedidos y entregas'
    ])

    {{-- =====================================================
        RESUMEN
    ====================================================== --}}

    <div class="row g-3 mb-4">

        <div class="col-12 col-md-6">

            <div class="delivery-my-stat delivery-my-stat-active">

                <div class="delivery-my-stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>

                <div>

                    <span>
                        Pedidos asignados
                    </span>

                    <strong>
                        {{ $cantidadAsignados }}
                    </strong>

                    <small>
                        En proceso de entrega
                    </small>

                </div>

            </div>

        </div>


        <div class="col-12 col-md-6">

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

    <div class="delivery-my-alert delivery-my-alert-error">

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

    <div class="delivery-my-tabs-wrapper">

        <div
            class="delivery-my-tabs"
            role="tablist">

            {{-- ASIGNADOS --}}

            <button
                type="button"
                class="delivery-my-tab active"
                id="tab-asignados"
                data-target="panel-asignados"
                role="tab"
                aria-selected="true">

                <i class="bi bi-bicycle"></i>

                <span>
                    Asignados
                </span>

                <strong>
                    {{ $cantidadAsignados }}
                </strong>

            </button>


            {{-- ENTREGADOS --}}

            <button
                type="button"
                class="delivery-my-tab"
                id="tab-entregados"
                data-target="panel-entregados"
                role="tab"
                aria-selected="false">

                <i class="bi bi-check2-circle"></i>

                <span>
                    Entregados
                </span>

                <strong>
                    {{ $cantidadEntregados }}
                </strong>

            </button>

        </div>

    </div>


    {{-- =====================================================
        PANEL TOMADOS
    ====================================================== --}}

    <div
        id="panel-asignados"
        class="delivery-my-tab-panel active">

        <div class="delivery-my-section">

            <div class="delivery-my-section-header">

                <div>

                    <span class="delivery-my-section-label">
                        EN PROCESO
                    </span>

                    <h2>
                        <i class="bi bi-bicycle"></i>
                        Pedidos asignados
                    </h2>

                    <p>
                        Pedidos que todavía debes entregar.
                    </p>

                </div>

                <div class="delivery-my-section-count active">
                    {{ $cantidadAsignados }}
                </div>

            </div>


            @if($pedidosAsignados->isEmpty())

            <div class="delivery-my-empty delivery-my-empty-active">

                <div class="delivery-my-empty-icon">
                    <i class="bi bi-bicycle"></i>
                </div>

                <h3>
                    No tienes pedidos registrados
                </h3>

                <p>
                    Actualmente no tienes pedidos asignados en proceso.
                    La siguiente entrega se asignará automáticamente cuando quedes libre.
                </p>

                <a
                    href="{{ route('delivery.pedidos.index') }}"
                    class="delivery-my-empty-btn">

                    <i class="bi bi-box-seam"></i>

                    Ver cola de pedidos

                </a>

            </div>

            @else

            <div class="row g-4">

                @foreach($pedidosAsignados as $asignacion)

                @php
                $pedido = $asignacion->pedido;
                $cliente = $pedido->user;

                $cantidadProductos = $pedido->detallePedidos
                ->sum('cantidad');
                @endphp

                <div class="col-12 col-lg-6">

                    <article
                        class="delivery-my-order-card pedido-card-clickable"
                        role="link"
                        tabindex="0"
                        onclick="if (!event.target.closest('button, a, form, input, select, textarea')) window.location.href='{{ route('delivery.pedidos.show', $pedido->id) }}';"
                        onkeydown="if ((event.key === 'Enter' || event.key === ' ') && !event.target.closest('button, a, form, input, select, textarea')) { event.preventDefault(); window.location.href='{{ route('delivery.pedidos.show', $pedido->id) }}'; }">

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


                            @if($pedido->estado === 'asignado')

                            <span class="delivery-my-status assigned">
                                <span></span>
                                Asignado
                            </span>

                            @elseif($pedido->estado === 'en_camino')

                            <span class="delivery-my-status route">
                                <span></span>
                                En camino
                            </span>

                            @endif

                        </div>


                        {{-- CLIENTE --}}

                        <div class="delivery-my-client">

                            <div class="delivery-my-avatar">
                                @if($cliente?->foto_perfil_url)
                                    <img
                                        src="{{ $cliente->foto_perfil_url }}"
                                        alt="Foto de {{ $cliente->name }}">
                                @else
                                    {{ strtoupper(substr($cliente->name ?? 'C', 0, 1)) }}
                                @endif
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


                        {{-- INFORMACIÓN --}}

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

                                <i class="bi bi-signpost-split"></i>

                                <div>

                                    <span>
                                        Costo de la ruta
                                        @if($pedido->distancia_delivery_km !== null)
                                            · {{ number_format((float) $pedido->distancia_delivery_km, 2) }} km
                                        @endif
                                    </span>

                                    <strong>
                                        Bs {{ number_format((float) ($pedido->tarifa_delivery ?? 0), 2) }}
                                    </strong>

                                </div>

                            </div>


                            <div class="delivery-my-info-item">

                                <i class="bi bi-person-badge-fill"></i>

                                <div>

                                    <span>
                                        Mi comisión
                                        ({{ number_format((float) ($pedido->porcentaje_delivery ?? 0), 0) }}%)
                                    </span>

                                    <strong>
                                        Bs {{ number_format((float) ($pedido->monto_delivery ?? 0), 2) }}
                                    </strong>

                                </div>

                            </div>


                            <div class="delivery-my-info-item">

                                <i class="bi bi-shop"></i>

                                <div>

                                    <span>
                                        Parte restaurante
                                        ({{ number_format((float) ($pedido->porcentaje_restaurante_delivery ?? 0), 0) }}%)
                                    </span>

                                    <strong>
                                        Bs {{ number_format((float) ($pedido->monto_restaurante_delivery ?? 0), 2) }}
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
                                class="delivery-my-detail-btn"
                                aria-label="Ver detalles del pedido #{{ $pedido->id }}">

                                <i class="bi bi-eye-fill"></i>

                                Ver detalles

                            </a>


                            @if($pedido->estado === 'asignado')

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

                            @elseif($pedido->estado === 'en_camino')

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

    </div>


    {{-- =====================================================
        PANEL ENTREGADOS
    ====================================================== --}}

    <div
        id="panel-entregados"
        class="delivery-my-tab-panel">

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

            @php
            /*
             * El historial se agrupa por la fecha en que la asignación
             * pasó a estado "entregado".
             *
             * Para las entregas finalizadas se utiliza updated_at, que registra la última modificación del registro.
             */
            $pedidosEntregadosPorFecha = $pedidosEntregados
                 ->sortByDesc(function ($asignacion) {
                     return $asignacion->updated_at;
                 })
                 ->groupBy(function ($asignacion) {
                     $fecha = $asignacion->updated_at;

                     return $fecha
                         ? $fecha->toDateString()
                         : $asignacion->pedido->created_at->toDateString();
                 });
            @endphp

            @foreach($pedidosEntregadosPorFecha as $fecha => $pedidosFecha)

            @php
            $fechaCarbon = \Carbon\Carbon::parse($fecha)->locale('es');

            if ($fechaCarbon->isToday()) {
                $tituloFecha = 'Hoy';
                $subtituloFecha = 'Entregas realizadas hoy';
                $fechaHoy = true;
            } elseif ($fechaCarbon->isYesterday()) {
                $tituloFecha = 'Ayer';
                $subtituloFecha = 'Entregas realizadas ayer';
                $fechaHoy = false;
            } elseif ($fechaCarbon->isSameDay(now()->subDays(2))) {
                $tituloFecha = 'Anteayer';
                $subtituloFecha = 'Entregas realizadas anteayer';
                $fechaHoy = false;
            } else {
                $tituloFecha = ucfirst($fechaCarbon->translatedFormat('l, d \\d\\e F \\d\\e Y'));
                $subtituloFecha = 'Entregas realizadas en esta fecha';
                $fechaHoy = false;
            }

            $collapseId = 'entregados-' . str_replace('-', '', $fecha);
            @endphp

            <section class="delivery-delivered-date-section mb-4">

                <div class="delivery-delivered-date-header">

                    <div>
                        <span class="delivery-my-section-label delivered">
                            HISTORIAL DE ENTREGAS
                        </span>

                        <h3 class="delivery-delivered-date-title">
                            <i class="bi bi-calendar-check"></i>
                            {{ $tituloFecha }}
                        </h3>

                        <p class="delivery-delivered-date-subtitle">
                            {{ $subtituloFecha }}
                        </p>
                    </div>

                    <div class="delivery-delivered-date-actions">

                        <span class="delivery-my-section-count delivered">
                            {{ $pedidosFecha->count() }}
                        </span>

                        @unless($fechaHoy)
                        <button
                            type="button"
                            class="delivery-delivered-toggle collapsed"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $collapseId }}"
                            aria-expanded="false"
                            aria-controls="{{ $collapseId }}">

                            <i class="bi bi-chevron-down"></i>
                            Ver pedidos

                        </button>
                        @endunless

                    </div>

                </div>

                <div
                    id="{{ $collapseId }}"
                    class="{{ $fechaHoy ? '' : 'collapse' }}">

                    <div class="row g-3">

                        @foreach($pedidosFecha as $asignacion)

                        @php
                        $pedido = $asignacion->pedido;
                        $cliente = $pedido->user;
                        @endphp

                        <div class="col-12 col-md-6 col-xl-4">

                            <a
                                href="{{ url('/delivery/pedidos/' . (int) $pedido->id) }}"
                                class="delivery-delivered-card pedido-card-clickable"
                                aria-label="Ver detalles del pedido #{{ $pedido->id }}">

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

                                    <div>
                                        <strong>
                                            Ruta:
                                            Bs {{ number_format((float) ($pedido->tarifa_delivery ?? 0), 2) }}
                                        </strong>

                                        <small class="delivery-delivered-financial">
                                            Mi comisión:
                                            Bs {{ number_format((float) ($pedido->monto_delivery ?? 0), 2) }}
                                            ({{ number_format((float) ($pedido->porcentaje_delivery ?? 0), 0) }}%)
                                            · Restaurante:
                                            Bs {{ number_format((float) ($pedido->monto_restaurante_delivery ?? 0), 2) }}
                                            ({{ number_format((float) ($pedido->porcentaje_restaurante_delivery ?? 0), 0) }}%)
                                        </small>
                                    </div>

                                    <span class="delivery-delivered-view-link">
                                        <i class="bi bi-eye-fill"></i>
                                        Ver detalles
                                    </span>

                                </div>

                            </a>

                        </div>

                        @endforeach

                    </div>

                </div>

            </section>

            @endforeach

            @endif

        </div>

    </div>

</div>


{{-- =====================================================
    JAVASCRIPT DE LAS PESTAÑAS
====================================================== --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const tabs = document.querySelectorAll('.delivery-my-tab');
        const panels = document.querySelectorAll('.delivery-my-tab-panel');

        tabs.forEach(function(tab) {

            tab.addEventListener('click', function() {

                const target = this.dataset.target;

                /*
                |--------------------------------------------------
                | Quitar estado activo de todas las pestañas
                |--------------------------------------------------
                */

                tabs.forEach(function(item) {

                    item.classList.remove('active');
                    item.setAttribute('aria-selected', 'false');

                });


                /*
                |--------------------------------------------------
                | Activar pestaña seleccionada
                |--------------------------------------------------
                */

                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');


                /*
                |--------------------------------------------------
                | Ocultar todos los paneles
                |--------------------------------------------------
                */

                panels.forEach(function(panel) {

                    panel.classList.remove('active');

                });


                /*
                |--------------------------------------------------
                | Mostrar panel seleccionado
                |--------------------------------------------------
                */

                const selectedPanel = document.getElementById(target);

                if (selectedPanel) {

                    selectedPanel.classList.add('active');

                }

            });

        });

    });
</script>

@endsection