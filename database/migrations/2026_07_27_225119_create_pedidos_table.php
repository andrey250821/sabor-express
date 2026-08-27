<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('pedidos', function (Blueprint $table) {


            $table->id();


            // Cliente que realizó el pedido
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();



            // Datos del pedido

            $table->decimal('total',10,2);



            $table->enum('estado',[

                'pendiente',
                'comprobante_enviado',
                'pagado',
                'preparando',
                'listo',
                'asignado',
                'en_camino',
                'entregado',
                'cancelado'

            ])
            ->default('pendiente');



            // Ubicación del cliente

            $table->decimal('latitud',10,7)
                  ->nullable();


            $table->decimal('longitud',10,7)
                  ->nullable();



            // Dirección escrita

            $table->text('direccion_entrega');



            // Observación para cocina

            $table->text('observacion_cliente')
                  ->nullable();



            // Referencia privada para delivery

            $table->text('referencia_delivery')
                  ->nullable();



            $table->timestamps();


        });

    }



    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }

};