<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('asignaciones_delivery', function (Blueprint $table) {


            $table->id();



            // Pedido asignado

            $table->foreignId('pedido_id')
                  ->constrained('pedidos')
                  ->cascadeOnDelete();



            // Delivery

            $table->foreignId('delivery_id')
                  ->constrained('users')
                  ->cascadeOnDelete();



            $table->enum('estado',[

                'pendiente',
                'aceptado',
                'rechazado',
                'tiempo_expirado',
                'entregado'

            ])
            ->default('pendiente');



            // Momento de asignación

            $table->timestamp('fecha_asignacion')
                  ->nullable();



            // Respuesta del delivery

            $table->timestamp('fecha_respuesta')
                  ->nullable();



            $table->timestamps();


        });

    }



    public function down(): void
    {
        Schema::dropIfExists('asignaciones_delivery');
    }

};