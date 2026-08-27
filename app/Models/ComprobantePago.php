<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComprobantePago extends Model
{

    protected $table = 'comprobantes_pago';


    protected $fillable = [
        'pedido_id',
        'imagen',
        'estado',
        'fecha_revision'
    ];


    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

}