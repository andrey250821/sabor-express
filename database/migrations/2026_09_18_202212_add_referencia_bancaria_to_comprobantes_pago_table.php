<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('comprobantes_pago', function (Blueprint $table) {
            $table->string('referencia_bancaria', 50)
                ->nullable()
                ->after('imagen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comprobantes_pago', function (Blueprint $table) {
            $table->dropColumn('referencia_bancaria');
        });
    }
};