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
    protected $casts = [
        'latitud_restaurante' => 'float',
        'longitud_restaurante' => 'float',
        'tarifa_minima_delivery' => 'decimal:2',
        'precio_km_delivery' => 'decimal:2',
        'porcentaje_delivery' => 'decimal:2',
        'porcentaje_restaurante_delivery' => 'decimal:2',
    ];

    protected $fillable = [
        'nombre_restaurante',
        'telefono',
        'direccion',
        'latitud_restaurante',
        'longitud_restaurante',
        'tarifa_minima_delivery',
        'precio_km_delivery',
        'porcentaje_delivery',
        'porcentaje_restaurante_delivery',
        'logo',
        'qr_pago',
    ];
}
