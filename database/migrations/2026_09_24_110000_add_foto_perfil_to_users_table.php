<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega la foto de perfil sin modificar ni eliminar
     * los datos existentes de la tabla users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('foto_perfil')->nullable()->after('google_id');
        });
    }

    /**
     * Revertir únicamente la columna agregada por esta migración.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('foto_perfil');
        });
    }
};
