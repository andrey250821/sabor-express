<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade las columnas usadas por la verificación de correo y las sesiones.
     * Las comprobaciones permiten actualizar instalaciones que ya las tengan.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'email_verified_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->timestamp('email_verified_at')->nullable();
            });
        }

        if (! Schema::hasColumn('users', 'remember_token')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->rememberToken();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'remember_token')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('remember_token');
            });
        }

        if (Schema::hasColumn('users', 'email_verified_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('email_verified_at');
            });
        }
    }
};
