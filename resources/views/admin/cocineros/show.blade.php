@extends('layouts.admin')

@section('content')

<div class="admin-cocinero-page">

    {{-- ENCABEZADO --}}
    <div class="admin-cocinero-detail-header">

        <div>
            <span class="admin-cocinero-detail-eyebrow">
                PERSONAL DE COCINA
            </span>

            <h2 class="admin-cocinero-detail-title">
                <i class="bi bi-person-badge-fill"></i>
                Detalle del cocinero
            </h2>

            <p class="admin-cocinero-detail-subtitle">
                Consulta el perfil y la actividad de preparación de pedidos.
            </p>
        </div>

        <a
            href="{{ route('admin.cocineros.index') }}"
            class="btn admin-cocinero-back">
            <i class="bi bi-arrow-left"></i>
            <span>Volver a cocineros</span>
        </a>

    </div>


    <div class="admin-cocinero-detail-grid">

        {{-- PERFIL --}}
        <div class="admin-cocinero-profile-card">

            <div class="admin-cocinero-profile-head">

                <a
                    href="{{ route('perfil.foto', $cocinero->id) }}"
                    class="admin-cocinero-profile-avatar"
                    title="Ver foto de {{ $cocinero->name }}">

                    @if($cocinero->foto_perfil_url)
                        <img
                            src="{{ $cocinero->foto_perfil_url }}"
                            alt="Foto de {{ $cocinero->name }}"
                            class="admin-cocinero-profile-avatar-image">
                    @else
                        <span>
                            {{ strtoupper(substr($cocinero->name, 0, 1)) }}
                        </span>
                    @endif

                    <span class="admin-cocinero-avatar-overlay">
                        <i class="bi bi-camera-fill"></i>
                    </span>

                </a>

                <div class="admin-cocinero-profile-identity">

                    <span class="admin-cocinero-role-label">
                        COCINERO
                    </span>

                    <h3>
                        {{ $cocinero->name }}
                    </h3>

                    <a
                        href="mailto:{{ $cocinero->email }}"
                        class="admin-cocinero-email">
                        <i class="bi bi-envelope-fill"></i>
                        {{ $cocinero->email }}
                    </a>

                    @if($cocinero->estado === 'activo')
                        <span class="admin-cocinero-status activo">
                            <i class="bi bi-check-circle-fill"></i>
                            Activo
                        </span>
                    @else
                        <span class="admin-cocinero-status inactivo">
                            <i class="bi bi-x-circle-fill"></i>
                            Inactivo
                        </span>
                    @endif

                </div>

            </div>


            {{-- DATOS --}}
            <div class="admin-cocinero-profile-data">

                <div class="admin-cocinero-info-box">
                    <span class="admin-cocinero-info-label">
                        <i class="bi bi-telephone-fill"></i>
                        Teléfono
                    </span>
                    <strong>
                        {{ $cocinero->telefono ?? 'No registrado' }}
                    </strong>
                </div>

                <div class="admin-cocinero-info-box">
                    <span class="admin-cocinero-info-label">
                        <i class="bi bi-person-vcard-fill"></i>
                        Rol
                    </span>
                    <strong>
                        {{ $cocinero->role?->nombre ?? 'Cocinero' }}
                    </strong>
                </div>

                <div class="admin-cocinero-info-box highlight">
                    <span class="admin-cocinero-info-label">
                        <i class="bi bi-bag-check-fill"></i>
                        Pedidos de cocina
                    </span>
                    <strong class="admin-cocinero-info-number">
                        {{ $cocinero->pedidos_cocina_fecha_count }}
                    </strong>
                    <small>
                        En la fecha seleccionada
                    </small>
                </div>

                <div class="admin-cocinero-info-box">
                    <span class="admin-cocinero-info-label">
                        <i class="bi bi-calendar3"></i>
                        Registrado desde
                    </span>
                    <strong>
                        {{ $cocinero->created_at?->format('d/m/Y') ?? 'No disponible' }}
                    </strong>
                    <small>
                        {{ $cocinero->created_at?->format('H:i') ?? '' }}
                    </small>
                </div>

            </div>


            <div class="admin-cocinero-profile-actions">

                <a
                    href="{{ route('admin.cocineros.edit', $cocinero->id) }}"
                    class="btn admin-cocinero-action-edit">
                    <i class="bi bi-pencil-square"></i>
                    Editar cocinero
                </a>

                <a
                    href="mailto:{{ $cocinero->email }}"
                    class="btn admin-cocinero-action-email">
                    <i class="bi bi-envelope-fill"></i>
                    Enviar correo
                </a>

            </div>

        </div>


        {{-- ACTIVIDAD --}}
        <div class="admin-cocinero-activity-card">

            <div class="admin-cocinero-activity-header">

                <div>
                    <span class="admin-cocinero-detail-eyebrow">
                        ACTIVIDAD
                    </span>

                    <h3>
                        Pedidos gestionados en cocina
                    </h3>

                    <p>
                        Pedidos asociados al cocinero durante la fecha seleccionada.
                    </p>
                </div>

                <span class="admin-cocinero-activity-count">
                    {{ $cocinero->pedidos_cocina_fecha_count }}
                    {{ $cocinero->pedidos_cocina_fecha_count === 1 ? 'pedido' : 'pedidos' }}
                </span>

            </div>

            <div class="admin-cocinero-activity-filter">
                @include('shared.date-filter', [
                    'fechaSeleccionada' => $fechaSeleccionada,
                    'tituloFecha' => 'Actividad de cocina'
                ])
            </div>


            <div class="admin-cocinero-orders">

                @forelse($cocinero->pedidosCocina as $pedido)

                    @php
                        $estadoPedido = match($pedido->estado) {
                            'pagado' => ['clase' => 'pagado', 'texto' => 'Pagado', 'icono' => 'bi-credit-card-fill'],
                            'preparando' => ['clase' => 'preparando', 'texto' => 'Preparando', 'icono' => 'bi-fire'],
                            'listo' => ['clase' => 'listo', 'texto' => 'Listo', 'icono' => 'bi-check-circle-fill'],
                            'asignado' => ['clase' => 'asignado', 'texto' => 'Asignado', 'icono' => 'bi-person-check-fill'],
                            'en_camino' => ['clase' => 'camino', 'texto' => 'En camino', 'icono' => 'bi-bicycle'],
                            'entregado' => ['clase' => 'entregado', 'texto' => 'Entregado', 'icono' => 'bi-check2-all'],
                            'cancelado' => ['clase' => 'cancelado', 'texto' => 'Cancelado', 'icono' => 'bi-x-circle-fill'],
                            default => ['clase' => 'default', 'texto' => ucfirst(str_replace('_', ' ', $pedido->estado)), 'icono' => 'bi-info-circle-fill'],
                        };
                    @endphp

                    <a
                        href="{{ route('admin.pedidos.show', $pedido->id) }}"
                        class="admin-cocinero-order-row">

                        <div class="admin-cocinero-order-id">
                            #{{ $pedido->id }}
                        </div>

                        <div class="admin-cocinero-order-main">
                            <strong>
                                {{ $pedido->user?->name ?? 'Cliente no disponible' }}
                            </strong>
                            <small>
                                <i class="bi bi-clock"></i>
                                {{ $pedido->created_at?->copy()->timezone(AppServicesFechaFiltroService::TIMEZONE)->format('d/m/Y H:i') }}
                            </small>
                        </div>

                        <div class="admin-cocinero-order-total">
                            Bs {{ number_format($pedido->total, 2) }}
                        </div>

                        <span class="admin-cocinero-order-state {{ $estadoPedido['clase'] }}">
                            <i class="bi {{ $estadoPedido['icono'] }}"></i>
                            {{ $estadoPedido['texto'] }}
                        </span>

                        <i class="bi bi-chevron-right admin-cocinero-order-arrow"></i>

                    </a>

                @empty

                    <div class="admin-cocinero-empty">

                        <div class="admin-cocinero-empty-icon">
                            <i class="bi bi-bag-x-fill"></i>
                        </div>

                        <h4>
                            No hay pedidos para esta fecha
                        </h4>

                        <p>
                            Cuando este cocinero gestione pedidos, aparecerán aquí.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>

    </div>

</div>

@endsection