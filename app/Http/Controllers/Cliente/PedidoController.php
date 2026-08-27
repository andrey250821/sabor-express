<?php

namespace App\Http\Controllers\Cliente;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pedido;
use App\Models\ComprobantePago;
use App\Models\Configuracion;
use App\Models\DetallePedido;
use App\Models\Producto;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class PedidoController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Mostrar formulario de pedido
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $carrito = session()->get('carrito', []);

        if (count($carrito) == 0) {
            return redirect()
                ->route('cliente.productos.index')
                ->with(
                    'error',
                    'El carrito está vacío'
                );
        }

        $total = 0;

        foreach ($carrito as $producto) {
            $total += $producto['subtotal'];
        }

        $configuracion = Configuracion::first();

        return view(
            'cliente.pedidos.create',
            compact(
                'total',
                'configuracion'
            )
        );
    }








    /*
    |--------------------------------------------------------------------------
    | Guardar pedido
    |--------------------------------------------------------------------------
    */


    public function store(Request $request)
    {



        $request->validate([
            'direccion_entrega' => 'required',
            'referencia_delivery' => 'nullable',
            'latitud' => 'nullable',
            'longitud' => 'nullable',
            'observacion_cliente' => 'nullable|string|max:500',
            'comprobante' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $carrito = session()->get('carrito', []);

        if (count($carrito) == 0) {


            return redirect()
                ->route('cliente.productos.index')
                ->with(
                    'error',
                    'El carrito está vacío'
                );
        }








        DB::beginTransaction();




        try {



            /*
            Calcular total real
            */


            $total = 0;



            foreach ($carrito as $item) {

                $total += $item['subtotal'];
            }









            /*
            Crear pedido
            */


            $pedido = Pedido::create([



                'user_id'
                =>
                Auth::id(),



                'total'
                =>
                $total,



                'estado'
                =>
                'comprobante_enviado',



                'latitud'
                =>
                $request->latitud,



                'longitud'
                =>
                $request->longitud,



                'direccion_entrega'
                =>
                $request->direccion_entrega,



                'observacion_cliente'
                =>
                'nullable|string|max:500',



                'referencia_delivery'
                =>
                $request->referencia_delivery



            ]);

            /*
            Guardar productos comprados
            y descontar stock
            */


            foreach ($carrito as $item) {



                DetallePedido::create([



                    'pedido_id'
                    =>
                    $pedido->id,



                    'producto_id'
                    =>
                    $item['id'],



                    'cantidad'
                    =>
                    $item['cantidad'],



                    'precio'
                    =>
                    $item['precio'],



                    'subtotal'
                    =>
                    $item['subtotal']



                ]);








                /*
                Descontar stock
                */


                $producto = Producto::find($item['id']);




                if ($producto) {


                    $producto->stock -= $item['cantidad'];




                    if ($producto->stock <= 0) {


                        $producto->stock = 0;


                        $producto->estado = 'agotado';
                    }




                    $producto->save();
                }
            }









            /*
            Guardar comprobante
            */


            if ($request->hasFile('comprobante')) {


                $imagen =
                    $request->file('comprobante')
                    ->store(
                        'comprobantes',
                        'public'
                    );




                ComprobantePago::create([



                    'pedido_id'
                    =>
                    $pedido->id,



                    'imagen'
                    =>
                    $imagen,



                    'estado'
                    =>
                    'pendiente'



                ]);
            }

            /*
            Confirmar todo
            */
            DB::commit();

            /*
            Vaciar carrito
            */


            session()->forget('carrito');

            return redirect()
                ->route('cliente.productos')
                ->with(
                    'success',
                    'Pedido enviado correctamente'
                );
        } catch (\Exception $e) {


            DB::rollBack();

            return back()
                ->with(
                    'error',
                    'Error al procesar pedido: ' . $e->getMessage()
                );
        }
    }
    /**
     * Mostrar los pedidos del cliente autenticado.
     */
    public function index()
    {
        $pedidos = Pedido::where('user_id', Auth::id())
            ->with([
                'detallePedidos.producto',
                'comprobantePago'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view(
            'cliente.pedidos.index',
            compact('pedidos')
        );
    }


    /**
     * Mostrar el detalle de un pedido del cliente.
     */
    public function show($id)
    {
        $pedido = Pedido::where('user_id', Auth::id())
            ->with([
                'detallePedidos.producto',
                'comprobantePago'
            ])
            ->findOrFail($id);

        return view(
            'cliente.pedidos.show',
            compact('pedido')
        );
    }
}
