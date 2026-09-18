<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comprobantes_pago', function (Blueprint $table) {
            $table->text('motivo_revision')
                ->nullable()
                ->after('referencia_bancaria');

            $table->json('datos_ocr')
                ->nullable()
                ->after('motivo_revision');
        });
    }

    public function down(): void
    {
        Schema::table('comprobantes_pago', function (Blueprint $table) {
            $table->dropColumn([
                'motivo_revision',
                'datos_ocr',
            ]);
        });
    }
};