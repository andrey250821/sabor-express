<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\ReservaCarrito;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ReservaCarritoService
{
    /**
     * Identificador del carrito que se conserva aunque Laravel regenere
     * el ID de sesión al iniciar o cerrar sesión.
     */
    public function token(): string
    {
        $token = session()->get('carrito_token');

        if (!is_string($token) || $token === '') {
            $token = Str::random(64);
            session()->put('carrito_token', $token);
        }

        return $token;
    }

    /**
     * Reservar o liberar unidades sin modificar todavía el stock físico.
     *
     * El bloqueo de la fila del producto serializa las operaciones
     * simultáneas para impedir que dos clientes reserven la última unidad.
     */
    public function reservar(int $productoId, int $delta): array
    {
        if ($delta === 0) {
            return [
                'success' => true,
                'stockDisponible' => $this->stockDisponible($productoId),
                'cantidadReservada' => 0,
            ];
        }

        $token = $this->token();

        return DB::transaction(function () use ($productoId, $delta, $token) {
            $producto = Producto::whereKey($productoId)
                ->lockForUpdate()
                ->first();

            if (!$producto) {
                return [
                    'success' => false,
                    'message' => 'El producto ya no existe.',
                ];
            }

            $ahora = now();

            // Las reservas vencidas dejan de bloquear unidades.
            ReservaCarrito::where('producto_id', $productoId)
                ->where('expires_at', '<=', $ahora)
                ->delete();

            $reservaPropia = ReservaCarrito::where('token', $token)
                ->where('producto_id', $productoId)
                ->lockForUpdate()
                ->first();

            $reservaPropiaActiva = $reservaPropia
                && $reservaPropia->expires_at
                && $reservaPropia->expires_at->isFuture();

            $cantidadPropia = $reservaPropiaActiva
                ? (int) $reservaPropia->cantidad
                : 0;

            $reservadoPorOtros = (int) ReservaCarrito::where('producto_id', $productoId)
                ->where('token', '!=', $token)
                ->where('expires_at', '>', $ahora)
                ->sum('cantidad');

            // Liberar una unidad también es válido cuando la reserva ya venció.
            // El cliente debe poder quitar del carrito un producto que dejó de reservar.
            if ($delta < 0 && $cantidadPropia === 0) {
                if ($reservaPropia) {
                    $reservaPropia->delete();
                }

                return [
                    'success' => true,
                    'stockDisponible' => max(
                        0,
                        (int) $producto->stock - $reservadoPorOtros
                    ),
                    'cantidadReservada' => 0,
                ];
            }

            $nuevaCantidadPropia = max(0, $cantidadPropia + $delta);

            if ($delta > 0) {
                if ($producto->estado !== 'disponible') {
                    return [
                        'success' => false,
                        'message' => 'El producto no está disponible.',
                    ];
                }

                $unidadesLibres = (int) $producto->stock
                    - $reservadoPorOtros
                    - $cantidadPropia;

                if ($unidadesLibres < $delta) {
                    return [
                        'success' => false,
                        'message' => 'No hay más unidades disponibles de este producto.',
                        'stockDisponible' => max(
                            0,
                            (int) $producto->stock - $reservadoPorOtros - $cantidadPropia
                        ),
                    ];
                }
            }

            if ($nuevaCantidadPropia === 0) {
                if ($reservaPropia) {
                    $reservaPropia->delete();
                }
            } else {
                ReservaCarrito::updateOrCreate(
                    [
                        'token' => $token,
                        'producto_id' => $productoId,
                    ],
                    [
                        'cantidad' => $nuevaCantidadPropia,
                        'expires_at' => $this->fechaExpiracion(),
                    ]
                );
            }

            return [
                'success' => true,
                'stockDisponible' => max(
                    0,
                    (int) $producto->stock - $reservadoPorOtros - $nuevaCantidadPropia
                ),
                'cantidadReservada' => $nuevaCantidadPropia,
            ];
        }, 3);
    }

    /**
     * Reconciliar el contenido del carrito con sus reservas.
     * También permite recuperar carritos creados antes de esta actualización,
     * siempre que todavía exista stock disponible.
     */
    public function sincronizarCarrito(array $carrito): array
    {
        if (empty($carrito)) {
            $this->renovar();
            return ['success' => true];
        }

        $token = $this->token();

        foreach ($carrito as $id => $item) {
            $cantidadSolicitada = max(0, (int) ($item['cantidad'] ?? 0));

            if ($cantidadSolicitada === 0) {
                continue;
            }

            $cantidadReservada = (int) ReservaCarrito::where('token', $token)
                ->where('producto_id', (int) $id)
                ->where('expires_at', '>', now())
                ->value('cantidad');

            $diferencia = $cantidadSolicitada - $cantidadReservada;

            if ($diferencia !== 0) {
                $resultado = $this->reservar((int) $id, $diferencia);

                if (!$resultado['success']) {
                    return $resultado;
                }
            } else {
                ReservaCarrito::where('token', $token)
                    ->where('producto_id', (int) $id)
                    ->where('expires_at', '>', now())
                    ->update([
                        'expires_at' => $this->fechaExpiracion(),
                    ]);
            }
        }

        $this->renovar();

        return ['success' => true];
    }

    /**
     * Extender únicamente las reservas que siguen vigentes.
     */
    public function renovar(): void
    {
        $token = session()->get('carrito_token');

        if (!$token) {
            return;
        }

        ReservaCarrito::where('token', $token)
            ->where('expires_at', '>', now())
            ->update([
                'expires_at' => $this->fechaExpiracion(),
            ]);
    }

    /**
     * Stock que todavía puede agregarse a carritos.
     */
    public function stockDisponible(int $productoId): int
    {
        $producto = Producto::find($productoId);

        if (!$producto || $producto->estado !== 'disponible') {
            return 0;
        }

        $reservado = (int) ReservaCarrito::where('producto_id', $productoId)
            ->where('expires_at', '>', now())
            ->sum('cantidad');

        return max(0, (int) $producto->stock - $reservado);
    }

    /**
     * Convertir una reserva en una salida definitiva al confirmar el pedido.
     * Este método debe ejecutarse dentro de la misma transacción del pedido.
     */
    public function consumirProducto(int $productoId, int $cantidad): Producto
    {
        $token = session()->get('carrito_token');

        if (!$token || $cantidad <= 0) {
            throw new RuntimeException('No se encontró una reserva válida para el carrito.');
        }

        $producto = Producto::whereKey($productoId)
            ->lockForUpdate()
            ->first();

        if (!$producto) {
            throw new RuntimeException('Uno de los productos del carrito ya no existe.');
        }

        $reserva = ReservaCarrito::where('token', $token)
            ->where('producto_id', $productoId)
            ->where('expires_at', '>', now())
            ->lockForUpdate()
            ->first();

        if (!$reserva || (int) $reserva->cantidad !== $cantidad) {
            throw new RuntimeException(
                'La reserva de "' . $producto->nombre . '" venció o cambió. Vuelve al carrito y verifica las cantidades.'
            );
        }

        if (
            $producto->estado !== 'disponible'
            || (int) $producto->stock < $cantidad
        ) {
            throw new RuntimeException(
                'El stock de "' . $producto->nombre . '" cambió. Actualiza el carrito antes de confirmar.'
            );
        }

        $producto->stock = (int) $producto->stock - $cantidad;

        if ($producto->stock <= 0) {
            $producto->stock = 0;
            $producto->estado = 'agotado';
        }

        $producto->save();
        $reserva->delete();

        return $producto;
    }

    /**
     * Liberar todas las reservas al vaciar el carrito.
     */
    public function liberarCarrito(): void
    {
        $token = session()->get('carrito_token');

        if ($token) {
            DB::transaction(function () use ($token) {
                $productoIds = ReservaCarrito::where('token', $token)
                    ->orderBy('producto_id')
                    ->pluck('producto_id')
                    ->unique()
                    ->values();

                if ($productoIds->isNotEmpty()) {
                    Producto::whereIn('id', $productoIds)
                        ->orderBy('id')
                        ->lockForUpdate()
                        ->get();
                }

                ReservaCarrito::where('token', $token)->delete();
            }, 3);
        }

        session()->forget('carrito_token');
    }

    private function fechaExpiracion()
    {
        // La reserva expira junto con la ventana de inactividad de la sesión.
        return now()->addMinutes(max(5, (int) config('session.lifetime', 120)));
    }
}
