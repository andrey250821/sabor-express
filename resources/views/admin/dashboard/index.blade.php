@extends('layouts.admin')

@section('content')

<div class="admin-dashboard">

    {{-- =========================================================
        ENCABEZADO
    ========================================================== --}}

    <div class="dashboard-header">

        <div>
            <span class="dashboard-eyebrow">
                PANEL DE CONTROL
            </span>

            <h2 class="dashboard-title">
                Bienvenido, {{ Auth::user()->name }} 👋
            </h2>

            <p class="dashboard-subtitle">
                Resumen general de Sabor Express
            </p>
        </div>

        <div class="dashboard-date">
            <i class="bi bi-calendar3"></i>

            {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}
        </div>

    </div>


    {{-- =========================================================
        INDICADORES PRINCIPALES
    ========================================================== --}}

    <div class="dashboard-metrics">

        {{-- PEDIDOS --}}
        <a
            href="{{ route('admin.pedidos.index') }}"
            class="dashboard-metric-card">

            <div class="metric-icon pedidos">
                <i class="bi bi-bag-check-fill"></i>
            </div>

            <div class="metric-content">

                <span class="metric-label">
                    Pedidos
                </span>

                <strong class="metric-value">
                    {{ $pedidos }}
                </strong>

                <span class="metric-description">
                    Pedidos registrados
                </span>

            </div>

            <i class="bi bi-arrow-up-right metric-arrow"></i>

        </a>


        {{-- VENTAS --}}
        <div class="dashboard-metric-card">

            <div class="metric-icon ventas">
                <i class="bi bi-cash-stack"></i>
            </div>

            <div class="metric-content">

                <span class="metric-label">
                    Ventas del mes
                </span>

                <strong class="metric-value">
                    Bs {{ number_format($ventasMes, 2) }}
                </strong>

                <span class="metric-description">
                    Ingresos registrados
                </span>

            </div>

        </div>


        {{-- CLIENTES --}}
        <a
            href="{{ route('admin.clientes.index') }}"
            class="dashboard-metric-card">

            <div class="metric-icon clientes">
                <i class="bi bi-people-fill"></i>
            </div>

            <div class="metric-content">

                <span class="metric-label">
                    Clientes
                </span>

                <strong class="metric-value">
                    {{ $clientes }}
                </strong>

                <span class="metric-description">
                    Usuarios registrados
                </span>

            </div>

            <i class="bi bi-arrow-up-right metric-arrow"></i>

        </a>


        {{-- DELIVERY --}}
        <a
            href="{{ route('admin.deliverys.index') }}"
            class="dashboard-metric-card">

            <div class="metric-icon delivery">
                <i class="bi bi-bicycle"></i>
            </div>

            <div class="metric-content">

                <span class="metric-label">
                    Delivery activos
                </span>

                <strong class="metric-value">
                    {{ $deliverys }}
                </strong>

                <span class="metric-description">
                    Repartidores disponibles
                </span>

            </div>

            <i class="bi bi-arrow-up-right metric-arrow"></i>

        </a>

    </div>


    {{-- =========================================================
        ESTADO DE PEDIDOS
    ========================================================== --}}

    <div class="dashboard-section">

        <div class="section-heading">

            <div>
                <span class="section-eyebrow">
                    OPERACIÓN
                </span>

                <h3>
                    Estado de pedidos
                </h3>
            </div>

            <a
                href="{{ route('admin.pedidos.index') }}"
                class="dashboard-link">
                Ver pedidos
                <i class="bi bi-arrow-right"></i>
            </a>

        </div>


        <div class="order-status-card">

            {{-- PENDIENTES --}}
            <a
                href="{{ route('admin.pedidos.index') }}"
                class="order-status-item">

                <span class="status-icon pendiente">
                    <i class="bi bi-hourglass-split"></i>
                </span>

                <span class="status-info">
                    <strong>{{ $pedidosPendientes }}</strong>
                    <small>Pendientes</small>
                </span>

            </a>


            {{-- PREPARANDO --}}
            <a
                href="{{ route('admin.pedidos.index') }}"
                class="order-status-item">

                <span class="status-icon preparando">
                    <i class="bi bi-fire"></i>
                </span>

                <span class="status-info">
                    <strong>{{ $pedidosPreparando }}</strong>
                    <small>Preparando</small>
                </span>

            </a>


            {{-- LISTOS --}}
            <a
                href="{{ route('admin.pedidos.index') }}"
                class="order-status-item">

                <span class="status-icon listo">
                    <i class="bi bi-check-circle-fill"></i>
                </span>

                <span class="status-info">
                    <strong>{{ $pedidosListos }}</strong>
                    <small>Listos</small>
                </span>

            </a>


            {{-- EN CAMINO --}}
            <a
                href="{{ route('admin.pedidos.index') }}"
                class="order-status-item">

                <span class="status-icon camino">
                    <i class="bi bi-bicycle"></i>
                </span>

                <span class="status-info">
                    <strong>{{ $pedidosEnCamino }}</strong>
                    <small>En camino</small>
                </span>

            </a>


            {{-- ENTREGADOS --}}
            <div class="order-status-item">

                <span class="status-icon entregado">
                    <i class="bi bi-check2-all"></i>
                </span>

                <span class="status-info">
                    <strong>{{ $pedidosEntregados }}</strong>
                    <small>Entregados</small>
                </span>

            </div>

        </div>

    </div>


    {{-- =========================================================
        CONTENIDO PRINCIPAL
    ========================================================== --}}

    <div class="dashboard-grid">


        {{-- =====================================================
            ÚLTIMOS PEDIDOS
        ====================================================== --}}

        <div class="dashboard-panel pedidos-panel">

            <div class="panel-header">

                <div>

                    <span class="panel-eyebrow">
                        ACTIVIDAD RECIENTE
                    </span>

                    <h3>
                        Últimos pedidos
                    </h3>

                </div>

                <a
                    href="{{ route('admin.pedidos.index') }}"
                    class="panel-action">
                    Ver todos
                    <i class="bi bi-arrow-right"></i>
                </a>

            </div>


            <div class="recent-orders">

                @forelse($ultimosPedidos as $pedido)

                <a
                    href="{{ route('admin.pedidos.show', $pedido->id) }}"
                    class="recent-order">

                    <div class="order-number">
                        #{{ $pedido->id }}
                    </div>


                    <div class="order-client">

                        <strong>
                            {{ $pedido->user->name ?? 'Cliente eliminado' }}
                        </strong>

                        <small>
                            {{ $pedido->created_at->format('d/m/Y H:i') }}
                        </small>

                    </div>


                    <div class="order-total">
                        Bs {{ number_format($pedido->total, 2) }}
                    </div>


                    <div class="order-state">

                        @if($pedido->estado == 'pendiente')

                        <span class="state-badge pendiente">
                            Pendiente
                        </span>

                        @elseif($pedido->estado == 'pagado')

                        <span class="state-badge pagado">
                            Pagado
                        </span>

                        @elseif($pedido->estado == 'preparando')

                        <span class="state-badge preparando">
                            Preparando
                        </span>

                        @elseif($pedido->estado == 'listo')

                        <span class="state-badge listo">
                            Listo
                        </span>

                        @elseif($pedido->estado == 'asignado')

                        <span class="state-badge asignado">
                            Asignado
                        </span>

                        @elseif($pedido->estado == 'en_camino')

                        <span class="state-badge camino">
                            En camino
                        </span>

                        @elseif($pedido->estado == 'entregado')

                        <span class="state-badge entregado">
                            Entregado
                        </span>

                        @elseif($pedido->estado == 'cancelado')

                        <span class="state-badge cancelado">
                            Cancelado
                        </span>

                        @else

                        <span class="state-badge">
                            {{ ucfirst($pedido->estado) }}
                        </span>

                        @endif

                    </div>


                    <i class="bi bi-chevron-right order-arrow"></i>

                </a>

                @empty

                <div class="empty-dashboard">

                    <i class="bi bi-inbox"></i>

                    <p>
                        No existen pedidos registrados.
                    </p>

                </div>

                @endforelse

            </div>

        </div>


        {{-- =====================================================
            RESUMEN RÁPIDO
        ====================================================== --}}

        <div class="dashboard-panel summary-panel">

            <div class="panel-header">

                <div>

                    <span class="panel-eyebrow">
                        RESUMEN
                    </span>

                    <h3>
                        Atención requerida
                    </h3>

                </div>

            </div>


            {{-- COMPROBANTES --}}
            <a
                href="{{ route('admin.comprobantes.index') }}"
                class="attention-card">

                <div class="attention-icon comprobantes">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>

                <div class="attention-content">

                    <strong>
                        {{ $comprobantesPendientes }}
                    </strong>

                    <span>
                        Comprobantes pendientes
                    </span>

                </div>

                <i class="bi bi-chevron-right"></i>

            </a>


            {{-- PEDIDOS PENDIENTES --}}
            <a
                href="{{ route('admin.pedidos.index') }}"
                class="attention-card">

                <div class="attention-icon pendientes">
                    <i class="bi bi-clock-history"></i>
                </div>

                <div class="attention-content">

                    <strong>
                        {{ $pedidosPendientes }}
                    </strong>

                    <span>
                        Pedidos pendientes
                    </span>

                </div>

                <i class="bi bi-chevron-right"></i>

            </a>


            {{-- ENTREGAS --}}
            <a
                href="{{ route('admin.pedidos.index') }}"
                class="attention-card">

                <div class="attention-icon entregas">
                    <i class="bi bi-truck"></i>
                </div>

                <div class="attention-content">

                    <strong>
                        {{ $pedidosEnCamino }}
                    </strong>

                    <span>
                        Pedidos en camino
                    </span>

                </div>

                <i class="bi bi-chevron-right"></i>

            </a>


            {{-- PRODUCTOS --}}
            <a
                href="{{ route('admin.productos.index') }}"
                class="quick-product-link">

                <div>

                    <strong>
                        {{ $productos }}
                    </strong>

                    <span>
                        Productos registrados
                    </span>

                </div>

                <i class="bi bi-box-seam"></i>

            </a>

        </div>

    </div>


    {{-- =========================================================
        GRÁFICO
    ========================================================== --}}

    <div class="dashboard-section chart-section">

        <div class="section-heading">

            <div>

                <span class="section-eyebrow">
                    RENDIMIENTO
                </span>

                <h3>
                    Ventas de los últimos 7 días
                </h3>

            </div>

            <div class="chart-total">

                <span>
                    Ventas del mes
                </span>

                <strong>
                    Bs {{ number_format($ventasMes, 2) }}
                </strong>

            </div>

        </div>


        <div class="dashboard-chart">

            <canvas id="ventasChart"></canvas>

        </div>

    </div>


</div>


{{-- =========================================================
    CHART.JS
========================================================== --}}

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ventas = @json($ventasSemana);

    const canvas = document.getElementById('ventasChart');

    if (canvas) {

        const labels = ventas.map(item => item.fecha);

        const datos = ventas.map(item => Number(item.total));


        new Chart(canvas, {

            type: 'line',

            data: {

                labels: labels,

                datasets: [

                    {

                        label: 'Ventas',

                        data: datos,

                        fill: true,

                        tension: 0.4,

                        borderWidth: 3,

                        pointRadius: 4,

                        pointHoverRadius: 6,

                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        display: false
                    }

                },

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {

                            callback: function(value) {

                                return 'Bs ' + value;

                            }

                        }

                    }

                }

            }

        });

    }
</script>

@endpush

@endsection