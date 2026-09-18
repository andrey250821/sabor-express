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
        'referencia_bancaria',
        'motivo_revision',
        'datos_ocr',
        'estado',
        'fecha_revision',
    ];

    protected function casts(): array
    {
        return [
            'datos_ocr' => 'array',
            'fecha_revision' => 'datetime',
        ];
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
