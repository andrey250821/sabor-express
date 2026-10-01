<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agregar la marca temporal que define el orden FIFO de Delivery.
     *
     * Es nullable para no romper pedidos antiguos ya existentes.
     * El servicio usa created_at como respaldo cuando fecha_listo es NULL.
     */
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->timestamp('fecha_listo')
                ->nullable()
                ->after('estado');

            $table->index(
                ['estado', 'fecha_listo'],
                'pedidos_estado_fecha_listo_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropIndex('pedidos_estado_fecha_listo_index');
            $table->dropColumn('fecha_listo');
        });
    }
};
