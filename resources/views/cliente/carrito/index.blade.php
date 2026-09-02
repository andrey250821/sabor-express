@extends('layouts.cliente')

@section('content')

<div class="cliente-carrito-page">

    {{-- =====================================================
         ENCABEZADO
    ====================================================== --}}

    <div class="cliente-carrito-header">

        <div>

            <span class="cliente-carrito-label">
                SABOR EXPRESS
            </span>

            <h1>
                <i class="bi bi-cart3"></i>
                Mi carrito
            </h1>

            <p>
                Revisa tus productos antes de realizar tu pedido.
            </p>

        </div>

        <a
            href="{{ route('cliente.productos.index') }}"
            class="cliente-carrito-btn-volver">

            <i class="bi bi-arrow-left"></i>

            Seguir comprando

        </a>

    </div>


    {{-- =====================================================
         MENSAJES
    ====================================================== --}}

    @if(session('success'))

    <div class="cliente-carrito-alert cliente-carrito-alert-success">

        <i class="bi bi-check-circle-fill"></i>

        <span>
            {{ session('success') }}
        </span>

    </div>

    @endif


    @if(session('error'))

    <div class="cliente-carrito-alert cliente-carrito-alert-error">

        <i class="bi bi-exclamation-circle-fill"></i>

        <span>
            {{ session('error') }}
        </span>

    </div>

    @endif


    {{-- =====================================================
         CARRITO VACÍO
    ====================================================== --}}

    @if(empty($carrito))

    <div class="cliente-carrito-vacio">

        <div class="cliente-carrito-vacio-icono">

            <i class="bi bi-cart-x"></i>

        </div>

        <h2>
            Tu carrito está vacío
        </h2>

        <p>
            Todavía no has agregado productos a tu carrito.
        </p>

        <a
            href="{{ route('cliente.productos.index') }}"
            class="cliente-carrito-btn-principal">

            <i class="bi bi-shop"></i>

            Ver productos

        </a>

    </div>

    @else


    {{-- =====================================================
         CONTENIDO DEL CARRITO
    ====================================================== --}}

    <div class="row g-4">


        {{-- =================================================
             PRODUCTOS
        ================================================== --}}

        <div class="col-lg-8">

            <div class="cliente-carrito-card">


                {{-- CABECERA --}}

                <div class="cliente-carrito-card-header">

                    <div>

                        <span class="cliente-carrito-card-titulo">
                            Productos seleccionados
                        </span>

                        <span class="cliente-carrito-card-subtitulo">
                            {{ count($carrito) }}
                            {{ count($carrito) == 1 ? 'producto' : 'productos' }}
                        </span>

                    </div>

                    <i class="bi bi-bag-check"></i>

                </div>


                {{-- CUERPO --}}

                <div class="cliente-carrito-card-body">


                    @foreach($carrito as $item)

                    <div
                        class="cliente-carrito-item"
                        data-item-id="{{ $item['id'] }}">


                        {{-- =================================
                             IMAGEN
                        ================================== --}}

                        <div class="cliente-carrito-imagen">

                            @if(!empty($item['imagen']))

                            <img
                                src="{{ asset('storage/' . $item['imagen']) }}"
                                alt="{{ $item['nombre'] }}">

                            @else

                            <div class="cliente-carrito-sin-imagen">

                                <i class="bi bi-image"></i>

                            </div>

                            @endif

                        </div>


                        {{-- =================================
                             INFORMACIÓN
                        ================================== --}}

                        <div class="cliente-carrito-info">

                            <h3>
                                {{ $item['nombre'] }}
                            </h3>

                            <p>

                                <span>
                                    Precio unitario
                                </span>

                                <strong>
                                    Bs.
                                    {{ number_format($item['precio'], 2) }}
                                </strong>

                            </p>

                        </div>


                        {{-- =================================
                             CANTIDAD
                        ================================== --}}

                        <div class="cliente-carrito-cantidad">

                            <span>
                                Cantidad
                            </span>

                            <div class="cliente-cantidad-controles">


                                {{-- DISMINUIR --}}

                                <button
                                    type="button"
                                    class="cliente-cantidad-btn btn-disminuir"
                                    data-id="{{ $item['id'] }}"
                                    title="Disminuir cantidad">

                                    <i class="bi bi-dash"></i>

                                </button>


                                {{-- CANTIDAD ACTUAL --}}

                                <strong
                                    class="cliente-cantidad-numero"
                                    data-id="{{ $item['id'] }}">

                                    {{ $item['cantidad'] }}

                                </strong>


                                {{-- AUMENTAR --}}

                                <button
                                    type="button"
                                    class="cliente-cantidad-btn btn-aumentar"
                                    data-id="{{ $item['id'] }}"
                                    title="Aumentar cantidad">

                                    <i class="bi bi-plus"></i>

                                </button>

                            </div>

                        </div>


                        {{-- =================================
                             SUBTOTAL
                        ================================== --}}

                        <div class="cliente-carrito-subtotal">

                            <span>
                                Subtotal
                            </span>

                            <strong
                                class="cliente-item-subtotal"
                                data-id="{{ $item['id'] }}">

                                Bs.
                                {{ number_format($item['precio'] * $item['cantidad'], 2) }}

                            </strong>

                        </div>


                        {{-- =================================
                             ELIMINAR
                        ================================== --}}

                        <div class="cliente-carrito-eliminar">

                            <button
                                type="button"
                                class="cliente-eliminar-btn btn-eliminar-producto"
                                data-id="{{ $item['id'] }}"
                                title="Eliminar producto">

                                <i class="bi bi-trash"></i>

                            </button>

                        </div>

                    </div>

                    @endforeach


                </div>

            </div>


            {{-- =============================================
                 VACIAR CARRITO
            ============================================== --}}

            <button
                type="button"
                id="btn-vaciar-carrito"
                class="cliente-btn-vaciar">

                <i class="bi bi-trash3"></i>

                Vaciar carrito

            </button>


        </div>


        {{-- =================================================
             RESUMEN
        ================================================== --}}

        <div class="col-lg-4">

            <div class="cliente-carrito-resumen">


                {{-- CABECERA --}}

                <div class="cliente-carrito-resumen-header">

                    <div>

                        <span>
                            SABOR EXPRESS
                        </span>

                        <h2>

                            <i class="bi bi-receipt"></i>

                            Resumen del pedido

                        </h2>

                    </div>

                </div>


                {{-- CUERPO --}}

                <div class="cliente-carrito-resumen-body">


                    {{-- PRODUCTOS --}}

                    <div class="cliente-resumen-linea">

                        <span>
                            Productos
                        </span>

                        <strong id="cliente-cantidad-productos">
                            {{ count($carrito) }}
                        </strong>

                    </div>


                    {{-- SUBTOTAL --}}

                    <div class="cliente-resumen-linea">

                        <span>
                            Subtotal
                        </span>

                        <strong id="cliente-total-carrito">

                            Bs.
                            {{ number_format($total, 2) }}

                        </strong>

                    </div>


                    {{-- DELIVERY --}}

                    <div class="cliente-resumen-linea cliente-resumen-delivery">

                        <span>
                            Delivery
                        </span>

                        <strong>
                            Se calculará después
                        </strong>

                    </div>


                    <div class="cliente-resumen-separador"></div>


                    {{-- TOTAL FINAL --}}

                    <div class="cliente-resumen-total">

                        <span>
                            Total
                        </span>

                        <strong id="cliente-total-final">

                            Bs.
                            {{ number_format($total, 2) }}

                        </strong>

                    </div>


                    {{-- =================================
                         CONTINUAR PEDIDO
                    ================================== --}}

                    <a
                        href="{{ route('cliente.pedidos.create') }}"
                        class="cliente-carrito-btn-principal cliente-carrito-btn-pedido">

                        <i class="bi bi-arrow-right-circle"></i>

                        Continuar con el pedido

                    </a>


                    {{-- =================================
                         SEGUIR COMPRANDO
                    ================================== --}}

                    <a
                        href="{{ route('cliente.productos.index') }}"
                        class="cliente-carrito-btn-secundario">

                        <i class="bi bi-arrow-left"></i>

                        Seguir comprando

                    </a>


                </div>

            </div>

        </div>

    </div>

    @endif

