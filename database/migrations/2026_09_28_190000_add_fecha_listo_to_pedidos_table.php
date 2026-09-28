<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->timestamp('fecha_listo')
                ->nullable()
                ->after('estado')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropIndex(['fecha_listo']);
            $table->dropColumn('fecha_listo');
        });
    }
};
