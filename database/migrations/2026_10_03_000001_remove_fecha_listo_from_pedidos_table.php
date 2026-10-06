<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * El sistema utiliza created_at como referencia temporal del pedido.
     * Esta migración elimina la columna legacy fecha_listo si todavía existe.
     */
    public function up(): void
    {
        if (Schema::hasColumn('pedidos', 'fecha_listo')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->dropColumn('fecha_listo');
            });
        }
    }

    /**
     * No se restaura una columna que ya no forma parte del diseño actual.
     */
    public function down(): void
    {
        // Intencionalmente no se restaura fecha_listo.
    }
};
