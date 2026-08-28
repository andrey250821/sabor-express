<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| CONTROLADORES ADMIN
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ComprobantePagoController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\PedidoController as AdminPedidoController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\DeliveryController;

/*
|--------------------------------------------------------------------------
| CONTROLADORES CLIENTE
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Cliente\DashboardController as ClienteDashboardController;
use App\Http\Controllers\Cliente\ProductoController as ClienteProductoController;
use App\Http\Controllers\Cliente\CarritoController;
use App\Http\Controllers\Cliente\PedidoController as ClientePedidoController;
/*
|--------------------------------------------------------------------------
| CONTROLADORES cocinero
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Cocinero\DashboardController as CocineroDashboardController;
/*
|--------------------------------------------------------------------------
| BREEZE
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
*/


Route::middleware('auth')->prefix('admin')->group(function () {

    /*
|--------------------------------------------------------------------------
| CLIENTES
|--------------------------------------------------------------------------
*/

    Route::get(
        '/clientes',
        [ClienteController::class, 'index']
    )->name('admin.clientes.index');


    Route::get(
        '/clientes/{id}',
        [ClienteController::class, 'show']
    )->name('admin.clientes.show');


    Route::patch(
        '/clientes/{id}/activar',
        [ClienteController::class, 'activar']
    )->name('admin.clientes.activar');


    Route::patch(
        '/clientes/{id}/desactivar',
        [ClienteController::class, 'desactivar']
    )->name('admin.clientes.desactivar');


    Route::delete(
        '/clientes/{id}',
        [ClienteController::class, 'destroy']
    )->name('admin.clientes.destroy');

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('admin.dashboard');




    /*
    |--------------------------------------------------------------------------
    | PRODUCTOS
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'productos',
        ProductoController::class
    )->names('admin.productos');




    /*
    |--------------------------------------------------------------------------
    | CATEGORIAS
    |--------------------------------------------------------------------------
    */


    Route::resource(
        'categorias',
        CategoriaController::class
    )->names('admin.categorias');




    /*
    |--------------------------------------------------------------------------
    | PEDIDOS
    |--------------------------------------------------------------------------
    */


    Route::get(
        '/pedidos',
        [AdminPedidoController::class, 'index']
    )->name('admin.pedidos.index');

    // VER DETALLE DEL PEDIDO

    Route::get(
        '/pedidos/{id}',
        [AdminPedidoController::class, 'show']
    )->name('admin.pedidos.show');

    Route::put(
        '/pedidos/{id}/estado',
        [AdminPedidoController::class, 'cambiarEstado']
    )->name('admin.pedidos.estado');

    Route::post(
        '/pedidos/{pedido}/asignar',
        [DeliveryController::class, 'asignar']
    )->name('admin.pedidos.asignar');




    /*
    |--------------------------------------------------------------------------
    | COMPROBANTES
    |--------------------------------------------------------------------------
    */


    Route::get(
        '/comprobantes/{estado?}',
        [ComprobantePagoController::class, 'index']
    )->name('admin.comprobantes.index');


    Route::put(
        '/comprobantes/{id}/aprobar',
        [ComprobantePagoController::class, 'aprobar']
    )->name('admin.comprobantes.aprobar');


    Route::put(
        '/comprobantes/{id}/rechazar',
        [ComprobantePagoController::class, 'rechazar']
    )->name('admin.comprobantes.rechazar');





    /*
    |--------------------------------------------------------------------------
    | CONFIGURACION
    |--------------------------------------------------------------------------
    */


    Route::get(
        '/configuracion',
        [ConfiguracionController::class, 'index']
    )->name('admin.configuracion.index');


    Route::put(
        '/configuracion',
        [ConfiguracionController::class, 'update']
    )->name('admin.configuracion.update');
});







/*
|--------------------------------------------------------------------------
| CLIENTE
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| DASHBOARD DEL CLIENTE
|--------------------------------------------------------------------------
*/

Route::get(
    '/cliente',
    [ClienteDashboardController::class, 'index']
)->name('cliente.dashboard.index');
/*
|--------------------------------------------------------------------------
| PRODUCTOS DEL CLIENTE
|--------------------------------------------------------------------------
*/

Route::get(
    '/productos',
    [ClienteProductoController::class, 'index']
)->name('cliente.productos.index');


/*
|--------------------------------------------------------------------------
| CARRITO
|--------------------------------------------------------------------------
*/

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
| PEDIDOS DEL CLIENTE
|--------------------------------------------------------------------------
*/

Route::get(
    '/pedido',
    [ClientePedidoController::class, 'create']
)->name('cliente.pedidos.create');

Route::post(
    '/pedido',
    [ClientePedidoController::class, 'store']
)->name('cliente.pedidos.store');

Route::get(
    '/pedidos',
    [ClientePedidoController::class, 'index']
)->name('cliente.pedidos.index');

Route::get(
    '/pedidos/{id}',
    [ClientePedidoController::class, 'show']
)->name('cliente.pedidos.show');
/*
|--------------------------------------------------------------------------
| DELIVERY
|--------------------------------------------------------------------------
*/

Route::get(
    '/delivery/dashboard',
    function () {
        return view('layouts.delivery');
    }
)->name('delivery.dashboard');

Route::get(
    '/deliverys',
    [DeliveryController::class, 'index']
)->name('admin.deliverys.index');

Route::get(
    '/deliverys/create',
    [DeliveryController::class, 'create']
)->name('admin.deliverys.create');

Route::post(
    '/deliverys',
    [DeliveryController::class, 'store']
)->name('admin.deliverys.store');

Route::get(
    '/deliverys/{id}/edit',
    [DeliveryController::class, 'edit']
)->name('admin.deliverys.edit');

Route::put(
    '/deliverys/{id}',
    [DeliveryController::class, 'update']
)->name('admin.deliverys.update');

Route::delete(
    '/deliverys/{id}',
    [DeliveryController::class, 'destroy']
)->name('admin.deliverys.destroy');

Route::patch(
    '/deliverys/{id}/activar',
    [DeliveryController::class, 'activar']
)->name('admin.deliverys.activar');

/*
|--------------------------------------------------------------------------
| COCINERO
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
        '/cocinero/dashboard',
        function () {
            return view('cocinero.dashboard');
        }
    )->name('cocinero.dashboard');

});
/*
|--------------------------------------------------------------------------
| AUTH BREEZE
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
