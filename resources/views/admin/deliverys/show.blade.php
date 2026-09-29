@extends('layouts.admin')

@section('title', 'Detalle de Delivery')

@section('content')

<div class="delivery-admin-detail-page">

    <div class="delivery-admin-detail-header">

        <div>
            <a
                href="{{ route('admin.deliverys.index') }}"
                class="delivery-admin-detail-back">

                <i class="bi bi-arrow-left"></i>
                Volver a Delivery

            </a>

            <span class="delivery-admin-detail-eyebrow">
                GESTIÓN DE DELIVERY
            </span>

            <h2 class="delivery-admin-detail-title">
                {{ $delivery->name }}
            </h2>

            <p class="delivery-admin-detail-subtitle">
                Consulta el rendimiento operativo y económico de este Delivery.
            </p>
        </div>

        <a
            href="{{ route('admin.deliverys.edit', $delivery->id) }}"
            class="btn btn-outline-light">

            <i class="bi bi-pencil-fill me-1"></i>
            Editar Delivery

        </a>

    </div>


    <div class="delivery-admin-profile-card mb-4">

        <div class="delivery-admin-profile-avatar">
            @if($delivery->foto_perfil_url)
                <img
                    src="{{ $delivery->foto_perfil_url }}"
                    alt="Foto de {{ $delivery->name }}">
            @else
                <span>
                    {{ strtoupper(substr($delivery->name ?? 'D', 0, 1)) }}
                </span>
            @endif
        </div>

        <div class="delivery-admin-profile-info">

            <strong>{{ $delivery->name }}</strong>

            <span>
                <i class="bi bi-envelope me-1"></i>
                {{ $delivery->email }}
            </span>

            @if($delivery->telefono)
                <span>
                    <i class="bi bi-telephone me-1"></i>
                    {{ $delivery->telefono }}
                </span>
            @endif

            <span class="{{ $delivery->estado === 'activo' ? 'text-success' : 'text-secondary' }}">
                <i class="bi bi-circle-fill me-1"></i>
                {{ ucfirst($delivery->estado) }}
            </span>

        </div>

    </div>


    {{-- ESTADÍSTICAS OPERATIVAS --}}
    <div class="delivery-admin-stat-grid mb-4">

        <div class="delivery-admin-stat-card">
            <span>
                <i class="bi bi-check2-all"></i>
                Pedidos entregados
            </span>

            <strong>{{ $pedidosEntregados->count() }}</strong>
        </div>

        <div class="delivery-admin-stat-card">
            <span>
                <i class="bi bi-bicycle"></i>
                Pedidos activos
            </span>

            <strong>{{ $pedidosActivos }}</strong>
        </div>

        <div class="delivery-admin-stat-card financial">
            <span>
                <i class="bi bi-cash-stack"></i>
                Total cobrado por entregas
            </span>

            <strong>
                Bs {{ number_format($totalDeliveryGenerado, 2) }}
            </strong>
        </div>

        <div class="delivery-admin-stat-card commission">
            <span>
                <i class="bi bi-person-badge-fill"></i>
                Comisión Delivery
            </span>

            <strong>
                Bs {{ number_format($comisionDelivery, 2) }}
            </strong>
        </div>

        <div class="delivery-admin-stat-card restaurant">
            <span>
                <i class="bi bi-shop-window"></i>
                Parte restaurante
            </span>

            <strong>
                Bs {{ number_format($parteRestaurante, 2) }}
            </strong>
        </div>

    </div>


    {{-- HISTORIAL --}}
    <div class="delivery-admin-history-card">

        <div class="delivery-admin-history-header">

            <div>
                <span>HISTORIAL</span>

                <h3>
                    Pedidos realizados por {{ $delivery->name }}
                </h3>
            </div>

            <span class="delivery-admin-history-count">
                {{ $asignaciones->count() }}
                {{ $asignaciones->count() === 1 ? 'asignación' : 'asignaciones' }}
            </span>

        </div>


        <div class="table-responsive">

            <table class="table delivery-admin-history-table align-middle mb-0">

                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Fecha</th>
                        <th>Distancia</th>
                        <th>Tarifa</th>
                        <th>Comisión</th>
                        <th>Restaurante</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($asignaciones as $asignacion)

                        @php
                            $pedido = $asignacion->pedido;
                        @endphp

                        <tr>

                            <td>
                                @if($pedido)
                                    <a
                                        href="{{ route('admin.pedidos.show', $pedido->id) }}"
                                        class="delivery-admin-order-link">

                                        #{{ $pedido->id }}

                                    </a>
                                @else
                                    Pedido eliminado
                                @endif
                            </td>

                            <td>
                                {{ $asignacion->created_at?->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                {{ $pedido?->distancia_delivery_km !== null
                                    ? number_format((float) $pedido->distancia_delivery_km, 2) . ' km'
                                    : '—' }}
                            </td>

                            <td>
                                Bs {{ number_format((float) ($pedido?->tarifa_delivery ?? 0), 2) }}
                            </td>

                            <td class="text-success fw-semibold">
                                Bs {{ number_format((float) ($pedido?->monto_delivery ?? 0), 2) }}
                            </td>

                            <td>
                                Bs {{ number_format((float) ($pedido?->monto_restaurante_delivery ?? 0), 2) }}
                            </td>

                            <td>
                                @switch($asignacion->estado)
                                    @case('aceptado')
                                        <span class="delivery-admin-state assigned">
                                            Asignado
                                        </span>
                                        @break

                                    @case('en_camino')
                                        <span class="delivery-admin-state route">
                                            En camino
                                        </span>
                                        @break

                                    @case('entregado')
                                        <span class="delivery-admin-state delivered">
                                            Entregado
                                        </span>
                                        @break

                                    @default
                                        <span class="delivery-admin-state">
                                            {{ ucfirst(str_replace('_', ' ', $asignacion->estado)) }}
                                        </span>
                                @endswitch
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="delivery-admin-empty">
                                Este Delivery todavía no tiene asignaciones registradas.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
