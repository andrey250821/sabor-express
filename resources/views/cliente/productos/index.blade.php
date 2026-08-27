@extends('layouts.cliente')

@section('content')

<div class="cliente-productos-page">

    {{-- ENCABEZADO --}}
    <div class="cliente-productos-header">

        <div>
            <span class="cliente-productos-label">
                SABOR EXPRESS
            </span>

            <h1>
                Nuestros productos
            </h1>

            <p>
                Elige tus productos favoritos y disfruta de
                nuestros sabores.
            </p>
        </div>

    </div>


    {{-- FILTROS --}}
    <div class="cliente-productos-filtros">

        <form
            action="{{ route('cliente.productos.index') }}"
            method="GET"
            class="cliente-filtros-form">

            {{-- BUSCADOR --}}
            <div class="cliente-busqueda">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Buscar producto...">

            </div>


            {{-- CATEGORÍA --}}
            <select
                name="categoria_id"
                class="cliente-categoria-select">

                <option value="">
                    Todas las categorías
                </option>

                @foreach($categorias as $categoria)

                <option
                    value="{{ $categoria->id }}"
                    {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                    {{ $categoria->nombre }}
                </option>

                @endforeach

            </select>


            {{-- BOTÓN BUSCAR --}}
            <button
                type="submit"
                class="cliente-btn-filtrar">
                <i class="bi bi-search"></i>
                Buscar
            </button>


            {{-- LIMPIAR --}}
            @if(request('buscar') || request('categoria_id'))

            <a
                href="{{ route('cliente.productos.index') }}"
                class="cliente-btn-limpiar">
                <i class="bi bi-x-circle"></i>
                Limpiar
            </a>

            @endif

        </form>

    </div>


    {{-- RESULTADOS --}}
    <div class="cliente-productos-resultados">

        <div>

            @if(request('buscar') || request('categoria_id'))

            <strong>
                Resultados encontrados
            </strong>

            @else

            <strong>
                Todos nuestros productos
            </strong>

            @endif

        </div>

        <span>
            {{ $productos->count() }}
            {{ $productos->count() == 1 ? 'producto' : 'productos' }}
        </span>

    </div>


    {{-- PRODUCTOS --}}
    <div class="cliente-productos-grid">

        @forelse($productos as $producto)

        <div class="cliente-producto-card">

            {{-- IMAGEN --}}
            <div class="cliente-producto-imagen">

                @if($producto->imagen)

                <img
                    src="{{ asset('storage/' . $producto->imagen) }}"
                    alt="{{ $producto->nombre }}">

                @else

                <div class="cliente-producto-sin-imagen">

                    <i class="bi bi-image"></i>

                    <span>
                        Sin imagen
                    </span>

                </div>

                @endif

            </div>


            {{-- INFORMACIÓN --}}
            <div class="cliente-producto-info">

                {{-- CATEGORÍA --}}
                <span class="cliente-producto-categoria">

                    {{ $producto->categoria->nombre ?? 'Sin categoría' }}

                </span>


                {{-- NOMBRE --}}
                <h3>
                    {{ $producto->nombre }}
                </h3>


                {{-- DESCRIPCIÓN --}}
                @if($producto->descripcion)

                <p>
                    {{ $producto->descripcion }}
                </p>

                @else

                <p class="cliente-producto-sin-descripcion">
                    Sin descripción.
                </p>

                @endif


                {{-- PRECIO --}}
                <div class="cliente-producto-precio">

                    Bs.
                    {{ number_format($producto->precio, 2) }}

                </div>


                {{-- STOCK --}}
                <div class="cliente-producto-stock">

                    <i class="bi bi-check-circle-fill"></i>

                    Disponible

                    <span>
                        {{ $producto->stock }} disponibles
                    </span>

                </div>


                {{-- AGREGAR --}}
                <form
                    action="{{ route('cliente.carrito.agregar', $producto->id) }}"
                    method="POST">

                    @csrf

                    <button
                        type="submit"
                        class="cliente-btn-agregar">

                        <i class="bi bi-cart-plus"></i>

                        Agregar al carrito

                    </button>

                </form>

            </div>

        </div>

        @empty

        {{-- SIN PRODUCTOS --}}
        <div class="cliente-productos-vacio">

            <i class="bi bi-search"></i>

            <h3>
                No encontramos productos
            </h3>

            <p>
                Intenta buscar otro producto o seleccionar
                otra categoría.
            </p>

            <a
                href="{{ route('cliente.productos.index') }}"
                class="cliente-btn-limpiar">
                Ver todos los productos
            </a>

        </div>

        @endforelse

    </div>

</div>

@endsection