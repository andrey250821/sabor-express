<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservaCarrito extends Model
{
    protected $table = 'reservas_carrito';

    protected $fillable = [
        'token',
        'producto_id',
        'cantidad',
        'expires_at',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
