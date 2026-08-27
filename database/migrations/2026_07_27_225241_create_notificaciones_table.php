<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('notificaciones', function (Blueprint $table) {


            $table->id();


            // Usuario que recibe la notificación
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();



            // Pedido relacionado (opcional)
            $table->foreignId('pedido_id')
                  ->nullable()
                  ->constrained('pedidos')
                  ->cascadeOnDelete();



            // Mensaje mostrado al usuario
            $table->text('mensaje');



            // Tipo de usuario
            $table->enum('tipo', [
                'cliente',
                'administrador',
                'delivery'
            ]);



            // Evento que generó la notificación
            $table->string('evento');



            // Si ya fue vista
            $table->boolean('leido')
                  ->default(false);



            // Para límite de respuesta del delivery
            $table->timestamp('fecha_expiracion')
                  ->nullable();



            $table->timestamps();


        });

    }



    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }

};