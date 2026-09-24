<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CONTROLADORES ADMIN
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\ComprobantePagoController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\CocineroController;
use App\Http\Controllers\Admin\PedidoController as AdminPedidoController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\NotificacionController;
/*
|--------------------------------------------------------------------------
| CONTROLADORES CLIENTE
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Cliente\CarritoController;
use App\Http\Controllers\Cliente\DashboardController as ClienteDashboardController;
use App\Http\Controllers\Cliente\PedidoController as ClientePedidoController;
use App\Http\Controllers\Cliente\ProductoController as ClienteProductoController;
use App\Http\Controllers\Cliente\CalificacionController;
use App\Http\Controllers\Cliente\NotificacionController as ClienteNotificacionController;
use App\Http\Controllers\Cliente\PerfilController as ClientePerfilController;

/*
|--------------------------------------------------------------------------
| CONTROLADORES COCINERO
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Cocinero\DashboardController as CocineroDashboardController;
use App\Http\Controllers\Cocinero\PedidoController as CocineroPedidoController;

/*
|--------------------------------------------------------------------------
| CONTROLADORES DELIVERY
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Delivery\DashboardController as DeliveryDashboardController;
use App\Http\Controllers\Delivery\PedidoController as DeliveryPedidoController;

/*
|--------------------------------------------------------------------------
| PERFIL
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('cliente.dashboard.index');
});


/*
|--------------------------------------------------------------------------
| PERFIL
|--------------------------------------------------------------------------
|
| Cualquier usuario autenticado puede acceder a su perfil.
|
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| ADMINISTRADOR
|--------------------------------------------------------------------------
|
| SOLO usuarios con rol "Administrador".
|
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Administrador'])
    ->prefix('admin')
    ->group(function () {

        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )->name('admin.dashboard');

        Route::get('/clientes', [ClienteController::class, 'index'])->name('admin.clientes.index');
        Route::get('/clientes/{id}', [ClienteController::class, 'show'])->name('admin.clientes.show');
        Route::patch('/clientes/{id}/activar', [ClienteController::class, 'activar'])->name('admin.clientes.activar');
        Route::patch('/clientes/{id}/desactivar', [ClienteController::class, 'desactivar'])->name('admin.clientes.desactivar');
        Route::delete('/clientes/{id}', [ClienteController::class, 'destroy'])->name('admin.clientes.destroy');


        Route::get(
            '/productos/{id}/calificaciones',
            [ProductoController::class, 'calificaciones']
        )->name('admin.productos.calificaciones');

        Route::resource('productos', ProductoController::class)->names('admin.productos');
        Route::resource('categorias', CategoriaController::class)->names('admin.categorias');

        Route::get('/pedidos', [AdminPedidoController::class, 'index'])->name('admin.pedidos.index');
        Route::get('/pedidos/{id}', [AdminPedidoController::class, 'show'])->name('admin.pedidos.show');

        Route::get('/comprobantes/{estado?}', [ComprobantePagoController::class, 'index'])->name('admin.comprobantes.index');
        Route::get('/comprobantes/{id}/imagen', [ComprobantePagoController::class, 'verImagen'])->name('admin.comprobantes.imagen');
        Route::put('/comprobantes/{id}/aprobar', [ComprobantePagoController::class, 'aprobar'])->name('admin.comprobantes.aprobar');
        Route::put('/comprobantes/{id}/rechazar', [ComprobantePagoController::class, 'rechazar'])->name('admin.comprobantes.rechazar');

        // Notificaciones del administrador
        Route::get(
            '/notificaciones',
            [NotificacionController::class, 'index']
        )->name('admin.notificaciones.index');

        Route::patch(
            '/notificaciones/{id}/leer',
            [NotificacionController::class, 'marcarLeida']
        )->name('admin.notificaciones.leer');

        Route::patch(
            '/notificaciones/leer-todas',
            [NotificacionController::class, 'marcarTodasLeidas']
        )->name('admin.notificaciones.leer.todas');

        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('admin.configuracion.index');
        Route::put('/configuracion', [ConfiguracionController::class, 'update'])->name('admin.configuracion.update');

        Route::get('/deliverys', [DeliveryController::class, 'index'])->name('admin.deliverys.index');
        Route::get('/deliverys/create', [DeliveryController::class, 'create'])->name('admin.deliverys.create');
        Route::post('/deliverys', [DeliveryController::class, 'store'])->name('admin.deliverys.store');
        Route::get('/deliverys/{id}/edit', [DeliveryController::class, 'edit'])->name('admin.deliverys.edit');
        Route::put('/deliverys/{id}', [DeliveryController::class, 'update'])->name('admin.deliverys.update');
        Route::delete('/deliverys/{id}', [DeliveryController::class, 'destroy'])->name('admin.deliverys.destroy');
        Route::patch('/deliverys/{id}/activar', [DeliveryController::class, 'activar'])->name('admin.deliverys.activar');

        // Gestión de Cocineros
        Route::get('/cocineros', [CocineroController::class, 'index'])->name('admin.cocineros.index');
        Route::get('/cocineros/create', [CocineroController::class, 'create'])->name('admin.cocineros.create');
        Route::get('/cocineros/{id}', [CocineroController::class, 'show'])->name('admin.cocineros.show');
        Route::post('/cocineros', [CocineroController::class, 'store'])->name('admin.cocineros.store');
        Route::get('/cocineros/{id}/edit', [CocineroController::class, 'edit'])->name('admin.cocineros.edit');
        Route::put('/cocineros/{id}', [CocineroController::class, 'update'])->name('admin.cocineros.update');
        Route::delete('/cocineros/{id}', [CocineroController::class, 'destroy'])->name('admin.cocineros.destroy');
        Route::patch('/cocineros/{id}/activar', [CocineroController::class, 'activar'])->name('admin.cocineros.activar');
    });


/*
|--------------------------------------------------------------------------
| CLIENTE - RUTAS PÚBLICAS
|--------------------------------------------------------------------------
|
| Los visitantes pueden navegar por el restaurante y utilizar el carrito
| sin necesidad de iniciar sesión.
|
|--------------------------------------------------------------------------
*/

