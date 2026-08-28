<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Cocinero</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body>

    <div class="container mt-5">

        <div class="card shadow">

            <div class="card-body">

                <h1 class="mb-3">
                    Panel del Cocinero
                </h1>

                <p>
                    Bienvenido, {{ auth()->user()->name }}
                </p>

                <hr>

                <h4>Pedidos</h4>

                <p>
                    Aquí aparecerán los pedidos pendientes de preparación.
                </p>

            </div>

        </div>

    </div>

</body>

</html>