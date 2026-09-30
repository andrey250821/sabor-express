<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Todo comprobante pendiente del esquema antiguo pasa a la
        // condición actual: en_revision.
        DB::table('comprobantes_pago')
            ->where('estado', 'pendiente')
            ->update([
                'estado' => 'en_revision',
            ]);

        // El flujo vigente solamente utiliza estos tres estados.
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE comprobantes_pago
                 MODIFY estado ENUM('en_revision', 'aprobado', 'rechazado')
                 NOT NULL DEFAULT 'en_revision'"
            );
        }

        if (Schema::hasColumn('comprobantes_pago', 'fecha_revision')) {
            Schema::table('comprobantes_pago', function ($table) {
                $table->dropColumn('fecha_revision');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('comprobantes_pago', 'fecha_revision')) {
            Schema::table('comprobantes_pago', function ($table) {
                $table->timestamp('fecha_revision')
                    ->nullable()
                    ->after('estado');
            });
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE comprobantes_pago
                 MODIFY estado ENUM('pendiente', 'en_revision', 'aprobado', 'rechazado')
                 NOT NULL DEFAULT 'pendiente'"
            );
        }
    }
};
