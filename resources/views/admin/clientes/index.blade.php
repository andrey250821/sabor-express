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
        <div class="clientes-search-bar mb-3 px-3 pt-3">
            <div class="position-relative">
                <i class="bi bi-envelope-search clientes-search-icon"></i>
                <input
                    type="search"
                    id="clientes-busqueda"
                    class="form-control clientes-search-input"
                    value="{{ $buscar ?? '' }}"
                    placeholder="Buscar cliente por Gmail o correo electrónico..."
                    autocomplete="off">
                <span
                    id="clientes-busqueda-loading"
                    class="clientes-search-loading d-none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </span>
            </div>
            <small class="clientes-search-help">
                La lista se actualiza automáticamente mientras escribes.
            </small>
        </div>

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


            <div class="clientes-count" id="clientes-count">

                {{ $clientes->count() }}
                {{ $clientes->count() == 1 ? 'cliente' : 'clientes' }}

            </div>

        </div>


        {{-- TABLA --}}
        <div id="clientes-table-container" class="clientes-table-wrapper">

            @include('admin.clientes.partials.tabla', ['clientes' => $clientes])

        </div>

    </div>

</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('clientes-busqueda');
    const container = document.getElementById('clientes-table-container');
    const count = document.getElementById('clientes-count');
    const loading = document.getElementById('clientes-busqueda-loading');

    if (!input || !container || !count) return;

    let timer = null;
    let controller = null;

    const buscarClientes = () => {
        const url = new URL('{{ route('admin.clientes.index') }}', window.location.origin);
        const value = input.value.trim();

        if (value) {
            url.searchParams.set('buscar', value);
        }

        if (controller) controller.abort();
        controller = new AbortController();

        loading?.classList.remove('d-none');

        fetch(url.toString(), {
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
            container.innerHTML = data.html;
            count.textContent = data.count + (data.count === 1 ? ' cliente' : ' clientes');
        })
        .catch(error => {
            if (error.name !== 'AbortError') {
                container.innerHTML = '<div class="p-4 text-center text-danger">No se pudo actualizar la búsqueda.</div>';
            }
        })
        .finally(() => {
            loading?.classList.add('d-none');
        });
    };

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(buscarClientes, 120);
    });
});
</script>
@endpush
@endsection