@extends('layouts.cliente')

@section('content')

<div class="cliente-carrito">

    {{-- ENCABEZADO --}}
    <div class="cliente-carrito-header">

        <div>
            <h1>
                <i class="bi bi-cart3"></i>
                Mi Carrito
            </h1>

            <p>
                Revisa los productos que deseas pedir.
            </p>
        </div>

        <a
            href="{{ route('cliente.productos.index') }}"
            class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Seguir comprando
        </a>

    </div>


    {{-- MENSAJES --}}
    @if(session('success'))

    <div class="alert alert-success">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
    </div>

    @endif


    @if(session('error'))

    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ session('error') }}
    </div>

    @endif


    {{-- CARRITO VACÍO --}}
    @if(empty($carrito))

    <div class="cliente-carrito-vacio">

        <div>
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
            class="btn btn-primary">
            <i class="bi bi-shop"></i>
            Ver productos
        </a>

    </div>

    @else

    <div class="row g-4">

        {{-- PRODUCTOS --}}
        <div class="col-lg-8">

            <div class="card">

                <div class="card-header">

                    <div>
                        <strong>
                            Productos seleccionados
                        </strong>

                        <span>
                            {{ count($carrito) }}
                            {{ count($carrito) == 1 ? 'producto' : 'productos' }}
                        </span>
                    </div>

                </div>


                <div class="card-body">

                    @foreach($carrito as $item)

                    <div class="cliente-carrito-item">

                        {{-- IMAGEN --}}
                        <div class="cliente-carrito-imagen">

                            @if(!empty($item['imagen']))

                            <img
                                src="{{ asset('storage/' . $item['imagen']) }}"
                                alt="{{ $item['nombre'] }}">

                            @else

                            <div>
                                <i class="bi bi-image"></i>
                            </div>

                            @endif

                        </div>


                        {{-- INFORMACIÓN --}}
                        <div class="cliente-carrito-info">

                            <h3>
                                {{ $item['nombre'] }}
                            </h3>

                            <p>
                                Bs.
                                {{ number_format($item['precio'], 2) }}
                                por unidad
                            </p>

                        </div>


                        {{-- CANTIDAD --}}
                        <div class="cliente-carrito-cantidad">

                            <span>
                                Cantidad
                            </span>

                            <div class="cliente-cantidad-controles">

                                <form
                                    action="{{ route('cliente.carrito.disminuir', $item['id']) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-dash"></i>
                                    </button>

                                </form>


                                <strong>
                                    {{ $item['cantidad'] }}
                                </strong>


                                <form
                                    action="{{ route('cliente.carrito.aumentar', $item['id']) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-plus"></i>
                                    </button>

                                </form>

                            </div>

                        </div>


                        {{-- SUBTOTAL --}}
                        <div class="cliente-carrito-subtotal">

                            <span>
                                Subtotal
                            </span>

                            <strong>
                                Bs.
                                {{ number_format($item['subtotal'], 2) }}
                            </strong>

                        </div>


                        {{-- ELIMINAR --}}
                        <div class="cliente-carrito-eliminar">

                            <form
                                action="{{ route('cliente.carrito.eliminar', $item['id']) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Eliminar producto">
                                    <i class="bi bi-trash"></i>
                                </button>

                            </form>

                        </div>

                    </div>

                    @endforeach

                </div>

            </div>


            {{-- VACIAR CARRITO --}}
            <div class="cliente-carrito-acciones">

                <form
                    action="{{ route('cliente.carrito.vaciar') }}"
                    method="POST">
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-outline-danger">
                        <i class="bi bi-trash3"></i>
                        Vaciar carrito
                    </button>

                </form>

            </div>

        </div>


        {{-- RESUMEN --}}
        <div class="col-lg-4">

            <div class="card cliente-carrito-resumen">

                <div class="card-header">

                    <h2>
                        <i class="bi bi-receipt"></i>
                        Resumen del pedido
                    </h2>

                </div>


                <div class="card-body">

                    <div class="cliente-resumen-linea">

                        <span>
                            Productos
                        </span>

                        <strong>
                            {{ count($carrito) }}
                        </strong>

                    </div>


                    <div class="cliente-resumen-linea">

                        <span>
                            Subtotal
                        </span>

                        <strong>
                            Bs.
                            {{ number_format($total, 2) }}
                        </strong>

                    </div>


                    <div class="cliente-resumen-linea">

                        <span>
                            Delivery
                        </span>

                        <strong>
                            Se calculará después
                        </strong>

                    </div>


                    <hr>


                    <div class="cliente-resumen-total">

                        <span>
                            Total
                        </span>

                        <strong>
                            Bs.
                            {{ number_format($total, 2) }}
                        </strong>

                    </div>


                    <a
                        href="{{ route('cliente.pedidos.create') }}"
                        class="btn btn-primary w-100 mt-4">
                        <i class="bi bi-credit-card"></i>
                        Continuar con el pedido
                    </a>


                    <a
                        href="{{ route('cliente.productos.index') }}"
                        class="btn btn-outline-secondary w-100 mt-2">
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