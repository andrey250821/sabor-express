<?php

namespace App\Models;
use App\Models\Calificacion;


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
        'cocinero_id',
        'total',
        'subtotal_productos',
        'tarifa_delivery',
        'distancia_delivery_km',
        'porcentaje_delivery',
        'monto_delivery',
        'porcentaje_restaurante_delivery',
        'monto_restaurante_delivery',
        'estado',
        'fecha_listo',
        'latitud',
        'longitud',
        'direccion_entrega',
        'observacion_cliente',
        'referencia_delivery',
    ];

    protected function casts(): array
    {
        return [
            'fecha_listo' => 'datetime',
            'subtotal_productos' => 'decimal:2',
            'tarifa_delivery' => 'decimal:2',
            'distancia_delivery_km' => 'decimal:2',
            'porcentaje_delivery' => 'decimal:2',
            'monto_delivery' => 'decimal:2',
            'porcentaje_restaurante_delivery' => 'decimal:2',
            'monto_restaurante_delivery' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Un pedido pertenece a un cliente.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cocinero que inició la preparación del pedido.
     */
    public function cocinero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cocinero_id');
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
     * Un pedido puede tener muchas calificaciones.
     */
    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class);
    }
}
