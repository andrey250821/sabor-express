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


            // Imagen del comprobante
            $table->string('imagen');


            $table->enum('estado', [

                'pendiente',
                'aprobado',
                'rechazado'

            ])
            ->default('pendiente');


            // Fecha en que administrador revisa
            $table->timestamp('fecha_revision')
                  ->nullable();


            // created_at y updated_at
            $table->timestamps();

        });

    }


    public function down(): void
    {
        Schema::dropIfExists('comprobantes_pago');
    }
};