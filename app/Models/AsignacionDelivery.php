<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsignacionDelivery extends Model
{
    protected $table = 'asignaciones_delivery';

    protected $fillable = [

        'pedido_id',

        'delivery_id',

        'fecha_inicio',

        'fecha_entrega',

    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'datetime',
            'fecha_entrega' => 'datetime',
        ];
    }


    public function pedido(): BelongsTo
    {
        return $this->belongsTo(
            Pedido::class
        );
    }


    public function delivery(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'delivery_id'
        );
    }
}
