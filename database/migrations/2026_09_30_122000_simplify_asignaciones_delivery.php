<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // El flujo anterior usaba estados que ya no existen.
        // Un registro pendiente solo se conserva si el pedido ya aparece
        // como asignado; en ese caso se normaliza a aceptado.
        $legacy = DB::table('asignaciones_delivery')
            ->whereIn('estado', [
                'pendiente',
                'rechazado',
                'tiempo_expirado',
            ])
            ->get([
                'id',
                'pedido_id',
                'estado',
            ]);

        foreach ($legacy as $asignacion) {
            $pedidoEstado = DB::table('pedidos')
                ->where('id', $asignacion->pedido_id)
                ->value('estado');

            if ($asignacion->estado === 'pendiente' && $pedidoEstado === 'asignado') {
                DB::table('asignaciones_delivery')
                    ->where('id', $asignacion->id)
                    ->update([
                        'estado' => 'aceptado',
                    ]);

                continue;
            }

            // Rechazado y tiempo_expirado son datos del flujo antiguo.
            // Si un pendiente no pertenece a un pedido ya asignado,
            // se elimina para dejar el pedido listo para una nueva asignación.
            DB::table('asignaciones_delivery')
                ->where('id', $asignacion->id)
                ->delete();
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE asignaciones_delivery
                 MODIFY estado ENUM('aceptado', 'en_camino', 'entregado')
                 NOT NULL DEFAULT 'aceptado'"
            );
        }

        foreach ([
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

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE asignaciones_delivery
                 MODIFY estado ENUM(
                     'pendiente',
                     'aceptado',
                     'rechazado',
                     'tiempo_expirado',
                     'en_camino',
                     'entregado'
                 )
                 NOT NULL DEFAULT 'pendiente'"
            );
        }
    }
};
