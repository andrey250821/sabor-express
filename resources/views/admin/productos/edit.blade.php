@extends('layouts.admin')

@section('content')

<div class="productos-form-page">

    <div class="productos-form-card">

        <div class="productos-form-header edit-header">

            <div>

                <h3>
                    <i class="bi bi-pencil-square"></i>
                    Editar Producto
                </h3>

                <p>
                    Modifica la información de {{ $producto->nombre }}
                </p>

            </div>

            <div class="form-header-icon">

                <i class="bi bi-pencil"></i>

            </div>

        </div>


        {{-- MENSAJE --}}
        @if(session('success'))

        <div class="alert alert-success productos-success">

            <i class="bi bi-check-circle-fill"></i>

            {{ session('success') }}

        </div>

        @endif


        {{-- ERRORES --}}
        @if($errors->any())

        <div class="alert alert-danger productos-error">

            <strong>
                <i class="bi bi-exclamation-triangle-fill"></i>
                Corrige los siguientes errores:
            </strong>

            <ul class="mb-0">

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
                action="{{ route('admin.productos.update', $producto->id) }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf
                @method('PUT')


                {{-- CATEGORIA --}}
                <div class="mb-4">

                    <label class="producto-form-label">
                        Categoría
                    </label>

                    <select
                        name="categoria_id"
                        class="form-control producto-input"
                        required>

                        @foreach($categorias as $categoria)

                        <option
                            value="{{ $categoria->id }}"
                            {{ $producto->categoria_id == $categoria->id ? 'selected' : '' }}>

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
                        value="{{ $producto->nombre }}"
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
                        rows="4">{{ $producto->descripcion }}</textarea>

                </div>


                <!-- IMAGEN DEL PRODUCTO -->

                <div class="mb-4">

                    <label class="form-label fw-bold">
                        Imagen del producto
                    </label>


                    <div class="row g-3">

                        <!-- IMAGEN ACTUAL -->

                        <div class="col-md-6">

                            <div class="preview-producto">

                                <div class="preview-header">

                                    <i class="bi bi-image"></i>

                                    Imagen actual

                                </div>


                                <div class="preview-body">

                                    @if($producto->imagen)

                                    <div class="preview-imagen-box">

                                        <img
                                            src="{{ Storage::url($producto->imagen) }}"
                                            alt="Imagen actual"
                                            class="preview-imagen"
                                            id="imagenActual">

                                    </div>

                                    @else

                                    <div class="sin-imagen">

                                        <i class="bi bi-image"></i>

                                        <span>
                                            No tiene imagen
                                        </span>

                                    </div>

                                    @endif

                                </div>

                            </div>

                        </div>


                        <!-- NUEVA IMAGEN -->

                        <div class="col-md-6">

                            <div class="preview-producto">

                                <div class="preview-header nueva">

                                    <i class="bi bi-image"></i>

                                    Nueva imagen

                                </div>


                                <div class="preview-body">

                                    <div
                                        id="previewEditContainer"
                                        class="preview-imagen-box d-none">

                                        <img
                                            id="previewEdit"
                                            src=""
                                            alt="Nueva imagen"
                                            class="preview-imagen">

                                    </div>


                                    <div
                                        id="sinNuevaImagen"
                                        class="sin-imagen">

                                        <i class="bi bi-cloud-upload"></i>

                                        <span>
                                            Seleccione una nueva imagen
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- INPUT -->

                    <div class="mt-3">

                        <label class="form-label fw-bold">

                            Cambiar imagen

                        </label>


                        <input
                            type="file"
                            name="imagen"
                            id="imagenEdit"
                            class="form-control"
                            accept="image/*">


                        <small class="text-muted">

                            Si no selecciona una nueva imagen,
                            se conservará la actual.

                        </small>

                    </div>

                </div>


                {{-- PRECIO Y STOCK --}}
                <div class="row g-3 mb-4">

                    <div class="col-md-6">

                        <label class="producto-form-label">
                            Precio (Bs)
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="precio"
                            class="form-control producto-input"
                            value="{{ $producto->precio }}"
                            required>

                    </div>


                    <div class="col-md-6">

                        <label class="producto-form-label">
                            Stock disponible
                        </label>

                        <input
                            type="number"
                            min="0"
                            name="stock"
                            class="form-control producto-input"
                            value="{{ $producto->stock }}"
                            required>

                    </div>

                </div>


                {{-- ESTADO --}}
                <div class="mb-4">

                    <label class="producto-form-label">
                        Estado
                    </label>

                    <select
                        name="estado"
                        class="form-control producto-input">

                        <option
                            value="disponible"
                            {{ $producto->estado == 'disponible' ? 'selected' : '' }}>

                            Disponible

                        </option>

                        <option
                            value="agotado"
                            {{ $producto->estado == 'agotado' ? 'selected' : '' }}>

                            Agotado

                        </option>

                    </select>

                </div>


                {{-- BOTONES --}}
                <div class="productos-form-actions">

                    <button
                        type="submit"
                        class="btn btn-success btn-producto-guardar">

                        <i class="bi bi-check-circle-fill"></i>
                        Actualizar Producto

                    </button>


                    <a
                        href="{{ route('admin.productos.index') }}"
                        class="btn btn-secondary btn-producto-cancelar">

                        <i class="bi bi-arrow-left"></i>
                        Cancelar

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const input = document.getElementById('imagenEdit');

        const previewContainer =
            document.getElementById('previewEditContainer');

        const preview =
            document.getElementById('previewEdit');

        const sinNuevaImagen =
            document.getElementById('sinNuevaImagen');


        if (!input) {
            return;
        }


        input.addEventListener('change', function(event) {

            const file = event.target.files[0];


            if (!file) {

                previewContainer.classList.add('d-none');

                sinNuevaImagen.classList.remove('d-none');

                preview.src = '';

                return;
            }


            if (!file.type.startsWith('image/')) {

                previewContainer.classList.add('d-none');

                sinNuevaImagen.classList.remove('d-none');

                preview.src = '';

                return;
            }


            const imageURL = URL.createObjectURL(file);


            preview.src = imageURL;


            previewContainer.classList.remove('d-none');

            sinNuevaImagen.classList.add('d-none');

        });

    });
</script>