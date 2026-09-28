<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Agregar el estado en_camino al flujo automático de Delivery.
     *
     * Se mantienen los estados antiguos en el ENUM para no romper
     * registros existentes en la base de datos. La aplicación nueva
     * utiliza principalmente aceptado, en_camino y entregado.
     */
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE asignaciones_delivery
             MODIFY estado ENUM(
                 'pendiente',
                 'aceptado',
                 'rechazado',
                 'tiempo_expirado',
                 'en_camino',
                 'entregado'
             ) NOT NULL DEFAULT 'pendiente'"
        );
    }

    /**
     * Revertir el cambio de forma segura.
     */
    public function down(): void
    {
        // Los registros en camino no pueden conservar ese estado
        // si regresamos al ENUM anterior, por lo que se normalizan
        // a aceptado antes de quitar en_camino.
        DB::table('asignaciones_delivery')
            ->where('estado', 'en_camino')
            ->update([
                'estado' => 'aceptado',
            ]);

        DB::statement(
            "ALTER TABLE asignaciones_delivery
             MODIFY estado ENUM(
                 'pendiente',
                 'aceptado',
                 'rechazado',
                 'tiempo_expirado',
                 'entregado'
             ) NOT NULL DEFAULT 'pendiente'"
        );
    }
};
