<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Corrige el nombre del administrador cuando quedó almacenado
         * con signos de interrogación por un problema anterior de codificación.
         *
         * Se modifica únicamente el valor exacto afectado.
         */
        DB::table('users')
            ->where('name', 'Adri??n')
            ->update([
                'name' => 'Adrián',
            ]);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('name', 'Adrián')
            ->update([
                'name' => 'Adri??n',
            ]);
    }
};
