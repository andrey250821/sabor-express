@extends('layouts.cliente')

@section('title', 'Realizar Pedido')

@section('content')

<div class="cliente-pedido-page">

    {{-- ENCABEZADO --}}
    <div class="cliente-pedido-header">

        <h1>
            <i class="bi bi-bag-check"></i>
            Realizar Pedido
        </h1>

        <p>
            Completa los datos para confirmar tu pedido.
        </p>

    </div>


    {{-- MENSAJES --}}
    @if(session('error'))

    <div class="alert alert-danger">
        {{ session('error') }}
    </div>

    @endif


    @if(session('success'))

    <div class="alert alert-success">
        {{ session('success') }}
    </div>

    @endif


    {{-- ERRORES DE VALIDACIÓN --}}
    @if($errors->any())

    <div class="alert alert-danger">

        <strong>Por favor corrige los siguientes errores:</strong>

        <ul class="mb-0 mt-2">

            @foreach($errors->all() as $error)

            <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

    @endif


    {{-- FORMULARIO --}}
    <form
        action="{{ route('cliente.pedidos.store') }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf


        {{-- DATOS DE ENTREGA --}}
        <div class="cliente-pedido-section">

            <h2>
                <i class="bi bi-geo-alt"></i>
                Datos de entrega
            </h2>


            <div class="mb-3">

                <label for="direccion_entrega">
                    Dirección de entrega
                </label>

                <textarea
                    name="direccion_entrega"
                    id="direccion_entrega"
                    class="form-control"
                    rows="3"
                    placeholder="Ingresa la dirección donde deseas recibir tu pedido"
                    required>{{ old('direccion_entrega') }}</textarea>

            </div>


            <div class="mb-3">

                <label for="referencia_delivery">
                    Referencia para el delivery
                </label>

                <textarea
                    name="referencia_delivery"
                    id="referencia_delivery"
                    class="form-control"
                    rows="2"
                    placeholder="Ej.: casa de color azul, frente a la plaza...">{{ old('referencia_delivery') }}</textarea>

            </div>


            <div class="mb-3">

                <label for="observacion_cliente">
                    Observaciones
                </label>

                <textarea
                    name="observacion_cliente"
                    id="observacion_cliente"
                    class="form-control"
                    rows="2"
                    maxlength="500"
                    placeholder="Alguna indicación adicional para tu pedido...">{{ old('observacion_cliente') }}</textarea>

            </div>

        </div>


        {{-- UBICACIÓN --}}
        <div class="cliente-pedido-section">

            <h2>
                <i class="bi bi-map"></i>
                Ubicación
            </h2>

            <p>
                Si deseas, puedes registrar las coordenadas de tu ubicación.
            </p>


            <div class="row">

                <div class="col-md-6 mb-3">

                    <label for="latitud">
                        Latitud
                    </label>

                    <input
                        type="text"
                        name="latitud"
                        id="latitud"
                        class="form-control"
                        value="{{ old('latitud') }}"
                        placeholder="-17.3895">

                </div>


                <div class="col-md-6 mb-3">

                    <label for="longitud">
                        Longitud
                    </label>

                    <input
                        type="text"
                        name="longitud"
                        id="longitud"
                        class="form-control"
                        value="{{ old('longitud') }}"
                        placeholder="-66.1568">

                </div>

            </div>

        </div>


        {{-- CONFIGURACIÓN DE PAGO --}}
        <div class="cliente-pedido-section">

            <h2>
                <i class="bi bi-credit-card"></i>
                Pago
            </h2>


            @if($configuracion)

            <p>
                Realiza el pago utilizando los datos proporcionados por el restaurante.
            </p>


            @if(!empty($configuracion->qr_pago))

            <div class="mb-3">

                <p>
                    <strong>QR de pago:</strong>
                </p>

                <img
                    src="{{ asset('storage/' . $configuracion->qr_pago) }}"
                    alt="QR de pago"
                    style="max-width: 300px;">

            </div>

            @endif


            @if(!empty($configuracion->numero_cuenta))

            <p>
                <strong>Número de cuenta:</strong>
                {{ $configuracion->numero_cuenta }}
            </p>

            @endif


            @else

            <div class="alert alert-warning">

                Los datos de pago todavía no han sido configurados.

            </div>

            @endif

        </div>


        {{-- COMPROBANTE --}}
        <div class="cliente-pedido-section">

            <h2>
                <i class="bi bi-image"></i>
                Comprobante de pago
            </h2>


            <div class="mb-3">

                <label for="comprobante">
                    Selecciona una imagen del comprobante
                </label>

                <input
                    type="file"
                    name="comprobante"
                    id="comprobante"
                    class="form-control"
                    accept=".jpg,.jpeg,.png"
                    required>

                <small>
                    Formatos permitidos: JPG, JPEG y PNG. Máximo 2 MB.
                </small>

            </div>

        </div>


        {{-- RESUMEN --}}
        <div class="cliente-pedido-section">

            <h2>
                <i class="bi bi-receipt"></i>
                Resumen del pedido
            </h2>


            <div class="cliente-pedido-total">

                <span>
                    Total a pagar:
                </span>

                <strong>
                    Bs {{ number_format($total, 2) }}
                </strong>

            </div>

        </div>


        {{-- BOTONES --}}
        <div class="cliente-pedido-actions">

            <a
                href="{{ route('cliente.carrito.index') }}"
                class="btn btn-secondary">

                <i class="bi bi-arrow-left"></i>
                Volver al carrito

            </a>


            <button
                type="submit"
                class="btn btn-primary">

                <i class="bi bi-check-circle"></i>
                Confirmar pedido

            </button>

        </div>

    </form>

</div>

@endsection