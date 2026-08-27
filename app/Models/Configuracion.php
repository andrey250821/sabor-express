<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    /**
     * Nombre de la tabla.
     */
    protected $table = 'configuraciones';


    /**
     * Campos permitidos para insertar.
     */
    protected $fillable = [
        'nombre_restaurante',
        'telefono',
        'direccion',
        'logo',
        'qr_pago',
    ];
}
