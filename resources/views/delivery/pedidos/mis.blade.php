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

<div class="delivery-my-orders">

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
        BUSCADOR GMAIL
    ====================================================== --}}

    <section class="delivery-search-card" data-delivery-animate>

        <div class="delivery-search-icon">
            <i class="bi bi-google"></i>
        </div>

        <div class="delivery-search-body">

            <label
                for="deliveryGmailSearch"
                class="delivery-search-label">

                Buscar por Gmail del cliente

            </label>

            <div class="delivery-search-input-wrapper">

                <i class="bi bi-search"></i>

                <input
                    id="deliveryGmailSearch"
                    type="search"
                    class="delivery-search-input"
                    value="{{ $busquedaGmail ?? '' }}"
                    placeholder="Ej. cliente@gmail.com"
                    autocomplete="off"
                    spellcheck="false">

                <button
                    type="button"
                    id="deliveryGmailClear"
                    class="delivery-search-clear"
                    aria-label="Limpiar búsqueda"
                    {{ empty($busquedaGmail) ? 'hidden' : '' }}>

                    <i class="bi bi-x-circle-fill"></i>

                </button>

            </div>

            <small
                id="deliverySearchResult"
                class="delivery-search-result">

                Escribe el Gmail del cliente para filtrar al instante.

            </small>

        </div>

    </section>


    {{-- =====================================================
        RESUMEN
    ====================================================== --}}

    <div class="row g-3 mb-4">

        <div class="col-12 col-md-6" data-delivery-animate>

            <div class="delivery-my-stat delivery-my-stat-active">

                <div class="delivery-my-stat-icon">
                    <i class="bi bi-bicycle"></i>
                </div>

                <div>
                    <span>
                        En proceso
                    </span>

                    <strong>
                        {{ $cantidadAsignados }}
                    </strong>

                    <small>
                        Asignados o en camino
                    </small>
                </div>

            </div>

        </div>


        <div class="col-12 col-md-6" data-delivery-animate>

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
                        Finalizados en la fecha seleccionada
                    </small>
                </div>

            </div>

        </div>

    </div>


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
                    En proceso
                </span>

                <strong>
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

                <strong>
                    {{ $cantidadEntregados }}
                </strong>

            </button>

        </div>

    </div>


    {{-- =====================================================
        PANEL EN PROCESO
    ====================================================== --}}

    <section
        id="panel-asignados"
        class="delivery-my-tab-panel active"
        data-delivery-search-panel>

        <div class="delivery-my-section">

            <div class="delivery-my-section-header">

                <div>
                    <span class="delivery-my-section-label">
                        FECHA SELECCIONADA
                    </span>

                    <h2>
                        <i class="bi bi-bicycle"></i>
                        Pedidos en proceso
                    </h2>

                    <p>
                        Asignados automáticamente y aún no finalizados.
                    </p>
                </div>

                <div class="delivery-my-section-count active">
                    {{ $cantidadAsignados }}
                </div>

            </div>


            @if($pedidosAsignados->isEmpty())

            <div
                class="delivery-my-empty delivery-my-empty-active"
                data-delivery-search-empty>

                <div class="delivery-my-empty-icon">
                    <i class="bi bi-bicycle"></i>
                </div>

                <h3>
                    No hay pedidos en proceso
                </h3>

                <p>
                    No tienes entregas asignadas o en camino dentro de la fecha seleccionada.
                    Las nuevas asignaciones llegarán automáticamente.
                </p>

            </div>

            @else

            <div class="row g-4">

                @foreach($pedidosAsignados as $asignacion)

                @php
                    $pedido = $asignacion->pedido;
                    $cliente = $pedido?->user;
                    $estado = $pedido?->estado;
                    $gmail = $cliente?->email ?? '';
                @endphp

                <div
                    class="col-12 col-lg-6"
                    data-delivery-search-card
                    data-gmail="{{ $gmail }}">

                    <article
                        class="delivery-my-order-card {{ $estado === 'asignado' ? 'delivery-assigned-locked' : 'delivery-in-route-card' }}"
                        data-delivery-animate>

                        <div class="delivery-my-order-header">

                            <div class="delivery-my-order-number">

                                <div class="delivery-my-order-number-icon">
                                    <i class="bi bi-receipt-cutoff"></i>
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

                            @if($estado === 'asignado')

                            <span class="delivery-my-status assigned">
                                <span></span>
                                Asignado
                            </span>

                            @else

                            <span class="delivery-my-status route">
                                <span></span>
                                En camino
                            </span>

                            @endif

                        </div>


                        {{-- ASIGNADO: INFORMACIÓN MÍNIMA --}}
                        @if($estado === 'asignado')

                        <div class="delivery-assigned-privacy">

                            <div class="delivery-assigned-privacy-icon">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>

                            <div>
                                <strong>
                                    Pedido protegido hasta iniciar
                                </strong>

                                <span>
                                    La dirección, productos, referencias y demás detalles
                                    se habilitan únicamente después de iniciar la entrega.
                                </span>
                            </div>

                        </div>


                        <div class="delivery-assigned-meta">

                            <div>
                                <span>Asignado</span>
                                <strong>
                                    {{ $asignacion->created_at?->format('d/m/Y H:i') ?? '—' }}
                                </strong>
                            </div>

                            <div>
                                <span>Estado</span>
                                <strong>
                                    Listo para iniciar
                                </strong>
                            </div>

                        </div>


                        <div class="delivery-my-actions delivery-assigned-actions">

                            <form
                                action="{{ route('delivery.pedidos.iniciar', $pedido->id) }}"
                                method="POST"
                                data-disable-on-submit>

                                @csrf
                                @method('PUT')

                                <button
                                    type="submit"
                                    class="delivery-my-start-btn"
                                    data-delivery-interactive>

                                    <i class="bi bi-play-circle-fill"></i>
                                    Iniciar entrega

                                </button>

                            </form>


                            <form
                                action="{{ route('delivery.pedidos.cancelar', $pedido->id) }}"
                                method="POST"
                                onsubmit="return confirm('¿Confirmas que quieres cancelar la asignación del pedido #{{ $pedido->id }}? El pedido volverá a la cola y será asignado nuevamente.')"
                                data-disable-on-submit>

                                @csrf
                                @method('PUT')

                                <button
                                    type="submit"
                                    class="delivery-my-cancel-btn"
                                    data-delivery-interactive>

                                    <i class="bi bi-x-circle-fill"></i>
                                    Cancelar pedido

                                </button>

                            </form>

                        </div>


                        {{-- EN CAMINO: DETALLES DESBLOQUEADOS --}}
                        @else

                        @php
                            $cantidadProductos = $pedido->detallePedidos
                                ->sum('cantidad');
                        @endphp

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

                                <span>Cliente</span>

                                <strong>
                                    {{ $cliente->name ?? 'Cliente eliminado' }}
                                </strong>

                                @if($cliente?->email)

                                <small>
                                    <i class="bi bi-envelope"></i>
                                    {{ $cliente->email }}
                                </small>

                                @endif

                            </div>

                        </div>

                        @if($cliente?->telefono)

                        <div class="delivery-my-phone">

                            <i class="bi bi-telephone-fill"></i>

                            <div>

                                <span>Teléfono de contacto</span>

                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $cliente->telefono) }}">
                                    {{ $cliente->telefono }}
                                </a>

                            </div>

                        </div>

                        @endif


                        <div class="delivery-my-order-info">

                            <div class="delivery-my-info-item">

                                <i class="bi bi-basket3"></i>

                                <div>
                                    <span>Productos</span>

                                    <strong>
                                        {{ $cantidadProductos }}
                                        {{ $cantidadProductos === 1 ? 'unidad' : 'unidades' }}
                                    </strong>
                                </div>

                            </div>


                            <div class="delivery-my-info-item">

                                <i class="bi bi-signpost-split"></i>

                                <div>
                                    <span>
                                        Ruta
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

                        </div>


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


                        @if(!empty($pedido->referencia_delivery))

                        <div class="delivery-my-reference">

                            <i class="bi bi-signpost-2-fill"></i>

                            <div>
                                <strong>Referencia para la entrega</strong>

                                <span>
                                    {{ $pedido->referencia_delivery }}
                                </span>
                            </div>

                        </div>

                        @endif


                        @if(!empty($pedido->observacion_cliente))

                        <div class="delivery-my-observation">

                            <i class="bi bi-chat-left-text-fill"></i>

                            <div>
                                <strong>Observación del cliente</strong>

                                <span>
                                    {{ $pedido->observacion_cliente }}
                                </span>
                            </div>

                        </div>

                        @endif


                        <div class="delivery-my-order-date">

                            <i class="bi bi-clock-history"></i>

                            Iniciada el
                            {{ $asignacion->updated_at?->format('d/m/Y') ?? $pedido->created_at->format('d/m/Y') }}
                            a las
                            {{ $asignacion->updated_at?->format('H:i') ?? $pedido->created_at->format('H:i') }}

                        </div>


                        <div class="delivery-my-actions">

                            <a
                                href="{{ route('delivery.pedidos.show', $pedido->id) }}"
                                class="delivery-my-detail-btn"
                                data-delivery-interactive>

                                <i class="bi bi-eye-fill"></i>
                                Ver detalles y mapa

                            </a>

                            <form
                                action="{{ route('delivery.pedidos.entregar', $pedido->id) }}"
                                method="POST"
                                onsubmit="return confirm('¿Confirmas que el pedido #{{ $pedido->id }} ya fue entregado al cliente?')"
                                data-disable-on-submit>

                                @csrf
                                @method('PUT')

                                <button
                                    type="submit"
                                    class="delivery-my-deliver-btn"
                                    data-delivery-interactive>

                                    <i class="bi bi-check-circle-fill"></i>
                                    Marcar como entregado

                                </button>

                            </form>

                        </div>

                        @endif

                    </article>

                </div>

                @endforeach

            </div>

            @endif

        </div>

    </section>


    {{-- =====================================================
        PANEL ENTREGADOS — SOLO FECHA SELECCIONADA
    ====================================================== --}}

    <section
        id="panel-entregados"
        class="delivery-my-tab-panel"
        data-delivery-search-panel>

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
                class="delivery-my-empty delivery-my-empty-delivered"
                data-delivery-search-empty>

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
                    $gmail = $cliente?->email ?? '';
                @endphp

                <div
                    class="col-12 col-md-6 col-xl-4"
                    data-delivery-search-card
                    data-gmail="{{ $gmail }}">

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

</div>

@endsection
