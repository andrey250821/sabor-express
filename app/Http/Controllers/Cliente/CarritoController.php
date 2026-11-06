<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Services\ReservaCarritoService;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    /**
     * Mostrar el carrito y mantener vigentes sus reservas.
     */
    public function index(ReservaCarritoService $reservas)
    {
        $carrito = session()->get('carrito', []);
        $sincronizacion = $reservas->sincronizarCarrito($carrito);

        if (!$sincronizacion['success']) {
            session()->flash('error', $sincronizacion['message']);
        }

        $total = 0;

        foreach ($carrito as $id => &$item) {
            $item['subtotal'] = (int) $item['cantidad'] * (float) $item['precio'];
            $item['stock_disponible'] = $reservas->stockDisponible((int) $id);
            $total += $item['subtotal'];
        }

        unset($item);

        session()->put('carrito', $carrito);

        return view('cliente.carrito.index', compact('carrito', 'total'));
    }

    /**
     * Agregar una unidad y reservarla de forma atómica.
     */
    public function agregar(
        Request $request,
        int $id,
        ReservaCarritoService $reservas
    ) {
        $carrito = session()->get('carrito', []);
        $sincronizacion = $reservas->sincronizarCarrito($carrito);

        if (!$sincronizacion['success']) {
            return $this->respuestaError($request, $sincronizacion['message']);
        }

        $resultado = $reservas->reservar($id, 1);

        if (!$resultado['success']) {
            return $this->respuestaError($request, $resultado['message']);
        }

        $producto = Producto::findOrFail($id);

        if (isset($carrito[$id])) {
            $carrito[$id]['cantidad']++;
            $carrito[$id]['subtotal'] =
                $carrito[$id]['cantidad'] * $carrito[$id]['precio'];
        } else {
            $carrito[$id] = [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio,
                'cantidad' => 1,
                'subtotal' => $producto->precio,
                'imagen' => $producto->imagen,
                'stock' => $producto->stock,
            ];
        }

        $carrito[$id]['stock_disponible'] = $resultado['stockDisponible'];
        session()->put('carrito', $carrito);

        $mensaje = 'Producto agregado al carrito.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'productoId' => $id,
                'cantidad' => $carrito[$id]['cantidad'],
                'stockDisponible' => $resultado['stockDisponible'],
                'cantidadCarrito' => $this->cantidadTotal($carrito),
            ]);
        }

        return back()->with('success', $mensaje);
    }

    /**
     * Aumentar una unidad del producto reservado.
     */
    public function aumentar(int $id, ReservaCarritoService $reservas)
    {
        $carrito = session()->get('carrito', []);

        if (!isset($carrito[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'El producto no está en el carrito.',
            ], 404);
        }

        $sincronizacion = $reservas->sincronizarCarrito($carrito);

        if (!$sincronizacion['success']) {
            return response()->json($sincronizacion, 422);
        }

        $resultado = $reservas->reservar($id, 1);

        if (!$resultado['success']) {
            return response()->json($resultado, 422);
        }

        $carrito[$id]['cantidad']++;
        $carrito[$id]['subtotal'] =
            $carrito[$id]['cantidad'] * $carrito[$id]['precio'];
        $carrito[$id]['stock_disponible'] = $resultado['stockDisponible'];

        session()->put('carrito', $carrito);

        return response()->json([
            'success' => true,
            'message' => 'Cantidad actualizada.',
            'productoId' => $id,
            'cantidad' => $carrito[$id]['cantidad'],
            'stockDisponible' => $resultado['stockDisponible'],
            'subtotal' => number_format($carrito[$id]['subtotal'], 2, '.', ''),
            'total' => number_format($this->calcularTotal($carrito), 2, '.', ''),
            'cantidadCarrito' => $this->cantidadTotal($carrito),
        ]);
    }

    /**
     * Disminuir una unidad y liberar su reserva.
     */
    public function disminuir(int $id, ReservaCarritoService $reservas)
    {
        $carrito = session()->get('carrito', []);

        if (!isset($carrito[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'El producto no está en el carrito.',
            ], 404);
        }

        $resultado = $reservas->reservar($id, -1);

        if (!$resultado['success']) {
            return response()->json($resultado, 422);
        }

        if ($carrito[$id]['cantidad'] > 1) {
            $carrito[$id]['cantidad']--;
            $carrito[$id]['subtotal'] =
                $carrito[$id]['cantidad'] * $carrito[$id]['precio'];
            $carrito[$id]['stock_disponible'] = $resultado['stockDisponible'];

            session()->put('carrito', $carrito);

            return response()->json([
                'success' => true,
                'eliminado' => false,
                'message' => 'Cantidad actualizada.',
                'productoId' => $id,
                'cantidad' => $carrito[$id]['cantidad'],
                'stockDisponible' => $resultado['stockDisponible'],
                'subtotal' => number_format($carrito[$id]['subtotal'], 2, '.', ''),
                'total' => number_format($this->calcularTotal($carrito), 2, '.', ''),
                'cantidadCarrito' => $this->cantidadTotal($carrito),
            ]);
        }

        unset($carrito[$id]);
        session()->put('carrito', $carrito);

        return response()->json([
            'success' => true,
            'eliminado' => true,
            'productoId' => $id,
            'stockDisponible' => $resultado['stockDisponible'],
            'message' => 'Producto eliminado del carrito.',
            'total' => number_format($this->calcularTotal($carrito), 2, '.', ''),
            'cantidadCarrito' => $this->cantidadTotal($carrito),
            'carritoVacio' => empty($carrito),
        ]);
    }

    /**
     * Eliminar todas las unidades de un producto y liberar su reserva.
     */
    public function eliminar(int $id, ReservaCarritoService $reservas)
    {
        $carrito = session()->get('carrito', []);

        if (!isset($carrito[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'El producto no está en el carrito.',
            ], 404);
        }

        $cantidad = (int) $carrito[$id]['cantidad'];
        $resultado = $reservas->reservar($id, -$cantidad);

        if (!$resultado['success']) {
            return response()->json($resultado, 422);
        }

        unset($carrito[$id]);
        session()->put('carrito', $carrito);

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del carrito.',
            'productoId' => $id,
            'stockDisponible' => $resultado['stockDisponible'],
            'total' => number_format($this->calcularTotal($carrito), 2, '.', ''),
            'cantidadCarrito' => $this->cantidadTotal($carrito),
            'carritoVacio' => empty($carrito),
        ]);
    }

    /**
     * Vaciar carrito y liberar todas las unidades reservadas.
     */
    public function vaciar(ReservaCarritoService $reservas)
    {
        $reservas->liberarCarrito();
        session()->forget('carrito');

        return response()->json([
            'success' => true,
            'message' => 'Carrito vaciado correctamente.',
            'total' => '0.00',
            'cantidadCarrito' => 0,
            'carritoVacio' => true,
        ]);
    }

    private function respuestaError(Request $request, string $mensaje)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $mensaje,
            ], 422);
        }

        return back()->with('error', $mensaje);
    }

    private function calcularTotal(array $carrito): float
    {
        $total = 0;

        foreach ($carrito as $item) {
            $total += (int) $item['cantidad'] * (float) $item['precio'];
        }

        return $total;
    }

    private function cantidadTotal(array $carrito): int
    {
        return array_sum(array_map(
            fn ($item) => (int) $item['cantidad'],
            $carrito
        ));
    }
}
