@extends('layouts.cliente')

@section('title', 'Notificaciones')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <span class="text-uppercase small fw-semibold text-muted">Sabor Express</span>
            <h1 class="h2 mb-1">
                <i class="bi bi-bell-fill me-2"></i>
                Mis notificaciones
            </h1>
            <p class="text-muted mb-0">
                Aquí recibirás avisos importantes sobre tus pedidos.
            </p>
        </div>

        @if($noLeidas > 0)
            <form
                id="form-marcar-todas-notificaciones-cliente"
                action="{{ route('cliente.notificaciones.leer.todas') }}"
                method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="bi bi-check2-all me-1"></i>
                    Marcar todas como leídas
                </button>
            </form>
        @endif
    </div>

    @forelse($notificacionesAgrupadas as $fecha => $grupo)
        <div class="mb-4">
            <h2 class="h6 text-uppercase text-muted mb-3">{{ $fecha }}</h2>

            <div class="d-flex flex-column gap-3">
                @foreach($grupo as $notificacion)
                    @php
                        $esRechazo = $notificacion->evento === 'comprobante_rechazado';
                        $esCamino = $notificacion->evento === 'pedido_en_camino';
                        $esAceptado = $notificacion->evento === 'pedido_aceptado';
                        $esRevision = $notificacion->evento === 'comprobante_en_revision';
                        $esEntregado = $notificacion->evento === 'pedido_entregado';

                        $icono = match (true) {
                            $esRechazo => 'bi-x-circle-fill',
                            $esCamino => 'bi-bicycle',
                            $esAceptado => 'bi-check-circle-fill',
                            $esRevision => 'bi-hourglass-split',
                            $esEntregado => 'bi-house-check-fill',
                            default => 'bi-info-circle-fill',
                        };

                        $claseIcono = match (true) {
                            $esRechazo => 'text-danger',
                            $esCamino => 'text-primary',
                            $esAceptado => 'text-success',
                            $esRevision => 'text-warning',
                            $esEntregado => 'text-success',
                            default => 'text-secondary',
                        };

                        $titulo = match (true) {
                            $esRechazo => 'Pedido rechazado',
                            $esCamino => 'Tu pedido está en camino',
                            $esAceptado => 'Pago confirmado',
                            $esRevision => 'Pago en revisión',
                            $esEntregado => 'Pedido entregado',
                            default => 'Actualización de pedido',
                        };
                    @endphp

                    <div
                        class="card border-0 shadow-sm {{ !$notificacion->leido ? 'border-start border-4 border-primary' : '' }} notification-card-cliente {{ $notificacion->leido ? 'leida' : 'no-leida' }}"
                        data-notificacion-card="{{ $notificacion->id }}">
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-3">
                                <div class="fs-3 {{ $claseIcono }}">
                                    <i class="bi {{ $icono }}"></i>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="d-flex flex-wrap justify-content-between gap-2">
                                        <h3 class="h5 mb-1">
                                            {{ $titulo }}
                                            @if(!$notificacion->leido)
                                                <span class="badge text-bg-primary ms-1">Nueva</span>
                                            @endif
                                        </h3>

                                        <small class="text-muted">
                                            {{ $notificacion->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>

                                    <p class="mb-2">
                                        {{ $notificacion->mensaje }}
                                    </p>

                                    @if($notificacion->pedido)
                                        <div class="mb-3 small text-muted">
                                            <i class="bi bi-bag-check me-1"></i>
                                            Pedido #{{ $notificacion->pedido->id }}
                                            · Bs. {{ number_format($notificacion->pedido->total, 2) }}
                                        </div>

                                        <a
                                            href="{{ route('cliente.pedidos.show', $notificacion->pedido->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>
                                            Ver pedido
                                        </a>
                                    @endif

                                    @if(!$notificacion->leido)
                                        <form
                                            action="{{ route('cliente.notificaciones.leer', $notificacion->id) }}"
                                            method="POST"
                                            class="d-inline-block ms-2 js-marcar-notificacion-cliente">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-check2 me-1"></i>
                                                Marcar como leída
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-bell-slash display-5 text-muted"></i>
                <h2 class="h4 mt-3">No tienes notificaciones</h2>
                <p class="text-muted mb-0">
                    Aquí aparecerán los avisos sobre tus pedidos y su entrega.
                </p>
            </div>
        </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const formMarcarTodas = document.getElementById('form-marcar-todas-notificaciones-cliente');
        const badgeNavbar = document.getElementById('badge-notificaciones-cliente');

        const actualizarContador = (cantidad) => {
            if (badgeNavbar) {
                badgeNavbar.textContent = cantidad;
                badgeNavbar.classList.toggle('d-none', cantidad === 0);
            }

            if (formMarcarTodas) {
                formMarcarTodas.classList.toggle('d-none', cantidad === 0);
            }
        };

        const marcarComoLeida = async (form) => {
            const boton = form.querySelector('button[type="submit"]');
            const card = form.closest('[data-notificacion-card]');

            if (!boton || !card) return;

            boton.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('No se pudo marcar la notificación.');
                }

                const data = await response.json();

                card.classList.remove('border-start', 'border-4', 'border-primary', 'no-leida');
                card.classList.add('leida');

                form.remove();

                const badgeNueva = card.querySelector('.text-bg-primary');
                if (badgeNueva) {
                    badgeNueva.remove();
                }

                actualizarContador(data.noLeidas);
            } catch (error) {
                console.error(error);
                boton.disabled = false;
            }
        };

        document.querySelectorAll('.js-marcar-notificacion-cliente').forEach(form => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                marcarComoLeida(form);
            });
        });

        formMarcarTodas?.addEventListener('submit', async (event) => {
            event.preventDefault();

            const boton = formMarcarTodas.querySelector('button[type="submit"]');

            if (!boton) return;

            boton.disabled = true;

            try {
                const response = await fetch(formMarcarTodas.action, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('No se pudieron marcar todas las notificaciones.');
                }

                document.querySelectorAll('[data-notificacion-card]').forEach(card => {
                    card.classList.remove('border-start', 'border-4', 'border-primary', 'no-leida');
                    card.classList.add('leida');

                    const badgeNueva = card.querySelector('.text-bg-primary');
                    if (badgeNueva) {
                        badgeNueva.remove();
                    }

                    const form = card.querySelector('.js-marcar-notificacion-cliente');
                    if (form) {
                        form.remove();
                    }
                });

                actualizarContador(0);
            } catch (error) {
                console.error(error);
                boton.disabled = false;
            }
        });
    });
</script>
@endpush

