@extends('layouts.admin')

@section('content')

<div class="deliverys-page">

    {{-- CABECERA --}}
    <div class="deliverys-header">

        <div>

            <h2 class="deliverys-title">
                <i class="bi bi-bicycle"></i>
                Delivery
            </h2>

            <p class="deliverys-subtitle">
                Gestiona el personal de Delivery de Sabor Express
            </p>

        </div>


        <a href="{{ route('admin.deliverys.create') }}"
            class="btn btn-success deliverys-btn-nuevo">

            <i class="bi bi-plus-circle"></i>

            Nuevo Delivery

        </a>

    </div>


    {{-- MENSAJE --}}
    @if(session('success'))

    <div class="alert deliverys-alert">

        <i class="bi bi-check-circle-fill"></i>

        {{ session('success') }}

    </div>

    @endif


    {{-- CARD --}}
    <div class="deliverys-card">


        <div class="deliverys-card-header">

            <div>

                <h5>
                    <i class="bi bi-people"></i>

                    Lista de Delivery
                </h5>

                <small>
                    Personal encargado de realizar las entregas
                </small>

            </div>


            <span class="deliverys-count">

                {{ $deliverys->count() }}

                Delivery(s)

            </span>

        </div>


        {{-- BUSCADOR POR EMAIL --}}
        <div class="px-4 py-3 border-bottom">
            <label for="buscar-deliverys" class="form-label mb-2 fw-semibold">
                <i class="bi bi-search me-1"></i>
                Buscar Delivery por Gmail / correo electrónico
            </label>

            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                </span>

                <input
                    type="search"
                    id="buscar-deliverys"
                    class="form-control"
                    placeholder="Escribe el Gmail del Delivery..."
                    autocomplete="off">
            </div>

            <small class="text-muted d-block mt-2">
                Los resultados se actualizan automáticamente mientras escribes.
            </small>
        </div>


        {{-- TABLA RESPONSIVE --}}
        <div class="table-responsive">

            <table class="table deliverys-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Delivery</th>

                        <th>Contacto</th>

                        <th>Asignaciones</th>

                        <th>Estado</th>

                        <th class="text-center">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody id="deliverys-resultados">

                    @include('admin.deliverys._tabla', ['deliverys' => $deliverys])

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('buscar-deliverys');
        const resultados = document.getElementById('deliverys-resultados');
        const contador = document.querySelector('.deliverys-count');
        let controller = null;
        let timer = null;

        if (!input || !resultados || !contador) return;

        const buscarDeliverys = () => {
            const buscar = input.value.trim();

            if (controller) controller.abort();
            controller = new AbortController();

            const url = new URL(@json(route('admin.deliverys.index')), window.location.origin);
            if (buscar !== '') url.searchParams.set('buscar', buscar);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                signal: controller.signal
            })
                .then(response => {
                    if (!response.ok) throw new Error('No se pudo realizar la búsqueda.');
                    return response.json();
                })
                .then(data => {
                    resultados.innerHTML = data.html;
                    contador.textContent = data.count + ' ' + (data.count === 1 ? 'Delivery' : 'Deliverys');
                })
                .catch(error => {
                    if (error.name !== 'AbortError') console.error(error);
                });
        };

        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(buscarDeliverys, 120);
        });
    });
</script>
@endpush
