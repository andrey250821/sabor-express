<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Delivery</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">

    <div class="container py-5">

        {{-- ENCABEZADO --}}
        <div class="d-flex justify-content-between align-items-center mb-5">

            <div>
                <h1 class="fw-bold">
                    Panel del Delivery
                </h1>

                <p class="text-muted mb-0">
                    Bienvenido, {{ $delivery->name }}
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                    class="btn btn-outline-danger">

                    <i class="bi bi-box-arrow-right"></i>

                    Salir

                </button>
            </form>

        </div>


        {{-- TARJETAS --}}
        <div class="row g-4">


            {{-- PEDIDOS DISPONIBLES --}}
            <div class="col-md-4">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h6 class="text-muted">
                                    Pedidos disponibles
                                </h6>

                                <h2 class="fw-bold">
                                    {{ $pedidosDisponibles }}
                                </h2>

                            </div>

                            <div class="text-primary fs-1">
                                <i class="bi bi-bag-check"></i>
                            </div>

                        </div>

                        <p class="text-muted">
                            Pedidos listos para ser tomados.
                        </p>

                        <a href="{{ route('delivery.pedidos.index') }}"
                            class="btn btn-primary">

                            Ver pedidos

                        </a>

                    </div>

                </div>

            </div>


            {{-- MIS PEDIDOS --}}
            <div class="col-md-4">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h6 class="text-muted">
                                    Mis pedidos
                                </h6>

                                <h2 class="fw-bold">
                                    {{ $misPedidos }}
                                </h2>

                            </div>

                            <div class="text-warning fs-1">
                                <i class="bi bi-bicycle"></i>
                            </div>

                        </div>

                        <p class="text-muted">
                            Pedidos que estás gestionando actualmente.
                        </p>

                        <a href="{{ route('delivery.pedidos.mis') }}"
                            class="btn btn-warning">

                            Mis pedidos

                        </a>

                    </div>

                </div>

            </div>


            {{-- ENTREGADOS --}}
            <div class="col-md-4">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h6 class="text-muted">
                                    Pedidos entregados
                                </h6>

                                <h2 class="fw-bold">
                                    {{ $pedidosEntregados }}
                                </h2>

                            </div>

                            <div class="text-success fs-1">
                                <i class="bi bi-check-circle"></i>
                            </div>

                        </div>

                        <p class="text-muted">
                            Total de pedidos entregados por ti.
                        </p>

                    </div>

                </div>

            </div>

        </div>


        {{-- INFORMACIÓN DEL DELIVERY --}}
        <div class="card shadow-sm mt-5">

            <div class="card-body">

                <h4 class="fw-bold mb-4">

                    <i class="bi bi-person-circle"></i>

                    Mi información

                </h4>


                <div class="row">

                    <div class="col-md-6 mb-3">

                        <strong>Nombre:</strong>

                        <br>

                        {{ $delivery->name }}

                    </div>


                    <div class="col-md-6 mb-3">

                        <strong>Correo:</strong>

                        <br>

                        {{ $delivery->email }}

                    </div>


                    <div class="col-md-6 mb-3">

                        <strong>Estado:</strong>

                        <br>

                        <span class="badge bg-success">

                            {{ ucfirst($delivery->estado) }}

                        </span>

                    </div>


                    <div class="col-md-6 mb-3">

                        <strong>Rol:</strong>

                        <br>

                        Delivery

                    </div>

                </div>

            </div>

        </div>


    </div>


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>