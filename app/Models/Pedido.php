<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pedido extends Model
{
    /**
     * Campos asignables masivamente.
     */
    protected $fillable = [
        'user_id',
        'total',
        'estado',
        'latitud',
        'longitud',
        'direccion_entrega',
        'observacion_cliente',
        'referencia_delivery',
    ];

    /**
     * Un pedido pertenece a un cliente.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Un pedido tiene muchos productos.
     */
    public function detallePedidos(): HasMany
    {
        return $this->hasMany(DetallePedido::class);
    }

    /**
     * Un pedido tiene un comprobante de pago.
     */
    public function comprobantePago(): HasOne
    {
        return $this->hasOne(ComprobantePago::class);
    }

    /**
     * Un pedido tiene una asignación de delivery.
     */
    public function asignacionDelivery(): HasOne
    {
        return $this->hasOne(AsignacionDelivery::class);
    }

    /**
     * Un pedido tiene una calificación.
     */
    public function calificacion(): HasOne
    {
        return $this->hasOne(Calificacion::class);
    }
}