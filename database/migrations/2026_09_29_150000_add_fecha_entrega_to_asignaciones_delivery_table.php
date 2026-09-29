<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registrar la fecha real en la que una entrega fue completada.
     *
     * Se utiliza para calcular las comisiones del día y consultar
     * el historial de los últimos 7 días sin depender de created_at.
     */
    public function up(): void
    {
        Schema::table('asignaciones_delivery', function (Blueprint $table) {
            $table->timestamp('fecha_entrega')
                ->nullable()
                ->after('fecha_respuesta');
        });

        // Compatibilidad con entregas ya existentes:
        // usamos updated_at como aproximación a la fecha de finalización
        // únicamente para registros que ya están marcados como entregados.
        DB::table('asignaciones_delivery')
            ->where('estado', 'entregado')
            ->whereNull('fecha_entrega')
            ->update([
                'fecha_entrega' => DB::raw('updated_at'),
            ]);
    }

    public function down(): void
    {
        Schema::table('asignaciones_delivery', function (Blueprint $table) {
            $table->dropColumn('fecha_entrega');
        });
    }
};
