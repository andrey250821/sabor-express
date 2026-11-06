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

            <div class="cliente-productos-header-meta">
                <span>
                    <i class="bi bi-stars"></i>
                    Calidad y sabor
                </span>
                <span>
                    <i class="bi bi-lightning-charge-fill"></i>
                    Pedido rápido
                </span>
            </div>

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
                        <i class="bi bi-tag-fill"></i>
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
                         CALIFICACIÓN DEL PRODUCTO
                    ================================================== --}}
                    @if($producto->calificaciones_count > 0)

                        @php
                            $promedio = round(
                                $producto->calificaciones_avg_puntuacion
                            );
                        @endphp

                        <div class="cliente-producto-calificacion-resumen">
                            <span class="cliente-producto-rating-chip">
                                <i class="bi bi-star-fill"></i>
                                Valoración
                            </span>

                            <span class="cliente-producto-calificacion-estrellas">

                                @for($i = 1; $i <= 5; $i++)

                                    @if($i <= $promedio)

                                        <i class="bi bi-star-fill"></i>

                                    @else

                                        <i class="bi bi-star"></i>

                                    @endif

                                @endfor

                            </span>


                            <strong>
                                {{ number_format(
                                    $producto->calificaciones_avg_puntuacion,
                                    1
                                ) }}
                            </strong>


                            <span class="cliente-producto-calificacion-total">

                                ({{ $producto->calificaciones_count }}

                                {{ $producto->calificaciones_count === 1
                                    ? 'opinión'
                                    : 'opiniones'
                                }})

                            </span>

                        </div>

                    @else

                        <div class="cliente-producto-sin-calificaciones">

                            <i class="bi bi-star"></i>

                            <span>
                                Sé el primero en opinar
                            </span>

                        </div>

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


                        {{-- STOCK DISPONIBLE EN TIEMPO REAL --}}
                        @php
                            $stockDisponible = max(
                                0,
                                (int) $producto->stock - (int) ($producto->stock_reservado ?? 0)
                            );

                            $stockClase = $stockDisponible <= 0
                                ? 'agotado'
                                : ($stockDisponible <= 5
                                    ? 'bajo'
                                    : ($stockDisponible <= 15 ? 'medio' : 'alto'));
                        @endphp

                        <div
                            class="cliente-producto-stock cliente-producto-stock--{{ $stockClase }}"
                            data-producto-stock-container="{{ $producto->id }}"
                            data-stock-disponible="{{ $stockDisponible }}">

                            <span class="cliente-producto-stock-icono">
                                <i class="bi bi-box-seam-fill"></i>
                            </span>

                            <span
                                class="cliente-producto-stock-texto"
                                data-producto-stock-texto="{{ $producto->id }}">
                                @if($stockDisponible <= 0)
                                    Sin stock
                                @elseif($stockDisponible <= 5)
                                    Últimas {{ $stockDisponible }}
                                    {{ $stockDisponible == 1 ? 'unidad' : 'unidades' }}
                                @else
                                    {{ $stockDisponible }}
                                    {{ $stockDisponible == 1 ? 'unidad disponible' : 'unidades disponibles' }}
                                @endif
                            </span>

                        </div>

                    </div>


                    {{-- =================================================
                         OPINIONES Y AGREGAR AL CARRITO
                    ================================================== --}}
                    <div class="cliente-producto-acciones">


                        {{-- VER OPINIONES --}}
                        <a
                            href="{{ route('cliente.calificaciones.index', $producto->id) }}"
                            class="cliente-btn-opiniones">

                            <span class="cliente-btn-opiniones-icono">
                                <i class="bi bi-star-fill"></i>
                            </span>

                            <span>
                                Ver opiniones
                            </span>

                            <i class="bi bi-chevron-right cliente-btn-opiniones-flecha"></i>

                        </a>


                        {{-- AGREGAR AL CARRITO --}}
                        <form
                            action="{{ route('cliente.carrito.agregar', $producto->id) }}"
                            method="POST"
                            class="form-agregar-carrito">

                            @csrf

                            <button
                                type="submit"
                                class="cliente-btn-agregar w-100"
                                data-producto-agregar="{{ $producto->id }}"
                                @disabled($stockDisponible <= 0)>

                                <span class="cliente-btn-agregar-icono">
                                    <i class="bi bi-cart-plus"></i>
                                </span>

                                <span>
                                    Agregar al carrito
                                </span>

                                <i class="bi bi-arrow-right cliente-btn-agregar-flecha"></i>

                            </button>

                        </form>

                    </div>

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

    // =====================================================
    // AGREGAR PRODUCTOS AL CARRITO
    // =====================================================

    const formularios =
        document.querySelectorAll('.form-agregar-carrito');


    formularios.forEach(function(formulario) {

        formulario.addEventListener('submit', async function(e) {

            e.preventDefault();


            const boton =
                formulario.querySelector('.cliente-btn-agregar');


            const textoOriginal =
                boton.innerHTML;


            // =================================================
            // ESTADO DE CARGA
            // =================================================

            boton.disabled = true;

            boton.innerHTML = `
                <i class="bi bi-hourglass-split"></i>
                <span>Agregando...</span>
            `;


            try {

                const respuesta = await fetch(
                    formulario.action,
                    {

                        method: 'POST',

                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },

                        body: new FormData(formulario)

                    }
                );


                const datos = await respuesta.json();


                // =================================================
                // PRODUCTO AGREGADO CORRECTAMENTE
                // =================================================

                if (datos.success) {

                    mostrarMensajeCarrito(
                        datos.message,
                        'success'
                    );


                    // Actualizar contador y stock libre sin recargar.
                    actualizarContadorCarrito(datos.cantidadCarrito);

                    actualizarStockProducto(
                        datos.productoId,
                        datos.stockDisponible
                    );

                    if (Number(datos.stockDisponible) > 0) {
                        boton.innerHTML = `
                            <i class="bi bi-check-lg"></i>
                            <span>Agregado</span>
                        `;

                        setTimeout(function() {
                            const contenedorStock = document.querySelector(
                                `[data-producto-stock-container="${boton.dataset.productoAgregar}"]`
                            );
                            const stockActual = Number(
                                contenedorStock?.dataset.stockDisponible ?? datos.stockDisponible
                            );

                            if (stockActual > 0) {
                                boton.innerHTML = textoOriginal;
                                boton.disabled = false;
                            }
                        }, 1200);
                    }


                } else {

                    mostrarMensajeCarrito(
                        datos.message,
                        'error'
                    );

                    if (datos.stockDisponible !== undefined) {
                        actualizarStockProducto(
                            boton.dataset.productoAgregar,
                            datos.stockDisponible
                        );
                    }

                    boton.innerHTML =
                        textoOriginal;

                    boton.disabled = false;

                }

            } catch (error) {

                console.error(error);


                mostrarMensajeCarrito(
                    'Ocurrió un error al agregar el producto al carrito.',
                    'error'
                );


                boton.innerHTML =
                    textoOriginal;

                boton.disabled = false;

            }

        });

    });


    // =====================================================
    // STOCK COMPARTIDO EN TIEMPO REAL
    // =====================================================

    const urlStock = @json(route('cliente.productos.stock'));

    function actualizarStockProducto(productoId, stock) {
        if (productoId === undefined || stock === undefined) {
            return;
        }

        const id = String(productoId);
        const cantidad = Math.max(0, Number(stock));
        const contenedor = document.querySelector(
            `[data-producto-stock-container="${id}"]`
        );
        const texto = document.querySelector(
            `[data-producto-stock-texto="${id}"]`
        );
        const boton = document.querySelector(
            `[data-producto-agregar="${id}"]`
        );

        if (!contenedor || !texto || !boton) {
            return;
        }

        contenedor.classList.remove(
            'cliente-producto-stock--agotado',
            'cliente-producto-stock--bajo',
            'cliente-producto-stock--medio',
            'cliente-producto-stock--alto'
        );

        let clase = 'alto';
        let etiqueta = cantidad === 1
            ? '1 unidad disponible'
            : cantidad + ' unidades disponibles';

        if (cantidad <= 0) {
            clase = 'agotado';
            etiqueta = 'Sin stock';
        } else if (cantidad <= 5) {
            clase = 'bajo';
            etiqueta = 'Últimas ' + cantidad
                + (cantidad === 1 ? ' unidad' : ' unidades');
        } else if (cantidad <= 15) {
            clase = 'medio';
        }

        contenedor.classList.add('cliente-producto-stock--' + clase);
        contenedor.dataset.stockDisponible = String(cantidad);
        texto.textContent = etiqueta;
        boton.disabled = cantidad <= 0;

        if (cantidad <= 0) {
            boton.innerHTML = `
                <span class="cliente-btn-agregar-icono">
                    <i class="bi bi-x-circle"></i>
                </span>
                <span>Sin stock</span>
            `;
        } else if (boton.textContent.includes('Sin stock')) {
            boton.innerHTML = `
                <span class="cliente-btn-agregar-icono">
                    <i class="bi bi-cart-plus"></i>
                </span>
                <span>Agregar al carrito</span>
                <i class="bi bi-arrow-right cliente-btn-agregar-flecha"></i>
            `;
        }
    }

    async function consultarStockDisponible() {
        if (document.hidden) {
            return;
        }

        try {
            const respuesta = await fetch(urlStock, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                cache: 'no-store'
            });

            if (!respuesta.ok) {
                return;
            }

            const datos = await respuesta.json();

            if (datos.success && datos.stocks) {
                Object.entries(datos.stocks).forEach(function([id, stock]) {
                    actualizarStockProducto(id, stock);
                });
            }
        } catch (error) {
            // Si falla una consulta de actualización, el catálogo sigue funcionando.
            console.warn('No se pudo actualizar el stock en este momento.', error);
        }
    }

    consultarStockDisponible();
    window.setInterval(consultarStockDisponible, 6000);


    // =====================================================
    // MOSTRAR MENSAJE DEL CARRITO
    // =====================================================

    function mostrarMensajeCarrito(mensaje, tipo) {

        const mensajeAnterior =
            document.querySelector(
                '.cliente-mensaje-carrito'
            );


        if (mensajeAnterior) {

            mensajeAnterior.remove();

        }


        const alerta =
            document.createElement('div');


        alerta.className =
            'cliente-mensaje-carrito cliente-mensaje-' + tipo;


        const icono =
            tipo === 'success'
                ? 'bi-check-circle-fill'
                : 'bi-exclamation-circle-fill';


        alerta.innerHTML = `
            <i class="bi ${icono}"></i>
            <span>${mensaje}</span>
        `;


        document.body.appendChild(alerta);


        setTimeout(function() {

            alerta.classList.add(
                'cliente-mensaje-salir'
            );


            setTimeout(function() {

                alerta.remove();

            }, 300);

        }, 3000);

    }


    // =====================================================
    // MENSAJES DEL SERVIDOR (RESERVA PARA NAVEGACIÓN SIN JS)
    // =====================================================

    @if(session('success'))
        mostrarMensajeCarrito(
            @json(session('success')),
            'success'
        );
    @endif

    @if(session('error'))
        mostrarMensajeCarrito(
            @json(session('error')),
            'error'
        );
    @endif


    // =====================================================
    // ACTUALIZAR CONTADOR DEL CARRITO
    // =====================================================

    function actualizarContadorCarrito(cantidad) {

        const contador =
            document.querySelector(
                '.cliente-carrito-contador'
            );


        if (!contador) {

            return;

        }


        contador.textContent =
            cantidad;


        if (cantidad > 0) {

            contador.style.display =
                'inline-flex';

        }

    }

});

</script>

@endsection