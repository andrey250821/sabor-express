@extends('layouts.admin')

@section('content')

<div class="categorias-form-page">

    <!-- ENCABEZADO -->
    <div class="categorias-form-header mb-4">

        <div>
            <h2 class="categorias-form-title">
                <i class="bi bi-plus-circle-fill"></i>
                Nueva Categoría
            </h2>

            <p class="categorias-form-subtitle">
                Registra una nueva categoría para organizar los productos.
            </p>
        </div>

        <a href="{{ route('admin.categorias.index') }}"
            class="btn btn-outline-light categorias-btn-volver">

            <i class="bi bi-arrow-left"></i>
            Volver

        </a>

    </div>


    <!-- FORMULARIO -->
    <div class="card categorias-form-card">

        <div class="card-header categorias-form-card-header">

            <div class="d-flex align-items-center gap-3">

                <div class="categorias-icon">

                    <i class="bi bi-tags-fill"></i>

                </div>

                <div>

                    <h5 class="mb-1">
                        Información de la categoría
                    </h5>

                    <small>
                        Completa los datos solicitados
                    </small>

                </div>

            </div>

        </div>


        <div class="card-body categorias-form-body">

            {{-- ERRORES --}}
            @if ($errors->any())

            <div class="alert categorias-alert-error">

                <div class="fw-bold mb-2">

                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Hay algunos errores:

                </div>

                <ul class="mb-0">

                    @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

            @endif


            <form action="{{ route('admin.categorias.store') }}"
                method="POST">

                @csrf


                <!-- NOMBRE -->
                <div class="mb-4">

                    <label for="nombre"
                        class="categorias-label">

                        <i class="bi bi-tag-fill"></i>
                        Nombre de categoría

                    </label>


                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        value="{{ old('nombre') }}"
                        class="form-control categorias-input @error('nombre') is-invalid @enderror"
                        placeholder="Ejemplo: Hamburguesas"
                        required>


                    @error('nombre')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                    @enderror


                    <small class="categorias-help">

                        Ejemplo: Hamburguesas, Bebidas, Postres, Combos...

                    </small>

                </div>


                <!-- DESCRIPCIÓN -->
                <div class="mb-4">

                    <label for="descripcion"
                        class="categorias-label">

                        <i class="bi bi-card-text"></i>
                        Descripción

                    </label>


                    <textarea
                        id="descripcion"
                        name="descripcion"
                        class="form-control categorias-input @error('descripcion') is-invalid @enderror"
                        rows="5"
                        placeholder="Escribe una breve descripción de la categoría...">{{ old('descripcion') }}</textarea>


                    @error('descripcion')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                    @enderror


                    <small class="categorias-help">

                        La descripción es opcional.

                    </small>

                </div>


                <!-- BOTONES -->
                <div class="categorias-form-actions">

                    <a href="{{ route('admin.categorias.index') }}"
                        class="btn btn-secondary categorias-btn-cancelar">

                        <i class="bi bi-x-circle"></i>
                        Cancelar

                    </a>


                    <button type="submit"
                        class="btn categorias-btn-guardar">

                        <i class="bi bi-check-circle-fill"></i>
                        Guardar Categoría

                    </button>

                </div>


            </form>

        </div>

    </div>

</div>

@endsection