<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Completar la limpieza del esquema:
     * - pedidos.fecha_listo ya no es necesaria;
     * - cualquier columna antigua de asignaciones_delivery que aún exista
     *   se elimina de forma segura.
     *
     * No se eliminan registros de pedidos ni asignaciones.
     */
    public function up(): void
    {
        if (Schema::hasColumn('pedidos', 'fecha_listo')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->dropColumn('fecha_listo');
            });
        }

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
        if (!Schema::hasColumn('pedidos', 'fecha_listo')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->timestamp('fecha_listo')
                    ->nullable()
                    ->after('estado')
                    ->index();
            });
        }

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
