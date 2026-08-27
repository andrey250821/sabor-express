<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{

    protected $table = 'notificaciones';


    protected $fillable = [
        'user_id',
        'pedido_id',
        'mensaje',
        'tipo',
        'evento',
        'leido',
        'fecha_expiracion',
    ];



    /**
     * Una notificación pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }



    /**
     * Una notificación puede pertenecer a un pedido.
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

}