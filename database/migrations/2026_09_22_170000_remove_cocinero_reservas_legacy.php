<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Los pedidos PAGADOS no deben conservar reservas hechas
     * por el flujo anterior. En el flujo actual, cocinero_id
     * se asigna recién cuando se pulsa "Preparar".
     */
    public function up(): void
    {
        DB::table('pedidos')
            ->where('estado', 'pagado')
            ->whereNotNull('cocinero_id')
            ->update([
                'cocinero_id' => null,
            ]);
    }

    public function down(): void
    {
        // No restauramos reservas antiguas porque esa información
        // ya no representa el flujo actual del sistema.
    }
};
