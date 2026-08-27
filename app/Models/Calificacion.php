<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calificacion extends Model
{
    /**
     * Nombre de la tabla.
     */
    protected $table = 'calificaciones';


    /**
     * Campos asignables.
     */
    protected $fillable = [
        'pedido_id',
        'user_id',
        'puntuacion',
        'comentario',
    ];


    /**
     * Una calificación pertenece a un pedido.
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }


    /**
     * Una calificación pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}