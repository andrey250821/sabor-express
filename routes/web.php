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
use App\Http\Controllers\Admin\PedidoController as AdminPedidoController;
use App\Http\Controllers\Admin\ProductoController;

/*
|--------------------------------------------------------------------------
| CONTROLADORES CLIENTE
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Cliente\CarritoController;
use App\Http\Controllers\Cliente\DashboardController as ClienteDashboardController;
use App\Http\Controllers\Cliente\PedidoController as ClientePedidoController;
use App\Http\Controllers\Cliente\ProductoController as ClienteProductoController;

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
| IMPORTANTE:
| El administrador NO cambia estados de pedidos manualmente.
| El administrador NO asigna repartidores.
|
| Su función es supervisar el sistema y administrar:
| - Clientes
| - Productos
| - Categorías
| - Comprobantes
| - Configuración
| - Repartidores
| - Pedidos
|
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {

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
    | CATEGORÍAS
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
    |
    | El administrador solamente:
    | - Lista pedidos
    | - Visualiza pedidos
    | - Supervisa estados
    |
    | NO cambia estados.
    | NO asigna repartidores.
    |
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/pedidos',
        [AdminPedidoController::class, 'index']
    )->name('admin.pedidos.index');

    Route::get(
        '/pedidos/{id}',
        [AdminPedidoController::class, 'show']
    )->name('admin.pedidos.show');


    /*
    |--------------------------------------------------------------------------
    | COMPROBANTES
    |--------------------------------------------------------------------------
    |
    | El administrador sí puede verificar el comprobante.
    |
    | Si lo aprueba:
    | comprobante_enviado -> pagado
    |
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
    | CONFIGURACIÓN
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


    /*
    |--------------------------------------------------------------------------
    | ADMINISTRACIÓN DE REPARTIDORES
    |--------------------------------------------------------------------------
    |
    | IMPORTANTE:
    | Estas rutas sirven para que el administrador cree y administre
    | las cuentas de los repartidores.
    |
    | NO sirven para asignar pedidos.
    |
    |--------------------------------------------------------------------------
    */

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
|
| AQUÍ está el nuevo funcionamiento.
|
| El repartidor:
|
| 1. Entra a su dashboard.
| 2. Ve pedidos con estado "listo".
| 3. Selecciona un pedido.
| 4. Presiona "Tomar pedido".
| 5. El sistema crea la asignación.
| 6. El pedido pasa a "asignado".
| 7. El repartidor puede iniciar la entrega.
| 8. Pasa a "en_camino".
| 9. Finalmente marca "entregado".
|
| El administrador NO interviene en este proceso.
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| DASHBOARD DELIVERY
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| DELIVERY
|--------------------------------------------------------------------------
*/

Route::get(
    '/delivery/dashboard',
    [DeliveryDashboardController::class, 'index']
)->name('delivery.dashboard');


/*
|--------------------------------------------------------------------------
| PEDIDOS DISPONIBLES PARA DELIVERY
|--------------------------------------------------------------------------
*/

Route::get(
    '/delivery/pedidos',
    [DeliveryPedidoController::class, 'index']
)->name('delivery.pedidos.index');


/*
|--------------------------------------------------------------------------
| DETALLE DE PEDIDO
|--------------------------------------------------------------------------
*/

Route::get(
    '/delivery/pedidos/{id}',
    [DeliveryPedidoController::class, 'show']
)->name('delivery.pedidos.show');


/*
|--------------------------------------------------------------------------
| TOMAR PEDIDO
|--------------------------------------------------------------------------
*/

Route::post(
    '/delivery/pedidos/{id}/tomar',
    [DeliveryPedidoController::class, 'tomar']
)->name('delivery.pedidos.tomar');


/*
|--------------------------------------------------------------------------
| MIS PEDIDOS / MIS ENTREGAS
|--------------------------------------------------------------------------
*/

Route::get(
    '/delivery/mis-pedidos',
    [DeliveryPedidoController::class, 'misPedidos']
)->name('delivery.pedidos.mis');


/*
|--------------------------------------------------------------------------
| INICIAR ENTREGA
|--------------------------------------------------------------------------
*/

Route::put(
    '/delivery/pedidos/{id}/iniciar',
    [DeliveryPedidoController::class, 'iniciar']
)->name('delivery.pedidos.iniciar');


/*
|--------------------------------------------------------------------------
| MARCAR PEDIDO COMO ENTREGADO
|--------------------------------------------------------------------------
*/

Route::put(
    '/delivery/pedidos/{id}/entregar',
    [DeliveryPedidoController::class, 'entregar']
)->name('delivery.pedidos.entregar');

/*
|--------------------------------------------------------------------------
| COCINERO
|--------------------------------------------------------------------------
|
| El cocinero trabaja únicamente con los pedidos pagados.
|
| Flujo:
|
| PAGADO
|    ↓
| PREPARANDO
|    ↓
| LISTO
|
| Cuando pasa a LISTO, automáticamente queda disponible
| para los repartidores.
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| DASHBOARD COCINERO
|--------------------------------------------------------------------------
*/

Route::get(
    '/cocinero/dashboard',
    [CocineroDashboardController::class, 'index']
)->name('cocinero.dashboard');


/*
|--------------------------------------------------------------------------
| LISTA DE PEDIDOS DEL COCINERO
|--------------------------------------------------------------------------
*/

Route::get(
    '/cocinero/pedidos',
    [CocineroPedidoController::class, 'index']
)->name('cocinero.pedidos.index');


/*
|--------------------------------------------------------------------------
| DETALLE DEL PEDIDO
|--------------------------------------------------------------------------
*/

Route::get(
    '/cocinero/pedidos/{id}',
    [CocineroPedidoController::class, 'show']
)->name('cocinero.pedidos.show');


/*
|--------------------------------------------------------------------------
| COMENZAR PREPARACIÓN
|--------------------------------------------------------------------------
|
| pagado -> preparando
|
|--------------------------------------------------------------------------
*/

Route::put(
    '/cocinero/pedidos/{id}/preparar',
    [CocineroPedidoController::class, 'preparar']
)->name('cocinero.pedidos.preparar');


/*
|--------------------------------------------------------------------------
| MARCAR PEDIDO COMO LISTO
|--------------------------------------------------------------------------
|
| preparando -> listo
|
| Después de esto el pedido aparece automáticamente
| en "Pedidos disponibles" del repartidor.
|
|--------------------------------------------------------------------------
*/

Route::put(
    '/cocinero/pedidos/{id}/listo',
    [CocineroPedidoController::class, 'listo']
)->name('cocinero.pedidos.listo');


/*
|--------------------------------------------------------------------------
| AUTH BREEZE
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