</div>

@endsection


@section('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {


        /*
        |--------------------------------------------------------------------------
        | CSRF TOKEN
        |--------------------------------------------------------------------------
        */

        const csrfToken =
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');


        /*
        |--------------------------------------------------------------------------
        | VERIFICAR CSRF
        |--------------------------------------------------------------------------
        */

        if (!csrfToken) {

            console.error(
                'No se encontró el token CSRF en el documento.'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | AUMENTAR
        |--------------------------------------------------------------------------
        */

        document.querySelectorAll('.btn-aumentar').forEach(function(boton) {

            boton.addEventListener('click', function() {

                cambiarCantidad(
                    this.dataset.id,
                    'aumentar',
                    this
                );

            });

        });


        /*
        |--------------------------------------------------------------------------
        | DISMINUIR
        |--------------------------------------------------------------------------
        */

        document.querySelectorAll('.btn-disminuir').forEach(function(boton) {

            boton.addEventListener('click', function() {

                cambiarCantidad(
                    this.dataset.id,
                    'disminuir',
                    this
                );

            });

        });


        /*
        |--------------------------------------------------------------------------
        | ELIMINAR
        |--------------------------------------------------------------------------
        */

        document.querySelectorAll('.btn-eliminar-producto').forEach(function(boton) {

            boton.addEventListener('click', function() {

                eliminarProducto(
                    this.dataset.id,
                    this
                );

            });

        });


        /*
        |--------------------------------------------------------------------------
        | VACIAR
        |--------------------------------------------------------------------------
        */

        const botonVaciar =
            document.getElementById('btn-vaciar-carrito');


        if (botonVaciar) {

            botonVaciar.addEventListener('click', function() {

                vaciarCarrito(this);

            });

        }


        /*
        |--------------------------------------------------------------------------
        | CAMBIAR CANTIDAD
        |--------------------------------------------------------------------------
        */

        async function cambiarCantidad(id, accion, boton) {

            if (boton.disabled) {
                return;
            }


            boton.disabled = true;


            const iconoOriginal =
                boton.innerHTML;


            boton.innerHTML =
                '<i class="bi bi-arrow-repeat"></i>';


            try {

                const respuesta = await fetch(
                    `/carrito/${accion}/${id}`, {
                        method: 'PUT',

                        headers: {

                            'X-CSRF-TOKEN': csrfToken,

                            'X-Requested-With': 'XMLHttpRequest',

                            'Accept': 'application/json'

                        }
                    }
                );


                const datos =
                    await respuesta.json();


                if (!respuesta.ok || !datos.success) {

                    mostrarMensajeCarrito(
                        datos.message ||
                        'No se pudo actualizar la cantidad.',
                        'error'
                    );

                    boton.innerHTML =
                        iconoOriginal;

                    boton.disabled =
                        false;

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | PRODUCTO ELIMINADO AL DISMINUIR
                |--------------------------------------------------------------------------
                */

                if (datos.eliminado) {

                    eliminarElementoProducto(id);

                    actualizarTotal(datos.total);

                    actualizarContador(
                        datos.cantidadCarrito
                    );

                    actualizarCantidadProductos();


                    mostrarMensajeCarrito(
                        datos.message,
                        'success'
                    );


                    if (datos.carritoVacio) {

                        mostrarCarritoVacio();

                    }

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | ACTUALIZAR CANTIDAD
                |--------------------------------------------------------------------------
                */

                const cantidad =
                    document.querySelector(
                        `.cliente-cantidad-numero[data-id="${id}"]`
                    );


                if (cantidad) {

                    cantidad.textContent =
                        datos.cantidad;

                }


                /*
                |--------------------------------------------------------------------------
                | ACTUALIZAR SUBTOTAL
                |--------------------------------------------------------------------------
                */

                const subtotal =
                    document.querySelector(
                        `.cliente-item-subtotal[data-id="${id}"]`
                    );


                if (subtotal) {

                    subtotal.textContent =
                        `Bs. ${datos.subtotal}`;

                }


                /*
                |--------------------------------------------------------------------------
                | ACTUALIZAR TOTAL
                |--------------------------------------------------------------------------
                */

                actualizarTotal(
                    datos.total
                );


                actualizarContador(
                    datos.cantidadCarrito
                );


                mostrarMensajeCarrito(
                    datos.message,
                    'success'
                );


            } catch (error) {

                console.error(error);

                mostrarMensajeCarrito(
                    'Ocurrió un error al actualizar el carrito.',
                    'error'
                );

            }


            boton.innerHTML =
                iconoOriginal;

            boton.disabled =
                false;

        }


        /*
        |--------------------------------------------------------------------------
        | ELIMINAR PRODUCTO
        |--------------------------------------------------------------------------
        */

        async function eliminarProducto(id, boton) {

            if (boton.disabled) {
                return;
            }


            boton.disabled = true;


            const iconoOriginal =
                boton.innerHTML;


            boton.innerHTML =
                '<i class="bi bi-arrow-repeat"></i>';


            try {

                const respuesta = await fetch(
                    `/carrito/eliminar/${id}`, {
                        method: 'DELETE',

                        headers: {

                            'X-CSRF-TOKEN': csrfToken,

                            'X-Requested-With': 'XMLHttpRequest',

                            'Accept': 'application/json'

                        }
                    }
                );


                const datos =
                    await respuesta.json();


                if (!respuesta.ok || !datos.success) {

                    mostrarMensajeCarrito(
                        datos.message ||
                        'No se pudo eliminar el producto.',
                        'error'
                    );

                    boton.innerHTML =
                        iconoOriginal;

                    boton.disabled =
                        false;

                    return;

                }


                eliminarElementoProducto(id);


                actualizarTotal(
                    datos.total
                );


                actualizarContador(
                    datos.cantidadCarrito
                );


                actualizarCantidadProductos();


                mostrarMensajeCarrito(
                    datos.message,
                    'success'
                );


                if (datos.carritoVacio) {

                    mostrarCarritoVacio();

                }


            } catch (error) {

                console.error(error);

                mostrarMensajeCarrito(
                    'Ocurrió un error al eliminar el producto.',
                    'error'
                );

                boton.innerHTML =
                    iconoOriginal;

                boton.disabled =
                    false;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | VACIAR CARRITO
        |--------------------------------------------------------------------------
        */

        async function vaciarCarrito(boton) {

            if (boton.disabled) {
                return;
            }


            boton.disabled = true;


            const textoOriginal =
                boton.innerHTML;


            boton.innerHTML = `
            <i class="bi bi-arrow-repeat"></i>
            Vaciando...
        `;


            try {

                const respuesta = await fetch(
                    '{{ route("cliente.carrito.vaciar") }}', {
                        method: 'DELETE',

                        headers: {

                            'X-CSRF-TOKEN': csrfToken,

                            'X-Requested-With': 'XMLHttpRequest',

                            'Accept': 'application/json'

                        }
                    }
                );


                const datos =
                    await respuesta.json();


                if (!respuesta.ok || !datos.success) {

                    mostrarMensajeCarrito(
                        datos.message ||
                        'No se pudo vaciar el carrito.',
                        'error'
                    );

                    boton.innerHTML =
                        textoOriginal;

                    boton.disabled =
                        false;

                    return;

                }


                mostrarMensajeCarrito(
                    datos.message,
                    'success'
                );


                setTimeout(function() {

                    mostrarCarritoVacio();

                }, 400);


            } catch (error) {

                console.error(error);

                mostrarMensajeCarrito(
                    'Ocurrió un error al vaciar el carrito.',
                    'error'
                );

                boton.innerHTML =
                    textoOriginal;

                boton.disabled =
                    false;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | ELIMINAR VISUALMENTE
        |--------------------------------------------------------------------------
        */

        function eliminarElementoProducto(id) {

            const elemento =
                document.querySelector(
                    `.cliente-carrito-item[data-item-id="${id}"]`
                );


            if (!elemento) {
                return;
            }


            elemento.style.transition =
                'opacity .25s ease, transform .25s ease';


            elemento.style.opacity =
                '0';


            elemento.style.transform =
                'translateX(30px)';


            setTimeout(function() {

                elemento.remove();

            }, 250);

        }


        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR TOTAL
        |--------------------------------------------------------------------------
        */

        function actualizarTotal(total) {

            const totalCarrito =
                document.getElementById(
                    'cliente-total-carrito'
                );


            const totalFinal =
                document.getElementById(
                    'cliente-total-final'
                );


            if (totalCarrito) {

                totalCarrito.textContent =
                    `Bs. ${total}`;

            }


            if (totalFinal) {

                totalFinal.textContent =
                    `Bs. ${total}`;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR CONTADOR HEADER
        |--------------------------------------------------------------------------
        */

        function actualizarContador(cantidad) {

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

            } else {

                contador.style.display =
                    'none';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR CANTIDAD DE TIPOS DE PRODUCTOS
        |--------------------------------------------------------------------------
        */

        function actualizarCantidadProductos() {

            const productos =
                document.querySelectorAll(
                    '.cliente-carrito-item'
                );


            const contador =
                document.getElementById(
                    'cliente-cantidad-productos'
                );


            if (contador) {

                contador.textContent =
                    productos.length;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | CARRITO VACÍO
        |--------------------------------------------------------------------------
        */

        function mostrarCarritoVacio() {

            window.location.reload();

        }


        /*
        |--------------------------------------------------------------------------
        | MENSAJE
        |--------------------------------------------------------------------------
        */

        function mostrarMensajeCarrito(
            mensaje,
            tipo
        ) {

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
                'cliente-mensaje-carrito cliente-mensaje-' +
                tipo;


            const icono =
                tipo === 'success' ?
                'bi-check-circle-fill' :
                'bi-exclamation-circle-fill';


            alerta.innerHTML = `
            <i class="bi ${icono}"></i>
            <span>${mensaje}</span>
        `;


            document.body.appendChild(
                alerta
            );


            setTimeout(function() {

                alerta.classList.add(
                    'cliente-mensaje-salir'
                );


                setTimeout(function() {

                    alerta.remove();

                }, 300);

            }, 2500);

        }

    });
</script>

@endsection