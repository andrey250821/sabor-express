<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pedidos', 'fecha_listo')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->timestamp('fecha_listo')
                    ->nullable()
                    ->after('estado');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pedidos', 'fecha_listo')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->dropColumn('fecha_listo');
            });
        }
    }
};
