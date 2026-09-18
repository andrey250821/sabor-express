<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('comprobantes_pago')
            ->where('estado', 'pendiente')
            ->update(['estado' => 'en_revision']);

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE comprobantes_pago
                 MODIFY estado ENUM('pendiente', 'en_revision', 'aprobado', 'rechazado')
                 NOT NULL DEFAULT 'pendiente'"
            );
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::table('comprobantes_pago')
                ->where('estado', 'en_revision')
                ->update(['estado' => 'pendiente']);

            DB::statement(
                "ALTER TABLE comprobantes_pago
                 MODIFY estado ENUM('pendiente', 'aprobado', 'rechazado')
                 NOT NULL DEFAULT 'pendiente'"
            );
        } else {
            DB::table('comprobantes_pago')
                ->where('estado', 'en_revision')
                ->update(['estado' => 'pendiente']);
        }
    }
};