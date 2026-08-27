@extends('layouts.admin')

@section('content')

<div class="categorias-page">

    {{-- ENCABEZADO --}}
    <div class="categorias-header mb-4">

        <div>
            <h2 class="categorias-title">
                <i class="bi bi-tags"></i>
                Categorías
            </h2>

            <p class="categorias-subtitle">
                Administra las categorías de productos de Sabor Express.
            </p>
        </div>

        <a href="{{ route('admin.categorias.create') }}"
            class="btn btn-categoria-nueva">

            <i class="bi bi-plus-lg"></i>
            Nueva categoría

        </a>

    </div>


    {{-- MENSAJE --}}
    @if(session('success'))

    <div class="alert categorias-alert alert-dismissible fade show">

        <i class="bi bi-check-circle-fill me-2"></i>

        {{ session('success') }}

        <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

    @endif


    {{-- CARD --}}
    <div class="categorias-card">

        <div class="categorias-card-header">

            <div>

                <h5>
                    <i class="bi bi-collection"></i>
                    Lista de categorías
                </h5>

                <small>
                    Categorías registradas en el sistema
                </small>

            </div>


            <span class="categorias-count">

                {{ $categorias->count() }}

                {{ $categorias->count() == 1 ? 'categoría' : 'categorías' }}

            </span>

        </div>


        {{-- TABLA --}}
        @if($categorias->count())

        <div class="table-responsive">

            <table class="table categorias-table mb-0">

                <thead>

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            Categoría
                        </th>

                        <th>
                            Descripción
                        </th>

                        <th>
                            Estado
                        </th>

                        <th class="text-center">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($categorias as $categoria)

                    <tr>

                        <td>

                            <span class="categoria-id">
                                {{ $categoria->id }}
                            </span>

                        </td>


                        <td>

                            <div class="categoria-nombre">

                                <div class="categoria-icon">

                                    <i class="bi bi-tag-fill"></i>

                                </div>

                                <strong>
                                    {{ $categoria->nombre }}
                                </strong>

                            </div>

                        </td>


                        <td>

                            <span class="categoria-descripcion">

                                {{ $categoria->descripcion ?: 'Sin descripción' }}

                            </span>

                        </td>


                        <td>

                            @if($categoria->estado === 'activo')

                            <span class="categoria-estado activo">

                                <i class="bi bi-check-circle-fill"></i>

                                Activo

                            </span>

                            @else

                            <span class="categoria-estado inactivo">

                                <i class="bi bi-x-circle-fill"></i>

                                Inactivo

                            </span>

                            @endif

                        </td>


                        <td>

                            <div class="categoria-acciones">

                                <a href="{{ route('admin.categorias.edit', $categoria->id) }}"
                                    class="btn btn-categoria-editar"
                                    title="Editar">

                                    <i class="bi bi-pencil"></i>

                                    <span>Editar</span>

                                </a>


                                <form
                                    action="{{ route('admin.categorias.destroy', $categoria->id) }}"
                                    method="POST">

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-categoria-eliminar"
                                        onclick="return confirm('¿Está seguro de eliminar esta categoría?')"
                                        title="Eliminar">

                                        <i class="bi bi-trash"></i>

                                        <span>Eliminar</span>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        @else

        {{-- SIN CATEGORÍAS --}}
        <div class="categorias-empty">

            <div class="categorias-empty-icon">

                <i class="bi bi-tags"></i>

            </div>

            <h5>
                No hay categorías registradas
            </h5>

            <p>
                Crea una categoría para comenzar a organizar tus productos.
            </p>

            <a href="{{ route('admin.categorias.create') }}"
                class="btn btn-categoria-nueva">

                <i class="bi bi-plus-lg"></i>

                Crear categoría

            </a>

        </div>

        @endif

    </div>

</div>

@endsection