<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Simplificar la tabla de asignaciones Delivery.
     *
     * La asignación solo representa la relación entre un pedido y
     * el Delivery. El estado operativo pertenece exclusivamente a
     * pedidos.estado.
     *
     * Esta migración no elimina registros históricos.
     */
    public function up(): void
    {
        foreach ([
            'estado',
            'fecha_asignacion',
            'fecha_respuesta',
            'fecha_entrega',
        ] as $column) {
            if (Schema::hasColumn('asignaciones_delivery', $column)) {
                Schema::table('asignaciones_delivery', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('asignaciones_delivery', 'estado')) {
            Schema::table('asignaciones_delivery', function (Blueprint $table) {
                $table->enum('estado', [
                    'pendiente',
                    'aceptado',
                    'rechazado',
                    'tiempo_expirado',
                    'en_camino',
                    'entregado',
                ])
                ->default('pendiente')
                ->after('delivery_id');
            });
        }

        if (!Schema::hasColumn('asignaciones_delivery', 'fecha_asignacion')) {
            Schema::table('asignaciones_delivery', function (Blueprint $table) {
                $table->timestamp('fecha_asignacion')
                    ->nullable()
                    ->after('estado');
            });
        }

        if (!Schema::hasColumn('asignaciones_delivery', 'fecha_respuesta')) {
            Schema::table('asignaciones_delivery', function (Blueprint $table) {
                $table->timestamp('fecha_respuesta')
                    ->nullable()
                    ->after('fecha_asignacion');
            });
        }

        if (!Schema::hasColumn('asignaciones_delivery', 'fecha_entrega')) {
            Schema::table('asignaciones_delivery', function (Blueprint $table) {
                $table->timestamp('fecha_entrega')
                    ->nullable()
                    ->after('fecha_respuesta');
            });
        }
    }
};
