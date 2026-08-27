@extends('layouts.admin')

@section('content')

<div class="container-fluid px-0 configuracion-page">

    {{-- ENCABEZADO --}}
    <div class="configuracion-header">

        <div class="configuracion-title-wrapper">

            <div class="configuracion-icon">
                <i class="bi bi-gear-fill"></i>
            </div>

            <div>
                <h2 class="configuracion-title">
                    Configuración
                </h2>

                <p class="configuracion-subtitle">
                    Administra la información y configuración de Sabor Express.
                </p>
            </div>

        </div>

    </div>


    {{-- MENSAJE --}}
    @if(session('success'))

    <div class="alert configuracion-alert-success alert-dismissible fade show">

        <i class="bi bi-check-circle-fill"></i>

        <span>
            {{ session('success') }}
        </span>

        <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

    @endif


    @if(session('error'))

    <div class="alert configuracion-alert-error alert-dismissible fade show">

        <i class="bi bi-exclamation-circle-fill"></i>

        <span>
            {{ session('error') }}
        </span>

        <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

    @endif


    {{-- FORMULARIO --}}
    <form action="{{ route('admin.configuracion.update') }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')


        {{-- INFORMACIÓN DEL RESTAURANTE --}}
        <div class="configuracion-card mb-4">

            <div class="configuracion-card-header">

                <div class="configuracion-section-icon">
                    <i class="bi bi-shop"></i>
                </div>

                <div>

                    <h5>
                        Información del restaurante
                    </h5>

                    <p>
                        Datos principales de Sabor Express
                    </p>

                </div>

            </div>


            <div class="configuracion-card-body">

                <div class="row g-4">

                    {{-- NOMBRE --}}
                    <div class="col-12 col-lg-6">

                        <label class="configuracion-label">

                            <i class="bi bi-shop"></i>

                            Nombre del restaurante

                        </label>

                        <input
                            type="text"
                            class="form-control configuracion-input"
                            name="nombre_restaurante"
                            value="{{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}"
                            placeholder="Nombre del restaurante">

                    </div>


                    {{-- TELEFONO --}}
                    <div class="col-12 col-lg-6">

                        <label class="configuracion-label">

                            <i class="bi bi-telephone-fill"></i>

                            Teléfono

                        </label>

                        <input
                            type="text"
                            class="form-control configuracion-input"
                            name="telefono"
                            value="{{ $configuracion->telefono ?? '' }}"
                            placeholder="Ej. 72222222">

                    </div>


                    {{-- DIRECCION --}}
                    <div class="col-12">

                        <label class="configuracion-label">

                            <i class="bi bi-geo-alt-fill"></i>

                            Dirección

                        </label>

                        <textarea
                            class="form-control configuracion-input configuracion-textarea"
                            name="direccion"
                            rows="4"
                            placeholder="Dirección del restaurante">{{ $configuracion->direccion ?? '' }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        {{-- ARCHIVOS --}}
        <div class="row g-4">


            {{-- LOGO --}}
            <div class="col-12 col-xl-6">

                <div class="configuracion-card configuracion-file-card h-100">

                    <div class="configuracion-card-header">

                        <div class="configuracion-section-icon">
                            <i class="bi bi-image"></i>
                        </div>

                        <div>

                            <h5>
                                Logo del restaurante
                            </h5>

                            <p>
                                Imagen utilizada como identidad visual.
                            </p>

                        </div>

                    </div>


                    <div class="configuracion-card-body">

                        <label class="configuracion-label">

                            <i class="bi bi-upload"></i>

                            Seleccionar logo

                        </label>


                        <input
                            type="file"
                            class="form-control configuracion-input"
                            name="logo"
                            accept="image/*"
                            onchange="previewImage(event, 'logoPreview')">


                        <small class="configuracion-help">
                            Formatos recomendados: JPG, PNG o WEBP.
                        </small>


                        {{-- PREVIEW --}}
                        <div class="configuracion-preview-container">

                            <span class="configuracion-preview-title">
                                Vista previa
                            </span>


                            <div class="configuracion-logo-preview">

                                <img
                                    id="logoPreview"
                                    @if(!empty($configuracion->logo))
                                src="{{ asset('storage/'.$configuracion->logo) }}"
                                @endif
                                alt="Vista previa del logo">

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- QR --}}
            <div class="col-12 col-xl-6">

                <div class="configuracion-card configuracion-file-card h-100">

                    <div class="configuracion-card-header">

                        <div class="configuracion-section-icon">
                            <i class="bi bi-qr-code"></i>
                        </div>

                        <div>

                            <h5>
                                QR de pago
                            </h5>

                            <p>
                                Código QR utilizado para recibir pagos.
                            </p>

                        </div>

                    </div>


                    <div class="configuracion-card-body">

                        <label class="configuracion-label">

                            <i class="bi bi-upload"></i>

                            Seleccionar QR

                        </label>


                        <input
                            type="file"
                            class="form-control configuracion-input"
                            name="qr_pago"
                            accept="image/*"
                            onchange="previewImage(event, 'qrPreview')">


                        <small class="configuracion-help">
                            Utiliza una imagen clara y de buena resolución.
                        </small>


                        {{-- PREVIEW --}}
                        <div class="configuracion-preview-container">

                            <span class="configuracion-preview-title">
                                Vista previa
                            </span>


                            <div class="configuracion-qr-preview">

                                <img
                                    id="qrPreview"
                                    @if(!empty($configuracion->qr_pago))
                                src="{{ asset('storage/'.$configuracion->qr_pago) }}"
                                @endif
                                alt="Vista previa del QR">

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- BOTONES --}}
        <div class="configuracion-actions">

            <button
                type="submit"
                class="btn configuracion-btn-save">

                <i class="bi bi-check-circle-fill"></i>

                Guardar configuración

            </button>

        </div>

    </form>

</div>


{{-- PREVISUALIZACIÓN DE IMÁGENES --}}
<script>
    function previewImage(event, id) {
        const imagen = event.target.files[0];

        if (!imagen) {
            return;
        }

        const reader = new FileReader();

        reader.onload = function(e) {
            const preview = document.getElementById(id);

            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };

        reader.readAsDataURL(imagen);
    }
</script>

@endsection