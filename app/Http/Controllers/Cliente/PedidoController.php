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
    public function create()
    {
        $carrito = session()->get('carrito', []);

        if (count($carrito) === 0) {
            return redirect()
                ->route('cliente.productos.index')
                ->with('error', 'El carrito está vacío');
        }

        $total = 0;

        foreach ($carrito as $producto) {
            $total += $producto['subtotal'];
        }

        $configuracion = Configuracion::first();

        return view('cliente.pedidos.create', compact('total', 'configuracion'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'direccion_entrega' => 'required|string|max:1000',
            'referencia_delivery' => 'nullable|string|max:1000',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'observacion_cliente' => 'nullable|string|max:500',
            'comprobante' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $carrito = session()->get('carrito', []);

        if (count($carrito) === 0) {
            return redirect()
                ->route('cliente.productos.index')
                ->with('error', 'El carrito está vacío');
        }

        DB::beginTransaction();

        try {
            $total = 0;

            foreach ($carrito as $item) {
                $total += $item['subtotal'];
            }

            $pedido = Pedido::create([
                'user_id' => Auth::id(),
                'total' => $total,
                'estado' => 'comprobante_enviado',
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
                'direccion_entrega' => $request->direccion_entrega,
                'observacion_cliente' => $request->observacion_cliente,
                'referencia_delivery' => $request->referencia_delivery,
            ]);

            foreach ($carrito as $item) {
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                    'subtotal' => $item['subtotal'],
                ]);

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

            $imagen = $request->file('comprobante')->store('comprobantes', 'public');

            ComprobantePago::create([
                'pedido_id' => $pedido->id,
                'imagen' => $imagen,
                'estado' => 'pendiente',
            ]);

            DB::commit();
            session()->forget('carrito');

            return redirect()
                ->route('cliente.pedidos.show', $pedido->id)
                ->with('success', 'Pedido enviado correctamente. El comprobante será revisado por el restaurante.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error al procesar pedido: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $pedidos = Pedido::where('user_id', Auth::id())
            ->with(['detallePedidos.producto', 'comprobantePago'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cliente.pedidos.index', compact('pedidos'));
    }

    public function show($id)
    {
        $pedido = Pedido::where('user_id', Auth::id())
            ->with(['detallePedidos.producto', 'comprobantePago'])
            ->findOrFail($id);

        return view('cliente.pedidos.show', compact('pedido'));
    }
}