Route::get(
    '/cliente',
    [ClienteDashboardController::class, 'index']
)->name('cliente.dashboard.index');

Route::get(
    '/productos',
    [ClienteProductoController::class, 'index']
)->name('cliente.productos.index');

Route::get(
    '/carrito',
    [CarritoController::class, 'index']
)->name('cliente.carrito.index');

Route::post(
    '/carrito/agregar/{id}',
    [CarritoController::class, 'agregar']
)->name('cliente.carrito.agregar');

Route::put(
    '/carrito/aumentar/{id}',
    [CarritoController::class, 'aumentar']
)->name('cliente.carrito.aumentar');

Route::put(
    '/carrito/disminuir/{id}',
    [CarritoController::class, 'disminuir']
)->name('cliente.carrito.disminuir');

Route::delete(
    '/carrito/eliminar/{id}',
    [CarritoController::class, 'eliminar']
)->name('cliente.carrito.eliminar');

Route::delete(
    '/carrito/vaciar',
    [CarritoController::class, 'vaciar']
)->name('cliente.carrito.vaciar');


/*
|--------------------------------------------------------------------------
| CLIENTE - RUTAS PROTEGIDAS
|--------------------------------------------------------------------------
|
| Estas funciones requieren que el usuario haya iniciado sesión y tenga
| el rol Cliente.
|
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Cliente'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | CALIFICACIONES
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/pedidos/{pedidoId}/productos/{productoId}/calificar',
            [CalificacionController::class, 'store']
        )->name('cliente.calificaciones.store');

        Route::put(
            '/pedidos/{pedidoId}/productos/{productoId}/calificar',
            [CalificacionController::class, 'update']
        )->name('cliente.calificaciones.update');

        Route::get(
            '/productos/{productoId}/calificaciones',
            [CalificacionController::class, 'index']
        )->name('cliente.calificaciones.index');


        /*
        |--------------------------------------------------------------------------
        | NOTIFICACIONES DEL CLIENTE
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/notificaciones',
            [ClienteNotificacionController::class, 'index']
        )->name('cliente.notificaciones.index');

        Route::patch(
            '/notificaciones/{id}/leer',
            [ClienteNotificacionController::class, 'marcarLeida']
        )->name('cliente.notificaciones.leer');

        Route::patch(
            '/notificaciones/leer-todas',
            [ClienteNotificacionController::class, 'marcarTodasLeidas']
        )->name('cliente.notificaciones.leer.todas');


        /*
        |--------------------------------------------------------------------------
        | CONFIGURACIÓN DEL PERFIL DEL CLIENTE
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/configuracion',
            [ClientePerfilController::class, 'edit']
        )->name('cliente.configuracion.edit');

        Route::patch(
            '/configuracion',
            [ClientePerfilController::class, 'update']
        )->name('cliente.configuracion.update');

        Route::patch(
            '/configuracion/password',
            [ClientePerfilController::class, 'updatePassword']
        )->name('cliente.configuracion.password');


        /*
        |--------------------------------------------------------------------------
        | REALIZAR PEDIDO / CHECKOUT
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/pedido',
            [ClientePedidoController::class, 'create']
        )->name('cliente.pedidos.create');

        Route::get(
            '/pedido/direccion',
            [ClientePedidoController::class, 'direccion']
        )->name('cliente.pedidos.direccion');

        Route::post(
            '/pedido/generar-comprobantes-prueba',
            [ClientePedidoController::class, 'generarComprobantesPrueba']
        )->name('cliente.pedidos.generar.comprobantes.prueba');

        Route::get(
            '/pedido/comprobante-prueba/{nombreArchivo}',
            [ClientePedidoController::class, 'verComprobantePrueba']
        )->where('nombreArchivo', 'pedido_[0-9]+_cliente_[A-Za-z0-9_-]+_(CORRECTO|MONTO_INCORRECTO|REFERENCIA_DUPLICADA|INCOMPLETO)\.png')
         ->name('cliente.pedidos.comprobante.prueba.imagen');

        Route::get(
            '/pedidos/{id}/comprobante-imagen',
            [ClientePedidoController::class, 'verComprobante']
        )->name('cliente.pedidos.comprobante.imagen');

        Route::post(
            '/pedido',
            [ClientePedidoController::class, 'store']
        )->name('cliente.pedidos.store');


        /*
        |--------------------------------------------------------------------------
        | MIS PEDIDOS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/pedidos',
            [ClientePedidoController::class, 'index']
        )->name('cliente.pedidos.index');

        Route::get(
            '/pedidos/{id}',
            [ClientePedidoController::class, 'show']
        )->name('cliente.pedidos.show');
    });
/*
|--------------------------------------------------------------------------
| DELIVERY
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Delivery'])
    ->prefix('delivery')
    ->group(function () {
        Route::get('/dashboard', [DeliveryDashboardController::class, 'index'])->name('delivery.dashboard');
        Route::get('/pedidos', [DeliveryPedidoController::class, 'index'])->name('delivery.pedidos.index');
        Route::get('/pedidos/{id}', [DeliveryPedidoController::class, 'show'])->name('delivery.pedidos.show');
        Route::post('/pedidos/{id}/tomar', [DeliveryPedidoController::class, 'tomar'])->name('delivery.pedidos.tomar');
        Route::get('/mis-pedidos', [DeliveryPedidoController::class, 'misPedidos'])->name('delivery.pedidos.mis');
        Route::put('/pedidos/{id}/iniciar', [DeliveryPedidoController::class, 'iniciar'])->name('delivery.pedidos.iniciar');
        Route::put('/pedidos/{id}/entregar', [DeliveryPedidoController::class, 'entregar'])->name('delivery.pedidos.entregar');
    });


/*
|--------------------------------------------------------------------------
| COCINERO
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Cocinero'])
    ->prefix('cocinero')
    ->group(function () {
        Route::get('/dashboard', [CocineroDashboardController::class, 'index'])->name('cocinero.dashboard');
        Route::get('/perfil', [CocineroPerfilController::class, 'edit'])->name('cocinero.perfil.edit');
        Route::patch('/perfil', [CocineroPerfilController::class, 'update'])->name('cocinero.perfil.update');
        Route::get('/pedidos', [CocineroPedidoController::class, 'index'])->name('cocinero.pedidos.index');
        Route::get('/pedidos/{id}', [CocineroPedidoController::class, 'show'])->name('cocinero.pedidos.show');
        Route::put('/pedidos/{id}/preparar', [CocineroPedidoController::class, 'preparar'])->name('cocinero.pedidos.preparar');
        Route::put('/pedidos/{id}/listo', [CocineroPedidoController::class, 'listo'])->name('cocinero.pedidos.listo');
    });


require __DIR__ . '/auth.php';
