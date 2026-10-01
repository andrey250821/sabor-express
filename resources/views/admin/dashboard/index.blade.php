@extends('layouts.admin')

@section('content')

<div class="admin-dashboard">

    <div class="dashboard-header">
        <div>
            <span class="dashboard-eyebrow">PANEL DE CONTROL</span>
            <h2 class="dashboard-title">
                Bienvenido, {{ Auth::user()->name }} 👋
            </h2>
            <p class="dashboard-subtitle">Supervisión general de Sabor Express</p>
        </div>

        <div class="dashboard-date">
            <i class="bi bi-calendar3"></i>
            {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}
        </div>
    </div>

    @include('shared.date-filter', [
        'fechaSeleccionada' => $fechaSeleccionada,
        'tituloFecha' => 'Actividad del administrador'
    ])

    <div class="dashboard-metrics">

        <a href="{{ route('admin.pedidos.index', ['fecha' => $fechaSeleccionada->toDateString()]) }}" class="dashboard-metric-card">
            <div class="metric-icon pedidos"><i class="bi bi-bag-check-fill"></i></div>
            <div class="metric-content">
                <span class="metric-label">Pedidos del día</span>
                <strong class="metric-value">{{ $pedidos }}</strong>
                <span class="metric-description">Pedidos registrados en la fecha seleccionada</span>
            </div>
            <i class="bi bi-arrow-up-right metric-arrow"></i>
        </a>

        <div class="dashboard-metric-card">
            <div class="metric-icon ventas"><i class="bi bi-cash-stack"></i></div>
            <div class="metric-content">
                <span class="metric-label">Ventas del día</span>
                <strong class="metric-value">Bs {{ number_format($ventasDia, 2) }}</strong>
                <span class="metric-description">Ventas del flujo normal de la fecha seleccionada</span>
            </div>
        </div>

        <a href="{{ route('admin.clientes.index') }}" class="dashboard-metric-card">
            <div class="metric-icon clientes"><i class="bi bi-people-fill"></i></div>
            <div class="metric-content">
                <span class="metric-label">Clientes</span>
                <strong class="metric-value">{{ $clientes }}</strong>
                <span class="metric-description">Usuarios con rol Cliente</span>
            </div>
            <i class="bi bi-arrow-up-right metric-arrow"></i>
        </a>

        <a href="{{ route('admin.deliverys.index') }}" class="dashboard-metric-card">
            <div class="metric-icon delivery"><i class="bi bi-bicycle"></i></div>
            <div class="metric-content">
                <span class="metric-label">Delivery activos</span>
                <strong class="metric-value">{{ $deliverys }}</strong>
                <span class="metric-description">Personal activo de Delivery</span>
            </div>
            <i class="bi bi-arrow-up-right metric-arrow"></i>
        </a>

    </div>

    <div class="dashboard-section">

        <div class="section-heading">
            <div>
                <span class="section-eyebrow">OPERACIÓN</span>
                <h3>Estado de pedidos · {{ $fechaSeleccionada->format('d/m/Y') }}</h3>
            </div>

            <a href="{{ route('admin.pedidos.index', ['fecha' => $fechaSeleccionada->toDateString()]) }}" class="dashboard-link">
                Ver pedidos
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="order-status-card">

            <a href="{{ route('admin.pedidos.index', ['fecha' => $fechaSeleccionada->toDateString()]) }}" class="order-status-item">
                <span class="status-icon pagado"><i class="bi bi-credit-card-fill"></i></span>
                <span class="status-info">
                    <strong>{{ $pedidosPagados }}</strong>
                    <small>Pagados</small>
                </span>
            </a>

            <a href="{{ route('admin.pedidos.index', ['fecha' => $fechaSeleccionada->toDateString()]) }}" class="order-status-item">
                <span class="status-icon preparando"><i class="bi bi-fire"></i></span>
                <span class="status-info">
                    <strong>{{ $pedidosPreparando }}</strong>
                    <small>Preparando</small>
                </span>
            </a>

            <a href="{{ route('admin.pedidos.index', ['fecha' => $fechaSeleccionada->toDateString()]) }}" class="order-status-item">
                <span class="status-icon listo"><i class="bi bi-check-circle-fill"></i></span>
                <span class="status-info">
                    <strong>{{ $pedidosListos }}</strong>
                    <small>Listos</small>
                </span>
            </a>

            <a href="{{ route('admin.pedidos.index', ['fecha' => $fechaSeleccionada->toDateString()]) }}" class="order-status-item">
                <span class="status-icon asignado"><i class="bi bi-person-check-fill"></i></span>
                <span class="status-info">
                    <strong>{{ $pedidosAsignados }}</strong>
                    <small>Asignados</small>
                </span>
            </a>

            <a href="{{ route('admin.pedidos.index', ['fecha' => $fechaSeleccionada->toDateString()]) }}" class="order-status-item">
                <span class="status-icon camino"><i class="bi bi-bicycle"></i></span>
                <span class="status-info">
                    <strong>{{ $pedidosEnCamino }}</strong>
                    <small>En camino</small>
                </span>
            </a>

            <div class="order-status-item">
                <span class="status-icon entregado"><i class="bi bi-check2-all"></i></span>
                <span class="status-info">
                    <strong>{{ $pedidosEntregados }}</strong>
                    <small>Entregados</small>
                </span>
            </div>

            <div class="order-status-item">
                <span class="status-icon cancelado"><i class="bi bi-x-circle-fill"></i></span>
                <span class="status-info">
                    <strong>{{ $pedidosCancelados }}</strong>
                    <small>Cancelados</small>
                </span>
            </div>

        </div>
    </div>

    <div class="dashboard-grid">

        <div class="dashboard-panel pedidos-panel">

            <div class="panel-header">
                <div>
                    <span class="panel-eyebrow">ACTIVIDAD DEL DÍA</span>
                    <h3>Pedidos de la fecha seleccionada</h3>
                </div>

                <a href="{{ route('admin.pedidos.index', ['fecha' => $fechaSeleccionada->toDateString()]) }}" class="panel-action">
                    Ver todos
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="recent-orders">

                @forelse($ultimosPedidos as $pedido)

                <a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="recent-order">

                    <div class="order-number">#{{ $pedido->id }}</div>

                    <div class="order-client">
                        <strong>{{ $pedido->user->name ?? 'Cliente eliminado' }}</strong>
                        <small>{{ $pedido->created_at->copy()->timezone(\App\Services\FechaFiltroService::TIMEZONE)->format('d/m/Y H:i') }}</small>
                    </div>

                    <div class="order-total">
                        Bs {{ number_format($pedido->total, 2) }}
                    </div>

                    <div class="order-state">
                        @switch($pedido->estado)
                            @case('pagado')
                                <span class="state-badge pagado">Pagado</span>
                                @break
                            @case('preparando')
                                <span class="state-badge preparando">Preparando</span>
                                @break
                            @case('listo')
                                <span class="state-badge listo">Listo</span>
                                @break
                            @case('asignado')
                                <span class="state-badge asignado">Asignado</span>
                                @break
                            @case('en_camino')
                                <span class="state-badge camino">En camino</span>
                                @break
                            @case('entregado')
                                <span class="state-badge entregado">Entregado</span>
                                @break
                            @case('cancelado')
                                <span class="state-badge cancelado">Cancelado</span>
                                @break
                            @default
                                <span class="state-badge">{{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}</span>
                        @endswitch
                    </div>

                    <i class="bi bi-chevron-right order-arrow"></i>

                </a>

                @empty

                <div class="empty-dashboard">
                    <i class="bi bi-inbox"></i>
                    <p>No existen pedidos registrados.</p>
                </div>

                @endforelse

            </div>
        </div>

        <div class="dashboard-panel summary-panel">

            <div class="panel-header">
                <div>
                    <span class="panel-eyebrow">ATENCIÓN</span>
                    <h3>Revisión administrativa</h3>
                </div>
            </div>

            <a href="{{ route('admin.comprobantes.index', 'en_revision') }}" class="attention-card">

                <div class="attention-icon comprobantes">
                    <i class="bi bi-shield-exclamation"></i>
                </div>

                <div class="attention-content">
                    <strong>{{ $comprobantesEnRevision }}</strong>
                    <span>Comprobantes en revisión</span>
                </div>

                <i class="bi bi-chevron-right"></i>

            </a>

            <div class="attention-description">
                Solo requieren intervención del administrador los comprobantes
                que presentan una inconsistencia en la validación automática.
            </div>

            <div class="quick-product-link">
                <div>
                    <strong>{{ $pedidosEnColaDelivery }}</strong>
                    <span>Pedidos listos en cola de Delivery</span>
                </div>
                <i class="bi bi-list-ol"></i>
            </div>

            <div class="quick-product-link">
                <div>
                    <strong>{{ $pedidosEnCamino }}</strong>
                    <span>Pedidos actualmente en camino</span>
                </div>
                <i class="bi bi-truck"></i>
            </div>

        </div>

    </div>

    <div class="dashboard-section dashboard-delivery-financial-section">

        <div class="section-heading">

            <div>
                <span class="section-eyebrow">DELIVERY</span>
                <h3>Resumen económico de entregas · {{ $fechaSeleccionada->format('d/m/Y') }}</h3>
            </div>

            <a
                href="{{ route('admin.deliverys.index') }}"
                class="dashboard-link">

                Ver Deliverys
                <i class="bi bi-arrow-right"></i>

            </a>

        </div>

        <div class="dashboard-delivery-financial-grid">

            <div class="dashboard-delivery-financial-card">
                <span>
                    <i class="bi bi-cash-stack"></i>
                    Total cobrado por entregas
                </span>

                <strong>
                    Bs {{ number_format($ingresosDelivery, 2) }}
                </strong>
            </div>

            <div class="dashboard-delivery-financial-card">
                <span>
                    <i class="bi bi-person-badge-fill"></i>
                    Comisión Delivery
                </span>

                <strong>
                    Bs {{ number_format($comisionesDelivery, 2) }}
                </strong>
            </div>

            <div class="dashboard-delivery-financial-card">
                <span>
                    <i class="bi bi-shop-window"></i>
                    Parte restaurante
                </span>

                <strong>
                    Bs {{ number_format($parteRestauranteDelivery, 2) }}
                </strong>
            </div>

        </div>

        <p class="dashboard-delivery-financial-note">
            Estos importes corresponden a entregas finalizadas en la fecha seleccionada.
            Cada pedido conserva la tarifa y los porcentajes utilizados al momento de su creación.
            {{ $entregasDeliveryDia }} {{ $entregasDeliveryDia === 1 ? 'entrega fue' : 'entregas fueron' }} contabilizada{{ $entregasDeliveryDia === 1 ? '' : 's' }} en ese día.
        </p>

    </div>

    <div class="dashboard-section chart-section">

        <div class="section-heading">

            <div>
                <span class="section-eyebrow">RENDIMIENTO</span>
                <h3>Ventas de los últimos 7 días</h3>
            </div>

            <div class="chart-total">
                <span>Ventas del mes</span>
                <strong>Bs {{ number_format($ventasMes, 2) }}</strong>
            </div>

        </div>

        <div class="dashboard-chart">
            <canvas id="ventasChart"></canvas>
        </div>

    </div>

</div>

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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ventas = @json($ventasSemana);
    const canvas = document.getElementById('ventasChart');

    if (canvas) {
        new Chart(canvas, {
            type: 'line',
            data: {
                labels: ventas.map(item => item.fecha),
                datasets: [{
                    label: 'Ventas',
                    data: ventas.map(item => Number(item.total)),
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return 'Bs ' + value; }
                        }
                    }
                }
            }
        });
    }
</script>
@endpush

@endsection
