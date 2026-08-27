@extends('layouts.admin')

@section('content')

<div class="productos-form-page">

    <div class="productos-form-card">

        {{-- CABECERA --}}
        <div class="productos-form-header">

            <div>

                <h3>
                    <i class="bi bi-plus-circle"></i>
                    Nuevo Producto
                </h3>

                <p>
                    Registra un nuevo producto para Sabor Express
                </p>

            </div>

            <div class="form-header-icon">

                <i class="bi bi-box-seam"></i>

            </div>

        </div>


        {{-- ERRORES --}}
        @if($errors->any())

        <div class="alert alert-danger productos-error">

            <strong>
                <i class="bi bi-exclamation-triangle-fill"></i>
                Hay errores en el formulario
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

                @endforeach

            </ul>

        </div>

        @endif


        <div class="productos-form-body">

            <form
                action="{{ route('admin.productos.store') }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf


                {{-- CATEGORIA --}}
                <div class="mb-4">

                    <label class="producto-form-label">
                        Categoría
                    </label>

                    <select
                        name="categoria_id"
                        class="form-control producto-input"
                        required>

                        <option value="">
                            Seleccione una categoría
                        </option>

                        @foreach($categorias as $categoria)

                        <option
                            value="{{ $categoria->id }}"
                            {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>

                            {{ $categoria->nombre }}

                        </option>

                        @endforeach

                    </select>

                </div>


                {{-- NOMBRE --}}
                <div class="mb-4">

                    <label class="producto-form-label">
                        Nombre del producto
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        class="form-control producto-input"
                        value="{{ old('nombre') }}"
                        placeholder="Ejemplo: Hamburguesa clásica"
                        required>

                </div>


                {{-- DESCRIPCION --}}
                <div class="mb-4">

                    <label class="producto-form-label">
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        class="form-control producto-input"
                        rows="4"
                        placeholder="Describe brevemente el producto...">{{ old('descripcion') }}</textarea>

                </div>


                {{-- PRECIO Y STOCK --}}
                <div class="row g-3 mb-4">

                    <div class="col-md-6">

                        <label class="producto-form-label">
                            Precio (Bs)
                        </label>

                        <div class="input-group">

                            <span class="input-group-text producto-input-icon">
                                Bs
                            </span>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="precio"
                                class="form-control producto-input"
                                value="{{ old('precio') }}"
                                placeholder="0.00"
                                required>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <label class="producto-form-label">
                            Stock
                        </label>

                        <input
                            type="number"
                            min="0"
                            name="stock"
                            class="form-control producto-input"
                            value="{{ old('stock') }}"
                            placeholder="0"
                            required>

                    </div>

                </div>


                <!-- IMAGEN DEL PRODUCTO -->
                <div class="mb-4">

                    <label class="form-label fw-bold">
                        Imagen del producto
                    </label>

                    <input
                        type="file"
                        name="imagen"
                        id="imagen"
                        class="form-control"
                        accept="image/*">

                    <small class="text-muted">
                        Seleccione una imagen para visualizarla antes de guardar.
                    </small>

                    <!-- PREVISUALIZACIÓN -->
                    <div id="preview-container" class="preview-container d-none mt-3">

                        <div class="preview-header">
                            <i class="bi bi-image"></i>
                            Vista previa
                        </div>

                        <div class="preview-body">
                            <img
                                id="preview-imagen"
                                src=""
                                alt="Vista previa"
                                class="preview-imagen">
                        </div>

                    </div>

                </div>


                {{-- BOTONES --}}
                <div class="productos-form-actions">

                    <button
                        type="submit"
                        class="btn btn-success btn-producto-guardar">

                        <i class="bi bi-check-circle-fill"></i>
                        Guardar Producto

                    </button>


                    <a
                        href="{{ route('admin.productos.index') }}"
                        class="btn btn-secondary btn-producto-cancelar">

                        <i class="bi bi-arrow-left"></i>
                        Volver

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection
@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const inputImagen = document.getElementById('imagen');

        const previewContainer =
            document.getElementById('preview-container');

        const previewImagen =
            document.getElementById('preview-imagen');


        if (!inputImagen) {
            return;
        }


        inputImagen.addEventListener('change', function(event) {

            const archivo = event.target.files[0];


            if (!archivo) {

                previewContainer.classList.add('d-none');

                previewImagen.src = '';

                return;

            }


            if (!archivo.type.startsWith('image/')) {

                previewContainer.classList.add('d-none');

                previewImagen.src = '';

                return;

            }


            const url = URL.createObjectURL(archivo);


            previewImagen.src = url;

            previewContainer.classList.remove('d-none');


            previewImagen.onload = function() {

                URL.revokeObjectURL(url);

            };

        });

    });
</script>

@endpush