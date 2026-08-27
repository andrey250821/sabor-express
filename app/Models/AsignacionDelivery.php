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

        'estado',

        'fecha_asignacion',

        'fecha_respuesta',

    ];


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
