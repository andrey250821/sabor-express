@extends('layouts.admin')

@section('content')

<div class="deliverys-page">

    {{-- CABECERA --}}
    <div class="deliverys-header">

        <div>

            <h2 class="deliverys-title">
                <i class="bi bi-bicycle"></i>
                Deliverys
            </h2>

            <p class="deliverys-subtitle">
                Gestiona los Deliverys de Sabor Express
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


        <div class="deliverys-search-bar mb-3 px-3 pt-3">
            <div class="position-relative">
                <i class="bi bi-envelope-search deliverys-search-icon"></i>
                <input
                    type="search"
                    id="deliverys-busqueda"
                    class="form-control deliverys-search-input"
                    value="{{ $buscar ?? '' }}"
                    placeholder="Buscar Delivery por Gmail o correo electrónico..."
                    autocomplete="off">
                <span
                    id="deliverys-busqueda-loading"
                    class="deliverys-search-loading d-none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </span>
            </div>
            <small class="deliverys-search-help">
                La lista se actualiza automáticamente mientras escribes.
            </small>
        </div>

        <div class="deliverys-card-header">

            <div>

                <h5>
                    <i class="bi bi-people"></i>

                    Lista de Deliverys
                </h5>

                <small>
                    Personal encargado de realizar las entregas
                </small>

            </div>


            <span class="deliverys-count" id="deliverys-count">

                {{ $deliverys->count() }}

                {{ $deliverys->count() == 1 ? 'Delivery' : 'Deliverys' }}

            </span>

        </div>


        {{-- TABLA RESPONSIVE --}}
        <div id="deliverys-table-container" class="table-responsive">

            @include('admin.deliverys.partials.tabla', ['deliverys' => $deliverys])

        </div>

    </div>

</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('deliverys-busqueda');
    const container = document.getElementById('deliverys-table-container');
    const count = document.getElementById('deliverys-count');
    const loading = document.getElementById('deliverys-busqueda-loading');

    if (!input || !container || !count) return;

    let timer = null;
    let controller = null;

    const buscarDeliverys = () => {
        const url = new URL('{{ route('admin.deliverys.index') }}', window.location.origin);
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
            count.textContent = data.count + (data.count === 1 ? ' Delivery' : ' Deliverys');
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
        timer = setTimeout(buscarDeliverys, 120);
    });
});
</script>
@endpush
@endsection