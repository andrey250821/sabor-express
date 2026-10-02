@extends('layouts.admin')

@section('content')

<div class="categorias-page container-fluid px-0">

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

    @if(session('error'))

    <div class="alert categorias-error alert-dismissible fade show">

        <i class="bi bi-exclamation-triangle-fill me-2"></i>

        {{ session('error') }}

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

                                <a
                                    href="{{ route('admin.categorias.edit', $categoria->id) }}"
                                    class="btn btn-categoria-editar"
                                    title="Editar categoría">

                                    <i class="bi bi-pencil"></i>
                                    <span>Editar</span>

                                </a>

                                @if($categoria->estado === 'activo')

                                <form
                                    action="{{ route('admin.categorias.inactivar', $categoria->id) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="btn btn-categoria-inactivar"
                                        title="Inactivar categoría"
                                        onclick="return confirm('¿Deseas inactivar esta categoría? Los productos asociados conservarán su información.')">

                                        <i class="bi bi-eye-slash-fill"></i>
                                        <span>Inactivar</span>

                                    </button>

                                </form>

                                @else

                                <form
                                    action="{{ route('admin.categorias.activar', $categoria->id) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="btn btn-categoria-activar"
                                        title="Activar categoría">

                                        <i class="bi bi-eye-fill"></i>
                                        <span>Activar</span>

                                    </button>

                                </form>

                                @endif

                                <form
                                    action="{{ route('admin.categorias.destroy', $categoria->id) }}"
                                    method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-categoria-eliminar"
                                        onclick="return confirm('¿Está seguro de eliminar esta categoría? Solo se podrá eliminar si no tiene productos asociados.')"
                                        title="Eliminar categoría">

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