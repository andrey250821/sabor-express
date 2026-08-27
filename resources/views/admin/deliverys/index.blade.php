@extends('layouts.admin')

@section('content')

<div class="deliverys-page">

    {{-- CABECERA --}}
    <div class="deliverys-header">

        <div>

            <h2 class="deliverys-title">
                <i class="bi bi-bicycle"></i>
                Repartidores
            </h2>

            <p class="deliverys-subtitle">
                Gestiona los repartidores de Sabor Express
            </p>

        </div>


        <a href="{{ route('admin.deliverys.create') }}"
            class="btn btn-success deliverys-btn-nuevo">

            <i class="bi bi-plus-circle"></i>

            Nuevo repartidor

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

                    Lista de repartidores
                </h5>

                <small>
                    Personal encargado de realizar las entregas
                </small>

            </div>


            <span class="deliverys-count">

                {{ $deliverys->count() }}

                repartidor(es)

            </span>

        </div>


        {{-- TABLA RESPONSIVE --}}
        <div class="table-responsive">

            <table class="table deliverys-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Repartidor</th>

                        <th>Contacto</th>

                        <th>Asignaciones</th>

                        <th>Estado</th>

                        <th class="text-center">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($deliverys as $delivery)

                    <tr>

                        {{-- ID --}}
                        <td>

                            <span class="delivery-id">

                                #{{ $delivery->id }}

                            </span>

                        </td>


                        {{-- NOMBRE --}}
                        <td>

                            <div class="delivery-person">

                                <div class="delivery-avatar">

                                    <i class="bi bi-person-fill"></i>

                                </div>


                                <div>

                                    <strong>
                                        {{ $delivery->name }}
                                    </strong>

                                    <small>
                                        Repartidor
                                    </small>

                                </div>

                            </div>

                        </td>


                        {{-- CONTACTO --}}
                        <td>

                            <div class="delivery-contact">

                                <span>

                                    <i class="bi bi-envelope"></i>

                                    {{ $delivery->email }}

                                </span>


                                @if($delivery->telefono)

                                <span>

                                    <i class="bi bi-telephone"></i>

                                    {{ $delivery->telefono }}

                                </span>

                                @endif

                            </div>

                        </td>


                        {{-- ASIGNACIONES --}}
                        <td>

                            <span class="delivery-asignaciones">

                                <i class="bi bi-box-seam"></i>

                                {{ $delivery->asignaciones_delivery_count }}

                            </span>

                        </td>


                        {{-- ESTADO --}}
                        <td>

                            @if($delivery->estado === 'activo')

                            <span class="delivery-estado activo">

                                <i class="bi bi-check-circle-fill"></i>

                                Activo

                            </span>

                            @else

                            <span class="delivery-estado inactivo">

                                <i class="bi bi-x-circle-fill"></i>

                                Inactivo

                            </span>

                            @endif

                        </td>


                        {{-- ACCIONES --}}
                        <td>

                            <div class="delivery-actions">


                                {{-- EDITAR --}}
                                <a
                                    href="{{ route('admin.deliverys.edit', $delivery->id) }}"
                                    class="btn-delivery editar"
                                    title="Editar">

                                    <i class="bi bi-pencil-square"></i>

                                </a>


                                @if($delivery->estado === 'activo')

                                {{-- DESACTIVAR --}}
                                <form
                                    action="{{ route('admin.deliverys.destroy', $delivery->id) }}"
                                    method="POST">

                                    @csrf

                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        class="btn-delivery eliminar"
                                        title="Desactivar"
                                        onclick="return confirm('¿Deseas desactivar este repartidor?')">

                                        <i class="bi bi-person-dash"></i>

                                    </button>

                                </form>

                                @else

                                {{-- ACTIVAR --}}
                                <form
                                    action="{{ route('admin.deliverys.activar', $delivery->id) }}"
                                    method="POST">

                                    @csrf

                                    @method('PATCH')


                                    <button
                                        type="submit"
                                        class="btn-delivery activar"
                                        title="Activar">

                                        <i class="bi bi-person-check"></i>

                                    </button>

                                </form>

                                @endif


                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="6">

                            <div class="deliverys-empty">

                                <i class="bi bi-bicycle"></i>

                                <h5>
                                    No hay repartidores registrados
                                </h5>

                                <p>
                                    Crea el primer repartidor para comenzar.
                                </p>


                                <a
                                    href="{{ route('admin.deliverys.create') }}"
                                    class="btn btn-success">

                                    <i class="bi bi-plus-circle"></i>

                                    Crear repartidor

                                </a>

                            </div>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection