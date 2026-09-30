<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('notificaciones', 'fecha_expiracion')) {
            Schema::table('notificaciones', function (Blueprint $table) {
                $table->dropColumn('fecha_expiracion');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('notificaciones', 'fecha_expiracion')) {
            Schema::table('notificaciones', function (Blueprint $table) {
                $table->timestamp('fecha_expiracion')
                    ->nullable()
                    ->after('leido');
            });
        }
    }
};
