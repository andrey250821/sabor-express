<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // El flujo anterior usaba estados que ya no existen.
        // Los registros 'pendiente' que pertenecen a pedidos ya asignados
        // se normalizan a 'aceptado'. Los demás registros legacy se eliminan
        // para que la tabla quede coherente con el flujo actual.
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

            DB::table('asignaciones_delivery')
                ->where('id', $asignacion->id)
                ->delete();
        }

        // El único flujo vigente para una asignación es:
        // aceptado -> en_camino -> entregado.
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE asignaciones_delivery
                 MODIFY estado ENUM('aceptado', 'en_camino', 'entregado')
                 NOT NULL DEFAULT 'aceptado'"
            );
        }

        Schema::table('asignaciones_delivery', function ($table) {
            $columns = [
                'fecha_asignacion',
                'fecha_respuesta',
                'fecha_entrega',
            ];

            $existing = array_values(array_filter(
                $columns,
                fn ($column) => Schema::hasColumn('asignaciones_delivery', $column)
            ));

            if (!empty($existing)) {
                $table->dropColumn($existing);
            }
        });
    }

    public function down(): void
    {
        Schema::table('asignaciones_delivery', function ($table) {
            if (!Schema::hasColumn('asignaciones_delivery', 'fecha_asignacion')) {
                $table->timestamp('fecha_asignacion')
                    ->nullable()
                    ->after('estado');
            }

            if (!Schema::hasColumn('asignaciones_delivery', 'fecha_respuesta')) {
                $table->timestamp('fecha_respuesta')
                    ->nullable()
                    ->after('fecha_asignacion');
            }

            if (!Schema::hasColumn('asignaciones_delivery', 'fecha_entrega')) {
                $table->timestamp('fecha_entrega')
                    ->nullable()
                    ->after('fecha_respuesta');
            }
        });

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
