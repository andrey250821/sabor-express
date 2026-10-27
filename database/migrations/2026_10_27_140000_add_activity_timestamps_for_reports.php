<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->timestamp('fecha_inicio_preparacion')
                ->nullable()
                ->after('cocinero_id');

            $table->timestamp('fecha_listo')
                ->nullable()
                ->after('fecha_inicio_preparacion');
        });

        Schema::table('asignaciones_delivery', function (Blueprint $table) {
            $table->timestamp('fecha_inicio')
                ->nullable()
                ->after('delivery_id');

            $table->timestamp('fecha_entrega')
                ->nullable()
                ->after('fecha_inicio');
        });
    }

    public function down(): void
    {
        Schema::table('asignaciones_delivery', function (Blueprint $table) {
            $table->dropColumn(['fecha_inicio', 'fecha_entrega']);
        });

        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['fecha_inicio_preparacion', 'fecha_listo']);
        });
    }
};
