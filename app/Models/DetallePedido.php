<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallePedido extends Model
{
    /**
     * Nombre de la tabla.
     */
    protected $table = 'detalle_pedidos';

    /**
     * Campos asignables.
     */
    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio',
        'subtotal',
    ];

    /**
     * Un detalle pertenece a un pedido.
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Un detalle pertenece a un producto.
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}