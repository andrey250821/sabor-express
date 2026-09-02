<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Producto;

class CarritoController extends Controller
{
    /**
     * Mostrar carrito.
     */
    public function index()
    {
        $carrito = session()->get('carrito', []);

        $total = 0;

        foreach ($carrito as $id => &$item) {

            // Recalcular siempre el subtotal
            $item['subtotal'] =
                $item['cantidad'] * $item['precio'];

            $total += $item['subtotal'];
        }

        unset($item);

        // Guardar los subtotales corregidos en la sesión
        session()->put('carrito', $carrito);

        return view(
            'cliente.carrito.index',
            compact('carrito', 'total')
        );
    }

    /**
     * Agregar producto al carrito.
     */
    public function agregar($id)
    {
        $producto = Producto::findOrFail($id);

        if (
            $producto->estado !== 'disponible' ||
            $producto->stock <= 0
        ) {
            return response()->json([
                'success' => false,
                'message' => 'El producto no está disponible.'
            ], 422);
        }

        $carrito = session()->get('carrito', []);

        if (isset($carrito[$id])) {

            if ($carrito[$id]['cantidad'] >= $producto->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay más unidades disponibles de este producto.'
                ], 422);
            }

            $carrito[$id]['cantidad']++;

            $carrito[$id]['subtotal'] =
                $carrito[$id]['cantidad'] *
                $carrito[$id]['precio'];
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

        session()->put('carrito', $carrito);

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito.',
            'cantidadCarrito' => $this->cantidadTotal($carrito)
        ]);
    }

    /**
     * Aumentar cantidad.
     */
    public function aumentar($id)
    {
        $carrito = session()->get('carrito', []);

        if (!isset($carrito[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'El producto no está en el carrito.'
            ], 404);
        }

        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'El producto ya no existe.'
            ], 404);
        }

        if (
            $producto->estado !== 'disponible' ||
            $producto->stock <= 0
        ) {
            return response()->json([
                'success' => false,
                'message' => 'El producto ya no está disponible.'
            ], 422);
        }

        if ($carrito[$id]['cantidad'] >= $producto->stock) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes agregar más unidades. Stock máximo alcanzado.'
            ], 422);
        }

        $carrito[$id]['cantidad']++;

        $carrito[$id]['subtotal'] =
            $carrito[$id]['cantidad'] *
            $carrito[$id]['precio'];

        session()->put('carrito', $carrito);

        return response()->json([
            'success' => true,
            'message' => 'Cantidad actualizada.',
            'cantidad' => $carrito[$id]['cantidad'],
            'subtotal' => number_format(
                $carrito[$id]['subtotal'],
                2,
                '.',
                ''
            ),
            'total' => number_format(
                $this->calcularTotal($carrito),
                2,
                '.',
                ''
            ),
            'cantidadCarrito' => $this->cantidadTotal($carrito)
        ]);
    }

    /**
     * Disminuir cantidad.
     */
    public function disminuir($id)
    {
        $carrito = session()->get('carrito', []);

        if (!isset($carrito[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'El producto no está en el carrito.'
            ], 404);
        }

        if ($carrito[$id]['cantidad'] > 1) {

            $carrito[$id]['cantidad']--;

            $carrito[$id]['subtotal'] =
                $carrito[$id]['cantidad'] *
                $carrito[$id]['precio'];

            session()->put('carrito', $carrito);

            return response()->json([
                'success' => true,
                'eliminado' => false,
                'message' => 'Cantidad actualizada.',
                'cantidad' => $carrito[$id]['cantidad'],
                'subtotal' => number_format(
                    $carrito[$id]['subtotal'],
                    2,
                    '.',
                    ''
                ),
                'total' => number_format(
                    $this->calcularTotal($carrito),
                    2,
                    '.',
                    ''
                ),
                'cantidadCarrito' => $this->cantidadTotal($carrito)
            ]);
        }

        unset($carrito[$id]);

        session()->put('carrito', $carrito);

        return response()->json([
            'success' => true,
            'eliminado' => true,
            'message' => 'Producto eliminado del carrito.',
            'total' => number_format(
                $this->calcularTotal($carrito),
                2,
                '.',
                ''
            ),
            'cantidadCarrito' => $this->cantidadTotal($carrito),
            'carritoVacio' => empty($carrito)
        ]);
    }

    /**
     * Eliminar producto.
     */
    public function eliminar($id)
    {
        $carrito = session()->get('carrito', []);

        if (!isset($carrito[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'El producto no está en el carrito.'
            ], 404);
        }

        unset($carrito[$id]);

        session()->put('carrito', $carrito);

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del carrito.',
            'total' => number_format(
                $this->calcularTotal($carrito),
                2,
                '.',
                ''
            ),
            'cantidadCarrito' => $this->cantidadTotal($carrito),
            'carritoVacio' => empty($carrito)
        ]);
    }

    /**
     * Vaciar carrito.
     */
    public function vaciar()
    {
        session()->forget('carrito');

        return response()->json([
            'success' => true,
            'message' => 'Carrito vaciado correctamente.',
            'total' => '0.00',
            'cantidadCarrito' => 0,
            'carritoVacio' => true
        ]);
    }

    /**
     * Calcular total.
     */
    private function calcularTotal($carrito)
    {
        $total = 0;

        foreach ($carrito as $item) {

            $subtotal =
                $item['cantidad'] * $item['precio'];

            $total += $subtotal;
        }

        return $total;
    }

    /**
     * Calcular cantidad total de productos.
     */
    private function cantidadTotal($carrito)
    {
        $cantidad = 0;

        foreach ($carrito as $item) {
            $cantidad += $item['cantidad'];
        }

        return $cantidad;
    }
}
