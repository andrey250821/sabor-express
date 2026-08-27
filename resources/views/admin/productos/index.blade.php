@extends('layouts.admin')

@section('content')

<div class="productos-page">

    {{-- CABECERA --}}
    <div class="productos-header">

        <div>
            <h2 class="productos-title">
                🍔 Productos
            </h2>

            <p class="productos-subtitle">
                Administra los productos disponibles en Sabor Express
            </p>
        </div>

        <a href="{{ route('admin.productos.create') }}"
            class="btn btn-success productos-btn-nuevo">

            <i class="bi bi-plus-circle"></i>
            Nuevo Producto

        </a>

    </div>


    {{-- MENSAJE --}}
    @if(session('success'))

    <div class="alert productos-alert alert-dismissible fade show">

        <i class="bi bi-check-circle-fill"></i>

        {{ session('success') }}

        <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

    @endif


    {{-- TARJETA --}}
    <div class="productos-card">

        <div class="productos-card-header">

            {{-- INFORMACIÓN DE LA LISTA --}}
            <div class="productos-card-title">

                <div class="productos-card-title-icon">
                    <i class="bi bi-box-seam"></i>
                </div>

                <div>
                    <h5>
                        Lista de productos
                    </h5>

                    <small>
                        Productos registrados en el sistema
                    </small>
                </div>

                <span class="productos-count">
                    {{ $productos->count() }}
                    {{ $productos->count() == 1 ? 'producto' : 'productos' }}
                </span>

            </div>


            {{-- FILTRO --}}
            <div class="productos-filtro-card">

                <form
                    action="{{ route('admin.productos.index') }}"
                    method="GET"
                    class="productos-filtro-form">

                    {{-- CATEGORÍA --}}
                    <div class="productos-filtro-field">

                        <label class="productos-filtro-label">

                            <i class="bi bi-funnel-fill"></i>

                            Filtrar por categoría

                        </label>


                        <select
                            name="categoria_id"
                            class="productos-filtro-select">

                            <option value="">
                                Todas las categorías
                            </option>

                            @foreach($categorias as $categoria)

                            <option
                                value="{{ $categoria->id }}"
                                {{ (string) request('categoria_id') === (string) $categoria->id ? 'selected' : '' }}>

                                {{ $categoria->nombre }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- BOTÓN FILTRAR --}}
                    <button
                        type="submit"
                        class="productos-btn-filtrar">

                        <i class="bi bi-funnel-fill"></i>

                        Filtrar

                    </button>


                    {{-- BOTÓN LIMPIAR --}}
                    <a
                        href="{{ route('admin.productos.index') }}"
                        class="productos-btn-limpiar">

                        <i class="bi bi-x-circle"></i>

                        Limpiar

                    </a>

                </form>

            </div>

        </div>


        {{-- TABLA RESPONSIVE --}}
        <div class="table-responsive productos-table-container">

            <table class="table productos-table align-middle mb-0">

                <thead>

                    <tr>

                        <th>Imagen</th>

                        <th>Nombre</th>

                        <th>Categoría</th>

                        <th>Precio</th>

                        <th>Stock</th>

                        <th>Estado</th>

                        <th class="text-center">Acciones</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($productos as $producto)

                    <tr>

                        {{-- IMAGEN --}}
                        <td>

                            @if($producto->imagen)

                            <img
                                src="{{ Storage::url($producto->imagen) }}"
                                class="producto-imagen"
                                alt="{{ $producto->nombre }}">

                            @else

                            <div class="producto-sin-imagen">

                                <i class="bi bi-image"></i>

                            </div>

                            @endif

                        </td>


                        {{-- NOMBRE --}}
                        <td>

                            <div class="producto-nombre">

                                <strong>
                                    {{ $producto->nombre }}
                                </strong>

                                @if($producto->descripcion)

                                <small>
                                    {{ Str::limit($producto->descripcion, 45) }}
                                </small>

                                @endif

                            </div>

                        </td>


                        {{-- CATEGORIA --}}
                        <td>

                            <span class="producto-categoria">

                                <i class="bi bi-tag"></i>

                                {{ $producto->categoria->nombre }}

                            </span>

                        </td>


                        {{-- PRECIO --}}
                        <td>

                            <strong class="producto-precio">

                                Bs {{ number_format($producto->precio, 2) }}

                            </strong>

                        </td>


                        {{-- STOCK --}}
                        <td>

                            @if($producto->stock > 10)

                            <span class="stock-badge stock-alto">

                                {{ $producto->stock }}

                            </span>

                            @elseif($producto->stock > 0)

                            <span class="stock-badge stock-bajo">

                                {{ $producto->stock }}

                            </span>

                            @else

                            <span class="stock-badge stock-agotado">

                                0

                            </span>

                            @endif

                        </td>


                        {{-- ESTADO --}}
                        <td>

                            @if($producto->estado == 'disponible')

                            <span class="estado-producto disponible">

                                <i class="bi bi-check-circle-fill"></i>

                                Disponible

                            </span>

                            @else

                            <span class="estado-producto agotado">

                                <i class="bi bi-x-circle-fill"></i>

                                Agotado

                            </span>

                            @endif

                        </td>


                        {{-- ACCIONES --}}
                        <td>

                            <div class="producto-acciones">

                                <a
                                    href="{{ route('admin.productos.edit', $producto->id) }}"
                                    class="btn-producto editar"
                                    title="Editar producto">

                                    <i class="bi bi-pencil-fill"></i>

                                </a>


                                <form
                                    action="{{ route('admin.productos.destroy', $producto->id) }}"
                                    method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn-producto eliminar"
                                        title="Eliminar producto"
                                        onclick="return confirm('¿Está seguro de eliminar este producto?')">

                                        <i class="bi bi-trash-fill"></i>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="7">

                            <div class="productos-empty">

                                <i class="bi bi-box-seam"></i>

                                <h5>
                                    No hay productos registrados
                                </h5>

                                <p>
                                    Comienza agregando tu primer producto.
                                </p>

                                <a
                                    href="{{ route('admin.productos.create') }}"
                                    class="btn btn-success">

                                    <i class="bi bi-plus-circle"></i>
                                    Nuevo Producto

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