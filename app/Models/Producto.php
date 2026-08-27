<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    /**
     * Campos asignables masivamente.
     */
    protected $fillable = [
        'categoria_id',
        'nombre',
        'descripcion',
        'imagen',
        'precio',
        'stock',
        'estado',
    ];

    /**
     * Un producto pertenece a una categoría.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Un producto puede estar en muchos detalles de pedidos.
     */
    public function detallePedidos(): HasMany
    {
        return $this->hasMany(DetallePedido::class);
    }
}