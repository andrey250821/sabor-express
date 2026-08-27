<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('calificaciones', function (Blueprint $table) {


            $table->id();



            $table->foreignId('pedido_id')
                  ->constrained('pedidos')
                  ->cascadeOnDelete();



            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();



            $table->integer('puntuacion');



            $table->text('comentario')
                  ->nullable();



            $table->timestamps();


        });

    }



    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }

};