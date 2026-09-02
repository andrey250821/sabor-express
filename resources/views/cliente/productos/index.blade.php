@extends('layouts.cliente')

@section('content')

<div class="cliente-productos-page">

    {{-- =====================================================
         ENCABEZADO
    ====================================================== --}}
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


    {{-- =====================================================
         FILTROS
    ====================================================== --}}
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
                    placeholder="Buscar producto..."
                    autocomplete="off">

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


            {{-- BUSCAR --}}
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


    {{-- =====================================================
         RESULTADOS
    ====================================================== --}}
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


    {{-- =====================================================
         PRODUCTOS
    ====================================================== --}}
    <div class="cliente-productos-grid">

        @forelse($productos as $producto)

        <div class="cliente-producto-card">

            {{-- =================================================
                     IMAGEN
                ================================================== --}}
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


                {{-- CATEGORÍA --}}
                <span class="cliente-producto-badge">

                    {{ $producto->categoria->nombre ?? 'Sin categoría' }}

                </span>

            </div>


            {{-- =================================================
                     INFORMACIÓN DEL PRODUCTO
                ================================================== --}}
            <div class="cliente-producto-info">


                {{-- NOMBRE --}}
                <h3 class="cliente-producto-nombre">

                    {{ $producto->nombre }}

                </h3>


                {{-- DESCRIPCIÓN --}}
                @if($producto->descripcion)

                <p class="cliente-producto-descripcion">

                    {{ $producto->descripcion }}

                </p>

                @else

                <p class="cliente-producto-descripcion cliente-producto-sin-descripcion">

                    Sin descripción disponible.

                </p>

                @endif


                {{-- =================================================
                         PRECIO Y STOCK
                    ================================================== --}}
                <div class="cliente-producto-bottom">

                    {{-- PRECIO --}}
                    <div>

                        <span class="cliente-producto-precio-label">
                            Precio
                        </span>

                        <div class="cliente-producto-precio">

                            Bs.
                            {{ number_format($producto->precio, 2) }}

                        </div>

                    </div>


                    {{-- STOCK --}}
                    <div class="cliente-producto-stock">

                        <i class="bi bi-check-circle-fill"></i>

                        <span>
                            Disponible
                        </span>

                    </div>

                </div>


                {{-- =================================================
                         AGREGAR AL CARRITO
                    ================================================== --}}
                <form
                    action="{{ route('cliente.carrito.agregar', $producto->id) }}"
                    method="POST"
                    class="form-agregar-carrito">

                    @csrf

                    <button
                        type="submit"
                        class="cliente-btn-agregar">

                        <i class="bi bi-cart-plus"></i>

                        <span>Agregar al carrito</span>

                    </button>

                </form>

            </div>

        </div>

        @empty

        {{-- =================================================
                 SIN PRODUCTOS
            ================================================== --}}
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

                <i class="bi bi-arrow-left"></i>

                Ver todos los productos

            </a>

        </div>

        @endforelse

    </div>

</div>

@endsection
@section('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const formularios = document.querySelectorAll('.form-agregar-carrito');

        formularios.forEach(function(formulario) {

            formulario.addEventListener('submit', async function(e) {

                e.preventDefault();

                const boton = formulario.querySelector('.cliente-btn-agregar');
                const textoOriginal = boton.innerHTML;

                // Estado de carga
                boton.disabled = true;

                boton.innerHTML = `
                <i class="bi bi-hourglass-split"></i>
                <span>Agregando...</span>
            `;

                try {

                    const respuesta = await fetch(formulario.action, {

                        method: 'POST',

                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },

                        body: new FormData(formulario)
                    });

                    const datos = await respuesta.json();

                    if (datos.success) {

                        mostrarMensajeCarrito(
                            datos.message,
                            'success'
                        );

                        // Actualizar contador del carrito si existe
                        actualizarContadorCarrito(datos.cantidadCarrito);

                        boton.innerHTML = `
                        <i class="bi bi-check-lg"></i>
                        <span>Agregado</span>
                    `;

                        setTimeout(function() {

                            boton.innerHTML = textoOriginal;
                            boton.disabled = false;

                        }, 1500);

                    } else {

                        mostrarMensajeCarrito(
                            datos.message,
                            'error'
                        );

                        boton.innerHTML = textoOriginal;
                        boton.disabled = false;
                    }

                } catch (error) {

                    console.error(error);

                    mostrarMensajeCarrito(
                        'Ocurrió un error al agregar el producto al carrito.',
                        'error'
                    );

                    boton.innerHTML = textoOriginal;
                    boton.disabled = false;
                }

            });

        });


        // =====================================================
        // MOSTRAR MENSAJE
        // =====================================================

        function mostrarMensajeCarrito(mensaje, tipo) {

            const mensajeAnterior =
                document.querySelector('.cliente-mensaje-carrito');

            if (mensajeAnterior) {
                mensajeAnterior.remove();
            }

            const alerta = document.createElement('div');

            alerta.className =
                'cliente-mensaje-carrito cliente-mensaje-' + tipo;

            const icono = tipo === 'success' ?
                'bi-check-circle-fill' :
                'bi-exclamation-circle-fill';

            alerta.innerHTML = `
            <i class="bi ${icono}"></i>
            <span>${mensaje}</span>
        `;

            document.body.appendChild(alerta);

            setTimeout(function() {

                alerta.classList.add('cliente-mensaje-salir');

                setTimeout(function() {
                    alerta.remove();
                }, 300);

            }, 3000);
        }


        // =====================================================
        // ACTUALIZAR CONTADOR DEL CARRITO
        // =====================================================

        function actualizarContadorCarrito(cantidad) {

            const contador =
                document.querySelector('.cliente-carrito-contador');

            if (!contador) {
                return;
            }

            contador.textContent = cantidad;

            if (cantidad > 0) {
                contador.style.display = 'inline-flex';
            }
        }

    });
</script>

@endsection