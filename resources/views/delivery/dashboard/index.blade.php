@extends('layouts.delivery')

@section('title', 'Dashboard - Delivery')
@section('section', 'Panel de Delivery')
@section('heading', 'Centro de entregas')

@section('content')

<div class="container-fluid delivery-dashboard">

    {{-- =========================================================
         HERO
    ========================================================== --}}
    <section class="delivery-dashboard-hero mb-4" data-delivery-animate>

        <div class="delivery-dashboard-hero-glow"></div>

        <div class="row align-items-center g-4 position-relative">

            <div class="col-12 col-lg-8">

                <div class="delivery-dashboard-kicker">

                    <span class="delivery-dashboard-kicker-dot"></span>

                    <i class="bi bi-bicycle"></i>

                    Delivery ·
                    {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}

                </div>

                <h1 class="delivery-dashboard-title">

                    ¡Hola, {{ $delivery->name ?? auth()->user()->name }}!

                    <span>🏍️</span>

                </h1>

                <p class="delivery-dashboard-text">

                    Bienvenido a tu centro de entregas.
                    Consulta la cantidad de pedidos en cola. Las entregas se asignan automáticamente y mantén actualizado el estado de tus pedidos.

                </p>

                <div class="delivery-dashboard-actions">

                    <a href="{{ route('delivery.pedidos.index') }}"
                        class="delivery-dashboard-btn-primary" data-delivery-interactive>

                        <i class="bi bi-box-seam"></i>

                        Ver cola de pedidos

                    </a>

                    <a href="{{ route('delivery.pedidos.mis') }}"
                        class="delivery-dashboard-btn-secondary" data-delivery-interactive>

                        <i class="bi bi-bicycle"></i>

                        Mis pedidos

                    </a>

                </div>

                <div class="delivery-dashboard-date">

                    <i class="bi bi-calendar3"></i>

                    {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}

                </div>

            </div>


            {{-- =================================================
                 HERO VISUAL
            ================================================== --}}
            <div class="col-12 col-lg-4">

                <div class="delivery-dashboard-hero-visual">

                    <div class="delivery-hero-circle circle-one"></div>

                    <div class="delivery-hero-circle circle-two"></div>

                    <div class="delivery-hero-circle circle-three"></div>


                    <div class="delivery-bike-icon">

                        <i class="bi bi-bicycle"></i>

                    </div>


                    {{-- PEDIDOS EN COLA --}}
                    <div class="delivery-floating-card floating-top">

                        <div class="delivery-floating-icon available">

                            <i class="bi bi-box-seam"></i>

                        </div>

                        <div>

                            <strong>
                                {{ $pedidosEnCola }}
                            </strong>

                            <span>
                                En cola
                            </span>

                        </div>

                    </div>


                    {{-- PEDIDOS ACTIVOS --}}
                    <div class="delivery-floating-card floating-bottom">

                        <div class="delivery-floating-icon active">

                            <i class="bi bi-truck"></i>

                        </div>

                        <div>

                            <strong>
                                {{ $misPedidos }}
                            </strong>

                            <span>
                                En reparto
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    @include('shared.date-filter', [
        'fechaSeleccionada' => $fechaSeleccionada,
        'tituloFecha' => 'Actividad y ganancias del Delivery'
    ])

    {{-- =========================================================
         ESTADO DEL DELIVERY
    ========================================================== --}}
    <section class="delivery-dashboard-status mb-4" data-delivery-animate>

        <div class="delivery-dashboard-status-left">

            @if($pedidosEnCola > 0)

            <div class="delivery-status-pulse warning">
                <span></span>
            </div>

            <div>

                <strong>

                    {{ $pedidosEnCola }}

                    {{ $pedidosEnCola === 1
                            ? 'pedido en cola'
                            : 'pedidos en cola'
                        }}

                </strong>

                <small>

                    Hay pedidos listos en cocina esperando asignación automática.

                </small>

            </div>

            @else

            <div class="delivery-status-pulse success">
                <span></span>
            </div>

            <div>

                <strong>
                    No hay pedidos en cola
                </strong>

                <small>
                    Actualmente no hay pedidos esperando asignación.
                </small>

            </div>

            @endif

        </div>


        <div class="delivery-dashboard-status-badge">

            <i class="bi bi-activity"></i>

            Delivery activo

        </div>

    </section>


    {{-- =========================================================
         ESTADÍSTICAS
    ========================================================== --}}
    <div class="row g-3 mb-4">


        {{-- PEDIDOS EN COLA --}}
        <div class="col-12 col-sm-6 col-xl-4">

            <a href="{{ route('delivery.pedidos.index') }}"
                class="delivery-stat-link" data-delivery-interactive>

                <div class="delivery-stat-card warning">

                    <div class="delivery-stat-header">

                        <div>

                            <span class="delivery-stat-label">
                                En cola
                            </span>

                            <strong class="delivery-stat-number">
                                {{ $pedidosEnCola }}
                            </strong>

                        </div>

                        <div class="delivery-stat-icon">

                            <i class="bi bi-box-seam"></i>

                        </div>

                    </div>

                    <div class="delivery-stat-bottom">

                        <span>
                            Pedidos listos esperando asignación
                        </span>

                        <i class="bi bi-arrow-up-right"></i>

                    </div>

                </div>

            </a>

        </div>


        {{-- MIS PEDIDOS --}}
        <div class="col-12 col-sm-6 col-xl-4">

            <a href="{{ route('delivery.pedidos.mis') }}"
                class="delivery-stat-link" data-delivery-interactive>

                <div class="delivery-stat-card primary">

                    <div class="delivery-stat-header">

                        <div>

                            <span class="delivery-stat-label">
                                Mis pedidos
                            </span>

                            <strong class="delivery-stat-number">
                                {{ $misPedidos }}
                            </strong>

                        </div>

                        <div class="delivery-stat-icon">

                            <i class="bi bi-bicycle"></i>

                        </div>

                    </div>

                    <div class="delivery-stat-bottom">

                        <span>
                            Entregas en proceso
                        </span>

                        <i class="bi bi-arrow-up-right"></i>

                    </div>

                </div>

            </a>

        </div>


        {{-- ENTREGADOS --}}
        <div class="col-12 col-sm-6 col-xl-4">

            <div class="delivery-stat-card success">

                <div class="delivery-stat-header">

                    <div>

                        <span class="delivery-stat-label">
                            Entregados
                        </span>

                        <strong class="delivery-stat-number">
                            {{ $entregasDia }}
                        </strong>

                    </div>

                    <div class="delivery-stat-icon">

                        <i class="bi bi-check2-circle"></i>

                    </div>

                </div>

                <div class="delivery-stat-bottom">

                    <span>
                        Entregas completadas
                    </span>

                    <i class="bi bi-check-circle"></i>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         RESUMEN ECONÓMICO DEL DÍA
    ========================================================== --}}
    <section class="delivery-dashboard-financial mb-4" data-delivery-animate>

        <div class="delivery-financial-header">

            <div>
                <span class="delivery-financial-kicker">
                    INGRESOS DEL DÍA
                </span>

                <h2>
                    Resumen de {{ $fechaSeleccionada->format('d/m/Y') }}
                </h2>

                <p>
                    Solo se contabilizan las entregas finalizadas en la fecha seleccionada.
                </p>
            </div>

            <div class="delivery-financial-icon">
                <i class="bi bi-cash-stack"></i>
            </div>

        </div>


        <div class="delivery-financial-grid">

            <div class="delivery-financial-card commission">
                <span>
                    Mi comisión
                </span>

                <strong>
                    Bs {{ number_format($comisionDia, 2) }}
                </strong>

                <small>
                    De tus entregas
                </small>
            </div>


            <div class="delivery-financial-card restaurant">
                <span>
                    Parte restaurante
                </span>

                <strong>
                    Bs {{ number_format($parteRestauranteDia, 2) }}
                </strong>

                <small>
                    Parte del restaurante
                </small>
            </div>


            <div class="delivery-financial-card">
                <span>
                    Entregas completadas
                </span>

                <strong>
                    {{ $entregasDia }}
                </strong>

                <small>
                    {{ $fechaSeleccionada->format('d/m/Y') }}
                </small>
            </div>

        </div>


        {{-- DETALLE DE LA FECHA SELECCIONADA --}}
        <div class="delivery-history-wrapper mt-4">

            <div class="delivery-history-header">
                <div>
                    <strong>
                        Entregas del {{ $fechaSeleccionada->format('d/m/Y') }}
                    </strong>
                    <span>
                        {{ $entregasDia }} {{ $entregasDia === 1 ? 'entrega completada' : 'entregas completadas' }}
                    </span>
                </div>
            </div>

            <div class="delivery-history-selected mt-3">

                <div class="delivery-history-selected-header">
                    <div>
                        <span>Día seleccionado</span>
                        <strong>{{ $fechaSeleccionada->format('d/m/Y') }}</strong>
                    </div>

                    <div class="delivery-history-selected-totals">
                        <div>
                            <small>Total cobrado</small>
                            <strong>Bs {{ number_format($tarifaDeliveryDia, 2) }}</strong>
                        </div>

                        <div>
                            <small>Mi comisión</small>
                            <strong>Bs {{ number_format($comisionDia, 2) }}</strong>
                        </div>

                        <div>
                            <small>Restaurante</small>
                            <strong>Bs {{ number_format($parteRestauranteDia, 2) }}</strong>
                        </div>
                    </div>
                </div>

                @if($entregasDiaSeleccionado->isEmpty())

                    <div class="delivery-history-empty">
                        <i class="bi bi-calendar-x"></i>
                        <span>
                            No hay entregas completadas en esta fecha.
                        </span>
                    </div>

                @else

                    <div class="delivery-history-list">
                        @foreach($entregasDiaSeleccionado as $asignacion)

                            @php
                                $pedidoHistorico = $asignacion->pedido;
                            @endphp

                            <div class="delivery-history-item">

                                <div>
                                    <strong>
                                        Pedido #{{ $pedidoHistorico?->id ?? '—' }}
                                    </strong>

                                    <small>
                                        {{ $asignacion->updated_at?->copy()->timezone(\App\Services\FechaFiltroService::TIMEZONE)->format('H:i') ?? '—' }}
                                        ·
                                        {{ number_format((float) ($pedidoHistorico?->distancia_delivery_km ?? 0), 2) }} km
                                    </small>
                                </div>

                                <div class="delivery-history-item-values">

                                    <span>
                                        Ruta:
                                        <strong>
                                            Bs {{ number_format((float) ($pedidoHistorico?->tarifa_delivery ?? 0), 2) }}
                                        </strong>
                                    </span>

                                    <span>
                                        Tú:
                                        <strong>
                                            Bs {{ number_format((float) ($pedidoHistorico?->monto_delivery ?? 0), 2) }}
                                            ({{ number_format((float) ($pedidoHistorico?->porcentaje_delivery ?? 0), 0) }}%)
                                        </strong>
                                    </span>

                                    <span>
                                        Restaurante:
                                        <strong>
                                            Bs {{ number_format((float) ($pedidoHistorico?->monto_restaurante_delivery ?? 0), 2) }}
                                            ({{ number_format((float) ($pedidoHistorico?->porcentaje_restaurante_delivery ?? 0), 0) }}%)
                                        </strong>
                                    </span>

                                </div>

                            </div>

                        @endforeach
                    </div>

                @endif

            </div>

        </div>
    </section>


    {{-- =========================================================
         INFORMACIÓN FINAL
    ========================================================== --}}
    <section class="delivery-dashboard-footer-card" data-delivery-animate>

        <div class="delivery-footer-card-icon">

            <i class="bi bi-shield-check"></i>

        </div>

        <div class="delivery-footer-card-content">

            <strong>
                Entregas seguras y eficientes
            </strong>

            <span>

                Cada entrega que realizas forma parte del flujo de
                {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}.
                Mantén actualizado el estado de tus entregas.

            </span>

        </div>

        <div class="delivery-footer-card-badge">

            <i class="bi bi-check-circle-fill"></i>

            Sistema activo

        </div>

    </section>

</div>




@endsection
@push('scripts')
@if($fechaSeleccionada->isToday())
<script>
    setInterval(() => {
        if (document.visibilityState === 'visible') {
            window.location.reload();
        }
    }, 10000);
</script>
@endif
@endpush
