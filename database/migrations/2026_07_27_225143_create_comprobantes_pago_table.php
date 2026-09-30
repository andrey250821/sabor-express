<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes_pago', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pedido_id')
                ->constrained('pedidos')
                ->cascadeOnDelete();

            $table->string('imagen');

            $table->string('referencia_bancaria', 50)
                ->nullable();

            $table->text('motivo_revision')
                ->nullable();

            $table->json('datos_ocr')
                ->nullable();

            $table->enum('estado', [
                'en_revision',
                'aprobado',
                'rechazado',
            ])->default('en_revision');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes_pago');
    }
};
