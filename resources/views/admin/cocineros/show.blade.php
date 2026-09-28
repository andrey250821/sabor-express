@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-person-badge-fill me-2"></i>
                Detalle del cocinero
            </h2>

            <p class="text-muted mb-0">
                Información y actividad del personal de cocina.
            </p>
        </div>

        <a
            href="{{ route('admin.cocineros.index') }}"
            class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Volver a cocineros
        </a>
    </div>

    <div class="row g-4">

        <div class="col-12 col-xl-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div
                            style="
                                width: 90px;
                                height: 90px;
                                flex: 0 0 90px;
                                border-radius: 50%;
                                overflow: hidden;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                background: #8b1e45;
                                color: #fff;
                                font-size: 34px;
                                font-weight: 700;
                            ">
                            @if($cocinero->foto_perfil_url)
                                <img
                                    src="{{ $cocinero->foto_perfil_url }}"
                                    alt="Foto de {{ $cocinero->name }}"
                                    style="width:100%;height:100%;object-fit:cover;display:block;">
                            @else
                                {{ strtoupper(substr($cocinero->name, 0, 1)) }}
                            @endif
                        </div>

                        <div>
                            <h3 class="h4 mb-1">{{ $cocinero->name }}</h3>
                            <p class="text-muted mb-1">{{ $cocinero->email }}</p>

                            @if($cocinero->estado === 'activo')
                                <span class="badge text-bg-success">Activo</span>
                            @else
                                <span class="badge text-bg-secondary">Inactivo</span>
                            @endif
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="p-3 rounded bg-light">
                                <small class="text-muted d-block">Teléfono</small>
                                <strong>{{ $cocinero->telefono ?? 'No registrado' }}</strong>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="p-3 rounded bg-light h-100">
                                <small class="text-muted d-block">Rol</small>
                                <strong>{{ $cocinero->role?->nombre ?? 'Cocinero' }}</strong>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="p-3 rounded bg-light h-100">
                                <small class="text-muted d-block">Pedidos de cocina</small>
                                <strong>{{ $cocinero->pedidos_cocina_count }}</strong>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 rounded bg-light">
                                <small class="text-muted d-block">Registrado desde</small>
                                <strong>{{ $cocinero->created_at?->format('d/m/Y H:i') ?? 'No disponible' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <a
                            href="{{ route('admin.cocineros.edit', $cocinero->id) }}"
                            class="btn btn-outline-primary">
                            <i class="bi bi-pencil-square me-1"></i>
                            Editar
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-xl-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <h3 class="h5 mb-1">
                        Últimos pedidos gestionados en cocina
                    </h3>

                    <p class="text-muted mb-4">
                        Historial reciente asociado a este cocinero.
                    </p>

                    @forelse($cocinero->pedidosCocina as $pedido)
                        <div class="border rounded p-3 mb-2">
                            <div class="d-flex justify-content-between align-items-center gap-3">
                                <strong>
                                    Pedido #{{ $pedido->id }}
                                </strong>

                                <span class="badge text-bg-light">
                                    {{ ucfirst($pedido->estado) }}
                                </span>
                            </div>

                            <div class="small text-muted mt-1">
                                Cliente:
                                {{ $pedido->user?->name ?? 'No disponible' }}
                                · {{ $pedido->created_at?->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-bag-x fs-1"></i>
                            <p class="mt-3 mb-0">
                                Este cocinero todavía no tiene pedidos de cocina registrados.
                            </p>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
