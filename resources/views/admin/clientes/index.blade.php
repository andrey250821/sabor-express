@extends('layouts.admin')

@section('content')

<div class="container-fluid px-0 clientes-page">

    {{-- ENCABEZADO --}}
    <div class="clientes-header">

        <div>

            <h2 class="clientes-title">
                Clientes registrados
            </h2>

            <p class="clientes-subtitle">
                Administración de clientes de Sabor Express
            </p>

        </div>

    </div>


    {{-- MENSAJE SUCCESS --}}
    @if(session('success'))

    <div class="alert alert-success alert-dismissible fade show clientes-alert">

        {{ session('success') }}

        <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

    @endif


    {{-- MENSAJE ERROR --}}
    @if(session('error'))

    <div class="alert alert-danger alert-dismissible fade show clientes-alert">

        {{ session('error') }}

        <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

    @endif


    {{-- TARJETA --}}
    <div class="card clientes-card border-0">

        {{-- CABECERA --}}
        <div class="clientes-card-header">

            <div class="clientes-card-header-left">

                <div class="clientes-card-icon">
                    <i class="bi bi-people-fill"></i>
                </div>

                <div>

                    <h5 class="clientes-card-title">
                        Lista de clientes
                    </h5>

                    <p class="clientes-card-description">
                        Clientes registrados en el sistema
                    </p>

                </div>

            </div>


            <div class="clientes-count">

                {{ $clientes->count() }}
                {{ $clientes->count() == 1 ? 'cliente' : 'clientes' }}

            </div>

        </div>


        {{-- BUSCADOR POR EMAIL --}}
    <div class="px-4 py-3 border-bottom">
        <label for="buscar-clientes" class="form-label mb-2 fw-semibold">
            <i class="bi bi-search me-1"></i>
            Buscar cliente por Gmail / correo electrónico
        </label>

        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-envelope"></i>
            </span>

            <input
                type="search"
                id="buscar-clientes"
                class="form-control"
                placeholder="Escribe el Gmail del cliente..."
                autocomplete="off">
        </div>

        <small class="text-muted d-block mt-2">
            Los resultados se actualizan automáticamente mientras escribes.
        </small>
    </div>


    {{-- TABLA --}}
        <div class="clientes-table-wrapper">

            <table class="clientes-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Nombre</th>

                        <th>Email</th>

                        <th>Teléfono</th>

                        <th>Pedidos</th>

                        <th>Estado</th>

                        <th class="text-center">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody id="clientes-resultados">

                    @include('admin.clientes._tabla', ['clientes' => $clientes])

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('buscar-clientes');
        const resultados = document.getElementById('clientes-resultados');
        const contador = document.querySelector('.clientes-count');
        let controller = null;
        let timer = null;

        if (!input || !resultados || !contador) return;

        const buscarClientes = () => {
            const buscar = input.value.trim();

            if (controller) controller.abort();
            controller = new AbortController();

            const url = new URL(@json(route('admin.clientes.index')), window.location.origin);
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
                    contador.textContent = data.count + ' ' + (data.count === 1 ? 'cliente' : 'clientes');
                })
                .catch(error => {
                    if (error.name !== 'AbortError') console.error(error);
                });
        };

        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(buscarClientes, 120);
        });
    });
</script>
@endpush
