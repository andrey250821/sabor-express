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

        foreach ($carrito as $item) {
            $total += $item['subtotal'];
        }

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

        // Verificar disponibilidad
        if (
            $producto->estado !== 'disponible' ||
            $producto->stock <= 0
        ) {
            return back()->with(
                'error',
                'El producto no está disponible.'
            );
        }

        $carrito = session()->get('carrito', []);

        // Si ya existe
        if (isset($carrito[$id])) {

            // Verificar stock
            if ($carrito[$id]['cantidad'] >= $producto->stock) {
                return back()->with(
                    'error',
                    'No hay más unidades disponibles de este producto.'
                );
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

        return back()->with(
            'success',
            'Producto agregado al carrito.'
        );
    }

    /**
     * Aumentar cantidad.
     */
    public function aumentar($id)
    {
        $carrito = session()->get('carrito', []);

        if (!isset($carrito[$id])) {
            return back();
        }

        $producto = Producto::find($id);

        if (!$producto) {
            return back()->with(
                'error',
                'El producto ya no existe.'
            );
        }

        // Verificar stock actual
        if ($carrito[$id]['cantidad'] >= $producto->stock) {
            return back()->with(
                'error',
                'No puedes agregar más unidades. Stock máximo alcanzado.'
            );
        }

        $carrito[$id]['cantidad']++;

        $carrito[$id]['subtotal'] =
            $carrito[$id]['cantidad'] *
            $carrito[$id]['precio'];

        session()->put('carrito', $carrito);

        return back();
    }

    /**
     * Disminuir cantidad.
     */
    public function disminuir($id)
    {
        $carrito = session()->get('carrito', []);

        if (isset($carrito[$id])) {

            if ($carrito[$id]['cantidad'] > 1) {

                $carrito[$id]['cantidad']--;

                $carrito[$id]['subtotal'] =
                    $carrito[$id]['cantidad'] *
                    $carrito[$id]['precio'];
            } else {

                unset($carrito[$id]);
            }
        }

        session()->put('carrito', $carrito);

        return back();
    }

    /**
     * Eliminar producto.
     */
    public function eliminar($id)
    {
        $carrito = session()->get('carrito', []);

        unset($carrito[$id]);

        session()->put('carrito', $carrito);

        return back()->with(
            'success',
            'Producto eliminado del carrito.'
        );
    }

    /**
     * Vaciar carrito.
     */
    public function vaciar()
    {
        session()->forget('carrito');

        return back()->with(
            'success',
            'Carrito vaciado correctamente.'
        );
    }
}
